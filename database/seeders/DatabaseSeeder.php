<?php

namespace Database\Seeders;

use App\Models\Admin\Category;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
<<<<<<< HEAD
        // User::factory(10)->create();

        $this->call([
            CategorySeeder::class,
            ProductSeeder::class,
        ]);
=======
        // Gọi RoleSeeder trước để tạo roles
        $this->call(RoleSeeder::class);
        
        // Gọi UserSeeder để tạo users
        $this->call(UserSeeder::class);
        
        // Tạo categories và products
        Category::factory(10)->create();
        $this->call(ProductSeeder::class);
>>>>>>> 9e08ea1f7e66b8e22f8e56b61cd87255fc7d5a93
    }
}

