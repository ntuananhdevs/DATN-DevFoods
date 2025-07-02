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
        Schema::table('combos', function (Blueprint $table) {
            $table->string('status')->default('selling')->after('price');
        });
        // Migrate dữ liệu từ active sang status
        DB::table('combos')->where('active', true)->update(['status' => 'selling']);
        DB::table('combos')->where('active', false)->update(['status' => 'discontinued']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('combos', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
