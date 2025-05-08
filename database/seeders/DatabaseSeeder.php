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
        
        // Tạo categories và products
        Category::factory(10)->create();
        $this->call(ProductSeeder::class);
    }
}

