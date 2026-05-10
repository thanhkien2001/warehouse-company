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
        body, html { height: 100%; font-family: 'Inter', sans-serif; overflow: hidden; background: #fff; }

        .login-container {
            display: flex;
            width: 100vw;
            height: 100vh;
        }

        /* Left Side: Visual/Banner */
        .login-visual {
            flex: 1;
            background: url("{{ asset('images/banner_giao dien phan men_gamberte.png') }}") center center / cover no-repeat;
            position: relative;
        }

        /* Right Side: Form */
        .login-form-side {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #ffffff;
            position: relative;
        }

        .form-wrapper {
            width: 100%;
            padding: 70px;
        }

        /* Brand */
        .brand {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 32px;
            justify-content: center;
        }
        
        /* Headings */
        .form-header { margin-bottom: 35px; text-align: center; }
        .form-header h2 {
            font-size: 26px;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -0.5px;
        }
        .form-header p {
            font-size: 14px;
            color: #64748b;
            margin-top: 8px;
        }

        /* Fields */
        .field { margin-bottom: 30px; }
        .field label {
            display: block;
            font-size: 13px;
            font-weight: 700;
            color: #334155;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .input-wrap { position: relative; }
        .input-wrap .icon-left {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 16px;
            transition: color 0.25s;
            pointer-events: none;
        }
        .input-wrap input {
            width: 100%;
            padding: 14px 14px 14px 48px;
            border: 1.5px solid #cbd5e1;
            border-radius: 12px;
            font-size: 15px;
            font-family: 'Inter', sans-serif;
            color: #0f172a;
            background: #f8fafc;
            outline: none;
            transition: all 0.25s;
        }
        .input-wrap input::placeholder { color: #94a3b8; font-weight: 500; }
        .input-wrap input:focus {
            border-color: #0070D2;
            background: #ffffff;
            box-shadow: 0 0 0 4px rgba(0,112,210,0.1);
        }
        .input-wrap input:focus + .icon-left { color: #0070D2; }

        .toggle-pw {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #94a3b8;
            font-size: 15px;
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
            border-radius: 10px;
            padding: 12px 16px;
            color: #dc2626;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 24px;
        }

        /* Submit */
        .btn-login {
            width: 100%;
            padding: 15px;
            background: #002B6B;
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            box-shadow: 0 4px 12px rgba(0,112,210,0.25);
            font-family: 'Inter', sans-serif;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            background: #005bb5;
            box-shadow: 0 8px 20px rgba(0,112,210,0.35);
        }
        .btn-login:active { transform: translateY(0); }

        /* Footer */
        .card-footer {
            margin-top: 30px;
            padding-top: 25px;
            border-top: 1px solid #e2e8f0;
            text-align: center;
        }
        .card-footer a {
            color: #002B6B;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
        }
        .card-footer a:hover { text-decoration: underline; }
        .card-footer span { color: #64748b; font-size: 14px; }

        /* Security note */
        .security-note {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-top: 20px;
        }
        .security-note i { color: #94a3b8; font-size: 13px; }
        .security-note span { font-size: 12px; color: #94a3b8; font-weight: 500; }

        @media (max-width: 992px) {
            .login-visual { display: none; }
        }
    </style>
</head>
<body>

<div class="login-container">
    <!-- Left Side: Full Image -->
    <div class="login-visual"></div>

    <!-- Right Side: Form -->
    <div class="login-form-side">
        <div class="form-wrapper">
            <!-- Brand Logo -->
            <div style="text-align: center; margin-bottom: 35px;">
                <img src="{{ asset('images/logo_gamberte_login.png') }}"
                     alt="Logo"
                     style="height: 100px; width: auto; object-fit: contain; display: inline-block;">
            </div>

            <!-- Heading -->
            <div class="form-header">
                <h2>Đăng nhập hệ thống</h2>
                <p>Vui lòng đăng nhập để truy cập tài khoản của bạn</p>
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

                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 22px;">
                    <label style="display: flex; align-items: center; gap: 8px; font-size: 13.5px; color: #475569; cursor: pointer; font-weight: 500;">
                        <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }} style="accent-color: #0070D2; width: 16px; height: 16px; cursor: pointer;">
                        Ghi nhớ đăng nhập
                    </label>
                    <a href="#" style="font-size: 13.5px; color: #002B6B; font-weight: 600; text-decoration: none;">Quên mật khẩu?</a>
                </div>

                <button type="submit" class="btn-login" id="btn-submit">
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

    document.querySelector('form').addEventListener('submit', function() {
        const btn = document.getElementById('btn-submit');
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang đăng nhập...';
    });
</script>
</body>
</html>
