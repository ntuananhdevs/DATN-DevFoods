<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PromotionBranch;
use App\Models\PromotionProgram;
use App\Models\Branch;

class PromotionBranchSeeder extends Seeder
{
    public function run()
    {
        $promotionPrograms = PromotionProgram::where('applicable_scope', 'specific_branches')->get();
        $branches = Branch::all();

        foreach ($promotionPrograms as $program) {
            $randomBranches = $branches->random(fake()->numberBetween(1, 3));
            foreach ($randomBranches as $branch) {
                PromotionBranch::factory()->create([
                    'promotion_program_id' => $program->id,
                    'branch_id' => $branch->id,
                ]);
            }
        }
    }
}