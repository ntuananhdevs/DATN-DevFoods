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
            [
                'key' => 'shipping_base_fee',
                'value' => '25000',
                'description' => 'Phí vận chuyển cơ bản cho km đầu tiên (VND)',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'shipping_fee_per_km',
                'value' => '5000',
                'description' => 'Phí vận chuyển mỗi km sau km đầu tiên (VND)',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'max_delivery_distance',
                'value' => '20',
                'description' => 'Khoảng cách giao hàng tối đa (km)',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'default_preparation_time',
                'value' => '15',
                'description' => 'Thời gian chuẩn bị mặc định (phút)',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'average_speed_kmh',
                'value' => '20',
                'description' => 'Tốc độ trung bình của shipper (km/h)',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'buffer_time_minutes',
                'value' => '10',
                'description' => 'Thời gian dự phòng cho giao hàng (phút)',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('general_setting')->insert($settings);
    }
}