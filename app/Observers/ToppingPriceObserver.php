<?php

namespace App\Observers;

use App\Events\Customer\ToppingPriceUpdated;
use App\Models\Topping;
use App\Models\ToppingStock;
use Illuminate\Support\Facades\Log;

class ToppingPriceObserver
{
    /**
     * Handle the Topping "updated" event.
     */
    public function updated(Topping $topping): void
    {
        // Check if price has changed
        if ($topping->isDirty('price')) {
            $oldPrice = $topping->getOriginal('price');
            $newPrice = $topping->price;
            
            Log::info('Topping price updated', [
                'topping_id' => $topping->id,
                'topping_name' => $topping->name,
                'old_price' => $oldPrice,
                'new_price' => $newPrice
            ]);

            // Get all branches that have stock for this topping
            $branchIds = ToppingStock::where('topping_id', $topping->id)
                ->distinct()
                ->pluck('branch_id');

            // Broadcast topping price update event for each branch
            foreach ($branchIds as $branchId) {
                event(new ToppingPriceUpdated(
                    $topping->id,
                    $topping->name,
                    $oldPrice,
                    $newPrice,
                    $branchId
                ));
            }
        }
    }

    /**
     * Handle the Topping "created" event.
     */
    public function created(Topping $topping): void
    {
        Log::info('New topping created with price', [
            'topping_id' => $topping->id,
            'topping_name' => $topping->name,
            'price' => $topping->price
        ]);

        // Get all branches that have stock for this topping
        $branchIds = ToppingStock::where('topping_id', $topping->id)
            ->distinct()
            ->pluck('branch_id');

        // Broadcast topping price update event for each branch
        foreach ($branchIds as $branchId) {
            event(new ToppingPriceUpdated(
                $topping->id,
                $topping->name,
                0, // No old price for new toppings
                $topping->price,
                $branchId
            ));
        }
    }
} 