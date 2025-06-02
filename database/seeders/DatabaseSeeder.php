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
        $this->call([
            RoleSeeder::class,
            UserRankSeeder::class,
            UserSeeder::class,
            // CategorySeeder::class,
            BranchSeeder::class,
            DriverApplicationSeeder::class,
            DriverSeeder::class,
            UserRoleSeeder::class,
            FastFoodSeeder::class,
            BannerSeeder::class,
            PromotionProgramSeeder::class,
            PromotionDiscountCodeSeeder::class,
            UserDiscountCodeSeeder::class,
            DiscountCodeBranchSeeder::class,
            PromotionBranchSeeder::class,
            DiscountCodeProductSeeder::class,
            DiscountUsageHistorySeeder::class,
            UserRankHistorySeeder::class,
        ]);
    }
}
