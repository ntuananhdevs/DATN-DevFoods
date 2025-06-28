<?php

namespace App\Events;

use App\Models\Order;
// SỬA Ở ĐÂY: Dùng PrivateChannel thay vì Channel
use Illuminate\Broadcasting\PrivateChannel; 
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

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
        // SỬA Ở ĐÂY: Gửi đến kênh riêng tư 'drivers'
        return [new PrivateChannel('drivers')];
    }

    public function broadcastAs(): string
    {
        return 'new-order-event';
    }

    public function broadcastWith(): array
    {
        Log::info('Broadcasting NewOrderAvailable event for order ID: ' . $this->order->id);
        
        // Giữ nguyên phần này, chỉ gửi dữ liệu cần thiết
        return [
            'order' => [
                'id' => $this->order->id,
                'order_code' => $this->order->order_code ?? $this->order->id, // Thêm mã đơn hàng cho dễ nhìn
                'delivery_address' => $this->order->delivery_address,
                'total_amount' => $this->order->total_amount, // Gửi thêm tổng tiền
                // Thêm các trường khác nếu cần
            ]
        ];
    }
}