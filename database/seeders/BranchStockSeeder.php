<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BranchStock;
use App\Models\Branch;
use App\Models\ProductVariant;

class BranchStockSeeder extends Seeder
{
    public function run()
    {
        $branches = Branch::all();
        $variants = ProductVariant::all();

        foreach ($branches as $branch) {
            foreach ($variants as $variant) {
                BranchStock::create([
                    'branch_id' => $branch->id,
                    'product_variant_id' => $variant->id,
                    'stock_quantity' => rand(0, 100),
                ]);
            }
        }
    }
} 