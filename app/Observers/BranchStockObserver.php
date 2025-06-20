<?php

namespace App\Observers;

use App\Models\BranchStock;
use App\Events\Customer\StockUpdated;
use Illuminate\Support\Facades\Log;

class BranchStockObserver
{
    /**
     * Handle the BranchStock "created" event.
     */
    public function created(BranchStock $branchStock): void
    {
        $this->broadcastStockUpdate($branchStock);
    }

    /**
     * Handle the BranchStock "updated" event.
     */
    public function updated(BranchStock $branchStock): void
    {
        $this->broadcastStockUpdate($branchStock);
    }

    /**
     * Handle the BranchStock "deleted" event.
     */
    public function deleted(BranchStock $branchStock): void
    {
        $this->broadcastStockUpdate($branchStock);
    }

    /**
     * Broadcast stock update event
     */
    private function broadcastStockUpdate(BranchStock $branchStock): void
    {
        try {
            // Get the current stock quantity
            $stockQuantity = $branchStock->stock_quantity;
            
            // Get the product variant ID
            $productVariantId = $branchStock->product_variant_id;
            
            // Get the branch ID
            $branchId = $branchStock->branch_id;

            // Log the stock update
            Log::info('Stock update detected', [
                'branch_id' => $branchId,
                'product_variant_id' => $productVariantId,
                'stock_quantity' => $stockQuantity
            ]);

            // Broadcast the stock update event
            event(new StockUpdated($branchId, $productVariantId, $stockQuantity));
        } catch (\Exception $e) {
            Log::error('Error broadcasting stock update', [
                'error' => $e->getMessage(),
                'branch_stock_id' => $branchStock->id
            ]);
        }
    }
} 