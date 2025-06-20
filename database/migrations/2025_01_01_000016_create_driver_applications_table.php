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
        Schema::create('driver_applications', function (Blueprint $table) {
            $table->id();
            $table->string('full_name', 100);
            $table->string('email', 100)->unique();
            $table->string('phone_number', 15)->unique();
            $table->date('date_of_birth');
            $table->enum('gender', ['male', 'female', 'other']);
            $table->string('id_card_number', 20)->unique();
            $table->date('id_card_issue_date');
            $table->string('id_card_issue_place', 100);
            $table->text('address');
            // $table->string('city', 50);
            // $table->string('district', 50);
            $table->enum('vehicle_type', ['motorcycle', 'car', 'bicycle']);
            $table->string('vehicle_model', 50);
            $table->string('vehicle_color', 50);
            $table->string('license_plate', 20)->unique();
            $table->string('driver_license_number', 20)->unique();
            $table->string('id_card_front_image', 255)->nullable();
            $table->string('id_card_back_image', 255)->nullable();
            $table->string('driver_license_image', 255)->nullable();
            $table->string('profile_image', 255)->nullable();
            $table->string('vehicle_registration_image', 255)->nullable();
            $table->string('bank_name', 100);
            $table->string('bank_account_number', 50);
            $table->string('bank_account_name', 100);
            $table->string('emergency_contact_name', 100);
            $table->string('emergency_contact_phone', 20);
            $table->string('emergency_contact_relationship', 50);
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('admin_notes')->nullable();
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('driver_applications');
    }
};