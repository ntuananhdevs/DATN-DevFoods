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
        Schema::table('drivers', function (Blueprint $table) {
            $table->timestamp('locked_at')->nullable()->after('admin_notes');
            $table->timestamp('locked_until')->nullable()->after('locked_at');
            $table->foreignId('locked_by')->nullable()->constrained('users')->nullOnDelete()->after('locked_until');
            $table->text('lock_reason')->nullable()->after('locked_by');
            $table->timestamp('unlocked_at')->nullable()->after('lock_reason');
            $table->foreignId('unlocked_by')->nullable()->constrained('users')->nullOnDelete()->after('unlocked_at');
            $table->timestamp('status_changed_at')->nullable()->after('unlocked_by');
            $table->foreignId('status_changed_by')->nullable()->constrained('users')->nullOnDelete()->after('status_changed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('drivers', function (Blueprint $table) {
            $table->dropForeign(['locked_by']);
            $table->dropForeign(['unlocked_by']);
            $table->dropForeign(['status_changed_by']);
            $table->dropColumn([
                'locked_at',
                'locked_until', 
                'locked_by',
                'lock_reason',
                'unlocked_at',
                'unlocked_by',
                'status_changed_at',
                'status_changed_by'
            ]);
        });
    }
};
