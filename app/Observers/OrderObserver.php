<?php

namespace App\Observers;

use App\Models\Order;
use App\Events\Branch\NewOrderReceived;
use Illuminate\Support\Facades\Log;

class OrderObserver
{
    /**
     * Handle the Order "created" event.
     */
    public function created(Order $order): void
    {
        try {
            // Broadcast new order event
            event(new NewOrderReceived($order));
            
            Log::info('New order created and broadcasted', [
                'order_id' => $order->id,
                'order_code' => $order->order_code,
                'branch_id' => $order->branch_id,
                'status' => $order->status
            ]);
        } catch (\Exception $e) {
            Log::error('Error broadcasting new order event', [
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
                event(new \App\Events\Branch\OrderStatusUpdated($order, $oldStatus, $newStatus));
                
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