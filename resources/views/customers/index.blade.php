@extends('layouts.app')
@section('title', 'Đối Tác - Khách Hàng')
@section('page-title', 'Đối Tác - Khách Hàng')
@section('page-subtitle', 'Theo dõi, tìm kiếm và quản lý danh bạ khách hàng của bạn.')

@section('content')
<div class="card" style="padding: 24px;">
    
    <div class="page-header-row" style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 20px; border-bottom: 2.5px solid #cbd5e1; margin-bottom: 25px;">
        <div style="display: flex; align-items: center; gap: 16px;">
            <div style="width: 56px; height: 56px; background: #eef2ff; border-radius: 16px; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 10px rgba(79, 70, 229, 0.15); flex-shrink: 0;">
                <i class="fas fa-address-book" style="font-size: 24px; color: #0070D2;"></i>
            </div>
            <div>
                <h2 style="font-size: 22px; font-weight: 800; color: #0f172a; margin: 0 0 4px 0; letter-spacing: -0.5px;">Khách Hàng & Đối Tác</h2>
                <p style="margin: 0; color: #64748b; font-size: 13.5px;">Quản lý thông tin liên hệ và mã số thuế đối tác.</p>
            </div>
        </div>
    </div>

    {{-- BỘ LỌC MỚI --}}
    <form method="GET" id="search-form-new" style="display: flex; flex-direction: column; gap: 12px; margin-bottom: 25px;">
        {{-- Hàng 1: Từ ngày - Đến ngày + Nút lọc + Thêm khách hàng --}}
        <div class="filter-row">
            <div class="filter-group">
                <div class="date-range-group" style="display: flex; gap: 0;">
                    <div style="display: flex; align-items: center; background: #fff; border: 1px solid #cbd5e1; border-radius: 6px 0 0 6px; padding: 0 12px; border-right: none;">
                        <i class="far fa-calendar-alt" style="color: #64748b; font-size: 14px;"></i>
                    </div>
                    <input type="date" name="date_start" value="{{ request('date_start') }}" style="padding: 10px; border: 1px solid #cbd5e1; border-left: none; font-size: 14px; color: #475569; outline: none; width: 140px;" placeholder="Từ ngày">
                    <div style="display: flex; align-items: center; background: #fff; border: 1px solid #cbd5e1; padding: 0 12px; border-left: none; border-right: none;">
                        <i class="far fa-calendar-alt" style="color: #64748b; font-size: 14px;"></i>
                    </div>
                    <input type="date" name="date_end" value="{{ request('date_end') }}" style="padding: 10px; border: 1px solid #cbd5e1; border-left: none; border-radius: 0 6px 6px 0; font-size: 14px; color: #475569; outline: none; width: 140px;" placeholder="Đến ngày">
                </div>
                <button type="submit" style="background: #0070D2; color: white; border: none; padding: 10px 15px; border-radius: 6px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 5px;">
                    <i class="fas fa-search"></i> Lọc
                </button>
                <a href="{{ route('customers.index') }}" style="background: #E74C3C; color: white; padding: 10px 15px; border-radius: 6px; text-decoration: none; font-size: 13px; font-weight: 600;">Xóa lọc</a>
            </div>

            <button type="button" onclick="openCreateKHModal()" style="background: #0070D2; color: white; border: none; padding: 12px 24px; border-radius: 6px; font-weight: 700; font-size: 14px; cursor: pointer; display: flex; align-items: center; gap: 8px; transition: 0.3s;">
                <i class="fas fa-plus"></i> Thêm khách hàng
            </button>
        </div>

        {{-- Hàng 2: Tìm kiếm + Các bộ lọc còn lại --}}
        <div class="filter-row">
            <div style="position: relative; flex: 1; min-width: 300px; max-width: 500px;">
                <i class="fas fa-search" style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #64748b; font-size: 14px;"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm kiếm theo tên, SĐT, MST..." style="width: 100%; padding: 10px 15px 10px 40px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 14px; outline: none; box-sizing: border-box;">
            </div>

            <div class="filter-group">
                <select name="khu_vuc" onchange="this.form.submit()" style="padding: 10px 15px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 13.5px; color: #475569; background: #fff; cursor: pointer; min-width: 120px;">
                    <option value="">Khu vực</option>
                    <option value="Miền Bắc" {{ request('khu_vuc')=='Miền Bắc'?'selected':'' }}>Miền Bắc</option>
                    <option value="Miền Trung" {{ request('khu_vuc')=='Miền Trung'?'selected':'' }}>Miền Trung</option>
                    <option value="Miền Nam" {{ request('khu_vuc')=='Miền Nam'?'selected':'' }}>Miền Nam</option>
                </select>
                <select name="tinh_trang" onchange="this.form.submit()" style="padding: 10px 15px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 13.5px; color: #475569; background: #fff; cursor: pointer; min-width: 120px;">
                    <option value="">Tình trạng</option>
                    <option value="active" {{ request('tinh_trang')=='active'?'selected':'' }}>Đang hoạt động</option>
                    <option value="unactive" {{ request('tinh_trang')=='unactive'?'selected':'' }}>Ngưng giao dịch</option>
                </select>
                <button type="button" onclick="document.getElementById('kh-import-input').click()" style="background: #D97706; color: white; border: none; padding: 10px 15px; border-radius: 6px; font-weight: 600; font-size: 13.5px; cursor: pointer; display: flex; align-items: center; gap: 6px;">
                    <i class="fas fa-download"></i> Nhập Excel
                </button>
                <button type="button" onclick="exportKHExcel()" style="background: #059669; color: white; border: none; padding: 10px 15px; border-radius: 6px; font-weight: 600; font-size: 13.5px; cursor: pointer; display: flex; align-items: center; gap: 6px;">
                    <i class="fas fa-upload"></i> Xuất Excel
                </button>
            </div>
        </div>
    </form>
    <input type="file" id="kh-import-input" style="display:none" onchange="importKHExcel(this)">

    <div class="legacy-table-container">
        <table class="legacy-table">
            <thead>
                <tr>
                    <th style="text-align: center; width: 3%;"><input type="checkbox" id="check-all-kh" onclick="toggleCheckAll(this)"></th>
                    <th style="text-align: center; width: 5%;">STT</th>
                    <th style="text-align: center; width: 10%;">Ngày tạo</th>
                    <th style="text-align: center; width: 10%;">Mã Khách Hàng</th>
                    <th style="text-align: center; width: 25%;">Thông tin khách hàng</th>
                    <th style="text-align: center; width: 12%;">Mã Số Thuế</th>
                    <th style="text-align: center; width: 12%; min-width: 110px;">Khu vực</th>
                    <th style="text-align: center; width: 10%;">P.I.C</th>
                    <th style="text-align: center; width: 8%;">Tình trạng</th>
                    <th style="text-align: center; width: 7%;">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($customers as $idx => $kh)
                <tr style="cursor: pointer;" onclick="if(!event.target.closest('button') && !event.target.closest('input') && !event.target.closest('a')) { window.location.href='{{ route('customers.show', $kh->id) }}'; }">
                    <td style="text-align: center;"><input type="checkbox" class="check-kh" value="{{ $kh->id }}"></td>
                    <td style="text-align: center;">{{ $customers->firstItem() + $idx }}</td>
                    <td style="text-align: center;">{{ $kh->created_at->format('d/m/Y') }}</td>
                    <td style="text-align: center;"><b style="color:#0070D2">{{ $kh->ma_kh }}</b></td>
                    <td class="col-left">
                        <div style="font-weight: 800; color: #0f172a; margin-bottom: 5px; font-size: 14.5px;">{{ $kh->ten_cty }}</div>
                        <div style="font-size: 12px; color: #64748b; line-height: 1.6;">
                            <div><i class="fas fa-phone-alt" style="margin-right: 6px; width: 12px;"></i>{{ $kh->sdt }}</div>
                            <div title="{{ $kh->dia_chi }}"><i class="fas fa-map-marker-alt" style="margin-right: 6px; width: 12px;"></i>{{ Str::limit($kh->dia_chi, 50) }}</div>
                        </div>
                    </td>
                    <td style="text-align: center;">{{ $kh->ma_so_thue }}</td>
                    <td style="text-align: center; white-space: nowrap;">
                        <span class="badge-region {{ Str::slug($kh->khu_vuc) }}">{{ $kh->khu_vuc }}</span>
                    </td>
                    <td style="text-align: center;">
                        <span style="font-size: 12px; font-weight: 600; color: #475569;">{{ $kh->creator->display_name ?? 'System' }}</span>
                    </td>
                    <td style="text-align: center;">
                        @if(($kh->tinh_trang ?? 'active') == 'active')
                            <span class="badge-status-kh active">Đang hoạt động</span>
                        @else
                            <span class="badge-status-kh unactive">Ngưng giao dịch</span>
                        @endif
                    </td>
                    <td style="text-align: center; white-space: nowrap;">
                        <button onclick="editKH({{ $kh->id }})" class="action-btn btn-edit-pro" title="Sửa"><i class="fas fa-edit"></i></button>
                        <button onclick="deleteKH({{ $kh->id }}, '{{ $kh->ten_cty }}')" class="action-btn btn-del-pro" title="Xóa" style="margin-left: 5px;"><i class="fas fa-trash-alt"></i></button>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" style="padding: 40px; text-align: center; color: #94a3b8;">Chưa có khách hàng nào.</td></tr>
                @endforelse
            </tbody>
        </table>
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
            <h3 id="modal-kh-title" style="font-weight: 800; font-size: 20px;"><i class="fas fa-user-plus" style="color:#0070D2"></i> Thêm Khách Hàng</h3>
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
                <div id="kh_tai_lieu_preview" style="margin-top: 5px; font-size: 11px; color: #0070D2;"></div>
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
            <button class="ui-btn ui-btn-primary" style="border-radius: 6px; background: #0070D2;" onclick="submitKH()"><i class="fas fa-save"></i> Lưu dữ liệu</button>
        </div>
    </div>
</div>

<style>
    .modal-pro-label { font-size: 14px; font-weight: 700; color: #334155; margin-bottom: 8px; display: block; }
    .modal-pro-input { width: 100%; border: 1.5px solid #cbd5e1; border-radius: 8px; padding: 11px 14px; font-size: 14px; outline: none;box-sizing: border-box; transition: 0.3s; }
    .modal-pro-input:focus { border-color: #0070D2; background: #fff; box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1); }

    .action-btn { width: 32px; height: 32px; border-radius: 8px; display: inline-flex; align-items: center; justify-content: center; text-decoration: none; border: none; cursor: pointer; transition: 0.2s; }
    .btn-edit-pro { background: #eef2ff; color: #0070D2; }
    .btn-edit-pro:hover { background: #0070D2; color: #fff; }
    .btn-del-pro { background: #fef2f2; color: #ef4444; }
    .btn-del-pro:hover { background: #ef4444; color: #fff; }

    /* Override Region Badges with Background Color */
    .badge-region { padding: 4px 12px !important; border-radius: 20px !important; color: #fff !important; border: none !important; font-size: 11px !important; white-space: nowrap !important; min-width: 85px !important; display: inline-block !important; text-align: center !important; }
    .badge-region.mien-bac { background: #3498DB !important; }
    .badge-region.mien-trung { background: #E67E22 !important; }
    .badge-region.mien-nam { background: #27AE60 !important; }

    .badge-status-kh { padding: 4px 12px; border-radius: 20px; color: #fff; font-size: 11px; font-weight: 700; white-space: nowrap; border: none; }
    .badge-status-kh.active { background: #27AE60; }
    .badge-status-kh.unactive { background: #ef4444; }

    /* Responsive adjustments */
    .filter-row { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; }
    .filter-group { display: flex; gap: 8px; align-items: center; flex-wrap: wrap; }
    
    @media (max-width: 1400px) {
        .legacy-table-container { overflow-x: auto; -webkit-overflow-scrolling: touch; }
        .legacy-table { min-width: 1200px; }
    }

    @media (max-width: 1200px) {
        .filter-row { flex-direction: column; align-items: stretch; }
        .filter-group { justify-content: flex-start; }
        #search-form-new { gap: 20px; }
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
        document.getElementById('modal-kh-title').innerHTML = '<i class="fas fa-user-plus" style="color:#0070D2"></i> Thêm Khách Hàng';
        
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
        document.getElementById('modal-kh-title').innerHTML = '<i class="fas fa-edit" style="color:#0070D2"></i> Sửa Thông Tin Khách Hàng';
        
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
                document.getElementById('kh_tai_lieu_preview').innerHTML = `<a href="${fileUrl}" target="_blank" style="color: #0070D2; text-decoration: underline; font-weight: 600;">${fileName}</a>`;
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
        // Build query string from form
        const formData = new FormData(document.getElementById('search-form-new'));
        const params = new URLSearchParams(formData);
        params.set('export', 'excel');
        window.location.href = '{{ route("customers.index") }}?' + params.toString();
    }

    function toggleCheckAll(source) {
        const checkboxes = document.querySelectorAll('.check-kh');
        checkboxes.forEach(cb => cb.checked = source.checked);
    }

    async function importKHExcel(input) {
        if (!input.files || !input.files[0]) return;
        const file = input.files[0];
        const formData = new FormData();
        formData.append('file', file);

        showToast('Đang xử lý nhập liệu...', 'info');

        try {
            const res = await fetch('/customers/import', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: formData
            }).then(r => r.json());

            if (res.success) {
                await showToast(res.message);
                location.reload();
            } else {
                alert(res.message || 'Lỗi khi nhập Excel');
            }
        } catch (e) {
            alert('Có lỗi xảy ra khi kết nối máy chủ');
        }
    }
</script>
@endpush
