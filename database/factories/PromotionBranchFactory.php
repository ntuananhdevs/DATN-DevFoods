<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\PromotionProgram;
use App\Models\Branch;
use App\Models\PromotionBranch;

class PromotionBranchFactory extends Factory
{
    protected $model = PromotionBranch::class;

    public function definition(): array
    {
        return [
            'promotion_program_id' => PromotionProgram::factory(),
            'branch_id' => Branch::factory(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}