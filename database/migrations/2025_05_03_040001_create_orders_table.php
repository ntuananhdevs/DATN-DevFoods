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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches');
            $table->foreignId('customer_id')->constrained('users');
            $table->foreignId('driver_id')->nullable()->constrained('drivers');
            $table->foreignId('address_id')->constrained('addresses');
            $table->foreignId('payment_id')->nullable()->constrained('payments');
            $table->foreignId('discount_code_id')->nullable()->constrained('discount_codes');
            $table->string('order_number')->unique();
            $table->dateTime('order_date');
            $table->dateTime('estimated_delivery_time')->nullable();
            $table->dateTime('actual_delivery_time')->nullable();
            $table->enum('status', ['new', 'processing', 'ready', 'delivery', 'completed', 'cancelled'])->default('new');
            $table->decimal('subtotal', 10, 2);
            $table->decimal('delivery_fee', 10, 2)->default(0);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('tax_amount', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2);
            $table->integer('points_earned')->default(0);
            $table->enum('points_status', ['awarded', 'pending', 'cancelled', 'refunded'])->default('pending');
            $table->text('notes')->nullable();
            
            // Thông tin hoàn tiền
            $table->enum('refund_status', ['requested', 'processing', 'refunded', 'rejected'])->nullable();
            $table->decimal('refunded_amount', 10, 2)->default(0);
            $table->string('responsible_party')->nullable();
            
            // Thông tin xác nhận giao hàng
            $table->string('delivery_confirmation_photo')->nullable();
            $table->dateTime('delivery_confirmation_time')->nullable();
            $table->string('delivery_confirmation_gps')->nullable();
            
            // Thông tin thanh toán bằng số dư
            $table->boolean('is_balance_payment')->default(false);
            $table->string('transaction_reference')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};