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
            --primary: #0070D2;
            --primary-dark: #005bb5;
            --sidebar-bg: #ffffff;
            --sidebar-width: 260px;
            --header-height: 60px;
            --bg-body: #f1f5f9;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --radius-lg: 16px;
            --shadow-card: 0 4px 20px rgba(0,0,0,0.04);
            --font-family: 'Inter', sans-serif;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: var(--font-family); background: var(--bg-body); color: var(--text-main); display: flex; flex-direction: column; height: 100vh; overflow: hidden; }

        /* ═══════════════ HEADER ═══════════════ */
        #topbar {
            height: var(--header-height);
            background: var(--primary);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 24px;
            flex-shrink: 0;
            z-index: 1100;
            box-shadow: 0 2px 10px rgba(0,112,210,0.35);
        }

        .topbar-logo {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
        }
        .topbar-logo img {
            height: 38px;
            width: auto;
            object-fit: contain;
            filter: brightness(0) invert(1); /* make image white */
        }
        .topbar-logo-text {
            display: flex;
            flex-direction: column;
        }
        .topbar-logo-text .brand-name {
            font-size: 15px;
            font-weight: 800;
            color: #fff;
            letter-spacing: -0.3px;
            line-height: 1;
        }
        .topbar-logo-text .brand-sub {
            font-size: 10px;
            color: rgba(255,255,255,0.65);
            letter-spacing: 0.5px;
            text-transform: uppercase;
            margin-top: 2px;
        }

        /* Page title in center */
        .topbar-center {
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            color: rgba(255,255,255,0.9);
            font-size: 14px;
            font-weight: 600;
            pointer-events: none;
        }

        /* User area */
        .topbar-right { display: flex; align-items: center; gap: 12px; position: relative; }

        .topbar-user {
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
            padding: 5px 10px 5px 5px;
            border-radius: 50px;
            transition: background 0.2s;
        }
        .topbar-user:hover { background: rgba(255,255,255,0.15); }

        .topbar-avatar {
            width: 36px; height: 36px; border-radius: 50%;
            background: rgba(255,255,255,0.25);
            border: 2px solid rgba(255,255,255,0.5);
            display: flex; align-items: center; justify-content: center;
            font-weight: 800; font-size: 14px; color: #fff;
            overflow: hidden;
            flex-shrink: 0;
        }
        .topbar-avatar img { width: 100%; height: 100%; object-fit: cover; }

        .topbar-user-info { line-height: 1.2; }
        .topbar-user-name { font-size: 13px; font-weight: 700; color: #fff; }
        .topbar-user-role { font-size: 10.5px; color: rgba(255,255,255,0.65); }

        .topbar-chevron { color: rgba(255,255,255,0.7); font-size: 11px; transition: transform 0.2s; }
        .topbar-user.open .topbar-chevron { transform: rotate(180deg); }

        /* User dropdown */
        .user-dropdown {
            display: none;
            position: absolute;
            top: calc(100% + 10px);
            right: 0;
            width: 220px;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.18);
            border: 1px solid #e2e8f0;
            overflow: hidden;
            z-index: 9999;
            animation: dropIn 0.18s ease;
        }
        @keyframes dropIn { from { opacity: 0; transform: translateY(-8px); } to { opacity: 1; transform: translateY(0); } }
        .user-dropdown.show { display: block; }

        .dropdown-header {
            padding: 14px 16px;
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
        }
        .dropdown-header .dh-name { font-weight: 700; font-size: 15px; color: #0f172a; }
        .dropdown-header .dh-role { font-size: 11px; color: #64748b; margin-top: 2px; }

        .dropdown-item {
            display: flex; align-items: center; gap: 12px;
            padding: 11px 16px;
            color: #374151; font-size: 15px; font-weight: 500;
            text-decoration: none; cursor: pointer;
            transition: background 0.15s;
            border: none; background: transparent; width: 100%; text-align: left; font-family: inherit;
        }
        .dropdown-item i { width: 16px; text-align: center; color: #64748b; font-size: 13px; }
        .dropdown-item:hover { background: #f1f5f9; }
        .dropdown-item:hover i { color: var(--primary); }
        .dropdown-item.logout { color: var(--danger); }
        .dropdown-item.logout i { color: var(--danger); }
        .dropdown-item.logout:hover { background: #fef2f2; }
        .dropdown-divider { height: 1px; background: #e2e8f0; margin: 4px 0; }

        /* ═══════════════ BODY WRAP ═══════════════ */
        #body-wrap {
            display: flex;
            flex: 1;
            overflow: hidden;
        }

        /* ═══════════════ SIDEBAR (WHITE) ═══════════════ */
        #sidebar {
            width: var(--sidebar-width);
            background: var(--sidebar-bg);
            display: flex;
            flex-direction: column;
            height: 100%;
            flex-shrink: 0;
            border-right: 1px solid #e2e8f0;
            transition: all 0.3s ease;
            z-index: 1000;
            overflow-y: auto;
        }
        #sidebar::-webkit-scrollbar { width: 3px; }
        #sidebar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 4px; }

        .sidebar-section-title {
            font-size: 10.5px;
            font-weight: 700;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            padding: 18px 20px 6px;
        }

        .menu-container { flex: 1; padding: 12px 12px 20px; }

        .menu-list { list-style: none; }
        .menu-item {
            display: flex; align-items: center; gap: 11px;
            padding: 10px 14px; border-radius: 8px;
            color: #475569; font-size: 16px; font-weight: 500;
            text-decoration: none; cursor: pointer; transition: all 0.18s;
            margin-bottom: 2px; border: none; background: transparent; width: 100%; text-align: left;
        }
        .menu-item i { width: 18px; text-align: center; font-size: 15px; color: #94a3b8; transition: color 0.18s; }
        .menu-item span { flex: 1; }
        .menu-item:hover {
            background: #f0f7ff;
            color: var(--primary);
        }
        .menu-item:hover i { color: var(--primary); }
        .menu-item.active {
            background: #e8f2fd;
            color: var(--primary);
            font-weight: 700;
        }
        .menu-item.active i { color: var(--primary); }

        /* Submenu */
        .has-submenu { position: relative; }
        .submenu { list-style: none; padding-left: 14px; display: none; margin: 2px 0 4px; }
        .submenu.show { display: block; }
        .submenu-item {
            display: flex; align-items: center; gap: 10px;
            padding: 9px 14px 9px 16px; border-radius: 7px;
            color: #64748b; font-size: 15px; font-weight: 500;
            text-decoration: none; cursor: pointer; transition: all 0.18s;
        }
        .submenu-item i { font-size: 13px; color: #94a3b8; width: 16px; text-align: center; transition: color 0.18s; }
        .submenu-item:hover { background: #f0f7ff; color: var(--primary); }
        .submenu-item:hover i { color: var(--primary); }
        .submenu-item.active { color: var(--primary); font-weight: 700; background: #e8f2fd; }
        .submenu-item.active i { color: var(--primary); }

        .arrow { margin-left: auto; font-size: 10px; color: #94a3b8; transition: 0.3s; }
        .has-submenu.open .arrow { transform: rotate(180deg); }

        /* Sidebar bottom copyright */
        .sidebar-bottom {
            padding: 14px 20px;
            border-top: 1px solid #f1f5f9;
        }
        .sidebar-bottom p {
            font-size: 10.5px;
            color: #cbd5e1;
            text-align: center;
            font-weight: 500;
        }

        /* ═══════════════ MAIN CONTENT ═══════════════ */
        #main-wrapper { flex: 1; display: flex; flex-direction: column; overflow: hidden; }
        #content-area { flex: 1; overflow-y: auto; padding: 24px 28px 30px; }
        #content-area::-webkit-scrollbar { width: 5px; }
        #content-area::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 5px; }

        .topbar-content { flex: 1; display: flex; align-items: center; justify-content: space-between; }
        .page-header h2 { font-size: 24px; font-weight: 800; color: var(--text-main); margin: 0; }
        .page-header p { font-size: 13px; color: var(--text-muted); margin-top: 2px; font-weight: 500; }

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
        .ui-btn-primary { background: var(--primary); color: white; box-shadow: 0 4px 12px rgba(0,112,210,0.2); }
        .ui-btn-primary:hover { transform: translateY(-2px); box-shadow: 0 6px 15px rgba(0,112,210,0.25); }
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
        .swal2-container { z-index: 99999 !important; }

        /* PAGINATION OVERRIDES */
        .pagination { display: flex; list-style: none; gap: 5px; margin: 0; padding: 0; }
        .page-item .page-link { 
            padding: 8px 14px; border: 1.5px solid #e2e8f0; border-radius: 6px; 
            color: #475569; font-weight: 700; text-decoration: none; font-size: 13px;
            background: #fff; transition: 0.2s;
        }
        .page-item.active .page-link { background: #0070D2; color: #fff; border-color: #0070D2; box-shadow: 0 4px 10px rgba(0,112,210,0.25); }
        .page-item.disabled .page-link { color: #cbd5e1; background: #f8fafc; border-color: #f1f5f9; cursor: not-allowed; }
        .page-item:hover:not(.active):not(.disabled) .page-link { background: #f1f5f9; border-color: #cbd5e1; color: var(--primary); }

        @media (max-width: 992px) {
            #sidebar { position: fixed; left: calc(-1 * var(--sidebar-width)); top: var(--header-height); height: calc(100vh - var(--header-height)); }
            #sidebar.open { left: 0; }
        }
    </style>
    @stack('styles')
</head>
<body>

    <!-- ═══════════════ HEADER ═══════════════ -->
    <header id="topbar">
        <!-- Left: Logo -->
        <a href="{{ route('dashboard') }}" class="topbar-logo">
            <img src="https://i.ibb.co/whdGg4FK/Chat-GPT-Image-00-40-01-22-thg-3-2026.png" alt="GAMBERTE">
        </a>

        <!-- Right: User avatar + dropdown -->
        <div class="topbar-right">
            <div class="topbar-user" id="user-trigger" onclick="toggleUserDropdown()">
                <div class="topbar-avatar">
                    @if(auth()->user()->avatar ?? false)
                        <img src="{{ asset('storage/'.auth()->user()->avatar) }}" alt="avatar">
                    @else
                        {{ mb_substr(auth()->user()->display_name ?? 'U', 0, 1) }}
                    @endif
                </div>
                <div class="topbar-user-info">
                    <div class="topbar-user-name">{{ auth()->user()->display_name }}</div>
                    <div class="topbar-user-role">{{ auth()->user()->role ?? 'Nhân viên' }}</div>
                </div>
                <i class="fas fa-chevron-down topbar-chevron"></i>
            </div>

            <!-- Dropdown -->
            <div class="user-dropdown" id="user-dropdown">
                <div class="dropdown-header">
                    <div class="dh-name">{{ auth()->user()->display_name }}</div>
                    <div class="dh-role">{{ auth()->user()->role ?? 'Nhân viên' }}</div>
                </div>
                <button class="dropdown-item" onclick="openModal('modal-doi-matkhau'); closeUserDropdown();">
                    <i class="fas fa-key"></i> Đổi mật khẩu
                </button>
                <button class="dropdown-item" onclick="openModal('modal-doi-avatar'); closeUserDropdown();">
                    <i class="fas fa-camera"></i> Cập nhật ảnh đại diện
                </button>
                <div class="dropdown-divider"></div>
                <form method="POST" action="{{ route('logout') }}" id="logout-form-hd">
                    @csrf
                    <button type="button" class="dropdown-item logout" onclick="document.getElementById('logout-form-hd').submit()">
                        <i class="fas fa-sign-out-alt"></i> Đăng xuất
                    </button>
                </form>
            </div>
        </div>
    </header>

    <!-- ═══════════════ BODY WRAP ═══════════════ -->
    <div id="body-wrap">

        <!-- ═══════════════ SIDEBAR ═══════════════ -->
        <aside id="sidebar">
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
                            <li><a href="{{ route('customers.index') }}" class="submenu-item {{ request()->routeIs('customers.*') ? 'active' : '' }}">Khách hàng</a></li>
                            <li><a href="{{ route('orders.index') }}" class="submenu-item {{ request()->routeIs('orders.*') ? 'active' : '' }}">Đơn hàng (CTO)</a></li>
                            <li><a href="{{ route('deliveries.index') }}" class="submenu-item {{ request()->routeIs('deliveries.*') ? 'active' : '' }}">Phiếu giao hàng</a></li>
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
                            <li><a href="{{ route('debt.index') }}" class="submenu-item {{ request()->routeIs('debt.*') ? 'active' : '' }}">Công nợ</a></li>
                            <li><a href="{{ route('payments.index') }}" class="submenu-item {{ request()->routeIs('payments.*') ? 'active' : '' }}">Thanh toán</a></li>
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

            <div class="sidebar-bottom">
                <p>COPYRIGHT © GAMBERTE 2026</p>
            </div>
        </aside>

        <!-- ═══════════════ MAIN ═══════════════ -->
        <div id="main-wrapper">
            <main id="content-area">
                @yield('content')
            </main>
        </div>

    </div><!-- /#body-wrap -->

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

    {{-- MODAL ĐỔI ẢNH ĐẠI DIỆN --}}
    <div id="modal-doi-avatar" class="modal-overlay">
        <div class="modal-box" style="max-width: 420px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 2px solid #f1f5f9; padding-bottom: 15px;">
                <h3 style="font-weight: 800; font-size: 20px;"><i class="fas fa-camera" style="color: #0070D2"></i> Cập Nhật Ảnh Đại Diện</h3>
                <i class="fas fa-times" style="cursor: pointer; color: #94a3b8;" onclick="closeModal('modal-doi-avatar')"></i>
            </div>
                <div style="text-align: center; margin-bottom: 20px;">
                    <div style="width: 90px; height: 90px; border-radius: 50%; background: #e8f2fd; border: 3px solid #0070D2; display: flex; align-items: center; justify-content: center; margin: 0 auto 10px; overflow: hidden; font-size: 32px; font-weight: 800; color: #0070D2;">
                        @if(auth()->user()->avatar ?? false)
                            <img src="{{ asset('storage/'.auth()->user()->avatar) }}" style="width:100%;height:100%;object-fit:cover;">
                        @else
                            {{ mb_substr(auth()->user()->display_name ?? 'U', 0, 1) }}
                        @endif
                    </div>
                    <p style="font-size: 12px; color: #94a3b8;">Ảnh hiện tại của bạn</p>
                </div>
                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 8px;">Chọn ảnh mới</label>
                    <input type="file" name="avatar" accept="image/*" style="width: 100%; padding: 10px; border: 1.5px dashed #cbd5e1; border-radius: 8px; background: #f8fafc; cursor: pointer; font-size: 13px;">
                    <p style="font-size: 11px; color: #94a3b8; margin-top: 6px;">Hỗ trợ: JPG, PNG, GIF (tối đa 2MB)</p>
                </div>
                <button type="submit" class="ui-btn ui-btn-primary" style="width: 100%; justify-content: center; background: #0070D2;">
                    <i class="fas fa-upload"></i> Cập nhật ảnh đại diện
                </button>
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

        function toggleUserDropdown() {
            const trigger = document.getElementById('user-trigger');
            const dropdown = document.getElementById('user-dropdown');
            trigger.classList.toggle('open');
            dropdown.classList.toggle('show');
        }
        function closeUserDropdown() {
            document.getElementById('user-trigger').classList.remove('open');
            document.getElementById('user-dropdown').classList.remove('show');
        }
        // Close on outside click
        document.addEventListener('click', function(e) {
            const trigger  = document.getElementById('user-trigger');
            const dropdown = document.getElementById('user-dropdown');
            if (trigger && !trigger.contains(e.target) && dropdown && !dropdown.contains(e.target)) {
                closeUserDropdown();
            }
        });

        function openModal(id) { document.getElementById(id).classList.add('active'); }
        function closeModal(id) { document.getElementById(id).classList.remove('active'); }

        function showToast(msg, type = 'success') {
            return Swal.fire({
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
