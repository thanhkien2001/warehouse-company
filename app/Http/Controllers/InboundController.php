<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\InboundReceipt;
use App\Models\InboundItem;
use App\Models\InboundAttachment;
use App\Models\ProductCatalog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class InboundController extends Controller
{
    /** GET /ton-kho/nhap-kho — form tạo mới */
    public function create(Request $request)
    {
        $categories   = Category::orderBy('name')->get();
        $products     = ProductCatalog::where('trang_thai', 'Hoạt động')
                            ->with('category')
                            ->orderBy('ma_hang')
                            ->get(['id','ma_hang','ten_hang','category_id','don_vi_tinh','quy_cach','gia_nhap','nha_cung_cap']);
        $nextCode     = InboundReceipt::generateCode();
        
        $perPage      = $request->input('per_page', 15);
        $inboundItems = InboundItem::with(['receipt.attachments', 'category'])
                            ->paginate($perPage);

        return view('products.inbound', compact('categories', 'products', 'nextCode', 'inboundItems'));
    }

    /** POST /ton-kho/nhap-kho — lưu phiếu nhập */
    public function store(Request $request)
    {
        $itemsJson = $request->input('items', '[]');
        $items = json_decode($itemsJson, true);
        $receiptId = $request->input('receipt_id');

        if (!is_array($items) || count($items) === 0) {
            return response()->json(['success' => false, 'message' => 'Vui lòng thêm ít nhất 1 dòng hàng hóa!'], 422);
        }

        // Validate header fields
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'receipt_code' => 'required|string|max:50|unique:inbound_receipts,receipt_code,' . ($receiptId ?: 'NULL') . ',id',
            'receipt_date' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => implode(' | ', $validator->errors()->all())], 422);
        }

        // Validate từng dòng items
        foreach ($items as $idx => $item) {
            if (empty($item['ma_hang'])) {
                return response()->json(['success' => false, 'message' => "Dòng " . ($idx+1) . ": Mã hàng không được để trống!"], 422);
            }
            if (empty($item['ten_hang'])) {
                return response()->json(['success' => false, 'message' => "Dòng " . ($idx+1) . ": Tên hàng không được để trống!"], 422);
            }
            if (($item['so_luong'] ?? 0) <= 0) {
                return response()->json(['success' => false, 'message' => "Dòng " . ($idx+1) . ": Số lượng phải lớn hơn 0!"], 422);
            }
        }

        DB::beginTransaction();
        try {
            if ($receiptId) {
                $receipt = InboundReceipt::findOrFail($receiptId);
                $receipt->update([
                    'receipt_code'  => $request->receipt_code,
                    'invoice_no'    => $request->invoice_no,
                    'receipt_date'  => $request->receipt_date,
                    'invoice_date'  => $request->invoice_date,
                    'supplier_name' => $request->supplier_name,
                    'supplier_code' => $request->supplier_code,
                    'warehouse'     => $request->warehouse,
                    'origin'        => $request->origin,
                    'notes'         => $request->notes,
                ]);
                // Xóa các items cũ để ghi đè
                InboundItem::where('receipt_id', $receipt->id)->delete();
            } else {
                // 1. Tạo phiếu nhập mới
                $receipt = InboundReceipt::create([
                    'receipt_code'  => $request->receipt_code,
                    'invoice_no'    => $request->invoice_no,
                    'receipt_date'  => $request->receipt_date,
                    'invoice_date'  => $request->invoice_date,
                    'supplier_name' => $request->supplier_name,
                    'supplier_code' => $request->supplier_code,
                    'warehouse'     => $request->warehouse,
                    'origin'        => $request->origin,
                    'department'    => $request->department ?? 'Kho',
                    'created_by'    => auth()->id(),
                    'notes'         => $request->notes,
                    'status'        => 'completed',
                ]);
            }

            // 2. Lưu các dòng hàng
            $totalAmount = 0;
            foreach ($items as $idx => $item) {
                $thanh_tien = (float)($item['so_luong'] ?? 0) * (float)($item['don_gia'] ?? 0);
                $totalAmount += $thanh_tien;

                InboundItem::create([
                    'receipt_id'        => $receipt->id,
                    'product_catalog_id'=> $item['product_catalog_id'] ?? null,
                    'ma_hang'           => $item['ma_hang'],
                    'ten_hang'          => $item['ten_hang'],
                    'category_id'       => $item['category_id'] ?? null,
                    'don_vi_tinh'       => $item['don_vi_tinh'] ?? null,
                    'quy_cach'          => $item['quy_cach'] ?? null,
                    'so_luong'          => $item['so_luong'],
                    'don_gia'           => $item['don_gia'] ?? 0,
                    'thanh_tien'        => $thanh_tien,
                    'so_lo'             => $item['so_lo'] ?? null,
                    'ngay_san_xuat'     => $item['ngay_san_xuat'] ?: null,
                    'han_su_dung'       => $item['han_su_dung'] ?: null,
                    'kho_nhap'          => $item['kho_nhap'] ?? $request->warehouse,
                    'ghi_chu'           => $item['ghi_chu'] ?? null,
                    'sort_order'        => $idx,
                ]);
            }

            // Cập nhật tổng tiền
            $receipt->update(['total_amount' => $totalAmount]);

            // 3. Upload file đính kèm
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $storedName = Str::uuid() . '.' . $file->getClientOriginalExtension();
                    $path = $file->storeAs('inbound/' . $receipt->id, $storedName, 'public');

                    InboundAttachment::create([
                        'receipt_id'    => $receipt->id,
                        'original_name' => $file->getClientOriginalName(),
                        'stored_name'   => $storedName,
                        'file_path'     => $path,
                        'mime_type'     => $file->getMimeType(),
                        'file_size'     => $file->getSize(),
                    ]);
                }
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Đã lưu phiếu nhập ' . $receipt->receipt_code, 'id' => $receipt->id]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /** DELETE /ton-kho/nhap-kho/item/{id} — xóa dòng hàng */
    public function deleteItem($id)
    {
        $item = InboundItem::findOrFail($id);
        $receiptId = $item->receipt_id;
        
        $item->delete();
        
        if ($receiptId) {
            $remaining = InboundItem::where('receipt_id', $receiptId)->count();
            if ($remaining === 0) {
                // Xóa cả file đính kèm
                $atts = InboundAttachment::where('receipt_id', $receiptId)->get();
                foreach($atts as $a) {
                    Storage::disk('public')->delete($a->file_path);
                    $a->delete();
                }
                InboundReceipt::destroy($receiptId);
            }
        }
        
        return response()->json(['success' => true, 'message' => 'Đã xóa dòng dữ liệu thành công!']);
    }

    /** DELETE /ton-kho/nhap-kho/attachment/{id} — xóa file đính kèm */
    public function deleteAttachment($id)
    {
        $att = InboundAttachment::findOrFail($id);
        Storage::disk('public')->delete($att->file_path);
        $att->delete();
        return response()->json(['success' => true]);
    }

    /** GET /ton-kho/nhap-kho/next-code — lấy mã phiếu tiếp theo (AJAX) */
    public function nextCode()
    {
        return response()->json(['code' => InboundReceipt::generateCode()]);
    }

    /** GET /ton-kho/nhap-kho/product-search?q=... — tìm sản phẩm (AJAX autocomplete) */
    public function productSearch(Request $request)
    {
        $q = $request->q;
        $products = ProductCatalog::where('trang_thai', 'Hoạt động')
            ->where(function ($query) use ($q) {
                $query->where('ma_hang', 'like', "%$q%")
                      ->orWhere('ten_hang', 'like', "%$q%");
            })
            ->with('category')
            ->limit(20)
            ->get(['id','ma_hang','ten_hang','category_id','don_vi_tinh','quy_cach','gia_nhap','nha_cung_cap']);

        return response()->json($products);
    }
}
