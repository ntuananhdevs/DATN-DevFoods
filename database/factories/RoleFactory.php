<?php

namespace Database\Factories;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class RoleFactory extends Factory
{
    protected $model = Role::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->jobTitle,
            'permissions' => json_encode($this->faker->randomElements(['create', 'read', 'update', 'delete'], 2)),
        ];
    }
    public function users()
    {
        return $this->hasMany(User::class);
    }
}