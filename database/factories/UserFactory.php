<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
<<<<<<< HEAD
use App\Models\Role;
=======
>>>>>>> 9b9f675225f77e5568d3f1dd1d4d67da2c3ab1f6

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => bcrypt('password'),
            // 'role_id' => Role::inRandomOrder()->first()->id, // không cần nếu truyền từ ngoài
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
<<<<<<< HEAD
        return $this->state(fn(array $attributes) => [
=======
        return $this->state(fn (array $attributes) => [
>>>>>>> 9b9f675225f77e5568d3f1dd1d4d67da2c3ab1f6
            'email_verified_at' => null,
        ]);
    }
}
