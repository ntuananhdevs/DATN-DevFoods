<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Category;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'category_id' => Category::inRandomOrder()->first()->id ?? 1,
            'name' => $this->faker->unique()->words(3, true),
            'description' => $this->faker->paragraph(),
            'base_price' => $this->faker->randomFloat(2, 10000, 200000),
            'stock' => $this->faker->boolean(80), // 80% chance of being true
            'image' => 'products/default.jpg',
            'preparation_time' => $this->faker->numberBetween(5, 30),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * Indicate that the product is out of stock.
     */
    public function outOfStock(): static
    {
        return $this->state(fn(array $attributes) => [
            'stock' => false,
        ]);
    }
}
