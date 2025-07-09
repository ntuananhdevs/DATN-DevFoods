<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Address;
use App\Models\User;

class TestAddressSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get first user
        $user = User::first();
        
        if (!$user) {
            $this->command->error('No users found. Please create a user first.');
            return;
        }

        // Create test addresses
        $addresses = [
            [
                'user_id' => $user->id,
                'address_line' => 'Số 1 Đại Cồ Việt',
                'city' => 'Hà Nội',
                'district' => 'Hai Bà Trưng',
                'ward' => 'Phường Bách Khoa',
                'phone_number' => '0123456789',
                'is_default' => true,
                'latitude' => 21.0285,
                'longitude' => 105.8542,
            ],
            [
                'user_id' => $user->id,
                'address_line' => 'Số 144 Xuân Thủy',
                'city' => 'Hà Nội',
                'district' => 'Cầu Giấy',
                'ward' => 'Phường Dịch Vọng Hậu',
                'phone_number' => '0987654321',
                'is_default' => false,
                'latitude' => 21.0378,
                'longitude' => 105.7826,
            ],
            [
                'user_id' => $user->id,
                'address_line' => 'Số 54 Nguyễn Chí Thanh',
                'city' => 'Hà Nội',
                'district' => 'Đống Đa',
                'ward' => 'Phường Láng Thượng',
                'phone_number' => '0111222333',
                'is_default' => false,
                'latitude' => 21.0227,
                'longitude' => 105.8194,
            ]
        ];

        foreach ($addresses as $address) {
            Address::updateOrCreate(
                [
                    'user_id' => $address['user_id'],
                    'address_line' => $address['address_line']
                ],
                $address
            );
        }

        $this->command->info('Test addresses created successfully for user: ' . $user->email);
    }
} 