<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chờ duyệt – GAMBERTE WMS</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        *{box-sizing:border-box;margin:0;padding:0}
        body{font-family:'Inter',sans-serif;background:#0f172a;min-height:100vh;display:flex;align-items:center;justify-content:center;color:#fff;}
        .card{background:rgba(255,255,255,.07);backdrop-filter:blur(20px);border:1px solid rgba(255,255,255,.12);border-radius:24px;padding:48px 40px;max-width:460px;text-align:center;box-shadow:0 25px 60px rgba(0,0,0,.5);}
        .icon{width:80px;height:80px;background:rgba(245,158,11,.15);border-radius:50%;display:inline-flex;align-items:center;justify-content:center;font-size:35px;color:#f59e0b;margin-bottom:24px;animation:pulse 2s ease infinite;}
        @keyframes pulse{0%,100%{box-shadow:0 0 0 0 rgba(245,158,11,.4)}50%{box-shadow:0 0 0 15px rgba(245,158,11,0)}}
        h1{font-size:24px;font-weight:800;margin-bottom:12px;}
        p{color:rgba(255,255,255,.6);font-size:14.5px;line-height:1.7;margin-bottom:28px;}
        .btn{display:inline-flex;align-items:center;gap:8px;padding:12px 24px;border-radius:10px;font-size:14px;font-weight:700;cursor:pointer;text-decoration:none;border:none;font-family:'Inter',sans-serif;}
        .btn-logout{background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.2);color:#fff;}
        .btn-logout:hover{background:rgba(255,255,255,.15);}
        form{display:inline;}
    </style>
</head>
<body>
<div class="card">
    <div class="icon"><i class="fas fa-hourglass-half"></i></div>
    <h1>Tài Khoản Đang Chờ Duyệt</h1>
    <p>Tài khoản <strong>{{ auth()->user()->username }}</strong> đã được tạo thành công.<br>
       Vui lòng chờ Admin phê duyệt để bắt đầu sử dụng hệ thống.<br>
       Liên hệ quản trị viên nếu cần hỗ trợ.</p>
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="btn btn-logout"><i class="fas fa-sign-out-alt"></i> Đăng xuất</button>
    </form>
</div>
</body>
</html>
