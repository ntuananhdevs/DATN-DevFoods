<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use Carbon\Carbon;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                'category_id' => 1,
                'name' => 'Burger Gà',
                'sku' => 'BRG001',
                'base_price' => 45000,
                'available' => true,
                'preparation_time' => 10,
                'ingredients' => [
                    'Bánh mì burger',
                    'Xà lách',
                    'Cà chua',
                    'Dưa chuột',
                    'Hành tây',
                    'Sốt mayonnaise',
                    'Sốt cà chua',
                    'Thịt gà phi lê',
                    'Ức gà'
                ],
                'short_description' => 'Burger gà với sốt đặc biệt',
                'status' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'category_id' => 1,
                'name' => 'Burger Bò',
                'sku' => 'BRG002',
                'base_price' => 55000,
                'available' => true,
                'preparation_time' => 12,
                'ingredients' => [
                    'Bánh mì burger',
                    'Xà lách',
                    'Cà chua',
                    'Dưa chuột',
                    'Hành tây',
                    'Sốt mayonnaise',
                    'Sốt BBQ',
                    'Thịt bò xay',
                    'Phô mai'
                ],
                'short_description' => 'Burger bò với sốt BBQ đặc biệt',
                'status' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
