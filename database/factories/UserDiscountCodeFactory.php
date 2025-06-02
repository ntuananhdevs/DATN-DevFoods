<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\DiscountCode;
use App\Models\UserDiscountCode;

class UserDiscountCodeFactory extends Factory
{
    protected $model = UserDiscountCode::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'discount_code_id' => DiscountCode::factory(),
            'usage_count' => fake()->numberBetween(0, 3),
            'status' => fake()->randomElement(['available', 'used_up', 'expired']),
            'assigned_at' => now(),
            'first_used_at' => fake()->optional(0.5)->dateTimeThisYear(),
            'last_used_at' => fake()->optional(0.5)->dateTimeThisYear(),
        ];
    }
}