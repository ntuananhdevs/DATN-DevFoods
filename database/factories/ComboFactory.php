<?php

namespace Database\Factories;

use App\Models\Combo;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Combo>
 */
class ComboFactory extends Factory
{
    protected $model = Combo::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->words(2, true),
            'description' => $this->faker->sentence(),
            'price' => $this->faker->numberBetween(100000, 500000),
            'image' => 'https://via.placeholder.com/640x480.png/00ff55?text=food+' . $this->faker->word(),
            'active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
} 