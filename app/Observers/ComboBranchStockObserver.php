<?php

namespace App\Observers;

use App\Models\ComboBranchStock;
use App\Events\Customer\ComboBranchStockUpdated;

class ComboBranchStockObserver
{
    /**
     * Handle the ComboBranchStock "updated" event.
     */
    public function updated(ComboBranchStock $stock)
    {
        // Chỉ phát event nếu quantity thay đổi
        if ($stock->isDirty('quantity')) {
            event(new ComboBranchStockUpdated($stock->combo_id, $stock->branch_id, $stock->quantity));
        }
    }
} 