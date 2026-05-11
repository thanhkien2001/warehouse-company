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
            --primary: #002B6B;
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
        body { font-family: var(--font-family); background: var(--bg-body); color: var(--text-main); display: flex; height: 100vh; overflow: hidden; }
        
        input[type="date"], input[type="text"], input[type="number"], select, textarea {
            font-family: inherit !important;
        }

        /* ═══════════════ HEADER ═══════════════ */
        #topbar {
            height: var(--header-height);
            background: white;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 20px;
            flex-shrink: 0;
            z-index: 900;
            border-bottom: 1px solid #e2e8f0;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            gap: 12px;
        }

        /* Topbar Left – Company Name */
        .topbar-left {
            display: flex;
            flex-direction: column;
            justify-content: center;
            line-height: 1.35;
            flex-shrink: 0;
        }
        .topbar-left .company-row1 {
            font-size: 14px;
            font-weight: 600;
            color: #475569;
            letter-spacing: 0.2px;
        }
        .topbar-left .company-row2 {
            font-size: 14px;
            font-weight: 800;
            color: #002B6B;
            letter-spacing: 0.3px;
        }

        /* Topbar Center – Search */
        .topbar-center {
            flex: 1;
            max-width: 512px;
            margin: 0 auto;
        }
        .topbar-search {
            display: flex;
            align-items: center;
            background: #fff;
            border: 1.5px solid #e2e8f0;
            border-radius: 5px;
            padding: 0 14px 0 18px;
            gap: 8px;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .topbar-search:focus-within {
            border-color: #002B6B;
            box-shadow: 0 0 0 3px rgba(0,112,210,0.1);
        }
        .topbar-search input {
            flex: 1;
            border: none;
            background: transparent;
            outline: none;
            font-size: 13px;
            color: #1e293b;
            height: 36px;
            font-family: inherit;
        }
        .topbar-search input::placeholder { color: #94a3b8; }
        .topbar-search .search-icon {
            color: #94a3b8;
            font-size: 14px;
            flex-shrink: 0;
            cursor: pointer;
            transition: color 0.2s;
        }
        .topbar-search:focus-within .search-icon { color: #002B6B; }

        /* Topbar icon buttons (bell, question) */
        .topbar-icon-btn {
            position: relative;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: #fff;
            border: 1.5px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: #64748b;
            font-size: 15px;
            transition: all 0.2s;
            flex-shrink: 0;
        }
        .topbar-icon-btn:hover {
            background: #002B6B;
            color: #fff;
            border-color: #002B6B;
            box-shadow: 0 4px 12px rgba(0,112,210,0.25);
        }
        .topbar-icon-btn .notif-badge {
            position: absolute;
            top: -3px;
            right: -3px;
            width: 16px;
            height: 16px;
            background: #ef4444;
            border-radius: 50%;
            font-size: 9px;
            font-weight: 800;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid #EFF6FF;
            z-index: 2;
        }

        /* Notification Dropdown */
        .notif-dropdown {
            display: none;
            position: absolute;
            top: calc(100% + 8px);
            right: -100px;
            width: 320px;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.18);
            border: 1px solid #e2e8f0;
            z-index: 9999;
            animation: dropIn 0.18s ease;
            cursor: default;
        }
        .topbar-icon-btn:hover .notif-dropdown { display: block; }
        
        .notif-header {
            padding: 12px 16px;
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            font-weight: 700;
            font-size: 14px;
            color: #0f172a;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .notif-body {
            max-height: 400px;
            overflow-y: auto;
        }
        .notif-item {
            display: flex;
            gap: 12px;
            padding: 12px 16px;
            border-bottom: 1px solid #f1f5f9;
            transition: background 0.15s;
            text-decoration: none;
        }
        .notif-item:hover { background: #f8fafc; }
        .notif-item:last-child { border-bottom: none; }
        
        .notif-item-icon {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #eff6ff;
            color: #3b82f6;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            flex-shrink: 0;
        }
        .notif-item-content { flex: 1; min-width: 0; }
        .notif-item-title {
            font-size: 13px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 2px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .notif-item-desc {
            font-size: 12px;
            color: #64748b;
            line-height: 1.4;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .notif-item-time {
            font-size: 11px;
            color: #94a3b8;
            margin-top: 4px;
        }
        .notif-footer {
            padding: 10px;
            text-align: center;
            border-top: 1px solid #e2e8f0;
            font-size: 12px;
            font-weight: 600;
            color: #002B6B;
            cursor: pointer;
        }
        .notif-footer:hover { background: #f1f5f9; }


        /* Language Switcher */
        .lang-switcher {
            position: relative;
            flex-shrink: 0;
        }
        .lang-btn {
            display: flex;
            align-items: center;
            gap: 6px;
            height: 36px;
            padding: 0 12px 0 8px;
            background: #fff;
            border: 1.5px solid #e2e8f0;
            border-radius: 50px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 600;
            color: #374151;
            transition: all 0.2s;
            white-space: nowrap;
        }
        .lang-btn:hover {
            border-color: #002B6B;
            box-shadow: 0 0 0 3px rgba(0,112,210,0.1);
        }
        .lang-btn .lang-flag { width: 22px; height: 22px; object-fit: cover; border-radius: 3px; flex-shrink: 0; }
        .lang-btn .lang-chevron { font-size: 10px; color: #94a3b8; transition: transform 0.2s; margin-left: 2px; }
        .lang-switcher.open .lang-chevron { transform: rotate(180deg); }

        .lang-dropdown {
            display: none;
            position: absolute;
            top: calc(100% + 8px);
            right: 0;
            min-width: 150px;
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
            overflow: hidden;
            z-index: 9999;
            animation: dropIn 0.18s ease;
        }
        .lang-switcher.open .lang-dropdown { display: block; }
        .lang-option {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 16px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 600;
            color: #374151;
            transition: background 0.15s;
            border: none;
            background: transparent;
            width: 100%;
            text-align: left;
            font-family: inherit;
        }
        .lang-option:hover { background: #f1f5f9; color: #002B6B; }
        .lang-option.active { color: #002B6B; background: #eff6ff; }
        .lang-option .lang-flag { width: 22px; height: 22px; object-fit: cover; border-radius: 3px; flex-shrink: 0; }

        /* User area */
        .topbar-right { display: flex; align-items: center; gap: 15px; position: relative; }

        .topbar-user {
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
            padding: 5px 10px 5px 5px;
            border-radius: 50px;
            transition: background 0.2s;
        }
        .topbar-user:hover { background: #f8fafc; }

        .topbar-avatar {
            width: 36px; height: 36px; border-radius: 50%;
            background: #f1f5f9;
            border: 1px solid #e2e8f0;
            display: flex; align-items: center; justify-content: center;
            font-weight: 800; font-size: 14px; color: var(--primary);
            overflow: hidden;
            flex-shrink: 0;
        }
        .topbar-avatar img { width: 100%; height: 100%; object-fit: cover; }

        .topbar-user-info { line-height: 1.2; }
        .topbar-user-name { font-size: 14px; font-weight: 700; color: #1e293b; }
        .topbar-user-role { font-size: 10.5px; color: #64748b; }

        .topbar-chevron { color: #94a3b8; font-size: 11px; transition: transform 0.2s; }
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
            background: #002B6B; /* Changed to dark blue as in user screenshot */
            display: flex;
            flex-direction: column;
            height: 100vh;
            flex-shrink: 0;
            transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1), transform 0.3s ease;
            z-index: 1000;
            position: relative;
            color: white;
        }
        
        .sidebar-header {
            height: var(--header-height);
            display: flex;
            align-items: center;
            justify-content: center;
            background: #002B6B;
            margin-top :15px
        }
        .sidebar-logo {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
        }
        .sidebar-logo img {
            height: 64px;
            width: auto;
            object-fit: contain;
            filter: brightness(0) invert(1);
        }

        #sidebar-inner {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow-y: auto;
            overflow-x: hidden;
        }
        #sidebar-inner::-webkit-scrollbar { width: 3px; }
        #sidebar-inner::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 4px; }

        /* Sidebar Toggle Button */
        #sidebar-toggle {
            position: absolute;
            right: -18px;
            top: 50%;
            transform: translateY(-50%);
            width: 36px;
            height: 36px;
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            z-index: 1100;
            box-shadow: 0 4px 12px rgba(0,0,0,0.12);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            color: var(--primary);
            font-size: 16px;
        }
        #sidebar-toggle:hover {
            background: var(--primary);
            color: #fff;
            border-color: var(--primary);
            box-shadow: 0 6px 16px rgba(0,112,210,0.3);
            transform: translateY(-50%) scale(1.1);
        }

        /* Collapsed State */
        body.sidebar-collapsed #sidebar {
            width: 0;
            border-right: none;
        }
        body.sidebar-collapsed #sidebar-toggle {
            right: -18px;
            transform: translateY(-50%) rotate(180deg);
        }
        body.sidebar-collapsed #sidebar-inner {
            visibility: hidden;
            opacity: 0;
            transition: visibility 0s 0.3s, opacity 0.2s linear;
        }

        .sidebar-section-title {
            font-size: 10.5px;
            font-weight: 700;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            padding: 18px 20px 6px;
        }

        .menu-container { flex: 1; padding: 12px 12px 20px; background: #002B6B;}

        .menu-list { list-style: none; }
        .menu-item {
            display: flex; align-items: center; gap: 11px;
            padding: 10px 14px; border-radius: 8px;
            color: white; font-size: 15px; font-weight: 500;
            text-decoration: none; cursor: pointer; transition: all 0.18s;
            margin-bottom: 2px; border: none; background: transparent; width: 100%; text-align: left;
        }
        .menu-item i { width: 18px; text-align: center; font-size: 15px; color: white; transition: color 0.18s; }
        .menu-item span { flex: 1; }
        .menu-item:hover {
            background: rgba(255,255,255,0.1);
            color: #fff;
        }
        .menu-item:hover i { color: #fff; }
        .menu-item.active {
            background: #fff;
            color: #002B6B;
            font-weight: 700;
        }
        .menu-item.active i { color: #002B6B; }

        /* Submenu */
        .has-submenu { position: relative; }
        .submenu { list-style: none; padding-left: 14px; display: block; margin: 2px 0 4px; }
        .submenu.show { display: block; }
        .submenu-item {
            display: flex; align-items: center; gap: 10px;
            padding: 9px 14px 9px 16px; border-radius: 7px;
            color: white; font-size: 14px;
            text-decoration: none; cursor: pointer; transition: all 0.18s;
        }
        .submenu-item i { font-size: 13px; color: white; width: 16px; text-align: center; transition: color 0.18s; }
        .submenu-item:hover { background: rgba(255,255,255,0.1); color: #fff; }
        .submenu-item:hover i { color: #fff; }
        .submenu-item.active { color: #002B6B; font-weight: 700; background: #fff; }
        .submenu-item.active i { color: #002B6B; }

        .arrow { margin-left: auto; font-size: 10px; color: rgba(255,255,255,0.5); transition: 0.3s; transform: rotate(0deg); }
        .has-submenu.open .arrow { transform: rotate(180deg); }

        /* Sidebar bottom copyright */
        .sidebar-bottom {
            padding: 14px 20px;
            background-color: #002B6B;
            border-top: 1px solid #f1f5f9;
        }
        .sidebar-bottom p {
            font-size: 10.5px;
            color: white;
            text-align: center;
            font-weight: 500;
        }

        /* ═══════════════ MAIN CONTENT ═══════════════ */
        #main-wrapper { flex: 1; display: flex; flex-direction: column; overflow: hidden; }
        #content-area { flex: 1; overflow-y: auto; padding: 24px 28px 30px; background-color: white;}
        #content-area::-webkit-scrollbar { width: 5px; }
        #content-area::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 5px; }

        .topbar-content { flex: 1; display: flex; align-items: center; justify-content: space-between; }
        .page-header h2 { font-size: 24px; font-weight: 800; color: var(--text-main); margin: 0; }
        .page-header p { font-size: 13px; color: var(--text-muted); margin-top: 2px; font-weight: 500; }

        /* COMMON COMPONENTS (Legacy Parity) */
        .card { background: #fff; border-radius: var(--radius-lg); box-shadow: var(--shadow-card); padding: 24px; border: 1px solid #f1f5f9; }
        
        /* TABLE STYLE */
        .legacy-table-container {overflow: hidden; background: #fff; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); }
        .legacy-table { width: 100%; border-collapse: collapse; border:1px solid #e2e8f0;}
        .legacy-table thead th { background: #EFF6FF; padding: 14px 12px; color: black !important; font-size: 12px !important; font-weight: 700 !important; text-transform: uppercase; border-bottom: 2px solid #edf2f7; text-align: center; letter-spacing: 0.5px; border:1px solid #e2e8f0; white-space: nowrap; }
        .legacy-table tbody td { padding: 8px; border-bottom: 1px solid #f1f5f9; font-size: 13px; color: #1e293b; border:1px solid #e2e8f0; text-align: center; }
        .legacy-table tbody tr:last-child td { border-bottom: none; }
        .legacy-table tbody tr:hover { background: #f8fafc; }

        /* Cell Alignments */
        .col-left { text-align: left !important; }
        .col-center { text-align: center !important; }
        .col-right { text-align: right !important; }
        
        /* STATUS BADGES */
        .badge-status { display: inline-block; text-align: center; padding: 6px 4px; border-radius: 50px; font-size: 11px; font-weight: 700; white-space: nowrap; }
        .badge-status.cho-xac-nhan { color: #E67E22; }
        .badge-status.dang-xu-ly { color: #3498DB; }
        .badge-status.da-huy { color: #E74C3C; }
        .badge-status.hoan-thanh, .badge-status.da-giao-xong { color: #27AE60;}
        .badge-status.dang-van-chuyen, .badge-status.dang-giao { color: #8E44AD;}
        .badge-status.cho-giao-hang { color: #F39C12; }

        /* REGION BADGES */
        .badge-region { padding: 4px 12px; font-size: 11px; font-weight: 700; white-space: nowrap; display: inline-block; min-width: 80px; text-align: center; }
        .badge-region.mien-bac { color: #3b82f6; border-radius: 20px; background: #eff6ff; }
        .badge-region.mien-trung { color: #f97316; border-radius: 20px; background: #fff7ed; }
        .badge-region.mien-nam { color: #10b981; border-radius: 20px; background: #ecfdf5; }

        /* MODAL (Clean Modern) */
        .modal-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 10000; align-items: center; justify-content: center; }
        .modal-overlay.active { display: flex; }
        .modal-box { background: #fff; border-radius: 20px; width: 95%; max-width: 500px; padding: 25px; box-shadow: 0 15px 40px rgba(0,0,0,0.2); animation: zoomIn 0.3s; }
        @keyframes zoomIn { from { transform: scale(0.9); opacity: 0; } to { transform: scale(1); opacity: 1; } }
        
        /* BUTTONS */
        .ui-btn { padding: 10px 20px; border-radius: 6px; font-weight: 600; cursor: pointer; border: none; transition: 0.3s; font-size: 14px; display: inline-flex; align-items: center; gap: 8px; text-decoration: none; font-family: inherit; }
        .ui-btn-primary { background: var(--primary); color: white; box-shadow: 0 4px 12px rgba(0,112,210,0.2); }
        .ui-btn-primary:hover { transform: translateY(-2px); box-shadow: 0 6px 15px rgba(0,112,210,0.25); }
        .ui-btn-outline { background: #fff; color: #002B6B; border: 1px solid #002B6B; }
        .ui-btn-outline:hover { background: #f8fafc; border-color: #94a3b8; }
        
        /* STANDARDIZED BUTTONS */
        .ui-btn-pdf { background: #FF0000 !important; color: white !important; }
        .ui-btn-pdf:hover { background: #cc0000 !important; }
        .ui-btn-save { background: #002B6B !important; color: white !important; }
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
        .page-item.active .page-link { background: #002B6B; color: #fff; border-color: #002B6B; box-shadow: 0 4px 10px rgba(0,112,210,0.25); }
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
    <!-- ═══════════════ SIDEBAR ═══════════════ -->
    <aside id="sidebar">
        <div id="sidebar-toggle" onclick="toggleSidebar()">
            <i class="fas fa-chevron-left"></i>
        </div>
        
        <div id="sidebar-inner">
            <div class="sidebar-header">
                <a href="{{ route('dashboard') }}" class="sidebar-logo">
                    <img src="{{ asset('images/logo_gambete.png') }}" alt="GAMBERTE">
                </a>
            </div>
            <div class="menu-container">
                <ul class="menu-list">
                    <li>
                        <a href="{{ route('dashboard') }}" class="menu-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                            <i class="fas fa-home"></i> <span>Tổng quan</span>
                        </a>
                    </li>
                    @if(auth()->user()->canDo('sanpham', 'view'))
                    <li class="has-submenu {{ request()->routeIs('catalog.*') ? 'open' : '' }}">
                        <div class="menu-item" onclick="toggleSubmenu(this)">
                            <i class="fas fa-th-large"></i> <span>Danh mục</span>
                            <i class="fas fa-chevron-down arrow"></i>
                        </div>
                        <ul class="submenu {{ request()->routeIs('catalog.*') ? 'show' : '' }}">
                            <li><a href="{{ route('catalog.index') }}" class="submenu-item {{ request()->routeIs('catalog.*') ? 'active' : '' }}">Sản phẩm</a></li>
                        </ul>
                    </li>
                    @endif
                    @php
                        $canKH = auth()->user()->canDo('taomakhachhang', 'view');
                        $canDH = auth()->user()->canDo('taodonhang', 'view');
                        $canPG = auth()->user()->canDo('taophieugiao', 'view');
                    @endphp
                    @if($canKH || $canDH || $canPG)
                    <li class="has-submenu {{ request()->routeIs('orders.*') || request()->routeIs('customers.*') || request()->routeIs('deliveries.*') ? 'open' : '' }}">
                        <div class="menu-item" onclick="toggleSubmenu(this)">
                            <i class="fas fa-folder-open"></i> <span>Quản lý đơn hàng</span>
                            <i class="fas fa-chevron-down arrow"></i>
                        </div>
                        <ul class="submenu {{ request()->routeIs('orders.*') || request()->routeIs('customers.*') || request()->routeIs('deliveries.*') ? 'show' : '' }}">
                            @if($canKH) <li><a href="{{ route('customers.index') }}" class="submenu-item {{ request()->routeIs('customers.*') ? 'active' : '' }}">Tạo mã khách hàng</a></li> @endif
                            @if($canDH) <li><a href="{{ route('orders.index') }}" class="submenu-item {{ request()->routeIs('orders.*') ? 'active' : '' }}">Tạo đơn hàng</a></li> @endif
                            @if($canPG) <li><a href="{{ route('deliveries.index') }}" class="submenu-item {{ request()->routeIs('deliveries.*') ? 'active' : '' }}">Tạo phiếu giao hàng</a></li> @endif
                        </ul>
                    </li>
                    @endif

                    @php
                        $canNK = auth()->user()->canDo('nhapkho', 'view');
                        $canX = auth()->user()->canDo('baocaoxuatkho', 'view');
                        $canT = auth()->user()->canDo('baocaotonkho', 'view');
                    @endphp
                    @if($canNK || $canX || $canT)
                    <li class="has-submenu {{ request()->routeIs('inventory.*') ? 'open' : '' }}">
                        <div class="menu-item" onclick="toggleSubmenu(this)">
                            <i class="fas fa-boxes"></i> <span>Quản lý tồn kho</span>
                            <i class="fas fa-chevron-down arrow"></i>
                        </div>
                        <ul class="submenu {{ request()->routeIs('inventory.*') ? 'show' : '' }}">
                            @if($canNK) <li><a href="{{ route('inventory.inbound') }}" class="submenu-item {{ request()->routeIs('inventory.inbound') ? 'active' : '' }}">Nhập kho</a></li> @endif
                            @if($canX) <li><a href="{{ route('inventory.outbound-report') }}" class="submenu-item {{ request()->routeIs('inventory.outbound-report') ? 'active' : '' }}">Báo cáo xuất kho</a></li> @endif
                            @if($canT) <li><a href="{{ route('inventory.stock-report') }}" class="submenu-item {{ request()->routeIs('inventory.stock-report') ? 'active' : '' }}">Báo cáo tồn kho</a></li> @endif
                        </ul>
                    </li>
                    @endif

                    @php
                        $canCN = auth()->user()->canDo('congno', 'view');
                        $canTT = auth()->user()->canDo('thanhtoan', 'view');
                    @endphp
                    @if($canCN || $canTT)
                    <li class="has-submenu {{ request()->routeIs('payments.*') || request()->routeIs('debt.*') ? 'open' : '' }}">
                        <div class="menu-item" onclick="toggleSubmenu(this)">
                            <i class="fas fa-wallet"></i> <span>Quản lý công nợ</span>
                            <i class="fas fa-chevron-down arrow"></i>
                        </div>
                        <ul class="submenu {{ request()->routeIs('payments.*') || request()->routeIs('debt.*') ? 'show' : '' }}">
                            @if($canCN) <li><a href="{{ route('debt.index') }}" class="submenu-item {{ request()->routeIs('debt.*') ? 'active' : '' }}">Công nợ</a></li> @endif
                            @if($canTT) <li><a href="{{ route('payments.index') }}" class="submenu-item {{ request()->routeIs('payments.*') ? 'active' : '' }}">Thanh toán</a></li> @endif
                        </ul>
                    </li>
                    @endif

                    @php
                        $canTC = auth()->user()->canDo('baocaotc', 'view');
                        $canTH = auth()->user()->canDo('baocaoth', 'view');
                    @endphp
                    @if($canTC || $canTH)
                    <li class="has-submenu {{ request()->routeIs('reports.*') ? 'open' : '' }}">
                        <a href="#" class="menu-item" onclick="toggleSubmenu(this)">
                            <i class="fas fa-chart-pie"></i> <span>Báo cáo tài chính</span>
                            <i class="fas fa-chevron-down submenu-arrow"></i>
                        </a>
                        <ul class="submenu {{ request()->routeIs('reports.*') ? 'open' : '' }}">
                            @if($canTC) <li><a href="{{ route('reports.finance') }}" class="submenu-item {{ request()->routeIs('reports.finance') ? 'active' : '' }}">Biểu đồ tài chính</a></li> @endif
                            @if($canTH) <li><a href="{{ route('reports.summary') }}" class="submenu-item {{ request()->routeIs('reports.summary') ? 'active' : '' }}">Báo cáo tổng hợp</a></li> @endif
                        </ul>
                    </li>
                    @endif

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
        </div>
    </aside>

    <!-- ═══════════════ MAIN WRAPPER ═══════════════ -->
    <div id="main-wrapper">
        <!-- ═══════════════ TOPBAR (RIGHT ONLY) ═══════════════ -->
        <header id="topbar">
            {{-- Topbar Left: Company Name --}}
            <div class="topbar-left">
                <span class="company-row1">CÔNG TY TNHH</span>
                <span class="company-row2">GAMBERTE VIỆT NAM</span>
            </div>

            {{-- Topbar Center: Search --}}
            <div class="topbar-center">
                <div class="topbar-search">
                    <input type="text" placeholder="Tìm kiếm thông tin...">
                    <i class="fas fa-search search-icon"></i>
                </div>
            </div>

            <div class="topbar-right">
                {{-- Bell Notification --}}
                <div class="topbar-icon-btn" title="Thông báo">
                    <i class="fas fa-bell"></i>
                    @if(count($latestActivities ?? []) > 0)
                        <span class="notif-badge">{{ count($latestActivities) }}</span>
                    @endif
                    
                    <div class="notif-dropdown">
                        <div class="notif-header">
                            <span>Thông báo mới nhất</span>
                            <i class="fas fa-ellipsis-h" style="color: #94a3b8; font-size: 12px;"></i>
                        </div>
                        <div class="notif-body">
                            @forelse($latestActivities ?? [] as $act)
                                <div class="notif-item">
                                    <div class="notif-item-icon">
                                        <i class="fas {{ str_contains(strtolower($act->action), 'xóa') ? 'fa-trash-alt' : (str_contains(strtolower($act->action), 'tạo') ? 'fa-plus-circle' : 'fa-edit') }}" 
                                           style="color: {{ str_contains(strtolower($act->action), 'xóa') ? '#ef4444' : (str_contains(strtolower($act->action), 'tạo') ? '#10b981' : '#3b82f6') }}"></i>
                                    </div>
                                    <div class="notif-item-content">
                                        <div class="notif-item-title">{{ $act->action }}</div>
                                        <div class="notif-item-time">
                                            {{ $act->user->display_name ?? 'Hệ thống' }} · {{ $act->created_at->diffForHumans() }}
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div style="padding: 30px 20px; text-align: center; color: #94a3b8; font-size: 13px;">
                                    <i class="fas fa-bell-slash" style="font-size: 24px; margin-bottom: 10px; opacity: 0.5;"></i>
                                    <div>Không có thông báo mới</div>
                                </div>
                            @endforelse
                        </div>
                        <div class="notif-footer">Xem tất cả hoạt động</div>
                    </div>
                </div>
                {{-- Question / Help --}}
                <div class="topbar-icon-btn" title="Trợ giúp">
                    <i class="fa-regular fa-circle-question"></i>
                </div>
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
                        <div class="topbar-user-role">{{ auth()->user()->chuc_danh ?? 'Nhân viên' }}</div>
                    </div>
                    <i class="fas fa-chevron-down topbar-chevron"></i>
                </div>

                <!-- Dropdown -->
                <div class="user-dropdown" id="user-dropdown">
                    <div class="dropdown-header">
                        <div class="dh-name">{{ auth()->user()->display_name }}</div>
                        <div class="dh-role">{{ auth()->user()->chuc_danh ?? 'Nhân viên' }}</div>
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
                {{-- Language Switcher --}}
                <div class="lang-switcher" id="lang-switcher">
                    <button class="lang-btn" id="lang-trigger" onclick="toggleLangDropdown()">
                        <img src="{{ asset('images/vietnam_logo.png') }}" class="lang-flag" alt="VI" id="lang-trigger-flag">
                        <span id="lang-trigger-text">VI</span>
                        <i class="fas fa-chevron-down lang-chevron"></i>
                    </button>
                    <div class="lang-dropdown" id="lang-dropdown">
                        <button class="lang-option active" onclick="setLang('vi', this)">
                            <img src="{{ asset('images/vietnam_logo.png') }}" class="lang-flag" alt="VI"> Tiếng Việt
                        </button>
                        <button class="lang-option" onclick="setLang('en', this)">
                            <img src="{{ asset('images/us_logo.png') }}" class="lang-flag" alt="EN"> English
                        </button>
                    </div>
                </div>
            </div>
        </header>

        <!-- ═══════════════ CONTENT AREA ═══════════════ -->
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
                <h3 style="font-weight: 800; font-size: 20px;"><i class="fas fa-camera" style="color: #002B6B"></i> Cập Nhật Ảnh Đại Diện</h3>
                <i class="fas fa-times" style="cursor: pointer; color: #94a3b8;" onclick="closeModal('modal-doi-avatar')"></i>
            </div>
                <div style="text-align: center; margin-bottom: 20px;">
                    <div style="width: 90px; height: 90px; border-radius: 50%; background: #e8f2fd; border: 3px solid #002B6B; display: flex; align-items: center; justify-content: center; margin: 0 auto 10px; overflow: hidden; font-size: 32px; font-weight: 800; color: #002B6B;">
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
                <button type="submit" class="ui-btn ui-btn-primary" style="width: 100%; justify-content: center; background: #002B6B;">
                    <i class="fas fa-upload"></i> Cập nhật ảnh đại diện
                </button>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <div id="toast-container"></div>

    <script>
        // Sidebar Toggle Logic
        function toggleSidebar() {
            document.body.classList.toggle('sidebar-collapsed');
            // Save state to localStorage
            const isCollapsed = document.body.classList.contains('sidebar-collapsed');
            localStorage.setItem('sidebarCollapsed', isCollapsed);
        }

        // Restore sidebar state on load
        document.addEventListener('DOMContentLoaded', function() {
            const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
            if (isCollapsed) {
                document.body.classList.add('sidebar-collapsed');
            }
        });

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
        // Close user dropdown on outside click
        document.addEventListener('click', function(e) {
            const trigger  = document.getElementById('user-trigger');
            const dropdown = document.getElementById('user-dropdown');
            if (trigger && !trigger.contains(e.target) && dropdown && !dropdown.contains(e.target)) {
                closeUserDropdown();
            }
            // Close lang dropdown on outside click
            const langSwitcher = document.getElementById('lang-switcher');
            if (langSwitcher && !langSwitcher.contains(e.target)) {
                langSwitcher.classList.remove('open');
            }
        });

        // Language Switcher
        function toggleLangDropdown() {
            document.getElementById('lang-switcher').classList.toggle('open');
        }
        const langLabels = {
            vi: { src: '{{ asset("images/vietnam_logo.png") }}', text: 'VI' },
            en: { src: '{{ asset("images/us_logo.png") }}', text: 'EN' }
        };
        function setLang(code, btn) {
            const { src, text } = langLabels[code];
            document.getElementById('lang-trigger-flag').src = src;
            document.getElementById('lang-trigger-flag').alt = text;
            document.getElementById('lang-trigger-text').textContent = text;
            document.querySelectorAll('.lang-option').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            document.getElementById('lang-switcher').classList.remove('open');
        }

        function openModal(id) { document.getElementById(id).classList.add('active'); }
        function closeModal(id) { document.getElementById(id).classList.remove('active'); }

        function showToast(msg, type = 'success') {
            let title = 'Thông báo';
            if (type === 'success') title = 'Thành công';
            if (type === 'error') title = 'Lỗi';
            if (type === 'warning') title = 'Cảnh báo';

            return Swal.fire({
                title: title,
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
            if (amount === undefined || amount === null || amount === '') return '0,00';
            return Number(amount).toLocaleString('vi-VN', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
        }

        function formatQuantity(qty) {
            if (qty === undefined || qty === null || qty === '') return '0,00';
            return Number(qty).toLocaleString('vi-VN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }
    </script>
    @stack('scripts')
</body>
</html>
