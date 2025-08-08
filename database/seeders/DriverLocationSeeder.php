<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Driver;
use App\Models\DriverLocation;

class DriverLocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Xóa dữ liệu cũ
        DriverLocation::truncate();
        
        // Lấy danh sách tài xế có trạng thái active
        $drivers = Driver::where('status', 'active')->get();
        
        // Tọa độ trung tâm Hà Nội
        $centerLat = 21.0278;
        $centerLng = 105.8342;
        
        foreach ($drivers as $index => $driver) {
            // Tạo tọa độ ngẫu nhiên xung quanh trung tâm
            $lat = $centerLat + (rand(-10, 10) / 100);
            $lng = $centerLng + (rand(-10, 10) / 100);
            
            DriverLocation::create([
                'driver_id' => $driver->id,
                'latitude' => $lat,
                'longitude' => $lng,
                'updated_at' => now(),
            ]);
        }
    }
}