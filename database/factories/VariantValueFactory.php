<?php

namespace Database\Factories;

use App\Models\Variant;
use App\Models\VariantValue;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\VariantValue>
 */
class VariantValueFactory extends Factory
{
    protected $model = VariantValue::class;

    public function definition(): array
    {
        $variant = Variant::factory()->create();
        
        $values = [
            'Size' => ['Small', 'Medium', 'Large', 'Extra Large'],
            'Topping' => ['Extra Cheese', 'Extra Meat', 'Extra Vegetables', 'No Topping'],
            'Spice Level' => ['Mild', 'Medium', 'Hot', 'Extra Hot'],
            'Temperature' => ['Hot', 'Cold', 'Warm'],
            'Sugar Level' => ['No Sugar', 'Less Sugar', 'Normal', 'Extra Sugar']
        ];

        return [
            'variant_id' => $variant->id,
            'value' => $this->faker->randomElement($values[$variant->name]),
            'price_adjustment' => $this->faker->numberBetween(-10000, 20000),
            'active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
} 