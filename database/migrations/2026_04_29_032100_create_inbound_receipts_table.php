<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inbound_receipts', function (Blueprint $table) {
            $table->id();
            $table->string('receipt_code', 50)->unique()->comment('Số phiếu, VD: NK-2025-00001');
            $table->string('invoice_no', 100)->nullable()->comment('Số hóa đơn');
            $table->date('receipt_date')->comment('Ngày nhập');
            $table->date('invoice_date')->nullable()->comment('Ngày hóa đơn');
            $table->string('supplier_name', 255)->nullable()->comment('Nhà cung cấp');
            $table->string('supplier_code', 100)->nullable()->comment('Mã nhà cung cấp');
            $table->string('warehouse', 100)->nullable()->comment('Kho nhập');
            $table->string('origin', 100)->nullable()->comment('Xuất xứ');
            $table->string('department', 100)->nullable()->default('Kho')->comment('Bộ phận');
            $table->unsignedBigInteger('created_by')->nullable()->comment('Người nhập');
            $table->text('notes')->nullable()->comment('Ghi chú');
            $table->enum('status', ['draft', 'completed', 'cancelled'])->default('draft');
            $table->decimal('total_amount', 18, 2)->default(0)->comment('Tổng tiền');
            $table->timestamps();

            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inbound_receipts');
    }
};
