@extends('layouts.app')

@section('title', 'Danh mục sản phẩm')
<meta name="csrf-token" content="{{ csrf_token() }}">

@section('content')
<div class="card" style="padding: 24px; display: flex; flex-direction: column; gap: 20px;">
    {{-- PAGE HEADER (same style as /khach-hang) --}}
    <div class="page-header-row" style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 20px; border-bottom: 2.5px solid #cbd5e1; margin-bottom: 0;">
        <div style="display: flex; align-items: center; gap: 16px;">
            <div style="width: 56px; height: 56px; background: #eef2ff; border-radius: 16px; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 10px rgba(0, 112, 210, 0.15); flex-shrink: 0;">
                <i class="fas fa-th-large" style="font-size: 24px; color: #0070D2;"></i>
            </div>
            <div>
                <h2 style="font-size: 22px; font-weight: 800; color: #0f172a; margin: 0 0 4px 0; letter-spacing: -0.5px;">Danh mục sản phẩm</h2>
                <p style="margin: 0; color: #64748b; font-size: 13.5px;">Quản lý danh mục nguyên liệu và sản phẩm của kho.</p>
            </div>
        </div>
        <button class="btn-add-product" onclick="openProductModal()"><i class="fas fa-plus"></i> Thêm sản phẩm</button>
    </div>

    {{-- BLOCK 1: ACTIONS --}}
    <div class="action-bar">
        <div class="action-left">
            <button class="btn-action btn-import" onclick="openImportModal()"><i class="fas fa-file-import"></i> Import Excel</button>
            <a href="{{ route('catalog.export', request()->query()) }}" class="btn-action btn-export"><i class="fas fa-file-export"></i> Export Excel</a>
        </div>
    </div>

    {{-- BLOCK 2: FILTER --}}
    <div class="filter-card">
        <form method="GET" action="{{ route('catalog.index') }}" id="filterForm">
        <div class="filter-unified-6">
            <div class="filter-item">
                <label>Tìm kiếm</label>
                <div class="search-input-wrapper">
                    <input type="text" name="search" class="filter-input" placeholder="Nhập mã hoặc tên hàng" value="{{ request('search') }}">
                    <i class="fas fa-search"></i>
                </div>
            </div>
            <div class="filter-item">
                <label>Nhóm hàng</label>
                <select name="category_id" class="filter-input">
                    <option value="">Tất cả</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="filter-item">
                <label>Nhà cung cấp</label>
                <input type="text" name="nha_cung_cap" class="filter-input" placeholder="Tất cả" value="{{ request('nha_cung_cap') }}">
            </div>
            <div class="filter-item">
                <label>Trạng thái</label>
                <select name="trang_thai" class="filter-input">
                    <option value="">Tất cả</option>
                    <option value="Hoạt động" {{ request('trang_thai') == 'Hoạt động' ? 'selected' : '' }}>Hoạt động</option>
                    <option value="Ngừng hoạt động" {{ request('trang_thai') == 'Ngừng hoạt động' ? 'selected' : '' }}>Ngừng hoạt động</option>
                </select>
            </div>
            <div class="filter-item btn-group-col">
                <button type="submit" class="btn-search"><i class="fas fa-search"></i> Tìm kiếm</button>
                <a href="{{ route('catalog.index') }}" class="btn-clear"><i class="fas fa-times"></i> Xóa lọc</a>
            </div>
        </div>
        </form>
    </div>

    {{-- BLOCK 3: TABLE --}}
    <div class="table-card">
        <div class="table-responsive">
            <table class="catalog-table">
                <thead>
                    <tr>
                        <th width="3%"><input type="checkbox" class="check-all"></th>
                        <th width="3%">STT</th>
                        <th width="8%">Mã hàng</th>
                        <th width="20%">Tên hàng</th>
                        <th width="10%">Nhóm hàng</th>
                        <th width="8%">Quy cách</th>
                        <th width="5%">ĐVT</th>
                        <th width="10%">Giá bán (VNĐ)</th>
                        <th width="5%">VAT (%)</th>
                        <th width="12%">Nhà cung cấp</th>
                        <th width="8%">Trạng thái</th>
                        <th width="6%">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $index => $item)
                    <tr data-id="{{ $item->id }}"
                        data-ma="{{ $item->ma_hang }}"
                        data-ten="{{ $item->ten_hang }}"
                        data-cat="{{ $item->category_id }}"
                        data-quy-cach="{{ $item->quy_cach }}"
                        data-dvt="{{ $item->don_vi_tinh }}"
                        data-gia="{{ $item->gia_ban }}"
                        data-vat="{{ $item->vat }}"
                        data-ncc="{{ $item->nha_cung_cap }}"
                        data-ma-ncc="{{ $item->ma_ncc }}"
                        data-status="{{ $item->trang_thai }}"
                        data-note="{{ $item->ghi_chu }}">
                        <td><input type="checkbox" class="row-check" value="{{ $item->id }}"></td>
                        <td>{{ $loop->iteration }}</td>
                        <td class="text-bold">{{ $item->ma_hang }}</td>
                        <td class="text-left">{{ $item->ten_hang }}</td>
                        <td>{{ $item->category->name ?? '---' }}</td>
                        <td>{{ $item->quy_cach ?? '---' }}</td>
                        <td>{{ $item->don_vi_tinh ?? '---' }}</td>
                        <td class="text-right">{{ $item->gia_ban ? number_format($item->gia_ban, 0, ',', '.') : '---' }}</td>
                        <td>{{ $item->vat }}%</td>
                        <td class="text-left text-muted">{{ $item->nha_cung_cap ?? '---' }}</td>
                        <td>
                            <span class="status-badge {{ $item->trang_thai == 'Hoạt động' ? 'active' : 'inactive' }}">
                                {{ $item->trang_thai }}
                            </span>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <button class="btn-edit" title="Sửa" onclick="openEditModal(this)"><i class="fas fa-edit"></i></button>
                                <button class="btn-delete" title="Xóa" onclick="deleteProduct(this, {{ $item->id }})"><i class="fas fa-trash-alt"></i></button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="12" style="text-align:center; padding:40px; color:#94a3b8;"><i class="fas fa-box-open fa-2x" style="margin-bottom:10px; display:block;"></i>Chưa có sản phẩm nào</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINATION --}}
        <div class="pagination-bar">
            <div class="pagination-info">
                Hiển thị {{ $products->firstItem() ?? 0 }} đến {{ $products->lastItem() ?? 0 }} của {{ $products->total() }} kết quả
            </div>
            <div class="pagination-right">
                <div class="pagination-controls">
                    <a class="page-btn {{ $products->onFirstPage() ? 'disabled' : '' }}" href="{{ $products->url(1) }}" title="Trang đầu"><i class="fas fa-angle-double-left"></i></a>
                    <a class="page-btn {{ $products->onFirstPage() ? 'disabled' : '' }}" href="{{ $products->previousPageUrl() }}" title="Trang trước"><i class="fas fa-angle-left"></i></a>
                    @foreach($products->getUrlRange(max(1,$products->currentPage()-2), min($products->lastPage(),$products->currentPage()+2)) as $page => $url)
                        <a class="page-btn {{ $page == $products->currentPage() ? 'active' : '' }}" href="{{ $url }}">{{ $page }}</a>
                    @endforeach
                    <a class="page-btn {{ !$products->hasMorePages() ? 'disabled' : '' }}" href="{{ $products->nextPageUrl() }}" title="Trang sau"><i class="fas fa-angle-right"></i></a>
                    <a class="page-btn {{ !$products->hasMorePages() ? 'disabled' : '' }}" href="{{ $products->url($products->lastPage()) }}" title="Trang cuối"><i class="fas fa-angle-double-right"></i></a>
                </div>
                <div class="pagination-goto">
                    Đến trang
                    <input type="number" class="goto-input" id="gotoPage" value="{{ $products->currentPage() }}" min="1" max="{{ $products->lastPage() }}">
                </div>
                <div class="page-size">
                    <select class="per-page-select" onchange="changePerPage(this.value)">
                        @foreach([20,50,100] as $pp)
                            <option value="{{ $pp }}" {{ request('per_page', 20) == $pp ? 'selected' : '' }}>{{ $pp }} / trang</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- PRODUCT MODAL --}}
<div class="pm-overlay" id="productModal" onclick="handleOverlayClick(event)">
    <div class="pm-dialog">
        {{-- HEADER --}}
        <div class="pm-header">
            <h3 class="pm-title" id="modalTitle">Thêm sản phẩm mới</h3>
            <button class="pm-close" onclick="closeProductModal()"><i class="fas fa-times"></i></button>
        </div>

        {{-- BODY --}}
        <div class="pm-body">
            {{-- ROW 1: 3 khối --}}
            <div class="pm-row pm-row-3">
                {{-- KHỐI 1: Thông tin cơ bản --}}
                <div class="pm-block">
                    <div class="pm-block-title"><span class="pm-num">1</span> Thông tin cơ bản</div>
                    <div class="pm-field-grid">
                        <div class="pm-field">
                            <label>Mã hàng <span class="required">*</span></label>
                            <input type="text" id="f_ma_hang" placeholder="Tự động hoặc nhập tay" class="pm-input">
                        </div>
                        <div class="pm-field">
                            <label>Tên hàng <span class="required">*</span></label>
                            <input type="text" id="f_ten_hang" placeholder="Nhập tên hàng" class="pm-input">
                        </div>
                        <div class="pm-field">
                            <label>Nhóm hàng <span class="required">*</span></label>
                            <select id="f_category" class="pm-input">
                                <option value="">Chọn nhóm hàng</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="pm-field">
                            <label>Trạng thái</label>
                            <select id="f_trang_thai" class="pm-input">
                                <option value="Hoạt động">Hoạt động</option>
                                <option value="Ngừng hoạt động">Ngừng hoạt động</option>
                            </select>
                        </div>
                    </div>
                </div>

                {{-- KHỐI 2: Quy cách & ĐVT --}}
                <div class="pm-block">
                    <div class="pm-block-title"><span class="pm-num">2</span> Quy cách &amp; đơn vị tính</div>
                    <div class="pm-field-grid">
                        <div class="pm-field pm-field-full">
                            <label>Quy cách <span class="required">*</span></label>
                            <input type="text" id="f_quy_cach" placeholder="Nhập quy cách" class="pm-input">
                        </div>
                        <div class="pm-field pm-field-full">
                            <label>Đơn vị tính <span class="required">*</span></label>
                            <input type="text" id="f_dvt" placeholder="VD: Kg, Lít, Thùng" class="pm-input">
                        </div>
                    </div>
                </div>

                {{-- KHỐI 3: Giá & Thuế --}}
                <div class="pm-block">
                    <div class="pm-block-title"><span class="pm-num">3</span> Giá &amp; thuế</div>
                    <div class="pm-field-grid">
                        <div class="pm-field pm-field-full">
                            <label>Giá bán mặc định (VNĐ) <span class="required">*</span></label>
                            <input type="text" id="f_gia_ban" placeholder="Nhập giá bán" class="pm-input">
                        </div>
                        <div class="pm-field pm-field-full">
                            <label>Thuế VAT (%) <span class="required">*</span></label>
                            <select id="f_vat" class="pm-input">
                                <option value="0">0%</option>
                                <option value="5">5%</option>
                                <option value="8">8%</option>
                                <option value="10">10%</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ROW 2: 2 khối --}}
            <div class="pm-row pm-row-2">
                {{-- KHỐI 4: Nhà cung cấp --}}
                <div class="pm-block">
                    <div class="pm-block-title"><span class="pm-num">4</span> Nhà cung cấp</div>
                    <div class="pm-field-grid">
                        <div class="pm-field">
                            <label>Nhà cung cấp chính</label>
                            <input type="text" id="f_ncc" placeholder="Nhập tên nhà cung cấp" class="pm-input">
                        </div>
                        <div class="pm-field">
                            <label>Mã nhà cung cấp</label>
                            <input type="text" id="f_ma_ncc" placeholder="Nhập mã NCC (nếu có)" class="pm-input">
                        </div>
                    </div>
                </div>

                {{-- KHỐI 5: Ghi chú --}}
                <div class="pm-block">
                    <div class="pm-block-title"><span class="pm-num">5</span> Ghi chú</div>
                    <div class="pm-field pm-field-full" style="height: 100%;">
                        <label>Ghi chú</label>
                        <textarea id="f_ghi_chu" rows="4" placeholder="Nhập ghi chú (nếu có)..." class="pm-input pm-textarea"></textarea>
                    </div>
                </div>
            </div>
        </div>

        {{-- FOOTER --}}
        <div class="pm-footer">
            <button class="pm-btn-cancel" onclick="closeProductModal()">Hủy</button>
            <button class="pm-btn-save" id="modalSaveBtn"><i class="fas fa-save"></i> Lưu sản phẩm</button>
        </div>
    </div>
</div>

<style>

    /* ACTION BAR */
    .action-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 4px 0;
    }
    .action-left { display: flex; gap: 10px; }
    
    .btn-action {
        padding: 8px 16px;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 600;
        border: 1px solid #e2e8f0;
        color: #475569;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s;
    }
    .btn-import {
        background: #ecfdf5;
    }
    .btn-export {
        background: #F5F7FA;
    }
    .btn-action:hover {
        background: #f8fafc;
        border-color: #cbd5e1;
        color: #0070D2;
    }
    .btn-action i { color: #10b981; }
    .btn-export i { color: #3b82f6; }

    .btn-add-product {
        padding: 8px 18px;
        background: #0070D2;
        color: #fff;
        border: none;
        border-radius: 6px;
        font-weight: 600;
        font-size: 13px;
        cursor: pointer;
        box-shadow: 0 4px 12px rgba(0, 112, 210, 0.2);
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .btn-add-product:hover { background: #005bb5; transform: translateY(-1px); }

    /* FILTER */
    .filter-card {
        padding: 16px 0 4px;
        border-top: 1.5px solid #f1f5f9;
        border-bottom: 1.5px solid #f1f5f9;
    }
    .filter-unified-6 {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 16px;
        align-items: end;
    }
    .filter-item { display: flex; flex-direction: column; gap: 6px; }
    .filter-item label { font-size: 13px; font-weight: 700; color: #1e293b; }
    
    .filter-input {
        height: 36px;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        padding: 0 12px;
        font-size: 13px;
        outline: none;
        width: 100%;
        box-sizing: border-box;
    }
    .filter-input:focus { border-color: #0070D2; box-shadow: 0 0 0 3px rgba(0, 112, 210, 0.1); }

    .search-input-wrapper { position: relative; }
    .search-input-wrapper i {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
        font-size: 14px;
    }

    .btn-group-col {
        display: flex;
        flex-direction: row;
        gap: 8px;
    }
    .btn-search {
        flex: 1;
        height: 36px;
        background: #0070D2;
        color: #fff;
        border: none;
        border-radius: 6px;
        font-weight: 600;
        font-size: 13px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }
    .btn-clear {
        flex: 1;
        height: 36px;
        background: #fff;
        color: #ef4444;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        font-weight: 600;
        font-size: 13px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    /* TABLE */
    .table-card {
        border-top: 1.5px solid #f1f5f9;
        overflow: hidden;
    }
    .catalog-table {
        width: 100%;
        border-collapse: collapse;
    }
    .catalog-table thead th {
        background: #EFF6FF;
        padding: 10px 8px;
        font-size: 13px;
        font-weight: 700;
        color: black;
        text-align: center;
        border: 1px solid #e2e8f0;
        white-space: nowrap;
        text-transform: uppercase;
    }
    .catalog-table tbody td {
        padding: 9px 8px;
        font-size: 13px;
        color: #1e293b;
        text-align: center;
        border: 1px solid #e2e8f0;
        vertical-align: middle;
    }
    .catalog-table tbody tr:hover { background: #f0f7ff; }

    .text-left { text-align: left !important; }
    .text-right { text-align: right !important; }
    .text-bold { font-weight: 700; color: #0070D2; }
    .text-muted { color: #64748b; font-size: 13px; }

    /* BADGE */
    .status-badge {
        padding: 4px 12px;
        border-radius: 4px;
        font-size: 11px;
        font-weight: 700;
        display: inline-block;
        min-width: 90px;
    }
    .status-badge.active { background: #ecfdf5; color: #10b981; }
    .status-badge.inactive { background: #fef2f2; color: #ef4444; }

    /* ACTION BUTTONS */
    .action-buttons { display: flex; justify-content: center; gap: 6px; }
    .action-buttons button {
        width: 28px;
        height: 28px;
        border-radius: 4px;
        border: 1px solid #e2e8f0;
        background: #fff;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        transition: all 0.2s;
    }
    .btn-edit { color: #3b82f6; }
    .btn-edit:hover { background: #3b82f6; color: #fff; border-color: #3b82f6; }
    .btn-delete { color: #ef4444; }
    .btn-delete:hover { background: #ef4444; color: #fff; border-color: #ef4444; }

    /* PAGINATION BAR */
    .pagination-bar {
        padding: 12px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-top: 1px solid #f1f5f9;
        font-size: 13px;
    }
    .pagination-info { font-size: 12px; color: #64748b; }
    .pagination-right { display: flex; gap: 12px; align-items: center; }
    .pagination-controls { display: flex; gap: 4px; align-items: center; }
    
    .page-btn {
        width: 28px;
        height: 28px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px solid #e2e8f0;
        background: #fff;
        border-radius: 5px;
        font-size: 12px;
        font-weight: 600;
        color: #475569;
        cursor: pointer;
    }
    .page-btn.active {
        background: #0070D2;
        color: #fff;
        border-color: #0070D2;
    }
    .page-btn:disabled { opacity: 0.4; cursor: default; }
    .page-btn:hover:not(.active):not(:disabled) { background: #f1f5f9; }

    .pagination-goto {
        display: flex;
        align-items: center;
        gap: 6px;
        color: #64748b;
    }
    .goto-input {
        width: 40px;
        height: 28px;
        border: 1px solid #cbd5e1;
        border-radius: 4px;
        text-align: center;
        font-size: 12px;
        outline: none;
    }
    .page-size select {
        height: 28px;
        border: 1px solid #cbd5e1;
        border-radius: 4px;
        padding: 0 8px;
        font-size: 12px;
        color: #475569;
        outline: none;
    }

    /* ========== PRODUCT MODAL ========== */
    .pm-overlay {
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.45);
        z-index: 1050;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 20px;
        backdrop-filter: blur(2px);
    }
    .pm-overlay.open { display: flex; }

    .pm-dialog {
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 20px 60px rgba(0,0,0,0.18);
        width: 100%;
        max-width: 1240px;
        min-width: 900px;
        display: flex;
        flex-direction: column;
        max-height: 90vh;
        animation: pmSlideIn 0.2s ease;
    }
    @keyframes pmSlideIn {
        from { opacity: 0; transform: translateY(-20px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    /* Header */
    .pm-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 18px 24px 14px;
        border-bottom: 1px solid #f1f5f9;
    }
    .pm-title {
        font-size: 17px;
        font-weight: 700;
        color: #1e293b;
        margin: 0;
    }
    .pm-close {
        width: 30px; height: 30px;
        border-radius: 50%;
        border: 1px solid #e2e8f0;
        background: #f8fafc;
        color: #64748b;
        cursor: pointer;
        display: flex; align-items: center; justify-content: center;
        font-size: 13px;
        transition: all 0.2s;
    }
    .pm-close:hover { background: #ef4444; color: #fff; border-color: #ef4444; }

    /* Body */
    .pm-body {
        padding: 20px 24px;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .pm-row {
        display: grid;
        gap: 14px;
    }
    .pm-row-3 { grid-template-columns: 2fr 1fr 1.2fr; }
    .pm-row-2 { grid-template-columns: 1fr 1.2fr; }

    .pm-block {
        border: 1px solid #e8edf5;
        border-radius: 10px;
        padding: 14px 16px;
        background: #fafbfd;
    }
    .pm-block-title {
        font-size: 13px;
        font-weight: 700;
        color: #0070D2;
        margin-bottom: 12px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .pm-num {
        width: 20px; height: 20px;
        background: #0070D2;
        color: #fff;
        border-radius: 50%;
        font-size: 11px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .pm-field-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
    }
    .pm-field {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }
    .pm-field-full { grid-column: 1 / -1; }
    .pm-field label {
        font-size: 12px;
        font-weight: 600;
        color: #475569;
    }
    .required { color: #ef4444; }
    .pm-input {
        height: 36px;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        padding: 0 12px;
        font-size: 13px;
        color: #1e293b;
        outline: none;
        width: 100%;
        box-sizing: border-box;
        background: #fff;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .pm-input:focus {
        border-color: #0070D2;
        box-shadow: 0 0 0 3px rgba(0, 112, 210, 0.1);
    }
    .pm-textarea {
        height: auto !important;
        min-height: 90px;
        padding: 10px 12px;
        resize: vertical;
        line-height: 1.5;
    }

    /* Footer */
    .pm-footer {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        padding: 14px 24px 18px;
        border-top: 1px solid #f1f5f9;
    }
    .pm-btn-cancel {
        padding: 8px 22px;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        background: #fff;
        color: #475569;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
    }
    .pm-btn-cancel:hover { background: #f8fafc; }
    .pm-btn-save {
        padding: 8px 26px;
        border: none;
        border-radius: 6px;
        background: #0070D2;
        color: #fff;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
        box-shadow: 0 4px 12px rgba(0,112,210,0.25);
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .pm-btn-save:hover { background: #005bb5; }

    /* Responsive for smaller screens */
    @media (max-width: 900px) {
        .pm-row-3 { grid-template-columns: 1fr; }
        .pm-row-2 { grid-template-columns: 1fr; }
        .pm-dialog { min-width: unset; }
    }
</style>

@push('scripts')

{{-- IMPORT MODAL --}}
<div class="pm-overlay" id="importModal" onclick="if(event.target===this) closeImportModal()">
    <div class="pm-dialog" style="max-width:500px; min-width:400px;">
        <div class="pm-header">
            <h3 class="pm-title">Import danh sách sản phẩm</h3>
            <button class="pm-close" onclick="closeImportModal()"><i class="fas fa-times"></i></button>
        </div>
        <div class="pm-body" style="gap:14px;">
            <div style="background:#eff6ff; border:1px solid #bfdbfe; border-radius:8px; padding:12px 14px; font-size:13px; color:#1d4ed8;">
                <i class="fas fa-info-circle" style="margin-right:6px;"></i>
                File Excel phải có đúng định dạng template.
                <a href="{{ route('catalog.template') }}" style="font-weight:700; margin-left:4px;">Tải template mẫu</a>
            </div>
            <div class="pm-field pm-field-full">
                <label>Chọn file Excel / CSV</label>
                <input type="file" id="importFile" accept=".xlsx,.xls,.csv" class="pm-input" style="padding:6px 12px; height:auto;">
            </div>
            <div id="importStatus" style="display:none; font-size:13px;"></div>
        </div>
        <div class="pm-footer">
            <button class="pm-btn-cancel" onclick="closeImportModal()">Hủy</button>
            <button class="pm-btn-save" onclick="submitImport()"><i class="fas fa-upload"></i> Nhập dữ liệu</button>
        </div>
    </div>
</div>

<script>
    const CSRF = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const STORE_URL  = "{{ route('catalog.store') }}";
    const UPDATE_URL = "/san-pham/"; // + id
    const DELETE_URL = "/san-pham/"; // + id
    const IMPORT_URL = "{{ route('catalog.import') }}";

    let editingId = null;

    // ─────── UTILITY ───────────────────────────────────────────────
    function showToast(msg, type = 'success') {
        const color = type === 'success' ? '#10b981' : '#ef4444';
        const toast = document.createElement('div');
        toast.style.cssText = `
            position:fixed; bottom:24px; right:24px; z-index:9999;
            background:${color}; color:#fff; padding:12px 20px;
            border-radius:8px; font-size:13px; font-weight:600;
            box-shadow:0 4px 16px rgba(0,0,0,.15); display:flex; align-items:center; gap:8px;
            animation: fadeinup .3s ease;
        `;
        toast.innerHTML = `<i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i> ${msg}`;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 3500);
    }

    function ajaxJson(url, method, data) {
        return fetch(url, {
            method,
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            body: JSON.stringify(data),
        }).then(r => r.json());
    }

    // ─────── PRODUCT MODAL (ADD/EDIT) ──────────────────────────────
    function openProductModal() {
        editingId = null;
        document.getElementById('modalTitle').textContent = 'Thêm sản phẩm mới';
        document.getElementById('modalSaveBtn').innerHTML = '<i class="fas fa-save"></i> Lưu sản phẩm';
        document.getElementById('f_ma_hang').removeAttribute('readonly');
        clearProductForm();
        document.getElementById('productModal').classList.add('open');
        document.body.style.overflow = 'hidden';
    }

    function openEditModal(btn) {
        const tr = btn.closest('tr');
        editingId = tr.dataset.id;
        document.getElementById('modalTitle').textContent = 'Chỉnh sửa sản phẩm';
        document.getElementById('modalSaveBtn').innerHTML = '<i class="fas fa-save"></i> Cập nhật';
        document.getElementById('f_ma_hang').value = tr.dataset.ma;
        document.getElementById('f_ma_hang').setAttribute('readonly', true);
        document.getElementById('f_ten_hang').value = tr.dataset.ten;
        document.getElementById('f_category').value = tr.dataset.cat || '';
        document.getElementById('f_quy_cach').value = tr.dataset.quyCach || '';
        document.getElementById('f_dvt').value = tr.dataset.dvt || '';
        document.getElementById('f_gia_ban').value = tr.dataset.gia || '';
        document.getElementById('f_vat').value = tr.dataset.vat || '10';
        document.getElementById('f_ncc').value = tr.dataset.ncc || '';
        document.getElementById('f_ma_ncc').value = tr.dataset.maNcc || '';
        document.getElementById('f_trang_thai').value = tr.dataset.status || 'Hoạt động';
        document.getElementById('f_ghi_chu').value = tr.dataset.note || '';
        document.getElementById('productModal').classList.add('open');
        document.body.style.overflow = 'hidden';
    }

    function closeProductModal() {
        document.getElementById('productModal').classList.remove('open');
        document.body.style.overflow = '';
    }

    function handleOverlayClick(e) {
        if (e.target === e.currentTarget) closeProductModal();
    }

    function clearProductForm() {
        ['f_ma_hang','f_ten_hang','f_quy_cach','f_dvt','f_gia_ban','f_ncc','f_ma_ncc','f_ghi_chu'].forEach(id => {
            const el = document.getElementById(id);
            if (el) el.value = '';
        });
        const cat = document.getElementById('f_category');
        if (cat) cat.value = '';
        document.getElementById('f_trang_thai').value = 'Hoạt động';
        document.getElementById('f_vat').value = '10';
    }

    // SAVE (store or update)
    document.getElementById('modalSaveBtn').addEventListener('click', async () => {
        const btn = document.getElementById('modalSaveBtn');
        const payload = {
            ma_hang:      document.getElementById('f_ma_hang').value.trim(),
            ten_hang:     document.getElementById('f_ten_hang').value.trim(),
            category_id:  document.getElementById('f_category').value || null,
            quy_cach:     document.getElementById('f_quy_cach').value.trim(),
            don_vi_tinh:  document.getElementById('f_dvt').value.trim(),
            gia_ban:      document.getElementById('f_gia_ban').value || null,
            vat:          document.getElementById('f_vat').value || 10,
            nha_cung_cap: document.getElementById('f_ncc').value.trim(),
            ma_ncc:       document.getElementById('f_ma_ncc').value.trim(),
            trang_thai:   document.getElementById('f_trang_thai').value,
            ghi_chu:      document.getElementById('f_ghi_chu').value.trim(),
        };

        if (!payload.ten_hang) { showToast('Vui lòng nhập Tên hàng!', 'error'); return; }

        btn.disabled = true;
        try {
            let res;
            if (editingId) {
                res = await ajaxJson(UPDATE_URL + editingId, 'PUT', payload);
            } else {
                res = await ajaxJson(STORE_URL, 'POST', payload);
            }

            if (res.success) {
                showToast(res.message);
                closeProductModal();
                setTimeout(() => location.reload(), 800);
            } else {
                const errs = res.errors ? Object.values(res.errors).flat().join('\n') : res.message;
                showToast(errs, 'error');
            }
        } catch(e) {
            showToast('Đã có lỗi xảy ra!', 'error');
        } finally {
            btn.disabled = false;
        }
    });

    // DELETE
    async function deleteProduct(btn, id) {
        if (!confirm('Bạn có chắc muốn xóa sản phẩm này không?')) return;
        try {
            const res = await ajaxJson(DELETE_URL + id, 'DELETE', {});
            if (res.success) {
                showToast(res.message);
                btn.closest('tr').remove();
            } else {
                showToast(res.message, 'error');
            }
        } catch(e) {
            showToast('Đã có lỗi xảy ra!', 'error');
        }
    }

    // ─────── IMPORT MODAL ──────────────────────────────────────────
    function openImportModal() {
        document.getElementById('importFile').value = '';
        document.getElementById('importStatus').style.display = 'none';
        document.getElementById('importModal').classList.add('open');
        document.body.style.overflow = 'hidden';
    }

    function closeImportModal() {
        document.getElementById('importModal').classList.remove('open');
        document.body.style.overflow = '';
    }

    async function submitImport() {
        const file = document.getElementById('importFile').files[0];
        if (!file) { showToast('Vui lòng chọn file!', 'error'); return; }

        const status = document.getElementById('importStatus');
        status.style.display = 'block';
        status.style.color = '#0070D2';
        status.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang nhập dữ liệu...';

        const fd = new FormData();
        fd.append('file', file);
        fd.append('_token', CSRF);

        try {
            const res = await fetch(IMPORT_URL, { method: 'POST', body: fd }).then(r => r.json());
            if (res.success) {
                status.style.color = '#10b981';
                status.innerHTML = '<i class="fas fa-check-circle"></i> ' + res.message;
                setTimeout(() => { closeImportModal(); location.reload(); }, 1200);
            } else {
                status.style.color = '#ef4444';
                status.innerHTML = '<i class="fas fa-exclamation-circle"></i> ' + res.message;
            }
        } catch(e) {
            status.style.color = '#ef4444';
            status.innerHTML = '<i class="fas fa-exclamation-circle"></i> Có lỗi xảy ra!';
        }
    }

    // ─────── PAGINATION ────────────────────────────────────────────
    function changePerPage(val) {
        const url = new URL(window.location);
        url.searchParams.set('per_page', val);
        url.searchParams.set('page', 1);
        window.location = url.toString();
    }

    document.getElementById('gotoPage')?.addEventListener('change', function() {
        const url = new URL(window.location);
        url.searchParams.set('page', this.value);
        window.location = url.toString();
    });

    // Close on Escape
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') { closeProductModal(); closeImportModal(); }
    });
</script>

<style>
    .page-btn.disabled { opacity:.4; pointer-events:none; }
    .btn-action.btn-export { text-decoration:none; }
    .btn-clear { text-decoration:none; }
</style>
@endpush
@endsection

