<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập – GAMBERTE WMS</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body, html { margin: 0; padding: 0; height: 100%; font-family: 'Inter', sans-serif; }
        
        #login-overlay {
            position: fixed; top: 0; left: 0; width: 100vw; height: 100vh;
            background: linear-gradient(135deg, #3a50d4 0%, #d84ea2 100%) !important;
            display: flex; align-items: center; justify-content: center;
        }

        .login-modern-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.15);
            display: flex;
            width: 850px;
            max-width: 90%;
            min-height: 450px;
            overflow: hidden;
        }

        .login-modern-left {
            flex: 1;
            background-color: #ffffff;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px;
        }

        .login-modern-avatar-bg {
            background: #f4f5f7;
            border-radius: 50%;
            width: 240px;
            height: 240px;
            display: flex;
            justify-content: center;
            align-items: center;
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.08);
        }

        .login-modern-avatar-bg img {
            width: 150px;
            margin-top: 15px;
        }

        .login-modern-right {
            flex: 1.1;
            padding: 60px 70px 60px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: #ffffff;
        }

        .login-modern-right h2 {
            text-align: center;
            color: #222;
            margin-top: 0;
            margin-bottom: 35px;
            font-weight: 500;
            font-size: 26px;
            letter-spacing: 0.5px;
        }

        .input-group-modern {
            position: relative;
            margin-bottom: 20px;
        }

        .input-group-modern i {
            position: absolute;
            left: 20px;
            top: 50%;
            transform: translateY(-50%);
            color: #111;
            font-size: 14px;
        }

        .input-group-modern input {
            width: 100%;
            padding: 16px 20px 16px 45px;
            background-color: #f0f2f5;
            border: none;
            border-radius: 50px;
            font-size: 14px;
            font-weight: 400;
            font-family: 'Inter', sans-serif;
            color: #333;
            box-sizing: border-box;
            outline: none;
            transition: 0.3s;
        }

        .input-group-modern input:focus {
            background-color: #e4e6e9;
        }

        .btn-login-modern {
            width: 100%;
            padding: 16px;
            background-color: #60b759;
            color: white;
            border: none;
            border-radius: 50px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.2s;
            margin-bottom: 20px;
            letter-spacing: 0.5px;
        }

        .btn-login-modern:hover {
            background-color: #50a149;
        }

        .alert-error {
            color: #ef4444;
            font-size: 13px;
            font-weight: 600;
            text-align: center;
            margin-bottom: 15px;
        }

        @media (max-width: 768px) {
            .login-modern-container {
                flex-direction: column;
                width: 400px;
            }
            .login-modern-left { padding: 30px; }
            .login-modern-avatar-bg { width: 180px; height: 180px; }
            .login-modern-avatar-bg img { width: 110px; }
            .login-modern-right { padding: 30px 40px 40px 40px; }
        }
    </style>
</head>
<body>

    <div id="login-overlay">
        <div class="login-modern-container">
            <div class="login-modern-left">
                <div class="login-modern-avatar-bg">
                    <img src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png" alt="User Avatar">
                </div>
            </div>

            <div class="login-modern-right">
                <h2>User Login</h2>

                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    
                    <div class="input-group-modern">
                        <i class="fas fa-envelope"></i>
                        <input type="text" name="username" value="{{ old('username') }}" placeholder="Tên đăng nhập..." required autofocus>
                    </div>

                    <div class="input-group-modern">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="password" placeholder="Mật khẩu..." required>
                    </div>

                    @if ($errors->any())
                        <div class="alert-error">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    <button type="submit" class="btn-login-modern">Login</button>
                </form>

                <div style="text-align: center; margin-top: 5px;">
                    <a href="{{ route('register') }}" style="font-weight: 600; color: #10568f; text-decoration: none; font-size: 13.5px;">Chưa có tài khoản? Đăng ký ngay</a>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
