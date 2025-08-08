<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Order;
use Illuminate\Support\Facades\DB;

// Test order 56
$order = Order::find(56);
if (!$order) {
    echo "Order 56 not found\n";
    exit;
}

echo "Order found: {$order->id}\n";
echo "Status: {$order->status}\n";

// Get delivery coordinates
$deliveryLat = $order->address->latitude ?? $order->guest_latitude;
$deliveryLng = $order->address->longitude ?? $order->guest_longitude;

echo "Delivery coordinates: {$deliveryLat}, {$deliveryLng}\n";

if (!$deliveryLat || !$deliveryLng) {
    echo "No delivery coordinates found\n";
    exit;
}

// Test driver query
$drivers = DB::table('drivers')
    ->join('driver_locations', function($join) {
        $join->on('drivers.id', '=', 'driver_locations.driver_id')
             ->whereRaw('driver_locations.id = (
                 SELECT MAX(id) FROM driver_locations dl 
                 WHERE dl.driver_id = drivers.id
             )');
    })
    ->select(
        'drivers.id',
        'drivers.full_name',
        'drivers.phone_number',
        'drivers.is_available',
        'driver_locations.latitude',
        'driver_locations.longitude',
        'driver_locations.updated_at as location_updated_at',
        DB::raw('(
            6371 * acos(
                cos(radians(' . $deliveryLat . '))
                * cos(radians(driver_locations.latitude))
                * cos(radians(driver_locations.longitude) - radians(' . $deliveryLng . '))
                + sin(radians(' . $deliveryLat . '))
                * sin(radians(driver_locations.latitude))
            )
        ) AS distance')
    )
    ->where('drivers.is_available', true)
    ->where('drivers.status', 'active')
    ->whereNotNull('driver_locations.latitude')
    ->whereNotNull('driver_locations.longitude')
    ->whereRaw('(
        6371 * acos(
            cos(radians(' . $deliveryLat . '))
            * cos(radians(driver_locations.latitude))
            * cos(radians(driver_locations.longitude) - radians(' . $deliveryLng . '))
            + sin(radians(' . $deliveryLat . '))
            * sin(radians(driver_locations.latitude))
        )
    ) <= 10') // Within 10km radius
    ->orderBy('distance')
    ->limit(20)
    ->get();

echo "Found {$drivers->count()} drivers:\n";
foreach ($drivers as $driver) {
    echo "- {$driver->full_name}: {$driver->latitude}, {$driver->longitude} (Distance: {$driver->distance}km)\n";
}