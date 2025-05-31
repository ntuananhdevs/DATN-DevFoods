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
        Schema::create('user_discount_codes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('discount_code_id')->constrained()->onDelete('cascade');
            $table->integer('usage_count')->default(0); // Số lần đã sử dụng
            $table->enum('status', ['available', 'used_up', 'expired'])->default('available');
            $table->timestamp('assigned_at')->useCurrent();
            $table->timestamp('first_used_at')->nullable();
            $table->timestamp('last_used_at')->nullable();
            
            $table->unique(['user_id', 'discount_code_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_discount_codes');
    }
};
