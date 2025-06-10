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

        // Check if discount codes exist
        if ($discountCodes->isEmpty()) {
            echo "No discount codes available. Skipping PromotionDiscountCode seeding.\n";
            return;
        }

        foreach ($promotionPrograms as $program) {
            $randomCount = fake()->numberBetween(1, min(5, $discountCodes->count()));
            $randomDiscountCodes = $discountCodes->random($randomCount);
            foreach ($randomDiscountCodes as $discountCode) {
                PromotionDiscountCode::factory()->create([
                    'promotion_program_id' => $program->id,
                    'discount_code_id' => $discountCode->id,
                ]);
            }
        }
    }
}