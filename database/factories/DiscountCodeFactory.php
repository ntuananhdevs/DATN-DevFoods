<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\DiscountCode;

class DiscountCodeFactory extends Factory
{
    protected $model = DiscountCode::class;

    public function definition(): array
    {
        $discountType = fake()->randomElement(['percentage', 'fixed_amount', 'free_shipping']);
        $discountValue = $discountType === 'percentage' ? fake()->numberBetween(5, 50) : ($discountType === 'fixed_amount' ? fake()->randomFloat(2, 10, 100) : 0);

        return [
            'code' => strtoupper(fake()->unique()->lexify('??????')),
            'name' => fake()->words(3, true),
            'description' => fake()->sentence(),
            'image' => fake()->optional()->imageUrl(300, 300),
            'discount_type' => $discountType,
            'discount_value' => $discountValue,
            'min_order_amount' => fake()->randomFloat(2, 0, 100),
            'max_discount_amount' => $discountType === 'percentage' ? fake()->randomFloat(2, 50, 200) : null,
            'applicable_scope' => fake()->randomElement(['all_branches', 'specific_branches']),
            'applicable_items' => fake()->randomElement(['all_items', 'specific_products', 'specific_categories', 'combos_only']),
            'applicable_ranks' => json_encode(fake()->randomElements([1, 2, 3, 4, 5], fake()->numberBetween(1, 3))),
            'rank_exclusive' => fake()->boolean(20),
            'valid_days_of_week' => json_encode(fake()->randomElements([0, 1, 2, 3, 4, 5, 6], fake()->numberBetween(1, 7))),
            'valid_from_time' => fake()->time('H:i:s'),
            'valid_to_time' => fake()->time('H:i:s'),
            'usage_type' => fake()->randomElement(['public', 'personal']),
            'max_total_usage' => fake()->optional(0.8, null)->numberBetween(100, 1000),
            'max_usage_per_user' => fake()->numberBetween(1, 5),
            'current_usage_count' => fake()->numberBetween(0, 50),
            'start_date' => now(),
            'end_date' => now()->addDays(30),
            'is_active' => true,
            'is_featured' => fake()->boolean(30),
            'display_order' => fake()->numberBetween(0, 10),
            'created_by' => User::factory(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}