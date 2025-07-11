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
        // Update existing shipping settings to be consistent with frontend logic
        // Frontend uses: 100,000đ threshold and 15,000đ shipping fee
        
        // Update free shipping threshold from 200,000 to 100,000
        DB::table('general_setting')
            ->where('key', 'free_shipping_threshold')
            ->update([
                'value' => '100000',
                'description' => 'Ngưỡng miễn phí vận chuyển (VND) - Đồng bộ với frontend',
                'updated_at' => now()
            ]);

        // Insert or update shipping fee setting
        DB::table('general_setting')
            ->updateOrInsert(
                ['key' => 'shipping_fee'],
                [
                    'value' => '15000',
                    'description' => 'Phí vận chuyển tiêu chuẩn (VND) - Đồng bộ với frontend',
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
        // Revert to original settings
        DB::table('general_setting')
            ->where('key', 'free_shipping_threshold')
            ->update([
                'value' => '200000',
                'description' => 'Ngưỡng miễn phí vận chuyển (VND)',
                'updated_at' => now()
            ]);

        // Remove shipping_fee setting if it was inserted by this migration
        DB::table('general_setting')
            ->where('key', 'shipping_fee')
            ->delete();
    }
};
