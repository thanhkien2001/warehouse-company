<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $col) {
            $col->string('tinh_trang')->default('active')->after('khu_vuc');
            $col->string('tai_lieu')->nullable()->after('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $col) {
            $col->dropColumn(['tinh_trang', 'tai_lieu']);
        });
    }
};
