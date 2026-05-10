<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserPermission;
use App\Models\SystemSetting;
use App\Services\LogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function index()
    {
        $limit = request('limit', 20);
        $users = User::orderBy('role')->orderBy('username')->paginate($limit)->withQueryString();
        $users_all = User::orderBy('username')->get();
        return view('admin.index', compact('users', 'users_all'));
    }

    public static function getAllModules() {
        return [
            'sanpham', 'taomakhachhang', 'taodonhang', 'taophieugiao',
            'nhapkho', 'baocaoxuatkho', 'baocaotonkho',
            'congno', 'thanhtoan',
            'baocaotc', 'baocaoth'
        ];
    }

    public function storeUser(Request $request)
    {
        $data = $request->validate([
            'username'     => 'required|string|min:3|max:100|unique:users,username',
            'password'     => 'required|string|min:6',
            'role'         => 'required|string',
            'display_name' => 'nullable|string|max:150',
            'chuc_danh'    => 'nullable|string|max:150',
        ], [
            'username.unique' => 'Tên đăng nhập đã tồn tại.',
        ]);

        $user = User::create([
            'username'     => $data['username'],
            'password'     => Hash::make($data['password']),
            'role'         => $data['role'],
            'display_name' => $data['display_name'] ?? $data['username'],
            'chuc_danh'    => $data['chuc_danh'] ?? null,
            'status'       => 'Hoạt động',
        ]);

        // Khởi tạo quyền mặc định
        foreach (self::getAllModules() as $module) {
            UserPermission::create([
                'user_id'    => $user->id,
                'module'     => $module,
                'can_view'   => 0,
                'can_edit'   => 0,
                'can_delete' => 0,
                'can_export' => 0,
            ]);
        }

        LogService::log('Thêm tài khoản', "Thêm user [{$data['username']}] role: {$data['role']}");
        return response()->json(['success' => true, 'message' => 'Thêm tài khoản thành công!']);
    }

    public function updateUser(Request $request, User $user)
    {
        if ($user->role === 'Admin' && $user->id !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Không thể sửa tài khoản Admin khác!']);
        }

        $data = $request->validate([
            'password'     => 'nullable|string|min:6',
            'role'         => 'required|string',
            'display_name' => 'nullable|string|max:150',
            'chuc_danh'    => 'nullable|string|max:150',
        ]);

        $updateData = [
            'role'         => $data['role'],
            'display_name' => $data['display_name'] ?? $user->display_name,
            'chuc_danh'    => $data['chuc_danh'] ?? $user->chuc_danh,
        ];

        if (!empty($data['password'])) {
            $updateData['password'] = Hash::make($data['password']);
        }

        $user->update($updateData);
        LogService::log('Sửa tài khoản', "Sửa user [{$user->username}]");
        return response()->json(['success' => true, 'message' => 'Cập nhật thành công!']);
    }

    public function toggleStatus(User $user)
    {
        if ($user->role === 'Admin') {
            return response()->json(['success' => false, 'message' => 'Không thể khóa tài khoản Admin!']);
        }

        $newStatus = $user->status === 'Hoạt động' ? 'Khóa' : 'Hoạt động';
        $user->update(['status' => $newStatus]);
        LogService::log('Đổi trạng thái tài khoản', "User [{$user->username}] → {$newStatus}");
        return response()->json(['success' => true, 'message' => "Tài khoản [{$user->username}] đã {$newStatus}!", 'status' => $newStatus]);
    }

    public function destroyUser(User $user)
    {
        if ($user->role === 'Admin') {
            return response()->json(['success' => false, 'message' => 'Không thể xóa tài khoản Admin!']);
        }
        LogService::log('Xóa tài khoản', "Xóa user [{$user->username}]");
        $user->permissions()->delete();
        $user->delete();
        return response()->json(['success' => true, 'message' => 'Đã xóa tài khoản!']);
    }

    public function getPermissions(User $user)
    {
        $perms = $user->permissions->keyBy('module');
        $result = [];
        foreach (self::getAllModules() as $module) {
            $p = $perms->get($module);
            $result[$module] = [
                'view'   => (bool)($p?->can_view ?? 0),
                'edit'   => (bool)($p?->can_edit ?? 0),
                'delete' => (bool)($p?->can_delete ?? 0),
                'export' => (bool)($p?->can_export ?? 0),
            ];
        }
        return response()->json($result);
    }

    public function savePermissions(Request $request, User $user)
    {
        $perms = $request->get('permissions', []);
        foreach (self::getAllModules() as $module) {
            UserPermission::updateOrCreate(
                ['user_id' => $user->id, 'module' => $module],
                [
                    'can_view'   => !empty($perms[$module]['view']) ? 1 : 0,
                    'can_edit'   => !empty($perms[$module]['edit']) ? 1 : 0,
                    'can_delete' => !empty($perms[$module]['delete']) ? 1 : 0,
                    'can_export' => !empty($perms[$module]['export']) ? 1 : 0,
                ]
            );
        }

        LogService::log('Cập nhật phân quyền', "Phân quyền cho [{$user->username}]");
        return response()->json(['success' => true, 'message' => 'Đã lưu cấu hình phân quyền!']);
    }

    public function saveSetting(Request $request)
    {
        $request->validate([
            'key_name' => 'required|string',
            'value'    => 'nullable|string',
        ]);
        SystemSetting::set($request->key_name, $request->value);
        return response()->json(['success' => true]);
    }
}
