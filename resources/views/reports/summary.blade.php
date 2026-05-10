@extends('layouts.app')
@section('title', 'Báo cáo tổng hợp')

@section('content')
<div class="card" style="padding: 24px;">

    {{-- PAGE HEADER --}}
    <div class="page-header-row" style="display:flex; justify-content:space-between; align-items:center; padding-bottom:20px; border-bottom:2.5px solid #cbd5e1; margin-bottom:20px;">
        <div style="display:flex; align-items:center; gap:16px;">
            <div style="width:56px; height:56px; background:#eff6ff; border-radius:16px; display:flex; align-items:center; justify-content:center; box-shadow:0 4px 10px rgba(59,130,246,.15); flex-shrink:0;">
                <i class="fas fa-chart-bar" style="font-size:24px; color:#3b82f6;"></i>
            </div>
            <div>
                <h2 style="font-size:22px; font-weight:800; color:#0f172a; margin:0 0 4px; letter-spacing:-.5px;">Báo cáo tổng hợp</h2>
                <p style="margin:0; color:#64748b; font-size:13.5px;">Thống kê doanh thu, lợi nhuận và hiệu quả kinh doanh theo đơn hàng</p>
            </div>
        </div>
    </div>

    <style>
        /* ── FILTER ── */
        .ord-filter-card { padding: 14px 0 10px; border-bottom: 1.5px solid #f1f5f9; margin-bottom: 16px; }
        .ord-filter-grid { display: flex; gap: 12px; align-items: flex-end; flex-wrap: wrap; }
        .ord-filter-item { display: flex; flex-direction: column; gap: 5px; }
        .ord-filter-item label { font-size: 13px; font-weight: 700; color: #1e293b; }
        .ord-filter-input { height: 36px; border: 1px solid #cbd5e1; border-radius: 6px; padding: 0 12px; font-size: 13px; outline: none; box-sizing: border-box; color: #1e293b; background: #fff; }
        .ord-filter-input:focus { border-color: #0070D2; box-shadow: 0 0 0 3px rgba(0,112,210,.1); }
        .ord-search-wrapper { position: relative; }
        .ord-search-wrapper i { position: absolute; right: 12px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 14px; pointer-events: none; }
        .ord-search-wrapper .ord-filter-input { padding-right: 36px; }
        .ord-btn-search { height: 36px; padding: 0 16px; background: #0070D2; color: #fff; border: none; border-radius: 6px; font-weight: 600; font-size: 13px; cursor: pointer; display: flex; align-items: center; gap: 6px; white-space: nowrap; }
        .ord-btn-search:hover { background: #005bb5; }
        .ord-btn-clear { height: 36px; padding: 0 16px; background: #fff; color: #ef4444; border: 1px solid #e2e8f0; border-radius: 6px; font-weight: 600; font-size: 13px; cursor: pointer; display: flex; align-items: center; gap: 6px; text-decoration: none; white-space: nowrap; }
        .ord-btn-clear:hover { background: #fef2f2; border-color: #ef4444; }

        /* ── TABLE WRAPPER ── */
        .sr-table-outer {
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            overflow: hidden;
        }
        /* Force internal scroll — sticky thead works only when THIS div is the scroll container */
        .sr-table-scroll {
            height: calc(100vh - 370px);
            min-height: 300px;
            overflow-y: auto;
            overflow-x: auto;
        }

        /* ── TABLE ── */
        .sr-table {
            width: 100%; border-collapse: collapse; font-size: 13px; min-width: 1400px;
        }

        /* HEADER row - sticky top */
        .sr-table thead th {
            background: #EFF6FF;
            padding: 9px 7px;
            font-size: 13px;
            font-weight: 700;
            color: #000;
            text-align: center;
            border: 1px solid #e2e8f0;
            white-space: pre-line;
            vertical-align: middle;
            line-height: 1.35;
            position: sticky;
            top: 0;
            z-index: 4;
            text-transform: uppercase;
        }

        /* SUM row - sticky just below header */
        .sr-table thead .sum-row td {
            background: #fef9c3;
            color: #000;
            font-weight: 700;
            font-size: 13px;
            padding: 9px 7px;
            border: 1px solid #fde68a;
            position: sticky;
            /* top is set dynamically by JS based on actual th row height */
            z-index: 3;
        }

        /* BODY rows */
        .sr-table tbody td { padding: 7px 7px; font-size: 13px; color: #1e293b; border: 1px solid #e2e8f0; vertical-align: middle; }
        .sr-table tbody tr:hover { background: #f0f7ff; }
        .sr-table tbody tr:nth-child(even) { background: #f8fafc; }
        .sr-table tbody tr:nth-child(even):hover { background: #e8f2ff; }

        .t-ctr { text-align: center; }
        .t-right { text-align: right; }
        .t-left { text-align: left; }
        .gp-pos { color: #16a34a; font-weight: 700; }
        .gp-neg { color: #dc2626; font-weight: 700; }

        /* ── PAGINATION ── */
        .pager { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 8px; margin-top: 16px; }
        .pager-info { font-size: 13px; color: #64748b; }
        .pager-links a, .pager-links span { display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px; border-radius: 6px; border: 1px solid #e2e8f0; font-size: 13px; font-weight: 600; color: #475569; text-decoration: none; margin: 0 2px; transition: all .15s; }
        .pager-links a:hover { background: #0070D2; color: #fff; border-color: #0070D2; }
        .pager-links span.cur { background: #0070D2; color: #fff; border-color: #0070D2; }
        .pager-links span.dis { color: #cbd5e1; }
    </style>

    {{-- FILTER --}}
    <div class="ord-filter-card">
        <form method="GET" action="{{ route('reports.summary') }}" id="sr-form">
            <div class="ord-filter-grid">
                <div class="ord-filter-item">
                    <label>Từ ngày</label>
                    <input type="date" name="date_start" class="ord-filter-input" value="{{ $dateStart }}" style="width:150px;">
                </div>
                <div class="ord-filter-item">
                    <label>Đến ngày</label>
                    <input type="date" name="date_end" class="ord-filter-input" value="{{ $dateEnd }}" style="width:150px;">
                </div>
                <div class="ord-filter-item" style="flex:2; min-width:200px;">
                    <label>Khách hàng</label>
                    <select name="customer" id="filter-customer" class="ord-filter-input" style="width:100%;">
                        <option value="">Tất cả khách hàng</option>
                        @foreach($customers as $c)
                            <option value="{{ $c->id }}" {{ ($customerId ?? '') == $c->id ? 'selected' : '' }}>
                                [{{ $c->ma_kh }}] {{ $c->ten_cty }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="ord-filter-item" style="flex:2; min-width:200px;">
                    <label>Tìm kiếm</label>
                    <div class="ord-search-wrapper">
                        <input type="text" name="search" class="ord-filter-input" style="width:100%;" placeholder="Mã đơn, sản phẩm..." value="{{ $search }}">
                        <i class="fas fa-search"></i>
                    </div>
                </div>
                <div class="ord-filter-item" style="flex:none;">
                    <label>&nbsp;</label>
                    <div style="display:flex; gap:8px;">
                        <button type="submit" class="ord-btn-search"><i class="fas fa-search"></i> Tìm kiếm</button>
                        <a href="{{ route('reports.summary') }}" class="ord-btn-clear"><i class="fas fa-times"></i> Xóa lọc</a>
                    </div>
                </div>
            </div>
        </form>
    </div>

    {{-- TABLE --}}
    <div class="sr-table-outer">
        <div class="sr-table-scroll">
            <table class="sr-table">
                <thead>
                    {{-- Row 1: Column headers (sticky top=0) --}}
                    <tr>
                        <th style="width:3%">No.</th>
                        <th style="width:5%">Date</th>
                        <th style="width:7%; white-space:nowrap;">P.O Number</th>
                        <th style="width:6%; white-space:nowrap;">Invoice No.</th>
                        <th style="width:20%;">Customer Name</th>
                        <th style="width:5%; white-space:nowrap;">Product Code</th>
                        <th style="width:9%; white-space:nowrap;">Products</th>
                        <th style="width:4%">Quantity&#10;(KG)</th>
                        <th style="width:5%">Purchase Price&#10;(VND/KG)</th>
                        <th style="width:5%">Selling Price&#10;(VND/KG)</th>
                        <th style="width:4%">OPEX</th>
                        <th style="width:4%">Tax&#10;(%)</th>
                        <th style="width:5%">COGS</th>
                        <th style="width:6%">TO&#10;(VND)</th>
                        <th style="width:5%">GP&#10;(GP)</th>
                        <th style="width:4%">Gross Margin&#10;(%)</th>
                        <th style="width:5%">EBIT</th>
                        <th style="width:5%">Tax Expense</th>
                        <th style="width:5%">NPAT</th>
                    </tr>

                    {{-- Row 2: TỔNG CỘNG - sticky just below header --}}
                    <tr class="sum-row">
                        <td colspan="13" class="t-right" style="letter-spacing:.3px; color:black">TỔNG CỘNG</td>
                        <td class="t-right" style="color:red;">{{ number_format($sumTotals['sumDoanhThu'], 0, ',', '.') }}</td>
                        <td class="t-right" style="color:red;">{{ number_format($sumTotals['sumGP'], 0, ',', '.') }}</td>
                        <td class="t-ctr" style="color:red;">{{ number_format($sumTotals['sumGM'], 2, ',', '.') }}%</td>
                        <td class="t-right" style="color:red;">{{ number_format($sumTotals['sumEBIT'], 0, ',', '.') }}</td>
                        <td class="t-right" style="color:red;">{{ number_format($sumTotals['sumTax'], 0, ',', '.') }}</td>
                        <td class="t-right" style="color:red;">{{ number_format($sumTotals['sumNPAT'], 0, ',', '.') }}</td>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rows as $i => $row)
                    @php
                        $no      = ($rows->currentPage() - 1) * $rows->perPage() + $i + 1;
                        $gpCls   = $row->gross_profit >= 0 ? 'gp-pos' : 'gp-neg';
                        $npatCls = $row->npat >= 0 ? 'gp-pos' : 'gp-neg';
                    @endphp
                    <tr>
                        <td class="t-ctr">{{ $no }}</td>
                        <td class="t-ctr">{{ \Carbon\Carbon::parse($row->date)->format('d/m/Y') }}</td>
                        <td class="t-ctr" style="font-weight:700; color:#1d4ed8; white-space:nowrap;">{{ $row->dn_code ?? '—' }}</td>
                        <td class="t-ctr">{{ $row->invoice_no ?? '—' }}</td>
                        <td class="t-left" style="text-transform:uppercase; white-space:nowrap;">{{ $row->customer_name }}</td>
                        <td class="t-ctr">{{ $row->ma_hang }}</td>
                        <td class="t-left" style="white-space:nowrap;">{{ $row->ten_hang }}</td>
                        <td class="t-right">{{ number_format($row->so_luong, 2, ',', '.') }}</td>
                        <td class="t-right">{{ number_format($row->gia_nhap ?? 0, 0, ',', '.') }}</td>
                        <td class="t-right">{{ number_format($row->gia_ban ?? 0, 0, ',', '.') }}</td>
                        <td class="t-right">{{ number_format($row->opex_val, 0, ',', '.') }}</td>
                        <td class="t-ctr">{{ $row->tax_rate_val }}%</td>
                        <td class="t-right">{{ number_format($row->cogs, 0, ',', '.') }}</td>
                        <td class="t-right" style="font-weight:600;">{{ number_format($row->doanh_thu, 0, ',', '.') }}</td>
                        <td class="t-right {{ $gpCls }}">{{ number_format($row->gross_profit, 0, ',', '.') }}</td>
                        <td class="t-ctr {{ $gpCls }}">{{ number_format($row->gross_margin, 2, ',', '.') }}%</td>
                        <td class="t-right">{{ number_format($row->ebit, 0, ',', '.') }}</td>
                        <td class="t-right">{{ number_format($row->tax_expense, 0, ',', '.') }}</td>
                        <td class="t-right {{ $npatCls }}">{{ number_format($row->npat, 0, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="19" style="text-align:center; padding:50px; color:#94a3b8;">
                            <i class="fas fa-inbox" style="font-size:28px; display:block; margin-bottom:10px;"></i>
                            Không có dữ liệu phù hợp với điều kiện lọc
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- PAGINATION --}}
    @if($rows->hasPages())
    <div class="pager">
        <div class="pager-info">
            Hiển thị {{ $rows->firstItem() }}–{{ $rows->lastItem() }} trong {{ number_format($rows->total()) }} kết quả
        </div>
        <div class="pager-links">
            @if($rows->onFirstPage())
                <span class="dis"><i class="fas fa-chevron-left"></i></span>
            @else
                <a href="{{ $rows->previousPageUrl() }}"><i class="fas fa-chevron-left"></i></a>
            @endif
            @foreach($rows->getUrlRange(max(1,$rows->currentPage()-2), min($rows->lastPage(),$rows->currentPage()+2)) as $page => $url)
                @if($page == $rows->currentPage())
                    <span class="cur">{{ $page }}</span>
                @else
                    <a href="{{ $url }}">{{ $page }}</a>
                @endif
            @endforeach
            @if($rows->hasMorePages())
                <a href="{{ $rows->nextPageUrl() }}"><i class="fas fa-chevron-right"></i></a>
            @else
                <span class="dis"><i class="fas fa-chevron-right"></i></span>
            @endif
        </div>
    </div>
    @endif

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Đo chiều cao thực của row header (tr:first-child trong thead)
    function fixSumRowTop() {
        const headerRow = document.querySelector('.sr-table thead tr:first-child');
        const sumRowTds = document.querySelectorAll('.sr-table thead .sum-row td');
        if (!headerRow || !sumRowTds.length) return;
        const thHeight = headerRow.getBoundingClientRect().height;
        sumRowTds.forEach(td => {
            td.style.top = thHeight + 'px';
        });
    }

    fixSumRowTop();
    // Gọi lại sau một tick để đảm bảo fonts đã render đầy đủ
    setTimeout(fixSumRowTop, 100);
});
</script>
@endpush
