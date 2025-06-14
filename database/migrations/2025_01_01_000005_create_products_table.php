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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained();
            $table->string('sku')->unique();
            $table->string('name');
            $table->decimal('base_price', 12, 2);
            $table->integer('preparation_time')->nullable();
            $table->json('ingredients')->nullable();
            $table->text('short_description')->nullable();
            $table->text('description')->nullable();
            $table->integer('favorite_count')->default(0);
            $table->enum('status', ['coming_soon', 'selling', 'discontinued'])->default('selling');
            $table->timestamp('release_at')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('products');
    }
};