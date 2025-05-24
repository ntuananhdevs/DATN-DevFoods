<?php

namespace Database\Factories;

use App\Models\Variant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Variant>
 */
class VariantFactory extends Factory
{
    protected $model = Variant::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->randomElement(['Size', 'Topping', 'Spice Level', 'Temperature', 'Sugar Level']),
            'description' => $this->faker->sentence(),
            'active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
} 