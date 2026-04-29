<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\ProductCatalog;
use Carbon\Carbon;

class UnifiedDataSeeder extends Seeder
{
    public function run(): void
    {
        // Truncate tables liên quan
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('delivery_notes')->truncate();
        DB::table('order_meta')->truncate();
        DB::table('order_items')->truncate();
        DB::table('orders')->truncate();
        DB::table('products')->truncate();
        DB::table('inbound_items')->truncate();
        DB::table('inbound_receipts')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Check if customers empty, seed
        if (DB::table('customers')->count() === 0) {
            $this->seedCustomers();
        }

        $customers = DB::table('customers')->get();
        
        $products = ProductCatalog::all();
        if ($products->isEmpty()) {
            $this->call(PharmaceuticalProductSeeder::class);
        }
        
        DB::table('product_catalog')->update(['don_vi_tinh' => 'Kg']);
        $products = ProductCatalog::all();

        $suppliers = [
            'Dược Hậu Giang (DHG)',
            'Traphaco',
            'Dược phẩm Imexpharm',
            'Dược Mekophar',
            'Dược phẩm Boston',
            'Stella Pharma'
        ];
        $warehouses = ['Kho Nguyên Liệu', 'Kho Lab', 'Kho Thành Phẩm'];

        // 1. Seed Inbound (15 records)
        for ($i = 1; $i <= 15; $i++) {
            $date = Carbon::now()->subDays(rand(15, 45));
            $receiptCode = 'NK-2026-' . str_pad($i, 5, '0', STR_PAD_LEFT);

            $receiptId = DB::table('inbound_receipts')->insertGetId([
                'receipt_code'  => $receiptCode,
                'invoice_no'    => 'HD-' . rand(10000, 99999),
                'receipt_date'  => $date->format('Y-m-d'),
                'invoice_date'  => $date->copy()->subDays(rand(1, 5))->format('Y-m-d'),
                'supplier_name' => $suppliers[array_rand($suppliers)],
                'warehouse'     => $warehouses[array_rand($warehouses)],
                'origin'        => 'Việt Nam',
                'department'    => 'Kho',
                'created_by'    => 1,
                'notes'         => 'Nhập kho tự động mẫu #' . $i,
                'status'        => 'completed',
                'total_amount'  => 0,
                'created_at'    => now(),
                'updated_at'    => now()
            ]);

            $itemCount = rand(1, 4);
            $total = 0;
            $subProducts = $products->random($itemCount);

            foreach ($subProducts as $j => $p) {
                $qty = rand(200, 1000);
                $price = $p->gia_nhap ?? rand(10000, 100000);
                $subtotal = $qty * $price;
                $total += $subtotal;

                DB::table('inbound_items')->insert([
                    'receipt_id'         => $receiptId,
                    'product_catalog_id' => $p->id,
                    'ma_hang'            => $p->ma_hang,
                    'ten_hang'           => $p->ten_hang,
                    'category_id'        => $p->category_id,
                    'don_vi_tinh'        => $p->don_vi_tinh,
                    'quy_cach'           => $p->quy_cach,
                    'so_luong'           => $qty,
                    'don_gia'            => $price,
                    'thanh_tien'         => $subtotal,
                    'so_lo'              => 'LOT-' . rand(1000, 9999),
                    'ngay_san_xuat'      => $date->copy()->subMonths(rand(3, 12))->format('Y-m-d'),
                    'han_su_dung'        => $date->copy()->addMonths(rand(12, 24))->format('Y-m-d'),
                    'kho_nhap'           => $warehouses[array_rand($warehouses)],
                    'sort_order'         => $j,
                    'created_at'         => now(),
                    'updated_at'         => now()
                ]);

            }

            DB::table('inbound_receipts')->where('id', $receiptId)->update(['total_amount' => $total]);
        }

        // 2. Seed Outbound (Orders & Delivery Notes) - 15 records
        $orderStatuses = ['Chờ xác nhận', 'Đang xử lý', 'Đang vận chuyển', 'Hoàn thành'];
        
        for ($i = 1; $i <= 15; $i++) {
            $c = $customers->random();
            $date = Carbon::now()->subDays(rand(1, 14));
            $ctoCode = 'CTO-' . str_pad($i, 5, '0', STR_PAD_LEFT);
            $status = $orderStatuses[array_rand($orderStatuses)];

            $orderId = DB::table('orders')->insertGetId([
                'cto_code'    => $ctoCode,
                'customer_id' => $c->id,
                'ma_kh'       => $c->ma_kh,
                'ten_kh'      => $c->ten_cty,
                'ghi_chu'     => 'Đơn xuất kho tự động #' . $i,
                'trang_thai'  => $status,
                'nguoi_ban'   => 'Antigravity AI',
                'sdt_ban'     => '0123456789',
                'nguoi_mua'   => $c->nguoi_lien_he ?? 'Khách lẻ',
                'sdt_mua'     => $c->sdt ?? '0987654321',
                'created_by'  => 1,
                'order_date'  => $date->format('Y-m-d'),
                'created_at'  => now(),
                'updated_at'  => now()
            ]);

            DB::table('order_meta')->insert([
                'order_id'     => $orderId,
                'cto_code'     => $ctoCode,
                'tinh_trang'   => $status,
                'ty_gia'       => '25.000',
                'ngay_ty_gia'  => $date->format('d/m/Y'),
                'vat_percent'  => 8.00,
            ]);

            $itemCount = rand(1, 3);
            $orderProducts = $products->random($itemCount);

            foreach ($orderProducts as $idx => $p) {
                // Lấy 1 lô ngẫu nhiên đã nhập của sản phẩm này
                $existingInbound = DB::table('inbound_items')
                    ->where('product_catalog_id', $p->id)
                    ->inRandomOrder()
                    ->first();

                $lotCode = $existingInbound ? $existingInbound->so_lo : 'LOT-' . rand(1000, 9999);
                $hsdDate = $existingInbound ? $existingInbound->han_su_dung : $date->copy()->addYears(2)->format('Y-m-d');

                $qty = rand(10, 50);
                $price = $p->gia_ban ?? rand(20000, 150000);
                $subtotal = $qty * $price;

                DB::table('order_items')->insert([
                    'order_id'     => $orderId,
                    'cto_code'     => $ctoCode,
                    'ma_hang'      => $p->ma_hang,
                    'ten_hang'     => $p->ten_hang,
                    'mo_ta'        => 'Quy cách: ' . ($p->quy_cach ?? '---'),
                    'mo_ta_phu'    => 'Dạng ' . ($p->don_vi_tinh ?? 'đơn vị'),
                    'so_luong'     => $qty,
                    'don_vi_tinh'  => $p->don_vi_tinh,
                    'don_gia'      => $price,
                    'thanh_tien'   => $subtotal,
                    'ma_lot'       => $lotCode,
                    'han_su_dung'  => $hsdDate,
                    'quy_cach'     => $p->quy_cach,
                    'sort_order'   => $idx,
                    'created_at'   => now(),
                    'updated_at'   => now()
                ]);
            }

            // Tạo Delivery Note cho Đơn hàng xử lý/giao/hoàn thành
            if (in_array($status, ['Đang xử lý', 'Đang vận chuyển', 'Hoàn thành'])) {
                $dnStatus = 'Đã giao xong';
                if ($status === 'Đang xử lý') $dnStatus = 'Chờ giao hàng';
                if ($status === 'Đang vận chuyển') $dnStatus = 'Đang giao';

                DB::table('delivery_notes')->insert([
                    'dn_code'        => 'DN-' . Carbon::now()->format('Ymd') . '-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                    'order_id'       => $orderId,
                    'cto_code'       => $ctoCode,
                    'customer_id'    => $c->id,
                    'ma_kh'          => $c->ma_kh,
                    'ten_kh'         => $c->ten_cty,
                    'trang_thai'     => $dnStatus,
                    'han_thanh_toan' => rand(15, 60),
                    'nguoi_tao'      => 'Quản trị viên',
                    'delivery_date'  => $date->copy()->addDays(rand(1, 3))->format('Y-m-d'),
                    'created_at'     => now(),
                    'updated_at'     => now()
                ]);
            }
        }
    }

    private function seedCustomers(): void
    {
        $custs = [
            [
                'ma_kh'      => 'KH001',
                'ten_cty'    => 'CÔNG TY TNHH DƯỢC PHẨM MINH CHÂU',
                'ma_so_thue' => '0101234567',
                'dia_chi'    => '123 Hai Bà Trưng, Quận 1, TP. HCM',
                'nguoi_lien_he' => 'Nguyễn Thị Minh',
                'sdt'        => '0901234567',
                'khu_vuc'    => 'Miền Nam',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'ma_kh'      => 'KH002',
                'ten_cty'    => 'NHÀ THUỐC PHARMACITY #12',
                'ma_so_thue' => '0309876543',
                'dia_chi'    => '456 Lê Lợi, Quận Hoàn Kiếm, Hà Nội',
                'nguoi_lien_he' => 'Trần Quang Huy',
                'sdt'        => '0912345678',
                'khu_vuc'    => 'Miền Bắc',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'ma_kh'      => 'KH003',
                'ten_cty'    => 'CÔNG TY CP Y TẾ ĐÔNG Á',
                'ma_so_thue' => '0405678901',
                'dia_chi'    => '789 Hùng Vương, Quận Hải Châu, Đà Nẵng',
                'nguoi_lien_he' => 'Lê Thanh Bình',
                'sdt'        => '0922334455',
                'khu_vuc'    => 'Miền Trung',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ];

        foreach ($custs as $c) {
            DB::table('customers')->insert($c);
        }
    }
}
