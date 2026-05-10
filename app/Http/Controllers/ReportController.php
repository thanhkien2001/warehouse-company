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

        // Default year for chart if no filter
        $chartYear = $dateStart ? date('Y', strtotime($dateStart)) : date('Y');

        $query = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('delivery_notes', 'orders.id', '=', 'delivery_notes.order_id')
            ->join('customers', 'orders.customer_id', '=', 'customers.id')
            ->leftJoin('product_catalog', 'order_items.ma_hang', '=', 'product_catalog.ma_hang')
            ->where('delivery_notes.trang_thai', 'Đã giao xong')
            ->select([
                DB::raw('DATE(delivery_notes.updated_at) as date'),
                'customers.ten_cty as customer_name',
                'order_items.ma_hang',
                'order_items.ten_hang',
                'order_items.so_luong',
                'product_catalog.gia_nhap',
                'product_catalog.gia_ban',
            ]);

        $allDataQuery = clone $query;
        if ($dateStart) $allDataQuery->whereDate('delivery_notes.updated_at', '>=', $dateStart);
        if ($dateEnd)   $allDataQuery->whereDate('delivery_notes.updated_at', '<=', $dateEnd);

        $allRows = $allDataQuery->get();
        
        // Tính tổng KPI Blocks hiện tại
        $sumTotals = $this->calcSummaryTotals($allRows, 0, 20);

        // Tính tổng KPI Blocks YoY (năm trước)
        $prevDataQuery = clone $query;
        $hasPrev = false;
        if ($dateStart || $dateEnd) {
            $hasPrev = true;
            if ($dateStart) {
                $prevStart = \Carbon\Carbon::parse($dateStart)->subYear()->format('Y-m-d');
                $prevDataQuery->whereDate('delivery_notes.updated_at', '>=', $prevStart);
            }
            if ($dateEnd) {
                $prevEnd = \Carbon\Carbon::parse($dateEnd)->subYear()->format('Y-m-d');
                $prevDataQuery->whereDate('delivery_notes.updated_at', '<=', $prevEnd);
            }
        } else {
            // Nếu không có filter (tính ALL), thì so sánh với rỗng
            $prevDataQuery->whereRaw('1 = 0');
        }
        $prevRows = $prevDataQuery->get();
        $prevTotals = $this->calcSummaryTotals($prevRows, 0, 20);

        // Hàm tính % tăng trưởng
        $calcGrowth = function($curr, $prev) {
            if ($prev == 0) return $curr > 0 ? 100 : 0;
            return round((($curr - $prev) / abs($prev)) * 100, 1);
        };

        $growths = [
            'sumDoanhThu' => $calcGrowth($sumTotals['sumDoanhThu'], $prevTotals['sumDoanhThu']),
            'sumCOGS'     => $calcGrowth($sumTotals['sumCOGS'], $prevTotals['sumCOGS']),
            'sumGP'       => $calcGrowth($sumTotals['sumGP'], $prevTotals['sumGP']),
            'sumGM'       => $calcGrowth($sumTotals['sumGM'], $prevTotals['sumGM']),
            'sumEBIT'     => $calcGrowth($sumTotals['sumEBIT'], $prevTotals['sumEBIT']),
            'sumNPAT'     => $calcGrowth($sumTotals['sumNPAT'], $prevTotals['sumNPAT']),
            'has_prev'    => $hasPrev
        ];

        // Chuẩn bị dữ liệu vẽ 6 biểu đồ
        
        // 3.1, 3.2, 3.3 (Dữ liệu theo tháng)
        $chartRowsQuery = clone $query;
        if ($dateStart && $dateEnd) {
            $chartRowsQuery->whereDate('delivery_notes.updated_at', '>=', $dateStart)
                           ->whereDate('delivery_notes.updated_at', '<=', $dateEnd);
        } else {
            $chartRowsQuery->whereYear('delivery_notes.updated_at', $chartYear);
        }
        $chartRows = $chartRowsQuery->get();

        $monthlyData = [];
        // Khởi tạo 12 tháng nếu không filter (hoặc filter cùng trong 1 năm)
        if (!$dateStart && !$dateEnd) {
            for ($i=1; $i<=12; $i++) {
                $m = sprintf("%04d-%02d", $chartYear, $i);
                $monthlyData[$m] = ['dt'=>0, 'gp'=>0, 'ebit'=>0, 'tax'=>0, 'npat'=>0, 'qty'=>0];
            }
        }

        foreach ($chartRows as $r) {
            $m = date('Y-m', strtotime($r->date));
            if (!isset($monthlyData[$m])) {
                $monthlyData[$m] = ['dt'=>0, 'gp'=>0, 'ebit'=>0, 'tax'=>0, 'npat'=>0, 'qty'=>0];
            }
            $sl = (float)$r->so_luong;
            $gn = (float)($r->gia_nhap ?? 0);
            $gb = (float)($r->gia_ban ?? 0);
            $cogs = $sl * $gn;
            $dt = $sl * $gb;
            $gp = $dt - $cogs;
            $ebit = $gp - 0;
            $tax = max(0, $ebit) * 0.2;
            $npat = $ebit - $tax;

            $monthlyData[$m]['dt'] += $dt;
            $monthlyData[$m]['gp'] += $gp;
            $monthlyData[$m]['ebit'] += $ebit;
            $monthlyData[$m]['tax'] += $tax;
            $monthlyData[$m]['npat'] += $npat;
            $monthlyData[$m]['qty'] += $sl;
        }
        ksort($monthlyData);

        $labelsMonth = array_keys($monthlyData);
        $labelsMonthFormatted = array_map(function($m) { return date('m/Y', strtotime($m . '-01')); }, $labelsMonth);

        $chart1 = [
            'labels' => $labelsMonthFormatted,
            'dt' => array_column($monthlyData, 'dt'),
            'gp' => array_column($monthlyData, 'gp'),
            'npat' => array_column($monthlyData, 'npat'),
        ];
        
        $chart2 = [
            'labels' => $labelsMonthFormatted,
            'gm' => array_values(array_map(function($d) { return $d['dt'] > 0 ? round($d['gp'] / $d['dt'] * 100, 2) : 0; }, $monthlyData)),
        ];

        $chart3 = [
            'labels' => $labelsMonthFormatted,
            'qty' => array_column($monthlyData, 'qty'),
        ];

        // 3.4, 3.5, 3.6 (Dữ liệu Top 10 dựa trên $allRows)
        $prodStats = [];
        $custStats = [];
        foreach ($allRows as $r) {
            $pKey = $r->ten_hang ?: $r->ma_hang;
            $cKey = $r->customer_name;

            if (!isset($prodStats[$pKey])) $prodStats[$pKey] = ['dt'=>0, 'gp'=>0];
            if (!isset($custStats[$cKey])) $custStats[$cKey] = ['dt'=>0];

            $sl = (float)$r->so_luong;
            $gn = (float)($r->gia_nhap ?? 0);
            $gb = (float)($r->gia_ban ?? 0);
            $dt = $sl * $gb;
            $gp = $dt - ($sl * $gn);

            $prodStats[$pKey]['dt'] += $dt;
            $prodStats[$pKey]['gp'] += $gp;
            $custStats[$cKey]['dt'] += $dt;
        }

        // 3.4 Top 10 SP theo Doanh thu
        uasort($prodStats, function($a, $b) { return $b['dt'] <=> $a['dt']; });
        $topProdDt = array_slice($prodStats, 0, 10, true);
        $chart4 = [
            'labels' => array_keys($topProdDt),
            'data' => array_column($topProdDt, 'dt'),
        ];

        // 3.5 Top 10 SP theo Lợi nhuận gộp
        uasort($prodStats, function($a, $b) { return $b['gp'] <=> $a['gp']; });
        $topProdGp = array_slice($prodStats, 0, 10, true);
        $chart5 = [
            'labels' => array_keys($topProdGp),
            'data' => array_column($topProdGp, 'gp'),
        ];

        // 3.6 Top 10 Khách hàng theo Doanh thu
        uasort($custStats, function($a, $b) { return $b['dt'] <=> $a['dt']; });
        $topCustDt = array_slice($custStats, 0, 10, true);
        $chart6 = [
            'labels' => array_keys($topCustDt),
            'data' => array_column($topCustDt, 'dt'),
        ];

        return view('reports.finance', compact(
            'dateStart', 'dateEnd', 'sumTotals', 'growths',
            'chart1', 'chart2', 'chart3', 'chart4', 'chart5', 'chart6'
        ));
    }

    public function summary(Request $request)
    {
        $dateStart  = $request->get('date_start');
        $dateEnd    = $request->get('date_end');
        $customerId = $request->get('customer');
        $search     = $request->get('search');
        $opex       = (float)($request->get('opex', 0));
        $taxRate    = (float)($request->get('tax_rate', 20));

        $query = DB::table('order_items')
            ->join('orders',          'order_items.order_id', '=', 'orders.id')
            ->join('delivery_notes',  'orders.id', '=', 'delivery_notes.order_id')
            ->join('customers',       'orders.customer_id',   '=', 'customers.id')
            ->leftJoin('product_catalog', 'order_items.ma_hang', '=', 'product_catalog.ma_hang')
            ->leftJoin('order_meta',  'orders.id', '=', 'order_meta.order_id')
            ->where('delivery_notes.trang_thai', 'Đã giao xong')
            ->select([
                'orders.id as order_id',
                DB::raw('DATE(delivery_notes.updated_at) as date'),
                'orders.cto_code',
                'delivery_notes.dn_code',
                'customers.ten_cty as customer_name',
                'customers.khu_vuc',
                'order_items.ma_hang',
                'order_items.ten_hang',
                'order_items.so_luong',
                'order_items.don_gia',
                'order_items.thanh_tien',
                'product_catalog.gia_nhap',
                'product_catalog.gia_ban',
                DB::raw('NULL as invoice_no'),
            ]);

        if ($dateStart)   $query->whereDate('delivery_notes.updated_at', '>=', $dateStart);
        if ($dateEnd)     $query->whereDate('delivery_notes.updated_at', '<=', $dateEnd);
        if ($customerId)  $query->where('orders.customer_id', $customerId);
        if ($search)      $query->where(function($q) use ($search) {
            $q->where('delivery_notes.dn_code', 'like', "%{$search}%")
              ->orWhere('orders.cto_code', 'like', "%{$search}%")
              ->orWhere('customers.ten_cty', 'like', "%{$search}%")
              ->orWhere('order_items.ma_hang', 'like', "%{$search}%")
              ->orWhere('order_items.ten_hang', 'like', "%{$search}%");
        });

        $query->orderByDesc('delivery_notes.updated_at')->orderByDesc('orders.id');

        // Tính tổng toàn bộ (trước paginate)
        $allRows = $query->get();
        $sumTotals = $this->calcSummaryTotals($allRows, $opex, $taxRate);

        $rows = $query->paginate(20)->withQueryString();

        // Tính số liệu cho từng dòng
        $rows->getCollection()->transform(function($row) use ($opex, $taxRate) {
            $sl        = (float)$row->so_luong;
            $giaNhap   = (float)($row->gia_nhap ?? 0);
            $giaBan    = (float)($row->gia_ban ?? 0); // từ product_catalog

            $row->cogs         = $sl * $giaNhap;
            $row->doanh_thu    = $sl * $giaBan;       // TO = SL * gia_ban
            $row->gross_profit = $row->doanh_thu - $row->cogs;
            $row->gross_margin = $row->doanh_thu > 0
                ? round($row->gross_profit / $row->doanh_thu * 100, 2) : 0;
            $row->ebit         = $row->gross_profit - $opex;
            $row->tax_expense  = max(0, $row->ebit) * ($taxRate / 100);
            $row->npat         = $row->ebit - $row->tax_expense;
            $row->opex_val     = $opex;
            $row->tax_rate_val = $taxRate;
            return $row;
        });

        $customers = \App\Models\Customer::orderBy('ten_cty')->get(['id','ma_kh','ten_cty']);

        return view('reports.summary', compact(
            'rows', 'sumTotals',
            'dateStart', 'dateEnd', 'customerId', 'search', 'opex', 'taxRate',
            'customers'
        ));
    }

    private function calcSummaryTotals($allRows, float $opex, float $taxRate): array
    {
        $sumDoanhThu = 0; $sumGP = 0; $sumEBIT = 0; $sumTax = 0; $sumNPAT = 0; $sumCOGS = 0;
        foreach ($allRows as $row) {
            $sl     = (float)$row->so_luong;
            $gn     = (float)($row->gia_nhap ?? 0);
            $gb     = (float)($row->gia_ban ?? 0); // dùng gia_ban từ catalog
            $cogs   = $sl * $gn;
            $dt     = $sl * $gb;                   // TO = SL * gia_ban
            $gp     = $dt - $cogs;
            $ebit   = $gp - $opex;
            $tax    = max(0, $ebit) * ($taxRate / 100);
            $npat   = $ebit - $tax;
            
            $sumCOGS     += $cogs;
            $sumDoanhThu += $dt;
            $sumGP       += $gp;
            $sumEBIT     += $ebit;
            $sumTax      += $tax;
            $sumNPAT     += $npat;
        }
        $sumGM = $sumDoanhThu > 0 ? round($sumGP / $sumDoanhThu * 100, 2) : 0;
        return compact('sumDoanhThu', 'sumCOGS', 'sumGP', 'sumGM', 'sumEBIT', 'sumTax', 'sumNPAT');
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
