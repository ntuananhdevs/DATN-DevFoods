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
        return [
            'discount_code_id' => DiscountCode::factory(),
            'order_id' => Order::factory(),
            'user_id' => User::factory(),
            'branch_id' => Branch::factory(),
            'guest_phone' => fake()->optional(0.2)->phoneNumber(),
            'original_amount' => fake()->randomFloat(2, 50, 500),
            'discount_amount' => fake()->randomFloat(2, 5, 100),
            'used_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}