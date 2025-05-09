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
        Schema::create('points_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->integer('points'); // Số điểm (dương: cộng điểm, âm: trừ điểm)
            $table->integer('balance'); // Số dư điểm sau giao dịch
            $table->enum('type', ['order', 'referral', 'review', 'promotion', 'adjustment', 'expiration']);
            $table->foreignId('order_id')->nullable()->constrained('orders');
            $table->foreignId('review_id')->nullable()->constrained('product_reviews');
            $table->string('reference')->nullable(); // Mã tham chiếu
            $table->text('description')->nullable(); // Mô tả giao dịch
            $table->dateTime('transaction_date'); // Ngày giao dịch
            $table->dateTime('expiry_date')->nullable(); // Ngày hết hạn điểm
            $table->boolean('is_expired')->default(false); // Đã hết hạn chưa
            $table->foreignId('created_by')->nullable()->constrained('users'); // Người tạo giao dịch
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('points_transactions');
    }
};