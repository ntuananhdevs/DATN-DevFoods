<?php

namespace Database\Seeders;

use App\Models\VariantAttribute;
use App\Models\VariantValue;
use Illuminate\Database\Seeder;

class AttributeSeeder extends Seeder
{
    public function run()
    {
        // Chỉ tạo các VariantAttribute cần thiết để tránh quá nhiều variant
        // VariantValue sẽ được tạo riêng cho từng sản phẩm trong FastFoodSeeder

        VariantAttribute::create(['name' => 'Kích thước']);
        echo "Created VariantAttribute: Kích thước\n";
        
        VariantAttribute::create(['name' => 'Đường']);
        echo "Created VariantAttribute: Đường\n";
        
        echo "Optimized: Only 2 attributes created to reduce variant combinations\n";
        echo "Note: VariantValues will be created uniquely for each product in FastFoodSeeder\n";
    }
}