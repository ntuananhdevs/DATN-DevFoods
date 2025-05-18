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
            $table->string('phone_number')->after('password');
        });

        // Update existing records with phone numbers from applications
        $drivers = DB::table('drivers')
            ->join('driver_applications', 'drivers.application_id', '=', 'driver_applications.id')
            ->select('drivers.id', 'driver_applications.phone_number')
            ->get();

        foreach ($drivers as $driver) {
            DB::table('drivers')
                ->where('id', $driver->id)
                ->update(['phone_number' => $driver->phone_number]);
        }
    }

    public function down()
    {
        Schema::table('drivers', function (Blueprint $table) {
            $table->dropColumn('phone_number');
        });
    }
}; 