<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DiscountCodeProduct;
use App\Models\DiscountCode;
use App\Models\Product;
use App\Models\Category;
use App\Models\Combo;

class DiscountCodeProductSeeder extends Seeder
{
    public function run()
    {
        $discountCodes = DiscountCode::whereIn('applicable_items', ['specific_products', 'specific_categories', 'combos_only'])->get();
        
        echo "Found {$discountCodes->count()} discount codes with applicable items\n";

        // Kiểm tra dữ liệu
        if ($discountCodes->isEmpty()) {
            throw new \Exception('Không tìm thấy mã giảm giá với applicable_items phù hợp. Vui lòng chạy DiscountCodeSeeder trước.');
        }

        foreach ($discountCodes as $discountCode) {
            $count = fake()->numberBetween(1, 3);
            $type = fake()->randomElement(['product', 'category', 'combo']);

            for ($i = 0; $i < $count; $i++) {
                $data = [
                    'discount_code_id' => $discountCode->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                if ($type === 'product') {
                    $product = Product::inRandomOrder()->first();
                    if (!$product) {
                        throw new \Exception('Không tìm thấy sản phẩm. Vui lòng chạy FastFoodSeeder trước.');
                    }
                    $data['product_id'] = $product->id;
                } elseif ($type === 'category') {
                    $category = Category::inRandomOrder()->first();
                    if (!$category) {
                        throw new \Exception('Không tìm thấy danh mục. Vui lòng chạy FastFoodSeeder trước.');
                    }
                    $data['category_id'] = $category->id;
                } elseif ($type === 'combo') {
                    $combo = Combo::inRandomOrder()->first();
                    if (!$combo) {
                        throw new \Exception('Không tìm thấy combo. Vui lòng chạy FastFoodSeeder trước.');
                    }
                    $data['combo_id'] = $combo->id;
                }

                DiscountCodeProduct::create($data);
            }
        }
    }
}