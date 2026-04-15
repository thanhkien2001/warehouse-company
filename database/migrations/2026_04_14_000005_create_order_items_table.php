<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->string('cto_code', 50);
            $table->string('ma_hang', 100)->nullable();
            $table->text('mo_ta')->nullable();
            $table->string('ten_hang', 255)->nullable();
            $table->text('mo_ta_phu')->nullable();
            $table->decimal('so_luong', 15, 3)->default(0);
            $table->string('don_vi_tinh', 50)->nullable();
            $table->decimal('don_gia', 18, 2)->default(0);
            $table->decimal('thanh_tien', 18, 2)->default(0);
            $table->string('ma_lot', 100)->nullable();
            $table->date('han_su_dung')->nullable();
            $table->string('quy_cach', 255)->nullable();
            $table->decimal('quy_doi', 15, 3)->default(0);
            $table->text('ghi_chu')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index('cto_code');
            $table->index('ma_hang');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
