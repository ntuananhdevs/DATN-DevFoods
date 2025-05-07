<?php

namespace Database\Factories;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition()
    {
        return [
            'role_id' => Role::factory(), // Assuming you have a Role model
            'user_name' => $this->faker->userName,
            'full_name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'phone' => $this->faker->phoneNumber,
            'avatar' => $this->faker->imageUrl(),
            'google_id' => $this->faker->uuid,
            'balance' => $this->faker->randomFloat(2, 0, 1000),
            'active' => $this->faker->boolean,
            'email_verified_at' => $this->faker->dateTimeBetween('-1 years', 'now'),
            'password' => bcrypt('12345678'), // Default password
            'remember_token' => Str::random(10),
        ];
    }
}
