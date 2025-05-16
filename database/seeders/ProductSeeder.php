<?php

namespace Database\Seeders;

use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ProductVariantValue;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run()
    {
        // Lấy danh sách thuộc tính và giá trị
        $sizeAttribute = Attribute::where('name', 'Kích thước')->first();
        $sizeValues = AttributeValue::where('attribute_id', $sizeAttribute->id)->get();
        
        $sugarAttribute = Attribute::where('name', 'Đường')->first();
        $sugarValues = AttributeValue::where('attribute_id', $sugarAttribute->id)->get();
        
        $iceAttribute = Attribute::where('name', 'Đá')->first();
        $iceValues = AttributeValue::where('attribute_id', $iceAttribute->id)->get();
        
        // Danh sách sản phẩm mẫu
        $products = [
            [
                'category_id' => 1, // Trà sữa
                'name' => 'Trà sữa trân châu đường đen',
                'description' => 'Trà sữa trân châu đường đen thơm ngon, béo ngậy',
                'base_price' => 35000,
                'stock' => true,
                'image' => 'products/tra-sua-tran-chau.jpg',
                'preparation_time' => 5
            ],
            [
                'category_id' => 1, // Trà sữa
                'name' => 'Trà sữa matcha',
                'description' => 'Trà sữa matcha đậm đà hương vị Nhật Bản',
                'base_price' => 40000,
                'stock' => true,
                'image' => 'products/tra-sua-matcha.jpg',
                'preparation_time' => 5
            ],
            [
                'category_id' => 2, // Cà phê
                'name' => 'Cà phê đen đá',
                'description' => 'Cà phê đen đá đậm đà hương vị Việt Nam',
                'base_price' => 25000,
                'stock' => true,
                'image' => 'products/ca-phe-den-da.jpg',
                'preparation_time' => 3
            ],
            [
                'category_id' => 2, // Cà phê
                'name' => 'Cà phê sữa đá',
                'description' => 'Cà phê sữa đá thơm ngon, béo ngậy',
                'base_price' => 30000,
                'stock' => true,
                'image' => 'products/ca-phe-sua-da.jpg',
                'preparation_time' => 3
            ],
            [
                'category_id' => 3, // Trà trái cây
                'name' => 'Trà đào cam sả',
                'description' => 'Trà đào cam sả thơm ngon, thanh mát',
                'base_price' => 45000,
                'stock' => true,
                'image' => 'products/tra-dao-cam-sa.jpg',
                'preparation_time' => 5
            ]
        ];
        
        foreach ($products as $productData) {
            // Tạo sản phẩm
            $product = Product::create($productData);
            
            // Tạo các biến thể cho sản phẩm
            foreach ($sizeValues as $sizeValue) {
                // Điều chỉnh giá theo kích thước
                $priceAdjustment = 0;
                if ($sizeValue->value === 'Vừa') {
                    $priceAdjustment = 5000;
                } elseif ($sizeValue->value === 'Lớn') {
                    $priceAdjustment = 10000;
                } elseif ($sizeValue->value === 'Siêu lớn') {
                    $priceAdjustment = 15000;
                }
                
                // Tạo biến thể sản phẩm
                $variant = ProductVariant::create([
                    'product_id' => $product->id,
                    'price' => $product->base_price + $priceAdjustment,
                    'image' => $product->image,
                    'stock_quantity' => rand(10, 100),
                    'active' => true
                ]);
                
                // Thêm giá trị kích thước cho biến thể
                ProductVariantValue::create([
                    'product_variant_id' => $variant->id,
                    'attribute_value_id' => $sizeValue->id
                ]);
                
                // Thêm giá trị đường mặc định
                ProductVariantValue::create([
                    'product_variant_id' => $variant->id,
                    'attribute_value_id' => $sugarValues->where('value', 'Vừa đường')->first()->id
                ]);
                
                // Thêm giá trị đá mặc định
                ProductVariantValue::create([
                    'product_variant_id' => $variant->id,
                    'attribute_value_id' => $iceValues->where('value', 'Vừa đá')->first()->id
                ]);
            }
        }
    }
}
