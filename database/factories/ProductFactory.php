<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'category_id' => Category::inRandomOrder()->first()?->id ?? Category::factory(),
            'name' => $this->faker->words(2, true),
            'description' => $this->faker->sentence(),
            'base_price' => $this->faker->randomFloat(2, 10, 200),
            'stock' => $this->faker->numberBetween(0, 100),
            'image' => $this->faker->imageUrl(640, 480, 'food', true),
            'preparation_time' => $this->faker->numberBetween(5, 60),
        ];
    }
}
