<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PromotionProgram;

class PromotionProgramSeeder extends Seeder
{
    public function run()
    {
        PromotionProgram::factory()->count(10)->create(); // Tạo 10 chương trình khuyến mãi
    }
}