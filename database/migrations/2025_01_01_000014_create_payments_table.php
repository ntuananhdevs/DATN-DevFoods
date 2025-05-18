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
            $table->foreignId('payment_method_id')->constrained('payment_methods');
            $table->string('payer_name'); // Tên người thanh toán (có thể khác user - ví dụ người đặt hộ)
            $table->string('payer_email'); // Email người thanh toán
            $table->string('payer_phone'); // SĐT người thanh toán
            $table->string('bank_code')->nullable(); // Mã ngân hàng (nếu thanh toán qua bank gateway)
            $table->string('response_code')->nullable(); // Mã phản hồi từ cổng thanh toán (VNPAY, Momo...)
            $table->text('gateway_response')->nullable(); // Nội dung chi tiết phản hồi từ cổng thanh toán
            $table->string('transaction_id')->nullable(); // Mã giao dịch từ cổng thanh toán
            $table->decimal('amount', 10, 2); // Số tiền thanh toán
            $table->enum('status', ['pending', 'completed', 'failed', 'refunded'])->default('pending');
            $table->dateTime('payment_date'); // Ngày thanh toán
            $table->text('payment_details')->nullable(); // Chi tiết thanh toán (JSON)
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