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
        // Xóa dữ liệu cũ trong bảng user_roles
        DB::table('user_roles')->truncate();

        // Lấy tất cả user và role
        $users = User::all();
        $roles = Role::all();
        
        // Đảm bảo có dữ liệu
        if ($users->isEmpty() || $roles->isEmpty()) {
            $this->command->info('Không có dữ liệu users hoặc roles. Vui lòng chạy UserSeeder và RoleSeeder trước.');
            return;
        }

        // Tạo mảng để theo dõi các cặp user-role đã được tạo
        $assignedPairs = [];
        
        // Thêm một số role phụ cho khoảng 30% user
        $usersForExtraRoles = $users->random(intval($users->count() * 0.3));
        foreach ($usersForExtraRoles as $user) {
            // Lấy ngẫu nhiên 1-2 role khác với primary_role_id
            $extraRolesCount = rand(1, 2);
            $availableRoles = $roles->where('id', '!=', $user->role_id);
            
            if ($availableRoles->count() > 0) {
                $extraRoles = $availableRoles->random(min($extraRolesCount, $availableRoles->count()));
                
                foreach ($extraRoles as $role) {
                    $pair = $user->id . '-' . $role->id;
                    // Kiểm tra xem cặp user-role này đã tồn tại chưa
                    if (!in_array($pair, $assignedPairs)) {
                        DB::table('user_roles')->insert([
                            'user_id' => $user->id,
                            'role_id' => $role->id,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                        $assignedPairs[] = $pair;
                    }
                }
            }
        }
        
        $this->command->info('Đã tạo ' . count($assignedPairs) . ' bản ghi user_roles.');
    }
}