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
        Schema::create('order_cancellations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders');
            $table->foreignId('cancelled_by')->nullable()->constrained('users');
            $table->enum('cancellation_type', ['customer_cancel', 'driver_cancel', 'restaurant_cancel', 'system_cancel']);
            $table->dateTime('cancellation_date');
            $table->text('reason');
            $table->enum('cancellation_stage', ['before_processing', 'processing', 'ready_for_delivery', 'during_delivery']);
            $table->boolean('penalty_applied')->default(false);
            $table->decimal('penalty_amount', 10, 2)->default(0);
            $table->integer('points_deducted')->default(0);
            $table->string('evidence')->nullable(); // URL hình ảnh
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_cancellations');
    }
};