<?php

namespace Database\Seeders;

use App\Models\VariantAttribute;
use App\Models\VariantValue;
use Illuminate\Database\Seeder;

class AttributeSeeder extends Seeder
{
    public function run()
    {
        // Tạo thuộc tính Kích thước
        $sizeAttribute = VariantAttribute::create(['name' => 'Kích thước']);
        
        // Tạo các giá trị cho thuộc tính Kích thước
        $sizeValues = ['Nhỏ', 'Vừa', 'Lớn', 'Siêu lớn'];
        foreach ($sizeValues as $value) {
            VariantValue::create([
                'variant_attribute_id' => $sizeAttribute->id,
                'value' => $value
            ]);
        }
        
        // Tạo thuộc tính Đường
        $sugarAttribute = VariantAttribute::create(['name' => 'Đường']);
        
        // Tạo các giá trị cho thuộc tính Đường
        $sugarValues = ['Không đường', 'Ít đường', 'Vừa đường', 'Nhiều đường'];
        foreach ($sugarValues as $value) {
            VariantValue::create([
                'variant_attribute_id' => $sugarAttribute->id,
                'value' => $value
            ]);
        }
        
        // Tạo thuộc tính Đá
        $iceAttribute = VariantAttribute::create(['name' => 'Đá']);
        
        // Tạo các giá trị cho thuộc tính Đá
        $iceValues = ['Không đá', 'Ít đá', 'Vừa đá', 'Nhiều đá'];
        foreach ($iceValues as $value) {
            VariantValue::create([
                'variant_attribute_id' => $iceAttribute->id,
                'value' => $value
            ]);
        }
        
        // Tạo thuộc tính Topping
        $toppingAttribute = VariantAttribute::create(['name' => 'Topping']);
        
        // Tạo các giá trị cho thuộc tính Topping
        $toppingValues = ['Trân châu đen', 'Trân châu trắng', 'Thạch cà phê', 'Thạch trái cây', 'Pudding', 'Kem cheese'];
        foreach ($toppingValues as $value) {
            VariantValue::create([
                'variant_attribute_id' => $toppingAttribute->id,
                'value' => $value
            ]);
        }
    }
}