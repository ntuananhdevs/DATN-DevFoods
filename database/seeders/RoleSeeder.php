<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'name' => 'admin',
                'permissions' => ['*'],
            ],
            [
                'name' => 'manager',
                'permissions' => ['create', 'edit', 'view'],
            ],
            [
                'name' => 'staff',
                'permissions' => ['view'],
            ],
            [
                'name' => 'customer',
                'permissions' => ['view'],
            ],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(
                ['name' => $role['name']],
                ['permissions' => $role['permissions']]
            );
        }
    }
}
