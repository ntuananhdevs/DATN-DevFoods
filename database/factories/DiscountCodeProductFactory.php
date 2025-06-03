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

        // Lấy hoặc tạo product nếu type là product
        $product = null;
        if ($type === 'product') {
            $product = Product::inRandomOrder()->first();
            if (!$product) {
                $category = Category::inRandomOrder()->first() ?? Category::create([
                    'name' => 'Danh mục Tạm thời',
                    'description' => 'Danh mục tạm thời cho sản phẩm',
                    'image' => 'categories/temp.jpg',
                    'status' => true,
                ]);

                $product = Product::create([
                    'category_id' => $category->id,
                    'name' => 'Sản phẩm Tạm thời',
                    'sku' => 'PRD-TEMP-' . rand(1000, 9999),
                    'description' => 'Sản phẩm tạm thời cho mã giảm giá',
                    'short_description' => 'Sản phẩm tạm thời',
                    'base_price' => rand(30000, 200000),
                    'preparation_time' => rand(10, 30),
                    'ingredients' => json_encode([['name' => 'Nguyên liệu tạm thời', 'quantity' => '100g']]),
                    'status' => 'selling',
                    'is_featured' => false,
                ]);
            }
        }

        // Lấy hoặc tạo category nếu type là category
        $category = null;
        if ($type === 'category') {
            $category = Category::inRandomOrder()->first() ?? Category::create([
                'name' => 'Danh mục Tạm thời',
                'description' => 'Danh mục tạm thời cho mã giảm giá',
                'image' => 'categories/temp.jpg',
                'status' => true,
            ]);
        }

        // Lấy hoặc tạo combo nếu type là combo
        $combo = null;
        if ($type === 'combo') {
            $combo = Combo::inRandomOrder()->first() ?? Combo::create([
                'name' => 'Combo Tạm thời',
                'description' => 'Combo tạm thời cho mã giảm giá',
                'price' => rand(50000, 300000),
                'active' => true,
            ]);
        }

        return [
            'discount_code_id' => DiscountCode::factory(),
            'product_id' => $type === 'product' ? $product->id : null,
            'category_id' => $type === 'category' ? $category->id : null,
            'combo_id' => $type === 'combo' ? $combo->id : null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}