<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductTopping;
use App\Models\Topping;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductTopping>
 */
class ProductToppingFactory extends Factory
{
    protected $model = ProductTopping::class;

    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'topping_id' => Topping::factory(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
} 