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
            $table->foreignId('branch_id')->nullable()->constrained('branches'); // phân biệt chi nhánh
            $table->integer('rating'); // 1-5 sao
            $table->text('review')->nullable();
            $table->dateTime('review_date');
            $table->boolean('approved')->default(false);
            $table->string('review_image')->nullable();
            $table->boolean('is_verified_purchase')->default(true);
            $table->boolean('is_anonymous')->default(false);
            $table->integer('helpful_count')->default(0);
            $table->integer('report_count')->default(0);
            $table->boolean('is_featured')->default(false);
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