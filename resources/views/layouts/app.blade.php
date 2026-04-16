<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'GAMBERTE WMS')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        :root {
            /* Legacy Palette */
            --primary: #4318FF;
            --primary-dark: #3730a3;
            --sidebar-bg: #111c44; /* Dark blue from legacy */
            --bg-body: #f4f7fe;
            --text-main: #1b2559;
            --text-muted: #a3aed0;
            --radius-lg: 16px;
            --shadow-card: 0 4px 20px rgba(0,0,0,0.04);
            --font-family: 'Inter', sans-serif;
            --success: #01b574;
            --warning: #ffb800;
            --danger: #ee5d50;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: var(--font-family); background: var(--bg-body); color: var(--text-main); display: flex; height: 100vh; overflow: hidden; }

        /* SIDEBAR */
        #sidebar {
            width: 280px;
            background: var(--sidebar-bg);
            display: flex;
            flex-direction: column;
            height: 100vh;
            flex-shrink: 0;
            color: #fff;
            transition: all 0.3s ease;
            z-index: 1000;
        }

        .sidebar-header {
            padding: 30px 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            border-bottom: 1px solid rgba(255,255,255,0.05);
            text-align: center;
        }
        .sidebar-header img { width: 140px; margin-bottom: 8px; }
        .sidebar-header .sub-brand { font-size: 10px; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px; }

        .menu-container { flex: 1; overflow-y: auto; padding: 20px 15px; }
        .menu-container::-webkit-scrollbar { width: 3px; }
        .menu-container::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 4px; }

        .menu-list { list-style: none; }
        .menu-item {
            display: flex; align-items: center; gap: 12px;
            padding: 12px 18px; border-radius: 8px;
            color: #cbd5e1; font-size: 16px; font-weight: 500;
            text-decoration: none; cursor: pointer; transition: 0.2s;
            margin-bottom: 5px; border: none; background: transparent; width: 100%; text-align: left;
        }
        .menu-item i { width: 18px; text-align: center; }
        .menu-item:hover, .menu-item.active { background: rgba(255,255,255,0.1); color: #fff; }
        .menu-item.active { font-weight: 700; background: rgba(255,255,255,0.15); }

        /* Dropdown/Submenu */
        .has-submenu { position: relative; }
        .submenu { list-style: none; padding-left: 30px; display: none; margin-bottom: 10px; }
        .submenu.show { display: block; }
        .submenu-item {
            display: flex; align-items: center; gap: 10px;
            padding: 8px 15px; border-radius: 6px;
            color: #94a3b8; font-size: 15px; font-weight: 500;
            text-decoration: none; cursor: pointer; transition: 0.2s;
        }
        .submenu-item:hover { color: #fff; }
        .submenu-item.active { color: #fff; font-weight: 700; }
        .arrow { margin-left: auto; font-size: 10px; transition: 0.3s; }
        .has-submenu.open .arrow { transform: rotate(180deg); }

        .sidebar-footer {
            padding: 20px;
            background: rgba(0,0,0,0.15);
            border-top: 1px solid rgba(255,255,255,0.08);
        }
        .user-info { display: flex; align-items: center; gap: 12px; margin-bottom: 15px; }
        .user-avatar {
            width: 42px; height: 42px; border-radius: 50%;
            background: var(--primary); display: flex; align-items: center; justify-content: center;
            font-weight: 800; font-size: 16px; box-shadow: 0 4px 10px rgba(67,24,255,0.3);
        }
        .user-text { overflow: hidden; }
        .user-name { font-weight: 800; font-size: 14px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin-bottom: 2px; }
        .user-role { font-size: 10px; color: #94a3b8; padding: 2px 8px; background: rgba(255,255,255,0.1); border-radius: 10px; }

        .action-item-pro {
            color: #cbd5e1; font-size: 13px; font-weight: 500; cursor: pointer;
            display: flex; align-items: center; gap: 10px; padding: 8px 12px;
            border-radius: 8px; transition: 0.2s; margin-bottom: 4px; text-decoration: none;
        }
        .action-item-pro:hover { background: rgba(255, 255, 255, 0.08); color: #fff; }
        .action-item-pro.logout { color: var(--danger); }
        .action-item-pro.logout:hover { background: rgba(238,93,80,0.15); }

        /* MAIN CONTENT */
        #main-wrapper { flex: 1; display: flex; flex-direction: column; overflow: hidden; }
        
        #topbar {
            height: 70px; display: flex; align-items: center; padding: 0 30px;
            background: transparent; margin-top: 15px; flex-shrink: 0;
        }
        .topbar-content { flex: 1; display: flex; align-items: center; justify-content: space-between; }
        .page-header h2 { font-size: 24px; font-weight: 800; color: var(--text-main); margin: 0; }
        .page-header p { font-size: 13px; color: var(--text-muted); margin-top: 2px; font-weight: 500; }

        #content-area { flex: 1; overflow-y: auto; padding: 10px 30px 30px; }
        #content-area::-webkit-scrollbar { width: 5px; }
        #content-area::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 5px; }

        /* COMMON COMPONENTS (Legacy Parity) */
        .card { background: #fff; border-radius: var(--radius-lg); box-shadow: var(--shadow-card); padding: 24px; border: 1px solid #f1f5f9; }
        
        /* TABLE STYLE */
        .legacy-table-container { border-left: none !important; border-right: none !important; border-radius: 12px; overflow: hidden; background: #fff; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); }
        .legacy-table { width: 100%; border-collapse: collapse; border-left: none !important; border-right: none !important; }
        .legacy-table thead th { background: #f8fafc; padding: 16px 15px; color: #1e293b !important; font-size: 13px !important; font-weight: 800 !important; text-transform: uppercase; border-bottom: 2.5px solid #cbd5e1; text-align: left; letter-spacing: 0.5px; border-left: none !important; border-right: none !important; }
        .legacy-table tbody td { padding: 16px 15px; border-bottom: 1.5px solid #e2e8f0; font-size: 14px; color: #334155; border-left: none !important; border-right: none !important; }
        .legacy-table tbody tr:last-child td { border-bottom: none; }
        .legacy-table tbody tr:hover { background: #f8fafc; }
        
        /* STATUS BADGES */
        .badge-status { display: inline-block; width: 130px; text-align: center; padding: 6px 4px; border-radius: 6px; font-size: 11px; font-weight: 700; background: #fff; white-space: nowrap; }
        .badge-status.cho-xac-nhan { color: #E67E22; border: 1px solid #E67E22; }
        .badge-status.dang-xu-ly { color: #3498DB; border: 1px solid #3498DB; }
        .badge-status.da-huy { color: #E74C3C; border: 1px solid #E74C3C; }
        .badge-status.hoan-thanh, .badge-status.da-giao-xong { color: #27AE60; border: 1px solid #27AE60; }
        .badge-status.dang-van-chuyen, .badge-status.dang-giao { color: #8E44AD; border: 1px solid #8E44AD; }
        .badge-status.cho-giao-hang { color: #F39C12; border: 1px solid #F39C12; }

        /* REGION BADGES */
        .badge-region { padding: 2px 8px; font-size: 11px; font-weight: 700; background: #fff; }
        .badge-region.mien-bac { color: #3498DB; border: 1px solid #3498DB; border-radius: 20px; }
        .badge-region.mien-trung { color: #E67E22; border: 1px solid #E67E22; border-radius: 20px; }
        .badge-region.mien-nam { color: #27AE60; border: 1px solid #27AE60; border-radius: 20px; }

        /* MODAL (Clean Modern) */
        .modal-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 10000; align-items: center; justify-content: center; }
        .modal-overlay.active { display: flex; }
        .modal-box { background: #fff; border-radius: 20px; width: 95%; max-width: 500px; padding: 25px; box-shadow: 0 15px 40px rgba(0,0,0,0.2); animation: zoomIn 0.3s; }
        @keyframes zoomIn { from { transform: scale(0.9); opacity: 0; } to { transform: scale(1); opacity: 1; } }
        
        /* BUTTONS */
        .ui-btn { padding: 10px 20px; border-radius: 6px; font-weight: 600; cursor: pointer; border: none; transition: 0.3s; font-size: 14px; display: inline-flex; align-items: center; gap: 8px; text-decoration: none; font-family: inherit; }
        .ui-btn-primary { background: var(--primary); color: white; box-shadow: 0 4px 12px rgba(67,24,255,0.2); }
        .ui-btn-primary:hover { transform: translateY(-2px); box-shadow: 0 6px 15px rgba(67,24,255,0.25); }
        .ui-btn-outline { background: #fff; color: #0070D2; border: 1px solid #0070D2; }
        .ui-btn-outline:hover { background: #f8fafc; border-color: #94a3b8; }
        
        /* STANDARDIZED BUTTONS */
        .ui-btn-pdf { background: #FF0000 !important; color: white !important; }
        .ui-btn-pdf:hover { background: #cc0000 !important; }
        .ui-btn-save { background: #0070D2 !important; color: white !important; }
        .ui-btn-save:hover { background: #005bb5 !important; }

        /* TOAST & SWEETALERT Z-INDEX OVERRIDES */
        #toast-container { position: fixed; bottom: 30px; right: 30px; z-index: 99999; display: flex; flex-direction: column; gap: 10px; }
        .toast { padding: 12px 24px; border-radius: 12px; background: #fff; box-shadow: 0 10px 30px rgba(0,0,0,0.1); border-left: 6px solid; display: flex; align-items: center; gap: 12px; font-weight: 700; color: #2d3748; font-size: 14px; animation: slideInX 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55); min-width: 250px; }
        .toast.success { border-left-color: var(--success); }
        .toast.error { border-left-color: var(--danger); }
        @keyframes slideInX { from { transform: translateX(120%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
        /* Fix SweetAlert being hidden behind Modals */
        .swal2-container { z-index: 99999 !important; }

        /* PAGINATION OVERRIDES */
        .pagination { display: flex; list-style: none; gap: 5px; margin: 0; padding: 0; }
        .page-item .page-link { 
            padding: 8px 14px; border: 1.5px solid #e2e8f0; border-radius: 6px; 
            color: #475569; font-weight: 700; text-decoration: none; font-size: 13px;
            background: #fff; transition: 0.2s;
        }
        .page-item.active .page-link { background: #0070D2; color: #fff; border-color: #0070D2; box-shadow: 0 4px 10px rgba(67,24,255,0.25); }
        .page-item.disabled .page-link { color: #cbd5e1; background: #f8fafc; border-color: #f1f5f9; cursor: not-allowed; }
        .page-item:hover:not(.active):not(.disabled) .page-link { background: #f1f5f9; border-color: #cbd5e1; color: var(--primary); }

        @media (max-width: 992px) {
            #sidebar { position: fixed; left: -280px; }
            #sidebar.open { left: 0; }
        }
    </style>
    @stack('styles')
</head>
<body>
    <aside id="sidebar">
        <div class="sidebar-header">
            <img src="https://i.ibb.co/whdGg4FK/Chat-GPT-Image-00-40-01-22-thg-3-2026.png" alt="GAMBERTE">
            <div class="sub-brand">Business Management System</div>
        </div>

        <div class="menu-container">
            <ul class="menu-list">
                <li>
                    <a href="{{ route('dashboard') }}" class="menu-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="fas fa-home"></i> <span>Tổng quan</span>
                    </a>
                </li>

                <li class="has-submenu {{ request()->routeIs('orders.*') || request()->routeIs('customers.*') || request()->routeIs('deliveries.*') ? 'open' : '' }}">
                    <div class="menu-item" onclick="toggleSubmenu(this)">
                        <i class="fas fa-folder-open"></i> <span>Quản lý đơn hàng</span>
                        <i class="fas fa-chevron-down arrow"></i>
                    </div>
                    <ul class="submenu {{ request()->routeIs('orders.*') || request()->routeIs('customers.*') || request()->routeIs('deliveries.*') ? 'show' : '' }}">
                        <li><a href="{{ route('customers.index') }}" class="submenu-item {{ request()->routeIs('customers.*') ? 'active' : '' }}"><i class="fas fa-user-plus"></i> Khách hàng</a></li>
                        <li><a href="{{ route('orders.index') }}" class="submenu-item {{ request()->routeIs('orders.*') ? 'active' : '' }}"><i class="fas fa-cart-plus"></i> Đơn hàng (CTO)</a></li>
                        <li><a href="{{ route('deliveries.index') }}" class="submenu-item {{ request()->routeIs('deliveries.*') ? 'active' : '' }}"><i class="fas fa-file-export"></i> Phiếu giao hàng</a></li>
                    </ul>
                </li>

                <li>
                    <a href="{{ route('products.index') }}" class="menu-item {{ request()->routeIs('products.*') ? 'active' : '' }}">
                        <i class="fas fa-boxes"></i> <span>Quản lý tồn kho</span>
                    </a>
                </li>

                <li class="has-submenu {{ request()->routeIs('payments.*') || request()->routeIs('debt.*') ? 'open' : '' }}">
                    <div class="menu-item" onclick="toggleSubmenu(this)">
                        <i class="fas fa-folder-open"></i> <span>Quản lý công nợ</span>
                        <i class="fas fa-chevron-down arrow"></i>
                    </div>
                    <ul class="submenu {{ request()->routeIs('payments.*') || request()->routeIs('debt.*') ? 'show' : '' }}">
                        <li><a href="{{ route('debt.index') }}" class="submenu-item {{ request()->routeIs('debt.*') ? 'active' : '' }}"><i class="fas fa-exclamation-triangle"></i> Công nợ</a></li>
                        <li><a href="{{ route('payments.index') }}" class="submenu-item {{ request()->routeIs('payments.*') ? 'active' : '' }}"><i class="fas fa-hand-holding-usd"></i> Thanh toán</a></li>
                    </ul>
                </li>

                @if(auth()->user()->isAdmin())
                <li>
                    <a href="{{ route('admin.index') }}" class="menu-item {{ request()->routeIs('admin.index') ? 'active' : '' }}">
                        <i class="fas fa-user-shield"></i> <span>Quyền quản trị viên</span>
                    </a>
                </li>
                @endif
            </ul>
        </div>

        <div class="sidebar-footer">
            <div class="user-info">
                <div class="user-avatar">{{ mb_substr(auth()->user()->display_name, 0, 1) }}</div>
                <div class="user-text">
                    <div class="user-name">{{ auth()->user()->display_name }}</div>
                    <span class="user-role">{{ auth()->user()->role ?? 'Nhân viên' }}</span>
                </div>
            </div>
            
            <a href="javascript:void(0)" class="action-item-pro" onclick="openModal('modal-doi-matkhau')">
                <i class="fas fa-key"></i> <span>Đổi mật khẩu</span>
            </a>
            <form method="POST" action="{{ route('logout') }}" id="logout-form">
                @csrf
                <a href="javascript:void(0)" class="action-item-pro logout" onclick="document.getElementById('logout-form').submit()">
                    <i class="fas fa-sign-out-alt"></i> <span>Đăng xuất hệ thống</span>
                </a>
            </form>
            
            <p style="font-size: 11px; color: #475569; text-align: center; margin-top: 15px; font-weight: 600;">
                COPYRIGHT BY GAMBERTE @ 2026
            </p>
        </div>
    </aside>

    <div id="main-wrapper">
        <!-- <header id="topbar">
            <div class="topbar-content">
                <div class="page-header">
                    <h2>@yield('page-title', 'Dashboard')</h2>
                    <p>@yield('page-subtitle', 'Chào mừng quay trở lại!')</p>
                </div>
                
                <div style="display: flex; align-items: center; gap: 20px;">
                    <button class="ui-btn ui-btn-outline" style="border-radius: 6px; padding: 8px 15px;" onclick="location.reload()">
                        <i class="fas fa-sync-alt"></i> Tải lại
                    </button>
                    {{-- Avatar small for top bar just for aesthetic if needed, but legacy has it in sidebar footer --}}
                </div>
            </div>
        </header> -->

        <main id="content-area">
            @yield('content')
        </main>
    </div>

    {{-- MODALS --}}
    <div id="modal-doi-matkhau" class="modal-overlay">
        <div class="modal-box">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 2px solid #f1f5f9; padding-bottom: 15px;">
                <h3 style="font-weight: 800; font-size: 20px;"><i class="fas fa-key" style="color: var(--warning)"></i> Đổi Mật Khẩu</h3>
                <i class="fas fa-times" style="cursor: pointer; color: #94a3b8;" onclick="closeModal('modal-doi-matkhau')"></i>
            </div>
            <form action="{{ route('doi-mat-khau') }}" method="POST">
                @csrf
                <div style="margin-bottom: 15px;">
                    <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 8px;">Mật khẩu hiện tại</label>
                    <input type="password" name="mat_khau_cu" style="width: 100%; padding: 12px;  border-radius: 10px; outline: none; background: #f8fafc;" required>
                </div>
                <div style="margin-bottom: 15px;">
                    <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 8px;">Mật khẩu mới</label>
                    <input type="password" name="mat_khau_moi" style="width: 100%; padding: 12px;  border-radius: 10px; outline: none; background: #f8fafc;" required>
                </div>
                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 8px;">Xác nhận mật khẩu mới</label>
                    <input type="password" name="mat_khau_moi_confirmation" style="width: 100%; padding: 12px;  border-radius: 10px; outline: none; background: #f8fafc;" required>
                </div>
                <button type="submit" class="ui-btn ui-btn-primary" style="width: 100%; justify-content: center;">Cập nhật mật khẩu</button>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <div id="toast-container"></div>

    <script>
        function toggleSubmenu(el) {
            const parent = el.closest('.has-submenu');
            const submenu = parent.querySelector('.submenu');
            parent.classList.toggle('open');
            submenu.classList.toggle('show');
        }

        function openModal(id) { document.getElementById(id).classList.add('active'); }
        function closeModal(id) { document.getElementById(id).classList.remove('active'); }

        function showToast(msg, type = 'success') {
            Swal.fire({
                title: type === 'success' ? 'Thành công' : 'Lỗi',
                text: msg,
                icon: type,
                timer: 2000,
                showConfirmButton: false
            });
        }

        function showConfirm(title, text, callback) {
            Swal.fire({
                title: title,
                text: text,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Có, thực hiện!',
                cancelButtonText: 'Hủy'
            }).then((result) => {
                if (result.isConfirmed) {
                    callback();
                }
            });
        }

        // Auto override standard alert
        window.alert = function(msg) {
            Swal.fire({
                title: 'Thông báo',
                text: msg,
                icon: 'info',
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'Đóng'
            });
        };

        @if(session('success')) showToast("{{ session('success') }}", 'success'); @endif
        @if(session('error')) showToast("{{ session('error') }}", 'error'); @endif

        // Global functions for views
        async function apiPost(url, data) {
            return fetch(url, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                body: JSON.stringify(data)
            }).then(r => r.json());
        }
        async function apiPatch(url, data) {
            return fetch(url, {
                method: 'PATCH',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                body: JSON.stringify(data)
            }).then(r => r.json());
        }

        // Global Formatters
        function formatMoney(amount) {
            if (amount === undefined || amount === null || amount === '') return '0';
            return Number(amount).toLocaleString('vi-VN');
        }

        function formatQuantity(qty) {
            if (qty === undefined || qty === null || qty === '') return '0';
            // Show up to 3 decimals, remove trailing zeros
            return parseFloat(Number(qty).toFixed(3)).toLocaleString('vi-VN');
        }
    </script>
    @stack('scripts')
</body>
</html>
