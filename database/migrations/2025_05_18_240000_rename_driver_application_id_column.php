<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('drivers', function (Blueprint $table) {
            $table->renameColumn('driver_application_id', 'application_id');
        });
    }

    public function down()
    {
        Schema::table('drivers', function (Blueprint $table) {
            $table->renameColumn('application_id', 'driver_application_id');
        });
    }
}; 