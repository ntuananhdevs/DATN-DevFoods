<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DiscountCode;

class DiscountCodeSeeder extends Seeder
{
    public function run()
    {
        DiscountCode::factory()->count(20)->create(); // Tạo 20 mã giảm giá
    }
}