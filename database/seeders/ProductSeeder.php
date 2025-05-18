<?php

namespace Database\Seeders;

use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ProductVariantValue;
use App\Models\VariantAttribute;
use App\Models\VariantValue;
use App\Models\ProductVariantDetail;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run()
    {
        // Lấy danh sách thuộc tính và giá trị
        $sizeAttribute = VariantAttribute::where('name', 'Kích thước')->first();
        $sizeValues = VariantValue::where('variant_attribute_id', $sizeAttribute->id)->get();
        
        $sugarAttribute = VariantAttribute::where('name', 'Đường')->first();
        $sugarValues = VariantValue::where('variant_attribute_id', $sugarAttribute->id)->get();
        
        $iceAttribute = VariantAttribute::where('name', 'Đá')->first();
        $iceValues = VariantValue::where('variant_attribute_id', $iceAttribute->id)->get();
        
        // Danh sách sản phẩm mẫu
        $products = [
            [
                'category_id' => 1, // Trà sữa
                'name' => 'Trà sữa trân châu đường đen',
                'description' => 'Trà sữa trân châu đường đen thơm ngon, béo ngậy',
                'base_price' => 35000,
                'available' => true,
                'preparation_time' => 5,
                'status' => true,
                'is_featured' => false,
                'created_by' => 1,
                'updated_by' => 1,
            ],
            [
                'category_id' => 1, // Trà sữa
                'name' => 'Trà sữa matcha',
                'description' => 'Trà sữa matcha đậm đà hương vị Nhật Bản',
                'base_price' => 40000,
                'available' => true,
                'preparation_time' => 5,
                'status' => true,
                'is_featured' => false,
                'created_by' => 1,
                'updated_by' => 1,
            ],
            [
                'category_id' => 2, // Cà phê
                'name' => 'Cà phê đen đá',
                'description' => 'Cà phê đen đá đậm đà hương vị Việt Nam',
                'base_price' => 25000,
                'available' => true,
                'preparation_time' => 3,
                'status' => true,
                'is_featured' => false,
                'created_by' => 1,
                'updated_by' => 1,
            ],
            [
                'category_id' => 2, // Cà phê
                'name' => 'Cà phê sữa đá',
                'description' => 'Cà phê sữa đá thơm ngon, béo ngậy',
                'base_price' => 30000,
                'available' => true,
                'preparation_time' => 3,
                'status' => true,
                'is_featured' => false,
                'created_by' => 1,
                'updated_by' => 1,
            ],
            [
                'category_id' => 3, // Trà trái cây
                'name' => 'Trà đào cam sả',
                'description' => 'Trà đào cam sả thơm ngon, thanh mát',
                'base_price' => 45000,
                'available' => true,
                'preparation_time' => 5,
                'status' => true,
                'is_featured' => false,
                'created_by' => 1,
                'updated_by' => 1,
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
                    'image' => $product->image,
                    'active' => true
                ]);
                
                // Thêm giá trị kích thước cho biến thể
                ProductVariantDetail::create([
                    'product_variant_id' => $variant->id,
                    'variant_value_id' => $sizeValue->id
                ]);
                
                // Thêm giá trị đường mặc định
                ProductVariantDetail::create([
                    'product_variant_id' => $variant->id,
                    'variant_value_id' => $sugarValues->where('value', 'Vừa đường')->first()->id
                ]);
                
                // Thêm giá trị đá mặc định
                ProductVariantDetail::create([
                    'product_variant_id' => $variant->id,
                    'variant_value_id' => $iceValues->where('value', 'Vừa đá')->first()->id
                ]);
            }
        }
    }
}
