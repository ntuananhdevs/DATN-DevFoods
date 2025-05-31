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
        // Tạo 10 user mẫu
        \App\Models\User::factory(10)->create();

        // Tạo user admin mặc định
        \App\Models\User::factory()->create([
            'user_name' => 'spadmin',
            'full_name' => 'Administrator',
            'email' => 'admin@devfoods.com',
            'password' => bcrypt('admin'),
        ]);

        // Tạo user khách hàng mặc định 
        \App\Models\User::factory()->create([
            'user_name' => 'customer',
            'full_name' => 'Test Customer',
            'email' => 'customer@example.com',
        ]);

        // Tạo 3 người quản lý chi nhánh
        $branchManagers = [
            [
                'user_name' => 'manager1',
                'full_name' => 'Nguyễn Văn Quản Lý',
                'email' => 'manager1@devfoods.com',
                'password' => bcrypt('manager123'),
            ],
            [
                'user_name' => 'manager2',
                'full_name' => 'Trần Thị Quản Lý',
                'email' => 'manager2@devfoods.com',
                'password' => bcrypt('manager123'),
            ],
            [
                'user_name' => 'manager3',
                'full_name' => 'Lê Minh Quản Lý',
                'email' => 'manager3@devfoods.com',
                'password' => bcrypt('manager123'),
            ],
        ];
        foreach ($branchManagers as $manager) {
            \App\Models\User::factory()->create($manager);
        }
    }
}