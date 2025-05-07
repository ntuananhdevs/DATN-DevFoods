<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create 3 fake roles
        Role::factory()->count(3)->create()->each(function ($role) {
            // Create 10 fake users for each role
            User::factory()->count(10)->create([
                'role_id' => $role->id
            ]);
        });
    }
}