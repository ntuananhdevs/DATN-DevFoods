<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('branch_images', function (Blueprint $table) {
            $table->string('original_name')->nullable()->after('image_path');
        });
    }

    public function down()
    {
        Schema::table('branch_images', function (Blueprint $table) {
            $table->dropColumn('original_name');
        });
    }
};
