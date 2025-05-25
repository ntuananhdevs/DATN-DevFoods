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
        Schema::create('branch_stock', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('branch_id');
            $table->unsignedBigInteger('product_variant_id');
            $table->integer('stock_quantity')->default(0);
            $table->timestamp('updated_at')->useCurrent();

            $table->foreign('branch_id')->references('id')->on('branches');
            $table->foreign('product_variant_id')->references('id')->on('product_variants');
            $table->unique(['branch_id', 'product_variant_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('branch_stock');
    }
};
