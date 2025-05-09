<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Gọi RoleSeeder trước để tạo roles
        $this->call(RoleSeeder::class);

        // Gọi UserSeeder để tạo users
        $this->call(UserSeeder::class);

        $this->call(CategorySeeder::class);
        $this->call(ProductSeeder::class);
    }
}
