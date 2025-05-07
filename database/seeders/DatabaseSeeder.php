<?php

namespace Database\Seeders;

<<<<<<< HEAD
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;

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
=======
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
<<<<<<< HEAD
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
>>>>>>> 9b9f675225f77e5568d3f1dd1d4d67da2c3ab1f6
        ]);
=======
        User::factory(10)->create();
>>>>>>> 7088f382164c37f9caf064e185d73a37f6df5de3
    }
}