<?php
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Cập nhật tọa độ cho đơn hàng ID 53
$order = \App\Models\Order::find(53);

if ($order) {
    echo "Đang cập nhật tọa độ cho đơn hàng ID: {$order->id}, Mã: {$order->order_code}\n";
    echo "Địa chỉ giao hàng: {$order->delivery_address}\n";
    
    // Cập nhật tọa độ (sử dụng tọa độ của Hà Nội)
    $order->guest_latitude = 21.0278;
    $order->guest_longitude = 105.8342;
    $order->save();
    
    echo "\nĐã cập nhật tọa độ thành công:\n";
    echo "Guest Latitude: {$order->guest_latitude}\n";
    echo "Guest Longitude: {$order->guest_longitude}\n";
} else {
    echo "Không tìm thấy đơn hàng ID 53.\n";
}