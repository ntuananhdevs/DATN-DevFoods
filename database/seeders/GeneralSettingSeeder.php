<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GeneralSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            [
                'key' => 'tax_rate',
                'value' => '10',
                'description' => 'Thuế suất áp dụng cho đơn hàng (phần trăm)',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'free_shipping_threshold',
                'value' => '200000',
                'description' => 'Ngưỡng miễn phí vận chuyển (VND)',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('general_setting')->insert($settings);
    }
}