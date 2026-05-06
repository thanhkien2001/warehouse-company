<?php

namespace App\Http\Controllers;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

use App\Models\Payment;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Customer;
use App\Services\DebtService;
use App\Services\CodeGeneratorService;
use App\Services\LogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    public function indexDebt(Request $request)
    {
        $all = DebtService::getAll();
        $overdueDebts = array_filter($all, fn($d) => $d['is_overdue']);

        // Phân trang thủ công cho $allDebts
        $perPage = $request->get('limit', 10);
        $page = $request->get('page', Paginator::resolveCurrentPage() ?: 1);
        $items = collect($all);
        $allDebts = new LengthAwarePaginator(
            $items->forPage($page, $perPage),
            $items->count(),
            $perPage,
            $page,
            ['path' => Paginator::resolveCurrentPath()]
        );
        $allDebts->appends($request->all());

        return view('payments.debt', compact('allDebts', 'overdueDebts'));
    }

    public function index(Request $request)
    {
        $query = Payment::query();

        $filter = $request->get('filter', 'all');
        if ($filter === 'month') {
            $query->whereMonth('payment_date', now()->month)->whereYear('payment_date', now()->year);
        } elseif ($filter === 'year') {
            $query->whereYear('payment_date', now()->year);
        } elseif ($filter === 'custom' && $request->date_start && $request->date_end) {
            $query->whereBetween('payment_date', [$request->date_start, $request->date_end]);
        }

        if ($kw = $request->get('search')) {
            $query->where(function($q) use ($kw) {
                $q->where('ma_tt', 'like', "%{$kw}%")
                  ->orWhere('cto_code', 'like', "%{$kw}%")
                  ->orWhere('ma_kh', 'like', "%{$kw}%");
            });
        }

        $payments = $query->orderByDesc('payment_date')->orderByDesc('id')
            ->paginate(20)->withQueryString();

        // Thêm thông tin KH
        $maKHs = $payments->pluck('ma_kh')->unique();
        $customers = Customer::whereIn('ma_kh', $maKHs)
            ->get(['ma_kh','ten_cty','ma_so_thue','sdt','dia_chi','khu_vuc'])
            ->keyBy('ma_kh');

        // Thêm tổng đơn và còn lại cho mỗi payment
        $paymentsData = $payments->map(function($p) use ($customers) {
            $kh       = $customers->get($p->ma_kh);
            $order    = Order::where('cto_code', $p->cto_code)->with(['items', 'meta'])->first();
            
            $tongItems = $order ? $order->items->sum('thanh_tien') : 0;
            $vat       = $order?->meta?->vat_percent ?? 8;
            $tongDon   = $tongItems * (1 + $vat / 100);
            
            $daTra    = Payment::where('cto_code', $p->cto_code)->sum('so_tien');
            $conLai   = max(0, $tongDon - $daTra);

            return array_merge($p->toArray(), [
                'ten_kh'   => $kh?->ten_cty ?? $p->ma_kh,
                'mst'      => $kh?->ma_so_thue ?? '',
                'sdt'      => $kh?->sdt ?? '',
                'dia_chi'  => $kh?->dia_chi ?? '',
                'khu_vuc'  => $kh?->khu_vuc ?? '',
                'tong_don' => round($tongDon),
                'con_lai'  => round($conLai),
            ]);
        });

        // Lấy danh sách đơn hàng chưa thanh toán xong để phục vụ Modal tạo mới
        $allOrders = Order::whereNotIn('trang_thai', ['Đã hủy'])
            ->with(['items', 'meta', 'customer'])
            ->get();

        $unpaidOrders = [];
        foreach ($allOrders as $o) {
            $tongItems = $o->items->sum('thanh_tien');
            $vat       = $o->meta->vat_percent ?? 8;
            $tongDon   = $tongItems * (1 + $vat / 100);
            $daTra     = Payment::where('cto_code', $o->cto_code)->sum('so_tien');
            $conLai    = $tongDon - $daTra;

            if ($conLai > 0) {
                $unpaidOrders[] = [
                    'ma_don'  => $o->cto_code,
                    'ten_kh'  => $o->customer->ten_cty ?? $o->ma_kh,
                    'con_lai' => round($conLai),
                    'customer'=> $o->customer
                ];
            }
        }

        return view('payments.index', compact('payments', 'paymentsData', 'unpaidOrders', 'filter'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'cto_code' => 'required|string|exists:orders,cto_code',
            'so_tien'  => 'required|numeric|min:1',
            'ghi_chu'  => 'nullable|string',
        ]);

        $order    = Order::where('cto_code', $data['cto_code'])->firstOrFail();
        $user     = Auth::user();
        $maTT     = CodeGeneratorService::generateMaTT();

        Payment::create([
            'ma_tt'      => $maTT,
            'order_id'   => $order->id,
            'cto_code'   => $order->cto_code,
            'customer_id'=> $order->customer_id,
            'ma_kh'      => $order->ma_kh,
            'so_tien'    => $data['so_tien'],
            'nguoi_thu'  => $user->display_name ?? $user->username,
            'ghi_chu'    => $data['ghi_chu'] ?? null,
        ]);

        LogService::log('Ghi nhận thanh toán', "TT [{$maTT}] đơn [{$order->cto_code}] - " . number_format($data['so_tien']) . " VNĐ");

        return response()->json(['success' => true, 'message' => "Ghi nhận thanh toán [{$maTT}] thành công!"]);
    }

    public function getDebtInfo(string $ctoCode)
    {
        $order = Order::where('cto_code', $ctoCode)->with(['items', 'meta', 'customer'])->first();
        if (!$order) {
            return response()->json(['error' => 'Không tìm thấy đơn hàng.'], 404);
        }

        $tongItems = $order->items->sum('thanh_tien');
        $vat       = $order->meta->vat_percent ?? 8;
        $tongDon   = $tongItems * (1 + $vat / 100);
        $daTra     = Payment::where('cto_code', $ctoCode)->sum('so_tien');
        $conLai    = max(0, $tongDon - $daTra);

        return response()->json([
            'cto_code' => $ctoCode,
            'ma_kh'    => $order->ma_kh,
            'ten_kh'   => $order->ten_kh,
            'tong_don' => round($tongDon),
            'da_tra'   => round($daTra),
            'con_lai'  => round($conLai),
        ]);
    }
}
