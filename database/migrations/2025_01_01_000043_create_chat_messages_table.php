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
        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sender_id')->constrained('users');
            $table->foreignId('receiver_id')->constrained('users');
            $table->foreignId('branch_id')->nullable()->constrained('branches'); // Chi nhánh liên quan (nếu có)
            $table->foreignId('conversation_id')->nullable()->constrained('conversations')->onDelete('cascade');
            $table->text('message');
            $table->string('attachment')->nullable(); // URL tệp đính kèm
            $table->string('attachment_type')->nullable(); // Loại tệp đính kèm (image, file, audio, video)
            $table->dateTime('sent_at');
            $table->enum('status', ['sent', 'delivered', 'read'])->default('sent');
            $table->dateTime('read_at')->nullable(); // Thời điểm đọc
            $table->boolean('is_deleted')->default(false); // Đã xóa chưa
            $table->boolean('is_system_message')->default(false); // Là tin nhắn hệ thống
            $table->foreignId('related_order_id')->nullable()->constrained('orders'); // Đơn hàng liên quan (nếu có)
            $table->enum('sender_type', ['customer', 'branch_admin', 'branch_staff', 'super_admin']); // Loại người gửi
            $table->enum('receiver_type', ['customer', 'branch_admin', 'super_admin']); // Loại người nhận
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_messages');
    }
};
