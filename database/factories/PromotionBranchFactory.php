<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\PromotionProgram;
use App\Models\Branch;
use App\Models\PromotionBranch;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class PromotionBranchFactory extends Factory
{
    protected $model = PromotionBranch::class;

    public function definition(): array
    {
        // Lấy một chi nhánh ngẫu nhiên từ cơ sở dữ liệu
        $branch = Branch::inRandomOrder()->first();

        // Nếu không có chi nhánh, tạo một chi nhánh tạm thời
        if (!$branch) {
            $managerRole = Role::firstOrCreate(['name' => 'manager'], [
                'display_name' => 'Quản lý chi nhánh',
                'description' => 'Quản lý chi nhánh cửa hàng'
            ]);

            $manager = User::whereHas('roles', function($query) {
                $query->where('name', 'manager');
            })->inRandomOrder()->first();

            if (!$manager) {
                $manager = User::create([
                    'user_name' => 'temp_manager_' . uniqid(),
                    'full_name' => 'Quản Lý Tạm Thời',
                    'email' => 'temp_manager_' . uniqid() . '@example.com',
                    'phone' => '0900000' . rand(100, 999),
                    'password' => Hash::make('password123'),
                ]);
                $manager->roles()->attach($managerRole->id);
            }

            $branch = Branch::create([
                'branch_code' => 'TEMP' . rand(1000, 9999),
                'name' => 'Chi nhánh Tạm thời',
                'address' => 'Địa chỉ Tạm thời',
                'phone' => '0900000' . rand(100, 999),
                'email' => 'temp' . rand(1000, 9999) . '@devfoods.com',
                'latitude' => 10.7769,
                'longitude' => 106.7009,
                'opening_hour' => '08:00',
                'closing_hour' => '22:00',
                'manager_user_id' => $manager->id,
                'active' => true,
                'balance' => rand(5000, 10000),
                'rating' => rand(40, 50) / 10,
                'reliability_score' => rand(90, 100),
            ]);
        }

        return [
            'promotion_program_id' => PromotionProgram::factory(),
            'branch_id' => $branch->id,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}