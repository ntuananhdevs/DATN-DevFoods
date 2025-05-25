<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('users');
            $table->foreignId('branch_id')->constrained();
            $table->foreignId('driver_id')->nullable()->constrained('drivers');
            $table->foreignId('address_id')->nullable()->constrained('addresses');
            $table->foreignId('discount_code_id')->nullable()->constrained('discount_codes');
            $table->foreignId('payment_id')->nullable()->constrained('payments');
            $table->timestamp('estimated_delivery_time')->nullable();
            $table->timestamp('actual_delivery_time')->nullable();
            $table->decimal('delivery_fee', 12, 2)->default(0);
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->decimal('tax_amount', 12, 2)->default(0);
            $table->timestamp('order_date')->useCurrent();
            $table->timestamp('delivery_date')->nullable();
            $table->string('status', 50);
            $table->integer('points_earned')->default(0);
            $table->decimal('subtotal', 12, 2);
            $table->decimal('total_amount', 12, 2);
            $table->text('delivery_address')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('orders');
    }
};