<?php

namespace Database\Factories;

use App\Models\BranchStock;
use App\Models\ProductVariant;
use App\Models\Branch;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BranchStock>
 */
class BranchStockFactory extends Factory
{
    protected $model = BranchStock::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'branch_id' => Branch::factory(),
            'product_variant_id' => ProductVariant::factory(),
            'stock_quantity' => $this->faker->numberBetween(0, 100),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
