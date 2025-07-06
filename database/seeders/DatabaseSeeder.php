<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            UserRankSeeder::class,
            UserSeeder::class,
            BranchSeeder::class,
            DriverApplicationSeeder::class,
            DriverSeeder::class,
            UserRoleSeeder::class,
            FastFoodSeeder::class,
            BannerSeeder::class,
            PromotionProgramSeeder::class,
            DiscountCodeSeeder::class,
            PromotionDiscountCodeSeeder::class,
            UserDiscountCodeSeeder::class,
            DiscountCodeBranchSeeder::class,
            PromotionBranchSeeder::class,
            DiscountCodeProductSeeder::class,
            // DriverOrderSeeder::class, // Chuyển lên trước DiscountUsageHistorySeeder
            // DiscountUsageHistorySeeder::class,
            UserRankHistorySeeder::class,
            GeneralSettingSeeder::class,
            AddressSeeder::class, // Đảm bảo AddressSeeder chạy trước
            CartSeeder::class, // CartSeeder trước OrderSeeder
            OrderSeeder::class, // OrderSeeder sau khi đã có address
            ProductReviewSeeder::class,
        ]);
    }
}