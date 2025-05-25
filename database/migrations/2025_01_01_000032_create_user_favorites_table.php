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
        Schema::create('user_favorites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');  // Liên kết với người dùng
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');  // Liên kết với sản phẩm
            $table->timestamps();

            // Đảm bảo rằng mỗi người dùng chỉ có một bản ghi cho mỗi sản phẩm
            $table->unique(['user_id', 'product_id']);
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_favorites');
    }
};
