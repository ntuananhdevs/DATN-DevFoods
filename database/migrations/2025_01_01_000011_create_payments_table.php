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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->enum('payment_method', 
                            [
                                'cod',
                                'vnpay',
                                'balance'
                            ])->default('cod');
            $table->string('payer_name');
            $table->string('payer_email');
            $table->string('payer_phone');
            $table->string('txn_ref')->unique();
            $table->string('transaction_id')->nullable();
            $table->string('response_code')->nullable();
            $table->string('bank_code')->nullable();
            $table->unsignedBigInteger('payment_amount');
            $table->string('payment_currency')->default('VND');
            $table->enum('payment_status', ['pending', 'completed', 'failed', 'refunded'])->default('pending');
            $table->dateTime('payment_date')->nullable();
            $table->string('payment_method_detail')->nullable();
            $table->text('gateway_response')->nullable();
            $table->string('ip_address')->nullable();
            $table->text('callback_data')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};