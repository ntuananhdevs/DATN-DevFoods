<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Branch;
use App\Models\Address;
use App\Models\DriverLocation;
use Illuminate\Support\Facades\DB;

class UpdateLocationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Vị trí trung tâm (Hà Nội - khu vực Đống Đa)
        $centerLat = 21.0278;
        $centerLng = 105.8342;

        // 1. Cập nhật Branch ID 1
        Branch::where('id', 1)->update([
            'latitude' => $centerLat,
            'longitude' => $centerLng,
        ]);

        // 2. Cập nhật Address ID 12 (của user ID 6)
        Address::where('id', 12)->update([
            'latitude' => $centerLat + 0.002, // Cách branch khoảng 200m
            'longitude' => $centerLng + 0.002,
        ]);

        // 3. Cập nhật vị trí các tài xế để gần branch và khách hàng
        $driverLocations = [
            // Tài xế 1 - gần branch nhất
            [
                'driver_id' => 1,
                'latitude' => $centerLat + 0.001,
                'longitude' => $centerLng + 0.001,
            ],
            // Tài xế 2 - gần khách hàng nhất
            [
                'driver_id' => 2,
                'latitude' => $centerLat + 0.003,
                'longitude' => $centerLng + 0.003,
            ],
            // Tài xế 3 - ở giữa
            [
                'driver_id' => 3,
                'latitude' => $centerLat + 0.002,
                'longitude' => $centerLng + 0.001,
            ],
        ];

        foreach ($driverLocations as $location) {
            DriverLocation::updateOrCreate(
                ['driver_id' => $location['driver_id']],
                [
                    'latitude' => $location['latitude'],
                    'longitude' => $location['longitude'],
                ]
            );
        }

        $this->command->info('Đã cập nhật vị trí thành công!');
        $this->command->info('Branch ID 1: ' . $centerLat . ', ' . $centerLng);
        $this->command->info('Address ID 12: ' . ($centerLat + 0.002) . ', ' . ($centerLng + 0.002));
        $this->command->info('Các tài xế đã được đặt gần branch và khách hàng');
    }
} 