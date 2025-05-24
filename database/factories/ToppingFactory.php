<?php

namespace Database\Factories;

use App\Models\Topping;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Topping>
 */
class ToppingFactory extends Factory
{
    protected $model = Topping::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->word(),
            'price' => $this->faker->numberBetween(5000, 50000),
            'active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
} 