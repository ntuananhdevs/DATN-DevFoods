<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update shipping settings to use 200k threshold and 25k shipping fee
        // This ensures consistent logic: orders > 200k = free shipping, <= 200k = 25k fee
        
        // Update free shipping threshold to 200,000
        DB::table('general_setting')
            ->where('key', 'free_shipping_threshold')
            ->update([
                'value' => '200000',
                'description' => 'Ngưỡng miễn phí vận chuyển (VND)',
                'updated_at' => now()
            ]);

        // Update shipping fee to 25,000
        DB::table('general_setting')
            ->updateOrInsert(
                ['key' => 'shipping_fee'],
                [
                    'value' => '25000',
                    'description' => 'Phí vận chuyển tiêu chuẩn (VND)',
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to 100k threshold and 15k fee
        DB::table('general_setting')
            ->where('key', 'free_shipping_threshold')
            ->update([
                'value' => '100000',
                'description' => 'Ngưỡng miễn phí vận chuyển (VND) - Đồng bộ với frontend',
                'updated_at' => now()
            ]);

        DB::table('general_setting')
            ->where('key', 'shipping_fee')
            ->update([
                'value' => '15000',
                'description' => 'Phí vận chuyển tiêu chuẩn (VND) - Đồng bộ với frontend',
                'updated_at' => now()
            ]);
    }
};
