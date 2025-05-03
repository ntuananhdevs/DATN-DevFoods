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
        Schema::create('drivers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->string('license_number');
            $table->string('vehicle_type');
            $table->string('vehicle_registration');
            $table->string('vehicle_color');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->boolean('is_available')->default(true);
            $table->decimal('current_latitude', 10, 8)->nullable();
            $table->decimal('current_longitude', 11, 8)->nullable();
            $table->decimal('balance', 10, 2)->default(0);
            $table->decimal('rating', 3, 2)->default(5.00);
            $table->integer('cancellation_count')->default(0);
            $table->integer('reliability_score')->default(100);
            $table->integer('penalty_count')->default(0);
            $table->boolean('auto_deposit_earnings')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('drivers');
    }
};