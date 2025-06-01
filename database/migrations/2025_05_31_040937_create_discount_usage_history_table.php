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
        Schema::create('discount_usage_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('discount_code_id')->constrained();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained();
            $table->foreignId('branch_id')->constrained();
            $table->string('guest_phone')->nullable(); // Cho khách vãng lai
            $table->decimal('original_amount', 12, 2); // Số tiền gốc
            $table->decimal('discount_amount', 12, 2); // Số tiền được giảm
            $table->timestamp('used_at')->useCurrent();
            
            $table->index(['discount_code_id', 'used_at']);
            $table->index(['user_id', 'used_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discount_usage_history');
    }
};
