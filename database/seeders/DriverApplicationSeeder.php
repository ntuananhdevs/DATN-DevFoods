<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DriverApplication;

class DriverApplicationSeeder extends Seeder
{
    public function run(): void
    {
        // Tạo 20 đơn đăng ký với trạng thái khác nhau
        DriverApplication::factory(10)->approved()->create();
        DriverApplication::factory(5)->pending()->create();
        DriverApplication::factory(5)->rejected()->create();
    }
}