<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\DiscountCode;
use App\Models\Order;
use App\Models\User;
use App\Models\Branch;
use App\Models\DiscountUsageHistory;

class DiscountUsageHistoryFactory extends Factory
{
    protected $model = DiscountUsageHistory::class;

    public function definition(): array
    {
        // Fetch a random existing order and branch from the database
        $order = Order::inRandomOrder()->first();
        $branch = Branch::inRandomOrder()->first();

        // If no order or branch exists, return an empty array to skip record creation
        if (!$order || !$branch) {
            return [];
        }

        return [
            'discount_code_id' => DiscountCode::factory(),
            'order_id' => $order->id,
            'user_id' => User::factory(),
            'branch_id' => $branch->id,
            'guest_phone' => fake()->optional(0.2)->phoneNumber(),
            'original_amount' => fake()->randomFloat(2, 50, 500),
            'discount_amount' => fake()->randomFloat(2, 5, 100),
            'used_at' => now(),
        ];
    }
}