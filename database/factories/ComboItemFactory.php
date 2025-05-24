<?php

namespace Database\Factories;

use App\Models\Combo;
use App\Models\ComboItem;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ComboItem>
 */
class ComboItemFactory extends Factory
{
    protected $model = ComboItem::class;

    public function definition(): array
    {
        return [
            'combo_id' => Combo::factory(),
            'product_id' => Product::factory(),
            'quantity' => $this->faker->numberBetween(1, 3),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
} 