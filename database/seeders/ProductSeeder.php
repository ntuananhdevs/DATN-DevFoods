<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
<<<<<<< HEAD
use App\Models\Category;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $categories = Category::all();

        foreach ($categories as $category) {
            Product::factory()->count(20)->create([
                'category_id' => $category->id,
            ]);
        }
    }
}
=======
use App\Models\Admin\Product;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Tạo 20 sản phẩm mẫu
        Product::factory(20)->create();
        
        // Tạo thêm 5 sản phẩm hết hàng
        Product::factory(5)->outOfStock()->create();
    }
}
>>>>>>> 9e08ea1f7e66b8e22f8e56b61cd87255fc7d5a93
