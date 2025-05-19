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
        Schema::create('product_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('product_id')->constrained('products');
            $table->foreignId('order_id')->constrained('orders');
            $table->integer('rating'); // 1-5 sao
            $table->text('review')->nullable(); // Nội dung đánh giá
            $table->dateTime('review_date');
            $table->boolean('approved')->default(false); // Đã duyệt chưa
            $table->string('review_image')->nullable(); // URL hình ảnh đánh giá
            $table->boolean('is_verified_purchase')->default(true); // Là người mua đã xác minh
            $table->boolean('is_anonymous')->default(false); // Đánh giá ẩn danh
            $table->integer('helpful_count')->default(0); // Số lượt đánh giá hữu ích
            $table->integer('report_count')->default(0); // Số lượt báo cáo
            $table->boolean('is_featured')->default(false); // Đánh giá nổi bật
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_reviews');
    }
};