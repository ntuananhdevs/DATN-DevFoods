<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('review_reports', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('review_id');
            $table->unsignedBigInteger('user_id');
            $table->string('reason_type', 32)->nullable()->comment('Chuẩn hóa: spam, harassment, hate_speech, inappropriate, misinformation, other');
            $table->text('reason_detail')->nullable()->comment('Thông tin bổ sung chi tiết');
            $table->timestamps();

            $table->unique(['review_id', 'user_id']);
            $table->foreign('review_id')->references('id')->on('product_reviews')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('review_reports');
    }
}; 