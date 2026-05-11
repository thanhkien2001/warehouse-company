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
        min-height: calc(100vh - 100px);
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
        font-size: 13px;
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
        font-size: 13px;
        font-weight: 600;
        color: black;
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

    .filter-input:focus { border-color: #002B6B; }

    .btn-search {
        background: #002B6B;
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
        font-size: 13px;
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



    .table-toolbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
    }

    .table-title {
        font-size: 15px;
        font-weight: 700;
        color: #002B6B;
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
        font-size: 13px;
        font-weight: 700;
        color: #05080c;
        border: 1px solid #e2e8f0;
        text-align: center;
        white-space: nowrap;
        position: sticky;
        top: 0;
        z-index: 10;
        text-transform: uppercase;
    }

    .data-table td {
        padding: 7px 7px;
        font-size: 13px;
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
        color: #002B6B;
        font-weight: 600;
        text-decoration: none;
        font-size: 13px;
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

    .action-btn.eye  { color: #002B6B; }
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
        background: #002B6B;
        color: #fff;
        border-color: #002B6B;
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
    .btn-outline-hdr.filter { background: #002B6B; color: #fff; border-color: #002B6B; }

    .btn-outline-hdr:hover { opacity: 0.85; }

    /* ===== NGUON PHIEU cell ===== */
    .nguon-cell {
        font-size: 13px;
        color: #64748b;
    }

    .nguon-code {
        color: #002B6B;
        font-weight: 600;
    }
</style>
@endpush

@section('content')
<div class="obr-container">

    {{-- PAGE HEADER --}}
    <div class="obr-header">
        <div>
            <h2 style="font-size: 20px; font-weight: 900; color: #0f172a; margin: 0 0 5px 0;text-transform: uppercase;">Báo cáo xuất kho</h2>
        </div>
        <div class="obr-header-actions">
            <button class="btn-outline-hdr excel"><i class="fas fa-file-excel"></i> Xuất Excel</button>
            <button class="btn-outline-hdr pdf"><i class="fas fa-file-pdf"></i> Xuất PDF</button>
        </div>
    </div>

    {{-- BLOCK 1: BỘ LỌC --}}
    <div class="obr-block">
        <form class="filter-unified-6" method="GET" action="{{ route('inventory.outbound-report') }}">
            {{-- Row 1: 5 inputs + 1 ô trống --}}
            <div class="filter-item">
                <label>Từ ngày</label>
                <input type="date" name="tu_ngay" class="filter-input" value="{{ request('tu_ngay') }}">
            </div>
            <div class="filter-item">
                <label>Đến ngày</label>
                <input type="date" name="den_ngay" class="filter-input" value="{{ request('den_ngay') }}">
            </div>
            <div class="filter-item">
                <label>Loại xuất</label>
                <select name="loai_xuat" class="filter-input">
                    <option value="">Tất cả</option>
                    <option value="banhang" {{ request('loai_xuat') == 'banhang' ? 'selected' : '' }}>Bán hàng</option>
                    <option value="noibo" {{ request('loai_xuat') == 'noibo' ? 'selected' : '' }}>Nội bộ</option>
                </select>
            </div>
            <div class="filter-item">
                <label>Mã hàng</label>
                <input type="text" name="ma_hang" class="filter-input" placeholder="Chọn mã hàng" value="{{ request('ma_hang') }}">
            </div>
            <div class="filter-item">
                <label>Khách hàng</label>
                <input type="text" name="khach_hang" class="filter-input" placeholder="Nhập tên khách hàng" value="{{ request('khach_hang') }}">
            </div>
            {{-- Cột 6 row 1: trống --}}
            <div></div>

            {{-- Row 2: 5 inputs + 2 nút --}}
            <div class="filter-item">
                <label>Kho xuất</label>
                <select name="kho_xuat" class="filter-input">
                    <option value="">Tất cả</option>
                    <option value="Kho Nguyên Liệu" {{ request('kho_xuat') == 'Kho Nguyên Liệu' ? 'selected' : '' }}>Kho Nguyên Liệu</option>
                    <option value="Kho Lab" {{ request('kho_xuat') == 'Kho Lab' ? 'selected' : '' }}>Kho Lab</option>
                    <option value="Kho Thành Phẩm" {{ request('kho_xuat') == 'Kho Thành Phẩm' ? 'selected' : '' }}>Kho Thành Phẩm</option>
                </select>
            </div>
            <div class="filter-item">
                <label>Số phiếu</label>
                <input type="text" name="so_phieu" class="filter-input" placeholder="Nhập số phiếu" value="{{ request('so_phieu') }}">
            </div>
            <div class="filter-item">
                <label>Số đơn hàng</label>
                <input type="text" name="so_don_hang" class="filter-input" placeholder="Nhập số đơn hàng" value="{{ request('so_don_hang') }}">
            </div>
            <div class="filter-item">
                <label>LOT</label>
                <input type="text" name="lot" class="filter-input" placeholder="Nhập số LOT" value="{{ request('lot') }}">
            </div>
            <div class="filter-item">
                <label>Nhóm hàng</label>
                <select name="nhom_hang" class="filter-input">
                    <option value="">Tất cả</option>
                    <option value="nguyenlieu" {{ request('nhom_hang') == 'nguyenlieu' ? 'selected' : '' }}>Nguyên liệu</option>
                    <option value="thanhpham" {{ request('nhom_hang') == 'thanhpham' ? 'selected' : '' }}>Thành phẩm</option>
                    <option value="baobi" {{ request('nhom_hang') == 'baobi' ? 'selected' : '' }}>Bao bì</option>
                </select>
            </div>
            {{-- Cột 6 row 2: 2 nút ngang hàng --}}
            <div style="display: flex; flex-direction: row; gap: 6px; align-items: flex-end;">
                <button type="submit" class="btn-search" style="flex: 1;"><i class="fas fa-search"></i> Tìm kiếm</button>
                <button type="button" class="btn-clear" onclick="window.location.href='{{ route('inventory.outbound-report') }}'" style="flex: 1; background-color: #E74C3C; color: white; border-color: #E74C3C;">Xóa lọc</button>
            </div>
        </form>
    </div>

    {{-- BLOCK 2: STAT CARDS --}}
    <div class="stat-cards">
        <div class="stat-card">
            <div class="stat-icon blue"><i class="fas fa-file-alt"></i></div>
            <div>
                <div class="stat-label">Tổng số phiếu</div>
                <div class="stat-value">{{ $totalBills }}</div>
                <div class="stat-unit">phiếu</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon green"><i class="fas fa-boxes"></i></div>
            <div>
                <div class="stat-label">Tổng số lượng xuất</div>
                <div class="stat-value" style="font-size:15px;">{{ number_format($totalQty, 2, ',', '.') }}</div>
                <div class="stat-unit">Kg</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon orange"><i class="fas fa-coins"></i></div>
            <div>
                <div class="stat-label">Tổng giá trị xuất (VNĐ)</div>
                <div class="stat-value" style="font-size:15px;">{{ number_format($totalVal, 0, ',', '.') }}</div>
                <div class="stat-unit">VNĐ</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon purple"><i class="fas fa-building"></i></div>
            <div>
                <div class="stat-label">Số khách hàng</div>
                <div class="stat-value">{{ $totalCusts }}</div>
                <div class="stat-unit">khách hàng</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon teal"><i class="fas fa-warehouse"></i></div>
            <div>
                <div class="stat-label">Số kho xuất</div>
                <div class="stat-value">{{ $totalWHS }}</div>
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
                    <tbody id="tableBody">
                        @forelse ($rows as $i => $r)
                        <tr>
                            <td class="td-center"><input type="checkbox" class="check-ob" style="width:15px;height:15px;cursor:pointer;"></td>
                            <td class="td-center" style="color:#64748b; font-size:13px;">{{ ($rows->firstItem() ?? 1) + $i }}</td>
                            <td class="td-center" style="font-size:13px;">{{ \Carbon\Carbon::parse($r->delivery_date)->format('d/m/Y') }}</td>
                            <td class="td-center"><a href="{{ url('/phieu-giao/' . $r->dn_id) }}" class="link-phieu">{{ $r->dn_code }}</a></td>
                            <td class="td-center">
                                <span class="badge-type badge-banhang">Bán hàng</span>
                            </td>
                            <td style="text-transform:uppercase;">{{ $r->ten_kh }}</td>
                            <td class="td-center" style="font-size:13px;">{{ $r->kho_xuat ?? 'Kho Nguyên Liệu' }}</td>
                            <td class="td-center" style="font-weight:600; color:#002B6B; font-size:13px;">{{ $r->ma_hang }}</td>
                            <td style="font-size:13px;">{{ $r->ten_hang }}</td>
                            <td class="td-center" style="font-size:13px;">{{ $r->ma_lot }}</td>
                            <td class="td-center" style="font-size:13px;">{{ $r->han_su_dung ? \Carbon\Carbon::parse($r->han_su_dung)->format('d/m/Y') : '---' }}</td>
                            <td class="td-center">{{ $r->don_vi_tinh }}</td>
                            <td class="td-right" style="font-weight:600;">{{ number_format($r->so_luong, 2, ',', '.') }}</td>
                            <td class="td-right">{{ number_format($r->don_gia, 0, ',', '.') }}</td>
                            <td class="td-right" style="font-weight:600; color:#1e293b;">{{ number_format($r->thanh_tien, 0, ',', '.') }}</td>
                            <td style="text-align: center;">
                                <div class="nguon-cell">
                                    Đơn Hàng<br>
                                    <span class="nguon-code"><a href="{{ url('/don-hang/' . $r->order_id) }}" style="color:#002B6B; text-decoration:none;">{{ $r->phieu_xuat }}</a></span>
                                </div>
                            </td>
                            <td class="td-center">
                                <a href="{{ url('/phieu-giao/' . $r->dn_id) }}" class="action-btn eye" title="Xem chi tiết"><i class="fas fa-eye"></i></a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="17" class="td-center" style="padding:30px; color:#64748b;">Không tìm thấy dữ liệu xuất kho.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- PAGINATION --}}
            <div class="pagination-bar">
                <div class="pagination-info">
                    Hiển thị {{ $rows->firstItem() ?? 0 }} đến {{ $rows->lastItem() ?? 0 }} của {{ $rows->total() }} kết quả
                </div>
                <div style="display:flex; align-items:center; gap:12px;">
                    <div class="pagination-controls">
                        <a class="page-btn {{ $rows->onFirstPage() ? 'disabled' : '' }}" href="{{ $rows->appends(request()->query())->url(1) }}" title="Trang đầu"><i class="fas fa-angle-double-left"></i></a>
                        <a class="page-btn {{ $rows->onFirstPage() ? 'disabled' : '' }}" href="{{ $rows->appends(request()->query())->previousPageUrl() }}" title="Trang trước"><i class="fas fa-angle-left"></i></a>
                        
                        @foreach($rows->appends(request()->query())->getUrlRange(max(1, $rows->currentPage() - 2), min($rows->lastPage(), $rows->currentPage() + 2)) as $page => $url)
                            <a class="page-btn {{ $page == $rows->currentPage() ? 'active' : '' }}" href="{{ $url }}">{{ $page }}</a>
                        @endforeach

                        <a class="page-btn {{ !$rows->hasMorePages() ? 'disabled' : '' }}" href="{{ $rows->appends(request()->query())->nextPageUrl() }}" title="Trang sau"><i class="fas fa-angle-right"></i></a>
                        <a class="page-btn {{ !$rows->hasMorePages() ? 'disabled' : '' }}" href="{{ $rows->appends(request()->query())->url($rows->lastPage()) }}" title="Trang cuối"><i class="fas fa-angle-double-right"></i></a>
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
