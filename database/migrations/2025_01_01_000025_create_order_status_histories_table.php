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
        Schema::create('order_status_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->enum('old_status', ['new', 'processing', 'ready', 'delivery', 'completed', 'cancelled'])->nullable();
            $table->enum('new_status', ['new', 'processing', 'ready', 'delivery', 'completed', 'cancelled']);
            $table->foreignId('changed_by')->nullable()->constrained('users')->nullOnDelete(); // Ai thay đổi
            $table->string('changed_by_role')->nullable(); // Có thể là customer, admin, driver,...
            $table->text('note')->nullable();
            $table->timestamp('changed_at')->useCurrent();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_status_histories');
    }
};
