<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('users');
            $table->foreignId('branch_id')->nullable()->constrained('branches');
            $table->enum('status', ['new', 'distributed', 'closed', 'active', 'resolved', 'open', 'pending'])->default('new');
            $table->boolean('is_distributed')->default(false); // Trạng thái phân phối
            $table->timestamp('distribution_time')->nullable(); // Thời gian phân phối
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conversations');
    }
};
