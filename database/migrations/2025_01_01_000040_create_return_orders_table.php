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
        Schema::create('return_orders', function (Blueprint $table) {
            $table->id();
        
            // Liên kết đơn hàng gốc
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
        
            // Người thực hiện yêu cầu trả hàng (có thể là khách hàng)
            $table->foreignId('customer_id')->constrained('users');
        
            // Ngày yêu cầu trả hàng
            $table->dateTime('requested_at');
        
            // Lý do trả hàng
            $table->text('reason')->nullable();
        
            // Trạng thái xử lý trả hàng
            $table->enum('status', ['requested', 'approved', 'rejected', 'processing', 'completed'])->default('requested');
        
            // Số tiền hoàn lại
            $table->decimal('refunded_amount', 10, 2)->default(0);
        
            // Bên chịu trách nhiệm
            $table->string('responsible_party')->nullable(); // ví dụ: 'branch', 'driver', 'system'
        
            // Hình ảnh minh họa (nếu có)
            $table->string('return_photo')->nullable();
        
            // Thời điểm hoàn tiền xong
            $table->dateTime('refunded_at')->nullable();
        
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('return_orders');
    }
};