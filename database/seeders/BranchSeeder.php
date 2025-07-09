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
        // Lấy 3 manager theo email mới
        $managers = User::whereIn('email', [
            'manager1@devfoods.com',
            'manager2@devfoods.com',
            'manager3@devfoods.com',
        ])->get()->keyBy('email');

        // Tạo 3 chi nhánh cố định, mỗi chi nhánh gán đúng 1 manager
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
                'branch_code' => 'HN001',
                'manager_user_id' => $managers['manager1@devfoods.com']->id ?? null,
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
                'manager_user_id' => $managers['manager2@devfoods.com']->id ?? null,
            ],
            [
                'name' => 'Chi nhánh Đà Nẵng',
                'address' => '789 Nguyễn Văn Linh, Hải Châu, Đà Nẵng',
                'phone' => '0236789012',
                'email' => 'danang@devfoods.com',
                'latitude' => 16.0544,
                'longitude' => 108.2022,
                'opening_hour' => '08:00',
                'closing_hour' => '22:00',
                'branch_code' => 'DN003',
                'manager_user_id' => $managers['manager3@devfoods.com']->id ?? null,
            ],
        ];

        foreach ($fixedBranches as $branchData) {
            Branch::create(array_merge($branchData, [
                'active' => true,
                'balance' => rand(5000, 10000),
                'rating' => rand(40, 50) / 10,
                'reliability_score' => rand(90, 100),
            ]));
        }
    }
}
