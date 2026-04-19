<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use App\Models\DeliveryNote;
use App\Models\Payment;
use App\Models\ActivityLog;
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $filter    = $request->get('filter', 'month');
        $dateStart = $request->get('date_start');
        $dateEnd   = $request->get('date_end');

        [$start, $end] = $this->getDateRange($filter, $dateStart, $dateEnd);

        // KPI
        $tongKH    = Customer::count();
        $tongDon   = Order::whereBetween('order_date', [$start, $end])->count();

        // Doanh thu (tổng thanh tiền của đơn trong kỳ × VAT)
        $ordersInPeriod = Order::whereBetween('order_date', [$start, $end])
            ->with(['items', 'meta'])->get();

        $doanhThu = $ordersInPeriod->sum(function ($o) {
            $total = $o->items->sum('thanh_tien');
            $vat   = $o->meta->vat_percent ?? 8;
            return $total * (1 + $vat / 100);
        });

        // Tồn kho
        $inventoryReport = InventoryService::getReport();
        $tongSP   = collect($inventoryReport)->sum('tong_nhap');
        $tongTon  = collect($inventoryReport)->sum('con_lai');

        // Chart doanh thu theo tháng (12 tháng gần nhất)
        $revenueData   = $this->getRevenueChart();
        $ordersChart   = $this->getOrdersChart();
        $statusChart   = $this->getStatusChart();
        $topProducts   = $this->getTopProducts();
        $inventoryTop10 = collect($inventoryReport)
            ->sortByDesc('tong_nhap')->take(10)->values()->toArray();

        // Cảnh báo công nợ ≤3 ngày
        $debtWarnings = $this->getDebtWarnings();

        // Nhật ký hoạt động
        $activities = ActivityLog::with('user')
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        return view('dashboard.index', compact(
            'tongKH', 'tongDon', 'doanhThu', 'tongSP', 'tongTon',
            'revenueData', 'ordersChart', 'statusChart', 'topProducts',
            'inventoryTop10', 'debtWarnings', 'activities',
            'filter', 'dateStart', 'dateEnd'
        ));
    }

    private function getDateRange(string $filter, ?string $start, ?string $end): array
    {
        $today = now();
        return match ($filter) {
            'quarter' => [
                $today->copy()->firstOfQuarter()->toDateString(),
                $today->copy()->lastOfQuarter()->toDateString(),
            ],
            'year' => [
                $today->copy()->startOfYear()->toDateString(),
                $today->copy()->endOfYear()->toDateString(),
            ],
            'custom' => [$start ?? $today->toDateString(), $end ?? $today->toDateString()],
            default  => [ // month
                $today->copy()->startOfMonth()->toDateString(),
                $today->copy()->endOfMonth()->toDateString(),
            ],
        };
    }

    private function getRevenueChart(): array
    {
        $months = [];
        for ($i = 11; $i >= 0; $i--) {
            $months[] = now()->subMonths($i)->format('Y-m');
        }

        $rows = Order::select(
                DB::raw("DATE_FORMAT(order_date, '%Y-%m') as ym"),
                DB::raw('SUM(oi.thanh_tien) as rev_total')
            )
            ->join('order_items as oi', 'orders.id', '=', 'oi.order_id')
            ->where('order_date', '>=', now()->subMonths(11)->startOfMonth())
            ->groupBy('ym')
            ->get();

        $revenues = [];
        foreach ($rows as $row) {
            $revenues[(string)$row->ym] = (float)$row->rev_total;
        }

        return [
            'labels' => array_map(fn($m) => date('M/y', strtotime($m . '-01')), $months),
            'data'   => array_map(fn($m) => $revenues[$m] ?? 0, $months),
        ];
    }

    private function getOrdersChart(): array
    {
        $months = [];
        for ($i = 5; $i >= 0; $i--) {
            $months[] = now()->subMonths($i)->format('Y-m');
        }

        $rows = Order::select(
                DB::raw("DATE_FORMAT(order_date, '%Y-%m') as ym"),
                DB::raw('COUNT(*) as cnt_total')
            )
            ->where('order_date', '>=', now()->subMonths(5)->startOfMonth())
            ->groupBy('ym')
            ->get();

        $counts = [];
        foreach ($rows as $row) {
            $counts[(string)$row->ym] = (int)$row->cnt_total;
        }

        return [
            'labels' => array_map(fn($m) => date('m/y', strtotime($m . '-01')), $months),
            'data'   => array_map(fn($m) => $counts[$m] ?? 0, $months),
        ];
    }

    private function getStatusChart(): array
    {
        $statuses = ['Chờ xác nhận', 'Đang xử lý', 'Đang vận chuyển', 'Hoàn thành', 'Đã hủy'];

        $rows = Order::select('trang_thai', DB::raw('COUNT(*) as cnt'))
            ->groupBy('trang_thai')
            ->get();

        $counts = [];
        foreach ($rows as $row) {
            $counts[$row->trang_thai] = (int)$row->cnt;
        }

        return [
            'labels' => $statuses,
            'data'   => array_map(fn($s) => $counts[$s] ?? 0, $statuses),
        ];
    }

    private function getTopProducts(): array
    {
        return DB::table('order_items')
            ->select('ma_hang', 'ten_hang', DB::raw('SUM(so_luong) as total_qty'))
            ->whereNotNull('ma_hang')
            ->groupBy('ma_hang', 'ten_hang')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get()
            ->toArray();
    }

    private function getDebtWarnings(): array
    {
        $dns = DeliveryNote::with('order.meta')
            ->whereNotIn('trang_thai', ['Đã hủy'])
            ->whereRaw('han_thanh_toan > 0')
            ->get();

        $warnings = [];
        foreach ($dns as $dn) {
            if (!$dn->delivery_date) continue;
            $deadline = $dn->delivery_date->addDays($dn->han_thanh_toan);
            $daysLeft = (int) now()->startOfDay()->diffInDays($deadline, false);

            if ($daysLeft <= 3) {
                // Check còn nợ không
                $paid  = Payment::where('cto_code', $dn->cto_code)->sum('so_tien');
                $total = \App\Models\OrderItem::where('cto_code', $dn->cto_code)->sum('thanh_tien');
                $vat   = $dn->order->meta->vat_percent ?? 8;
                $due   = $total * (1 + $vat / 100);

                if (($due - $paid) < 1000) continue;

                $warnings[] = [
                    'kh_name'     => $dn->ten_kh,
                    'dn_code'     => $dn->dn_code,
                    'days_left'   => $daysLeft,
                    'deadline_str' => $deadline->format('d/m/Y'),
                ];
            }
        }

        usort($warnings, fn($a, $b) => $a['days_left'] <=> $b['days_left']);
        return array_slice($warnings, 0, 5);
    }
}
