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
        Schema::create('drivers', function (Blueprint $table) {
            $table->id();
            $table->string('email', 191)->unique();
            $table->string('password', 191);
            $table->string('full_name', 191);
            $table->string('phone_number', 191);
            $table->text('address')->nullable();
            $table->foreignId('application_id')->constrained('driver_applications')->onDelete('cascade');
            $table->string('status', 50);  // active, inactive, suspended, v.v.
            $table->boolean('is_available')->default(true);
            $table->decimal('balance', 12, 2)->default(0);
            $table->decimal('rating', 3, 2)->default(0);
            $table->integer('cancellation_count')->default(0);
            $table->decimal('reliability_score', 5, 2)->default(0);
            $table->integer('penalty_count')->default(0);
            $table->boolean('auto_deposit_earnings')->default(false);

            // OTP và các trường liên quan
            $table->string('otp', 6)->nullable();
            $table->timestamp('expires_at')->nullable();

            // Ghi chú và lịch sử
            $table->text('admin_notes')->nullable();
            $table->timestamp('password_reset_at')->nullable();
            $table->timestamp('password_changed_at')->nullable();
            $table->boolean('must_change_password')->default(false);
            $table->unsignedBigInteger('updated_by')->nullable();

            // Khóa/Unlock và lịch sử trạng thái
            $table->timestamp('locked_at')->nullable();
            $table->timestamp('locked_until')->nullable();
            $table->foreignId('locked_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('lock_reason')->nullable();
            $table->timestamp('unlocked_at')->nullable();
            $table->foreignId('unlocked_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('status_changed_at')->nullable();
            $table->foreignId('status_changed_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();

            // Indexes
            $table->index(['status', 'is_available']);
            $table->index(['created_at']);
            $table->index(['updated_at']);
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('drivers');
    }
};
