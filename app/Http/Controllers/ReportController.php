<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Payment;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function finance(Request $request)
    {
        $dateStart = $request->get('date_start');
        $dateEnd   = $request->get('date_end');
        $filter    = $request->get('filter', 'all');

        // Logic lọc ngày tương tự dashboard
        if ($filter !== 'all' && (!$dateStart || !$dateEnd)) {
            [$dateStart, $dateEnd] = $this->getDateRange($filter);
        }

        // 1. KPI - Tổng hợp theo kỳ
        $queryOrders = Order::whereNotIn('trang_thai', ['Đã hủy']);
        $queryPayments = Payment::query();

        if ($dateStart && $dateEnd) {
            $queryOrders->whereBetween('order_date', [$dateStart, $dateEnd]);
            $queryPayments->whereBetween('payment_date', [$dateStart, $dateEnd]);
        }

        $allOrders = $queryOrders->with(['items', 'meta'])->get();
        
        $tongDoanhThu = $allOrders->sum(function($o) {
            $base = $o->items->sum('thanh_tien');
            $vat  = $o->meta->vat_percent ?? 8;
            return $base * (1 + $vat / 100);
        });

        $tongDaThu = $queryPayments->sum('so_tien');
        $tongCongNo = $tongDoanhThu - $tongDaThu; // Đây là công nợ phát sinh trong kỳ nếu có filter
        
        // Nếu không lọc ngày, đây là tổng công nợ hệ thống
        if (!$dateStart) {
            $totalRev = Order::whereNotIn('trang_thai', ['Đã hủy'])->with(['items', 'meta'])->get()->sum(function($o) {
                return $o->items->sum('thanh_tien') * (1 + ($o->meta->vat_percent ?? 8) / 100);
            });
            $totalPaid = Payment::sum('so_tien');
            $currentTotalDebt = $totalRev - $totalPaid;
        } else {
            $currentTotalDebt = $tongCongNo;
        }

        $tyLeThuHoi = $tongDoanhThu > 0 ? ($tongDaThu / $tongDoanhThu) * 100 : 0;

        // 2. Biểu đồ Doanh thu vs Thu tiền (12 tháng gần nhất hoặc theo kỳ)
        $chartMonthly = $this->getMonthlyComparison($dateStart, $dateEnd);

        // 3. Doanh thu theo khu vực
        $chartRegion = $this->getRevenueByRegion($dateStart, $dateEnd);

        // 4. Top 10 khách hàng doanh thu cao nhất
        $topCustomers = $this->getTopCustomers($dateStart, $dateEnd);

        return view('reports.finance', compact(
            'tongDoanhThu', 'tongDaThu', 'tongCongNo', 'tyLeThuHoi', 'currentTotalDebt',
            'chartMonthly', 'chartRegion', 'topCustomers',
            'dateStart', 'dateEnd', 'filter'
        ));
    }

    private function getDateRange(string $filter): array
    {
        $today = now();
        return match ($filter) {
            'month'   => [$today->copy()->startOfMonth()->toDateString(), $today->copy()->endOfMonth()->toDateString()],
            'last_month' => [now()->subMonth()->startOfMonth()->toDateString(), now()->subMonth()->endOfMonth()->toDateString()],
            'quarter' => [$today->copy()->firstOfQuarter()->toDateString(), $today->copy()->lastOfQuarter()->toDateString()],
            'year'    => [$today->copy()->startOfYear()->toDateString(), $today->copy()->endOfYear()->toDateString()],
            default   => [null, null],
        };
    }

    private function getMonthlyComparison($start, $end)
    {
        $months = [];
        if ($start && $end) {
            $s = \Carbon\Carbon::parse($start)->startOfMonth();
            $e = \Carbon\Carbon::parse($end)->endOfMonth();
            while ($s <= $e) {
                $months[] = $s->format('Y-m');
                $s->addMonth();
            }
            if (count($months) > 24) $months = array_slice($months, -12); // Giới hạn nếu quá dài
        } else {
            for ($i = 11; $i >= 0; $i--) {
                $months[] = now()->subMonths($i)->format('Y-m');
            }
        }

        $minDate = \Carbon\Carbon::parse($months[0] . '-01')->toDateString();

        // Doanh thu theo tháng
        $revenueRows = DB::table('orders')
            ->join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->select(DB::raw("DATE_FORMAT(order_date, '%Y-%m') as ym"), DB::raw('SUM(thanh_tien) as total'))
            ->where('trang_thai', '!=', 'Đã hủy')
            ->where('order_date', '>=', $minDate)
            ->groupBy('ym')
            ->get()->keyBy('ym');

        // Thu tiền theo tháng
        $paymentRows = DB::table('payments')
            ->select(DB::raw("DATE_FORMAT(payment_date, '%Y-%m') as ym"), DB::raw('SUM(so_tien) as total'))
            ->where('payment_date', '>=', $minDate)
            ->groupBy('ym')
            ->get()->keyBy('ym');

        $dataRevenue = [];
        $dataPayment = [];
        $labels = [];

        foreach ($months as $m) {
            $labels[] = date('m/Y', strtotime($m . '-01'));
            $rev = $revenueRows->get($m)->total ?? 0;
            $dataRevenue[] = round($rev * 1.08); 
            $dataPayment[] = (float)($paymentRows->get($m)->total ?? 0);
        }

        return [
            'labels' => $labels,
            'revenue' => $dataRevenue,
            'payment' => $dataPayment
        ];
    }

    private function getRevenueByRegion($start, $end)
    {
        $query = DB::table('orders')
            ->join('customers', 'orders.customer_id', '=', 'customers.id')
            ->join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->select('customers.khu_vuc', DB::raw('SUM(order_items.thanh_tien) as total'))
            ->where('orders.trang_thai', '!=', 'Đã hủy');

        if ($start && $end) {
            $query->whereBetween('order_date', [$start, $end]);
        }

        $rows = $query->groupBy('customers.khu_vuc')->get();

        $labels = [];
        $data = [];
        foreach ($rows as $row) {
            $labels[] = $row->khu_vuc ?: 'Chưa phân loại';
            $data[] = round($row->total * 1.08);
        }

        return ['labels' => $labels, 'data' => $data];
    }

    private function getTopCustomers($start, $end)
    {
        $query = DB::table('orders')
            ->join('customers', 'orders.customer_id', '=', 'customers.id')
            ->join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->select('customers.ten_cty', DB::raw('SUM(order_items.thanh_tien) as total'))
            ->where('orders.trang_thai', '!=', 'Đã hủy');

        if ($start && $end) {
            $query->whereBetween('order_date', [$start, $end]);
        }

        $rows = $query->groupBy('customers.id', 'customers.ten_cty')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        $labels = [];
        $data = [];
        foreach ($rows as $row) {
            $labels[] = $row->ten_cty;
            $data[] = round($row->total * 1.08);
        }

        return ['labels' => $labels, 'data' => $data];
    }
}
