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
        Schema::create('promotion_discount_codes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('promotion_program_id')->constrained()->onDelete('cascade');
            $table->foreignId('discount_code_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            
            $table->unique(['promotion_program_id', 'discount_code_id'], 'promotion_discount_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promotion_discount_codes');
    }
};
