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

        // Kiểm tra xem có chi nhánh và mã giảm giá không
        if ($branches->isEmpty()) {
            throw new \Exception('Không tìm thấy chi nhánh. Vui lòng chạy BranchSeeder trước.');
        }

        if ($discountCodes->isEmpty()) {
            throw new \Exception('Không tìm thấy mã giảm giá với applicable_scope "specific_branches". Vui lòng chạy DiscountCodeSeeder trước.');
        }

        foreach ($discountCodes as $discountCode) {
            $randomBranches = $branches->random(fake()->numberBetween(1, min(3, $branches->count())));
            foreach ($randomBranches as $branch) {
                DiscountCodeBranch::create([
                    'discount_code_id' => $discountCode->id,
                    'branch_id' => $branch->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}