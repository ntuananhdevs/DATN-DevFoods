<?php

namespace App\Observers;

use App\Models\ComboBranchStock;
use App\Events\Customer\ComboStockUpdated;
use Illuminate\Support\Facades\Log;

class ComboBranchStockObserver
{
    /**
     * Handle the ComboBranchStock "created" event.
     */
    public function created(ComboBranchStock $comboBranchStock): void
    {
        $this->broadcastStockUpdate($comboBranchStock);
    }

    /**
     * Handle the ComboBranchStock "updated" event.
     */
    public function updated(ComboBranchStock $comboBranchStock): void
    {
        $this->broadcastStockUpdate($comboBranchStock);
    }

    /**
     * Handle the ComboBranchStock "deleted" event.
     */
    public function deleted(ComboBranchStock $comboBranchStock): void
    {
        $this->broadcastStockUpdate($comboBranchStock);
    }

    /**
     * Broadcast stock update event
     */
    private function broadcastStockUpdate(ComboBranchStock $comboBranchStock): void
    {
        try {
            $stockQuantity = $comboBranchStock->quantity; // Sửa lại đúng tên cột
            $comboId = $comboBranchStock->combo_id;
            $branchId = $comboBranchStock->branch_id;

            Log::info('Combo stock update detected', [
                'branch_id' => $branchId,
                'combo_id' => $comboId,
                'stock_quantity' => $stockQuantity
            ]);

            event(new ComboStockUpdated($branchId, $comboId, $stockQuantity));
        } catch (\Exception $e) {
            Log::error('Error broadcasting combo stock update', [
                'error' => $e->getMessage(),
                'combo_branch_stock_id' => $comboBranchStock->id
            ]);
        }
    }
} 