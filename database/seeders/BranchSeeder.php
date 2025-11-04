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
                'name' => 'Chi nhánh Đống Đa',
                'address' => '123 Đường Láng, Đống Đa, Hà Nội',
                'phone' => '0243123456',
                'email' => 'hanoi1@devfoods.com',
                'latitude' => 21.0278,
                'longitude' => 105.8342, // Đống Đa (Trung tâm)
                'opening_hour' => '07:30',
                'closing_hour' => '22:30',
                'branch_code' => 'HN001',
                'manager_user_id' => $managers['manager1@devfoods.com']->id ?? null,
            ],
            [
                'name' => 'Chi nhánh Cầu Giấy',
                'address' => 'Cầu Giấy, Hà Nội',
                'phone' => '0243123457',
                'email' => 'hanoi2@devfoods.com',
                'latitude' => 21.0362,
                'longitude' => 105.7829, // Cầu Giấy (Phía Tây)
                'opening_hour' => '07:30',
                'closing_hour' => '22:30',
                'branch_code' => 'HN002',
                'manager_user_id' => $managers['manager2@devfoods.com']->id ?? null,
            ],
            [
                'name' => 'Chi nhánh Hà Đông',
                'address' => 'Hà Đông, Hà Nội',
                'phone' => '0243123458',
                'email' => 'hanoi3@devfoods.com',
                'latitude' => 20.9726,
                'longitude' => 105.7772, // Hà Đông (Phía Nam)
                'opening_hour' => '07:30',
                'closing_hour' => '22:30',
                'branch_code' => 'HN003',
                'manager_user_id' => $managers['manager3@devfoods.com']->id ?? null,
            ],
        ];

        foreach ($fixedBranches as $branchData) {
            // Idempotent seeding: cập nhật nếu đã tồn tại theo branch_code, tạo mới nếu chưa có
            Branch::updateOrCreate(
                ['branch_code' => $branchData['branch_code']],
                array_merge($branchData, [
                    'active' => true,
                    'balance' => rand(5000, 10000),
                    'rating' => rand(40, 50) / 10,
                    'reliability_score' => rand(90, 100),
                ])
            );
        }
    }
}
