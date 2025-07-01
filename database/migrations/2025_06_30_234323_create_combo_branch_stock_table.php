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
        Schema::create('combo_branch_stock', function (Blueprint $table) {
            $table->id();
            $table->foreignId('combo_id')->constrained('combos')->onDelete('cascade'); // Liên kết với combo
            $table->foreignId('branch_id')->constrained('branches')->onDelete('cascade'); // Liên kết với chi nhánh
            $table->integer('quantity')->default(0); 
            $table->timestamps(); 

            $table->unique(['combo_id', 'branch_id']); // Đảm bảo mỗi combo chỉ có một số lượng tại một chi nhánh
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('combo_branch_stock');
    }
};
