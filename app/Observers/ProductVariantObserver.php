<?php

namespace App\Observers;

use App\Events\Customer\ProductVariantUpdated;
use App\Models\ProductVariant;
use App\Models\BranchStock;
use Illuminate\Support\Facades\Log;

class ProductVariantObserver
{
    /**
     * Handle the ProductVariant "created" event.
     */
    public function created(ProductVariant $productVariant): void
    {
        Log::info('New product variant created', [
            'product_variant_id' => $productVariant->id,
            'product_id' => $productVariant->product_id
        ]);

        // Load variant values for broadcasting
        $productVariant->load(['variantValues.attribute']);
        
        $this->broadcastVariantUpdate($productVariant, 'created');
    }

    /**
     * Handle the ProductVariant "updated" event.
     */
    public function updated(ProductVariant $productVariant): void
    {
        Log::info('Product variant updated', [
            'product_variant_id' => $productVariant->id,
            'product_id' => $productVariant->product_id
        ]);

        // Load variant values for broadcasting
        $productVariant->load(['variantValues.attribute']);
        
        $this->broadcastVariantUpdate($productVariant, 'updated');
    }

    /**
     * Handle the ProductVariant "deleted" event.
     */
    public function deleted(ProductVariant $productVariant): void
    {
        Log::info('Product variant deleted', [
            'product_variant_id' => $productVariant->id,
            'product_id' => $productVariant->product_id
        ]);

        // Load variant values before deletion for broadcasting
        $productVariant->load(['variantValues.attribute']);
        
        $this->broadcastVariantUpdate($productVariant, 'deleted');
    }

    /**
     * Broadcast variant update event
     */
    private function broadcastVariantUpdate(ProductVariant $productVariant, string $action): void
    {
        try {
            // Get variant data
            $variantData = [
                'id' => $productVariant->id,
                'product_id' => $productVariant->product_id,
                'active' => $productVariant->active,
                'variant_values' => $productVariant->variantValues->map(function($value) {
                    return [
                        'id' => $value->id,
                        'attribute_id' => $value->variant_attribute_id,
                        'attribute_name' => $value->attribute->name,
                        'value' => $value->value,
                        'price_adjustment' => $value->price_adjustment
                    ];
                })->toArray(),
                'price' => $productVariant->price,
                'variant_description' => $productVariant->variant_description
            ];

            Log::info('Broadcasting variant data', [
                'action' => $action,
                'variant_data' => $variantData
            ]);

            // Get all branches that have stock for this product variant
            $branchIds = BranchStock::where('product_variant_id', $productVariant->id)
                ->distinct()
                ->pluck('branch_id');

            // If no specific branch stocks, get branches that have any stock for this product
            if ($branchIds->isEmpty()) {
                $branchIds = BranchStock::whereHas('productVariant', function($query) use ($productVariant) {
                        $query->where('product_id', $productVariant->product_id);
                    })
                    ->distinct()
                    ->pluck('branch_id');
            }

            Log::info('Broadcasting to branches', [
                'branch_ids' => $branchIds->toArray()
            ]);

            // Broadcast variant update event for each branch
            foreach ($branchIds as $branchId) {
                event(new ProductVariantUpdated(
                    $productVariant->product_id,
                    $action,
                    $variantData,
                    $branchId
                ));
            }
        } catch (\Exception $e) {
            Log::error('Error broadcasting product variant update', [
                'error' => $e->getMessage(),
                'product_variant_id' => $productVariant->id
            ]);
        }
    }
} 