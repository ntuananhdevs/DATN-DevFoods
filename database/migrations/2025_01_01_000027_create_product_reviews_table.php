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
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('product_id')->nullable()->constrained('products')->onDelete('cascade');
            $table->foreignId('combo_id')->nullable()->constrained('combos')->onDelete('cascade');
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->foreignId('branch_id')->nullable()->constrained('branches')->onDelete('set null'); // phân biệt chi nhánh
            $table->integer('rating'); // 1-5 sao
            $table->text('review')->nullable();
            $table->dateTime('review_date');
            $table->string('review_image')->nullable();
            $table->boolean('is_verified_purchase')->default(true);
            $table->integer('helpful_count')->default(0);
            $table->integer('report_count')->default(0);
            $table->boolean('is_featured')->default(false);
            $table->timestamps();
            
            // Mỗi user chỉ có thể review 1 lần cho mỗi product/combo trong mỗi order
            $table->unique(['user_id', 'product_id', 'combo_id', 'order_id']);
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