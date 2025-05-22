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
        Schema::create('point_rules', function (Blueprint $table) {
            $table->id(); // Tạo cột ID tự động tăng làm khóa chính
            $table->string('name'); // Tên quy tắc tích điểm, ví dụ: "Mặc định", "Khuyến mãi cuối tuần"
            $table->decimal('point_per_currency', 10, 4)->default(0.01); // Số điểm được tích trên mỗi đơn vị tiền tệ, ví dụ: 1 điểm cho mỗi 100đ
            $table->decimal('min_order_amount', 10, 2)->default(0); // Số tiền đơn hàng tối thiểu để được tích điểm
            $table->enum('customer_type', ['all', 'regular', 'vip'])->default('all'); // Loại khách hàng áp dụng: tất cả, thường xuyên, VIP
            $table->boolean('is_active')->default(true); // Trạng thái kích hoạt của quy tắc tích điểm
            $table->timestamps(); // Tự động tạo 2 cột created_at và updated_at để theo dõi thời gian
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('point_rules');
    }
};