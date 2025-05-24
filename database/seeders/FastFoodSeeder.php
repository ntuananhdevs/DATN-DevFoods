<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Category;
use App\Models\Combo;
use App\Models\ComboItem;
use App\Models\Product;
use App\Models\ProductTopping;
use App\Models\ProductVariant;
use App\Models\ProductVariantDetail;
use App\Models\Topping;
use App\Models\VariantAttribute;
use App\Models\VariantValue;
use Illuminate\Database\Seeder;

class FastFoodSeeder extends Seeder
{
    public function run(): void
    {
        // Tạo categories
        $categories = [
            ['name' => 'Burger', 'description' => 'Các loại burger thơm ngon'],
            ['name' => 'Pizza', 'description' => 'Pizza đa dạng hương vị'],
            ['name' => 'Gà Rán', 'description' => 'Gà rán và các món từ gà'],
            ['name' => 'Cơm', 'description' => 'Các món cơm đặc sắc'],
            ['name' => 'Mì', 'description' => 'Các loại mì ngon'],
            ['name' => 'Đồ Uống', 'description' => 'Đồ uống giải khát'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }

        // Tạo variants
        $variants = [
            'Size',
            'Topping',
            'Spice Level',
            'Temperature',
            'Sugar Level'
        ];

        foreach ($variants as $variant) {
            VariantAttribute::create([
                'name' => $variant
            ]);
        }

        // Tạo variant values
        $variantValues = [
            'Size' => ['Small', 'Medium', 'Large', 'Extra Large'],
            'Topping' => ['Extra Cheese', 'Extra Meat', 'Extra Vegetables', 'No Topping'],
            'Spice Level' => ['Mild', 'Medium', 'Hot', 'Extra Hot'],
            'Temperature' => ['Hot', 'Cold', 'Warm'],
            'Sugar Level' => ['No Sugar', 'Less Sugar', 'Normal', 'Extra Sugar']
        ];

        foreach ($variants as $variant) {
            foreach ($variantValues[$variant] as $value) {
                VariantValue::create([
                    'variant_attribute_id' => VariantAttribute::where('name', $variant)->first()->id,
                    'value' => $value,
                    'price_adjustment' => rand(-10000, 20000)
                ]);
            }
        }

        // Tạo products
        $products = Product::factory(30)->create();

        // Tạo variants cho mỗi product
        foreach ($products as $product) {
            $variants = ProductVariant::factory(3)->create([
                'product_id' => $product->id
            ]);

            // Tạo variant details cho mỗi variant
            foreach ($variants as $variant) {
                $variantValues = VariantValue::inRandomOrder()->take(2)->get();
                foreach ($variantValues as $value) {
                    ProductVariantDetail::create([
                        'product_variant_id' => $variant->id,
                        'variant_value_id' => $value->id
                    ]);
                }
            }
        }

        // Tạo combos
        $combos = Combo::factory(10)->create();

        // Tạo combo items
        foreach ($combos as $combo) {
            $products = Product::inRandomOrder()->take(rand(2, 4))->get();
            foreach ($products as $product) {
                ComboItem::create([
                    'combo_id' => $combo->id,
                    'product_id' => $product->id,
                    'quantity' => rand(1, 3)
                ]);
            }
        }

        // Tạo toppings
        $toppings = Topping::factory(20)->create();

        // Tạo product toppings
        foreach ($products as $product) {
            $productToppings = $toppings->random(rand(2, 5));
            foreach ($productToppings as $topping) {
                ProductTopping::create([
                    'product_id' => $product->id,
                    'topping_id' => $topping->id
                ]);
            }
        }

        // Tạo branches
        $branches = Branch::factory(5)->create();

        // Tạo branch stocks
        foreach ($branches as $branch) {
            $productVariants = ProductVariant::inRandomOrder()->take(20)->get();
            foreach ($productVariants as $variant) {
                $branch->stocks()->create([
                    'product_variant_id' => $variant->id,
                    'stock_quantity' => rand(10, 100)
                ]);
            }
        }
    }
} 