<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin\Product;
use App\Models\Admin\Category;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // Lấy tất cả category
        $categories = Category::all();

        foreach ($categories as $category) {
            // Tạo 20 sản phẩm cho từng category
            Product::factory()->count(20)->create([
                'category_id' => $category->id,
            ]);
        }
    }
}
