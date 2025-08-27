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
        Schema::create('refund_requests', function (Blueprint $table) {
            $table->id();
            $table->string('request_code')->unique(); // Mã yêu cầu hoàn tiền
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->decimal('refund_amount', 10, 2); // Số tiền hoàn
            $table->enum('refund_method', ['balance', 'vnpay'])->default('balance'); // Phương thức hoàn tiền
            $table->enum('status', ['pending', 'approved', 'rejected', 'completed'])->default('pending');
            $table->text('reason'); // Lý do hoàn tiền
            $table->text('customer_message')->nullable(); // Tin nhắn từ khách hàng
            $table->json('attachments')->nullable(); // Hình ảnh đính kèm (JSON array)
            $table->text('admin_note')->nullable(); // Ghi chú từ admin
            $table->unsignedBigInteger('processed_by')->nullable(); // ID của admin xử lý
            $table->timestamp('requested_at')->useCurrent();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->foreign('customer_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('set null');
            $table->foreign('processed_by')->references('id')->on('users')->onDelete('set null');
            
            // Indexes
            $table->index(['customer_id', 'status']);
            $table->index(['branch_id', 'status']);
            $table->index('order_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('refund_requests');
    }
};