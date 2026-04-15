<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('delivery_notes', function (Blueprint $table) {
            $table->id();
            $table->string('dn_code', 50)->unique();
            $table->foreignId('order_id')->constrained('orders');
            $table->string('cto_code', 50);
            $table->foreignId('customer_id')->constrained('customers');
            $table->string('ma_kh', 50);
            $table->string('ten_kh', 255);
            $table->enum('trang_thai', ['Chờ giao hàng', 'Đang giao', 'Đã giao xong', 'Đã hủy'])
                ->default('Chờ giao hàng');
            $table->integer('han_thanh_toan')->default(0);
            $table->string('nguoi_tao', 255)->nullable();
            $table->date('delivery_date');
            $table->text('dn_pdf_url')->nullable();
            $table->timestamps();

            $table->index('cto_code');
            $table->index('delivery_date');
            $table->index('trang_thai');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_notes');
    }
};
