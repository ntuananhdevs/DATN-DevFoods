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
        Schema::create('product_imgs', function (Blueprint $table) {
            $table->id();
            $table->string('img')->nullable();
            $table->boolean('is_primary')->default(false);
            $table->foreignId('product_id')->constrained()->onDelete('cascade');            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_imgs');
    }
};
