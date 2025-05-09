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
        Schema::create('promotions', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Tên khuyến mãi
            $table->text('description')->nullable(); // Mô tả
            $table->enum('promotion_type', ['product', 'category', 'order', 'shipping']); // Loại khuyến mãi
            $table->decimal('discount_amount', 10, 2); // Giá trị giảm
            $table->enum('discount_unit', ['percentage', 'fixed']); // Đơn vị giảm (%, tiền)
            $table->decimal('min_order_value', 10, 2)->default(0); // Giá trị đơn hàng tối thiểu
            $table->decimal('max_discount_amount', 10, 2)->nullable(); // Giá trị giảm tối đa (cho % giảm)
            $table->date('start_date'); // Ngày bắt đầu
            $table->date('end_date'); // Ngày kết thúc
            $table->boolean('active')->default(true); // Trạng thái hoạt động
            $table->integer('usage_limit')->nullable(); // Giới hạn sử dụng
            $table->integer('usage_count')->default(0); // Số lần đã sử dụng
            $table->boolean('is_featured')->default(false); // Khuyến mãi nổi bật
            $table->string('promotion_image')->nullable(); // Hình ảnh khuyến mãi
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promotions');
    }
};