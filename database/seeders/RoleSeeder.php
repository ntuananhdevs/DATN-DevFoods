<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        Role::insert([
            ['name' => 'admin', 'permissions' => json_encode(['*'])],
            ['name' => 'manager', 'permissions' => json_encode(['create', 'edit', 'view'])],
            ['name' => 'staff', 'permissions' => json_encode(['view'])],
            ['name' => 'customer', 'permissions' => json_encode(['view'])],
        ]);
    }
}
