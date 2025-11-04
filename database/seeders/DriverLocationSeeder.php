<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Driver;
use App\Models\DriverLocation;

class DriverLocationSeeder extends Seeder
{
    /**
     * Tạo tọa độ ngẫu nhiên trong phạm vi Hà Nội
     */
    private function generateHanoiCoordinates()
    {
        // Các khu vực khác nhau của Hà Nội với tọa độ trung tâm
        $hanoiAreas = [
            // Quận Hoàn Kiếm
            ['lat' => 21.0285, 'lng' => 105.8542, 'radius' => 0.01],
            // Quận Ba Đình
            ['lat' => 21.0367, 'lng' => 105.8345, 'radius' => 0.015],
            // Quận Đống Đa
            ['lat' => 21.0144, 'lng' => 105.8294, 'radius' => 0.02],
            // Quận Hai Bà Trưng
            ['lat' => 21.0058, 'lng' => 105.8469, 'radius' => 0.018],
            // Quận Hoàng Mai
            ['lat' => 20.9815, 'lng' => 105.8516, 'radius' => 0.025],
            // Quận Long Biên
            ['lat' => 21.0367, 'lng' => 105.8906, 'radius' => 0.03],
            // Quận Tây Hồ
            ['lat' => 21.0583, 'lng' => 105.8194, 'radius' => 0.025],
            // Quận Thanh Xuân
            ['lat' => 20.9881, 'lng' => 105.8019, 'radius' => 0.02],
            // Quận Cầu Giấy
            ['lat' => 21.0333, 'lng' => 105.7947, 'radius' => 0.018],
            // Quận Nam Từ Liêm
            ['lat' => 21.0378, 'lng' => 105.7644, 'radius' => 0.03],
            // Quận Bắc Từ Liêm
            ['lat' => 21.0608, 'lng' => 105.7756, 'radius' => 0.025],
            // Quận Hà Đông
            ['lat' => 20.9719, 'lng' => 105.7694, 'radius' => 0.025],
        ];
        
        // Chọn ngẫu nhiên một khu vực
        $area = $hanoiAreas[array_rand($hanoiAreas)];
        
        // Tạo tọa độ ngẫu nhiên trong bán kính của khu vực đó
        $angle = mt_rand(0, 360) * (M_PI / 180); // Góc ngẫu nhiên
        $distance = mt_rand(0, 100) / 100 * $area['radius']; // Khoảng cách ngẫu nhiên
        
        $lat = $area['lat'] + ($distance * cos($angle));
        $lng = $area['lng'] + ($distance * sin($angle));
        
        return [
            'latitude' => round($lat, 6),
            'longitude' => round($lng, 6)
        ];
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Xóa dữ liệu cũ
        DriverLocation::truncate();
        
        // Lấy danh sách tài xế có trạng thái active
        $drivers = Driver::where('status', 'active')->get();
        
        foreach ($drivers as $driver) {
            // Tạo tọa độ ngẫu nhiên cho tài xế
            $coordinates = $this->generateHanoiCoordinates();
            
            DriverLocation::create([
                'driver_id' => $driver->id,
                'latitude' => $coordinates['latitude'],
                'longitude' => $coordinates['longitude'],
                'updated_at' => now(),
            ]);
        }
        
        $this->command->info('Đã cập nhật tọa độ cho ' . $drivers->count() . ' tài xế với vị trí rải rác khắp Hà Nội!');
    }
}