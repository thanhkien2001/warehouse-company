@extends('layouts.app')
@section('title', 'Quản Trị Hệ Thống')

@section('content')
<style>
    .admin-nav { display: flex; gap: 30px; border-bottom: 2px solid #e2e8f0; margin-bottom: 25px; padding: 0 10px; }
    .nav-item { padding: 12px 5px; cursor: pointer; font-size: 15px; font-weight: 700; color: #64748b; border-bottom: 3px solid transparent; transition: all 0.3s; display: flex; align-items: center; gap: 8px; }
    .nav-item:hover { color: #0070D2; }
    .nav-item.active { color: #0070D2; border-bottom-color: #0070D2; }
    .tab-pane { display: none; animation: fadeIn 0.3s ease; }
    .tab-pane.active { display: block; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

    /* Action buttons style like khach-hang */
    .action-btn { width: 32px; height: 32px; border-radius: 8px; display: inline-flex; align-items: center; justify-content: center; text-decoration: none; border: none; cursor: pointer; transition: 0.2s; font-size: 14px; }
    .btn-edit-pro { background: #eef2ff; color: #0070D2; }
    .btn-edit-pro:hover { background: #0070D2; color: #fff; }
    .btn-del-pro { background: #fef2f2; color: #ef4444; }
    .btn-del-pro:hover { background: #ef4444; color: #fff; }
    .btn-shield-pro { background: #fff7ed; color: #f97316; }
    .btn-shield-pro:hover { background: #f97316; color: #fff; }

    .modal-pro-label { font-size: 13px; font-weight: 700; color: #334155; margin-bottom: 8px; display: block; }
    .modal-pro-input { width: 100%; border: 1.5px solid #cbd5e1; border-radius: 8px; padding: 11px 14px; font-size: 14px; outline: none; background: #f8fafc; box-sizing: border-box; transition: 0.3s; }
    .modal-pro-input:focus { border-color: #0070D2; background: #fff; box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1); }
</style>

<div style="background: #ffffff; border-radius: 20px; padding: 24px; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.05); border: 1px solid #f1f5f9;">
    {{-- HEADER --}}
    <div style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 20px; border-bottom: 2.5px solid #cbd5e1; margin-bottom: 25px;">
        <div style="display: flex; align-items: center; gap: 16px;">
            <div style="width: 56px; height: 56px; background: #eef2ff; border-radius: 16px; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 10px rgba(79, 70, 229, 0.15); flex-shrink: 0;">
                <i class="fas fa-user-shield" style="font-size: 24px; color: #0070D2;"></i>
            </div>
            <div>
                <h2 style="font-size: 22px; font-weight: 800; color: #0f172a; margin: 0 0 4px 0; letter-spacing: -0.5px;">Quản Trị Hệ Thống</h2>
                <p style="margin: 0; color: #64748b; font-size: 13.5px;">Trung tâm điều khiển tài khoản và bảo mật phân quyền.</p>
            </div>
        </div>
        <button onclick="openModalUser()" style="background: #0070D2; color: white; border: none; padding: 12px 24px; border-radius: 6px; font-weight: 700; font-size: 14px; cursor: pointer; display: flex; align-items: center; gap: 8px; transition: 0.3s; box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);">
            <i class="fas fa-plus-circle"></i> Cấp Tài Khoản Mới
        </button>
    </div>

    {{-- SUB-NAV --}}
    <div class="admin-nav">
        <div class="nav-item active" onclick="switchAdminTab('users', this)"><i class="fas fa-users-cog"></i> Tài khoản nhân viên</div>
        <div class="nav-item" onclick="switchAdminTab('matrix', this)"><i class="fas fa-key"></i> Bảng phân quyền matrix</div>
    </div>

    {{-- TAB: USERS --}}
    <div id="tab-users" class="tab-pane active">
        <div class="legacy-table-container">
            <table class="legacy-table">
                <thead>
                    <tr>
                        <th style="text-align: center; width: 60px;">STT</th>
                        <th style="width: 180px;">Tài khoản</th>
                        <th>Tên hiển thị</th>
                        <th style="text-align: center; width: 140px;">Chức vụ</th>
                        <th style="text-align: center; width: 140px;">Trạng Thái</th>
                        <th style="text-align: center; width: 160px;">Ngày đăng ký</th>
                        <th style="text-align: center; width: 120px;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $idx => $u)
                    <tr>
                        <td style="text-align: center; color: #94a3b8;">{{ $users->firstItem() + $idx }}</td>
                        <td><b style="color:#0070D2;">{{ $u->username }}</b></td>
                        <td style="font-weight:600; color: #1e293b;">{{ $u->display_name }}</td>
                        <td style="text-align: center;">
                            @if($u->role == 'Admin') <span class="badge badge-purple" style="background:#f3e8ff; color:#7e22ce;"><i class="fas fa-crown"></i> Admin</span>
                            @elseif($u->role == 'QuanLy') <span class="badge badge-blue" style="background:#eff6ff; color:#1d4ed8;">Quản lý</span>
                            @elseif($u->role == 'KeToan') <span class="badge badge-yellow" style="background:#fefce8; color:#a16207;">Kế toán</span>
                            @elseif($u->role == 'NhanVien') <span class="badge badge-green" style="background:#dcfce7; color:#15803d;">Nhân viên</span>
                            @else <span class="badge badge-gray">{{ $u->role }}</span>
                            @endif
                        </td>
                        <td style="text-align: center;">
                            @if($u->status == 'Hoạt động') <span class="badge badge-green" style="background:#dcfce7; color:#15803d;"><i class="fas fa-check-circle"></i> Đang H.Động</span>
                            @elseif($u->status == 'Đang chờ duyệt') <span class="badge badge-orange" style="background:#fff7ed; color:#c2410c;"><i class="fas fa-hourglass-half"></i> Chờ duyệt</span>
                            @else <span class="badge badge-red" style="background:#fee2e2; color:#b91c1c;"><i class="fas fa-lock"></i> Đã khóa</span>
                            @endif
                        </td>
                        <td style="text-align: center; color: #64748b;">{{ $u->created_at->format('d/m/Y H:i') }}</td>
                        <td style="text-align: center;">
                            <button class="action-btn btn-edit-pro" onclick='openModalUser(@json($u))' title="Phân Quyền / Sửa"><i class="fas fa-edit"></i></button>
                            @if($u->id != auth()->id())
                            <button class="action-btn btn-del-pro" style="margin-left: 5px;" onclick="deleteUser({{ $u->id }}, '{{ $u->username }}')" title="Xóa tài khoản"><i class="fas fa-trash-alt"></i></button>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- PAGINATION --}}
        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 25px; padding: 0 10px;">
            <div style="color: #64748b; font-size: 14px;">Đang hiển thị <b>{{ $users->firstItem() ?? 0 }} - {{ $users->lastItem() ?? 0 }}</b> trong tổng số <b>{{ $users->total() }}</b> tài khoản</div>
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
                <div>{{ $users->appends(request()->all())->links('pagination::bootstrap-4') }}</div>
            </div>
        </div>
    </div>

    {{-- TAB: MATRIX --}}
    <div id="tab-matrix" class="tab-pane">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; background: #f8fafc; padding: 15px 20px; border-radius: 12px; border: 1px solid #e2e8f0;">
            <div style="display: flex; align-items: center; gap: 15px;">
                <span style="font-weight: 700; color: #475569;">Chọn tài khoản:</span>
                <select id="matrix-user-select" onchange="loadMatrixPermissions()" style="padding: 10px 15px; border-radius: 8px; border: 1px solid #cbd5e1; outline: none; font-weight: 700; color: #0f172a; background: #fff; cursor: pointer; min-width: 250px;">
                    <option value="">Chọn nhân viên để cấu hình</option>
                    @foreach($users_all as $u)
                        <option value="{{ $u->id }}">{{ $u->username }} ({{ $u->display_name }})</option>
                    @endforeach
                </select>
            </div>
            <button onclick="saveMatrixPermissions()" style="background: #10b981; color: #fff; border: none; padding: 12px 25px; border-radius: 8px; font-weight: 700; cursor: pointer; box-shadow: 0 4px 10px rgba(16, 185, 129, 0.2); display: flex; align-items: center; gap: 8px; transition: 0.3s;">
                <i class="fas fa-save"></i> Lưu Ma Trận Quyền
            </button>
        </div>

        <div class="legacy-table-container">
            <table class="legacy-table">
                <thead>
                    <tr>
                        <th style="width:40%">PHÂN HỆ CHỨC NĂNG</th>
                        <th style="text-align: center; color: #3b82f6;"><i class="fas fa-eye"></i> Được Xem</th>
                        <th style="text-align: center; color: #f59e0b;"><i class="fas fa-pen"></i> Thêm/Sửa</th>
                        <th style="text-align: center; color: #ef4444;"><i class="fas fa-trash-alt"></i> Xóa</th>
                    </tr>
                </thead>
                <tbody id="matrix-body">
                    @php
                        $matrix_modules = [
                            'khachhang' => ['name' => 'Quản lý Khách Hàng', 'icon' => 'fa-users'],
                            'donhang'   => ['name' => 'Quản lý Đơn Hàng (CTO)', 'icon' => 'fa-shopping-cart'],
                            'phieugiao' => ['name' => 'Phiếu Giao Hàng (DN)', 'icon' => 'fa-file-export'],
                            'tonkho'    => ['name' => 'Quản lý Tồn Kho & SP', 'icon' => 'fa-boxes']
                        ];
                    @endphp
                    @foreach($matrix_modules as $key => $mod)
                    <tr>
                        <td>
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <div style="width: 32px; height: 32px; background: #f1f5f9; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas {{ $mod['icon'] }}" style="color: #64748b; font-size: 14px;"></i>
                                </div>
                                <b style="color: #0f172a; font-size: 14.5px;">{{ $mod['name'] }}</b>
                            </div>
                        </td>
                        <td style="text-align: center;"><input type="checkbox" class="matrix-perm" data-mod="{{ $key }}" data-act="view" style="transform: scale(1.4); cursor: pointer;"></td>
                        <td style="text-align: center;"><input type="checkbox" class="matrix-perm" data-mod="{{ $key }}" data-act="edit" style="transform: scale(1.4); cursor: pointer;"></td>
                        <td style="text-align: center;"><input type="checkbox" class="matrix-perm" data-mod="{{ $key }}" data-act="delete" style="transform: scale(1.4); cursor: pointer;"></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div style="margin-top: 15px; padding: 15px; background: #fffbeb; border: 1px solid #fde68a; border-radius: 10px; color: #92400e; font-size: 13px;">
            <i class="fas fa-exclamation-triangle"></i> <b>Chú ý:</b> Việc cấu hình ma trận quyền sẽ ghi đè lên các thiết lập quyền hiện tại của tài khoản đã chọn. Vui lòng kiểm tra kỹ trước khi nhấn "Lưu".
        </div>
    </div>
</div>

{{-- MODAL USER --}}
<div id="modal-user" class="modal-overlay">
    <div class="modal-box lg" style="width: 650px; max-width: 95vw; padding: 0; border-radius: 16px; overflow: hidden;">
        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #f1f5f9; padding: 20px 24px; background: #fff;">
            <h3 id="mu-title" style="font-weight: 800; font-size: 20px; margin: 0;"><i class="fas fa-user-plus" style="color:#0070D2"></i> Cấp Tài Khoản Mới</h3>
            <i class="fas fa-times" style="cursor: pointer; color: #64748b; font-size: 18px;" onclick="closeModal('modal-user')"></i>
        </div>
        <div style="padding: 24px;">
            <input type="hidden" id="mu_id">
            
            <div style="display:grid;grid-template-columns: 1fr 1fr;gap:20px;margin-bottom:20px">
                <div class="form-group">
                    <label class="modal-pro-label">Tên Đăng Nhập <span>*</span></label>
                    <input type="text" id="mu_username" class="modal-pro-input" autocomplete="off" placeholder="Ví dụ: admin">
                </div>
                <div class="form-group">
                    <label class="modal-pro-label">Tên Hiển Thị <span>*</span></label>
                    <input type="text" id="mu_display" class="modal-pro-input" autocomplete="off" placeholder="Ví dụ: Nguyễn Văn A">
                </div>
            </div>

            <div style="display:grid;grid-template-columns: 1fr 1fr;gap:20px;margin-bottom:20px">
                <div class="form-group">
                    <label class="modal-pro-label">Nhóm Quyền <span>*</span></label>
                    <select id="mu_role" class="modal-pro-input" onchange="autoCheckPermissions()">
                        <option value="NhanVien">Nhân viên thông thường</option>
                        <option value="KeToan">Kế toán / Thủ kho</option>
                        <option value="QuanLy">Quản lý kho</option>
                        <option value="Admin">Admin (Toàn quyền)</option>
                        <option value="MoiDangKy">Mới đăng ký (Chờ duyệt)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="modal-pro-label">Trạng thái <span>*</span></label>
                    <select id="mu_status" class="modal-pro-input">
                        <option value="Hoạt động">Hoạt động bình thường</option>
                        <option value="Đang chờ duyệt">Đang chờ xét duyệt</option>
                        <option value="Bị khóa">Khóa tài khoản</option>
                    </select>
                </div>
            </div>

            <div class="form-group" style="margin-bottom: 25px;">
                <label class="modal-pro-label">Mật khẩu (Để trống nếu giữ nguyên)</label>
                <input type="text" id="mu_pass" class="modal-pro-input" placeholder="Nhập mật khẩu mới...">
            </div>

            <h4 style="font-size:14px;font-weight:800;color:#0070D2;margin-bottom:15px;display:flex;align-items:center;gap:8px;">
                <i class="fas fa-shield-alt"></i> Phân quyền chi tiết cho tài khoản
            </h4>
            <div style="background:#f8fafc;border-radius:12px;border:1px solid #e2e8f0;padding:20px;display:grid;grid-template-columns:1fr 1fr;gap:15px">
                @foreach($matrix_modules as $k => $mod)
                <div style="background:#fff;padding:15px;border-radius:12px;border:1px solid #cbd5e1;">
                    <div style="font-weight:800;font-size:13px;margin-bottom:10px;color:#0f172a; display:flex; align-items:center; gap:6px;">
                        <i class="fas {{ $mod['icon'] }}" style="color: #64748b; font-size: 12px;"></i> {{ $mod['name'] }}
                    </div>
                    <label style="display:flex;align-items:center;gap:8px;margin-bottom:6px;font-size:12.5px;cursor:pointer; color: #475569;"><input type="checkbox" class="cb-perm" data-mod="{{ $k }}" data-act="view"> <span>Cho phép <b style="color:#3b82f6">XEM</b></span></label>
                    <label style="display:flex;align-items:center;gap:8px;margin-bottom:6px;font-size:12.5px;cursor:pointer; color: #475569;"><input type="checkbox" class="cb-perm" data-mod="{{ $k }}" data-act="edit"> <span>Cho phép <b style="color:#f59e0b">THÊM/SỬA</b></span></label>
                    <label style="display:flex;align-items:center;gap:8px;font-size:12.5px;cursor:pointer; color: #475569;"><input type="checkbox" class="cb-perm" data-mod="{{ $k }}" data-act="delete"> <span>Cho phép <b style="color:#ef4444">XÓA</b></span></label>
                </div>
                @endforeach
            </div>
            
        </div>
        <div style="display: flex; justify-content: flex-end; gap: 12px; border-top: 1px solid #f1f5f9; padding: 20px 24px; background: #fff;">
            <button class="ui-btn ui-btn-outline" style="border-radius: 6px;" onclick="closeModal('modal-user')">Hủy bỏ</button>
            <button class="ui-btn ui-btn-primary" style="border-radius: 6px; background: #0070D2;" onclick="saveUser()"><i class="fas fa-save"></i> Lưu dữ liệu</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function switchAdminTab(tab, el) {
    document.querySelectorAll('.tab-pane').forEach(tp => tp.classList.remove('active'));
    document.querySelectorAll('.nav-item').forEach(ni => ni.classList.remove('active'));
    
    document.getElementById('tab-' + tab).classList.add('active');
    el.classList.add('active');
}

async function loadMatrixPermissions() {
    const userId = document.getElementById('matrix-user-select').value;
    const checks = document.querySelectorAll('.matrix-perm');
    checks.forEach(c => { c.checked = false; c.disabled = !userId; });
    
    if (!userId) return;

    try {
        const res = await fetch(`/admin/users/${userId}/permissions`).then(r => r.json());
        checks.forEach(cb => {
            const mod = cb.dataset.mod;
            const act = cb.dataset.act;
            if (res[mod] && res[mod][act]) {
                cb.checked = true;
            }
        });
    } catch (e) {
        showToast('Không thể tải quyền hạn!', 'error');
    }
}

async function saveMatrixPermissions() {
    const userId = document.getElementById('matrix-user-select').value;
    if (!userId) return showToast('Vui lòng chọn tài khoản!', 'warning');

    const perms = {};
    document.querySelectorAll('.matrix-perm').forEach(cb => {
        const mod = cb.dataset.mod;
        const act = cb.dataset.act;
        if (!perms[mod]) perms[mod] = {};
        perms[mod][act] = cb.checked;
    });

    try {
        const res = await apiPost(`/admin/users/${userId}/permissions`, { permissions: perms });
        if (res.success) {
            showToast('Đã lưu cấu hình ma trận quyền!');
        } else {
            showToast(res.message, 'error');
        }
    } catch (e) {
        showToast('Lỗi khi lưu!', 'error');
    }
}

function autoCheckPermissions() {
    const role = document.getElementById('mu_role').value;
    const isAd = role === 'Admin';
    const cbs = document.querySelectorAll('.cb-perm');
    
    if (isAd) {
        cbs.forEach(cb => { cb.checked = true; cb.disabled = true; });
    } else {
        cbs.forEach(cb => { cb.disabled = false; });
        if (role === 'QuanLy') {
            cbs.forEach(cb => cb.checked = true);
        } else if (role === 'KeToan') {
            cbs.forEach(cb => cb.checked = (cb.dataset.act==='view' || (cb.dataset.mod==='donhang' && cb.dataset.act==='edit')));
        } else if (role === 'NhanVien') {
            cbs.forEach(cb => {
                cb.checked = (cb.dataset.act==='view') || (cb.dataset.mod==='donhang' && cb.dataset.act==='edit') || (cb.dataset.mod==='khachhang' && cb.dataset.act==='edit');
            });
        }
    }
}

function openModalUser(u = null) {
    const cbs = document.querySelectorAll('.cb-perm');
    cbs.forEach(cb => { cb.checked = false; cb.disabled = false; });
    
    if(!u) {
        document.getElementById('mu-title').innerHTML = '<i class="fas fa-user-plus" style="color:#0070D2"></i> Cấp Tài Khoản Mới';
        document.getElementById('mu_id').value = '';
        document.getElementById('mu_username').value = '';
        document.getElementById('mu_username').readOnly = false;
        document.getElementById('mu_display').value = '';
        document.getElementById('mu_role').value = 'NhanVien';
        document.getElementById('mu_status').value = 'Hoạt động';
        document.getElementById('mu_pass').value = '';
        autoCheckPermissions();
    } else {
        document.getElementById('mu-title').innerHTML = '<i class="fas fa-edit" style="color:#0070D2"></i> Sửa Thông Tin Tài Khoản';
        document.getElementById('mu_id').value = u.id;
        document.getElementById('mu_username').value = u.username;
        document.getElementById('mu_username').readOnly = true;
        document.getElementById('mu_display').value = u.display_name;
        document.getElementById('mu_role').value = u.role;
        document.getElementById('mu_status').value = u.status;
        document.getElementById('mu_pass').value = '';
        
        if (u.role === 'Admin') {
            cbs.forEach(cb => { cb.checked = true; cb.disabled = true; });
        } else {
            const userId = u.id;
            fetch(`/admin/users/${userId}/permissions`).then(r => r.json()).then(res => {
                cbs.forEach(cb => {
                    const mod = cb.dataset.mod;
                    const act = cb.dataset.act;
                    if (res[mod] && res[mod][act]) cb.checked = true;
                });
            });
        }
    }
    openModal('modal-user');
}

async function saveUser() {
    const id = document.getElementById('mu_id').value;
    const perms = {};
    document.querySelectorAll('.cb-perm').forEach(cb => {
        const mod = cb.dataset.mod;
        const act = cb.dataset.act;
        if(!perms[mod]) perms[mod] = {};
        perms[mod][act] = cb.checked;
    });
    
    const data = {
        username: document.getElementById('mu_username').value,
        display_name: document.getElementById('mu_display').value,
        role: document.getElementById('mu_role').value,
        status: document.getElementById('mu_status').value,
        password: document.getElementById('mu_pass').value,
        permissions: perms
    };
    
    try {
        const url = id ? `/admin/users/${id}` : '/admin/users';
        const method = id ? 'PATCH' : 'POST';
        const res = await fetch(url, {
            method, headers: {'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},
            body: JSON.stringify(data)
        }).then(r => r.json());
        
        if (res.success) { 
            await showToast('Đã lưu thành công!'); 
            location.reload(); 
        } else { 
            showToast(res.message, 'error'); 
        }
    } catch(e) { 
        showToast('Lỗi kết nối hệ thống!', 'error'); 
    }
}

function deleteUser(id, un) {
    showConfirm('Xóa Tài Khoản', `Bạn có chắc muốn xóa tài khoản? Hành động này không thể hoàn tác.`, async () => {
        try {
            const res = await apiDelete(`/admin/users/${id}`);
            if (res.success) { await showToast('Đã xóa tài khoản'); location.reload(); }
            else { showToast('Lỗi khi xóa!', 'error'); }
        } catch(e) { showToast('Lỗi hệ thống!', 'error'); }
    });
}

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.matrix-perm').forEach(c => c.disabled = true);
});
</script>
@endpush


