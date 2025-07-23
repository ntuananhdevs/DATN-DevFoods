<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Order;

$order = Order::where('status', 'delivered')->whereNull('driver_earning')->first();

if ($order) {
    echo "Order: " . $order->order_code . "\n";
    echo "Delivery fee: " . $order->delivery_fee . "\n";
    echo "Driver ID: " . $order->driver_id . "\n";
    
    if ($order->delivery_fee > 0) {
        $commissionRate = config('shipping.driver_commission_rate', 0.8);
        $order->driver_earning = $order->delivery_fee * $commissionRate;
        $order->save();
        echo "Updated driver_earning to: " . number_format($order->driver_earning) . " VND\n";
    } else {
        echo "Delivery fee is 0, cannot calculate driver earning\n";
    }
} else {
    echo "No orders found with delivered status and null driver_earning\n";
}