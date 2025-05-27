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
            Schema::table('drivers', function (Blueprint $table) {
                // Thông tin cá nhân
                $table->text('address')->nullable()->after('phone_number');
                
                // Thông tin giấy phép lái xe
                $table->string('license_class', 10)->nullable()->after('license_number');
                $table->date('license_expiry')->nullable()->after('license_class');
                $table->string('license_plate', 20)->nullable()->after('license_expiry');
                
                // Trường để lưu ảnh giấy tờ
                $table->string('id_card_front')->nullable()->after('license_plate');
                $table->string('id_card_back')->nullable()->after('id_card_front');
                $table->string('license_front')->nullable()->after('id_card_back');
                $table->string('license_back')->nullable()->after('license_front');
                
                // Ghi chú nội bộ và lịch sử
                $table->text('admin_notes')->nullable()->after('auto_deposit_earnings');
                $table->timestamp('password_reset_at')->nullable()->after('admin_notes');
                $table->timestamp('password_changed_at')->nullable()->after('password_reset_at');
                $table->boolean('must_change_password')->default(false)->after('password_changed_at');
                $table->unsignedBigInteger('updated_by')->nullable()->after('must_change_password');
                
                // Thêm index cho performance
                $table->index(['status', 'is_available']);
                $table->index(['created_at']);
                $table->index(['updated_at']);
                
                // Foreign key cho updated_by
                $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
            });
        }

        /**
         * Reverse the migrations.
         */
        public function down(): void
        {
            Schema::table('drivers', function (Blueprint $table) {
                $table->dropForeign(['updated_by']);
                $table->dropIndex(['status', 'is_available']);
                $table->dropIndex(['created_at']);
                $table->dropIndex(['updated_at']);
                
                $table->dropColumn([
                    'address',
                    'license_class',
                    'license_expiry',
                    'license_plate',
                    'id_card_front',
                    'id_card_back',
                    'license_front',
                    'license_back',
                    'admin_notes',
                    'password_reset_at',
                    'password_changed_at',
                    'must_change_password',
                    'updated_by'
                ]);
            });
        }
    };
