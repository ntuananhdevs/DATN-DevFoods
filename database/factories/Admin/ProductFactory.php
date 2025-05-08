<?php

namespace Database\Factories\Admin;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Admin\Product;
use App\Models\Admin\Category;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'category_id' => Category::inRandomOrder()->first()->id,
            'name' => $this->faker->unique()->words(3, true),
            'description' => $this->faker->paragraph,
            'base_price' => $this->faker->randomFloat(2, 10000, 200000), // Changed from 'price' to 'base_price'
            'stock' => $this->faker->boolean(80),
            'image' => 'products/default.jpg',
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}