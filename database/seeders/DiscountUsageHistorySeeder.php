<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DiscountUsageHistory;
use App\Models\DiscountCode;
use App\Models\Order;
use App\Models\User;
use App\Models\Branch;

class DiscountUsageHistorySeeder extends Seeder
{
    public function run()
    {
        $discountCodes = DiscountCode::all();
        $orders = Order::all();
        $users = User::all();
        $branches = Branch::all();

        foreach ($discountCodes as $discountCode) {
            $count = fake()->numberBetween(1, 5);
            for ($i = 0; $i < $count; $i++) {
                DiscountUsageHistory::factory()->create([
                    'discount_code_id' => $discountCode->id,
                    'order_id' => $orders->random()->id,
                    'user_id' => fake()->boolean(80) ? $users->random()->id : null,
                    'branch_id' => $branches->random()->id,
                ]);
            }
        }
    }
}