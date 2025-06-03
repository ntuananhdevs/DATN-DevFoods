<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\UserRank;
use App\Models\UserRankHistory;

class UserRankHistoryFactory extends Factory
{
    protected $model = UserRankHistory::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'old_rank_id' => UserRank::factory(),
            'new_rank_id' => UserRank::factory(),
            'total_spending' => fake()->randomFloat(2, 100, 10000),
            'total_orders' => fake()->numberBetween(1, 50),
            'reason' => fake()->sentence(),
            'changed_at' => now(),
        ];
    }
}