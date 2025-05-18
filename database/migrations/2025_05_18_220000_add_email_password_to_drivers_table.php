<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::table('drivers', function (Blueprint $table) {
            $table->string('email')->nullable()->after('id');
            $table->string('password')->after('email');
        });


        // Make email required and unique
        Schema::table('drivers', function (Blueprint $table) {
            $table->string('email')->unique()->nullable(false)->change();
        });
    }

    public function down()
    {
        Schema::table('drivers', function (Blueprint $table) {
            $table->dropColumn(['email', 'password']);
        });
    }
}; 