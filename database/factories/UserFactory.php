<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\UserRank;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_name' => fake()->unique()->userName(),
            'full_name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'avatar' => 'avatars/default.jpg',
            'google_id' => null,
            'balance' => fake()->randomFloat(2, 0, 1000),
            'active' => true,
            'email_verified_at' => now(),
            'user_rank_id' => UserRank::inRandomOrder()->first()?->id ?? 1, // Use existing rank or default to 1
            'birthday' => fake()->optional()->date(),
            'gender' => fake()->optional()->randomElement(['male', 'female', 'other']),
            'total_spending' => fake()->randomFloat(2, 0, 10000),
            'total_orders' => fake()->numberBetween(0, 50),
            'rank_updated_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn(array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
    
    public function inactive(): static
    {
        return $this->state(fn(array $attributes) => [
            'active' => false,
        ]);
    }

    public function withGoogleId(): static
    {
        return $this->state(fn(array $attributes) => [
            'google_id' => Str::random(21),
        ]);
    }
}