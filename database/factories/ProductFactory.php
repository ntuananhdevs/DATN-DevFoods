<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition()
    {
        return [
            'category_id' => Category::factory(),
            'name' => $this->faker->words(3, true),
            'description' => $this->faker->paragraph(),
            'base_price' => $this->faker->randomFloat(2, 10000, 200000),
            'stock' => $this->faker->boolean(80),
            'image' => 'products/' . $this->faker->image('public/storage/products', 640, 480, null, false),
            'preparation_time' => $this->faker->numberBetween(5, 30),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
