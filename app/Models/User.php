<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'username', 'password', 'role', 'status', 'display_name',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = ['password' => 'hashed'];

    public function permissions()
    {
        return $this->hasMany(UserPermission::class);
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'Admin';
    }

    public function isMoiDangKy(): bool
    {
        return $this->role === 'MoiDangKy';
    }

    public function canDo(string $module, string $action): bool
    {
        if ($this->isAdmin()) return true;
        if ($this->isMoiDangKy()) return false;

        $perm = $this->permissions()->where('module', $module)->first();
        if (!$perm) return false;

        return match ($action) {
            'view'   => (bool) $perm->can_view,
            'edit'   => (bool) $perm->can_edit,
            'delete' => (bool) $perm->can_delete,
            default  => false,
        };
    }

    // Override để dùng username thay vì email
    public function getAuthIdentifierName()
    {
        return 'username';
    }
}
