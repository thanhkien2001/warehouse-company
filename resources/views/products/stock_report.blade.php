@extends('layouts.app')

@section('title', 'Báo cáo tồn kho')

@push('styles')
<style>
    /* ===== LAYOUT ===== */
    .sk-container {
        display: flex;
        flex-direction: column;
        gap: 10px;
        max-width: 1620px;
        margin: 0 auto;
        height: calc(100vh - 100px);
        min-width: 1200px;
    }

    /* ===== PAGE HEADER ===== */
    .sk-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .sk-header h2 { font-weight: 800; font-size: 18px; margin: 0; }
    .sk-header .breadcrumb { font-size: 11px; color: #64748b; margin: 2px 0 0; }
    .sk-header-actions { display: flex; gap: 8px; align-items: center; }

    /* ===== BLOCK ===== */
    .sk-block {
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.08);
        border: 1px solid #e2e8f0;
        padding: 14px 16px;
    }

    /* ===== FILTER ===== */
    .filter-grid-5 {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 8px 14px;
    }

    .filter-grid-6b {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 8px 14px;
        align-items: flex-end;
        margin-top: 8px;
    }

    /* Wrapper bao cả 2 row thành 1 grid thống nhất */
    .filter-unified {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 8px 14px;
        align-items: end;
    }

    .filter-unified .filter-row-span {
        grid-column: 1 / -1;
        display: contents;
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
        background: #0070D2; color: #fff; border: none;
        padding: 0 16px; height: 32px; border-radius: 5px;
        font-size: 12px; font-weight: 600; cursor: pointer;
        white-space: nowrap; display: flex; align-items: center; justify-content: center; gap: 5px;
    }
    .btn-search:hover { background: #005cb8; }

    .btn-clear {
        background: #E74C3C; color: #fff; border: none;
        padding: 0 12px; height: 32px; border-radius: 5px;
        font-size: 12px; font-weight: 600; cursor: pointer;
        white-space: nowrap; display: flex; align-items: center; justify-content: center; gap: 5px;
    }
    .btn-clear:hover { background: #b91c1c; }

    /* ===== STAT CARDS ===== */
    .stat-cards {
        display: grid;
        grid-template-columns: repeat(6, 1fr);
        gap: 10px;
    }

    .stat-card {
        background: #fff; border-radius: 8px;
        border: 1px solid #e2e8f0; padding: 11px 14px;
        display: flex; align-items: center; gap: 11px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.06);
    }

    .stat-icon {
        width: 38px; height: 38px; border-radius: 9px;
        display: flex; align-items: center; justify-content: center;
        font-size: 17px; flex-shrink: 0;
    }
    .stat-icon.blue   { background: #eff6ff; color: #3b82f6; }
    .stat-icon.green  { background: #f0fdf4; color: #22c55e; }
    .stat-icon.orange { background: #fff7ed; color: #f97316; }
    .stat-icon.purple { background: #faf5ff; color: #a855f7; }
    .stat-icon.amber  { background: #fffbeb; color: #f59e0b; }
    .stat-icon.red    { background: #fef2f2; color: #ef4444; }

    .stat-label { font-size: 10px; color: #64748b; margin-bottom: 2px; }
    .stat-value { font-size: 18px; font-weight: 800; color: #1e293b; line-height: 1.1; }
    .stat-unit  { font-size: 10px; color: #94a3b8; font-weight: 500; margin-top: 1px; }

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
        display: flex; justify-content: space-between;
        align-items: center; margin-bottom: 10px;
    }

    .table-title {
        font-size: 13px; font-weight: 700; color: #0070D2;
        text-transform: uppercase; display: flex; align-items: center; gap: 6px;
    }

    .table-toolbar-right {
        display: flex; align-items: center; gap: 8px;
        font-size: 12px; color: #475569;
    }

    .per-page-select {
        padding: 3px 8px; border: 1px solid #cbd5e1;
        border-radius: 4px; font-size: 12px; outline: none; height: 28px;
    }

    .table-scroll {
        flex: 1;
        overflow-y: auto;
        overflow-x: auto;
        border: 1px solid #e2e8f0;
        border-radius: 5px;
    }

    /* ===== DATA TABLE ===== */
    .data-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 1400px;
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

    .td-center { text-align: center; }
    .td-right  { text-align: right; }

    /* ===== PARENT ROW ===== */
    .parent-row {
        cursor: pointer;
    }
    .parent-row td {
        font-weight: 700;
    }

    .toggle-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: white;
        width: 20px; height: 20px;
        border-radius: 4px;
        color: black;
        font-size: 10px;
        cursor: pointer;
        transition: transform 0.2s;
        border: none;
        flex-shrink: 0;
    }

    .toggle-btn.open { transform: rotate(90deg); }

    .parent-stt-cell {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 4px;
    }

    /* ===== CHILD ROW ===== */
    .child-row { display: none; background: #fff; }
    .child-row.visible { display: table-row; }
    .child-row:hover { background: #f8fafc; }

    .child-row td { font-size: 11px; border-top: none; }

    .child-stt {
        color: #94a3b8;
        font-size: 11px;
        font-weight: 400;
        text-align: center;
    }

    /* ===== BADGES ===== */
    .badge-status {
        display: inline-flex; align-items: center;
        padding: 2px 8px; border-radius: 20px;
        font-size: 10px; font-weight: 700; white-space: nowrap;
    }
    .badge-ok      { background: #f0fdf4; color: #16a34a; }
    .badge-near    { background: #fffbeb; color: #d97706; }
    .badge-expired { background: #fef2f2; color: #dc2626; }

    /* ===== ACTION BTNS ===== */
    .action-btn {
        background: none; border: none; cursor: pointer;
        padding: 3px 5px; border-radius: 4px;
        font-size: 14px; line-height: 1;
    }
    .action-btn.eye  { color: #0070D2; }
    .action-btn:hover { opacity: 0.7; }

    /* ===== PAGINATION ===== */
    .pagination-bar {
        display: flex; justify-content: space-between;
        align-items: center; margin-top: 10px;
        font-size: 12px; color: #64748b; flex-shrink: 0;
    }

    .pagination-controls { display: flex; align-items: center; gap: 4px; }

    .page-btn {
        width: 28px; height: 28px; display: flex;
        align-items: center; justify-content: center;
        border: 1px solid #e2e8f0; border-radius: 5px;
        background: #fff; font-size: 12px;
        cursor: pointer; color: #475569; font-weight: 600;
    }
    .page-btn.active { background: #0070D2; color: #fff; border-color: #0070D2; }
    .page-btn:hover:not(.active):not(:disabled) { background: #f1f5f9; }
    .page-btn:disabled { opacity: 0.4; cursor: default; }

    .pagination-goto {
        display: flex; align-items: center; gap: 6px;
        font-size: 12px; color: #64748b;
    }
    .goto-input {
        width: 42px; height: 28px; text-align: center;
        border: 1px solid #cbd5e1; border-radius: 5px;
        font-size: 12px; outline: none;
    }

    /* ===== HEADER BUTTONS ===== */
    .btn-outline-hdr {
        background: #fff; border: 1px solid #cbd5e1;
        padding: 6px 13px; border-radius: 5px;
        font-size: 12px; font-weight: 600; cursor: pointer;
        display: flex; align-items: center; gap: 5px; color: #475569;
    }
    .btn-outline-hdr.excel { color: #16a34a; border-color: #bbf7d0; background: #f0fdf4; }
    .btn-outline-hdr.pdf   { color: #dc2626; border-color: #fecaca; background: #fef2f2; }
    .btn-outline-hdr:hover { opacity: 0.85; }

    /* aggregated values in parent row */
    .agg-qty   { color: #0070D2; font-size: 13px; }
    .agg-value { color: #1e293b; font-size: 12px; }
</style>
@endpush

@section('content')
<div class="sk-container">

    {{-- PAGE HEADER --}}
    <div class="sk-header">
        <div>
            <h2>Báo cáo tồn kho</h2>
            <p class="breadcrumb">Trang chủ › Quản lý kho › Báo cáo tồn kho</p>
        </div>
        <div class="sk-header-actions">
            <button class="btn-outline-hdr excel"><i class="fas fa-file-excel"></i> Xuất Excel</button>
            <button class="btn-outline-hdr pdf"><i class="fas fa-file-pdf"></i> Xuất PDF</button>
        </div>
    </div>

    {{-- BLOCK 1: BỘ LỌC --}}
    <div class="sk-block">
        <div class="filter-unified">
            {{-- Row 1: 5 inputs --}}
            <div class="filter-item">
                <label>Từ ngày</label>
                <input type="date" class="filter-input" value="{{ date('Y-m-01') }}">
            </div>
            <div class="filter-item">
                <label>Đến ngày</label>
                <input type="date" class="filter-input" value="{{ date('Y-m-d') }}">
            </div>
            <div class="filter-item">
                <label>Mã hàng</label>
                <input type="text" class="filter-input" placeholder="Chọn mã hàng">
            </div>
            <div class="filter-item">
                <label>Tên hàng</label>
                <input type="text" class="filter-input" placeholder="Nhập tên hàng">
            </div>
            <div class="filter-item">
                <label>LOT</label>
                <input type="text" class="filter-input" placeholder="Nhập số LOT">
            </div>

            {{-- Row 2: 4 inputs + 1 cell có 2 nút --}}
            <div class="filter-item">
                <label>Nhóm hàng</label>
                <select class="filter-input">
                    <option value="">Tất cả</option>
                    <option>Chiết xuất</option>
                    <option>Chất dưỡng ẩm</option>
                    <option>Dầu làm mềm</option>
                    <option>Hoạt chất</option>
                    <option>Chất bảo quản</option>
                </select>
            </div>
            <div class="filter-item">
                <label>Kho</label>
                <select class="filter-input">
                    <option value="">Tất cả</option>
                    <option>Kho Nguyên Liệu</option>
                    <option>Kho Lab</option>
                </select>
            </div>
            <div class="filter-item">
                <label>HSD từ ngày</label>
                <input type="date" class="filter-input">
            </div>
            <div class="filter-item">
                <label>HSD đến ngày</label>
                <input type="date" class="filter-input">
            </div>
            {{-- Cột 5 của row 2: 2 nút ngang hàng --}}
            <div style="display: flex; flex-direction: row; gap: 6px; align-items: flex-end;">
                <button class="btn-search" style="flex: 1;"><i class="fas fa-search"></i> Tìm kiếm</button>
                <button class="btn-clear" style="flex: 1;">Xóa lọc</button>
            </div>
        </div>
    </div>

    {{-- BLOCK 2: STAT CARDS (6 ô) --}}
    <div class="stat-cards">
        <div class="stat-card">
            <div class="stat-icon blue"><i class="fas fa-layer-group"></i></div>
            <div>
                <div class="stat-label">Tổng số SKU</div>
                <div class="stat-value">56</div>
                <div class="stat-unit">mặt hàng</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon green"><i class="fas fa-tags"></i></div>
            <div>
                <div class="stat-label">Tổng số LOT</div>
                <div class="stat-value">162</div>
                <div class="stat-unit">lô hàng</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon orange"><i class="fas fa-weight-hanging"></i></div>
            <div>
                <div class="stat-label">Tổng tồn lượng</div>
                <div class="stat-value" style="font-size:15px;">12.450,50</div>
                <div class="stat-unit">Kg</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon purple"><i class="fas fa-coins"></i></div>
            <div>
                <div class="stat-label">Giá trị tồn kho</div>
                <div class="stat-value" style="font-size:13px;">3.568.250.000</div>
                <div class="stat-unit">VNĐ</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon amber"><i class="fas fa-exclamation-triangle"></i></div>
            <div>
                <div class="stat-label">Hàng sắp hết hạn</div>
                <div class="stat-value" style="color:#d97706;">7</div>
                <div class="stat-unit">lô hàng</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon red"><i class="fas fa-exclamation-circle"></i></div>
            <div>
                <div class="stat-label">Hàng hết hạn</div>
                <div class="stat-value" style="color:#dc2626;">2</div>
                <div class="stat-unit">lô hàng</div>
            </div>
        </div>
    </div>

    {{-- BLOCK 3: TABLE --}}
    <div class="sk-block table-block">
        <div class="table-block-inner">
            <div class="table-toolbar">
                <div class="table-title"><i class="fas fa-table"></i> Danh sách tồn kho</div>
                <div class="table-toolbar-right">
                    Hiển thị
                    <select class="per-page-select">
                        <option>10</option>
                        <option selected>20</option>
                        <option>50</option>
                    </select>
                    dòng
                    <i class="fas fa-cog" style="color:#94a3b8; cursor:pointer;" title="Cài đặt cột"></i>
                </div>
            </div>

            <div class="table-scroll">
                <table class="data-table" id="stockTable">
                    <thead>
                        <tr>
                            <th width="40">STT</th>
                            <th width="85">Mã hàng</th>
                            <th width="160">Tên hàng</th>
                            <th width="90">Nhóm hàng</th>
                            <th width="45">ĐVT</th>
                            <th width="90">LOT</th>
                            <th width="85">HSD</th>
                            <th width="130">Kho</th>
                            <th width="100">Tồn sử dụng (Kg)</th>
                            <th width="110">Đơn giá vốn (VNĐ)</th>
                            <th width="120">Giá trị tồn (VNĐ)</th>
                            <th width="85">Trạng thái</th>
                            <th width="60">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>

                        @php
                        $products = [
                            [
                                'ma' => 'EX-ALV-05', 'ten' => 'Chiết xuất Aloe vera', 'nhom' => 'Chiết xuất', 'dvt' => 'Kg',
                                'ton' => '1.250,00', 'gtri' => '937.500.000',
                                'lots' => [
                                    ['F20250406','14/08/2026','KHO NGUYÊN LIỆU','500,00','750.000','375.000.000','Còn hạn','ok'],
                                    ['F20250407','15/08/2026','KHO NGUYÊN LIỆU','400,00','750.000','300.000.000','Còn hạn','ok'],
                                    ['F20250408','17/08/2026','KHO NGUYÊN LIỆU','250,00','1.050.000','262.500.000','Sắp hết hạn','near'],
                                    ['F20250409','19/08/2026','KHO NGUYÊN LIỆU','100,00','1.050.000','105.000.000','Sắp hết hạn','near'],
                                ],
                            ],
                            [
                                'ma' => 'AC-NIA-25', 'ten' => 'Niacinamide PCC', 'nhom' => 'Hoạt chất', 'dvt' => 'Kg',
                                'ton' => '1.000,00', 'gtri' => '800.000.000',
                                'lots' => [
                                    ['F20250501','20/09/2026','KHO NGUYÊN LIỆU','600,00','800.000','480.000.000','Còn hạn','ok'],
                                    ['F20250502','22/09/2026','KHO LAB','400,00','800.000','320.000.000','Còn hạn','ok'],
                                ],
                            ],
                            [
                                'ma' => 'HM-GLY-25', 'ten' => 'Glycerin', 'nhom' => 'Chất dưỡng ẩm', 'dvt' => 'Kg',
                                'ton' => '2.500,00', 'gtri' => '1.750.000.000',
                                'lots' => [
                                    ['G20250301','05/06/2026','KHO NGUYÊN LIỆU','1.500,00','700.000','1.050.000.000','Còn hạn','ok'],
                                    ['G20250302','10/06/2026','KHO NGUYÊN LIỆU','1.000,00','700.000','700.000.000','Còn hạn','ok'],
                                ],
                            ],
                            [
                                'ma' => 'EM-CG-25', 'ten' => 'Emolient CG', 'nhom' => 'Dầu làm mềm', 'dvt' => 'Kg',
                                'ton' => '950,00', 'gtri' => '617.500.000',
                                'lots' => [
                                    ['E20250401','01/05/2026','KHO LAB','450,00','650.000','292.500.000','Sắp hết hạn','near'],
                                    ['E20250402','02/05/2026','KHO LAB','500,00','650.000','325.000.000','Sắp hết hạn','near'],
                                ],
                            ],
                            [
                                'ma' => 'PR-BQ-25', 'ten' => 'Chất bảo quản', 'nhom' => 'Chất bảo quản', 'dvt' => 'Kg',
                                'ton' => '1.200,00', 'gtri' => '540.000.000',
                                'lots' => [
                                    ['P20250201','10/03/2026','KHO NGUYÊN LIỆU','200,00','450.000','90.000.000','Hết hạn','expired'],
                                    ['P20250202','25/10/2026','KHO NGUYÊN LIỆU','1.000,00','450.000','450.000.000','Còn hạn','ok'],
                                ],
                            ],
                            [
                                'ma' => 'AO-OXY-25', 'ten' => 'Chống oxy hóa', 'nhom' => 'Hoạt chất', 'dvt' => 'Kg',
                                'ton' => '300,00', 'gtri' => '78.750.000',
                                'lots' => [
                                    ['A20250301','15/09/2026','KHO LAB','300,00','262.500','78.750.000','Còn hạn','ok'],
                                ],
                            ],
                        ];
                        @endphp

                        @foreach($products as $pi => $prod)
                        {{-- PARENT ROW --}}
                        <tr class="parent-row" onclick="toggleGroup({{ $pi }})">
                            <td class="td-center">
                                <div class="parent-stt-cell">
                                    <button class="toggle-btn" id="toggle-{{ $pi }}">
                                        <i class="fas fa-chevron-right"></i>
                                    </button>
                                </div>
                            </td>
                            <td style="font-weight:700; color:#0070D2; font-size:12px;">{{ $prod['ma'] }}</td>
                            <td>{{ $prod['ten'] }}</td>
                            <td class="td-center">{{ $prod['nhom'] }}</td>
                            <td class="td-center">{{ $prod['dvt'] }}</td>
                            {{-- LOT, HSD, Kho, Đơn giá vốn, Trạng thái, Thao tác: để trống ở parent --}}
                            <td></td>
                            <td></td>
                            <td></td>
                            <td class="td-right"><span class="agg-qty">{{ $prod['ton'] }}</span></td>
                            <td></td>
                            <td class="td-right"><span class="agg-value" style="font-weight:700;">{{ $prod['gtri'] }}</span></td>
                            <td></td>
                            <td></td>
                        </tr>

                        {{-- CHILD ROWS --}}
                        @foreach($prod['lots'] as $li => $lot)
                        <tr class="child-row" data-group="{{ $pi }}">
                            <td class="child-stt">{{ $li + 1 }}</td>
                            <td style="color:#0070D2; font-size:11px; padding-left:16px;">{{ $prod['ma'] }}</td>
                            <td style="font-size:11px; color:#475569; padding-left:10px;">{{ $prod['ten'] }}</td>
                            <td class="td-center" style="font-size:11px;">{{ $prod['nhom'] }}</td>
                            <td class="td-center" style="font-size:11px;">{{ $prod['dvt'] }}</td>
                            <td class="td-center" style="font-size:11px; font-weight:600;">{{ $lot[0] }}</td>
                            <td class="td-center" style="font-size:11px;">{{ $lot[1] }}</td>
                            <td style="font-size:11px;">{{ $lot[2] }}</td>
                            <td class="td-right" style="font-weight:600;">{{ $lot[3] }}</td>
                            <td class="td-right" style="font-size:11px;">{{ $lot[4] }}</td>
                            <td class="td-right" style="font-size:11px;">{{ $lot[5] }}</td>
                            <td class="td-center">
                                @if($lot[7] === 'ok')
                                    <span class="badge-status badge-ok">Còn hạn</span>
                                @elseif($lot[7] === 'near')
                                    <span class="badge-status badge-near">Sắp hết hạn</span>
                                @else
                                    <span class="badge-status badge-expired">Hết hạn</span>
                                @endif
                            </td>
                            <td class="td-center">
                                <button class="action-btn eye" title="Xem chi tiết"><i class="fas fa-eye"></i></button>
                            </td>
                        </tr>
                        @endforeach
                        @endforeach

                    </tbody>
                </table>
            </div>

            {{-- PAGINATION --}}
            <div class="pagination-bar">
                <div>Hiển thị 1 đến 5 của 5 kết quả</div>
                <div style="display:flex; align-items:center; gap:12px;">
                    <div class="pagination-controls">
                        <button class="page-btn" disabled><i class="fas fa-angle-double-left"></i></button>
                        <button class="page-btn" disabled><i class="fas fa-angle-left"></i></button>
                        <button class="page-btn active">1</button>
                        <button class="page-btn"><i class="fas fa-angle-right"></i></button>
                        <button class="page-btn"><i class="fas fa-angle-double-right"></i></button>
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
    function toggleGroup(groupId) {
        const children = document.querySelectorAll(`.child-row[data-group="${groupId}"]`);
        const btn = document.getElementById(`toggle-${groupId}`);
        const isOpen = btn.classList.contains('open');

        if (isOpen) {
            children.forEach(r => r.classList.remove('visible'));
            btn.classList.remove('open');
        } else {
            children.forEach(r => r.classList.add('visible'));
            btn.classList.add('open');
        }
    }

    // Clear filter
    document.querySelector('.btn-clear')?.addEventListener('click', () => {
        document.querySelectorAll('.filter-input').forEach(el => {
            if (el.tagName === 'SELECT') el.selectedIndex = 0;
            else el.value = '';
        });
    });
</script>
@endpush
