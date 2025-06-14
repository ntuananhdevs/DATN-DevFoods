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
        Schema::create('branches', function (Blueprint $table) {
            $table->id();
            $table->string('branch_code')->unique();
            $table->string('name')->unique();
            $table->string('address', 255)->unique();
            $table->string('phone')->unique();
            $table->string('email')->unique();
            $table->unsignedBigInteger('manager_user_id')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->time('opening_hour');
            $table->time('closing_hour');
            $table->boolean('active')->default(true);
            $table->decimal('balance', 10, 2)->default(0);
            $table->decimal('rating', 3, 2)->default(5.00);
            $table->integer('reliability_score')->default(100);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('branches');
    }
};