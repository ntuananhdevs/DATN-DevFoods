<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Lấy danh sách role_id hiện có
        $roleIds = Role::pluck('id')->toArray();

        // Tạo 10 user mẫu và gán role_id ngẫu nhiên từ các role đã có
        User::factory(10)->create([
            'role_id' => function () use ($roleIds) {
                return $roleIds[array_rand($roleIds)];
            },
        ]);

        // Tạo user admin mặc định
        User::factory()->create([
            'user_name' => 'spadmin',
            'full_name' => 'Administrator',
            'email' => 'admin@devfoods.com',
            'password' => bcrypt('admin'),
            'role_id' => Role::where('name', 'admin')->first()->id,
        ]);

        // Tạo user khách hàng mặc định
        User::factory()->create([
            'user_name' => 'customer',
            'full_name' => 'Test Customer',
            'email' => 'customer@example.com',
            'role_id' => Role::where('name', 'customer')->first()->id,
        ]);

        
        // Tạo 3 người quản lý chi nhánh
        $managerRoleId = Role::where('name', 'manager')->first()->id ?? $roleIds[array_rand($roleIds)];
        
        $branchManagers = [
            [
                'user_name' => 'manager1',
                'full_name' => 'Nguyễn Văn Quản Lý',
                'email' => 'manager1@devfoods.com',
                'password' => bcrypt('manager123'),
                'role_id' => $managerRoleId,
            ],
            [
                'user_name' => 'manager2',
                'full_name' => 'Trần Thị Quản Lý',
                'email' => 'manager2@devfoods.com',
                'password' => bcrypt('manager123'),
                'role_id' => $managerRoleId,
            ],
            [
                'user_name' => 'manager3',
                'full_name' => 'Lê Minh Quản Lý',
                'email' => 'manager3@devfoods.com',
                'password' => bcrypt('manager123'),
                'role_id' => $managerRoleId,
            ],
        ];
        
        foreach ($branchManagers as $manager) {
            User::factory()->create($manager);
        }
    }
}