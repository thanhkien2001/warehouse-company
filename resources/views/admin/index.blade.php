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

    .modal-pro-split { display: grid; grid-template-columns: 3fr 7fr; gap: 24px; align-items: start; }
    .modal-pro-left { padding-right: 15px; border-right: 1px solid #e2e8f0; }
    .modal-pro-right { padding-left: 5px; }
    
    .perm-table { width: 100%; border-collapse: collapse; }
    .perm-table th { background: #f8fafc; padding: 12px; text-align: center; font-size: 13px; font-weight: 700; color: black; border-bottom: 2px solid #e2e8f0; }
    .perm-table th:first-child { text-align: left; }
    .perm-table td { padding: 10px 12px; border-bottom: 1px solid #f1f5f9; font-size: 13px; text-align: center; }
    .perm-table td:first-child { text-align: left; font-weight: 600; color: #1e293b; }
    
    .perm-group { background: #EFF6FF; cursor: pointer; transition: 0.2s; }
    .perm-group:hover { background: #f1f5f9; }
    .perm-group td { font-weight: 800; color: #0f172a; border-bottom: 1px solid #e2e8f0; }
    
    .perm-child-row { display: none; }
    .perm-child-row.show { display: table-row; }
    
    .custom-checkbox { transform: scale(1.3); cursor: pointer; accent-color: #0070D2; }
    
    .modal-box.xl { width: 95vw; max-width: 1400px; }
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
                        <th style="width: 5%; text-align: center;">STT</th>
                        <th style="width: 15%;">Tài khoản</th>
                        <th style="width: 35%;">Tên hiển thị</th>
                        <th style="width: 15%; text-align: center;">Chức danh / Vai trò</th>
                        <th style="width: 12%; text-align: center;">Trạng Thái</th>
                        <th style="width: 10%; text-align: center;">Ngày đăng ký</th>
                        <th style="width: 8%; text-align: center;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $idx => $u)
                    <tr>
                        <td style="text-align: center; color: #94a3b8;">{{ $users->firstItem() + $idx }}</td>
                        <td><b style="color:#0070D2;">{{ $u->username }}</b></td>
                        <td style="font-weight:600; color: #1e293b;">{{ $u->display_name }}</td>
                        <td style="text-align: center;">
                            <div style="font-weight: 600; color: #475569; font-size: 13px; margin-bottom: 4px;">{{ $u->chuc_danh }}</div>
                            @if($u->role == 'Admin') <span class="badge badge-purple" style="background:#f3e8ff; color:#7e22ce;"><i class="fas fa-crown"></i> Admin</span>
                            @elseif($u->role == 'KeToan') <span class="badge badge-yellow" style="background:#fefce8; color:#a16207;">Kế toán</span>
                            @elseif($u->role == 'Nhanvienkinhdoanh') <span class="badge badge-green" style="background:#dcfce7; color:#15803d;">Nhân viên kinh doanh</span>
                            @elseif($u->role == 'Kho') <span class="badge badge-blue" style="background:#eff6ff; color:#1d4ed8;">Quản lý kho</span>
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
    <div class="modal-box xl" style="padding: 0; border-radius: 16px; overflow: hidden; max-height: 95vh; display: flex; flex-direction: column;">
        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #f1f5f9; padding: 20px 24px; background: #fff;">
            <h3 id="mu-title" style="font-weight: 800; font-size: 20px; margin: 0;"><i class="fas fa-user-plus" style="color:#0070D2"></i> Cấp Tài Khoản Mới</h3>
            <i class="fas fa-times" style="cursor: pointer; color: #64748b; font-size: 18px;" onclick="closeModal('modal-user')"></i>
        </div>
        <div style="padding: 24px; overflow-y: auto; flex: 1;">
            <input type="hidden" id="mu_id">
            <div class="modal-pro-split">
                {{-- LEFT SIDE: THÔNG TIN TÀI KHOẢN --}}
                <div class="modal-pro-left">
                    <h4 style="font-size: 14px; font-weight: 800; color: #0f172a; margin-bottom: 15px; border-bottom: 2px solid #e2e8f0; padding-bottom: 8px;">
                        <i class="fas fa-user"></i> THÔNG TIN TÀI KHOẢN
                    </h4>
                    
                    <div class="form-group">
                        <label class="modal-pro-label">Tên đăng nhập <span style="color:red">*</span></label>
                        <input type="text" id="mu_username" class="modal-pro-input" autocomplete="off" placeholder="Tên đăng nhập viết liền, không dấu">
                    </div>
                    <div class="form-group" style="margin-top:15px">
                        <label class="modal-pro-label">Tên hiển thị <span style="color:red">*</span></label>
                        <input type="text" id="mu_display" class="modal-pro-input" autocomplete="off" placeholder="Ví dụ: Nguyễn Văn A">
                    </div>
                    <div class="form-group" style="margin-top:15px">
                        <label class="modal-pro-label">Chức danh</label>
                        <input type="text" id="mu_chuc_danh" class="modal-pro-input" autocomplete="off" placeholder="Ví dụ: Trưởng phòng...">
                    </div>
                    
                    <h4 style="font-size: 14px; font-weight: 800; color: #0f172a; margin-bottom: 15px; margin-top: 25px; border-bottom: 2px solid #e2e8f0; padding-bottom: 8px;">
                        <i class="fas fa-list-alt"></i> THÔNG TIN KHÁC
                    </h4>
                    <div class="form-group">
                        <label class="modal-pro-label">Nhóm quyền <span style="color:red">*</span></label>
                        <select id="mu_role" class="modal-pro-input" onchange="autoCheckPermissions()">
                            <option value="Admin">Admin</option>
                            <option value="KeToan">Kế toán</option>
                            <option value="Nhanvienkinhdoanh">Nhân viên kinh doanh</option>
                            <option value="Kho">Quản lý kho</option>
                        </select>
                    </div>
                    <div class="form-group" style="margin-top:15px">
                        <label class="modal-pro-label">Trạng thái <span style="color:red">*</span></label>
                        <select id="mu_status" class="modal-pro-input">
                            <option value="Hoạt động">Hoạt động</option>
                            <option value="Đang chờ duyệt">Đang chờ duyệt</option>
                            <option value="Bị khóa">Khóa</option>
                        </select>
                    </div>
                    <div class="form-group" style="margin-top:15px">
                        <label class="modal-pro-label">Mật khẩu tạm thời</label>
                        <input type="text" id="mu_pass" class="modal-pro-input" placeholder="Mật khẩu sẽ được yêu cầu đổi">
                    </div>
                </div>

                {{-- RIGHT SIDE: PHÂN QUYỀN HỆ THỐNG --}}
                <div class="modal-pro-right">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                        <h4 style="font-size: 14px; font-weight: 800; color: #0070D2; margin: 0;">
                            <i class="fas fa-shield-alt"></i> PHÂN QUYỀN HỆ THỐNG
                        </h4>
                        <div>
                            <button class="ui-btn ui-btn-outline" style="padding: 6px 12px; font-size: 12px; margin-right: 8px;" onclick="checkAllPerms(true)">Chọn tất cả</button>
                            <button class="ui-btn ui-btn-outline" style="padding: 6px 12px; font-size: 12px; color: #ef4444; border-color: #fca5a5;" onclick="checkAllPerms(false)">Bỏ chọn tất cả</button>
                        </div>
                    </div>
                    
                    <div style="background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 8px; overflow: hidden;">
                        <table class="perm-table">
                            <thead>
                                <tr>
                                    <th style="width: 40%;">PHÂN HỆ / CHỨC NĂNG</th>
                                    <th style="width: 15%;">XEM</th>
                                    <th style="width: 15%;">THÊM/SỬA</th>
                                    <th style="width: 15%;">XÓA</th>
                                    <th style="width: 15%;">XUẤT DỮ LIỆU</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                $sysMods = [
                                    'Danh mục' => [
                                        'sanpham' => 'Sản phẩm',
                                    ],
                                    'Quản lý đơn hàng' => [
                                        'taomakhachhang' => 'Tạo mã khách hàng',
                                        'taodonhang'   => 'Tạo đơn hàng',
                                        'taophieugiao' => 'Tạo phiếu giao hàng',
                                    ],
                                    'Quản lý tồn kho' => [
                                        'nhapkho'   => 'Nhập kho',
                                        'baocaoxuatkho'   => 'Báo cáo xuất kho',
                                        'baocaotonkho' => 'Báo cáo tồn kho',
                                    ],
                                    'Quản lý công nợ' => [
                                        'congno'    => 'Công nợ',
                                        'thanhtoan' => 'Thanh toán',
                                    ],
                                    'Báo cáo tài chính' => [
                                        'baocaotc'  => 'Biểu đồ tài chính',
                                        'baocaoth'  => 'Báo cáo tổng hợp',
                                    ]
                                ];
                                @endphp
                                @foreach($sysMods as $groupName => $children)
                                    @php $gid = Str::slug($groupName); @endphp
                                    <tr class="perm-group" onclick="togglePermGroup('{{ $gid }}')">
                                        <td>
                                            <i class="fas fa-chevron-down" id="icon-{{ $gid }}" style="margin-right: 8px; color: #64748b; font-size: 11px;"></i>
                                            {{ $groupName }}
                                        </td>
                                        <td></td><td></td><td></td><td></td>
                                    </tr>
                                    @foreach($children as $mk => $mname)
                                    <tr class="perm-child-row group-{{ $gid }} show">
                                        <td style="padding-left: 30px; color: black; font-weight: normal;">{{ $mname }}</td>
                                        <td><input type="checkbox" class="cb-perm custom-checkbox" data-mod="{{ $mk }}" data-act="view"></td>
                                        <td><input type="checkbox" class="cb-perm custom-checkbox" data-mod="{{ $mk }}" data-act="edit"></td>
                                        <td><input type="checkbox" class="cb-perm custom-checkbox" data-mod="{{ $mk }}" data-act="delete"></td>
                                        <td><input type="checkbox" class="cb-perm custom-checkbox" data-mod="{{ $mk }}" data-act="export"></td>
                                    </tr>
                                    @endforeach
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
        </div>
        <div style="display: flex; justify-content: flex-end; gap: 12px; border-top: 1px solid #f1f5f9; padding: 20px 24px; background: #fff;">
            <button class="ui-btn ui-btn-outline" style="border-radius: 6px;" onclick="closeModal('modal-user')">Hủy bỏ</button>
            <button class="ui-btn ui-btn-primary" style="border-radius: 6px; background: #0070D2;" onclick="saveUser()"><i class="fas fa-save"></i> Lưu tài khoản</button>
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

function togglePermGroup(gid) {
    const rows = document.querySelectorAll('.group-' + gid);
    const icon = document.getElementById('icon-' + gid);
    let isShowing = false;
    rows.forEach(r => {
        if (r.classList.contains('show')) isShowing = true;
    });
    
    if (isShowing) {
        rows.forEach(r => r.classList.remove('show'));
        if (icon) { icon.classList.remove('fa-chevron-down'); icon.classList.add('fa-chevron-right'); }
    } else {
        rows.forEach(r => r.classList.add('show'));
        if (icon) { icon.classList.remove('fa-chevron-right'); icon.classList.add('fa-chevron-down'); }
    }
}

function checkAllPerms(checked) {
    const role = document.getElementById('mu_role').value;
    if (role === 'Admin') return; // Admin can't change manually
    document.querySelectorAll('.cb-perm').forEach(cb => {
        if (!cb.disabled) cb.checked = checked;
    });
}

function autoCheckPermissions() {
    const role = document.getElementById('mu_role').value;
    const isAd = role === 'Admin';
    const cbs = document.querySelectorAll('.cb-perm');
    
    if (isAd) {
        cbs.forEach(cb => { cb.checked = true; cb.disabled = true; });
    } else {
        cbs.forEach(cb => { cb.disabled = false; cb.checked = false; });
        if (role === 'Kho') {
            cbs.forEach(cb => {
                if (['tonkho', 'nhapkho', 'baocaoxuatkho', 'baocaotonkho'].includes(cb.dataset.mod)) cb.checked = true;
            });
        } else if (role === 'KeToan') {
            cbs.forEach(cb => {
                if (['congno', 'thanhtoan', 'baocaotc', 'baocaoth'].includes(cb.dataset.mod)) cb.checked = true;
                if (cb.dataset.act === 'view') cb.checked = true;
            });
        } else if (role === 'Nhanvienkinhdoanh') {
            cbs.forEach(cb => {
                if (['khachhang', 'donhang', 'phieugiao', 'sanpham'].includes(cb.dataset.mod)) cb.checked = true;
                if (cb.dataset.act === 'view') cb.checked = true;
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
        document.getElementById('mu_chuc_danh').value = '';
        document.getElementById('mu_role').value = 'Nhanvienkinhdoanh';
        document.getElementById('mu_status').value = 'Hoạt động';
        document.getElementById('mu_pass').value = '';
        autoCheckPermissions();
    } else {
        document.getElementById('mu-title').innerHTML = '<i class="fas fa-edit" style="color:#0070D2"></i> Sửa Thông Tin Tài Khoản';
        document.getElementById('mu_id').value = u.id;
        document.getElementById('mu_username').value = u.username;
        document.getElementById('mu_username').readOnly = true;
        document.getElementById('mu_display').value = u.display_name;
        document.getElementById('mu_chuc_danh').value = u.chuc_danh || '';
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
        chuc_danh: document.getElementById('mu_chuc_danh').value,
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


