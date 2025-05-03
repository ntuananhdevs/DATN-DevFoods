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
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Tên phương thức thanh toán
            $table->string('code')->unique(); // Mã phương thức thanh toán
            $table->text('description')->nullable(); // Mô tả
            $table->enum('type', ['cash', 'bank_transfer', 'credit_card', 'e_wallet', 'other']);
            $table->boolean('active')->default(true); // Trạng thái hoạt động
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_methods');
    }
};