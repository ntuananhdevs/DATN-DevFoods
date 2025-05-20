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
        Schema::create('point_rules', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // VD: "Mặc định", "Khuyến mãi cuối tuần"
            $table->decimal('point_per_currency', 10, 4)->default(0.01); // VD: 1 điểm mỗi 100đ
            $table->decimal('min_order_amount', 10, 2)->default(0); // điều kiện tối thiểu
            $table->enum('customer_type', ['all', 'regular', 'vip'])->default('all'); // nếu cần
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discounts');
    }
};