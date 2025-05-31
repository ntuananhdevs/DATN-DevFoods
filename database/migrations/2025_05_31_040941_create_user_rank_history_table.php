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
        Schema::create('user_rank_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('old_rank_id')->nullable()->constrained('user_ranks');
            $table->foreignId('new_rank_id')->constrained('user_ranks');
            $table->decimal('total_spending', 12, 2); // Tổng chi tiêu tại thời điểm lên rank
            $table->integer('total_orders'); // Tổng số đơn hàng
            $table->text('reason')->nullable(); // Lý do thay đổi rank
            $table->timestamp('changed_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_rank_history');
    }
};
