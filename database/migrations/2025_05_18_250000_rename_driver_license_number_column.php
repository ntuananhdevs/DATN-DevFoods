<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('drivers', function (Blueprint $table) {
            $table->renameColumn('driver_license_number', 'license_number');
        });
    }

    public function down()
    {
        Schema::table('drivers', function (Blueprint $table) {
            $table->renameColumn('license_number', 'driver_license_number');
        });
    }
}; 