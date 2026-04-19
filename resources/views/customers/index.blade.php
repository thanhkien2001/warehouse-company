@extends('layouts.app')
@section('title', 'Đối Tác - Khách Hàng')
@section('page-title', 'Đối Tác - Khách Hàng')
@section('page-subtitle', 'Theo dõi, tìm kiếm và quản lý danh bạ khách hàng của bạn.')

@section('content')
<div class="card" style="padding: 24px;">
    
    <div style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 20px; border-bottom: 2.5px solid #cbd5e1; margin-bottom: 25px;">
        <div style="display: flex; align-items: center; gap: 16px;">
            <div style="width: 56px; height: 56px; background: #eef2ff; border-radius: 16px; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 10px rgba(79, 70, 229, 0.15); flex-shrink: 0;">
                <i class="fas fa-address-book" style="font-size: 24px; color: #0070D2;"></i>
            </div>
            <div>
                <h2 style="font-size: 22px; font-weight: 800; color: #0f172a; margin: 0 0 4px 0; letter-spacing: -0.5px;">Khách Hàng & Đối Tác</h2>
                <p style="margin: 0; color: #64748b; font-size: 13.5px;">Quản lý thông tin liên hệ và mã số thuế đối tác.</p>
            </div>
        </div>
        <button onclick="openCreateKHModal()" style="background: #0070D2; color: white; border: none; padding: 12px 24px; border-radius: 6px; font-weight: 700; font-size: 14px; cursor: pointer; display: flex; align-items: center; gap: 8px; transition: 0.3s; box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);">
            <i class="fas fa-user-plus"></i> Thêm Khách hàng
        </button>
    </div>

    <div style="display: flex; gap: 10px; align-items: center; margin-bottom: 25px;">
        <select onchange="window.location.href=this.value" style="padding: 10px 15px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 13.5px; color: #475569; outline: none; cursor: pointer; background: #fff; min-width: 150px;">
            <option value="{{ request()->fullUrlWithQuery(['time'=>'all']) }}" {{ request('time')=='all'?'selected':'' }}>Tất cả thời gian</option>
            <option value="{{ request()->fullUrlWithQuery(['time'=>'today']) }}" {{ request('time')=='today'?'selected':'' }}>Hôm nay</option>
            <option value="{{ request()->fullUrlWithQuery(['time'=>'month']) }}" {{ request('time')=='month'?'selected':'' }}>Tháng này</option>
        </select>

        <div style="width: 400px; position: relative;">
            <form method="GET" id="search-form">
                @if(request('time')) <input type="hidden" name="time" value="{{ request('time') }}"> @endif
                @if(request('sort')) <input type="hidden" name="sort" value="{{ request('sort') }}"> @endif
                @if(request('limit')) <input type="hidden" name="limit" value="{{ request('limit') }}"> @endif
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Nhập tên khách hàng, SĐT, mã số thuế..." style="width: 100%; box-sizing: border-box; padding: 10px 15px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 13.5px; outline: none; color: #000000;">
            </form>
        </div>

        <button onclick="exportKHExcel()" style="background: #10b981; color: white; border: none; padding: 10px 15px; border-radius: 6px; font-weight: 600; font-size: 13.5px; cursor: pointer; display: flex; align-items: center; gap: 6px; transition: 0.2s;">
            <i class="fas fa-file-excel"></i> Xuất Excel
        </button>

        <select onchange="window.location.href=this.value" style="padding: 10px 15px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 13.5px; color: #475569; outline: none; cursor: pointer; background: #fff; min-width: 120px;">
            <option value="{{ request()->fullUrlWithQuery(['sort'=>'newest']) }}" {{ request('sort')=='newest'?'selected':'' }}>Mới nhất ↓</option>
            <option value="{{ request()->fullUrlWithQuery(['sort'=>'oldest']) }}" {{ request('sort')=='oldest'?'selected':'' }}>Cũ nhất ↑</option>
            <option value="{{ request()->fullUrlWithQuery(['sort'=>'az']) }}" {{ request('sort')=='az'?'selected':'' }}>Tên A → Z</option>
        </select>
    </div>

    <div class="legacy-table-container">
        <table class="legacy-table">
            <thead>
                <tr>
                    <th style="text-align: center; width: 5%;">STT</th>
                    <th style="text-align: center; width: 10%;">Ngày tạo</th>
                    <th style="text-align: center; width: 13%;">Mã Khách Hàng</th>
                    <th style="text-align: left; width: 25%;">Thông tin khách hàng</th>
                    <th style="text-align: center; width: 12%;">Mã Số Thuế</th>
                    <th style="text-align: center; width: 10%;">Khu vực</th>
                    <th style="text-align: left; width: 15%;">Ghi chú</th>
                    <th style="text-align: center; width: 10%;">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($customers as $idx => $kh)
                <tr>
                    <td style="text-align: center;">{{ $customers->firstItem() + $idx }}</td>
                    <td style="text-align: center;">{{ $kh->created_at->format('d/m/Y') }}</td>
                    <td style="text-align: center;"><b style="color:#0070D2">{{ $kh->ma_kh }}</b></td>
                    <td>
                        <div style="font-weight: 800; color: #0f172a; margin-bottom: 5px; font-size: 14.5px;">{{ $kh->ten_cty }}</div>
                        <div style="font-size: 12px; color: #64748b; line-height: 1.6;">
                            <div><i class="fas fa-phone-alt" style="color: #0070D2; margin-right: 6px; width: 12px;"></i>{{ $kh->sdt }}</div>
                            <div title="{{ $kh->dia_chi }}"><i class="fas fa-map-marker-alt" style="color: #ef4444; margin-right: 6px; width: 12px;"></i>{{ Str::limit($kh->dia_chi, 50) }}</div>
                        </div>
                    </td>
                    <td style="text-align: center;">{{ $kh->ma_so_thue }}</td>
                    <td style="text-align: center;">
                        <span class="badge-region {{ Str::slug($kh->khu_vuc) }}">{{ $kh->khu_vuc }}</span>
                    </td>
                    <td style="font-style: italic; color: #94a3b8; font-size: 12px;">{{ $kh->ghi_chu ?: '---' }}</td>
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
            <div style="grid-column: span 2;">
                <label class="modal-pro-label">Ghi chú thêm</label>
                <input type="text" id="kh_ghichu" class="modal-pro-input" placeholder="Ghi chú nội bộ...">
            </div>
        </div>
        <div style="display: flex; justify-content: flex-end; gap: 12px; border-top: 1px solid #f1f5f9; padding-top: 20px; margin-top: 20px;">
            <button class="ui-btn ui-btn-outline" style="border-radius: 6px;" onclick="closeModal('modal-kh')">Hủy bỏ</button>
            <button class="ui-btn ui-btn-primary" style="border-radius: 6px; background: #0070D2;" onclick="submitKH()"><i class="fas fa-save"></i> Lưu dữ liệu</button>
        </div>
    </div>
</div>

<style>
    .modal-pro-label { font-size: 13px; font-weight: 700; color: #334155; margin-bottom: 8px; display: block; }
    .modal-pro-input { width: 100%; border: 1.5px solid #cbd5e1; border-radius: 8px; padding: 11px 14px; font-size: 14px; outline: none; background: #f8fafc; box-sizing: border-box; transition: 0.3s; }
    .modal-pro-input:focus { border-color: #0070D2; background: #fff; box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1); }

    .action-btn { width: 32px; height: 32px; border-radius: 8px; display: inline-flex; align-items: center; justify-content: center; text-decoration: none; border: none; cursor: pointer; transition: 0.2s; }
    .btn-edit-pro { background: #eef2ff; color: #0070D2; }
    .btn-edit-pro:hover { background: #0070D2; color: #fff; }
    .btn-del-pro { background: #fef2f2; color: #ef4444; }
    .btn-del-pro:hover { background: #ef4444; color: #fff; }
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

        ['kh_ten','kh_mst','kh_khuvuc','kh_sdt','kh_email','kh_diachi','kh_nguoilienhe','kh_sdtnhan','kh_diachinhan','kh_ghichu'].forEach(id => {
            document.getElementById(id).value = '';
        });
        
        openModal('modal-kh');
    }

    async function submitKH() {
        const data = {
            ten_cty: document.getElementById('kh_ten').value,
            ma_so_thue: document.getElementById('kh_mst').value,
            khu_vuc: document.getElementById('kh_khuvuc').value,
            sdt: document.getElementById('kh_sdt').value,
            email: document.getElementById('kh_email').value,
            dia_chi: document.getElementById('kh_diachi').value,
            nguoi_lien_he: document.getElementById('kh_nguoilienhe').value,
            sdt_nhan: document.getElementById('kh_sdtnhan').value,
            dia_chi_nhan: document.getElementById('kh_diachinhan').value,
            ghi_chu: document.getElementById('kh_ghichu').value,
            created_date: document.getElementById('kh_created_date').value
        };

        if (!data.ten_cty || !data.ma_so_thue) return alert('Vui lòng nhập Tên và Mã số thuế!');

        let url = '{{ route("customers.store") }}';
        let method = 'POST';
        if (currentEditId) {
            url = `/khach-hang/${currentEditId}`;
            method = 'PATCH';
        }

        const res = await fetch(url, {
            method: method,
            headers: { 
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify(data)
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
            document.getElementById('kh_created_date').value = res.created_date || '';
            
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
        window.location.href = '{{ route("customers.index", ["export" => "excel"]) }}';
    }
</script>
@endpush
