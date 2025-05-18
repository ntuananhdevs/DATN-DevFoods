<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('drivers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('driver_application_id')->constrained('driver_applications')->onDelete('cascade');
            $table->string('driver_license_number')->unique();
            $table->string('vehicle_type')->nullable();
            $table->string('vehicle_registration')->nullable();
            $table->string('vehicle_color')->nullable();
            $table->string('status');  // active, inactive, suspended
            $table->boolean('is_available')->default(true);
            $table->decimal('current_latitude', 10, 7)->nullable();
            $table->decimal('current_longitude', 10, 7)->nullable();
            $table->decimal('balance', 12, 2)->default(0);
            $table->decimal('rating', 3, 2)->default(0);
            $table->integer('cancellation_count')->default(0);
            $table->decimal('reliability_score', 5, 2)->default(0);
            $table->integer('penalty_count')->default(0);
            $table->boolean('auto_deposit_earnings')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('drivers');
    }
};