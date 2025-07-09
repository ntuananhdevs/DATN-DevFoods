<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\UserRank;
use App\Models\Role;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Đảm bảo user_ranks đã được tạo
        if (UserRank::count() === 0) {
            UserRank::factory()->count(5)->create();
        }

        // Lấy ID của rank thấp nhất (Đồng)
        $defaultRankId = UserRank::where('slug', 'bronze')->first()->id;

        // Tạo hoặc cập nhật user admin
        $admin = User::updateOrCreate(
            ['email' => 'admin@devfoods.com'],
            [
                'user_name' => 'admin',
                'full_name' => 'Administrator',
                'email' => 'admin@devfoods.com',
                'phone' => '0123456789',
                'password' => bcrypt('admin'),
                'user_rank_id' => $defaultRankId,
                'total_spending' => 0,
                'total_orders' => 0,
                'rank_updated_at' => now(),
                'balance' => 0,
                'active' => true,
            ]
        );

        // Gán role admin cho user admin
        $adminRole = Role::where('name', 'admin')->first();
        if ($adminRole) {
            $admin->roles()->sync([$adminRole->id]);
        }

        // Tạo hoặc cập nhật 3 người quản lý chi nhánh
        $branchManagers = [
            [
                'user_name' => 'manager1',
                'full_name' => 'Nguyen Van A',
                'email' => 'manager1@devfoods.com',
                'phone' => '0123456781',
            ],
            [
                'user_name' => 'manager2',
                'full_name' => 'Nguyen Van B',
                'email' => 'manager2@devfoods.com',
                'phone' => '0123456782',
            ],
            [
                'user_name' => 'manager3',
                'full_name' => 'Nguyen Van C',
                'email' => 'manager3@devfoods.com',
                'phone' => '0123456783',
            ],
        ];

        $managerRole = Role::where('name', 'manager')->first();
        foreach ($branchManagers as $managerData) {
            $manager = User::updateOrCreate(
                ['email' => $managerData['email']],
                [
                    'user_name' => $managerData['user_name'],
                    'full_name' => $managerData['full_name'],
                    'email' => $managerData['email'],
                    'phone' => $managerData['phone'],
                    'password' => bcrypt('manager'),
                    'user_rank_id' => $defaultRankId,
                    'total_spending' => 0,
                    'total_orders' => 0,
                    'rank_updated_at' => now(),
                    'balance' => 0,
                    'active' => true,
                ]
            );
            
            // Gán role manager cho user manager
            if ($managerRole) {
                $manager->roles()->sync([$managerRole->id]);
            }
        }

        // Tạo hoặc cập nhật user customer chuẩn
        $customer = User::updateOrCreate(
            ['email' => 'customer@devfoods.com'],
            [
                'user_name' => 'customer',
                'full_name' => 'Nguyen Kha Banh',
                'email' => 'customer@devfoods.com',
                'phone' => '0123456789',
                'password' => bcrypt('customer'),
                'user_rank_id' => $defaultRankId,
                'total_spending' => 0,
                'total_orders' => 0,
                'rank_updated_at' => now(),
                'balance' => 0,
                'active' => true,
            ]
        );

        // Gán role customer cho user customer
        $customerRole = Role::where('name', 'customer')->first();
        if ($customerRole) {
            $customer->roles()->sync([$customerRole->id]);
        }
    }
}