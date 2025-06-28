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

        // Tạo user admin
        User::factory()->create([
            'user_name' => 'admin',
            'full_name' => 'Administrator',
            'email' => 'admin@devfoods.com',
            'password' => bcrypt('admin'),
            'user_rank_id' => $defaultRankId,
            'total_spending' => 0,
            'total_orders' => 0,
            'rank_updated_at' => now(),
        ]);

        // Tạo 3 người quản lý chi nhánh
        $branchManagers = [
            [
                'user_name' => 'manager1',
                'full_name' => 'Nguyen Van A',
                'email' => 'manager1@devfoods.com',
                'password' => bcrypt('manager'),
                'user_rank_id' => $defaultRankId,
                'total_spending' => 0,
                'total_orders' => 0,
                'rank_updated_at' => now(),
            ],
            [
                'user_name' => 'manager2',
                'full_name' => 'Nguyen Van B',
                'email' => 'manager2@devfoods.com',
                'password' => bcrypt('manager'),
                'user_rank_id' => $defaultRankId,
                'total_spending' => 0,
                'total_orders' => 0,
                'rank_updated_at' => now(),
            ],
            [
                'user_name' => 'manager3',
                'full_name' => 'Nguyen Van C',
                'email' => 'manager3@devfoods.com',
                'password' => bcrypt('manager'),
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