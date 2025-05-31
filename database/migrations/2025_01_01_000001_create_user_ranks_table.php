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
        Schema::create('user_ranks', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Đồng, Bạc, Vàng, Bạch Kim, Kim Cương
            $table->string('slug')->unique(); // bronze, silver, gold, platinum, diamond
            $table->string('color', 7)->default('#CD7F32'); // Màu đại diện
            $table->string('icon')->nullable(); // Icon rank
            $table->decimal('min_spending', 12, 2)->default(0); // Chi tiêu tối thiểu để đạt rank
            $table->integer('min_orders')->default(0); // Số đơn hàng tối thiểu
            $table->decimal('discount_percentage', 5, 2)->default(0); // % giảm giá mặc định
            $table->json('benefits')->nullable(); // Quyền lợi khác (JSON)
            $table->integer('display_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_ranks');
    }
};
