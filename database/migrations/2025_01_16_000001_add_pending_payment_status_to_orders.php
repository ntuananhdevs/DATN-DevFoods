<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Thêm 'pending_payment' vào enum status của bảng orders
        DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM(
            'pending_payment',
            'awaiting_confirmation',
            'confirmed',
            'awaiting_driver',
            'driver_confirmed',
            'waiting_driver_pick_up',
            'driver_picked_up',
            'in_transit',
            'delivered',
            'item_received',
            'cancelled',
            'refunded',
            'payment_failed',
            'payment_received',
            'order_failed'
        ) DEFAULT 'pending_payment'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Xóa 'pending_payment' khỏi enum status và đặt lại default
        DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM(
            'awaiting_confirmation',
            'confirmed',
            'awaiting_driver',
            'driver_confirmed',
            'waiting_driver_pick_up',
            'driver_picked_up',
            'in_transit',
            'delivered',
            'item_received',
            'cancelled',
            'refunded',
            'payment_failed',
            'payment_received',
            'order_failed'
        ) DEFAULT 'awaiting_confirmation'");
    }
};