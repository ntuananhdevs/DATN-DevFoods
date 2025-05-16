<?php

namespace Database\Seeders;

use App\Models\Attribute;
use App\Models\AttributeValue;
use Illuminate\Database\Seeder;

class AttributeSeeder extends Seeder
{
    public function run()
    {
        // Tạo thuộc tính Kích thước
        $sizeAttribute = Attribute::create(['name' => 'Kích thước']);
        
        // Tạo các giá trị cho thuộc tính Kích thước
        $sizeValues = ['Nhỏ', 'Vừa', 'Lớn', 'Siêu lớn'];
        foreach ($sizeValues as $value) {
            AttributeValue::create([
                'attribute_id' => $sizeAttribute->id,
                'value' => $value
            ]);
        }
        
        // Tạo thuộc tính Đường
        $sugarAttribute = Attribute::create(['name' => 'Đường']);
        
        // Tạo các giá trị cho thuộc tính Đường
        $sugarValues = ['Không đường', 'Ít đường', 'Vừa đường', 'Nhiều đường'];
        foreach ($sugarValues as $value) {
            AttributeValue::create([
                'attribute_id' => $sugarAttribute->id,
                'value' => $value
            ]);
        }
        
        // Tạo thuộc tính Đá
        $iceAttribute = Attribute::create(['name' => 'Đá']);
        
        // Tạo các giá trị cho thuộc tính Đá
        $iceValues = ['Không đá', 'Ít đá', 'Vừa đá', 'Nhiều đá'];
        foreach ($iceValues as $value) {
            AttributeValue::create([
                'attribute_id' => $iceAttribute->id,
                'value' => $value
            ]);
        }
        
        // Tạo thuộc tính Topping
        $toppingAttribute = Attribute::create(['name' => 'Topping']);
        
        // Tạo các giá trị cho thuộc tính Topping
        $toppingValues = ['Trân châu đen', 'Trân châu trắng', 'Thạch cà phê', 'Thạch trái cây', 'Pudding', 'Kem cheese'];
        foreach ($toppingValues as $value) {
            AttributeValue::create([
                'attribute_id' => $toppingAttribute->id,
                'value' => $value
            ]);
        }
    }
}