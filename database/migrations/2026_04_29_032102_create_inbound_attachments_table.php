<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inbound_attachments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('receipt_id')->comment('FK -> inbound_receipts');
            $table->string('original_name', 255)->comment('Tên file gốc');
            $table->string('stored_name', 255)->comment('Tên file lưu trữ');
            $table->string('file_path', 500)->comment('Đường dẫn file storage');
            $table->string('mime_type', 100)->nullable();
            $table->unsignedBigInteger('file_size')->nullable()->comment('Bytes');
            $table->timestamps();

            $table->foreign('receipt_id')->references('id')->on('inbound_receipts')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inbound_attachments');
    }
};
