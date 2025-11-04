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
        Schema::create('driver_ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('driver_id')->constrained('drivers')->onDelete('cascade');
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->integer('rating'); // 1-5 sao
            $table->text('comment')->nullable();
            $table->boolean('is_anonymous')->default(false); // Cho phép đánh giá ẩn danh
            $table->timestamp('rated_at');
            $table->boolean('is_flagged')->default(false); // Đánh dấu đánh giá không phù hợp
            $table->text('admin_notes')->nullable(); // Ghi chú của admin
            $table->timestamps();
            
            // Mỗi user chỉ có thể đánh giá 1 lần cho mỗi tài xế trong mỗi đơn hàng
            $table->unique(['user_id', 'driver_id', 'order_id']);
            
            // Indexes
            $table->index('driver_id');
            $table->index('order_id');
            $table->index('rating');
            $table->index('rated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('driver_ratings');
    }
};