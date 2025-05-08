<?php

namespace Database\Seeders;

use App\Models\Admin\Category;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Gọi RoleSeeder trước để tạo roles
        $this->call(RoleSeeder::class);

        // Gọi UserSeeder để tạo users
        $this->call(UserSeeder::class);

        // Gọi CategorySeeder nếu có file riêng, hoặc dùng factory
        $this->call(CategorySeeder::class); // hoặc dùng dòng dưới nếu không có seeder
        // Category::factory(10)->create();

        // Gọi ProductSeeder để tạo sản phẩm
        $this->call(ProductSeeder::class);
    }
}
