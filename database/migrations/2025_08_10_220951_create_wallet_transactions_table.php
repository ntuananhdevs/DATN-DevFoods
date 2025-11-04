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
        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['deposit', 'withdraw', 'payment', 'refund'])->default('deposit');
            $table->decimal('amount', 15, 2);
            $table->string('payment_method')->nullable(); // vnpay, momo, bank_transfer
            $table->enum('status', ['pending', 'completed', 'failed', 'cancelled'])->default('pending');
            $table->string('description')->nullable();
            $table->string('transaction_code')->unique()->nullable();
            $table->json('metadata')->nullable(); // Thông tin bổ sung như thông tin ngân hàng
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index(['user_id', 'type']);
            $table->index(['user_id', 'status']);
            $table->index(['transaction_code']);
            $table->index(['created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallet_transactions');
    }
};
