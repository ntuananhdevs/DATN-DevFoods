<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Update avatar column to support storing just filename instead of full URL
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Modify avatar column comment to clarify it now stores filename or URL
            $table->string('avatar', 255)->nullable()->comment('Avatar filename (S3) or full URL (Google/external)')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Revert avatar column back to original state
            $table->string('avatar', 255)->nullable()->comment('Avatar URL')->change();
        });
    }
};
