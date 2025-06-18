<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('driver_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('driver_id')->constrained('drivers')->onDelete('cascade');

            // Giấy phép lái xe
            $table->string('license_number', 191);
            $table->string('license_class', 10)->nullable();
            $table->date('license_expiry')->nullable();
            $table->string('license_front')->nullable();
            $table->string('license_back')->nullable();

            // Giấy tờ tuỳ thân
            $table->string('id_card_front')->nullable();
            $table->string('id_card_back')->nullable();

            // Thông tin xe
            $table->string('vehicle_type', 191)->nullable();
            $table->string('vehicle_registration', 191)->nullable();
            $table->string('vehicle_color', 191)->nullable();
            $table->string('license_plate', 20)->nullable();

            $table->timestamps();

            $table->index('driver_id');
            $table->index('license_number');
        });
    }

    public function down()
    {
        Schema::dropIfExists('driver_documents');
    }
};
