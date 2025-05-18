<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Attribute;
use App\Models\VariantAttribute;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
            CategorySeeder::class,
            AttributeSeeder::class,
            ProductSeeder::class,
            BranchSeeder::class,
            DriverApplicationSeeder::class,
            DriverSeeder::class,
            UserRoleSeeder::class,
            BranchStockSeeder::class,
        ]);
    }
}
