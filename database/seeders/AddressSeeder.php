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
                'phone_number' => '0123456789',
                'address_line' => '123 Đường Nguyễn Huệ',
                'ward' => 'Phường Bến Nghé',
                'district' => 'Quận 1',
                'city' => 'TP. Hồ Chí Minh',
                'latitude' => 10.7769,
                'longitude' => 106.7009,
                'is_default' => true,
            ],
            [
                'phone_number' => '0987654321',
                'address_line' => '456 Đường Lê Lợi',
                'ward' => 'Phường Bến Thành',
                'district' => 'Quận 1',
                'city' => 'TP. Hồ Chí Minh',
                'latitude' => 10.7769,
                'longitude' => 106.7009,
                'is_default' => false,
            ],
            [
                'phone_number' => '0369852147',
                'address_line' => '789 Đường Pasteur',
                'ward' => 'Phường Nguyễn Thái Bình',
                'district' => 'Quận 1',
                'city' => 'TP. Hồ Chí Minh',
                'latitude' => 10.7769,
                'longitude' => 106.7009,
                'is_default' => false,
            ],
            [
                'phone_number' => '0521478963',
                'address_line' => '321 Đường Võ Văn Tần',
                'ward' => 'Phường 6',
                'district' => 'Quận 3',
                'city' => 'TP. Hồ Chí Minh',
                'latitude' => 10.7769,
                'longitude' => 106.7009,
                'is_default' => true,
            ],
            [
                'phone_number' => '0741852963',
                'address_line' => '654 Đường Hai Bà Trưng',
                'ward' => 'Phường Đa Kao',
                'district' => 'Quận 1',
                'city' => 'TP. Hồ Chí Minh',
                'latitude' => 10.7769,
                'longitude' => 106.7009,
                'is_default' => false,
            ],
            [
                'phone_number' => '0963852741',
                'address_line' => '987 Đường Đồng Khởi',
                'ward' => 'Phường Bến Nghé',
                'district' => 'Quận 1',
                'city' => 'TP. Hồ Chí Minh',
                'latitude' => 10.7769,
                'longitude' => 106.7009,
                'is_default' => true,
            ],
            [
                'phone_number' => '0147852963',
                'address_line' => '147 Đường Trần Hưng Đạo',
                'ward' => 'Phường Cầu Ông Lãnh',
                'district' => 'Quận 1',
                'city' => 'TP. Hồ Chí Minh',
                'latitude' => 10.7769,
                'longitude' => 106.7009,
                'is_default' => false,
            ],
            [
                'phone_number' => '0852963741',
                'address_line' => '258 Đường Nguyễn Thị Minh Khai',
                'ward' => 'Phường Đa Kao',
                'district' => 'Quận 1',
                'city' => 'TP. Hồ Chí Minh',
                'latitude' => 10.7769,
                'longitude' => 106.7009,
                'is_default' => true,
            ],
            [
                'phone_number' => '0741852963',
                'address_line' => '369 Đường Lý Tự Trọng',
                'ward' => 'Phường Bến Thành',
                'district' => 'Quận 1',
                'city' => 'TP. Hồ Chí Minh',
                'latitude' => 10.7769,
                'longitude' => 106.7009,
                'is_default' => false,
            ],
            [
                'phone_number' => '0963852741',
                'address_line' => '741 Đường Nam Kỳ Khởi Nghĩa',
                'ward' => 'Phường 8',
                'district' => 'Quận 3',
                'city' => 'TP. Hồ Chí Minh',
                'latitude' => 10.7769,
                'longitude' => 106.7009,
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
    }
} 