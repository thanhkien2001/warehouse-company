<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('cto_code', 50)->unique();
            $table->foreignId('customer_id')->constrained('customers');
            $table->string('ma_kh', 50);
            $table->string('ten_kh', 255);
            $table->text('ghi_chu')->nullable();
            $table->enum('trang_thai', ['Chờ xác nhận', 'Đang xử lý', 'Đang vận chuyển', 'Hoàn thành', 'Đã hủy'])
                ->default('Chờ xác nhận');
            $table->string('nguoi_ban', 150)->nullable();
            $table->string('sdt_ban', 20)->nullable();
            $table->string('nguoi_mua', 150)->nullable();
            $table->string('sdt_mua', 20)->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->date('order_date');
            $table->timestamps();

            $table->index('order_date');
            $table->index('trang_thai');
            $table->index('ma_kh');
        });

        Schema::create('order_meta', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->string('cto_code', 50)->unique();
            $table->string('tinh_trang', 100)->default('Chờ xác nhận');
            $table->string('ty_gia', 50)->nullable();
            $table->string('ngay_ty_gia', 20)->nullable();
            $table->decimal('vat_percent', 5, 2)->default(8.00);
            $table->text('pdf_url')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_meta');
        Schema::dropIfExists('orders');
    }
};
