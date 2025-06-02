<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\UserRank;

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
        $silverRankId = UserRank::where('slug', 'silver')->first()->id;

        // Tạo 10 user mẫu
        User::factory()->count(10)->create([
            'user_rank_id' => $defaultRankId,
            'total_spending' => fake()->randomFloat(2, 0, 1000),
            'total_orders' => fake()->numberBetween(0, 10),
            'rank_updated_at' => now(),
        ]);

        // Tạo user admin mặc định
        User::factory()->create([
            'user_name' => 'spadmin',
            'full_name' => 'Administrator',
            'email' => 'admin@devfoods.com',
            'password' => bcrypt('admin'),
            'user_rank_id' => $defaultRankId,
            'total_spending' => 0,
            'total_orders' => 0,
            'rank_updated_at' => now(),
        ]);

        // Tạo user khách hàng mặc định 
        User::factory()->create([
            'user_name' => 'customer',
            'full_name' => 'Test Customer',
            'email' => 'customer@example.com',
            'user_rank_id' => $silverRankId,
            'total_spending' => fake()->randomFloat(2, 1000, 5000),
            'total_orders' => fake()->numberBetween(5, 20),
            'rank_updated_at' => now(),
        ]);

        // Tạo 3 người quản lý chi nhánh
        $branchManagers = [
            [
                'user_name' => 'manager1',
                'full_name' => 'Nguyễn Văn Quản Lý',
                'email' => 'manager1@devfoods.com',
                'password' => bcrypt('manager123'),
                'user_rank_id' => $defaultRankId,
                'total_spending' => 0,
                'total_orders' => 0,
                'rank_updated_at' => now(),
            ],
            [
                'user_name' => 'manager2',
                'full_name' => 'Trần Thị Quản Lý',
                'email' => 'manager2@devfoods.com',
                'password' => bcrypt('manager123'),
                'user_rank_id' => $defaultRankId,
                'total_spending' => 0,
                'total_orders' => 0,
                'rank_updated_at' => now(),
            ],
            [
                'user_name' => 'manager3',
                'full_name' => 'Lê Minh Quản Lý',
                'email' => 'manager3@devfoods.com',
                'password' => bcrypt('manager123'),
                'user_rank_id' => $defaultRankId,
                'total_spending' => 0,
                'total_orders' => 0,
                'rank_updated_at' => now(),
            ],
        ];
        foreach ($branchManagers as $manager) {
            User::factory()->create($manager);
        }
    }
}