<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductVariantFactory extends Factory
{
    protected $model = ProductVariant::class;

    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'image' => $this->faker->imageUrl(640, 480, 'food'),
            'active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}