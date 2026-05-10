@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Tổng Quan Kinh Doanh')
@section('page-subtitle', 'Bảng điều khiển và biểu đồ thống kê hoạt động hệ thống')

@push('styles')
<style>
    /* Legacy Dashboard Design System */
    .dash-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-top: 15px;
        margin-bottom: 25px;
    }

    .dash-card {
        background: #ffffff;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
        border: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        gap: 20px;
        transition: all 0.3s;
        position: relative;
        overflow: hidden;
    }

    .dash-icon-wrap {
        width: 65px;
        height: 65px;
        border-radius: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        flex-shrink: 0;
        box-shadow: inset 0 -3px 0 rgba(0, 0, 0, 0.1);
        transition: transform 0.3s;
    }

    /* .dash-card:hover .dash-icon-wrap {
        transform: rotate(-10deg) scale(1.08);
    } */

    .bg-blue { background: #eff6ff; color: #3b82f6; }
    .bg-purple { background: #faf5ff; color: #8b5cf6; }
    .bg-orange { background: #fff7ed; color: #f59e0b; }
    .bg-green { background: #f0fdf4; color: #10b981; }
    .bg-red { background: #fef2f2; color: #ef4444; }

    .dash-info {
        display: flex;
        flex-direction: column;
        z-index: 1;
        width: 100%;
    }

    .dash-info h5 {
        margin: 0;
        font-size: 13px;
        color: #64748b;
        font-weight: 700;
        text-transform: uppercase;
    }

    .dash-info h2 {
        margin: 6px 0 0 0;
        font-size: 24px;
        color: #0f172a;
        font-weight: 900;
    }

    .chart-box {
        background: #ffffff;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
        border: 1px solid #f1f5f9;
        display: flex;
        flex-direction: column;
        margin-bottom: 20px;
    }

    .chart-title {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 15px;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 20px;
        margin-top: 0;
    }

    .chart-canvas {
        position: relative;
        height: 300px;
        width: 100%;
    }

    .warning-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 15px;
        background: #fef2f2;
        border-radius: 8px;
        border-left: 4px solid #ef4444;
        transition: 0.2s;
        margin-bottom: 10px;
    }

    .warning-item:hover {
        transform: translateX(4px);
        box-shadow: 0 4px 8px rgba(239, 68, 68, 0.1);
        background: #fee2e2;
    }

    .activity-item {
        display: flex;
        gap: 15px;
        border-bottom: 1px dashed #e2e8f0;
        padding-bottom: 15px;
        margin-bottom: 15px;
    }

    .activity-item:last-child {
        border-bottom: none;
        padding-bottom: 0;
        margin-bottom: 0;
    }

    .activity-dot {
        width: 34px;
        height: 34px;
        border-radius: 10px;
        background: #eff6ff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        color: #3b82f6;
        flex-shrink: 0;
    }

    .filter-btn {
        background: transparent;
        border: none;
        padding: 8px 18px;
        border-radius: 50px;
        font-size: 13px;
        color: #64748b;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
    }

    .filter-btn.active {
        background: #002B6B;
        color: white;
        box-shadow: 0 2px 6px rgba(79, 70, 229, 0.3);
    }
</style>
@endpush

@section('content')
<div style="display: flex; flex-wrap: wrap; gap: 15px; margin-bottom: 25px; align-items: center;">
    <div style="height: 44px; display: flex; background: #fff; border: 1px solid #cbd5e1; border-radius: 50px; padding: 4px; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
        <a href="{{ route('dashboard', ['filter'=>'month']) }}" class="filter-btn {{ $filter==='month'?'active':'' }}">Tháng này</a>
        <a href="{{ route('dashboard', ['filter'=>'quarter']) }}" class="filter-btn {{ $filter==='quarter'?'active':'' }}">Quý này</a>
        <a href="{{ route('dashboard', ['filter'=>'year']) }}" class="filter-btn {{ $filter==='year'?'active':'' }}">Năm nay</a>
    </div>
    
    
    <form method="GET" action="{{ route('dashboard') }}" style="height: 44px; display: flex; align-items: center; gap: 8px; background: #fff; padding: 4px 5px 4px 18px; border-radius: 50px; border: 1px solid #cbd5e1; box-sizing: border-box; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
        <input type="hidden" name="filter" value="custom">
        <input type="date" name="date_start" value="{{ $dateStart }}" style="border:none; outline:none; color:#475569; font-size:13.5px; background:transparent; cursor: pointer;">
        <span style="color:#94a3b8; font-weight: bold;">-</span>
        <input type="date" name="date_end" value="{{ $dateEnd }}" style="border:none; outline:none; color:#475569; font-size:13.5px; background:transparent; cursor: pointer;">
        <button type="submit" style="height: 34px; background: #002B6B; color: white; border: none; border-radius: 50px; padding: 0 16px; font-size: 13px; font-weight: 700; cursor: pointer; transition: 0.2s; box-shadow: 0 2px 6px rgba(0,112,210,0.25);">Áp dụng</button>
    </form>
    <a href="{{ route('dashboard', ['filter'=>$filter]) }}" style="background: #fff; border-radius: 6px; padding: 10px 16px; color: #002B6B; border: 1px solid #002B6B; text-decoration: none; font-weight: 600; font-size: 14px; margin-left: auto; display: inline-flex; align-items: center; gap: 8px;"><i class="fas fa-sync-alt"></i> Làm mới</a>
</div>

{{-- KPI CARDS --}}
<div class="dash-grid">
    <div class="dash-card card-blue">
        <div class="dash-icon-wrap bg-blue"><i class="fas fa-users"></i></div>
        <div class="dash-info"><h5>Khách Hàng</h5><h2>{{ number_format($tongKH) }}</h2></div>
    </div>
    <div class="dash-card card-purple">
        <div class="dash-icon-wrap bg-purple"><i class="fas fa-shopping-cart"></i></div>
        <div class="dash-info"><h5>Đơn Hàng</h5><h2>{{ number_format($tongDon) }}</h2></div>
    </div>
    <div class="dash-card card-orange">
        <div class="dash-icon-wrap bg-orange"><i class="fas fa-box-open"></i></div>
        <div class="dash-info"><h5>Đã Bán</h5><h2>{{ number_format($tongSP) }}</h2></div>
    </div>
    <div class="dash-card card-green">
        <div class="dash-icon-wrap bg-green"><i class="fas fa-layer-group"></i></div>
        <div class="dash-info"><h5>Tồn / Tổng</h5><h2>{{ number_format($tongTon) }}</h2></div>
    </div>
    <div class="dash-card card-red">
        <div class="dash-icon-wrap bg-red"><i class="fas fa-coins"></i></div>
        <div class="dash-info"><h5>Doanh Thu</h5><h2 style="font-size:18px">{{ number_format($doanhThu) }} <small>đ</small></h2></div>
    </div>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 20px;">
    <div class="chart-box">
        <div class="chart-title"><i class="fas fa-chart-line" style="color:#3b82f6"></i> Doanh Thu 12 Tháng</div>
        <div class="chart-canvas"><canvas id="revenueChart"></canvas></div>
    </div>
    <div class="chart-box">
        <div class="chart-title"><i class="fas fa-chart-bar" style="color:#8b5cf6"></i> Số Đơn 6 Tháng</div>
        <div class="chart-canvas"><canvas id="ordersChart"></canvas></div>
    </div>
</div>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px;">
    <div class="chart-box">
        <div class="chart-title"><i class="fas fa-chart-pie" style="color:#10b981"></i> Trạng Thái Đơn Hàng</div>
        <div style="height:300px; display: flex; justify-content: center;"><div style="position:relative; width:60%;"><canvas id="statusChart"></canvas></div></div>
    </div>
    <div class="chart-box">
        <div class="chart-title"><i class="fas fa-trophy" style="color:#f59e0b"></i> Top 5 Sản Phẩm</div>
        <div class="chart-canvas"><canvas id="topProductsChart"></canvas></div>
    </div>
</div>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px;">
    <div class="chart-box">
        <div class="chart-title"><i class="fas fa-pallet" style="color:#8b5cf6"></i> Thống Kê Tồn Kho (Top 10)</div>
        <div class="chart-canvas"><canvas id="inventoryChart"></canvas></div>
    </div>
    <div class="chart-box" style="border-color:#fecaca; background:#fffcfc;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:14px; border-bottom:1px dashed #fca5a5; padding-bottom:12px;">
            <div class="chart-title" style="color:#ef4444; margin:0"><i class="fas fa-exclamation-triangle"></i> Sắp Đến Hạn TT</div>
            <span style="background:#fee2e2; color:#ef4444; font-size:11px; padding:3px 10px; border-radius:6px; font-weight:800">≤ 3 Ngày</span>
        </div>
        <div style="overflow-y:auto; max-height:250px;">
            @forelse($debtWarnings as $w)
                <div class="warning-item" style="border-left-color: {{ $w['days_left'] <= 0 ? '#ef4444' : '#f59e0b' }}">
                    <div style="flex: 1;">
                        <div style="font-weight: 700; font-size: 13px; color: #0f172a;">{{ $w['kh_name'] }}</div>
                        <div style="font-size: 11.5px; color: #64748b; margin-top: 2px;">{{ $w['dn_code'] }} · Hạn: <b>{{ $w['deadline_str'] }}</b></div>
                    </div>
                    <div style="background: {{ $w['days_left'] <= 0 ? '#fef2f2' : '#fff7ed' }}; color: {{ $w['days_left'] <= 0 ? '#ef4444' : '#f59e0b' }}; padding: 3px 8px; border-radius: 6px; font-size: 11px; font-weight: 800;">
                        {{ $w['days_left'] <= 0 ? 'Quá hạn' : 'Còn '.$w['days_left'].' ngày' }}
                    </div>
                </div>
            @empty
                <div style="text-align:center; color:#94a3b8; padding:30px 10px;">Không có nợ sắp hạn.</div>
            @endforelse
        </div>
    </div>
</div>

<div class="chart-box">
    <div class="chart-title"><i class="fas fa-history" style="color:#10b981"></i> Hoạt Động Gần Đây</div>
    <div style="display:flex; flex-direction:column; gap:15px;">
        @forelse($activities as $log)
            <div class="activity-item">
                <div class="activity-dot"><i class="fas fa-bolt"></i></div>
                <div style="flex: 1;">
                    <div style="font-size:13.5px; font-weight:600; color:#0f172a;">{{ $log->action }}</div>
                    <div style="font-size:12px; color:#94a3b8; margin-top: 2px;">{{ $log->user?->display_name ?? 'Hệ thống' }} · {{ $log->created_at->diffForHumans() }}</div>
                </div>
            </div>
        @empty
            <div style="text-align:center; color:#94a3b8; padding:20px;">Chưa có hoạt động.</div>
        @endforelse
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
window.addEventListener('load', function() {
    const CHART_OPTS = { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom', labels: { usePointStyle: true, boxWidth: 8 } } } };

    // Revenue
    const revenueEl = document.getElementById('revenueChart');
    if (revenueEl) new Chart(revenueEl, {
        type: 'line',
        data: {
            labels: @json($revenueData['labels']),
            datasets: [{ label: 'Doanh Thu', data: @json($revenueData['data']), borderColor: '#3b82f6', backgroundColor: 'rgba(59,130,246,.1)', fill: true, tension: .4 }]
        },
        options: { ...CHART_OPTS, scales: { y: { beginAtZero: true } } }
    });

    // Orders
    const ordersEl = document.getElementById('ordersChart');
    if (ordersEl) new Chart(ordersEl, {
        type: 'bar',
        data: {
            labels: @json($ordersChart['labels']),
            datasets: [{ label: 'Số Đơn', data: @json($ordersChart['data']), backgroundColor: '#8b5cf6', borderRadius: 6 }]
        },
        options: { ...CHART_OPTS, scales: { y: { beginAtZero: true } } }
    });

    // Status
    const statusEl = document.getElementById('statusChart');
    if (statusEl) new Chart(statusEl, {
        type: 'doughnut',
        data: {
            labels: @json($statusChart['labels']),
            datasets: [{ data: @json($statusChart['data']), backgroundColor: ['#f59e0b','#3b82f6','#8b5cf6','#10b981','#ef4444'], borderWidth: 0 }]
        },
        options: { ...CHART_OPTS, cutout: '65%' }
    });

    // Top Products
    const topEl = document.getElementById('topProductsChart');
    if (topEl) new Chart(topEl, {
        type: 'bar',
        data: {
            labels: @json(collect($topProducts)->pluck('ten_hang')->map(fn($n) => mb_substr($n,0,15).'...')->toArray()),
            datasets: [{ label: 'Số lượng', data: @json(collect($topProducts)->pluck('total_qty')->toArray()), backgroundColor: '#10b981', borderRadius: 6 }]
        },
        options: { ...CHART_OPTS, indexAxis: 'y', plugins: { legend: { display: false } } }
    });

    // Inventory
    const invEl = document.getElementById('inventoryChart');
    if (invEl) new Chart(invEl, {
        type: 'bar',
        data: {
            labels: @json(collect($inventoryTop10)->pluck('ma_hang')->toArray()),
            datasets: [
                { label: 'Tồn Kho', data: @json(collect($inventoryTop10)->pluck('con_lai')->toArray()), backgroundColor: '#002B6B', borderRadius: 4 }
            ]
        },
        options: { ...CHART_OPTS, scales: { y: { beginAtZero: true } } }
    });
});
</script>
@endpush
