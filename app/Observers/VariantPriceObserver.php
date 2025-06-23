<?php

namespace App\Observers;

use App\Events\Customer\VariantPriceUpdated;
use App\Models\VariantValue;
use App\Models\BranchStock;
use Illuminate\Support\Facades\Log;

class VariantPriceObserver
{
    /**
     * Handle the VariantValue "updated" event.
     */
    public function updated(VariantValue $variantValue): void
    {
        // Check if price_adjustment has changed
        if ($variantValue->isDirty('price_adjustment')) {
            $oldPriceAdjustment = $variantValue->getOriginal('price_adjustment');
            $newPriceAdjustment = $variantValue->price_adjustment;
            
            Log::info('Variant price adjustment updated', [
                'variant_value_id' => $variantValue->id,
                'variant_value' => $variantValue->value,
                'attribute_id' => $variantValue->variant_attribute_id,
                'attribute_name' => $variantValue->attribute->name,
                'old_price_adjustment' => $oldPriceAdjustment,
                'new_price_adjustment' => $newPriceAdjustment
            ]);

            // Get all products that use this variant value
            $productIds = $variantValue->productVariants()
                ->with('product')
                ->get()
                ->pluck('product_id')
                ->unique();

            // Get all branches that have stock for products using this variant value
            $branchIds = BranchStock::whereHas('productVariant', function($query) use ($variantValue) {
                    $query->whereHas('variantValues', function($q) use ($variantValue) {
                        $q->where('variant_value_id', $variantValue->id);
                    });
                })
                ->distinct()
                ->pluck('branch_id');

            // Broadcast variant price update event for each branch
            foreach ($branchIds as $branchId) {
                // Get the first product ID (we'll broadcast for each product separately if needed)
                $productId = $productIds->first();
                
                if ($productId) {
                    event(new VariantPriceUpdated(
                        $productId,
                        $variantValue->id,
                        $variantValue->variant_attribute_id,
                        $variantValue->attribute->name,
                        $variantValue->value,
                        $oldPriceAdjustment,
                        $newPriceAdjustment,
                        $branchId
                    ));
                }
            }
        }
    }

    /**
     * Handle the VariantValue "created" event.
     */
    public function created(VariantValue $variantValue): void
    {
        Log::info('New variant value created with price adjustment', [
            'variant_value_id' => $variantValue->id,
            'variant_value' => $variantValue->value,
            'attribute_id' => $variantValue->variant_attribute_id,
            'attribute_name' => $variantValue->attribute->name,
            'price_adjustment' => $variantValue->price_adjustment
        ]);

        // Get all products that use this variant value
        $productIds = $variantValue->productVariants()
            ->with('product')
            ->get()
            ->pluck('product_id')
            ->unique();

        // Get all branches that have stock for products using this variant value
        $branchIds = BranchStock::whereHas('productVariant', function($query) use ($variantValue) {
                $query->whereHas('variantValues', function($q) use ($variantValue) {
                    $q->where('variant_value_id', $variantValue->id);
                });
            })
            ->distinct()
            ->pluck('branch_id');

        // Broadcast variant price update event for each branch
        foreach ($branchIds as $branchId) {
            $productId = $productIds->first();
            
            if ($productId) {
                event(new VariantPriceUpdated(
                    $productId,
                    $variantValue->id,
                    $variantValue->variant_attribute_id,
                    $variantValue->attribute->name,
                    $variantValue->value,
                    0, // No old price adjustment for new variants
                    $variantValue->price_adjustment,
                    $branchId
                ));
            }
        }
    }
} 