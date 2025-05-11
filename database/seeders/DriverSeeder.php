<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Driver;

class DriverSeeder extends Seeder
{
    public function run(): void
    {
        // Tạo 10 tài xế
        Driver::factory(8)->create();
        Driver::factory(2)->inactive()->create();
    }
}