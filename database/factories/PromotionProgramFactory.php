<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\PromotionProgram;

class PromotionProgramFactory extends Factory
{
    protected $model = PromotionProgram::class;

    public function definition(): array
    {
        return [
            'name' => fake()->words(3, true),
            'description' => fake()->paragraph(),
            'banner_image' => fake()->optional()->imageUrl(800, 400),
            'thumbnail_image' => fake()->optional()->imageUrl(200, 200),
            'applicable_scope' => fake()->randomElement(['all_branches', 'specific_branches']),
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