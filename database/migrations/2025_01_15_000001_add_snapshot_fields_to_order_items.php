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
        // Thêm các trường snapshot cho order_items
        Schema::table('order_items', function (Blueprint $table) {
            // Chỉ thêm các trường chưa tồn tại
            if (!Schema::hasColumn('order_items', 'product_name')) {
                $table->string('product_name')->nullable()->after('total_price');
            }
            if (!Schema::hasColumn('order_items', 'product_sku')) {
                $table->string('product_sku')->nullable()->after('product_name');
            }
            if (!Schema::hasColumn('order_items', 'product_description')) {
                $table->text('product_description')->nullable()->after('product_sku');
            }
            if (!Schema::hasColumn('order_items', 'product_image')) {
                $table->string('product_image')->nullable()->after('product_description');
            }
            if (!Schema::hasColumn('order_items', 'variant_name')) {
                $table->string('variant_name')->nullable()->after('product_image');
            }
            if (!Schema::hasColumn('order_items', 'variant_attributes')) {
                $table->json('variant_attributes')->nullable()->after('variant_name');
            }
            if (!Schema::hasColumn('order_items', 'variant_price')) {
                $table->decimal('variant_price', 10, 2)->nullable()->after('variant_attributes');
            }
            if (!Schema::hasColumn('order_items', 'combo_name')) {
                $table->string('combo_name')->nullable()->after('variant_price');
            }
            if (!Schema::hasColumn('order_items', 'combo_description')) {
                $table->text('combo_description')->nullable()->after('combo_name');
            }
            if (!Schema::hasColumn('order_items', 'combo_image')) {
                $table->string('combo_image')->nullable()->after('combo_description');
            }
            if (!Schema::hasColumn('order_items', 'combo_items')) {
                $table->json('combo_items')->nullable()->after('combo_image');
            }
            if (!Schema::hasColumn('order_items', 'combo_price')) {
                $table->decimal('combo_price', 10, 2)->nullable()->after('combo_items');
            }
        });
        
        // Thêm snapshot địa chỉ vào bảng orders
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'delivery_address_line')) {
                $table->string('delivery_address_line')->nullable()->after('address_id');
            }
            if (!Schema::hasColumn('orders', 'delivery_ward')) {
                $table->string('delivery_ward')->nullable()->after('delivery_address_line');
            }
            if (!Schema::hasColumn('orders', 'delivery_district')) {
                $table->string('delivery_district')->nullable()->after('delivery_ward');
            }
            if (!Schema::hasColumn('orders', 'delivery_province')) {
                $table->string('delivery_province')->nullable()->after('delivery_district');
            }
            if (!Schema::hasColumn('orders', 'delivery_phone')) {
                $table->string('delivery_phone')->nullable()->after('delivery_province');
            }
            if (!Schema::hasColumn('orders', 'delivery_recipient_name')) {
                $table->string('delivery_recipient_name')->nullable()->after('delivery_phone');
            }
        });

        // Thêm các trường snapshot cho order_item_toppings
        Schema::table('order_item_toppings', function (Blueprint $table) {
            if (!Schema::hasColumn('order_item_toppings', 'topping_name')) {
                $table->string('topping_name')->nullable()->after('price');
            }
            if (!Schema::hasColumn('order_item_toppings', 'topping_sku')) {
                $table->string('topping_sku')->nullable()->after('topping_name');
            }
            if (!Schema::hasColumn('order_item_toppings', 'topping_description')) {
                $table->text('topping_description')->nullable()->after('topping_sku');
            }
            if (!Schema::hasColumn('order_item_toppings', 'topping_image')) {
                $table->string('topping_image')->nullable()->after('topping_description');
            }
            if (!Schema::hasColumn('order_item_toppings', 'topping_unit_price')) {
                $table->decimal('topping_unit_price', 10, 2)->nullable()->after('topping_image');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $columnsToDropOrderItems = [];
            if (Schema::hasColumn('order_items', 'product_name')) $columnsToDropOrderItems[] = 'product_name';
            if (Schema::hasColumn('order_items', 'product_sku')) $columnsToDropOrderItems[] = 'product_sku';
            if (Schema::hasColumn('order_items', 'product_description')) $columnsToDropOrderItems[] = 'product_description';
            if (Schema::hasColumn('order_items', 'product_image')) $columnsToDropOrderItems[] = 'product_image';
            if (Schema::hasColumn('order_items', 'variant_name')) $columnsToDropOrderItems[] = 'variant_name';
            if (Schema::hasColumn('order_items', 'variant_attributes')) $columnsToDropOrderItems[] = 'variant_attributes';
            if (Schema::hasColumn('order_items', 'variant_price')) $columnsToDropOrderItems[] = 'variant_price';
            if (Schema::hasColumn('order_items', 'combo_name')) $columnsToDropOrderItems[] = 'combo_name';
            if (Schema::hasColumn('order_items', 'combo_description')) $columnsToDropOrderItems[] = 'combo_description';
            if (Schema::hasColumn('order_items', 'combo_image')) $columnsToDropOrderItems[] = 'combo_image';
            if (Schema::hasColumn('order_items', 'combo_items')) $columnsToDropOrderItems[] = 'combo_items';
            if (Schema::hasColumn('order_items', 'combo_price')) $columnsToDropOrderItems[] = 'combo_price';
            
            if (!empty($columnsToDropOrderItems)) {
                $table->dropColumn($columnsToDropOrderItems);
            }
        });
        
        Schema::table('orders', function (Blueprint $table) {
            $columnsToDropOrders = [];
            if (Schema::hasColumn('orders', 'delivery_address_line')) $columnsToDropOrders[] = 'delivery_address_line';
            if (Schema::hasColumn('orders', 'delivery_ward')) $columnsToDropOrders[] = 'delivery_ward';
            if (Schema::hasColumn('orders', 'delivery_district')) $columnsToDropOrders[] = 'delivery_district';
            if (Schema::hasColumn('orders', 'delivery_province')) $columnsToDropOrders[] = 'delivery_province';
            if (Schema::hasColumn('orders', 'delivery_phone')) $columnsToDropOrders[] = 'delivery_phone';
            if (Schema::hasColumn('orders', 'delivery_recipient_name')) $columnsToDropOrders[] = 'delivery_recipient_name';
            
            if (!empty($columnsToDropOrders)) {
                $table->dropColumn($columnsToDropOrders);
            }
        });

        Schema::table('order_item_toppings', function (Blueprint $table) {
            $columnsToDropToppings = [];
            if (Schema::hasColumn('order_item_toppings', 'topping_name')) $columnsToDropToppings[] = 'topping_name';
            if (Schema::hasColumn('order_item_toppings', 'topping_sku')) $columnsToDropToppings[] = 'topping_sku';
            if (Schema::hasColumn('order_item_toppings', 'topping_description')) $columnsToDropToppings[] = 'topping_description';
            if (Schema::hasColumn('order_item_toppings', 'topping_image')) $columnsToDropToppings[] = 'topping_image';
            if (Schema::hasColumn('order_item_toppings', 'topping_unit_price')) $columnsToDropToppings[] = 'topping_unit_price';
            
            if (!empty($columnsToDropToppings)) {
                $table->dropColumn($columnsToDropToppings);
            }
        });
    }
};