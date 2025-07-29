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
        Schema::create('review_replies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('review_id')->constrained('product_reviews')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Người phản hồi
            $table->text('reply'); // Nội dung phản hồi
            $table->dateTime('reply_date');
            $table->boolean('is_official')->default(false); // Phản hồi chính thức từ cửa hàng
            $table->boolean('is_hidden')->default(false); // Ẩn phản hồi
            $table->integer('helpful_count')->default(0); // Số lượt đánh giá hữu ích
            $table->integer('report_count')->default(0); // Số lượt báo cáo
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('review_replies');
    }
};