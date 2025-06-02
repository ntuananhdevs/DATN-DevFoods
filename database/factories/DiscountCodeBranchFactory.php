<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\DiscountCode;
use App\Models\Branch;
use App\Models\DiscountCodeBranch;

class DiscountCodeBranchFactory extends Factory
{
    protected $model = DiscountCodeBranch::class;

    public function definition(): array
    {
        return [
            'discount_code_id' => DiscountCode::factory(),
            'branch_id' => Branch::factory(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}