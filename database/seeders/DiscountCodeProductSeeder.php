<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DiscountCodeProduct;
use App\Models\DiscountCode;
use App\Models\Product;
use App\Models\Category;
use App\Models\Combo;

class DiscountCodeProductSeeder extends Seeder
{
    public function run()
    {
        $discountCodes = DiscountCode::whereIn('applicable_items', ['specific_products', 'specific_categories', 'combos_only'])->get();
        
        foreach ($discountCodes as $discountCode) {
            $count = fake()->numberBetween(1, 3);
            for ($i = 0; $i < $count; $i++) {
                DiscountCodeProduct::factory()->create([
                    'discount_code_id' => $discountCode->id,
                ]);
            }
        }
    }
}