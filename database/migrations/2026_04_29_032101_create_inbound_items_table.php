<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inbound_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('receipt_id')->comment('FK -> inbound_receipts');
            $table->unsignedBigInteger('product_catalog_id')->nullable()->comment('FK -> product_catalog');
            $table->string('ma_hang', 100)->nullable()->comment('Mã hàng (snapshot)');
            $table->string('ten_hang', 255)->nullable()->comment('Tên hàng (snapshot)');
            $table->unsignedBigInteger('category_id')->nullable()->comment('Nhóm hàng');
            $table->string('don_vi_tinh', 50)->nullable()->comment('ĐVT');
            $table->string('quy_cach', 100)->nullable()->comment('Quy cách');
            $table->decimal('so_luong', 18, 4)->default(0)->comment('Số lượng nhập');
            $table->decimal('don_gia', 18, 2)->default(0)->comment('Đơn giá');
            $table->decimal('thanh_tien', 18, 2)->default(0)->comment('Thành tiền (SL * Đơn giá)');
            $table->string('so_lo', 100)->nullable()->comment('Số lô / LOT');
            $table->date('ngay_san_xuat')->nullable()->comment('Ngày sản xuất');
            $table->date('han_su_dung')->nullable()->comment('Hạn sử dụng');
            $table->string('kho_nhap', 100)->nullable()->comment('Vị trí kho cụ thể');
            $table->text('ghi_chu')->nullable();
            $table->integer('sort_order')->default(0)->comment('Thứ tự dòng');
            $table->timestamps();

            $table->foreign('receipt_id')->references('id')->on('inbound_receipts')->onDelete('cascade');
            $table->foreign('product_catalog_id')->references('id')->on('product_catalog')->onDelete('set null');
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inbound_items');
    }
};
