<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\LogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) return redirect()->route('dashboard');
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ], [
            'username.required' => 'Vui lòng nhập tên đăng nhập.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
        ]);

        $user = User::where('username', $request->username)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()->withErrors(['username' => 'Tên đăng nhập hoặc mật khẩu không đúng.'])->withInput();
        }

        if ($user->status === 'Khóa') {
            return back()->withErrors(['username' => 'Tài khoản của bạn đã bị khóa. Liên hệ Admin.'])->withInput();
        }

        Auth::login($user, $request->boolean('remember'));
        LogService::log('Đăng nhập', 'Đăng nhập thành công từ IP: ' . $request->ip());

        if ($user->role === 'MoiDangKy') {
            return redirect()->route('cho-duyet');
        }

        return redirect()->intended(route('dashboard'));
    }

    public function logout(Request $request)
    {
        LogService::log('Đăng xuất');
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'username'              => 'required|string|min:3|max:100|unique:users,username',
            'password'              => 'required|string|min:6|confirmed',
            'display_name'          => 'nullable|string|max:150',
        ], [
            'username.unique'    => 'Tên đăng nhập đã được sử dụng.',
            'username.min'       => 'Tên đăng nhập tối thiểu 3 ký tự.',
            'password.min'       => 'Mật khẩu tối thiểu 6 ký tự.',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp.',
        ]);

        User::create([
            'username'     => $request->username,
            'password'     => Hash::make($request->password),
            'display_name' => $request->display_name ?? $request->username,
            'role'         => 'MoiDangKy',
            'status'       => 'Hoạt động',
        ]);

        return redirect()->route('login')->with('success', 'Đăng ký thành công! Vui lòng chờ Admin duyệt tài khoản.');
    }

    public function choDuyet()
    {
        return view('auth.cho-duyet');
    }

    public function doiMatKhau(Request $request)
    {
        $request->validate([
            'mat_khau_cu'  => 'required',
            'mat_khau_moi' => 'required|min:6|confirmed',
        ], [
            'mat_khau_moi.min'       => 'Mật khẩu mới tối thiểu 6 ký tự.',
            'mat_khau_moi.confirmed' => 'Xác nhận mật khẩu không khớp.',
        ]);

        $user = Auth::user();
        if (!Hash::check($request->mat_khau_cu, $user->password)) {
            return back()->withErrors(['mat_khau_cu' => 'Mật khẩu cũ không đúng.']);
        }

        $user->update(['password' => Hash::make($request->mat_khau_moi)]);
        LogService::log('Đổi mật khẩu');

        Auth::logout();
        $request->session()->invalidate();
        return redirect()->route('login')->with('success', 'Đổi mật khẩu thành công! Vui lòng đăng nhập lại.');
    }

    public function doiThongTin(Request $request)
    {
        $request->validate(['display_name' => 'required|string|max:150']);
        Auth::user()->update(['display_name' => $request->display_name]);
        return back()->with('success', 'Cập nhật thông tin thành công!');
    }
}
