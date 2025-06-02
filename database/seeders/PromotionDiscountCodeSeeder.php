<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PromotionDiscountCode;
use App\Models\PromotionProgram;
use App\Models\DiscountCode;

class PromotionDiscountCodeSeeder extends Seeder
{
    public function run()
    {
        $promotionPrograms = PromotionProgram::all();
        $discountCodes = DiscountCode::all();

        foreach ($promotionPrograms as $program) {
            $randomDiscountCodes = $discountCodes->random(fake()->numberBetween(1, 5));
            foreach ($randomDiscountCodes as $discountCode) {
                PromotionDiscountCode::factory()->create([
                    'promotion_program_id' => $program->id,
                    'discount_code_id' => $discountCode->id,
                ]);
            }
        }
    }
}