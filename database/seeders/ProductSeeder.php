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
                'category_id' => 1,
                'sku' => 'TST001',
                'name' => 'Trà sữa trân châu đường đen',
                'description' => 'Trà sữa trân châu đường đen thơm ngon, béo ngậy',
                'short_description' => 'Trà sữa trân châu đường đen thơm ngon',
                'base_price' => 35000,
                'available' => true,
                'preparation_time' => 5,
                'status' => 'selling',
                'is_featured' => false,
                'created_by' => 1,
                'updated_by' => 1,
            ],
            [
                'category_id' => 1,
                'sku' => 'TSM002',
                'name' => 'Trà sữa matcha',
                'description' => 'Trà sữa matcha đậm đà hương vị Nhật Bản',
                'short_description' => 'Trà sữa matcha đậm đà',
                'base_price' => 40000,
                'available' => true,
                'preparation_time' => 5,
                'status' => 'selling',
                'is_featured' => true,
                'created_by' => 1,
                'updated_by' => 1,
            ],
            [
                'category_id' => 2,
                'sku' => 'CFD003',
                'name' => 'Cà phê đen đá',
                'description' => 'Cà phê đen đá đậm đà hương vị Việt Nam',
                'short_description' => 'Cà phê đen đá đậm đà',
                'base_price' => 25000,
                'available' => true,
                'preparation_time' => 3,
                'status' => 'selling',
                'is_featured' => false,
                'created_by' => 1,
                'updated_by' => 1,
            ],
            [
                'category_id' => 2,
                'sku' => 'CFS004',
                'name' => 'Cà phê sữa đá',
                'description' => 'Cà phê sữa đá thơm ngon, béo ngậy',
                'short_description' => 'Cà phê sữa đá thơm ngon',
                'base_price' => 30000,
                'available' => true,
                'preparation_time' => 3,
                'status' => 'selling',
                'is_featured' => false,
                'created_by' => 1,
                'updated_by' => 1,
            ],
            [
                'category_id' => 3,
                'sku' => 'TTC005',
                'name' => 'Trà đào cam sả',
                'description' => 'Trà đào cam sả thơm ngon, thanh mát',
                'short_description' => 'Trà đào cam sả thanh mát',
                'base_price' => 45000,
                'available' => true,
                'preparation_time' => 5,
                'status' => 'selling',
                'is_featured' => true,
                'created_by' => 1,
                'updated_by' => 1,
            ]
        ];
        
        foreach ($products as $productData) {
            // Tạo sản phẩm
            $product = Product::create($productData);
            
            // Tạo các biến thể cho sản phẩm
            if ($sizeValues && $sugarValues && $iceValues) {
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
                        'image' => null,
                        'active' => true
                    ]);
                    
                    // Thêm giá trị kích thước cho biến thể
                    ProductVariantDetail::create([
                        'product_variant_id' => $variant->id,
                        'variant_value_id' => $sizeValue->id
                    ]);
                    
                    // Thêm giá trị đường mặc định
                    $defaultSugar = $sugarValues->where('value', 'Vừa đường')->first();
                    if ($defaultSugar) {
                        ProductVariantDetail::create([
                            'product_variant_id' => $variant->id,
                            'variant_value_id' => $defaultSugar->id
                        ]);
                    }
                    
                    // Thêm giá trị đá mặc định
                    $defaultIce = $iceValues->where('value', 'Vừa đá')->first();
                    if ($defaultIce) {
                        ProductVariantDetail::create([
                            'product_variant_id' => $variant->id,
                            'variant_value_id' => $defaultIce->id
                        ]);
                    }
                }
            }
        }
    }
}
