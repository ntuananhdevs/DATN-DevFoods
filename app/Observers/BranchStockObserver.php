<?php

namespace App\Observers;

use App\Models\BranchStock;
use App\Events\Customer\StockUpdated;

class BranchStockObserver
{
    /**
     * Handle the BranchStock "created" event.
     *
     * @param  \App\Models\BranchStock  $branchStock
     * @return void
     */
    public function created(BranchStock $branchStock)
    {
        event(new StockUpdated(
            $branchStock->branch_id,
            $branchStock->product_variant_id,
            $branchStock->stock_quantity
        ));
    }

    /**
     * Handle the BranchStock "updated" event.
     *
     * @param  \App\Models\BranchStock  $branchStock
     * @return void
     */
    public function updated(BranchStock $branchStock)
    {
        if ($branchStock->isDirty('stock_quantity')) {
            event(new StockUpdated(
                $branchStock->branch_id,
                $branchStock->product_variant_id,
                $branchStock->stock_quantity
            ));
        }
    }
} 