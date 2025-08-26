<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Driver;
use App\Models\DriverApplication;
use App\Models\DriverLocation;
use App\Models\DriverDocument;
use Illuminate\Support\Facades\Hash;

class DriverSeeder extends Seeder
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
            ['lat' => 21.0278, 'lng' => 105.8342, 'radius' => 0.02],
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
     * Tính khoảng cách giữa hai điểm (Haversine formula)
     */
    private function calculateDistance($lat1, $lng1, $lat2, $lng2)
    {
        $earthRadius = 6371; // km
        
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        
        $a = sin($dLat/2) * sin($dLat/2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLng/2) * sin($dLng/2);
        
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        
        return $earthRadius * $c;
    }

    /**
     * Tạo tọa độ gần chi nhánh nhất
     */
    private function generateCoordinatesNearBranch($branchLat, $branchLng, $maxDistance = 2)
    {
        // Tạo tọa độ trong bán kính $maxDistance km từ chi nhánh
        $angle = mt_rand(0, 360) * (M_PI / 180);
        $distance = mt_rand(50, $maxDistance * 1000) / 1000; // 0.05km đến $maxDistance km
        
        // Chuyển đổi khoảng cách từ km sang độ (xấp xỉ)
        $distanceInDegrees = $distance / 111; // 1 độ ≈ 111km
        
        $lat = $branchLat + ($distanceInDegrees * cos($angle));
        $lng = $branchLng + ($distanceInDegrees * sin($angle));
        
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
        $drivers = [
            [
                'full_name' => 'Nguyễn Văn A',
                'email' => 'driver1@test.com',
                'phone_number' => '0981714620',
                'password' => '123456',
                'status' => 'active',
                'is_available' => true,
                'vehicle_type' => 'motorcycle',
                'license_number' => 'TEST001',
            ],
            [
                'full_name' => 'Nguyễn Văn B',
                'email' => 'driver2@test.com',
                'phone_number' => '0981714621',
                'password' => '123456',
                'status' => 'active',
                'is_available' => true,
                'vehicle_type' => 'motorcycle',
                'license_number' => 'TEST002',
            ],
            [
                'full_name' => 'Nguyễn Văn C',
                'email' => 'driver3@test.com',
                'phone_number' => '0981714622',
                'password' => '123456',
                'status' => 'active',
                'is_available' => true,
                'vehicle_type' => 'motorcycle',
                'license_number' => 'TEST003',
            ],
            // Thêm 20 tài xế mới
            [
                'full_name' => 'Trần Văn D',
                'email' => 'driver4@test.com',
                'phone_number' => '0981714623',
                'password' => '123456',
                'status' => 'active',
                'is_available' => true,
                'vehicle_type' => 'motorcycle',
                'license_number' => 'TEST004',
            ],
            [
                'full_name' => 'Lê Thị E',
                'email' => 'driver5@test.com',
                'phone_number' => '0981714624',
                'password' => '123456',
                'status' => 'active',
                'is_available' => true,
                'vehicle_type' => 'motorcycle',
                'license_number' => 'TEST005',
            ],
            [
                'full_name' => 'Phạm Văn F',
                'email' => 'driver6@test.com',
                'phone_number' => '0981714625',
                'password' => '123456',
                'status' => 'active',
                'is_available' => true,
                'vehicle_type' => 'motorcycle',
                'license_number' => 'TEST006',
            ],
            [
                'full_name' => 'Hoàng Thị G',
                'email' => 'driver7@test.com',
                'phone_number' => '0981714626',
                'password' => '123456',
                'status' => 'active',
                'is_available' => true,
                'vehicle_type' => 'motorcycle',
                'license_number' => 'TEST007',
            ],
            [
                'full_name' => 'Vũ Văn H',
                'email' => 'driver8@test.com',
                'phone_number' => '0981714627',
                'password' => '123456',
                'status' => 'active',
                'is_available' => true,
                'vehicle_type' => 'motorcycle',
                'license_number' => 'TEST008',
            ],
            [
                'full_name' => 'Đặng Thị I',
                'email' => 'driver9@test.com',
                'phone_number' => '0981714628',
                'password' => '123456',
                'status' => 'active',
                'is_available' => true,
                'vehicle_type' => 'motorcycle',
                'license_number' => 'TEST009',
            ],
            [
                'full_name' => 'Bùi Văn K',
                'email' => 'driver10@test.com',
                'phone_number' => '0981714629',
                'password' => '123456',
                'status' => 'active',
                'is_available' => true,
                'vehicle_type' => 'motorcycle',
                'license_number' => 'TEST010',
            ],
            [
                'full_name' => 'Đinh Thị L',
                'email' => 'driver11@test.com',
                'phone_number' => '0981714630',
                'password' => '123456',
                'status' => 'active',
                'is_available' => true,
                'vehicle_type' => 'motorcycle',
                'license_number' => 'TEST011',
            ],
            [
                'full_name' => 'Dương Văn M',
                'email' => 'driver12@test.com',
                'phone_number' => '0981714631',
                'password' => '123456',
                'status' => 'active',
                'is_available' => true,
                'vehicle_type' => 'motorcycle',
                'license_number' => 'TEST012',
            ],
            [
                'full_name' => 'Cao Thị N',
                'email' => 'driver13@test.com',
                'phone_number' => '0981714632',
                'password' => '123456',
                'status' => 'active',
                'is_available' => true,
                'vehicle_type' => 'motorcycle',
                'license_number' => 'TEST013',
            ],
            [
                'full_name' => 'Lý Văn O',
                'email' => 'driver14@test.com',
                'phone_number' => '0981714633',
                'password' => '123456',
                'status' => 'active',
                'is_available' => true,
                'vehicle_type' => 'motorcycle',
                'license_number' => 'TEST014',
            ],
            [
                'full_name' => 'Mạc Thị P',
                'email' => 'driver15@test.com',
                'phone_number' => '0981714634',
                'password' => '123456',
                'status' => 'active',
                'is_available' => true,
                'vehicle_type' => 'motorcycle',
                'license_number' => 'TEST015',
            ],
            [
                'full_name' => 'Tô Văn Q',
                'email' => 'driver16@test.com',
                'phone_number' => '0981714635',
                'password' => '123456',
                'status' => 'active',
                'is_available' => true,
                'vehicle_type' => 'motorcycle',
                'license_number' => 'TEST016',
            ],
            [
                'full_name' => 'Hồ Thị R',
                'email' => 'driver17@test.com',
                'phone_number' => '0981714636',
                'password' => '123456',
                'status' => 'active',
                'is_available' => true,
                'vehicle_type' => 'motorcycle',
                'license_number' => 'TEST017',
            ],
            [
                'full_name' => 'Võ Văn S',
                'email' => 'driver18@test.com',
                'phone_number' => '0981714637',
                'password' => '123456',
                'status' => 'active',
                'is_available' => true,
                'vehicle_type' => 'motorcycle',
                'license_number' => 'TEST018',
            ],
            [
                'full_name' => 'Đỗ Thị T',
                'email' => 'driver19@test.com',
                'phone_number' => '0981714638',
                'password' => '123456',
                'status' => 'active',
                'is_available' => true,
                'vehicle_type' => 'motorcycle',
                'license_number' => 'TEST019',
            ],
            [
                'full_name' => 'Nông Văn U',
                'email' => 'driver20@test.com',
                'phone_number' => '0981714639',
                'password' => '123456',
                'status' => 'active',
                'is_available' => true,
                'vehicle_type' => 'motorcycle',
                'license_number' => 'TEST020',
            ],
            [
                'full_name' => 'Kiều Thị V',
                'email' => 'driver21@test.com',
                'phone_number' => '0981714640',
                'password' => '123456',
                'status' => 'active',
                'is_available' => true,
                'vehicle_type' => 'motorcycle',
                'license_number' => 'TEST021',
            ],
            [
                'full_name' => 'Ông Văn W',
                'email' => 'driver22@test.com',
                'phone_number' => '0981714641',
                'password' => '123456',
                'status' => 'active',
                'is_available' => true,
                'vehicle_type' => 'motorcycle',
                'license_number' => 'TEST022',
            ],
            [
                'full_name' => 'Ứng Thị X',
                'email' => 'driver23@test.com',
                'phone_number' => '0981714642',
                'password' => '123456',
                'status' => 'active',
                'is_available' => true,
                'vehicle_type' => 'motorcycle',
                'license_number' => 'TEST023',
            ],
        ];

        foreach ($drivers as $driverData) {
            // Tạo driver application
            $application = DriverApplication::updateOrCreate(
                ['email' => $driverData['email']],
                [
                    'full_name' => $driverData['full_name'],
                    'email' => $driverData['email'],
                    'phone_number' => $driverData['phone_number'],
                    'status' => 'approved',
                    'date_of_birth' => '1990-01-01',
                    'gender' => 'male',
                    'id_card_number' => 'TEST' . $driverData['license_number'],
                    'id_card_issue_date' => '2010-01-01',
                    'id_card_issue_place' => 'Hà Nội',
                    'address' => 'Hà Nội, Việt Nam',
                    'vehicle_type' => $driverData['vehicle_type'],
                    'vehicle_model' => 'Honda Wave',
                    'vehicle_color' => 'Đen',
                    'license_plate' => '30A-' . str_pad(substr($driverData['license_number'], -3), 5, '0', STR_PAD_LEFT),
                    'driver_license_number' => $driverData['license_number'],
                    'bank_name' => 'Vietcombank',
                    'bank_account_number' => '1234567890',
                    'bank_account_name' => $driverData['full_name'],
                    'emergency_contact_name' => 'Liên hệ khẩn cấp',
                    'emergency_contact_phone' => '0987654321',
                    'emergency_contact_relationship' => 'Gia đình',
                ]
            );

            // Tạo driver
            $driver = Driver::updateOrCreate(
                ['email' => $driverData['email']],
                [
                    'full_name' => $driverData['full_name'],
                    'email' => $driverData['email'],
                    'phone_number' => $driverData['phone_number'],
                    'password' => $driverData['password'],
                    'status' => $driverData['status'],
                    'is_available' => $driverData['is_available'],
                    'application_id' => $application->id,
                    'address' => 'Hà Nội, Việt Nam',
                ]
            );

            // Tạo tọa độ cho tài xế - Nguyễn Văn A sẽ ở gần chi nhánh Đống Đa
            if ($driverData['full_name'] === 'Nguyễn Văn A') {
                // Tọa độ chi nhánh Đống Đa
                $dongDaLat = 21.0278;
                $dongDaLng = 105.8342;
                $coordinates = $this->generateCoordinatesNearBranch($dongDaLat, $dongDaLng, 1); // Trong bán kính 1km
            } else {
                $coordinates = $this->generateHanoiCoordinates();
            }
            
            // Tạo driver location
            DriverLocation::updateOrCreate(
                ['driver_id' => $driver->id],
                [
                    'latitude' => $coordinates['latitude'],
                    'longitude' => $coordinates['longitude'],
                ]
            );

            // Tạo driver document với đầy đủ thông tin xe từ migration
            DriverDocument::updateOrCreate(
                ['driver_id' => $driver->id],
                [
                    // Thông tin giấy phép lái xe
                    'license_number' => $driverData['license_number'],
                    'license_class' => 'A1',
                    'license_expiry' => now()->addYears(5),
                    'license_front' => null, // Có thể thêm đường dẫn ảnh sau
                    'license_back' => null,  // Có thể thêm đường dẫn ảnh sau
                    
                    // Thông tin giấy tờ tùy thân
                    'id_card_front' => null, // Có thể thêm đường dẫn ảnh sau
                    'id_card_back' => null,  // Có thể thêm đường dẫn ảnh sau
                    
                    // Thông tin xe (theo migration)
                    'vehicle_type' => $driverData['vehicle_type'],
                    'vehicle_registration' => 'REG-' . $driverData['license_number'], // Số đăng ký xe
                    'vehicle_color' => 'Đen',
                    'license_plate' => '30A-' . str_pad(substr($driverData['license_number'], -3), 5, '0', STR_PAD_LEFT),
                ]
            );
        }

        $this->command->info('Đã tạo ' . count($drivers) . ' tài xế test thành công! (Bao gồm 20 tài xế mới được thêm vào)');
    }
}
