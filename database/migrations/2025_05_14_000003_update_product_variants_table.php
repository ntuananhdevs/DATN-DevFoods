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
        // Kiểm tra xem bảng product_variants đã tồn tại chưa
        if (Schema::hasTable('product_variants')) {
            // Xóa cột name nếu tồn tại
            if (Schema::hasColumn('product_variants', 'name')) {
                Schema::table('product_variants', function (Blueprint $table) {
                    $table->dropColumn('name');
                });
            }
        } else {
            // Tạo bảng product_variants nếu chưa tồn tại
            Schema::create('product_variants', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_id')->constrained('products');
                $table->decimal('price', 10, 2);
                $table->string('image')->nullable();
                $table->integer('stock_quantity')->default(0);
                $table->boolean('active')->default(true);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Không thực hiện gì trong down vì chúng ta không muốn xóa bảng
    }
};