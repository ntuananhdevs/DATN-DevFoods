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
        Schema::create('discount_codes', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('description')->nullable();
            $table->decimal('discount_amount', 10, 2); // Giá trị giảm
            $table->decimal('minimum_order_value', 10, 2)->default(0); // Giá trị đơn hàng tối thiểu
            $table->enum('discount_type', ['percentage', 'fixed'])->default('percentage'); // % hoặc tiền cố định
            $table->integer('usage_limit')->nullable(); // Giới hạn sử dụng (null = không giới hạn)
            $table->integer('usage_count')->default(0); // Số lần đã sử dụng
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
        // First drop the foreign key constraint
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign('orders_discount_code_id_foreign');
        });

        // Then drop the table
        Schema::dropIfExists('discount_codes');
    }
};