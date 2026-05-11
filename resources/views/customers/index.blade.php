@extends('layouts.app')
@section('title', 'Đối Tác - Khách Hàng')
@section('page-title', 'Đối Tác - Khách Hàng')
@section('page-subtitle', 'Theo dõi, tìm kiếm và quản lý danh bạ khách hàng của bạn.')

@section('content')
<div class="card" style="padding: 24px; display: flex; flex-direction: column; gap: 10px;">

    {{-- PAGE HEADER --}}
    <div class="page-header-row" style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 20px; border-bottom: 2.5px solid #cbd5e1; margin-bottom: 0;">
        <div style="display: flex; align-items: center; gap: 16px;">
            <div style="width: 56px; height: 56px; background: #eef2ff; border-radius: 16px; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 10px rgba(79, 70, 229, 0.15); flex-shrink: 0;">
                <i class="fas fa-address-book" style="font-size: 24px; color: #002B6B;"></i>
            </div>
            <div>
                <h2 style="font-size: 20px; font-weight: 800; color: #0f172a; margin: 0 0 4px 0; letter-spacing: -0.5px;text-transform: uppercase;">Khách Hàng & Đối Tác</h2>
                <p style="margin: 0; color: #64748b; font-size: 13px;text-transform: uppercase;text-transform: uppercase;">Quản lý thông tin liên hệ và mã số thuế đối tác.</p>
            </div>
        </div>
        <button type="button" onclick="openCreateKHModal()" class="btn-add-kh">
            <i class="fas fa-plus"></i> Thêm khách hàng
        </button>
    </div>

    {{-- BỘ LỌC --}}
    <div class="kh-filter-card">
        <form method="GET" id="search-form-new">
            {{-- Row 1: Ngày + Search + Khu vực + Tình trạng + Nút --}}
            <div class="kh-filter-grid">
                <div class="kh-filter-item">
                    <label>Từ ngày</label>
                    <input type="date" name="date_start" class="kh-filter-input" value="{{ request('date_start') }}">
                </div>
                <div class="kh-filter-item">
                    <label>Đến ngày</label>
                    <input type="date" name="date_end" class="kh-filter-input" value="{{ request('date_end') }}">
                </div>
                <div class="kh-filter-item" style="flex: 2;">
                    <label>Tìm kiếm</label>
                    <div class="kh-search-wrapper">
                        <input type="text" name="search" class="kh-filter-input" placeholder="Nhập tên, SĐT, MST..." value="{{ request('search') }}">
                        <i class="fas fa-search"></i>
                    </div>
                </div>
                <div class="kh-filter-item">
                    <label>Khu vực</label>
                    <select name="khu_vuc" class="kh-filter-input">
                        <option value="">Tất cả</option>
                        <option value="Miền Bắc" {{ request('khu_vuc')=='Miền Bắc' ? 'selected' : '' }}>Miền Bắc</option>
                        <option value="Miền Trung" {{ request('khu_vuc')=='Miền Trung' ? 'selected' : '' }}>Miền Trung</option>
                        <option value="Miền Nam" {{ request('khu_vuc')=='Miền Nam' ? 'selected' : '' }}>Miền Nam</option>
                    </select>
                </div>
                <div class="kh-filter-item">
                    <label>Tình trạng</label>
                    <select name="tinh_trang" class="kh-filter-input">
                        <option value="">Tất cả</option>
                        <option value="active" {{ request('tinh_trang')=='active' ? 'selected' : '' }}>Đang hoạt động</option>
                        <option value="unactive" {{ request('tinh_trang')=='unactive' ? 'selected' : '' }}>Ngưng giao dịch</option>
                    </select>
                </div>
                <div class="kh-filter-item kh-filter-btn-group">
                    <label>&nbsp;</label>
                    <div style="display: flex; gap: 8px;">
                        <button type="submit" class="kh-btn-search"><i class="fas fa-search"></i> Lọc</button>
                        <a href="{{ route('customers.index') }}" class="kh-btn-clear"><i class="fas fa-times"></i> Xóa lọc</a>
                    </div>
                </div>
            </div>

            {{-- Row 2: Import / Export Excel --}}
            <div class="kh-action-row">
                <button type="button" onclick="document.getElementById('kh-import-input').click()" class="kh-btn-excel import">
                    <i class="fas fa-file-import"></i> Nhập Excel
                </button>
                <button type="button" onclick="exportKHExcel()" class="kh-btn-excel export">
                    <i class="fas fa-file-export"></i> Xuất Excel
                </button>
            </div>
        </form>
    </div>
    <input type="file" id="kh-import-input" style="display:none" onchange="importKHExcel(this)">


    <div class="kh-table-card">
        <div class="table-responsive">
            <table class="kh-table">
                <thead>
                    <tr>
                        <th width="3%"><input type="checkbox" id="check-all-kh" onclick="toggleCheckAll(this)"></th>
                        <th width="5%">STT</th>
                        <th width="8%">Ngày tạo</th>
                        <th width="8%">Mã Khách Hàng</th>
                        <th width="26%">Thông tin khách hàng</th>
                        <th width="10%">Mã Số Thuế</th>
                        <th width="10%">Khu vực</th>
                        <th width="9%">P.I.C</th>
                        <th width="11%">Tình trạng</th>
                        <th width="8%">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($customers as $idx => $kh)
                    <tr style="cursor: pointer;" onclick="if(!event.target.closest('button') && !event.target.closest('input') && !event.target.closest('a')) { window.location.href='{{ route('customers.show', $kh->id) }}'; }">
                        <td><input type="checkbox" class="check-kh" value="{{ $kh->id }}"></td>
                        <td>{{ $customers->firstItem() + $idx }}</td>
                        <td>{{ $kh->created_date ? \Carbon\Carbon::parse($kh->created_date)->format('d/m/Y') : $kh->created_at->format('d/m/Y') }}</td>
                        <td class="text-bold">{{ $kh->ma_kh }}</td>
                        <td class="text-left">
                            <div style="font-weight: 700; color: #0f172a; margin-bottom: 3px; font-size: 13px; text-transform: uppercase;">{{ $kh->ten_cty }}</div>
                            <div style="font-size: 11px; color: #64748b; line-height: 1.6;">
                                <div><i class="fas fa-phone-alt" style="margin-right: 5px; width: 11px; transform: scaleX(-1); display: inline-block;"></i>{{ $kh->sdt }}</div>
                                <div title="{{ $kh->dia_chi }}"><i class="fas fa-map-marker-alt" style="margin-right: 5px; width: 11px;"></i>{{ Str::limit($kh->dia_chi, 500) }}</div>
                            </div>
                        </td>
                        <td>{{ $kh->ma_so_thue }}</td>
                        <td>
                            <span class="badge-region {{ Str::slug($kh->khu_vuc) }}">{{ $kh->khu_vuc }}</span>
                        </td>
                        <td>
                            <span style="font-size: 12px; font-weight: 600; color: #475569;">{{ $kh->creator->display_name ?? 'System' }}</span>
                        </td>
                        <td>
                            @if(($kh->tinh_trang ?? 'active') == 'active')
                                <span class="badge-status-kh active">Đang hoạt động</span>
                            @else
                                <span class="badge-status-kh unactive">Ngưng giao dịch</span>
                            @endif
                        </td>
                        <td>
                            <div class="kh-action-buttons">
                                <button onclick="editKH({{ $kh->id }})" class="btn-edit" title="Sửa"><i class="fas fa-edit"></i></button>
                                <button onclick="deleteKH({{ $kh->id }}, '{{ $kh->ten_cty }}')" class="btn-delete" title="Xóa"><i class="fas fa-trash-alt"></i></button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="10" style="text-align:center; padding:40px; color:#94a3b8;"><i class="fas fa-users fa-2x" style="margin-bottom:10px; display:block;"></i>Chưa có khách hàng nào</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 25px;">
        <div style="color: #64748b; font-size: 14px;">Đang hiển thị {{ $customers->firstItem() ?? 0 }} - {{ $customers->lastItem() ?? 0 }} trong tổng số {{ $customers->total() }} khách hàng</div>
        <div style="display: flex; align-items: center; gap: 20px;">
            <div style="display: flex; align-items: center; gap: 8px; color: #64748b; font-size: 13px;">
                <span>Hiển thị:</span>
                <select onchange="window.location.href=this.value" style="border: none; outline: none; background: transparent; font-weight: 700; cursor: pointer; color: #0f172a; font-size: 14px;">
                    @foreach([5, 10, 15, 20, 50] as $size)
                        <option value="{{ request()->fullUrlWithQuery(['limit' => $size]) }}" {{ request('limit', 20) == $size ? 'selected' : '' }}>{{ $size }}</option>
                    @endforeach
                </select>
                <span>mục</span>
            </div>
            <div>{{ $customers->appends(request()->all())->links('pagination::bootstrap-4') }}</div>
        </div>
    </div>
</div>

{{-- MODAL KH --}}
<div id="modal-kh" class="modal-overlay">
    <div class="modal-box" style="width: 750px; max-width: 95vw;">
        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #f1f5f9; padding-bottom: 15px; margin-bottom: 20px;">
            <h3 id="modal-kh-title" style="font-weight: 800; font-size: 20px;"><i class="fas fa-user-plus" style="color:#002B6B"></i> Thêm Khách Hàng</h3>
            <i class="fas fa-times" style="cursor: pointer; color: #64748b;" onclick="closeModal('modal-kh')"></i>
        </div>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
            <div style="grid-column: span 2;">
                <label class="modal-pro-label">Tên khách hàng / Công ty <span style="color:#ef4444">*</span></label>
                <input type="text" id="kh_ten" class="modal-pro-input" placeholder="Nhập tên chính xác...">
            </div>
            <div>
                <label class="modal-pro-label">Ngày tạo (Dùng làm Mã KH) <span style="color:#ef4444">*</span></label>
                <input type="date" id="kh_created_date" class="modal-pro-input">
            </div>
            <div>
                <label class="modal-pro-label">Mã số thuế <span style="color:#ef4444">*</span></label>
                <input type="text" id="kh_mst" class="modal-pro-input" placeholder="Nhập MST..." oninput="this.value = this.value.replace(/[^0-9]/g, '')">
            </div>
            <div>
                <label class="modal-pro-label">Khu vực</label>
                <select id="kh_khuvuc" class="modal-pro-input">
                    <option value="">Chọn khu vực</option>
                    <option value="Miền Bắc">Miền Bắc</option>
                    <option value="Miền Trung">Miền Trung</option>
                    <option value="Miền Nam">Miền Nam</option>
                </select>
            </div>
            <div>
                <label class="modal-pro-label">Số điện thoại</label>
                <input type="text" id="kh_sdt" class="modal-pro-input" placeholder="SĐT chính...">
            </div>
            <div>
                <label class="modal-pro-label">Email</label>
                <input type="email" id="kh_email" class="modal-pro-input" placeholder="example@gmail.com">
            </div>
            <div>
                <label class="modal-pro-label">Tài liệu đối tác</label>
                <input type="file" id="kh_tai_lieu" class="modal-pro-input" style="padding: 8px;">
                <div id="kh_tai_lieu_preview" style="margin-top: 5px; font-size: 11px; color: #002B6B;"></div>
            </div>
            <div style="grid-column: span 2;">
                <label class="modal-pro-label">Địa chỉ công ty (Xuất hóa đơn)</label>
                <input type="text" id="kh_diachi" class="modal-pro-input" placeholder="Địa chỉ đăng ký...">
            </div>
            <div>
                <label class="modal-pro-label">Người liên hệ</label>
                <input type="text" id="kh_nguoilienhe" class="modal-pro-input" placeholder="Tên đại diện...">
            </div>
            <div>
                <label class="modal-pro-label">SĐT nhận hàng</label>
                <input type="text" id="kh_sdtnhan" class="modal-pro-input" placeholder="SĐT kho/nhận...">
            </div>
            <div style="grid-column: span 2;">
                <label class="modal-pro-label">Địa chỉ nhận hàng</label>
                <input type="text" id="kh_diachinhan" class="modal-pro-input" placeholder="Địa chỉ giao hàng...">
            </div>
            <div style="grid-column: span 2; display: flex; gap: 15px;">
                <div style="width: 70%;">
                    <label class="modal-pro-label">Ghi chú thêm</label>
                    <input type="text" id="kh_ghichu" class="modal-pro-input" placeholder="Ghi chú nội bộ...">
                </div>
                <div style="width: 30%;">
                    <label class="modal-pro-label">Tình trạng</label>
                    <select id="kh_tinhtrang" class="modal-pro-input">
                        <option value="active">Đang hoạt động</option>
                        <option value="unactive">Ngưng giao dịch</option>
                    </select>
                </div>
            </div>
        </div>
        <div style="display: flex; justify-content: flex-end; gap: 12px; border-top: 1px solid #f1f5f9; padding-top: 20px; margin-top: 20px;">
            <button class="ui-btn ui-btn-outline" style="border-radius: 6px;" onclick="closeModal('modal-kh')">Hủy bỏ</button>
            <button class="ui-btn ui-btn-primary" style="border-radius: 6px; background: #002B6B;" onclick="submitKH()"><i class="fas fa-save"></i> Lưu dữ liệu</button>
        </div>
    </div>
</div>

<style>
    .modal-pro-label { font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 6px; display: block; }
    .modal-pro-input { width: 100%; border: 1px solid #cbd5e1; border-radius: 6px; padding: 8px 12px; font-size: 13px; outline: none; box-sizing: border-box; transition: border-color 0.2s, box-shadow 0.2s; height: 36px; }
    textarea.modal-pro-input { height: auto; }
    select.modal-pro-input { height: 36px; }
    input[type="file"].modal-pro-input { height: auto; padding: 6px 12px; }
    .modal-pro-input:focus { border-color: #002B6B; background: #fff; box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1); }

    /* ===== BUTTON ADD ===== */
    .btn-add-kh {
        padding: 8px 18px;
        background: #002B6B;
        color: #fff;
        border: none;
        border-radius: 6px;
        font-weight: 600;
        font-size: 13px;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 8px;
        box-shadow: 0 4px 12px rgba(0, 112, 210, 0.2);
        transition: all 0.2s;
        white-space: nowrap;
    }
    .btn-add-kh:hover { background: #005bb5; transform: translateY(-1px); }

    /* ===== KH FILTER (outbound_report style) ===== */
    .kh-filter-card {
        padding: 16px 0 4px;
        border-top: 1.5px solid #f1f5f9;
        border-bottom: 1.5px solid #f1f5f9;
        display: flex;
        flex-direction: column;
        gap: 12px;
    }
    .kh-filter-grid {
        display: flex;
        gap: 12px;
        align-items: flex-end;
        flex-wrap: wrap;
    }
    .kh-filter-item {
        display: flex;
        flex-direction: column;
        gap: 5px;
        flex: 1;
        min-width: 120px;
    }
    .kh-filter-item label {
        font-size: 13px;
        font-weight: 700;
        color: #1e293b;
    }
    .kh-filter-input {
        height: 36px;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        padding: 0 12px;
        font-size: 13px;
        outline: none;
        width: 100%;
        box-sizing: border-box;
        color: #1e293b;
        background: #fff;
    }
    .kh-filter-input:focus { border-color: #002B6B; box-shadow: 0 0 0 3px rgba(0, 112, 210, 0.1); }
    .kh-search-wrapper { position: relative; }
    .kh-search-wrapper i {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
        font-size: 14px;
    }
    .kh-search-wrapper .kh-filter-input { padding-right: 36px; }
    .kh-filter-btn-group { flex: none; min-width: unset; }
    .kh-btn-search {
        height: 36px;
        padding: 0 16px;
        background: #002B6B;
        color: #fff;
        border: none;
        border-radius: 6px;
        font-weight: 600;
        font-size: 13px;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 6px;
        white-space: nowrap;
    }
    .kh-btn-search:hover { background: #005bb5; }
    .kh-btn-clear {
        height: 36px;
        padding: 0 16px;
        background: #fff;
        color: #ef4444;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        font-weight: 600;
        font-size: 13px;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 6px;
        text-decoration: none;
        white-space: nowrap;
    }
    .kh-btn-clear:hover { background: #fef2f2; color: #ef4444; border-color: #ef4444; }
    .kh-action-row {
        display: flex;
        gap: 10px;
        padding-top: 15px;
        float:right;
    }
    .kh-btn-excel {
        padding: 7px 14px;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 600;
        border: 1px solid #e2e8f0;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 7px;
        transition: all 0.2s;
        color: #475569;
    }
    .kh-btn-excel.export {
        background: #F5F7FA;
    }
    .kh-btn-excel.import {
        background: #ecfdf5;
    }
    .kh-btn-excel i { font-size: 13px; }
    .kh-btn-excel.import i { color: #10b981; }
    .kh-btn-excel.export i { color: #3b82f6; }
    .kh-btn-excel:hover { background: #f8fafc; border-color: #cbd5e1; }

    /* ===== KH TABLE (catalog style) ===== */
    .kh-table-card {
        border-top: 1.5px solid #f1f5f9;
        overflow: hidden;
    }
    .kh-table {
        width: 100%;
        border-collapse: collapse;
    }
    .kh-table thead th {
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
    .kh-table tbody td {
        padding: 9px 8px;
        font-size: 13px;
        color: #1e293b;
        text-align: center;
        border: 1px solid #e2e8f0;
        vertical-align: middle;
    }
    .kh-table tbody tr:hover { background: #f0f7ff; }
    .kh-table .text-left { text-align: left !important; }
    .kh-table .text-bold { font-weight: 700; color: #002B6B; }

    /* Action buttons (catalog style) */
    .kh-action-buttons { display: flex; justify-content: center; gap: 6px; }
    .kh-action-buttons .btn-edit,
    .kh-action-buttons .btn-delete {
        width: 28px;
        height: 28px;
        border-radius: 4px;
        border: 1px solid #e2e8f0;
        background: #fff;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        transition: all 0.2s;
    }
    .kh-action-buttons .btn-edit { color: #3b82f6; }
    .kh-action-buttons .btn-edit:hover { background: #3b82f6; color: #fff; border-color: #3b82f6; }
    .kh-action-buttons .btn-delete { color: #ef4444; }
    .kh-action-buttons .btn-delete:hover { background: #ef4444; color: #fff; border-color: #ef4444; }

    /* Badges */
    .badge-region { padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; white-space: nowrap; display: inline-block; text-align: center; min-width: 75px; }
    .badge-region.mien-bac { color: #3b82f6; background: #eff6ff; }
    .badge-region.mien-trung { color: #f97316; background: #fff7ed; }
    .badge-region.mien-nam { color: #10b981; background: #ecfdf5; }

    .badge-status-kh { padding: 4px 12px; border-radius: 4px; color: #fff; font-size: 11px; font-weight: 700; white-space: nowrap; display: inline-block; min-width: 100px; text-align: center; }
    .badge-status-kh.active { background: #ecfdf5; color: #10b981; }
    .badge-status-kh.unactive { background: #fef2f2; color: #ef4444; }

    /* Checkbox */
    .check-kh, #check-all-kh { width: 15px; height: 15px; cursor: pointer; vertical-align: middle; }

    /* Filter row */
    .filter-row { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; }
    .filter-group { display: flex; gap: 8px; align-items: center; flex-wrap: wrap; }

    @media (max-width: 1200px) {
        .filter-row { flex-direction: column; align-items: stretch; }
        .filter-group { justify-content: flex-start; }
        #search-form-new { gap: 20px; }
        .kh-table-card { overflow-x: auto; }
        .kh-table { min-width: 1100px; }
    }

    @media (max-width: 768px) {
        .card { padding: 15px !important; }
        .page-header-row { flex-direction: column; align-items: flex-start !important; gap: 15px; }
        .date-range-group { width: 100%; display: flex; }
        .date-range-group input { flex: 1; min-width: 0; }
    }
</style>
@endsection

@push('scripts')
<script>
    let currentEditId = null;

    function openCreateKHModal() {
        currentEditId = null;
        document.getElementById('modal-kh-title').innerHTML = '<i class="fas fa-user-plus" style="color:#002B6B"></i> Thêm Khách Hàng';
        
        // Reset fields
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('kh_created_date').value = today;
        document.getElementById('kh_created_date').disabled = false; // Luôn cho phép khi thêm mới

        ['kh_ten','kh_mst','kh_khuvuc','kh_sdt','kh_email','kh_diachi','kh_nguoilienhe','kh_sdtnhan','kh_diachinhan','kh_ghichu','kh_tai_lieu'].forEach(id => {
            const el = document.getElementById(id);
            if (el) el.value = '';
        });
        document.getElementById('kh_tinhtrang').value = 'active';
        document.getElementById('kh_tai_lieu_preview').innerHTML = '';
        
        openModal('modal-kh');
    }

    async function submitKH() {
        const formData = new FormData();
        formData.append('ten_cty', document.getElementById('kh_ten').value);
        formData.append('ma_so_thue', document.getElementById('kh_mst').value);
        formData.append('khu_vuc', document.getElementById('kh_khuvuc').value);
        formData.append('sdt', document.getElementById('kh_sdt').value);
        formData.append('email', document.getElementById('kh_email').value);
        formData.append('dia_chi', document.getElementById('kh_diachi').value);
        formData.append('nguoi_lien_he', document.getElementById('kh_nguoilienhe').value);
        formData.append('sdt_nhan', document.getElementById('kh_sdtnhan').value);
        formData.append('dia_chi_nhan', document.getElementById('kh_diachinhan').value);
        formData.append('ghi_chu', document.getElementById('kh_ghichu').value);
        formData.append('tinh_trang', document.getElementById('kh_tinhtrang').value);
        formData.append('created_date', document.getElementById('kh_created_date').value);
        
        const fileInput = document.getElementById('kh_tai_lieu');
        if (fileInput.files.length > 0) {
            formData.append('tai_lieu_file', fileInput.files[0]);
        }

        if (!document.getElementById('kh_ten').value || !document.getElementById('kh_mst').value) {
            return alert('Vui lòng nhập Tên và Mã số thuế!');
        }

        let url = '{{ route("customers.store") }}';
        if (currentEditId) {
            url = `/khach-hang/${currentEditId}`;
            formData.append('_method', 'PATCH');
        }

        const res = await fetch(url, {
            method: 'POST', // Dùng POST + _method PATCH để upload file
            headers: { 
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: formData
        }).then(r => r.json());

        if (res.success) {
            await showToast(res.message);
            location.reload();
        } else {
            alert(res.message || 'Có lỗi xảy ra!');
        }
    }

    async function editKH(id) {
        currentEditId = id;
        document.getElementById('modal-kh-title').innerHTML = '<i class="fas fa-edit" style="color:#002B6B"></i> Sửa Thông Tin Khách Hàng';
        
        const res = await fetch(`/khach-hang/${id}`, {
            headers: { 'Accept': 'application/json' }
        }).then(r => r.json());

        if (res) {
            document.getElementById('kh_ten').value = res.ten_cty || '';
            document.getElementById('kh_mst').value = res.ma_so_thue || '';
            document.getElementById('kh_khuvuc').value = res.khu_vuc || '';
            document.getElementById('kh_sdt').value = res.sdt || '';
            document.getElementById('kh_email').value = res.email || '';
            document.getElementById('kh_diachi').value = res.dia_chi || '';
            document.getElementById('kh_nguoilienhe').value = res.nguoi_lien_he || '';
            document.getElementById('kh_sdtnhan').value = res.sdt_nhan || '';
            document.getElementById('kh_diachinhan').value = res.dia_chi_nhan || '';
            document.getElementById('kh_ghichu').value = res.ghi_chu || '';
            document.getElementById('kh_tinhtrang').value = res.tinh_trang || 'active';
            
            const dateInput = document.getElementById('kh_created_date');
            if (res.created_date) {
                let d = new Date(res.created_date);
                dateInput.value = d.toISOString().split('T')[0];
            } else {
                dateInput.value = '';
            }
            dateInput.disabled = true; // Vô hiệu hóa khi sửa
            
            if (res.tai_lieu) {
                const fileName = res.tai_lieu.split('/').pop();
                const fileUrl = `/storage/${res.tai_lieu}`;
                document.getElementById('kh_tai_lieu_preview').innerHTML = `<a href="${fileUrl}" target="_blank" style="color: #002B6B; text-decoration: underline; font-weight: 600;">${fileName}</a>`;
            } else {
                document.getElementById('kh_tai_lieu_preview').innerHTML = '';
            }
            
            openModal('modal-kh');
        }
    }

    async function deleteKH(id, name) {
        showConfirm('Xóa Khách Hàng', `Bạn có chắc muốn xóa? Hành động này không thể hoàn tác.`, async () => {
            const res = await fetch(`/khach-hang/${id}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            }).then(r => r.json());
            
            if (res.success) {
                await showToast(res.message);
                location.reload();
            } else {
                alert(res.message);
            }
        });
    }

    function exportKHExcel() {
        const form = document.getElementById('search-form-new');
        const formData = new FormData(form);
        const params = new URLSearchParams();
        
        for (let [key, value] of formData.entries()) {
            if (value) params.append(key, value);
        }
        
        // Add selected IDs if any
        const checkedBoxes = document.querySelectorAll('.check-kh:checked');
        if (checkedBoxes.length > 0) {
            const ids = Array.from(checkedBoxes).map(cb => cb.value);
            params.append('ids', ids.join(','));
        }
        
        window.location.href = '{{ route("customers.export") }}?' + params.toString();
    }

    function toggleCheckAll(source) {
        const checkboxes = document.querySelectorAll('.check-kh');
        checkboxes.forEach(cb => cb.checked = source.checked);
    }

    async function importKHExcel(input) {
        if (!input.files || !input.files[0]) return;
        const file = input.files[0];
        const formData = new FormData();
        formData.append('excel_file', file);

        await showToast('Đang xử lý nhập liệu...', 'info');

        try {
            const res = await fetch('{{ route("customers.import") }}', {
                method: 'POST',
                headers: { 
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: formData
            }).then(r => r.json());

            if (res.success) {
                await showToast(res.message);
                location.reload();
            } else {
                alert(res.message || 'Lỗi khi nhập Excel');
            }
        } catch (e) {
            console.error(e);
            alert('Có lỗi xảy ra khi kết nối máy chủ');
        } finally {
            input.value = ''; // Reset input
        }
    }
</script>
@endpush
