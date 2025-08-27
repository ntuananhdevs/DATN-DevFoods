<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('wallet_transactions', function (Blueprint $table) {
            $table->timestamp('expires_at')->nullable()->after('processed_at');
            $table->index(['status', 'expires_at']);
        });
        
        // Cập nhật enum status
        DB::statement("ALTER TABLE wallet_transactions MODIFY COLUMN status ENUM('pending', 'completed', 'failed', 'cancelled', 'expired') DEFAULT 'pending'");
    }

    public function down(): void
    {
        Schema::table('wallet_transactions', function (Blueprint $table) {
            $table->dropIndex(['status', 'expires_at']);
            $table->dropColumn('expires_at');
        });
        
        DB::statement("ALTER TABLE wallet_transactions MODIFY COLUMN status ENUM('pending', 'completed', 'failed', 'cancelled') DEFAULT 'pending'");
    }
};
