<?php

namespace Database\Factories;

use App\Models\ProductVariant;
use App\Models\ProductVariantDetail;
use App\Models\VariantValue;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductVariantDetail>
 */
class ProductVariantDetailFactory extends Factory
{
    protected $model = ProductVariantDetail::class;

    public function definition(): array
    {
        return [
            'product_variant_id' => ProductVariant::factory(),
            'variant_value_id' => VariantValue::factory(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
} 