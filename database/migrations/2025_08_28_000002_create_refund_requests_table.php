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
            $table->string('refund_code')->unique(); // Mã yêu cầu hoàn tiền
            $table->unsignedBigInteger('order_id'); // ID đơn hàng
            $table->unsignedBigInteger('customer_id'); // ID khách hàng
            $table->unsignedBigInteger('branch_id')->nullable(); // ID chi nhánh xử lý
            $table->decimal('refund_amount', 15, 2); // Số tiền hoàn
            $table->enum('refund_type', ['full', 'partial'])->default('full'); // Loại hoàn tiền
            $table->text('reason'); // Lý do hoàn tiền
            $table->text('customer_message')->nullable(); // Tin nhắn từ khách hàng
            $table->json('attachments')->nullable(); // File đính kèm (hình ảnh, video)
            $table->enum('status', ['pending', 'processing', 'approved', 'rejected', 'completed'])->default('pending');
            $table->text('admin_note')->nullable(); // Ghi chú từ admin
            $table->unsignedBigInteger('processed_by')->nullable(); // ID người xử lý
            $table->timestamp('processed_at')->nullable(); // Thời gian xử lý
            $table->timestamp('completed_at')->nullable(); // Thời gian hoàn thành
            $table->timestamps();
            $table->softDeletes(); // Thêm cột deleted_at cho soft deletes
            
            // Foreign keys
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->foreign('customer_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('set null');
            $table->foreign('processed_by')->references('id')->on('users')->onDelete('set null');
            
            // Indexes
            $table->index(['customer_id', 'status']);
            $table->index(['order_id']);
            $table->index(['status', 'created_at']);
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