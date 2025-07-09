<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Address;
use App\Models\User;

class AddressSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all(); // Use all users instead of only customers

        $addresses = [
            [
                'phone_number' => '0909123456',
                'address_line' => '1 Tràng Tiền',
                'ward' => 'Phường Tràng Tiền',
                'district' => 'Quận Hoàn Kiếm',
                'city' => 'Hà Nội',
                'latitude' => 21.0285,
                'longitude' => 105.8542,
                'is_default' => true,
            ],
            [
                'phone_number' => '0912345678',
                'address_line' => '10 Đinh Tiên Hoàng',
                'ward' => 'Phường Lý Thái Tổ',
                'district' => 'Quận Hoàn Kiếm',
                'city' => 'Hà Nội',
                'latitude' => 21.0307,
                'longitude' => 105.8520,
                'is_default' => false,
            ],
            [
                'phone_number' => '0987654321',
                'address_line' => '15 Hàng Bài',
                'ward' => 'Phường Hàng Bài',
                'district' => 'Quận Hoàn Kiếm',
                'city' => 'Hà Nội',
                'latitude' => 21.0257,
                'longitude' => 105.8525,
                'is_default' => false,
            ],
            [
                'phone_number' => '0978123456',
                'address_line' => '20 Lý Thường Kiệt',
                'ward' => 'Phường Phan Chu Trinh',
                'district' => 'Quận Hoàn Kiếm',
                'city' => 'Hà Nội',
                'latitude' => 21.0250,
                'longitude' => 105.8520,
                'is_default' => true,
            ],
            [
                'phone_number' => '0967123456',
                'address_line' => '25 Hai Bà Trưng',
                'ward' => 'Phường Tràng Tiền',
                'district' => 'Quận Hoàn Kiếm',
                'city' => 'Hà Nội',
                'latitude' => 21.0270,
                'longitude' => 105.8530,
                'is_default' => false,
            ],
            [
                'phone_number' => '0956123456',
                'address_line' => '30 Phan Chu Trinh',
                'ward' => 'Phường Phan Chu Trinh',
                'district' => 'Quận Hoàn Kiếm',
                'city' => 'Hà Nội',
                'latitude' => 21.0235,
                'longitude' => 105.8535,
                'is_default' => true,
            ],
            [
                'phone_number' => '0945123456',
                'address_line' => '35 Hàng Khay',
                'ward' => 'Phường Tràng Tiền',
                'district' => 'Quận Hoàn Kiếm',
                'city' => 'Hà Nội',
                'latitude' => 21.0272,
                'longitude' => 105.8505,
                'is_default' => false,
            ],
            [
                'phone_number' => '0934123456',
                'address_line' => '40 Quang Trung',
                'ward' => 'Phường Trần Hưng Đạo',
                'district' => 'Quận Hoàn Kiếm',
                'city' => 'Hà Nội',
                'latitude' => 21.0220,
                'longitude' => 105.8490,
                'is_default' => true,
            ],
            [
                'phone_number' => '0923123456',
                'address_line' => '45 Lê Thái Tổ',
                'ward' => 'Phường Hàng Trống',
                'district' => 'Quận Hoàn Kiếm',
                'city' => 'Hà Nội',
                'latitude' => 21.0280,
                'longitude' => 105.8495,
                'is_default' => false,
            ],
            [
                'phone_number' => '0912123456',
                'address_line' => '50 Hàng Gai',
                'ward' => 'Phường Hàng Gai',
                'district' => 'Quận Hoàn Kiếm',
                'city' => 'Hà Nội',
                'latitude' => 21.0330,
                'longitude' => 105.8490,
                'is_default' => true,
            ],
        ];

        foreach ($users as $user) {
            // Mỗi user có 1-3 địa chỉ
            $userAddressCount = rand(1, 3);
            $userAddresses = array_rand($addresses, $userAddressCount);
            
            if (!is_array($userAddresses)) {
                $userAddresses = [$userAddresses];
            }

            foreach ($userAddresses as $index => $addressIndex) {
                $addressData = $addresses[$addressIndex];
                $addressData['user_id'] = $user->id;
                $addressData['is_default'] = $index === 0; // Địa chỉ đầu tiên là mặc định
                
                Address::create($addressData);
            }
        }

        // Đảm bảo luôn có address cho user id=1
        $user1 = \App\Models\User::find(1);
        if ($user1 && !\App\Models\Address::where('user_id', 1)->exists()) {
            \App\Models\Address::create([
                'user_id' => 1,
                'phone_number' => '0909123456',
                'address_line' => '1 Test St',
                'ward' => 'Phường 1',
                'district' => 'Quận 1',
                'city' => 'HCM',
                'latitude' => 10.762622,
                'longitude' => 106.660172,
                'is_default' => true,
            ]);
        }
    }
} 