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
        Schema::create('reward_point_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('order_id')->nullable()->constrained('orders')->onDelete('set null'); // Ghi nhận điểm từ đơn hàng nào
            $table->integer('points'); // Có thể là số âm (trừ điểm) hoặc số dương (cộng điểm)
            $table->string('reason'); // Ví dụ: "Tích điểm từ đơn hàng #123", "Đổi voucher XYZ"
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reward_point_histories');
    }
};