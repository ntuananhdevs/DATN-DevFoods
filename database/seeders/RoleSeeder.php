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
                'name' => 'spadmin',
                'permissions' => ['*'],
            ],
            [
                'name' => 'admin',
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
            [
                'name' => 'driver',
                'permissions' => ['view', 'driver_actions'],
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
