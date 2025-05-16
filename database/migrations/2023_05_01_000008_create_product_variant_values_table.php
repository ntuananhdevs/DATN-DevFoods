<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductVariantValuesTable extends Migration
{
    public function up()
    {
        Schema::create('product_variant_values', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('product_variant_id');
            $table->unsignedBigInteger('attribute_value_id');
            $table->timestamps();

            // Thay đổi cách đặt tên ràng buộc unique
            $table->unique(['product_variant_id', 'attribute_value_id'], 'pv_attr_val_unique');
            
            $table->foreign('product_variant_id')->references('id')->on('product_variants')->onDelete('cascade');
            $table->foreign('attribute_value_id')->references('id')->on('attribute_values')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('product_variant_values');
    }
}