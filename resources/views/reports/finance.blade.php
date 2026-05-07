@extends('layouts.app')

@section('title', 'Báo Cáo Tài Chính')
@section('page-title', 'Báo Cáo Tài Chính')
@section('page-subtitle', 'Tổng hợp số liệu doanh thu, thu tiền và công nợ toàn hệ thống.')

@push('styles')
<style>
    .dash-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 20px;
        margin-bottom: 25px;
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
        background: #0070D2;
        color: white;
        box-shadow: 0 2px 6px rgba(0,112,210,0.3);
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
    }
    .dash-icon-wrap {
        width: 56px;
        height: 56px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        flex-shrink: 0;
    }
    .bg-blue { background: #eff6ff; color: #3b82f6; }
    .bg-green { background: #f0fdf4; color: #10b981; }
    .bg-orange { background: #fff7ed; color: #f59e0b; }
    .bg-red { background: #fef2f2; color: #ef4444; }

    .dash-info h5 { margin: 0; font-size: 12px; color: #64748b; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; }
    .dash-info h2 { margin: 6px 0 0 0; font-size: 22px; color: #0f172a; font-weight: 800; }
    .dash-info small { font-size: 14px; font-weight: 600; color: #94a3b8; margin-left: 4px; }

    .chart-container {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
        margin-bottom: 20px;
    }
    .chart-box {
        background: #fff;
        border-radius: 16px;
        padding: 24px;
        border: 1px solid #f1f5f9;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
    }
    .chart-title {
        font-size: 16px;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 25px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .chart-canvas { position: relative; height: 320px; width: 100%; }

    @media (max-width: 1024px) {
        .chart-container { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('content')
<div style="display: flex; flex-wrap: wrap; gap: 15px; margin-bottom: 25px; align-items: center;">
    <div style="height: 44px; display: flex; background: #fff; border: 1px solid #cbd5e1; border-radius: 50px; padding: 4px; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
        <a href="{{ route('reports.finance', ['filter'=>'all']) }}" class="filter-btn {{ $filter==='all'?'active':'' }}">Tất cả</a>
        <a href="{{ route('reports.finance', ['filter'=>'month']) }}" class="filter-btn {{ $filter==='month'?'active':'' }}">Tháng này</a>
        <a href="{{ route('reports.finance', ['filter'=>'last_month']) }}" class="filter-btn {{ $filter==='last_month'?'active':'' }}">Tháng trước</a>
        <a href="{{ route('reports.finance', ['filter'=>'quarter']) }}" class="filter-btn {{ $filter==='quarter'?'active':'' }}">Quý này</a>
        <a href="{{ route('reports.finance', ['filter'=>'year']) }}" class="filter-btn {{ $filter==='year'?'active':'' }}">Năm nay</a>
    </div>
    
    <form method="GET" action="{{ route('reports.finance') }}" style="height: 44px; display: flex; align-items: center; gap: 8px; background: #fff; padding: 4px 5px 4px 18px; border-radius: 50px; border: 1px solid #cbd5e1; box-sizing: border-box; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
        <input type="hidden" name="filter" value="custom">
        <input type="date" name="date_start" value="{{ $dateStart }}" style="border:none; outline:none; color:#475569; font-size:13.5px; background:transparent; cursor: pointer;">
        <span style="color:#94a3b8; font-weight: bold;">-</span>
        <input type="date" name="date_end" value="{{ $dateEnd }}" style="border:none; outline:none; color:#475569; font-size:13.5px; background:transparent; cursor: pointer;">
        <button type="submit" style="height: 34px; background: #0070D2; color: white; border: none; border-radius: 50px; padding: 0 16px; font-size: 13px; font-weight: 700; cursor: pointer; transition: 0.2s; box-shadow: 0 2px 6px rgba(0,112,210,0.25);">Áp dụng</button>
    </form>
    <a href="{{ route('reports.finance') }}" style="background: #fff; border-radius: 6px; padding: 10px 16px; color: #0070D2; border: 1px solid #0070D2; text-decoration: none; font-weight: 600; font-size: 14px; margin-left: auto; display: inline-flex; align-items: center; gap: 8px;"><i class="fas fa-sync-alt"></i> Làm mới</a>
</div>

<div class="dash-grid">
    <div class="dash-card">
        <div class="dash-icon-wrap bg-blue"><i class="fas fa-file-invoice-dollar"></i></div>
        <div class="dash-info">
            <h5>Tổng Doanh Thu</h5>
            <h2>{{ number_format($tongDoanhThu) }}<small>đ</small></h2>
        </div>
    </div>
    <div class="dash-card">
        <div class="dash-icon-wrap bg-green"><i class="fas fa-hand-holding-usd"></i></div>
        <div class="dash-info">
            <h5>Đã Thu Tiền</h5>
            <h2>{{ number_format($tongDaThu) }}<small>đ</small></h2>
        </div>
    </div>
    <div class="dash-card">
        <div class="dash-icon-wrap bg-red"><i class="fas fa-exclamation-circle"></i></div>
        <div class="dash-info">
            <h5>Công Nợ Còn Lại</h5>
            <h2>{{ number_format($tongCongNo) }}<small>đ</small></h2>
        </div>
    </div>
    <div class="dash-card">
        <div class="dash-icon-wrap bg-orange"><i class="fas fa-chart-pie"></i></div>
        <div class="dash-info">
            <h5>Tỷ Lệ Thu Hồi</h5>
            <h2>{{ number_format($tyLeThuHoi, 1) }}<small>%</small></h2>
        </div>
    </div>
</div>

<div class="chart-container">
    <div class="chart-box" style="grid-column: span 2;">
        <div class="chart-title"><i class="fas fa-chart-line" style="color: #3b82f6;"></i> So Sánh Doanh Thu & Thu Tiền (12 Tháng)</div>
        <div class="chart-canvas"><canvas id="monthlyChart"></canvas></div>
    </div>

    <div class="chart-box">
        <div class="chart-title"><i class="fas fa-map-marked-alt" style="color: #10b981;"></i> Doanh Thu Theo Khu Vực</div>
        <div class="chart-canvas" style="height: 350px;"><canvas id="regionChart"></canvas></div>
    </div>

    <div class="chart-box">
        <div class="chart-title"><i class="fas fa-users" style="color: #f59e0b;"></i> Top 10 Khách Hàng Doanh Thu Cao Nhất</div>
        <div class="chart-canvas" style="height: 350px;"><canvas id="customerChart"></canvas></div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const commonOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { position: 'bottom', labels: { usePointStyle: true, font: { family: "'Inter', sans-serif", size: 12 } } }
        }
    };

    // 1. Monthly Revenue vs Payment
    new Chart(document.getElementById('monthlyChart'), {
        type: 'bar',
        data: {
            labels: @json($chartMonthly['labels']),
            datasets: [
                {
                    label: 'Doanh thu (đã VAT)',
                    data: @json($chartMonthly['revenue']),
                    backgroundColor: 'rgba(59, 130, 246, 0.7)',
                    borderRadius: 4
                },
                {
                    label: 'Thực thu',
                    data: @json($chartMonthly['payment']),
                    backgroundColor: 'rgba(16, 185, 129, 0.7)',
                    borderRadius: 4
                }
            ]
        },
        options: {
            ...commonOptions,
            scales: {
                y: { beginAtZero: true, ticks: { callback: value => value.toLocaleString('vi-VN') + ' đ' } }
            }
        }
    });

    // 2. Region Chart
    new Chart(document.getElementById('regionChart'), {
        type: 'doughnut',
        data: {
            labels: @json($chartRegion['labels']),
            datasets: [{
                data: @json($chartRegion['data']),
                backgroundColor: ['#3b82f6', '#10b981', '#f59e0b', '#8b5cf6', '#ef4444'],
                borderWidth: 0
            }]
        },
        options: { ...commonOptions, cutout: '65%' }
    });

    // 3. Top Customers
    new Chart(document.getElementById('customerChart'), {
        type: 'bar',
        data: {
            labels: @json($topCustomers['labels']),
            datasets: [{
                label: 'Doanh thu',
                data: @json($topCustomers['data']),
                backgroundColor: '#f59e0b',
                borderRadius: 4
            }]
        },
        options: {
            ...commonOptions,
            indexAxis: 'y',
            plugins: { legend: { display: false } },
            scales: { x: { ticks: { callback: value => (value/1000000).toFixed(0) + 'M' } } }
        }
    });
});
</script>
@endpush
