<?php

namespace App\Observers;

use App\Events\Customer\ToppingStockUpdated;
use App\Models\ToppingStock;

class ToppingStockObserver
{
    /**
     * Handle the ToppingStock "created" event.
     */
    public function created(ToppingStock $toppingStock): void
    {
        $this->broadcastStockUpdate($toppingStock);
    }

    /**
     * Handle the ToppingStock "updated" event.
     */
    public function updated(ToppingStock $toppingStock): void
    {
        $this->broadcastStockUpdate($toppingStock);
    }

    /**
     * Handle the ToppingStock "deleted" event.
     */
    public function deleted(ToppingStock $toppingStock): void
    {
        $this->broadcastStockUpdate($toppingStock);
    }

    /**
     * Broadcast stock update event
     */
    private function broadcastStockUpdate(ToppingStock $toppingStock): void
    {
        event(new ToppingStockUpdated(
            $toppingStock->branch_id,
            $toppingStock->topping_id,
            $toppingStock->stock_quantity
        ));
    }
} 