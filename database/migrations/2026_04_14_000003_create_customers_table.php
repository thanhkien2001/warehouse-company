<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('ma_kh', 50)->unique();
            $table->string('ten_cty', 255);
            $table->string('ma_so_thue', 50)->unique();
            $table->string('dia_chi', 500)->nullable();
            $table->string('nguoi_lien_he', 150)->nullable();
            $table->string('sdt', 20)->nullable();
            $table->string('dia_chi_nhan', 500)->nullable();
            $table->string('sdt_nhan', 20)->nullable();
            $table->string('email', 150)->nullable();
            $table->enum('khu_vuc', ['Miền Bắc', 'Miền Trung', 'Miền Nam', ''])->default('');
            $table->text('ghi_chu')->nullable();
            $table->date('created_date')->nullable();
            $table->timestamps();

            $table->index('khu_vuc');
            $table->index('created_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
