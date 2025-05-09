<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Thêm trường deleted_at vào các bảng cần xóa mềm
        // Ví dụ: bảng users
        Schema::table('users', function (Blueprint $table) {
            $table->softDeletes();
        });
        
        // Thêm cho các bảng khác nếu cần
        // Schema::table('products', function (Blueprint $table) {
        //     $table->softDeletes();
        // });
    }

    public function down()
    {
        // Xóa trường deleted_at khi rollback
        Schema::table('users', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        
        // Xóa cho các bảng khác nếu có
        // Schema::table('products', function (Blueprint $table) {
        //     $table->dropSoftDeletes();
        // });
    }
};