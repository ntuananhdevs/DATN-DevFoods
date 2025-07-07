<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use App\Models\UserRole;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Chỉ chạy seeder này nếu chưa có dữ liệu trong bảng user_roles
        if (DB::table('user_roles')->count() > 0) {
            echo "UserRoleSeeder: Bảng user_roles đã có dữ liệu, bỏ qua.\n";
            return;
        }

        echo "UserRoleSeeder: Bắt đầu gán roles cho users...\n";

        // Gán role admin cho user admin
        $admin = User::where('user_name', 'admin')->first();
        $adminRole = Role::where('name', 'admin')->first();
        if ($admin && $adminRole) {
            DB::table('user_roles')->insert([
                'user_id' => $admin->id,
                'role_id' => $adminRole->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            echo "Đã gán role admin cho user: {$admin->email}\n";
        } else {
            echo "Không tìm thấy admin user hoặc admin role\n";
        }

        // Gán role manager cho các manager
        $managerRole = Role::where('name', 'manager')->first();
        $managers = User::whereIn('user_name', ['manager1', 'manager2', 'manager3'])->get();
        foreach ($managers as $manager) {
            if ($managerRole) {
                DB::table('user_roles')->insert([
                    'user_id' => $manager->id,
                    'role_id' => $managerRole->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                echo "Đã gán role manager cho user: {$manager->email}\n";
            }
        }

        // Gán role customer cho các customer
        $customerRole = Role::where('name', 'customer')->first();
        $customers = User::where('user_name', 'customer')->get();
        foreach ($customers as $customer) {
            if ($customerRole) {
                DB::table('user_roles')->insert([
                    'user_id' => $customer->id,
                    'role_id' => $customerRole->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                echo "Đã gán role customer cho user: {$customer->email}\n";
            }
        }

        echo "UserRoleSeeder: Hoàn thành gán roles.\n";
    }
}
