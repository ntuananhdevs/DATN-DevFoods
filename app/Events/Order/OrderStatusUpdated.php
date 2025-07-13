<?php

namespace App\Events\Order;

use App\Notifications\OrderStatusNotification;
use App\Models\Branch;
use App\Models\User;
use App\Models\Driver;
use App\Models\Admin; // Nếu có
use App\Models\Order;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderStatusUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Order $order;

    /**
     * Create a new event instance.
     */
    public function __construct(Order $order)
    {
        $this->order = $order;

        // Lấy trạng thái mới
        $status = $order->status;
        $message = $this->getStatusMessage($status, $order);

        // Gửi cho branch
        $branch = Branch::find($order->branch_id);
        if ($branch) {
            $branch->notify(new OrderStatusNotification($order, $status, $message));
        }

        // Gửi cho admin (nếu có)
        // ...

        // Gửi cho customer
        if ($order->customer) {
            $order->customer->notify(new OrderStatusNotification($order, $status, $message));
        }

        // Gửi cho driver
        if ($order->driver) {
            $order->driver->notify(new OrderStatusNotification($order, $status, $message));
        }
    }

    protected function getStatusMessage($status, $order)
    {
        switch ($status) {
            case 'order_confirmed':
                return 'Đơn hàng đã được xác nhận';
            case 'driver_found':
                return 'Đã tìm thấy tài xế cho đơn hàng';
                // ... các trạng thái khác như hướng dẫn trước ...
            default:
                return 'Đơn hàng cập nhật trạng thái: ' . $status;
        }
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        // This makes the channel private, ensuring only the order owner can listen.
        // Make sure you have authentication for your broadcast channels configured.
        return [
            new PrivateChannel('order.' . $this->order->id),
        ];
    }

    /**
     * The name of the event to broadcast.
     */
    public function broadcastAs(): string
    {
        return 'OrderStatusUpdated'; // Explicitly name the event for the listener
    }

    /**
     * Get the data to broadcast.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        // We send only the necessary data to the frontend.
        return [
            'status' => $this->order->status,
            'status_text' => $this->order->status_text,
            'status_color' => $this->order->status_color, // Gửi cả object/mảng màu sắc
            'status_icon' => $this->order->status_icon,   // Gửi cả icon để cập nhật giao diện
            'actual_delivery_time' => optional($this->order->actual_delivery_time)->format('H:i - d/m/Y'),
        ];
    }
}
