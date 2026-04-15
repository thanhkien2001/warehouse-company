<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderMeta;
use App\Models\DeliveryNote;
use App\Models\Product;
use App\Models\Payment;
use App\Models\SystemSetting;

class SampleDataSeeder extends Seeder
{
    public function run()
    {
        $this->command->info('Bắt đầu tạo dữ liệu mẫu...');

        // 1. Tạo Users
        $users = [
            ['username' => 'quanly', 'display_name' => 'Quản lý Vũ', 'role' => 'QuanLy'],
            ['username' => 'ketoan', 'display_name' => 'Kế toán Hương', 'role' => 'KeToan'],
            ['username' => 'nhanvien1', 'display_name' => 'Nhân viên Hùng', 'role' => 'NhanVien'],
        ];

        foreach ($users as $u) {
            User::firstOrCreate(
                ['username' => $u['username']],
                [
                    'password' => Hash::make('123456'),
                    'role' => $u['role'],
                    'status' => 'Hoạt động',
                    'display_name' => $u['display_name'],
                ]
            );
        }

        // 2. Tạo Customers
        $khs = [];
        $congTies = ['Việt Tiến', 'Bình An', 'Hoàng Gia', 'Công Nghệ VNG', 'Cơ Khí Đại Phát', 'Xây Dựng Hòa Bình', 'Minh Vũ', 'Phúc Long', 'Vĩnh Khang', 'Đức Thành'];
        for($i=0; $i<10; $i++) {
            $num = $i + 1;
            $khs[] = Customer::create([
                'ma_kh' => 'KH' . str_pad($num, 4, '0', STR_PAD_LEFT),
                'ten_cty' => 'Công ty TNHH Thương Mại ' . $congTies[$i],
                'ma_so_thue' => '031' . rand(1000000, 9999999),
                'dia_chi' => rand(10, 999) . ' Nguyễn Trãi, Quận ' . rand(1, 12) . ', TPHCM',
                'nguoi_lien_he' => 'Nguyễn Văn ' . chr(65 + $i),
                'sdt' => '090' . rand(1000000, 9999999),
                'email' => 'lienhe' . $num . '@example.com',
                'khu_vuc' => ['Miền Bắc', 'Miền Trung', 'Miền Nam'][array_rand(['Miền Bắc', 'Miền Trung', 'Miền Nam'])],
                'ghi_chu' => 'Dữ liệu mẫu',
                'created_date' => Carbon::now()->subDays(rand(10, 100))
            ]);
        }

        // 3. Tạo Products (Lịch sử nhập kho quy trình kho)
        $products = [];
        $spNames = ['Cáp đồng bọc nhựa 2mm', 'Dây cáp mạng CAT6', 'Thép tấm 2 ly', 'Vít gỗ đầu bằng', 'Motor giảm tốc 1HP', 'Sơn chống rỉ 5L', 'Màn hình LED công nghiệp', 'Cảm biến quang NPN', 'Ống nhựa PVC d20', 'Khớp nối mềm inox'];
        $dvt = ['Meter', 'Thùng', 'Tấm', 'Hộp', 'Cái', 'Thùng', 'Cái', 'Cái', 'Ống', 'Cái'];
        $gias = [150000, 450000, 250000, 80000, 1200000, 300000, 2500000, 150000, 45000, 210000];

        for($i=0; $i<10; $i++) {
            $ma = 'SP' . str_pad($i+1, 3, '0', STR_PAD_LEFT);
            $products[] = Product::create([
                'ma_hang' => $ma,
                'ten_hang' => $spNames[$i],
                'mo_ta' => 'Hàng loại 1',
                'so_luong_nhap' => rand(100, 500),
                'don_vi_tinh' => $dvt[$i],
                'don_gia' => $gias[$i] * 0.7, // Giá nhập = 70% giá bán
                'ghi_chu' => 'Mẫu nhập kho đợt '.($i+1),
                'nhap_date' => Carbon::now()->subDays(rand(20, 60))
            ]);
        }

        // 4. Tạo Orders
        $orders = [];
        $statuses = ['Chờ xác nhận', 'Đang xử lý', 'Đang vận chuyển', 'Hoàn thành', 'Hoàn thành', 'Hoàn thành', 'Hoàn thành', 'Hoàn thành', 'Đã hủy'];
        
        for ($i=1; $i<=10; $i++) {
            $kh = $khs[array_rand($khs)];
            $dt = Carbon::now()->subDays(rand(1, 30));
            $order = Order::create([
                'cto_code' => 'CTO-' . $dt->format('ymd') . str_pad($i, 3, '0', STR_PAD_LEFT),
                'customer_id' => $kh->id,
                'ma_kh' => $kh->ma_kh,
                'ten_kh' => $kh->ten_cty,
                'order_date' => $dt,
                'trang_thai' => $statuses[array_rand($statuses)],
                'nguoi_ban' => 'Nhân viên Hùng',
                'sdt_ban' => '0901112222',
                'nguoi_mua' => $kh->nguoi_lien_he,
                'sdt_mua' => $kh->sdt,
                'created_by' => 1,
                'ghi_chu' => 'Đơn hàng tự động seed'
            ]);
            
            OrderMeta::create([
                'order_id' => $order->id,
                'cto_code' => $order->cto_code,
                'vat_percent' => rand(0, 1) == 1 ? 8 : 10
            ]);
            
            // 5. OrderItems
            $itemCount = rand(1, 4);
            $shuffledP = collect($products)->shuffle()->take($itemCount);
            foreach ($shuffledP as $p) {
                $sl = rand(2, 25);
                $idx = array_search($p->ten_hang, $spNames);
                $gia = $gias[$idx];
                
                OrderItem::create([
                    'order_id' => $order->id,
                    'cto_code' => $order->cto_code,
                    'ma_hang' => $p->ma_hang,
                    'ten_hang' => $p->ten_hang,
                    'mo_ta_phu' => 'Màu mặc định',
                    'so_luong' => $sl,
                    'don_vi_tinh' => $p->don_vi_tinh,
                    'don_gia' => $gia,
                    'thanh_tien' => $sl * $gia,
                ]);
            }
            
            $orders[] = $order;
        }

        // 6. Delivery Notes
        foreach($orders as $idx => $order) {
            if (in_array($order->trang_thai, ['Đang vận chuyển', 'Hoàn thành'])) {
                DeliveryNote::create([
                    'order_id' => $order->id,
                    'customer_id' => $order->customer_id,
                    'dn_code' => 'DN-' . Carbon::parse($order->order_date)->addDays(1)->format('ymd') . str_pad($idx+1, 3, '0', STR_PAD_LEFT),
                    'cto_code' => $order->cto_code,
                    'ma_kh' => $order->ma_kh,
                    'ten_kh' => $order->ten_kh,
                    'delivery_date' => Carbon::parse($order->order_date)->addDays(rand(1, 3)),
                    'han_thanh_toan' => rand(0, 1) ? 30 : 15,
                    'trang_thai' => $order->trang_thai == 'Hoàn thành' ? 'Đã giao xong' : 'Đang giao',
                    'nguoi_tao' => 'admin',
                ]);
            }
        }

        // 7. Payments
        $ttIds = 1;
        foreach($orders as $order) {
            if ($order->trang_thai == 'Hoàn thành') {
                if (rand(0, 1)) { // random 50% orders have payments
                    $total = OrderItem::where('order_id', $order->id)->sum('thanh_tien');
                    $vat = $total * (($order->meta->vat_percent ?? 0) / 100);
                    $totalAmount = $total + $vat;
                    
                    // Trả 1 nửa hoặc trả hết
                    $payAmount = rand(0, 1) ? $totalAmount : ($totalAmount / 2);
                    
                    Payment::create([
                        'order_id' => $order->id,
                        'customer_id' => $order->customer_id,
                        'ma_tt' => 'TT-' . Carbon::now()->format('ymdHi') . str_pad($ttIds++, 2, '0', STR_PAD_LEFT),
                        'cto_code' => $order->cto_code,
                        'ma_kh' => $order->ma_kh,
                        'payment_date' => Carbon::parse($order->order_date)->addDays(rand(5, 15)),
                        'so_tien' => $payAmount,
                        'nguoi_thu' => 'Kế toán Hương',
                        'ghi_chu' => 'Đã thu tiền đợt 1'
                    ]);
                }
            }
        }

        $this->command->info('✅ Dữ liệu mẫu đã được nạp thành công!');
        $this->command->line('Tạo thành công ~10 dòng cho mỗi bảng (Khách hàng, Đơn hàng, Phiếu giao, Tồn kho, Thanh toán...)');
    }
}
