<?php

namespace App\Events\Order;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderCancelledByCustomer implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    public $orderId;

    public function __construct($orderId)
    {
        $this->orderId = $orderId;
    }

    public function broadcastOn(): array
    {
        // Gửi sự kiện này đến tất cả các tài xế
        return [new PrivateChannel('drivers')];
    }

    public function broadcastAs(): string
    {
        return 'order-cancelled-event';
    }

    public function broadcastWith(): array
    {
        return ['order_id' => $this->orderId];
    }
}
