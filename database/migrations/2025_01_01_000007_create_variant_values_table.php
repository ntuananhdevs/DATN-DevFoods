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
        Schema::create('variant_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('variant_attribute_id')->constrained();
            $table->string('value');
            $table->decimal('price_adjustment', 12, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('variant_values');
    }
};