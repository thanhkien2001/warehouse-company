<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\InboundReceipt;
use App\Models\InboundItem;
use App\Models\ProductCatalog;
use Carbon\Carbon;

class InboundSeeder extends Seeder
{
    public function run(): void
    {
        // Truncate bảng liên quan trước khi seed lại
        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        \Illuminate\Support\Facades\DB::table('inbound_items')->truncate();
        \Illuminate\Support\Facades\DB::table('inbound_receipts')->truncate();
        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Lấy danh sách sản phẩm mẫu từ catalog
        $products = ProductCatalog::all();

        if ($products->isEmpty()) {
            // Nếu chưa có sản phẩm mẫu, chạy PharmaceuticalProductSeeder trước
            $this->call(PharmaceuticalProductSeeder::class);
            $products = ProductCatalog::all();
        }

        $suppliers = [
            'Dược Hậu Giang (DHG)',
            'Traphaco',
            'Dược phẩm Imexpharm',
            'Dược Mekophar',
            'Dược phẩm Boston',
            'Stella Pharma',
            'GlaxoSmithKline (GSK)'
        ];

        $warehouses = ['Kho Nguyên Liệu', 'Kho Lab'];

        for ($i = 1; $i <= 15; $i++) {
            $date = Carbon::now()->subDays(rand(1, 30));
            $receiptCode = 'NK-2026-' . str_pad($i, 5, '0', STR_PAD_LEFT);

            // Tạo Receipt
            $receipt = InboundReceipt::create([
                'receipt_code'  => $receiptCode,
                'invoice_no'    => 'HD-' . rand(10000, 99999),
                'receipt_date'  => $date->format('Y-m-d'),
                'invoice_date'  => $date->copy()->subDays(rand(1, 5))->format('Y-m-d'),
                'supplier_name' => $suppliers[array_rand($suppliers)],
                'warehouse'     => $warehouses[array_rand($warehouses)],
                'origin'        => 'Việt Nam',
                'department'    => 'Kho',
                'created_by'    => 1, // Mặc định gán user id 1
                'notes'         => 'Dữ liệu nhập kho tự động mẫu #' . $i,
                'status'        => 'completed',
                'total_amount'  => 0
            ]);

            // Tạo 1 - 3 dòng hàng hóa cho mỗi Receipt
            $itemCount = rand(1, 3);
            $total = 0;

            for ($j = 0; $j < $itemCount; $j++) {
                $p = $products->random();
                $qty = rand(20, 200);
                $price = $p->gia_nhap ?? rand(10000, 100000);
                $subtotal = $qty * $price;
                $total += $subtotal;

                InboundItem::create([
                    'receipt_id'         => $receipt->id,
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
                    'kho_nhap'           => $receipt->warehouse,
                    'sort_order'         => $j
                ]);
            }

            // Cập nhật lại tổng tiền Receipt
            $receipt->update(['total_amount' => $total]);
        }
    }
}
