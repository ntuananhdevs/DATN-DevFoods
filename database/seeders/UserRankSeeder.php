<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\UserRank;

class UserRankSeeder extends Seeder
{
    public function run()
    {
        // Chỉ tạo nếu bảng user_ranks trống
        if (UserRank::count() === 0) {
            UserRank::factory()->count(5)->create();
        }
    }
}