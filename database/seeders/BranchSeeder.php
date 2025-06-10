<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class BranchSeeder extends Seeder
{
    public function run()
    {
        // Đảm bảo có role manager
        $managerRole = Role::firstOrCreate(['name' => 'manager'], []);

        // Tạo một số manager nếu chưa có
        if (User::whereHas('roles', function ($query) {
            $query->where('name', 'manager');
        })->count() < 3) {
            $managers = [
                [
                    'user_name' => 'manager1',
                    'full_name' => 'Nguyễn Văn Quản Lý',
                    'email' => 'manager1@example.com',
                    'phone' => '0901234567',
                    'password' => Hash::make('password123'),
                ],
                [
                    'user_name' => 'manager2',
                    'full_name' => 'Trần Thị Quản Lý',
                    'email' => 'manager2@example.com',
                    'phone' => '0912345678',
                    'password' => Hash::make('password123'),
                ],
                [
                    'user_name' => 'manager3',
                    'full_name' => 'Lê Văn Quản Lý',
                    'email' => 'manager3@example.com',
                    'phone' => '0923456789',
                    'password' => Hash::make('password123'),
                ],
            ];

            foreach ($managers as $managerData) {
                $manager = User::create($managerData);
                $manager->roles()->attach($managerRole->id);
            }
        }

        // Tạo các chi nhánh cố định
        $fixedBranches = [
            [

                'name' => 'Chi nhánh Hà Nội',
                'address' => '123 Đường Láng, Đống Đa, Hà Nội',
                'phone' => '0243123456',
                'email' => 'hanoi@devfoods.com',
                'latitude' => 21.0278,
                'longitude' => 105.8342,
                'opening_hour' => '07:30',
                'closing_hour' => '22:30',
                'branch_code' => 'HCM001',
            ],
            [

                'name' => 'Chi nhánh Hồ Chí Minh',
                'address' => '456 Nguyễn Huệ, Quận 1, TP. Hồ Chí Minh',
                'phone' => '0283456789',
                'email' => 'hcm@devfoods.com',
                'latitude' => 10.7769,
                'longitude' => 106.7009,
                'opening_hour' => '07:00',
                'closing_hour' => '23:00',
                'branch_code' => 'HCM002',
            ],
            [

                'name' => 'Chi nhánh Đà Nẵng',
                'address' => '789 Nguyễn Văn Linh, Hải Châu, Đà Nẵng',
                'phone' => '0236789012',
                'email' => 'danang@devfoods.com',
                'latitude' => 16.0544,
                'longitude' => 108.2022,
                'opening_hour' => '08:00',
                'branch_code' => 'DN003',
                'closing_hour' => '22:00',
            ],
        ];

        $managerIds = User::whereHas('roles', function ($query) {
            $query->where('name', 'manager');
        })->pluck('id')->toArray();

        foreach ($fixedBranches as $index => $branchData) {
            Branch::create(array_merge($branchData, [
                'manager_user_id' => $managerIds[$index % count($managerIds)],
                'active' => true,
                'balance' => rand(5000, 10000),
                'rating' => rand(40, 50) / 10,
                'reliability_score' => rand(90, 100),
            ]));
        }
    }
}
