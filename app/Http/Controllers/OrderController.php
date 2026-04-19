<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderMeta;
use App\Models\Customer;
use App\Services\CodeGeneratorService;
use App\Services\InventoryService;
use App\Services\LogService;
use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::query();

        // 1. Áp dụng Filter Thời gian
        $time = $request->get('time', 'all');
        if ($time === 'month') {
            $query->whereMonth('order_date', now()->month)->whereYear('order_date', now()->year);
        } elseif ($time === 'quarter') {
            $query->whereBetween('order_date', [now()->startOfQuarter(), now()->endOfQuarter()]);
        } elseif ($time === 'year') {
            $query->whereYear('order_date', now()->year);
        } elseif ($time === 'custom' && $request->date_start && $request->date_end) {
            $query->whereBetween('order_date', [$request->date_start, $request->date_end]);
        }

        // 2. Áp dụng Filter Tìm kiếm
        if ($kw = $request->get('search')) {
            $query->where(function($q) use ($kw) {
                $q->where('cto_code', 'like', "%{$kw}%")
                  ->orWhere('ten_kh', 'like', "%{$kw}%")
                  ->orWhere('ma_kh', 'like', "%{$kw}%")
                  ->orWhere('ghi_chu', 'like', "%{$kw}%");
            });
        }

        // 3. Đếm theo trạng thái (Clone query trước khi lọc theo status)
        $countQuery = clone $query;
        $counts = $countQuery->select('trang_thai', DB::raw('COUNT(*) as order_count'))
            ->groupBy('trang_thai')->pluck('order_count', 'trang_thai')->toArray();
        $counts['all'] = array_sum($counts);

        // 4. Áp dụng Filter Trạng thái (cho table)
        if ($status = $request->get('status')) {
            if ($status !== 'all') {
                $query->where('trang_thai', $status);
            }
        }

        // 5. Sắp xếp & Paginate
        $sort = $request->get('sort', 'newest');
        match ($sort) {
            'oldest' => $query->orderBy('order_date'),
            default  => $query->orderByDesc('order_date')->orderByDesc('id'),
        };

        $limit = $request->get('limit', 20);
        $orders = $query->with(['customer', 'meta'])->paginate($limit)->withQueryString();

        $customers = Customer::orderBy('ten_cty')->get(['id','ma_kh','ten_cty']);

        $ty_gia = SystemSetting::get('ty_gia', 25450);

        return view('orders.index', compact('orders', 'time', 'sort', 'counts', 'customers', 'ty_gia'));
    }

    public function show(Order $order)
    {
        $order->load(['customer', 'items', 'meta', 'deliveryNote']);
        $stockMap = InventoryService::getAllStockMap();
        $products = \App\Models\Product::select('ma_hang', 'ten_hang', 'mo_ta', 'don_vi_tinh', 'don_gia')
            ->whereNotNull('ma_hang')
            ->orderBy('ma_hang')
            ->get()
            ->unique('ma_hang')
            ->values();

        return view('orders.show', compact('order', 'stockMap', 'products'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'ngay_tao'    => 'required|date',
            'ghi_chu'     => 'nullable|string',
            'seller_name' => 'nullable|string|max:150',
            'seller_phone'=> 'nullable|string|max:20',
            'buyer_name'  => 'nullable|string|max:150',
            'buyer_phone' => 'nullable|string|max:20',
        ]);

        $customer = Customer::findOrFail($data['customer_id']);
        $ctoCode  = CodeGeneratorService::generateCtoCo($customer->ma_kh);

        $order = Order::create([
            'customer_id' => $data['customer_id'],
            'order_date'  => $data['ngay_tao'],
            'ghi_chu'     => $data['ghi_chu'],
            'nguoi_ban'   => $data['seller_name'],
            'sdt_ban'     => $data['seller_phone'],
            'nguoi_mua'   => $data['buyer_name'],
            'sdt_mua'     => $data['buyer_phone'],
            'cto_code'    => $ctoCode,
            'ma_kh'       => $customer->ma_kh,
            'ten_kh'      => $customer->ten_cty,
            'trang_thai'  => 'Chờ xác nhận',
            'created_by'  => Auth::id(),
        ]);

        OrderMeta::create([
            'order_id'    => $order->id,
            'cto_code'    => $order->cto_code,
            'tinh_trang'  => 'Chờ xác nhận',
            'vat_percent' => 8
        ]);

        LogService::log('Tạo đơn hàng', "Tạo mới đơn booking [{$ctoCode}] cho {$customer->ten_cty}");
        return response()->json(['success' => true, 'message' => 'Đã tạo đơn hàng thành công!', 'id' => $order->id]);
    }

    public function edit(Order $order)
    {
        $order->load(['customer', 'meta']);
        return response()->json(['success' => true, 'data' => $order]);
    }

    public function update(Request $request, Order $order)
    {
        $data = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'ngay_tao'    => 'required|date',
            'ghi_chu'     => 'nullable|string',
            'seller_name' => 'nullable|string|max:150',
            'seller_phone'=> 'nullable|string|max:20',
            'buyer_name'  => 'nullable|string|max:150',
            'buyer_phone' => 'nullable|string|max:20',
            'trang_thai'  => 'nullable|string',
        ]);

        $order->update([
            'customer_id' => $data['customer_id'],
            'order_date'  => $data['ngay_tao'],
            'ghi_chu'     => $data['ghi_chu'],
            'nguoi_ban'   => $data['seller_name'],
            'sdt_ban'     => $data['seller_phone'],
            'nguoi_mua'   => $data['buyer_name'],
            'sdt_mua'     => $data['buyer_phone'],
            'trang_thai'  => $data['trang_thai'] ?? $order->trang_thai,
        ]);

        LogService::log('Cập nhật đơn hàng', "Cập nhật basic info đơn [{$order->cto_code}]");
        return response()->json(['success' => true, 'message' => 'Đã cập nhật đơn hàng thành công!']);
    }

    public function destroy(Order $order)
    {
        if ($order->deliveryNote()->exists()) {
            return response()->json(['success' => false, 'message' => 'Không thể xóa đơn đã có phiếu giao hàng!']);
        }
        LogService::log('Xóa đơn hàng', "Xóa đơn [{$order->cto_code}]");
        $order->items()->delete();
        $order->meta()->delete();
        $order->delete();
        return response()->json(['success' => true, 'message' => 'Đã xóa đơn hàng!']);
    }

    public function saveItems(Request $request, Order $order)
    {
        $request->validate([
            'items'           => 'nullable|array',
            'trang_thai'      => 'nullable|string',
            'vat_percent'     => 'nullable|numeric|min:0|max:100',
            'ghi_chu'         => 'nullable|string',
        ]);

        // Lưu meta
        $order->meta()->updateOrCreate(
            ['order_id' => $order->id],
            [
                'tinh_trang'  => $request->trang_thai ?? $order->trang_thai,
                'vat_percent' => $request->vat_percent ?? 8,
            ]
        );

        // Cập nhật trạng thái và ghi chú
        $updateData = [];
        if ($request->trang_thai) $updateData['trang_thai'] = $request->trang_thai;
        if ($request->has('ghi_chu')) $updateData['ghi_chu'] = $request->ghi_chu;
        
        if (!empty($updateData)) {
            $order->update($updateData);
        }

        // Xóa items cũ và lưu lại
        $order->items()->delete();

        $items = $request->get('items', []);
        foreach ($items as $i => $item) {
            if (empty($item['ma_hang']) && empty($item['ten_hang'])) continue;

            $sl  = (float)($item['so_luong'] ?? 0);
            $gia = (float)str_replace(',', '', $item['don_gia'] ?? 0);
            $tt  = $sl * $gia;

            OrderItem::create([
                'order_id'    => $order->id,
                'cto_code'    => $order->cto_code,
                'ma_hang'     => $item['ma_hang'] ?? null,
                'ten_hang'    => $item['ten_hang'] ?? null,
                'mo_ta_phu'   => $item['mo_ta_phu'] ?? null,
                'so_luong'    => $sl,
                'don_vi_tinh' => $item['don_vi_tinh'] ?? null,
                'don_gia'     => $gia,
                'thanh_tien'  => $tt,
                'ma_lot'      => $item['ma_lot'] ?? null,
                'han_su_dung' => !empty($item['han_su_dung']) ? $item['han_su_dung'] : null,
                'quy_cach'    => $item['quy_cach'] ?? null,
                'quy_doi'     => (float)($item['quy_doi'] ?? 0),
                'ghi_chu'     => $item['ghi_chu'] ?? null,
                'sort_order'  => $i,
            ]);
        }

        LogService::log('Lưu chi tiết đơn', "Lưu [{$order->cto_code}] - " . count($items) . " dòng");
        return response()->json(['success' => true, 'message' => 'Đã lưu đơn hàng thành công!']);
    }
}
