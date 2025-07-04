<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Try to drop foreign key constraint if it exists
            try {
                $table->dropForeign(['payment_id']);
            } catch (\Exception $e) {
                // Foreign key might not exist, continue
            }
            
            // Drop the payment_id column
            $table->dropColumn('payment_id');
            
            // Add payment_method enum column
            $table->enum('payment_method', ['cod', 'vnpay', 'balance'])
                  ->after('discount_code_id')
                  ->default('cod');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Drop the payment_method column
            $table->dropColumn('payment_method');
            
            // Add back the payment_id column with foreign key constraint
            $table->foreignId('payment_id')
                  ->nullable()
                  ->constrained('payments')
                  ->after('discount_code_id');
        });
    }
};
