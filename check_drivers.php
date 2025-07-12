<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Driver;
use App\Models\DriverLocation;

echo "=== KIỂM TRA DỮ LIỆU TÀI XẾ ===\n\n";

// Kiểm tra drivers
echo "1. DANH SÁCH DRIVERS:\n";
$drivers = Driver::all();
foreach ($drivers as $driver) {
    echo "ID: {$driver->id} | Name: {$driver->full_name} | Status: {$driver->status} | Available: " . ($driver->is_available ? 'Yes' : 'No') . "\n";
}

echo "\n2. DRIVERS ACTIVE VÀ AVAILABLE:\n";
$activeDrivers = Driver::where('status', 'active')->where('is_available', true)->get();
foreach ($activeDrivers as $driver) {
    echo "ID: {$driver->id} | Name: {$driver->full_name}\n";
}

echo "\n3. DRIVER LOCATIONS:\n";
$locations = DriverLocation::all();
foreach ($locations as $location) {
    echo "Driver ID: {$location->driver_id} | Lat: {$location->latitude} | Lng: {$location->longitude}\n";
}

echo "\n4. QUERY TRONG JOB:\n";
$availableDrivers = Driver::where('status', 'active')
    ->where('is_available', true)
    ->with('location')
    ->get();

echo "Số tài xế tìm được: " . $availableDrivers->count() . "\n";
foreach ($availableDrivers as $driver) {
    echo "Driver: {$driver->full_name} | Location: " . ($driver->location ? "{$driver->location->latitude}, {$driver->location->longitude}" : "Không có vị trí") . "\n";
} 