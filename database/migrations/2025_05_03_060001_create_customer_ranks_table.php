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
        Schema::create('customer_ranks', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Tên hạng (bronze, silver, gold, diamond)
            $table->enum('rank', ['bronze', 'silver', 'gold', 'diamond']);
            $table->integer('min_points'); // Số điểm tối thiểu để đạt hạng
            $table->integer('max_points')->nullable(); // Số điểm tối đa của hạng (null cho hạng cao nhất)
            $table->decimal('discount_rate', 5, 2); // Tỷ lệ giảm giá (0-100%)
            $table->integer('points_multiplier')->default(1); // Hệ số nhân điểm thưởng
            $table->boolean('free_shipping')->default(false); // Miễn phí vận chuyển
            $table->integer('priority_support')->default(0); // Mức độ ưu tiên hỗ trợ
            $table->text('benefits')->nullable(); // Các quyền lợi khác
            $table->string('badge_image')->nullable(); // Hình ảnh huy hiệu
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_ranks');
    }
};