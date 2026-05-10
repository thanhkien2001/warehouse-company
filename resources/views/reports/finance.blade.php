@extends('layouts.app')
@section('title', 'Báo cáo tài chính')

@section('content')
<div class="card" style="padding: 24px; background: #f8fafc;">

    {{-- PAGE HEADER --}}
    <div class="page-header-row" style="display:flex; justify-content:space-between; align-items:center; padding-bottom:20px; border-bottom:2.5px solid #cbd5e1; margin-bottom:20px;">
        <div style="display:flex; align-items:center; gap:16px;">
            <div style="width:56px; height:56px; background:#eff6ff; border-radius:16px; display:flex; align-items:center; justify-content:center; box-shadow:0 4px 10px rgba(59,130,246,.15); flex-shrink:0;">
                <i class="fas fa-chart-line" style="font-size:24px; color:#3b82f6;"></i>
            </div>
            <div>
                <h2 style="font-size:22px; font-weight:800; color:#0f172a; margin:0 0 4px; letter-spacing:-.5px;">Báo cáo tài chính</h2>
                <p style="margin:0; color:#64748b; font-size:13.5px;">Tổng quan doanh thu, lợi nhuận và xu hướng kinh doanh</p>
            </div>
        </div>
    </div>

    <style>
        /* ── FILTER ── */
        .ord-filter-card { padding: 14px 20px; border-bottom: 1.5px solid #f1f5f9; margin-bottom: 24px; background: #fff; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,.05); }
        .ord-filter-grid { display: flex; gap: 12px; align-items: flex-end; flex-wrap: wrap; }
        .ord-filter-item { display: flex; flex-direction: column; gap: 5px; }
        .ord-filter-item label { font-size: 13px; font-weight: 700; color: #1e293b; }
        .ord-filter-input { height: 36px; border: 1px solid #cbd5e1; border-radius: 6px; padding: 0 12px; font-size: 13px; outline: none; box-sizing: border-box; color: #1e293b; background: #fff; }
        .ord-filter-input:focus { border-color: #0070D2; box-shadow: 0 0 0 3px rgba(0,112,210,.1); }
        .ord-btn-search { height: 36px; padding: 0 16px; background: #0070D2; color: #fff; border: none; border-radius: 6px; font-weight: 600; font-size: 13px; cursor: pointer; display: flex; align-items: center; gap: 6px; }
        .ord-btn-search:hover { background: #005bb5; }
        .ord-btn-clear { height: 36px; padding: 0 16px; background: #fff; color: #ef4444; border: 1px solid #e2e8f0; border-radius: 6px; font-weight: 600; font-size: 13px; cursor: pointer; display: flex; align-items: center; gap: 6px; text-decoration: none; }
        .ord-btn-clear:hover { background: #fef2f2; border-color: #ef4444; }

        /* ── KPI BLOCKS ── */
        .kpi-row { display: flex; gap: 16px; margin-bottom: 24px; flex-wrap: wrap; }
        .kpi-box { flex: 1; min-width: 160px; background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px; box-shadow: 0 2px 4px rgba(0,0,0,.02); display: flex; flex-direction: column; position: relative; overflow: hidden; }
        .kpi-box::before { content: ""; position: absolute; top: 0; left: 0; width: 4px; height: 100%; }
        /* .kpi-box.c-blue::before { background: #3b82f6; }
        .kpi-box.c-orange::before { background: #f97316; }
        .kpi-box.c-green::before { background: #22c55e; }
        .kpi-box.c-purple::before { background: #a855f7; }
        .kpi-box.c-teal::before { background: #14b8a6; } */
        
        .kpi-header { display: flex; align-items: center; gap: 10px; margin-bottom: 8px; }
        .kpi-icon { width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 14px; }
        .kpi-box.c-blue .kpi-icon { background: #eff6ff; color: #3b82f6; }
        .kpi-box.c-orange .kpi-icon { background: #fff7ed; color: #f97316; }
        .kpi-box.c-green .kpi-icon { background: #f0fdf4; color: #22c55e; }
        .kpi-box.c-purple .kpi-icon { background: #faf5ff; color: #a855f7; }
        .kpi-box.c-teal .kpi-icon { background: #f0fdfa; color: #14b8a6; }
        
        .kpi-title { font-size: 11.5px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: .5px; margin: 0; }
        .kpi-value { font-size: 22px; font-weight: 800; color: #0f172a; margin: 4px 0; }
        .kpi-sub { font-size: 12px; font-weight: 600; color: #94a3b8; }

        /* ── CHARTS ── */
        .chart-row { display: flex; gap: 20px; margin-bottom: 20px; flex-wrap: wrap; }
        .chart-box { flex: 1; min-width: 48%; background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px; box-shadow: 0 2px 4px rgba(0,0,0,.02); }
        .chart-title { font-size: 15px; font-weight: 800; color: #1e293b; margin: 0 0 16px 0; display: flex; align-items: center; gap: 8px; }
        .chart-container { position: relative; height: 320px; width: 100%; }
        
        @media (max-width: 1024px) {
            .chart-box { min-width: 100%; }
        }
    </style>

    {{-- FILTER --}}
    <div class="ord-filter-card">
        <form method="GET" action="{{ route('reports.finance') }}" id="sr-form">
            <div class="ord-filter-grid">
                <div class="ord-filter-item">
                    <label>Từ ngày</label>
                    <input type="date" name="date_start" class="ord-filter-input" value="{{ $dateStart }}" style="width:260px;">
                </div>
                <div class="ord-filter-item">
                    <label>Đến ngày</label>
                    <input type="date" name="date_end" class="ord-filter-input" value="{{ $dateEnd }}" style="width:260px;">
                </div>
                <div class="ord-filter-item" style="flex:none;">
                    <label>&nbsp;</label>
                    <div style="display:flex; gap:8px;">
                        <button type="submit" class="ord-btn-search"><i class="fas fa-search"></i> Xem báo cáo</button>
                        <a href="{{ route('reports.finance') }}" class="ord-btn-clear"><i class="fas fa-times"></i> Xóa lọc</a>
                    </div>
                </div>
            </div>
        </form>
    </div>

    {{-- 6 KPI BLOCKS --}}
    @php
    function renderGrowth($percent) {
        if ($percent > 0) return '<span style="color:#22c55e; font-size:11.5px; font-weight:600;"><i class="fas fa-arrow-up"></i> +'. $percent .'% YoY</span>';
        if ($percent < 0) return '<span style="color:#ef4444; font-size:11.5px; font-weight:600;"><i class="fas fa-arrow-down"></i> '. $percent .'% YoY</span>';
        return '<span style="color:#94a3b8; font-size:11.5px; font-weight:600;"><i class="fas fa-minus"></i> 0% YoY</span>';
    }
    @endphp
    <div class="kpi-row">
        <!-- 1. Doanh thu -->
        <div class="kpi-box c-blue">
            <div class="kpi-header">
                <div class="kpi-icon"><i class="fas fa-suitcase"></i></div>
                <h3 class="kpi-title">Doanh Thu</h3>
            </div>
            <div class="kpi-value">{{ number_format($sumTotals['sumDoanhThu'], 0, ',', '.') }}</div>
            <div style="display:flex;align-items:center;">
                <div class="kpi-sub"style="margin-right:10px">VND</div>
                @if($growths['has_prev']) {!! renderGrowth($growths['sumDoanhThu']) !!} @endif
            </div>
        </div>

        <!-- 2. Giá vốn -->
        <div class="kpi-box c-orange">
            <div class="kpi-header">
                <div class="kpi-icon"><i class="fas fa-shopping-cart"></i></div>
                <h3 class="kpi-title">Giá Vốn (COGS)</h3>
            </div>
            <div class="kpi-value">{{ number_format($sumTotals['sumCOGS'], 0, ',', '.') }}</div>
            <div style="display:flex;align-items:center;">
                <div class="kpi-sub"style="margin-right:10px">VND</div>
                @if($growths['has_prev']) {!! renderGrowth($growths['sumCOGS']) !!} @endif
            </div>
        </div>

        <!-- 3. Lợi nhuận gộp -->
        <div class="kpi-box c-green">
            <div class="kpi-header">
                <div class="kpi-icon"><i class="fas fa-chart-line"></i></div>
                <h3 class="kpi-title">Lợi Nhuận Gộp (GP)</h3>
            </div>
            <div class="kpi-value">{{ number_format($sumTotals['sumGP'], 0, ',', '.') }}</div>
           <div style="display:flex;align-items:center;">
                <div class="kpi-sub"style="margin-right:10px">VND</div>
                @if($growths['has_prev']) {!! renderGrowth($growths['sumGP']) !!} @endif
            </div>
        </div>

        <!-- 4. Biên lợi nhuận gộp -->
        <div class="kpi-box c-purple">
            <div class="kpi-header">
                <div class="kpi-icon"><i class="fas fa-percent"></i></div>
                <h3 class="kpi-title">Biên Lợi Nhuận Gộp</h3>
            </div>
            <div class="kpi-value">{{ number_format($sumTotals['sumGM'], 2, ',', '.') }}%</div>
            <div style="display:flex;align-items:center;">
                <div class="kpi-sub"style="margin-right:10px">%</div>
                @if($growths['has_prev']) {!! renderGrowth($growths['sumGM']) !!} @endif
            </div>
        </div>

        <!-- 5. EBIT -->
        <div class="kpi-box c-teal">
            <div class="kpi-header">
                <div class="kpi-icon"><i class="fas fa-briefcase"></i></div>
                <h3 class="kpi-title">Lợi Nhuận Hoạt Động (EBIT)</h3>
            </div>
            <div class="kpi-value">{{ number_format($sumTotals['sumEBIT'], 0, ',', '.') }}</div>
            <div style="display:flex;align-items:center;">
                <div class="kpi-sub"style="margin-right:10px">VND</div>
                @if($growths['has_prev']) {!! renderGrowth($growths['sumEBIT']) !!} @endif
            </div>
        </div>

        <!-- 6. NPAT -->
        <div class="kpi-box c-blue">
            <div class="kpi-header">
                <div class="kpi-icon"><i class="fas fa-dollar-sign"></i></div>
                <h3 class="kpi-title">Lợi Nhuận Sau Thuế (NPAT)</h3>
            </div>
            <div class="kpi-value">{{ number_format($sumTotals['sumNPAT'], 0, ',', '.') }}</div>
            <div style="display:flex;align-items:center;">
                <div class="kpi-sub"style="margin-right:10px">VND</div>
                @if($growths['has_prev']) {!! renderGrowth($growths['sumNPAT']) !!} @endif
            </div>
        </div>
    </div>

    {{-- CHARTS ROW 1 --}}
    <div class="chart-row">
        <!-- 3.1 Xu hướng doanh thu, lợi nhuận (Line) -->
        <div class="chart-box">
            <h4 class="chart-title"><i class="fas fa-chart-area" style="color:#3b82f6;"></i> Xu hướng doanh thu, lợi nhuận theo thời gian</h4>
            <div class="chart-container">
                <canvas id="chart1"></canvas>
            </div>
        </div>
        <!-- 3.2 Biên lợi nhuận gộp (%) (Line) -->
        <div class="chart-box">
            <h4 class="chart-title"><i class="fas fa-chart-line" style="color:#a855f7;"></i> Biên lợi nhuận gộp (%) theo thời gian</h4>
            <div class="chart-container">
                <canvas id="chart2"></canvas>
            </div>
        </div>
    </div>

    {{-- CHARTS ROW 2 --}}
    <div class="chart-row">
        <!-- 3.3 Sản lượng bán theo thời gian (Bar) -->
        <div class="chart-box">
            <h4 class="chart-title"><i class="fas fa-chart-bar" style="color:#3b82f6;"></i> Sản lượng bán theo thời gian (KG)</h4>
            <div class="chart-container">
                <canvas id="chart3"></canvas>
            </div>
        </div>
        <!-- 3.4 Top 10 sản phẩm theo doanh thu (H-Bar) -->
        <div class="chart-box">
            <h4 class="chart-title"><i class="fas fa-list-ol" style="color:#3b82f6;"></i> Top 10 sản phẩm theo doanh thu</h4>
            <div class="chart-container">
                <canvas id="chart4"></canvas>
            </div>
        </div>
    </div>

    {{-- CHARTS ROW 3 --}}
    <div class="chart-row">
        <!-- 3.5 Top 10 sản phẩm theo lợi nhuận gộp (H-Bar) -->
        <div class="chart-box">
            <h4 class="chart-title"><i class="fas fa-list-ol" style="color:#22c55e;"></i> Top 10 sản phẩm theo lợi nhuận gộp</h4>
            <div class="chart-container">
                <canvas id="chart5"></canvas>
            </div>
        </div>
        <!-- 3.6 Top 10 khách hàng theo doanh thu (H-Bar) -->
        <div class="chart-box">
            <h4 class="chart-title"><i class="fas fa-users" style="color:#a855f7;"></i> Top 10 khách hàng theo doanh thu</h4>
            <div class="chart-container">
                <canvas id="chart6"></canvas>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>
<script>
Chart.register(ChartDataLabels);
document.addEventListener('DOMContentLoaded', function() {
    // Shared chart options
    const commonOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            datalabels: { display: false },
            legend: { position: 'top', labels: { usePointStyle: true, boxWidth: 8, font: {family: 'Inter, sans-serif', size: 12} } },
            tooltip: {
                backgroundColor: 'rgba(15, 23, 42, 0.9)',
                titleFont: {family: 'Inter, sans-serif', size: 13},
                bodyFont: {family: 'Inter, sans-serif', size: 13},
                padding: 10,
                cornerRadius: 8,
                callbacks: {
                    label: function(context) {
                        let label = context.dataset.label || '';
                        if (label) label += ': ';
                        if (context.parsed.y !== null) {
                            label += new Intl.NumberFormat('vi-VN').format(context.parsed.y);
                        }
                        return label;
                    }
                }
            }
        },
        scales: {
            x: { grid: { display: false }, ticks: { font: {family: 'Inter, sans-serif', size: 11} } },
            y: { grid: { color: '#f1f5f9' }, border: { dash: [4, 4] }, ticks: { font: {family: 'Inter, sans-serif', size: 11} } }
        }
    };

    // 3.1 Xu hướng DT, GP, NPAT (Line)
    const ctx1 = document.getElementById('chart1').getContext('2d');
    new Chart(ctx1, {
        type: 'line',
        data: {
            labels: {!! json_encode($chart1['labels']) !!},
            datasets: [
                {
                    label: 'Doanh thu',
                    data: {!! json_encode($chart1['dt']) !!},
                    borderColor: '#3b82f6',
                    backgroundColor: '#3b82f6',
                    borderWidth: 2,
                    tension: 0.3,
                    pointRadius: 3
                },
                {
                    label: 'Lợi nhuận gộp (GP)',
                    data: {!! json_encode($chart1['gp']) !!},
                    borderColor: '#22c55e',
                    backgroundColor: '#22c55e',
                    borderWidth: 2,
                    tension: 0.3,
                    pointRadius: 3
                },
                {
                    label: 'Lợi nhuận sau thuế (NPAT)',
                    data: {!! json_encode($chart1['npat']) !!},
                    borderColor: '#a855f7',
                    backgroundColor: '#a855f7',
                    borderWidth: 2,
                    tension: 0.3,
                    pointRadius: 3
                }
            ]
        },
        options: commonOptions
    });

    // 3.2 Biên lợi nhuận gộp % (Line)
    const ctx2 = document.getElementById('chart2').getContext('2d');
    const opt2 = JSON.parse(JSON.stringify(commonOptions));
    opt2.plugins.tooltip.callbacks.label = function(context) { return context.dataset.label + ': ' + context.parsed.y + '%'; };
    new Chart(ctx2, {
        type: 'line',
        data: {
            labels: {!! json_encode($chart2['labels']) !!},
            datasets: [{
                label: 'Biên lợi nhuận gộp (%)',
                data: {!! json_encode($chart2['gm']) !!},
                borderColor: '#a855f7',
                backgroundColor: 'rgba(168, 85, 247, 0.1)',
                borderWidth: 2,
                tension: 0.3,
                fill: true,
                pointRadius: 4
            }]
        },
        options: opt2
    });

    // 3.3 Sản lượng bán KG (Bar)
    const ctx3 = document.getElementById('chart3').getContext('2d');
    new Chart(ctx3, {
        type: 'bar',
        data: {
            labels: {!! json_encode($chart3['labels']) !!},
            datasets: [{
                label: 'Sản lượng (KG)',
                data: {!! json_encode($chart3['qty']) !!},
                backgroundColor: '#3b82f6',
                borderRadius: 4,
                barPercentage: 0.6
            }]
        },
        options: {
            ...JSON.parse(JSON.stringify(commonOptions)),
            layout: { padding: { top: 30 } },
            plugins: {
                ...commonOptions.plugins,
                datalabels: {
                    display: true,
                    align: 'end',
                    anchor: 'end',
                    color: '#1e293b',
                    font: { weight: 'bold', size: 10, family: 'Inter, sans-serif' },
                    formatter: function(value) { return new Intl.NumberFormat('vi-VN').format(value); }
                }
            }
        }
    });

    // Options for Horizontal Bar Charts (3.4, 3.5, 3.6)
    const hBarOptions = {
        responsive: true,
        maintainAspectRatio: false,
        indexAxis: 'y',
        layout: { padding: { right: 50 } },
        plugins: {
            legend: { display: false },
            datalabels: {
                display: true,
                align: 'end',
                anchor: 'end',
                color: '#1e293b',
                font: { weight: 'bold', size: 10, family: 'Inter, sans-serif' },
                formatter: function(value) { return new Intl.NumberFormat('vi-VN').format(value); }
            },
            tooltip: {
                backgroundColor: 'rgba(15, 23, 42, 0.9)',
                titleFont: {family: 'Inter, sans-serif', size: 12},
                bodyFont: {family: 'Inter, sans-serif', size: 13},
                callbacks: {
                    label: function(context) { return new Intl.NumberFormat('vi-VN').format(context.parsed.x); }
                }
            }
        },
        scales: {
            x: { grid: { color: '#f1f5f9', borderDash: [4,4] }, ticks: { font: {family: 'Inter, sans-serif', size: 10} } },
            y: { grid: { display: false }, ticks: { font: {family: 'Inter, sans-serif', size: 11} } }
        }
    };

    // 3.4 Top 10 SP Doanh thu
    new Chart(document.getElementById('chart4').getContext('2d'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($chart4['labels']) !!},
            datasets: [{ data: {!! json_encode($chart4['data']) !!}, backgroundColor: '#3b82f6', borderRadius: 4 }]
        },
        options: hBarOptions
    });

    // 3.5 Top 10 SP GP
    new Chart(document.getElementById('chart5').getContext('2d'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($chart5['labels']) !!},
            datasets: [{ data: {!! json_encode($chart5['data']) !!}, backgroundColor: '#22c55e', borderRadius: 4 }]
        },
        options: hBarOptions
    });

    // 3.6 Top 10 KH Doanh thu
    new Chart(document.getElementById('chart6').getContext('2d'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($chart6['labels']) !!},
            datasets: [{ data: {!! json_encode($chart6['data']) !!}, backgroundColor: '#a855f7', borderRadius: 4 }]
        },
        options: hBarOptions
    });
});
</script>
@endpush
