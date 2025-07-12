<?php

namespace App\Events\Order;

use App\Models\Order;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewOrderAvailable implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    public Order $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function broadcastOn(): array
    {
        // Gửi đến kênh riêng tư 'drivers'
        return [new PrivateChannel('drivers')];
    }

    public function broadcastAs(): string
    {
        return 'new-order-event'; // Tên sự kiện để JS lắng nghe
    }

    public function broadcastWith(): array
    {
        // Gửi đi các dữ liệu cần thiết để hiển thị trên dashboard của tài xế
        return [
            'order' => [
                'id' => $this->order->id,
                'order_code' => $this->order->order_code ?? $this->order->id,
                'delivery_address' => $this->order->delivery_address,
                'total_amount' => $this->order->total_amount,
            ]
        ];
    }
}
