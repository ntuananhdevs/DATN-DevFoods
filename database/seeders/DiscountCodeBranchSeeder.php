<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DiscountCodeBranch;
use App\Models\DiscountCode;
use App\Models\Branch;

class DiscountCodeBranchSeeder extends Seeder
{
    public function run()
    {
        $discountCodes = DiscountCode::where('applicable_scope', 'specific_branches')->get();
        $branches = Branch::all();

        foreach ($discountCodes as $discountCode) {
            $randomBranches = $branches->random(fake()->numberBetween(1, 3));
            foreach ($randomBranches as $branch) {
                DiscountCodeBranch::factory()->create([
                    'discount_code_id' => $discountCode->id,
                    'branch_id' => $branch->id,
                ]);
            }
        }
    }
}