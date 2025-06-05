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
        Schema::create('promotion_programs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('banner_image')->nullable(); // Banner hiển thị
            $table->string('thumbnail_image')->nullable(); // Ảnh thu nhỏ
            
            // Phạm vi áp dụng
            $table->enum('applicable_scope', ['all_branches', 'specific_branches'])->default('all_branches');
            
            // Thời gian
            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();
            
            // Hiển thị
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false); // Nổi bật
            $table->integer('display_order')->default(0); // Thứ tự hiển thị
            
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promotion_programs');
    }
};
