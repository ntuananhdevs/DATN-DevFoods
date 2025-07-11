<?php

namespace App\Listeners\Order;

use App\Events\Order\OrderConfirmed;
use App\Jobs\FindDriverForOrderJob;

class FindDriverForOrder
{
    /**
     * Handle the event.
     */
    public function handle(OrderConfirmed $event)
    {
        // Dispatch job để tìm tài xế phù hợp
        FindDriverForOrderJob::dispatch($event->order);
    }
} 