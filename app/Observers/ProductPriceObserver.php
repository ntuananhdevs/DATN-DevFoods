<?php

namespace App\Observers;

use App\Events\Customer\ProductPriceUpdated;
use App\Models\Product;
use App\Models\BranchStock;
use Illuminate\Support\Facades\Log;

class ProductPriceObserver
{
    /**
     * Handle the Product "updated" event.
     */
    public function updated(Product $product): void
    {
        // Check if base_price has changed
        if ($product->isDirty('base_price')) {
            Log::info('Product base price updated', [
                'product_id' => $product->id,
                'old_price' => $product->getOriginal('base_price'),
                'new_price' => $product->base_price
            ]);

            // Get all branches that have this product through BranchStock
            $branchIds = BranchStock::whereHas('productVariant', function($query) use ($product) {
                    $query->where('product_id', $product->id);
                })
                ->distinct()
                ->pluck('branch_id');

            // Broadcast price update event for each branch
            foreach ($branchIds as $branchId) {
                event(new ProductPriceUpdated(
                    $product->id,
                    $product->base_price,
                    $branchId
                ));
            }
        }
    }
} 