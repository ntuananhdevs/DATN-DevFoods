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
        Schema::create('discounts', function (Blueprint $table) {
            $table->id();
            $table->enum('rank', ['bronze', 'silver', 'gold', 'diamond']); // Đồng nhất với bảng customer_ranks
            $table->decimal('discount_rate', 5, 2); // Tỷ lệ giảm giá (0-100%)
            $table->date('valid_from');
            $table->date('valid_to');
            $table->boolean('active')->default(true);
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