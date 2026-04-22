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
use Barryvdh\DomPDF\Facade\Pdf;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::query();

        // 1. Áp dụng Filter Thời gian & Tìm kiếm (Lọc sớm để counts chính xác)
        if ($request->date_start) {
            $query->whereDate('order_date', '>=', $request->date_start);
        }
        if ($request->date_end) {
            $query->whereDate('order_date', '<=', $request->date_end);
        }

        if ($kw = $request->get('search')) {
            $query->where(function($q) use ($kw) {
                $q->where('cto_code', 'like', "%{$kw}%")
                  ->orWhere('ten_kh', 'like', "%{$kw}%")
                  ->orWhere('ma_kh', 'like', "%{$kw}%")
                  ->orWhere('ghi_chu', 'like', "%{$kw}%");
            });
        }

        // 2. Đếm theo trạng thái (Dựa trên thời gian & search đã lọc)
        $countQuery = clone $query;
        $counts = $countQuery->select('trang_thai', DB::raw('COUNT(*) as order_count'))
            ->groupBy('trang_thai')
            ->pluck('order_count', 'trang_thai')
            ->toArray();
        $counts['all'] = array_sum($counts);

        if ($status = $request->get('status')) {
            if ($status !== 'all') {
                $query->where('trang_thai', $status);
            }
        }

        // 5. Sắp xếp & Paginate
        $sort = $request->get('sort', 'newest');
        if ($sort === 'oldest') {
            $query->orderBy('order_date');
        } else {
            $query->orderByDesc('order_date')->orderByDesc('id');
        }

        $limit = $request->get('limit', 20);
        $orders = $query->with(['customer', 'meta'])->paginate($limit)->withQueryString();

        $customers = Customer::orderBy('ten_cty')->get(['id','ma_kh','ten_cty']);

        $ty_gia = SystemSetting::get('ty_gia', 25450);

        return view('orders.index', compact('orders', 'sort', 'counts', 'customers', 'ty_gia'));
    }

    public function show(Order $order, Request $request)
    {
        $order->load(['customer', 'items', 'meta', 'deliveryNote']);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'data' => $order]);
        }

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

            $slInput = $item['so_luong'] ?? 0;
            if (is_string($slInput)) {
                $slInput = str_replace(',', '.', str_replace('.', '', $slInput));
            }
            $sl = (float)$slInput;

            $giaInput = $item['don_gia'] ?? 0;
            if (is_string($giaInput)) {
                $giaInput = str_replace('.', '', $giaInput);
            }
            $gia = (float)$giaInput;

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

    public function exportPdf(Order $order)
    {

        $order->load(['customer', 'items', 'meta']);
        $customer = $order->customer;

        $subtotal = 0;
        foreach ($order->items as $item) {
            $subtotal += $item->so_luong * $item->don_gia;
        }
        $vat_pct    = $order->meta?->vat_percent ?? 8;
        $ty_gia     = $order->meta?->ty_gia ?? null;
        $ngay_ty_gia = $order->meta?->ngay_ty_gia ?? null;

        $vat_amount = $subtotal * ($vat_pct / 100);
        $total      = $subtotal + $vat_amount;
        $total_text = $this->numberToVietnamese((int)round($total)) . ' đồng';

        // Lấy thêm thông tin cấu hình cho Footer
        $seller_info = [
            'ten' => SystemSetting::get('ten_cong_ty', 'CÔNG TY TNHH GAMBERTE VIỆT NAM'),
            'stk' => SystemSetting::get('stk_ngan_hang', '317574324'),
            'bank'=> SystemSetting::get('ten_ngan_hang', 'NGÂN HÀNG TMCP QUÂN ĐỘI (MB BANK)'),
            'branch'=> SystemSetting::get('chi_nhanh', 'CN PGD ĐỘC LẬP - QUẬN 1'),
            'mst' => SystemSetting::get('ma_so_thue', '0317574324'),
            'sdt' => SystemSetting::get('sdt_cong_ty', '0368 301 305'),
            'dia_chi' => SystemSetting::get('dia_chi_cong_ty', 'Tòa nhà Gamberte, Quận 1, TP.HCM'),
        ];

        $pdf = Pdf::loadView('orders.pdf', compact('order', 'customer', 'subtotal', 'vat_pct', 'vat_amount', 'total', 'total_text', 'ty_gia', 'ngay_ty_gia', 'seller_info'))
            ->setPaper('a4', 'portrait')
            ->setOption('isHtml5ParserEnabled', true)
            ->setOption('isRemoteEnabled', false)
            ->setOption('defaultFont', 'DejaVu Sans');

        $filename = 'BAO_GIA_' . $order->cto_code . '_' . date('YmdHi') . '.pdf';
        return $pdf->stream($filename);
    }

    private function numberToVietnamese(int $n): string
    {
        if ($n === 0) return 'Không';
        $ones  = ['', 'một', 'hai', 'ba', 'bốn', 'năm', 'sáu', 'bảy', 'tám', 'chín'];
        $teens = ['mười', 'mười một', 'mười hai', 'mười ba', 'mười bốn', 'mười lăm', 'mười sáu', 'mười bảy', 'mười tám', 'mười chín'];
        $tens  = ['', 'mười', 'hai mươi', 'ba mươi', 'bốn mươi', 'năm mươi', 'sáu mươi', 'bảy mươi', 'tám mươi', 'chín mươi'];

        $convert3 = function(int $num) use ($ones, $teens, $tens): string {
            if ($num === 0) return '';
            $h = intdiv($num, 100); $r = $num % 100;
            $result = '';
            if ($h > 0) $result .= $ones[$h] . ' trăm ';
            if ($r >= 10 && $r < 20) {
                $result .= $teens[$r - 10];
            } elseif ($r >= 20) {
                $result .= $tens[intdiv($r, 10)];
                if ($r % 10 > 0) $result .= ' ' . $ones[$r % 10];
            } elseif ($r > 0) {
                if ($h > 0) $result .= ' lẻ ';
                $result .= $ones[$r];
            }
            return trim($result);
        };

        $billions  = intdiv($n, 1_000_000_000); $n %= 1_000_000_000;
        $millions  = intdiv($n, 1_000_000);     $n %= 1_000_000;
        $thousands = intdiv($n, 1_000);          $n %= 1_000;
        $remainder = $n;

        $parts = [];
        if ($billions)  $parts[] = $convert3($billions)  . ' tỷ';
        if ($millions)  $parts[] = $convert3($millions)  . ' triệu';
        if ($thousands) $parts[] = $convert3($thousands) . ' nghìn';
        if ($remainder) $parts[] = $convert3($remainder);

        return ucfirst(implode(' ', $parts));
    }
}
