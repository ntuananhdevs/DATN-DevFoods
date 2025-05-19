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
        Schema::create('product_variant_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_variant_id')->constrained();
            $table->foreignId('variant_value_id')->constrained();
            $table->unique(['product_variant_id', 'variant_value_id'], 'pvd_product_variant_value_unique');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('product_variant_details');
    }
};