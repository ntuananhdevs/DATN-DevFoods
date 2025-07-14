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
                    'license_plate' => '30A-' . $driverData['license_number'],
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

            // Tạo driver document
            DriverDocument::updateOrCreate(
                ['driver_id' => $driver->id],
                [
                    'license_number' => $driverData['license_number'],
                    'license_class' => 'A1',
                    'license_expiry' => now()->addYears(5),
                    'vehicle_type' => $driverData['vehicle_type'],
                    'vehicle_registration' => 'TEST-' . $driverData['license_number'],
                    'vehicle_color' => 'Đen',
                    'license_plate' => '30A-' . $driverData['license_number'],
                ]
            );
        }

        $this->command->info('Đã tạo ' . count($drivers) . ' tài xế test thành công!');
    }
}
