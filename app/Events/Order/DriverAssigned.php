<?php

namespace App\Events\Order;

use App\Models\Order;
use App\Models\Driver;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DriverAssigned implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    public $order;
    public $driver;

    public function __construct(Order $order, Driver $driver)
    {
        $this->order = $order;
        // Lấy driver từ order
        $this->driver = $order->driver;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('driver.' . $this->order->driver_id);
    }

    public function broadcastAs(): string
    {
        return 'DriverAssigned';
    }

    public function broadcastWith(): array
    {
        return [
            'order' => [
                'id' => $this->order->id,
                'order_code' => $this->order->order_code,
                'delivery_address' => $this->order->delivery_address,
                'status' => $this->order->status,
            ]
        ];
    }
}
