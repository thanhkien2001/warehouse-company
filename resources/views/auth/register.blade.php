<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký – GAMBERTE WMS</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        *{box-sizing:border-box;margin:0;padding:0}
        body{font-family:'Inter',sans-serif;background:#0f172a;min-height:100vh;display:flex;align-items:center;justify-content:center;position:relative;overflow:hidden}
        body::before{content:'';position:absolute;width:600px;height:600px;background:radial-gradient(circle,rgba(79,70,229,.4) 0%,transparent 70%);top:-100px;left:-100px;}
        body::after{content:'';position:absolute;width:500px;height:500px;background:radial-gradient(circle,rgba(16,185,129,.3) 0%,transparent 70%);bottom:-100px;right:-100px;}
        .wrapper{position:relative;z-index:10;width:100%;max-width:480px;padding:16px;}
        .card{background:rgba(255,255,255,.07);backdrop-filter:blur(20px);border:1px solid rgba(255,255,255,.12);border-radius:24px;padding:36px;box-shadow:0 25px 60px rgba(0,0,0,.5);animation:fadeUp .4s ease;}
        @keyframes fadeUp{from{opacity:0;transform:translateY(30px)}to{opacity:1;transform:none}}
        .logo-area{text-align:center;margin-bottom:28px;}
        .logo-icon{width:60px;height:60px;background:linear-gradient(135deg,#002B6B,#7c3aed);border-radius:16px;display:inline-flex;align-items:center;justify-content:center;font-size:26px;color:#fff;box-shadow:0 10px 25px rgba(79,70,229,.5);margin-bottom:12px;}
        .logo-name{color:#fff;font-size:22px;font-weight:900;}
        .logo-sub{color:rgba(255,255,255,.5);font-size:12px;margin-top:4px;}
        .form-group{margin-bottom:16px;}
        .form-label{display:block;color:rgba(255,255,255,.7);font-size:13px;font-weight:600;margin-bottom:6px;}
        .input-wrap{position:relative;}
        .input-icon{position:absolute;left:14px;top:50%;transform:translateY(-50%);color:rgba(255,255,255,.4);}
        .form-input{width:100%;padding:12px 14px 12px 40px;background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.15);border-radius:10px;color:#fff;font-size:14px;font-family:'Inter',sans-serif;outline:none;transition:all .2s;}
        .form-input::placeholder{color:rgba(255,255,255,.3);}
        .form-input:focus{border-color:#002B6B;background:rgba(255,255,255,.12);}
        .error-msg{color:#fca5a5;font-size:12.5px;margin-top:5px;}
        .btn-submit{width:100%;padding:14px;background:linear-gradient(135deg,#002B6B,#7c3aed);color:#fff;border:none;border-radius:12px;font-size:15px;font-weight:700;font-family:'Inter',sans-serif;cursor:pointer;transition:all .2s;box-shadow:0 8px 24px rgba(79,70,229,.4);}
        .btn-submit:hover{transform:translateY(-2px);}
        .footer-link{text-align:center;margin-top:20px;color:rgba(255,255,255,.5);font-size:13px;}
        .footer-link a{color:#818cf8;text-decoration:none;font-weight:600;}
        .error-box{background:rgba(239,68,68,.15);border:1px solid rgba(239,68,68,.3);border-radius:10px;padding:12px 14px;margin-bottom:16px;color:#fca5a5;font-size:13px;}
    </style>
</head>
<body>
<div class="wrapper">
    <div class="card">
        <div class="logo-area">
            <div class="logo-icon"><i class="fas fa-user-plus"></i></div>
            <div class="logo-name">Đăng Ký Tài Khoản</div>
            <div class="logo-sub">Tài khoản sẽ chờ Admin duyệt trước khi sử dụng</div>
        </div>

        @if($errors->any())
            <div class="error-box"><i class="fas fa-exclamation-circle"></i> {{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('register.post') }}">
            @csrf
            <div class="form-group">
                <label class="form-label">Tên hiển thị</label>
                <div class="input-wrap">
                    <i class="fas fa-id-card input-icon"></i>
                    <input type="text" name="display_name" class="form-input" placeholder="Tên của bạn..." value="{{ old('display_name') }}">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Tên đăng nhập <span style="color:#ef4444">*</span></label>
                <div class="input-wrap">
                    <i class="fas fa-user input-icon"></i>
                    <input type="text" name="username" class="form-input" placeholder="Ví dụ: nguyen_van_a" value="{{ old('username') }}" required>
                </div>
                @error('username')<div class="error-msg">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label class="form-label">Mật khẩu <span style="color:#ef4444">*</span></label>
                <div class="input-wrap">
                    <i class="fas fa-lock input-icon"></i>
                    <input type="password" name="password" class="form-input" placeholder="Tối thiểu 6 ký tự..." required>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Xác nhận mật khẩu <span style="color:#ef4444">*</span></label>
                <div class="input-wrap">
                    <i class="fas fa-lock input-icon"></i>
                    <input type="password" name="password_confirmation" class="form-input" placeholder="Nhập lại mật khẩu..." required>
                </div>
            </div>
            <button type="submit" class="btn-submit"><i class="fas fa-user-check"></i> Đăng Ký</button>
        </form>

        <div class="footer-link">Đã có tài khoản? <a href="{{ route('login') }}">Đăng nhập</a></div>
    </div>
</div>
</body>
</html>
