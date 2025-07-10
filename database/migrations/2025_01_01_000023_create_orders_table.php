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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_code')->unique()->nullable()->index();
            $table->foreignId('customer_id')->nullable()->constrained('users');
            $table->foreignId('branch_id')->constrained('branches');
            $table->foreignId('driver_id')->nullable()->constrained('drivers');
            $table->foreignId('address_id')->nullable()->constrained('addresses');
            $table->foreignId('discount_code_id')->nullable()->constrained('discount_codes');
            $table->foreignId('payment_id')->nullable()->constrained('payments');

            $table->string('guest_name')->nullable();
            $table->string('guest_phone')->nullable();
            $table->string('guest_email')->nullable();
            $table->string('guest_address')->nullable();
            $table->string('guest_ward')->nullable();
            $table->string('guest_district')->nullable();
            $table->string('guest_city')->nullable();
            $table->decimal('guest_latitude', 10, 8)->nullable();
            $table->decimal('guest_longitude', 11, 8)->nullable();

            $table->timestamp('estimated_delivery_time')->nullable();
            $table->timestamp('actual_delivery_time')->nullable();
            $table->decimal('delivery_fee', 12, 2)->default(0);
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->decimal('tax_amount', 12, 2)->default(0);
            $table->timestamp('order_date')->useCurrent();
            $table->timestamp('delivery_date')->nullable();
            $table->enum('status', [
                            'awaiting_confirmation',  //  chờ xác nhận
                            'confirmed',              //  đã xác nhận
                            'awaiting_driver',        //  chờ tài xế nhận đơn
                            'driver_assigned',        // Tài xế đã được giao đơn
                            'driver_confirmed',       // Tài xế đã xác nhận đơn
                            'driver_picked_up',       // Tài xế đã nhận đơn
                            'in_transit',             // Đơn hàng đang trong quá trình giao hàng
                            'delivered',              // Đơn hàng đã giao thành công
                            'item_received', // Khách hàng đã nhận hàng
                            'cancelled', // Đơn hàng đã bị hủy
                            'refunded', // Đơn hàng đã được hoàn tiền
                            'payment_failed', // Thanh toán thất bại
                            'payment_received', // Thanh toán đã nhận
                            'order_failed' // Đơn hàng đã thất bại
            ])->default('awaiting_confirmation');
            $table->integer('points_earned')->default(0);
            $table->decimal('subtotal', 12, 2);
            $table->decimal('total_amount', 12, 2);
            $table->text('delivery_address')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
        // Tạo bảng order_items
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');

            // Nếu đơn hàng là sản phẩm đơn lẻ
            $table->foreignId('product_variant_id')->nullable()->constrained('product_variants')->onDelete('cascade');

            // Nếu đơn hàng là combo cố định
            $table->foreignId('combo_id')->nullable()->constrained('combos')->onDelete('cascade');

            $table->integer('quantity')->default(1);
            $table->decimal('unit_price', 12, 2);
            $table->decimal('total_price', 12, 2);
            $table->timestamps();

            // Chú ý: trong một dòng order_detail, chỉ nên có product_variant_id hoặc combo_id, không cùng lúc.
        });
    }

    public function down()
    {
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
    }
};