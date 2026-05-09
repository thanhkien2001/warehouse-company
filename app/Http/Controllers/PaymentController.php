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
use Carbon\Carbon;

class PaymentController extends Controller
{
    public function indexDebt(Request $request)
    {
        // Load toàn bộ danh sách công nợ từ service
        $all = DebtService::getAll();

        // Apply filters
        $filtered = $this->applyDebtFilters($all, $request);

        // Tách quá hạn và tất cả
        $overdueDebts = array_values(array_filter($filtered, fn($d) => $d['is_overdue'] ?? false));

        // Phân trang thủ công cho $allDebts
        $perPage = (int) $request->get('limit', 10);
        $page = (int) max(1, $request->get('page', 1));
        $items = collect($filtered);
        
        $allDebts = new LengthAwarePaginator(
            $items->forPage($page, $perPage)->values(),
            $items->count(),
            $perPage,
            $page,
            [
                'path' => route('debt.index'),
                'query' => $request->query(),
            ]
        );

        // Load customers cho dropdown
        $customers = Customer::orderBy('ten_cty')->get();

        return view('payments.debt', compact('allDebts', 'overdueDebts', 'customers'));
    }

    /**
     * Apply filters to debt list
     */
    private function applyDebtFilters(array $all, Request $request): array
    {
        $filtered = $all;

        // Filter by date range (ngay_giao)
        if ($request->filled('date_start')) {
            $startDate = Carbon::parse($request->input('date_start'))->startOfDay();
            $filtered = array_filter($filtered, function($d) use ($startDate) {
                if (!isset($d['ngay_giao']) || !$d['ngay_giao']) return true;
                try {
                    // Try multiple formats: YYYY-MM-DD or DD/MM/YYYY
                    $date = null;
                    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $d['ngay_giao'])) {
                        $date = Carbon::parse($d['ngay_giao'], 'Y-m-d')->startOfDay();
                    } else {
                        $date = Carbon::createFromFormat('d/m/Y', $d['ngay_giao'])->startOfDay();
                    }
                    return $date->gte($startDate);
                } catch (\Exception $e) {
                    return true;
                }
            });
        }

        if ($request->filled('date_end')) {
            $endDate = Carbon::parse($request->input('date_end'))->endOfDay();
            $filtered = array_filter($filtered, function($d) use ($endDate) {
                if (!isset($d['ngay_giao']) || !$d['ngay_giao']) return true;
                try {
                    $date = null;
                    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $d['ngay_giao'])) {
                        $date = Carbon::parse($d['ngay_giao'], 'Y-m-d')->startOfDay();
                    } else {
                        $date = Carbon::createFromFormat('d/m/Y', $d['ngay_giao'])->startOfDay();
                    }
                    return $date->lte($endDate);
                } catch (\Exception $e) {
                    return true;
                }
            });
        }

        // Filter by customer
        if ($request->filled('customer')) {
            $customerId = $request->input('customer');
            $filtered = array_filter($filtered, function($d) use ($customerId) {
                return isset($d['customer_id']) && $d['customer_id'] == $customerId;
            });
        }

        // Filter by text search (cto_code, ten_kh, ma_kh)
        if ($request->filled('search')) {
            $q = strtolower(trim($request->input('search')));
            $filtered = array_filter($filtered, function($d) use ($q) {
                $ctoMatch = isset($d['cto_code']) && strpos(strtolower($d['cto_code']), $q) !== false;
                $khMatch = isset($d['ten_kh']) && strpos(strtolower($d['ten_kh']), $q) !== false;
                $maKhMatch = isset($d['ma_kh']) && strpos(strtolower($d['ma_kh']), $q) !== false;
                return $ctoMatch || $khMatch || $maKhMatch;
            });
        }

        // Filter by status (con-han, sap-het-han, qua-han)
        if ($request->filled('sort') && $request->input('sort') !== 'all') {
            $status = $request->input('sort');
            $filtered = array_filter($filtered, function($d) use ($status) {
                // Lấy tinh_trang từ item (có thể là string hoặc key)
                $tinh_trang = strtolower($d['tinh_trang'] ?? '');
                $isOverdue = $d['is_overdue'] ?? false;
                $daysLeft = $d['days_left'] ?? null;
                
                // Normalize tinh_trang value
                if (strpos($tinh_trang, 'quá') !== false || strpos($tinh_trang, 'qua') !== false) {
                    $tinh_trang = 'qua-han';
                } elseif (strpos($tinh_trang, 'đến') !== false || strpos($tinh_trang, 'sắp') !== false) {
                    $tinh_trang = 'sap-het-han';
                } elseif (strpos($tinh_trang, 'còn') !== false) {
                    $tinh_trang = 'con-han';
                }

                if ($status === 'qua-han') {
                    return $tinh_trang === 'qua-han' || $isOverdue || ($daysLeft !== null && $daysLeft < 0);
                } elseif ($status === 'sap-het-han') {
                    return $tinh_trang === 'sap-het-han' || ($daysLeft !== null && $daysLeft >= 0 && $daysLeft <= 3);
                } elseif ($status === 'con-han') {
                    return $tinh_trang === 'con-han' || ($daysLeft !== null && $daysLeft > 3);
                }
                return true;
            });
        }
        return $filtered;
    }

    public function index(Request $request)
    {
        $query = Payment::query();

        if ($request->date_start) {
            $query->whereDate('payment_date', '>=', $request->date_start);
        }
        if ($request->date_end) {
            $query->whereDate('payment_date', '<=', $request->date_end);
        }
        if ($request->customer) {
            $query->where('ma_kh', $request->customer);
        }

        if ($kw = $request->get('search')) {
            $query->where(function($q) use ($kw) {
                $q->where('ma_tt', 'like', "%{$kw}%")
                  ->orWhere('cto_code', 'like', "%{$kw}%")
                  ->orWhere('ma_kh', 'like', "%{$kw}%")
                  ->orWhereHas('customer', function($cq) use ($kw) {
                      $cq->where('ten_cty', 'like', "%{$kw}%");
                  });
            });
        }

        $payments = $query->with(['order.customer', 'order.items', 'order.meta'])->orderByDesc('id')
            ->paginate(20)->withQueryString();

        // Transform collection to add calculated fields
        $payments->getCollection()->transform(function($p) {
            $order = $p->order;
            if ($order) {
                $tongDon = $order->total_with_vat;
            } else {
                $tongDon = 0;
            }
            
            $daTra     = Payment::where('cto_code', $p->cto_code)->sum('so_tien');
            $conLai    = max(0, $tongDon - $daTra);

            $p->ten_kh   = $order?->customer?->ten_cty ?? $p->ma_kh;
            $p->khu_vuc  = $order?->customer?->khu_vuc ?? '';
            $p->tong_don = round($tongDon);
            $p->con_lai  = round($conLai);
            return $p;
        });

        // Lấy danh sách đơn hàng chưa thanh toán xong cho Modal
        $unpaidOrders = [];
        $allOrders = Order::whereNotIn('trang_thai', ['Đã hủy'])
            ->with(['items', 'meta', 'customer'])
            ->get();

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

        $allCustomers = Customer::orderBy('ten_cty')->get(['id', 'ma_kh', 'ten_cty']);
        return view('payments.index', compact('payments', 'unpaidOrders', 'allCustomers'));
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
            'payment_date' => $request->payment_date ? \Carbon\Carbon::parse($request->payment_date) : now(),
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

    public function exportDebt(Request $request)
    {
        $mode = $request->input('export_mode', 'all');
        $ctoList = [];
        
        if ($mode === 'selected') {
            $ctoStr = $request->input('cto_codes', '');
            $ctoList = array_filter(explode(',', $ctoStr));
        }

        // Load dữ liệu (áp dụng filters)
        $all = DebtService::getAll();
        $filtered = $this->applyDebtFilters($all, $request);

        // Nếu selected, lọc theo cto_codes
        if ($mode === 'selected' && !empty($ctoList)) {
            $filtered = array_filter($filtered, function($d) use ($ctoList) {
                return in_array($d['cto_code'], $ctoList);
            });
        }

        // Tạo file Excel
        return $this->generateDebtExcel($filtered, $mode);
    }

    private function generateDebtExcel(array $data, string $mode)
    {
        $fileName = 'cong-no-' . date('Ymd-His') . '.csv';
        $fp = fopen('php://memory', 'w');
        
        // Set encoding for UTF-8 BOM (Excel sẽ đọc đúng tiếng Việt)
        fwrite($fp, "\xEF\xBB\xBF");
        
        // Header
        fputcsv($fp, [
            'Mã CTO',
            'Khách Hàng',
            'Mã KH',
            'Tổng Đơn (VNĐ)',
            'Đã Trả (VNĐ)',
            'Còn Lại (VNĐ)',
            'Ngày Giao',
            'Hạn (Ngày)',
            'Tình Trạng',
        ], ',');
        
        // Data rows
        foreach ($data as $row) {
            fputcsv($fp, [
                $row['cto_code'] ?? '',
                $row['ten_kh'] ?? '',
                $row['ma_kh'] ?? '',
                number_format($row['tong_don'] ?? 0),
                number_format(($row['tong_don'] ?? 0) - ($row['con_lai'] ?? 0)),
                number_format($row['con_lai'] ?? 0),
                $row['ngay_giao'] ?? '',
                $row['so_ngay_han'] ?? 0,
                $row['tinh_trang'] ?? '',
            ], ',');
        }
        
        rewind($fp);
        $csv = stream_get_contents($fp);
        fclose($fp);
        
        return response($csv)
            ->header('Content-Type', 'text/csv; charset=UTF-8')
            ->header('Content-Disposition', "attachment; filename=\"{$fileName}\"");
    }
}
