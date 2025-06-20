<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
      public function up()
    {
        Schema::create('discount_codes', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique(); // Mã voucher
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('image')->nullable(); // Hình ảnh voucher
            
            // Loại giảm giá
            $table->enum('discount_type', ['percentage', 'fixed_amount', 'free_shipping']);
            $table->decimal('discount_value', 12, 2); // Giá trị giảm (% hoặc số tiền)
            $table->enum('min_requirement_type', ['order_amount', 'product_price'])->nullable(); // Loại điều kiện tối thiểu
            $table->decimal('min_requirement_value', 12, 2)->nullable(); // Giá trị điều kiện tối thiểu
            $table->decimal('max_discount_amount', 12, 2)->nullable(); // Giảm tối đa (cho %)
            
            // Phạm vi áp dụng
            $table->enum('applicable_scope', ['all_branches', 'specific_branches'])->default('all_branches');
            $table->enum('applicable_items', ['all_items', 'all_products', 'all_categories', 'all_combos', 'specific_products', 'specific_categories', 'specific_combos', 'specific_variants'])->default('all_items');
            
            // Áp dụng theo rank
            $table->json('applicable_ranks')->nullable(); // [1,2,3] - ID của các rank được áp dụng
            $table->boolean('rank_exclusive')->default(false); // Chỉ dành cho rank cụ thể
            
            // Thời gian áp dụng
            $table->json('valid_days_of_week')->nullable(); // [1,2,3,4,5] (T2-T6) hoặc [6,0] (cuối tuần)
            $table->time('valid_from_time')->nullable(); // Từ giờ
            $table->time('valid_to_time')->nullable(); // Đến giờ
            
            // Đối tượng sử dụng
            $table->enum('usage_type', ['public', 'personal'])->default('public');
            $table->integer('max_total_usage')->nullable(); // Tổng số lần sử dụng tối đa
            $table->integer('max_usage_per_user')->default(1); // Số lần 1 user có thể dùng
            $table->integer('current_usage_count')->default(0); // Số lần đã sử dụng
            
            // Thời gian hiệu lực
            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();

            $table->boolean('is_active')->default(true);
            
            // Hiển thị
            $table->boolean('is_featured')->default(false); // Nổi bật
            $table->integer('display_order')->default(0);
            
            // Người tạo
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            
            // Indexes
            $table->index(['is_active', 'start_date', 'end_date']);
            $table->index(['discount_type', 'is_active']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('discount_codes');
    }
};