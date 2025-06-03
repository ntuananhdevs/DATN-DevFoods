<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\UserDiscountCode;
use App\Models\User;
use App\Models\DiscountCode;

class UserDiscountCodeSeeder extends Seeder
{
    public function run()
    {
        $users = User::all();
        $discountCodes = DiscountCode::all();

        foreach ($users as $user) {
            $randomDiscountCodes = $discountCodes->random(fake()->numberBetween(1, 3));
            foreach ($randomDiscountCodes as $discountCode) {
                UserDiscountCode::factory()->create([
                    'user_id' => $user->id,
                    'discount_code_id' => $discountCode->id,
                ]);
            }
        }
    }
}