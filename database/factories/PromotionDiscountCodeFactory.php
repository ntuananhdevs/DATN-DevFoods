<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\PromotionProgram;
use App\Models\DiscountCode;
use App\Models\PromotionDiscountCode;

class PromotionDiscountCodeFactory extends Factory
{
    protected $model = PromotionDiscountCode::class;

    public function definition(): array
    {
        return [
            'promotion_program_id' => PromotionProgram::factory(),
            'discount_code_id' => DiscountCode::factory(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}