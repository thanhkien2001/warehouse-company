<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProductCatalog;
use App\Models\Category;

class PharmaceuticalProductSeeder extends Seeder
{
    public function run(): void
    {
        // Tạo thêm các nhóm liên quan đến Dược
        $pharmaCats = [
            'Dược phẩm',
            'Kháng sinh',
            'Thực phẩm chức năng',
            'Vật tư y tế'
        ];

        $catIds = [];
        foreach ($pharmaCats as $name) {
            $cat = Category::firstOrCreate(['name' => $name]);
            $catIds[$name] = $cat->id;
        }

        // 15 sản phẩm liên quan đến dược
        $products = [
            [
                'ma_hang'       => 'DP-001',
                'ten_hang'      => 'Paracetamol 500mg',
                'category_id'   => $catIds['Dược phẩm'],
                'quy_cach'      => 'Hộp 10 vỉ x 10 viên',
                'don_vi_tinh'   => 'Hộp',
                'gia_nhap'      => 32000,
                'gia_ban'       => 45000,
                'nha_cung_cap'  => 'Dược Hậu Giang (DHG)',
            ],
            [
                'ma_hang'       => 'DP-002',
                'ten_hang'      => 'Ibuprofen 400mg',
                'category_id'   => $catIds['Dược phẩm'],
                'quy_cach'      => 'Hộp 10 vỉ x 10 viên',
                'don_vi_tinh'   => 'Hộp',
                'gia_nhap'      => 55000,
                'gia_ban'       => 75000,
                'nha_cung_cap'  => 'Traphaco',
            ],
            [
                'ma_hang'       => 'KS-001',
                'ten_hang'      => 'Amoxicillin 500mg',
                'category_id'   => $catIds['Kháng sinh'],
                'quy_cach'      => 'Hộp 10 vỉ x 10 viên',
                'don_vi_tinh'   => 'Hộp',
                'gia_nhap'      => 85000,
                'gia_ban'       => 110000,
                'nha_cung_cap'  => 'Dược phẩm Imexpharm',
            ],
            [
                'ma_hang'       => 'TPCN-001',
                'ten_hang'      => 'Vitamin C 500mg',
                'category_id'   => $catIds['Thực phẩm chức năng'],
                'quy_cach'      => 'Lọ 100 viên sủi',
                'don_vi_tinh'   => 'Lọ',
                'gia_nhap'      => 45000,
                'gia_ban'       => 65000,
                'nha_cung_cap'  => 'Dược Hậu Giang (DHG)',
            ],
            [
                'ma_hang'       => 'KS-002',
                'ten_hang'      => 'Cephalexin 500mg',
                'category_id'   => $catIds['Kháng sinh'],
                'quy_cach'      => 'Hộp 10 vỉ x 10 viên',
                'don_vi_tinh'   => 'Hộp',
                'gia_nhap'      => 78000,
                'gia_ban'       => 98000,
                'nha_cung_cap'  => 'Dược Mekophar',
            ],
            [
                'ma_hang'       => 'DP-003',
                'ten_hang'      => 'Omeprazole 20mg',
                'category_id'   => $catIds['Dược phẩm'],
                'quy_cach'      => 'Hộp 3 vỉ x 10 viên',
                'don_vi_tinh'   => 'Hộp',
                'gia_nhap'      => 25000,
                'gia_ban'       => 38000,
                'nha_cung_cap'  => 'Dược phẩm Boston',
            ],
            [
                'ma_hang'       => 'DP-004',
                'ten_hang'      => 'Metformin 500mg',
                'category_id'   => $catIds['Dược phẩm'],
                'quy_cach'      => 'Hộp 5 vỉ x 10 viên',
                'don_vi_tinh'   => 'Hộp',
                'gia_nhap'      => 42000,
                'gia_ban'       => 58000,
                'nha_cung_cap'  => 'Stella Pharma',
            ],
            [
                'ma_hang'       => 'DP-005',
                'ten_hang'      => 'Atorvastatin 10mg',
                'category_id'   => $catIds['Dược phẩm'],
                'quy_cach'      => 'Hộp 3 vỉ x 10 viên',
                'don_vi_tinh'   => 'Hộp',
                'gia_nhap'      => 60000,
                'gia_ban'       => 85000,
                'nha_cung_cap'  => 'Dược Sanofi',
            ],
            [
                'ma_hang'       => 'DP-006',
                'ten_hang'      => 'Salbutamol Inhaler 100mcg',
                'category_id'   => $catIds['Dược phẩm'],
                'quy_cach'      => 'Bình xịt 200 liều',
                'don_vi_tinh'   => 'Bình',
                'gia_nhap'      => 120000,
                'gia_ban'       => 155000,
                'nha_cung_cap'  => 'GlaxoSmithKline (GSK)',
            ],
            [
                'ma_hang'       => 'DP-007',
                'ten_hang'      => 'Cetirizine 10mg',
                'category_id'   => $catIds['Dược phẩm'],
                'quy_cach'      => 'Hộp 10 vỉ x 10 viên',
                'don_vi_tinh'   => 'Hộp',
                'gia_nhap'      => 18000,
                'gia_ban'       => 30000,
                'nha_cung_cap'  => 'Stella Pharma',
            ],
            [
                'ma_hang'       => 'DP-008',
                'ten_hang'      => 'Loratadine 10mg',
                'category_id'   => $catIds['Dược phẩm'],
                'quy_cach'      => 'Hộp 3 vỉ x 10 viên',
                'don_vi_tinh'   => 'Hộp',
                'gia_nhap'      => 22000,
                'gia_ban'       => 35000,
                'nha_cung_cap'  => 'Traphaco',
            ],
            [
                'ma_hang'       => 'DP-009',
                'ten_hang'      => 'Amlodipine 5mg',
                'category_id'   => $catIds['Dược phẩm'],
                'quy_cach'      => 'Hộp 3 vỉ x 10 viên',
                'don_vi_tinh'   => 'Hộp',
                'gia_nhap'      => 35000,
                'gia_ban'       => 50000,
                'nha_cung_cap'  => 'Dược phẩm Boston',
            ],
            [
                'ma_hang'       => 'DP-010',
                'ten_hang'      => 'Berberine 100mg',
                'category_id'   => $catIds['Dược phẩm'],
                'quy_cach'      => 'Lọ 100 viên',
                'don_vi_tinh'   => 'Lọ',
                'gia_nhap'      => 15000,
                'gia_ban'       => 25000,
                'nha_cung_cap'  => 'OPC Pharma',
            ],
            [
                'ma_hang'       => 'VTYT-001',
                'ten_hang'      => 'Khẩu trang y tế 4 lớp',
                'category_id'   => $catIds['Vật tư y tế'],
                'quy_cach'      => 'Hộp 50 cái',
                'don_vi_tinh'   => 'Hộp',
                'gia_nhap'      => 25000,
                'gia_ban'       => 45000,
                'nha_cung_cap'  => 'Dược phẩm Danameco',
            ],
            [
                'ma_hang'       => 'VTYT-002',
                'ten_hang'      => 'Cồn y tế 70 độ',
                'category_id'   => $catIds['Vật tư y tế'],
                'quy_cach'      => 'Chai 500ml',
                'don_vi_tinh'   => 'Chai',
                'gia_nhap'      => 12000,
                'gia_ban'       => 20000,
                'nha_cung_cap'  => 'Dược Mekophar',
            ],
        ];

        foreach ($products as $p) {
            ProductCatalog::updateOrCreate(
                ['ma_hang' => $p['ma_hang']],
                $p
            );
        }
    }
}
