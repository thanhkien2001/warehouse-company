<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'Hoạt chất',
            'Chiết xuất',
            'Chất dưỡng ẩm',
            'Dầu làm mềm',
            'Chất nhũ hóa',
            'Chất tạo đặc',
            'Chất bảo quản',
            'Chống oxy hóa',
            'Hương liệu',
            'Chất tạo màu',
            'Dung môi',
            'Chất HĐBM',
        ];

        foreach ($categories as $cat) {
            \App\Models\Category::updateOrCreate(['name' => $cat]);
        }
    }
}
