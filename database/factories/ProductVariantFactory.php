<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductVariantFactory extends Factory
{
    protected $model = ProductVariant::class;

    public function definition()
    {
        return [
            'product_id' => Product::factory(),
            'price' => function (array $attributes) {
                $product = Product::find($attributes['product_id']);
                return $product ? $product->base_price + $this->faker->randomFloat(2, 0, 50000) : $this->faker->randomFloat(2, 10000, 200000);
            },
            'image' => 'variants/' . $this->faker->image('public/storage/variants', 640, 480, null, false),
            'stock_quantity' => $this->faker->numberBetween(0, 100),
            'active' => $this->faker->boolean(90),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}