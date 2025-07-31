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
                'latitude' => 21.033,
                'longitude' => 105.849,
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
                'latitude' => 21.034,
                'longitude' => 105.850,
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
                'latitude' => 21.035,
                'longitude' => 105.851,
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
                'latitude' => 21.036,
                'longitude' => 105.852,
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
                'latitude' => 21.037,
                'longitude' => 105.853,
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
                'latitude' => 21.038,
                'longitude' => 105.854,
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
                'latitude' => 21.039,
                'longitude' => 105.855,
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
                'latitude' => 21.040,
                'longitude' => 105.856,
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
                'latitude' => 21.041,
                'longitude' => 105.857,
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
                'latitude' => 21.042,
                'longitude' => 105.858,
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
                'latitude' => 21.043,
                'longitude' => 105.859,
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
                'latitude' => 21.044,
                'longitude' => 105.860,
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
                'latitude' => 21.045,
                'longitude' => 105.861,
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
                'latitude' => 21.046,
                'longitude' => 105.862,
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
                'latitude' => 21.047,
                'longitude' => 105.863,
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
                'latitude' => 21.048,
                'longitude' => 105.864,
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
                'latitude' => 21.049,
                'longitude' => 105.865,
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
                'latitude' => 21.050,
                'longitude' => 105.866,
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
                'latitude' => 21.051,
                'longitude' => 105.867,
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
                'latitude' => 21.052,
                'longitude' => 105.868,
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
                'latitude' => 21.053,
                'longitude' => 105.869,
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
                'latitude' => 21.054,
                'longitude' => 105.870,
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
                'latitude' => 21.055,
                'longitude' => 105.871,
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

            // Tạo driver location (chỉ dùng driver_id, latitude, longitude)
            DriverLocation::updateOrCreate(
                ['driver_id' => $driver->id],
                [
                    'latitude' => $driverData['latitude'],
                    'longitude' => $driverData['longitude'],
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
