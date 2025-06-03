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
        Schema::create('driver_violations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('driver_id')->constrained('drivers')->onDelete('cascade');
            $table->string('violation_type', 100);
            $table->text('description');
            $table->enum('severity', ['low', 'medium', 'high', 'critical']);
            $table->decimal('penalty_amount', 10, 2)->nullable();
            $table->foreignId('reported_by')->constrained('users')->onDelete('cascade');
            $table->timestamp('reported_at');
            $table->enum('status', ['active', 'resolved', 'cancelled'])->default('active');
            $table->text('resolution_notes')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('driver_violations');
    }
};
