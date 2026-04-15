<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserPermission;
use App\Models\SystemSetting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Tạo tài khoản Admin mặc định
        $admin = User::firstOrCreate(
            ['username' => 'admin'],
            [
                'password'     => Hash::make('admin123'),
                'role'         => 'Admin',
                'status'       => 'Hoạt động',
                'display_name' => 'Quản trị viên',
            ]
        );

        // System settings mặc định
        SystemSetting::firstOrCreate(['key_name' => 'ty_gia'], ['value' => '25000']);
        SystemSetting::firstOrCreate(['key_name' => 'ngay_ty_gia'], ['value' => now()->format('d/m/Y')]);
        SystemSetting::firstOrCreate(['key_name' => 'ten_cong_ty'], ['value' => 'GAMBERTE']);
        SystemSetting::firstOrCreate(['key_name' => 'dia_chi_cong_ty'], ['value' => '']);

        $this->command->info('✅ Seeder hoàn thành!');
        $this->command->line('   👤 Admin: username=admin / password=admin123');
    }
}
