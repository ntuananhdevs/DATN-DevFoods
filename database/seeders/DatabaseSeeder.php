<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\Category;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Gọi RoleSeeder
        $this->call(RoleSeeder::class);

        // Lấy danh sách role_id hiện có
        $roleIds = Role::pluck('id')->toArray();

        // Tạo 10 user mẫu và gán role_id ngẫu nhiên từ các role đã có
        User::factory(10)->create([
            'role_id' => function () use ($roleIds) {
                return $roleIds[array_rand($roleIds)];
            },
        ]);

        // Tạo user test cụ thể nếu muốn
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'role_id' => $roleIds[array_rand($roleIds)],
        ]);

        Category::factory(10)->create();
    }
}
