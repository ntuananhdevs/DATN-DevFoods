<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\UserRankHistory;
use App\Models\User;
use App\Models\UserRank;

class UserRankHistorySeeder extends Seeder
{
    public function run()
    {
        $users = User::all();
        $ranks = UserRank::all();

        foreach ($users as $user) {
            $count = fake()->numberBetween(1, 3);
            for ($i = 0; $i < $count; $i++) {
                UserRankHistory::factory()->create([
                    'user_id' => $user->id,
                    'old_rank_id' => $i === 0 ? null : $ranks->random()->id,
                    'new_rank_id' => $ranks->random()->id,
                ]);
            }
        }
    }
}