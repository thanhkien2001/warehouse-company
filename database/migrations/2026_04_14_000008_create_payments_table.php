<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('ma_tt', 50)->unique();
            $table->foreignId('order_id')->nullable()->constrained('orders')->onDelete('set null');
            $table->string('cto_code', 50);
            $table->foreignId('customer_id')->nullable()->constrained('customers')->onDelete('set null');
            $table->string('ma_kh', 50);
            $table->decimal('so_tien', 18, 2);
            $table->string('nguoi_thu', 255)->nullable();
            $table->text('ghi_chu')->nullable();
            $table->timestamp('payment_date')->useCurrent();
            $table->timestamp('created_at')->useCurrent();

            $table->index('cto_code');
            $table->index('payment_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
