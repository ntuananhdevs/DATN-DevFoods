<?php

namespace App\Observers;

use App\Models\Order;
use App\Events\Order\NewOrderReceived;
use App\Events\OrderStatusUpdated;
use Illuminate\Support\Facades\Log;

class OrderObserver
{
    /**
     * Handle the Order "created" event.
     */
    public function created(Order $order): void
    {
        try {
            // Tạm thời tắt event để tránh duplicate với API OrderController
            // event(new NewOrderReceived($order));
            
            Log::info('New order created (event disabled to avoid duplicate)', [
                'order_id' => $order->id,
                'order_code' => $order->order_code,
                'branch_id' => $order->branch_id,
                'status' => $order->status
            ]);
        } catch (\Exception $e) {
            Log::error('Error in order created observer', [
                'error' => $e->getMessage(),
                'order_id' => $order->id
            ]);
        }
    }

    /**
     * Handle the Order "updated" event.
     */
    public function updated(Order $order): void
    {
        // Only broadcast if status changed
        if ($order->wasChanged('status')) {
            try {
                $oldStatus = $order->getOriginal('status');
                $newStatus = $order->status;
                
                // Broadcast status update event
                event(new OrderStatusUpdated($order, $oldStatus, $newStatus));
                
                Log::info('Order status updated and broadcasted', [
                    'order_id' => $order->id,
                    'order_code' => $order->order_code,
                    'branch_id' => $order->branch_id,
                    'old_status' => $oldStatus,
                    'new_status' => $newStatus
                ]);
            } catch (\Exception $e) {
                Log::error('Error broadcasting order status update event', [
                    'error' => $e->getMessage(),
                    'order_id' => $order->id
                ]);
            }
        }
    }

    /**
     * Handle the Order "deleted" event.
     */
    public function deleted(Order $order): void
    {
        // Handle order deletion if needed
    }

    /**
     * Handle the Order "restored" event.
     */
    public function restored(Order $order): void
    {
        // Handle order restoration if needed
    }

    /**
     * Handle the Order "force deleted" event.
     */
    public function forceDeleted(Order $order): void
    {
        // Handle force deletion if needed
    }
} 