<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_catalog', function (Blueprint $table) {
            $table->id();
            $table->string('ma_hang', 100)->unique();
            $table->string('ten_hang', 255);
            $table->unsignedBigInteger('category_id')->nullable();
            $table->string('quy_cach', 100)->nullable();
            $table->string('don_vi_tinh', 50)->nullable();
            $table->decimal('gia_ban', 18, 2)->nullable();
            $table->decimal('gia_nhap', 18, 2)->nullable();
            $table->tinyInteger('vat')->default(10); // 0,5,8,10
            $table->string('nha_cung_cap', 255)->nullable();
            $table->string('ma_ncc', 100)->nullable();
            $table->enum('trang_thai', ['Hoạt động', 'Ngừng hoạt động'])->default('Hoạt động');
            $table->text('ghi_chu')->nullable();
            $table->timestamps();

            $table->foreign('category_id')->references('id')->on('categories')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_catalog');
    }
};
