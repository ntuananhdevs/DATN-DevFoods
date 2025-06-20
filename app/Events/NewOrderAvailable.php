<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log; // <-- THÊM DÒNG NÀY

class NewOrderAvailable implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    public $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function broadcastOn(): array
    {
        return [new Channel('available-orders')];
    }

    public function broadcastAs()
    {
        return 'new-order-event';
    }

    /**
     * THÊM PHƯƠNG THỨC NÀY VÀO
     * Định dạng dữ liệu sẽ được gửi đi.
     */
    public function broadcastWith(): array
    {
        // Ghi log ngay trước khi gửi đi
        Log::info('Broadcasting NewOrderAvailable event for order ID: ' . $this->order->id);
        
        return ['order' => $this->order->toArray()];
    }
}