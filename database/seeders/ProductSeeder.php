<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
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