<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Address;
use App\Models\User;

echo "=== KIỂM TRA DỮ LIỆU ADDRESS ===\n\n";

// Kiểm tra User ID 6
echo "1. USER ID 6:\n";
$user = User::find(6);
if ($user) {
    echo "ID: {$user->id} | Email: {$user->email} | Name: {$user->name}\n";
} else {
    echo "❌ User ID 6 không tồn tại\n";
}

// Kiểm tra Address ID 12
echo "\n2. ADDRESS ID 12:\n";
$address = Address::find(12);
if ($address) {
    echo "ID: {$address->id} | User ID: {$address->user_id} | Address: {$address->address_line}\n";
    echo "Lat: {$address->latitude} | Lng: {$address->longitude}\n";
} else {
    echo "❌ Address ID 12 không tồn tại\n";
}

// Kiểm tra tất cả address của User ID 6
echo "\n3. TẤT CẢ ADDRESS CỦA USER ID 6:\n";
$userAddresses = Address::where('user_id', 6)->get();
if ($userAddresses->count() > 0) {
    foreach ($userAddresses as $addr) {
        echo "ID: {$addr->id} | Address: {$addr->address_line} | Lat: {$addr->latitude} | Lng: {$addr->longitude}\n";
    }
} else {
    echo "❌ User ID 6 không có address nào\n";
}

// Kiểm tra tất cả address có vị trí
echo "\n4. TẤT CẢ ADDRESS CÓ VỊ TRÍ:\n";
$addressesWithLocation = Address::whereNotNull('latitude')->whereNotNull('longitude')->get();
foreach ($addressesWithLocation as $addr) {
    echo "ID: {$addr->id} | User ID: {$addr->user_id} | Address: {$addr->address_line} | Lat: {$addr->latitude} | Lng: {$addr->longitude}\n";
} 