<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập – GAMBERTE WMS</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body, html { height: 100%; font-family: 'Inter', sans-serif; overflow: hidden; }

        /* ── FULL-SCREEN BACKGROUND ── */
        .login-bg {
            position: fixed;
            inset: 0;
            background: linear-gradient(145deg, #060d1f 0%, #0a1a3e 40%, #0d2a5e 70%, #0a1540 100%);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Animated grid overlay */
        .login-bg::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(0,112,210,0.1) 1px, transparent 1px),
                linear-gradient(90deg, rgba(0,112,210,0.1) 1px, transparent 1px);
            background-size: 60px 60px;
            animation: gridScroll 25s linear infinite;
        }
        @keyframes gridScroll { from { transform: translateY(0); } to { transform: translateY(60px); } }

        /* Glowing blobs */
        .blob {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            pointer-events: none;
        }
        .blob-1 {
            width: 500px; height: 500px;
            background: radial-gradient(circle, rgba(0,112,210,0.22) 0%, transparent 70%);
            top: -100px; left: -100px;
            animation: blobMove 8s ease-in-out infinite;
        }
        .blob-2 {
            width: 400px; height: 400px;
            background: radial-gradient(circle, rgba(67,24,255,0.18) 0%, transparent 70%);
            bottom: -80px; right: -80px;
            animation: blobMove 10s ease-in-out infinite reverse;
        }
        .blob-3 {
            width: 300px; height: 300px;
            background: radial-gradient(circle, rgba(0,180,255,0.12) 0%, transparent 70%);
            top: 40%; left: 60%;
            animation: blobMove 12s ease-in-out infinite 2s;
        }
        @keyframes blobMove {
            0%, 100% { transform: translate(0, 0) scale(1); }
            33%       { transform: translate(30px, -30px) scale(1.08); }
            66%       { transform: translate(-20px, 20px) scale(0.95); }
        }

        /* Floating icons scattered in background */
        .bg-icon {
            position: absolute;
            width: 48px; height: 48px;
            background: rgba(255,255,255,0.04);
            backdrop-filter: blur(4px);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: rgba(96,165,250,0.5);
            font-size: 18px;
            pointer-events: none;
            animation: floatDrift 6s ease-in-out infinite;
        }
        .bg-icon:nth-child(1)  { top: 12%; left: 8%;   animation-delay: 0s;    animation-duration: 5.5s; }
        .bg-icon:nth-child(2)  { top: 20%; right: 12%;  animation-delay: 1s;    animation-duration: 6.2s; }
        .bg-icon:nth-child(3)  { top: 55%; left: 5%;    animation-delay: 2s;    animation-duration: 5.8s; }
        .bg-icon:nth-child(4)  { top: 70%; right: 8%;   animation-delay: 0.5s;  animation-duration: 6.5s; }
        .bg-icon:nth-child(5)  { top: 85%; left: 20%;   animation-delay: 1.5s;  animation-duration: 5.2s; }
        .bg-icon:nth-child(6)  { top: 10%; left: 30%;   animation-delay: 0.8s;  animation-duration: 7s;   }
        .bg-icon:nth-child(7)  { top: 78%; right: 25%;  animation-delay: 2.5s;  animation-duration: 5.9s; }
        .bg-icon:nth-child(8)  { top: 40%; left: 15%;   animation-delay: 3s;    animation-duration: 6.8s; }
        .bg-icon:nth-child(9)  { top: 35%; right: 5%;   animation-delay: 1.8s;  animation-duration: 5.4s; }
        .bg-icon:nth-child(10) { top: 90%; right: 40%;  animation-delay: 0.3s;  animation-duration: 6.1s; }

        @keyframes floatDrift {
            0%, 100% { transform: translateY(0px)  rotate(0deg);   opacity: 0.5; }
            50%       { transform: translateY(-16px) rotate(5deg);   opacity: 0.9; }
        }

        /* ── LOGIN CARD ── */
        .login-card {
            position: relative;
            z-index: 10;
            width: 420px;
            background: rgba(255,255,255,0.97);
            border-radius: 20px;
            box-shadow:
                0 0 0 1px rgba(255,255,255,0.15),
                0 25px 60px rgba(0,0,0,0.5),
                0 8px 20px rgba(0,112,210,0.2);
            overflow: hidden;
            animation: cardIn 0.5s cubic-bezier(0.34, 1.56, 0.64, 1) both;
        }
        @keyframes cardIn {
            from { opacity: 0; transform: translateY(30px) scale(0.95); }
            to   { opacity: 1; transform: translateY(0)    scale(1); }
        }

        /* Top accent bar */
        .card-accent {
            height: 5px;
            background: linear-gradient(90deg, #0070D2 0%, #4318FF 50%, #0070D2 100%);
            background-size: 200% 100%;
            animation: shineBar 3s linear infinite;
        }
        @keyframes shineBar { from { background-position: 200% 0; } to { background-position: -200% 0; } }

        .card-body { padding: 40px 44px 36px; }

        /* Brand */
        .brand {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 32px;
            justify-content: center;
        }
        .brand-icon {
            width: 50px; height: 50px;
            background: linear-gradient(135deg, #0070D2, #4318FF);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 22px;
            box-shadow: 0 6px 16px rgba(0,112,210,0.4);
            flex-shrink: 0;
        }
        .brand-text { text-align: left; }
        .brand-name {
            font-size: 20px;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -0.5px;
            line-height: 1;
        }
        .brand-name span { color: #0070D2; }
        .brand-sub {
            font-size: 10.5px;
            color: #94a3b8;
            font-weight: 500;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-top: 4px;
        }

        /* Headings */
        .form-header { margin-bottom: 28px; text-align: center; }
        .form-header h2 {
            font-size: 22px;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -0.5px;
        }
        .form-header p {
            font-size: 13px;
            color: #94a3b8;
            margin-top: 6px;
        }

        /* Fields */
        .field { margin-bottom: 16px; }
        .field label {
            display: block;
            font-size: 12px;
            font-weight: 700;
            color: #374151;
            margin-bottom: 7px;
            text-transform: uppercase;
            letter-spacing: 0.6px;
        }
        .input-wrap { position: relative; }
        .input-wrap .icon-left {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #cbd5e1;
            font-size: 14px;
            transition: color 0.25s;
            pointer-events: none;
        }
        .input-wrap input {
            width: 100%;
            padding: 13px 44px;
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            font-size: 14px;
            font-family: 'Inter', sans-serif;
            color: #0f172a;
            background: #f8fafc;
            outline: none;
            transition: all 0.25s;
        }
        .input-wrap input::placeholder { color: #c0c9d4; }
        .input-wrap input:focus {
            border-color: #0070D2;
            background: #ffffff;
            box-shadow: 0 0 0 4px rgba(0,112,210,0.1);
        }
        .input-wrap input:focus + .icon-left { color: #0070D2; }

        .toggle-pw {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #c0c9d4;
            font-size: 14px;
            background: none;
            border: none;
            padding: 4px;
            transition: color 0.2s;
        }
        .toggle-pw:hover { color: #0070D2; }

        /* Error */
        .alert-error {
            display: flex;
            align-items: center;
            gap: 10px;
            background: #fef2f2;
            border: 1px solid #fecaca;
            border-left: 4px solid #ef4444;
            border-radius: 8px;
            padding: 11px 14px;
            color: #dc2626;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 18px;
        }

        /* Submit */
        .btn-login {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #0070D2 0%, #1d4ed8 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            letter-spacing: 0.3px;
            box-shadow: 0 4px 16px rgba(0,112,210,0.45);
            font-family: 'Inter', sans-serif;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(0,112,210,0.55);
        }
        .btn-login:active { transform: translateY(0); }

        /* Footer */
        .card-footer {
            margin-top: 22px;
            padding-top: 20px;
            border-top: 1px solid #f1f5f9;
            text-align: center;
        }
        .card-footer a {
            color: #0070D2;
            text-decoration: none;
            font-size: 13px;
            font-weight: 600;
        }
        .card-footer a:hover { text-decoration: underline; }
        .card-footer span { color: #94a3b8; font-size: 13px; }

        /* Security note */
        .security-note {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-top: 16px;
        }
        .security-note i { color: #94a3b8; font-size: 12px; }
        .security-note span { font-size: 11.5px; color: #94a3b8; }

        @media (max-width: 480px) {
            .login-card { width: calc(100vw - 32px); margin: 16px; }
            .card-body { padding: 32px 28px 28px; }
        }
    </style>
</head>
<body>

<div class="login-bg">
    <!-- Blobs -->
    <div class="blob blob-1"></div>
    <div class="blob blob-2"></div>
    <div class="blob blob-3"></div>

    <!-- Floating background icons -->
    <div class="bg-icon"><i class="fas fa-boxes"></i></div>
    <div class="bg-icon"><i class="fas fa-truck"></i></div>
    <div class="bg-icon"><i class="fas fa-warehouse"></i></div>
    <div class="bg-icon"><i class="fas fa-chart-line"></i></div>
    <div class="bg-icon"><i class="fas fa-barcode"></i></div>
    <div class="bg-icon"><i class="fas fa-clipboard-list"></i></div>
    <div class="bg-icon"><i class="fas fa-dolly"></i></div>
    <div class="bg-icon"><i class="fas fa-box-open"></i></div>
    <div class="bg-icon"><i class="fas fa-layer-group"></i></div>
    <div class="bg-icon"><i class="fas fa-coins"></i></div>

    <!-- ── CENTERED CARD ── -->
    <div class="login-card">
        <div class="card-accent"></div>
        <div class="card-body">

            <!-- Brand Logo -->
            <div style="text-align: center; margin-bottom: 28px;">
                <img src="https://i.ibb.co/whdGg4FK/Chat-GPT-Image-00-40-01-22-thg-3-2026.png"
                     alt="Logo"
                     style="height: 80px; width: auto; object-fit: contain; display: inline-block;">
            </div>

            <!-- Heading -->
            <div class="form-header">
                <h2>Đăng nhập hệ thống</h2>
                <p>Nhập thông tin tài khoản để tiếp tục sử dụng</p>
            </div>

            <!-- Form -->
            <form method="POST" action="{{ route('login') }}">
                @csrf

                @if ($errors->any())
                <div class="alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    {{ $errors->first() }}
                </div>
                @endif

                <div class="field">
                    <label>Tên đăng nhập</label>
                    <div class="input-wrap">
                        <input
                            type="text"
                            id="username"
                            name="username"
                            value="{{ old('username') }}"
                            placeholder="Nhập tên đăng nhập..."
                            required autofocus
                        >
                        <i class="fas fa-user icon-left"></i>
                    </div>
                </div>

                <div class="field">
                    <label>Mật khẩu</label>
                    <div class="input-wrap">
                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="Nhập mật khẩu..."
                            required
                        >
                        <i class="fas fa-lock icon-left"></i>
                        <button type="button" class="toggle-pw" onclick="togglePw()" title="Hiện / ẩn mật khẩu">
                            <i id="pw-eye" class="fas fa-eye-slash"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn-login" id="btn-submit">
                    <i class="fas fa-sign-in-alt"></i>
                    Đăng nhập
                </button>
            </form>

            <div class="card-footer">
                <span>Chưa có tài khoản? </span>
                <a href="{{ route('register') }}">Đăng ký ngay</a>
                <div class="security-note">
                    <i class="fas fa-shield-alt"></i>
                    <span>Kết nối bảo mật · Dữ liệu được mã hóa</span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function togglePw() {
        const pw   = document.getElementById('password');
        const icon = document.getElementById('pw-eye');
        if (pw.type === 'password') {
            pw.type = 'text';
            icon.className = 'fas fa-eye';
        } else {
            pw.type = 'password';
            icon.className = 'fas fa-eye-slash';
        }
    }

    // Attach to form SUBMIT event (not button click) so form always submits first
    document.querySelector('form').addEventListener('submit', function() {
        const btn = document.getElementById('btn-submit');
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang đăng nhập...';
        // Do NOT disable — just show spinner; page will navigate away on success
    });
</script>
</body>
</html>
