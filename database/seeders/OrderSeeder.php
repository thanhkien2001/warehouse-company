<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Customer;
use App\Models\ProductCatalog;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        // Truncate existing outbound data
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('delivery_notes')->truncate();
        DB::table('order_meta')->truncate();
        DB::table('order_items')->truncate();
        DB::table('orders')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Check and seed customers if empty
        if (DB::table('customers')->count() === 0) {
            $this->seedCustomers();
        }

        $customers = DB::table('customers')->get();
        $products = ProductCatalog::all();

        if ($products->isEmpty()) {
            $this->call(PharmaceuticalProductSeeder::class);
            $products = ProductCatalog::all();
        }

        $statuses = ['Chờ xác nhận', 'Đang xử lý', 'Đang vận chuyển', 'Hoàn thành'];

        for ($i = 1; $i <= 15; $i++) {
            $c = $customers->random();
            $date = Carbon::now()->subDays(rand(1, 20));
            $ctoCode = 'GAMBER-' . str_pad($i, 5, '0', STR_PAD_LEFT);
            $status = $statuses[array_rand($statuses)];

            // Create Order
            $orderId = DB::table('orders')->insertGetId([
                'cto_code'    => $ctoCode,
                'customer_id' => $c->id,
                'ma_kh'       => $c->ma_kh,
                'ten_kh'      => $c->ten_cty,
                'ghi_chu'     => 'Đơn hàng tự động số ' . $i,
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

            // Create Order Meta
            DB::table('order_meta')->insert([
                'order_id'     => $orderId,
                'cto_code'     => $ctoCode,
                'tinh_trang'   => $status,
                'ty_gia'       => '25.000',
                'ngay_ty_gia'  => $date->format('d/m/Y'),
                'vat_percent'  => 8.00,
            ]);

            // Create Order Items (1-4)
            $itemCount = rand(1, 4);
            $orderProducts = $products->random($itemCount);

            foreach ($orderProducts as $idx => $p) {
                $qty = rand(5, 50);
                $price = $p->gia_ban ?? rand(20000, 150000);
                $subtotal = $qty * $price;

                DB::table('order_items')->insert([
                    'order_id'     => $orderId,
                    'cto_code'     => $ctoCode,
                    'ma_hang'      => $p->ma_hang,
                    'ten_hang'     => $p->ten_hang,
                    'mo_ta'        => 'Đóng gói quy cách: ' . ($p->quy_cach ?? '---'),
                    'mo_ta_phu'    => 'Dạng ' . ($p->don_vi_tinh ?? 'đơn vị'),
                    'so_luong'     => $qty,
                    'don_vi_tinh'  => $p->don_vi_tinh,
                    'don_gia'      => $price,
                    'thanh_tien'   => $subtotal,
                    'ma_lot'       => 'LOT-' . rand(1000, 9999),
                    'han_su_dung'  => $date->copy()->addYears(2)->format('Y-m-d'),
                    'quy_cach'     => $p->quy_cach,
                    'sort_order'   => $idx,
                    'created_at'   => now(),
                    'updated_at'   => now()
                ]);
            }

            // Create Delivery Note if processed, shipping, or completed
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
            ],
            [
                'ma_kh'      => 'KH004',
                'ten_cty'    => 'BỆNH VIỆN ĐA KHOA QUỐC TẾ VINMEC',
                'ma_so_thue' => '0105556667',
                'dia_chi'    => 'Times City, 458 Minh Khai, Hà Nội',
                'nguoi_lien_he' => 'Phạm Thu Hương',
                'sdt'        => '0933445566',
                'khu_vuc'    => 'Miền Bắc',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'ma_kh'      => 'KH005',
                'ten_cty'    => 'CÔNG TY TNHH PHÁT TRIỂN Y TẾ VIỆT NAM',
                'ma_so_thue' => '0311223344',
                'dia_chi'    => '88 Nguyễn Văn Linh, Quận 7, TP. HCM',
                'nguoi_lien_he' => 'Đỗ Gia Bảo',
                'sdt'        => '0944556677',
                'khu_vuc'    => 'Miền Nam',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ];

        foreach ($custs as $c) {
            DB::table('customers')->insert($c);
        }
    }
}
