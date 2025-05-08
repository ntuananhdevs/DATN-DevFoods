<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Product;
use App\Models\Category;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'category_id' => Category::inRandomOrder()->first()?->id ?? Category::factory(),
            'name' => $this->faker->unique()->words(3, true),
            'description' => $this->faker->paragraph(),
            'base_price' => $this->faker->randomFloat(2, 10000, 200000),
            'stock' => $this->faker->boolean(80),
            'image' => 'products/default.jpg',
            'preparation_time' => $this->faker->numberBetween(5, 30),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    public function outOfStock(): static
    {
        return $this->state(fn(array $attributes) => [
            'stock' => false,
        ]);
    }
}
