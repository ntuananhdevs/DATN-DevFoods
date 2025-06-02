<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\DiscountCode;
use App\Models\Product;
use App\Models\Category;
use App\Models\Combo;
use App\Models\DiscountCodeProduct;

class DiscountCodeProductFactory extends Factory
{
    protected $model = DiscountCodeProduct::class;

    public function definition(): array
    {
        $type = fake()->randomElement(['product', 'category', 'combo']);
        
        return [
            'discount_code_id' => DiscountCode::factory(),
            'product_id' => $type === 'product' ? Product::factory() : null,
            'category_id' => $type === 'category' ? Category::factory() : null,
            'combo_id' => $type === 'combo' ? Combo::factory() : null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}