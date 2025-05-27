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
        Schema::create('banners', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();             // Tiêu đề banner
            $table->string('image_path');                    // Đường dẫn ảnh banner
            $table->string('position')->default('homepage');  // Vị trí hiển thị banner (top, bottom, ...)
            $table->text('description')->nullable();        // Mô tả banner
            $table->string('link')->nullable();              // Link khi click banner
            $table->boolean('is_active')->default(true);    // Trạng thái hiển thị
            $table->timestamp('start_at')->nullable();      // Thời gian bắt đầu hiển thị
            $table->timestamp('end_at')->nullable();        // Thời gian kết thúc hiển thị
            $table->integer('order')->default(0)->nullable();       // Thứ tự hiển thị
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('banners');
    }
};
