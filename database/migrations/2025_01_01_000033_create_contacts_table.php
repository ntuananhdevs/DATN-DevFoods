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
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->string('full_name'); // Họ và tên
            $table->string('email')->nullable()->unique(); // Email
            $table->string('phone')->nullable()->unique(); // Số điện thoại
            $table->string('subject'); // Chủ đề của liên hệs
            $table->text('message'); // Tin nhắn
            $table->timestamps(); // Thời gian tạo và cập nhật
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};
