<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('ma_hang', 100);
            $table->string('ten_hang', 255);
            $table->text('mo_ta')->nullable();
            $table->decimal('so_luong_nhap', 15, 3)->default(0);
            $table->string('don_vi_tinh', 50)->nullable();
            $table->decimal('don_gia', 18, 2)->default(0);
            $table->text('ghi_chu')->nullable();
            $table->enum('trang_thai', ['Hoạt động', 'Ngừng'])->default('Hoạt động');
            $table->date('nhap_date');
            $table->timestamps();

            $table->index('ma_hang');
            $table->index('nhap_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
