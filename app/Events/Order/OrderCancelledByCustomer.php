<?php

namespace App\Events\Order;

use App\Models\Order;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderCancelledByCustomer implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    public $orderId;
    public $driverId;

    public function __construct($orderId, $driverId)
    {
        $this->orderId = $orderId;
        $this->driverId = $driverId;
    }

    public function broadcastOn(): array
    {
        return [new PrivateChannel('driver.' . $this->driverId)];
    }

    public function broadcastAs(): string
    {
        return 'order-cancelled-event';
    }

    public function broadcastWith(): array
    {
        return [
            'order_id' => $this->orderId,
        ];
    }
}
