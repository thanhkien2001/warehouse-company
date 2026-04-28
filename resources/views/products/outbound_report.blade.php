@extends('layouts.app')

@section('title', 'Báo cáo xuất kho')

@push('styles')
<style>
    /* ===== LAYOUT ===== */
    .obr-container {
        display: flex;
        flex-direction: column;
        gap: 10px;
        max-width: 1620px;
        margin: 0 auto;
        height: calc(100vh - 100px);
        min-width: 1200px;
    }

    /* ===== PAGE HEADER ===== */
    .obr-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .obr-header h2 {
        font-weight: 800;
        font-size: 18px;
        margin: 0;
    }

    .obr-header .breadcrumb {
        font-size: 11px;
        color: #64748b;
        margin: 2px 0 0;
    }

    .obr-header-actions {
        display: flex;
        gap: 8px;
        align-items: center;
    }

    /* ===== BLOCK ===== */
    .obr-block {
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.08);
        border: 1px solid #e2e8f0;
        padding: 14px 16px;
    }

    /* ===== FILTER ===== */
    .filter-unified-6 {
        display: grid;
        grid-template-columns: repeat(6, 1fr);
        gap: 8px 14px;
        align-items: end;
    }

    .filter-item {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .filter-item label {
        font-size: 11px;
        font-weight: 600;
        color: #475569;
    }

    .filter-input {
        padding: 6px 10px;
        border: 1px solid #cbd5e1;
        border-radius: 5px;
        font-size: 12px;
        color: #1e293b;
        outline: none;
        background: #fff;
        width: 100%;
        box-sizing: border-box;
        height: 32px;
    }

    .filter-input:focus { border-color: #0070D2; }

    .btn-search {
        background: #0070D2;
        color: #fff;
        border: none;
        padding: 0 14px;
        height: 32px;
        border-radius: 5px;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        white-space: nowrap;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
    }

    .btn-clear {
        background: #fff;
        color: #64748b;
        border: 1px solid #cbd5e1;
        padding: 0 12px;
        height: 32px;
        border-radius: 5px;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        white-space: nowrap;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
    }

    .btn-clear:hover { background: #E74C3C; }

    /* ===== STAT CARDS ===== */
    .stat-cards {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 10px;
    }

    .stat-card {
        background: #fff;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
        padding: 12px 16px;
        display: flex;
        align-items: center;
        gap: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.06);
    }

    .stat-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        flex-shrink: 0;
    }

    .stat-icon.blue   { background: #eff6ff; color: #3b82f6; }
    .stat-icon.green  { background: #f0fdf4; color: #22c55e; }
    .stat-icon.orange { background: #fff7ed; color: #f97316; }
    .stat-icon.purple { background: #faf5ff; color: #a855f7; }
    .stat-icon.teal   { background: #f0fdfa; color: #14b8a6; }

    .stat-label {
        font-size: 11px;
        color: #64748b;
        margin-bottom: 2px;
    }

    .stat-value {
        font-size: 20px;
        font-weight: 800;
        color: #1e293b;
        line-height: 1.1;
    }

    .stat-unit {
        font-size: 10px;
        color: #94a3b8;
        font-weight: 500;
        margin-top: 1px;
    }

    /* ===== TABLE BLOCK ===== */
    .table-block {
        flex: 1;
        display: flex;
        flex-direction: column;
        min-height: 0;
    }

    .table-block-inner {
        flex: 1;
        display: flex;
        flex-direction: column;
        min-height: 0;
    }

    .table-toolbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
    }

    .table-title {
        font-size: 13px;
        font-weight: 700;
        color: #0070D2;
        text-transform: uppercase;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .table-toolbar-right {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 12px;
        color: #475569;
    }

    .per-page-select {
        padding: 3px 8px;
        border: 1px solid #cbd5e1;
        border-radius: 4px;
        font-size: 12px;
        outline: none;
        height: 28px;
    }

    .table-scroll {
        flex: 1;
        overflow-y: auto;
        overflow-x: scroll;
        border: 1px solid #e2e8f0;
        border-radius: 5px;
    }
    .table-scroll::-webkit-scrollbar { height: 8px; width: 6px; }
    .table-scroll::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 4px; }
    .table-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
    .table-scroll::-webkit-scrollbar-thumb:hover { background: #94a3b8; }


    /* ===== DATA TABLE ===== */
    .data-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 1800px;
    }

    .data-table th {
        background: #EFF6FF;
        padding: 9px 6px;
        font-size: 11px;
        font-weight: 700;
        color: #05080c;
        border: 1px solid #e2e8f0;
        text-align: center;
        white-space: nowrap;
        position: sticky;
        top: 0;
        z-index: 10;
    }

    .data-table td {
        padding: 7px 7px;
        font-size: 12px;
        border: 1px solid #e2e8f0;
        color: #1e293b;
        white-space: nowrap;
    }

    .data-table tbody tr:hover { background: #f8fafc; }
    .data-table tbody tr:nth-child(even) { background: #fafbfc; }
    .data-table tbody tr:nth-child(even):hover { background: #f1f5f9; }

    .td-center { text-align: center; }
    .td-right  { text-align: right; }

    .badge-type {
        display: inline-flex;
        align-items: center;
        padding: 2px 8px;
        border-radius: 20px;
        font-size: 10px;
        font-weight: 700;
    }

    .badge-banhang { background: #eff6ff; color: #3b82f6; }
    .badge-noibo   { background: #fef3c7; color: #d97706; }

    .link-phieu {
        color: #0070D2;
        font-weight: 600;
        text-decoration: none;
        font-size: 11px;
    }

    .link-phieu:hover { text-decoration: underline; }

    .action-btn {
        background: none;
        border: none;
        cursor: pointer;
        padding: 3px 5px;
        border-radius: 4px;
        font-size: 14px;
        line-height: 1;
    }

    .action-btn.eye  { color: #0070D2; }
    .action-btn.down { color: #10b981; }
    .action-btn:hover { opacity: 0.7; }

    /* ===== PAGINATION ===== */
    .pagination-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 10px;
        font-size: 12px;
        color: #64748b;
        flex-shrink: 0;
    }

    .pagination-info { font-size: 12px; color: #64748b; }

    .pagination-controls {
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .page-btn {
        width: 28px;
        height: 28px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px solid #e2e8f0;
        border-radius: 5px;
        background: #fff;
        font-size: 12px;
        cursor: pointer;
        color: #475569;
        font-weight: 600;
    }

    .page-btn.active {
        background: #0070D2;
        color: #fff;
        border-color: #0070D2;
    }

    .page-btn:hover:not(.active):not(:disabled) { background: #f1f5f9; }
    .page-btn:disabled { opacity: 0.4; cursor: default; }

    .pagination-goto {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 12px;
        color: #64748b;
    }

    .goto-input {
        width: 42px;
        height: 28px;
        text-align: center;
        border: 1px solid #cbd5e1;
        border-radius: 5px;
        font-size: 12px;
        outline: none;
    }

    /* ===== HEADER BUTTONS ===== */
    .btn-outline-hdr {
        background: #fff;
        border: 1px solid #cbd5e1;
        padding: 6px 13px;
        border-radius: 5px;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 5px;
        color: #475569;
    }

    .btn-outline-hdr.excel { color: #16a34a; border-color: #bbf7d0; background: #f0fdf4; }
    .btn-outline-hdr.pdf   { color: #dc2626; border-color: #fecaca; background: #fef2f2; }
    .btn-outline-hdr.filter { background: #0070D2; color: #fff; border-color: #0070D2; }

    .btn-outline-hdr:hover { opacity: 0.85; }

    /* ===== NGUON PHIEU cell ===== */
    .nguon-cell {
        font-size: 11px;
        color: #64748b;
    }

    .nguon-code {
        color: #0070D2;
        font-weight: 600;
    }
</style>
@endpush

@section('content')
<div class="obr-container">

    {{-- PAGE HEADER --}}
    <div class="obr-header">
        <div>
            <h2>Báo cáo xuất kho</h2>
        </div>
        <div class="obr-header-actions">
            <button class="btn-outline-hdr excel"><i class="fas fa-file-excel"></i> Xuất Excel</button>
            <button class="btn-outline-hdr pdf"><i class="fas fa-file-pdf"></i> Xuất PDF</button>
        </div>
    </div>

    {{-- BLOCK 1: BỘ LỌC --}}
    <div class="obr-block">
        <div class="filter-unified-6">
            {{-- Row 1: 5 inputs + 1 ô trống --}}
            <div class="filter-item">
                <label>Từ ngày</label>
                <input type="date" class="filter-input" value="{{ date('Y-m-01') }}">
            </div>
            <div class="filter-item">
                <label>Đến ngày</label>
                <input type="date" class="filter-input" value="{{ date('Y-m-d') }}">
            </div>
            <div class="filter-item">
                <label>Loại xuất</label>
                <select class="filter-input">
                    <option value="">Tất cả</option>
                    <option value="banhang">Bán hàng</option>
                    <option value="noibo">Nội bộ</option>
                </select>
            </div>
            <div class="filter-item">
                <label>Mã hàng</label>
                <input type="text" class="filter-input" placeholder="Chọn mã hàng">
            </div>
            <div class="filter-item">
                <label>Khách hàng</label>
                <input type="text" class="filter-input" placeholder="Nhập tên khách hàng">
            </div>
            {{-- Cột 6 row 1: trống --}}
            <div></div>

            {{-- Row 2: 5 inputs + 2 nút --}}
            <div class="filter-item">
                <label>Kho xuất</label>
                <select class="filter-input">
                    <option value="">Tất cả</option>
                    <option value="nguyen_lieu">Kho Nguyên Liệu</option>
                    <option value="lab">Kho Lab</option>
                </select>
            </div>
            <div class="filter-item">
                <label>Số phiếu</label>
                <input type="text" class="filter-input" placeholder="Nhập số phiếu">
            </div>
            <div class="filter-item">
                <label>Số đơn hàng</label>
                <input type="text" class="filter-input" placeholder="Nhập số đơn hàng">
            </div>
            <div class="filter-item">
                <label>LOT</label>
                <input type="text" class="filter-input" placeholder="Nhập số LOT">
            </div>
            <div class="filter-item">
                <label>Nhóm hàng</label>
                <select class="filter-input">
                    <option value="">Tất cả</option>
                    <option>Nguyên liệu</option>
                    <option>Thành phẩm</option>
                    <option>Bao bì</option>
                </select>
            </div>
            {{-- Cột 6 row 2: 2 nút ngang hàng --}}
            <div style="display: flex; flex-direction: row; gap: 6px; align-items: flex-end;">
                <button class="btn-search" style="flex: 1;"><i class="fas fa-search"></i> Tìm kiếm</button>
                <button class="btn-clear" style="flex: 1; background-color: #E74C3C; color: white; border-color: #E74C3C;">Xóa lọc</button>
            </div>
        </div>
    </div>

    {{-- BLOCK 2: STAT CARDS --}}
    <div class="stat-cards">
        <div class="stat-card">
            <div class="stat-icon blue"><i class="fas fa-file-alt"></i></div>
            <div>
                <div class="stat-label">Tổng số phiếu</div>
                <div class="stat-value">28</div>
                <div class="stat-unit">phiếu</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon green"><i class="fas fa-boxes"></i></div>
            <div>
                <div class="stat-label">Tổng số lượng xuất</div>
                <div class="stat-value">1.245,50</div>
                <div class="stat-unit">Kg</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon orange"><i class="fas fa-coins"></i></div>
            <div>
                <div class="stat-label">Tổng giá trị xuất (VNĐ)</div>
                <div class="stat-value" style="font-size:16px;">1.254.850.000</div>
                <div class="stat-unit">VNĐ</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon purple"><i class="fas fa-building"></i></div>
            <div>
                <div class="stat-label">Số khách hàng</div>
                <div class="stat-value">12</div>
                <div class="stat-unit">khách hàng</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon teal"><i class="fas fa-warehouse"></i></div>
            <div>
                <div class="stat-label">Số kho xuất</div>
                <div class="stat-value">3</div>
                <div class="stat-unit">kho</div>
            </div>
        </div>
    </div>

    {{-- BLOCK 3: TABLE --}}
    <div class="obr-block table-block">
        <div class="table-block-inner">
            <div class="table-toolbar">
                <div class="table-title"><i class="fas fa-table"></i> Danh sách phiếu xuất kho</div>
                <div class="table-toolbar-right">
                    Hiển thị
                    <select class="per-page-select" id="perPageSelect">
                        <option>10</option>
                        <option selected>20</option>
                        <option>50</option>
                    </select>
                    dòng
                    <i class="fas fa-cog" style="color:#94a3b8; cursor:pointer;" title="Cài đặt cột"></i>
                </div>
            </div>

            <div class="table-scroll">
                <table class="data-table" id="outboundTable">
                    <thead>
                        <tr>
                            <th width="30" style="text-align:center;"><input type="checkbox" id="check-all-ob" onclick="toggleCheckAllOB(this)" style="width:15px;height:15px;cursor:pointer;"></th>
                            <th width="35">STT</th>
                            <th width="85">Ngày xuất</th>
                            <th width="100">Số phiếu</th>
                            <th width="75">Loại xuất</th>
                            <th width="160">Khách hàng / ĐV nhận</th>
                            <th width="120">Kho xuất</th>
                            <th width="80">Mã hàng</th>
                            <th width="140">Tên hàng</th>
                            <th width="80">LOT</th>
                            <th width="85">HSD</th>
                            <th width="45">ĐVT</th>
                            <th width="75">SL xuất</th>
                            <th width="90">Đơn giá (VNĐ)</th>
                            <th width="100">Thành tiền (VNĐ)</th>
                            <th width="145">Nguồn phiếu</th>
                            <th width="65">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        @php
                        $rows = [
                            ['12/03/2026','XK-2026-0032','Bán hàng','CÔNG TY TNHH ABC','KHO NGUYÊN LIỆU','EX-ALV-05','Chiết xuất Aloe vera','F20250406','14/08/2026','Kg',25,'900.000','22.500.000','Phiếu giao hàng','GH-2026-0045'],
                            ['12/03/2026','XK-2026-0031','Bán hàng','CÔNG TY CP HÓA CHẤT VN','KHO NGUYÊN LIỆU','AC-NIA-25','Niacinamide PCC','F20250405','13/08/2026','Kg',50,'1.200.000','60.000.000','Phiếu giao hàng','GH-2026-0044'],
                            ['11/03/2026','XK-2026-0030','Bán hàng','CÔNG TY TNHH XYZ','KHO THÀNH PHẨM','HM-GLY-25','Glycerin','F20250408','16/08/2026','Kg',25,'700.000','17.500.000','Phiếu giao hàng','GH-2026-0043'],
                            ['11/03/2026','XK-2026-0029','Nội bộ','Sản xuất mỹ phẩm','KHO NGUYÊN LIỆU','EM-CG-25','Emolient CG','F20250409','17/08/2026','Kg',10,'950.000','9.500.000','Xuất kho nội bộ','NB-2026-0015'],
                            ['10/03/2026','XK-2026-0028','Bán hàng','CÔNG TY TNHH ABC','KHO NGUYÊN LIỆU','PR-BC-25','Chất bảo quản','F20250410','18/08/2026','Kg',25,'1.000.000','25.000.000','Phiếu giao hàng','GH-2026-0042'],
                            ['10/03/2026','XK-2026-0027','Bán hàng','CÔNG TY CP HÓA CHẤT VN','KHO NGUYÊN LIỆU','EX-ALV-05','Chiết xuất Aloe vera','F20250406','15/08/2026','Kg',25,'900.000','22.500.000','Phiếu giao hàng','GH-2026-0041'],
                            ['09/03/2026','XK-2026-0026','Nội bộ','Phòng QC','KHO NGUYÊN LIỆU','AO-OXY-25','Chống oxy hóa','F20250411','20/08/2026','Kg',2,'1.050.000','2.100.000','Xuất kho nội bộ','NB-2026-0014'],
                            ['09/03/2026','XK-2026-0025','Bán hàng','CÔNG TY TNHH XYZ','KHO THÀNH PHẨM','AC-NIA-25','Niacinamide PCC','F20250405','13/08/2026','Kg',25,'1.200.000','30.000.000','Phiếu giao hàng','GH-2026-0039'],
                            ['08/03/2026','XK-2026-0024','Bán hàng','CÔNG TY TNHH ABC','KHO NGUYÊN LIỆU','HM-GLY-25','Glycerin','F20250408','16/08/2026','Kg',25,'700.000','17.500.000','Phiếu giao hàng','GH-2026-0038'],
                            ['08/03/2026','XK-2026-0023','Bán hàng','CÔNG TY CP HÓA CHẤT VN','KHO NGUYÊN LIỆU','EX-ALV-05','Chiết xuất Aloe vera','F20250406','14/08/2026','Kg',25,'900.000','22.500.000','Phiếu giao hàng','GH-2026-0037'],
                        ];
                        @endphp

                        @foreach ($rows as $i => $r)
                        <tr>
                            <td class="td-center"><input type="checkbox" class="check-ob" style="width:15px;height:15px;cursor:pointer;"></td>
                            <td class="td-center" style="color:#64748b; font-size:11px;">{{ $i + 1 }}</td>
                            <td class="td-center" style="font-size:11px;">{{ $r[0] }}</td>
                            <td class="td-center"><a href="#" class="link-phieu">{{ $r[1] }}</a></td>
                            <td class="td-center">
                                @if($r[2] === 'Bán hàng')
                                    <span class="badge-type badge-banhang">Bán hàng</span>
                                @else
                                    <span class="badge-type badge-noibo">Nội bộ</span>
                                @endif
                            </td>
                            <td>{{ $r[3] }}</td>
                            <td style="font-size:11px;">{{ $r[4] }}</td>
                            <td class="td-center" style="font-weight:600; color:#0070D2; font-size:11px;">{{ $r[5] }}</td>
                            <td style="font-size:11px;">{{ $r[6] }}</td>
                            <td class="td-center" style="font-size:11px;">{{ $r[7] }}</td>
                            <td class="td-center" style="font-size:11px;">{{ $r[8] }}</td>
                            <td class="td-center">{{ $r[9] }}</td>
                            <td class="td-right" style="font-weight:600;">{{ number_format($r[10], 2, ',', '.') }}</td>
                            <td class="td-right">{{ $r[11] }}</td>
                            <td class="td-right" style="font-weight:600; color:#1e293b;">{{ $r[12] }}</td>
                            <td style="text-align: center;">
                                <div class="nguon-cell">
                                    {{ $r[13] }}<br>
                                    <span class="nguon-code">{{ $r[14] }}</span>
                                </div>
                            </td>
                            <td class="td-center">
                                <button class="action-btn eye" title="Xem chi tiết"><i class="fas fa-eye"></i></button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- PAGINATION --}}
            <div class="pagination-bar">
                <div class="pagination-info">Hiện thị 1 đến 10 của 28 kết quả</div>
                <div style="display:flex; align-items:center; gap:12px;">
                    <div class="pagination-controls">
                        <button class="page-btn" disabled title="Trang đầu"><i class="fas fa-angle-double-left"></i></button>
                        <button class="page-btn" disabled title="Trang trước"><i class="fas fa-angle-left"></i></button>
                        <button class="page-btn active">1</button>
                        <button class="page-btn">2</button>
                        <button class="page-btn" title="Trang sau"><i class="fas fa-angle-right"></i></button>
                        <button class="page-btn" title="Trang cuối"><i class="fas fa-angle-double-right"></i></button>
                    </div>
                    <div class="pagination-goto">
                        Đến trang
                        <input type="number" class="goto-input" value="1" min="1" max="99">
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    function toggleCheckAllOB(src) {
        document.querySelectorAll('.check-ob').forEach(cb => cb.checked = src.checked);
    }

    // Clear filter
    document.querySelector('.btn-clear')?.addEventListener('click', () => {
        document.querySelectorAll('.filter-input').forEach(el => {
            if (el.tagName === 'SELECT') el.selectedIndex = 0;
            else if (el.type === 'date') el.value = '';
            else el.value = '';
        });
    });
</script>
@endpush
