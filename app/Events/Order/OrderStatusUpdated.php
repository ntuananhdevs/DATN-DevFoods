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
    public string $oldStatus;
    public string $newStatus;

    /**
     * Create a new event instance.
     */
    public function __construct(Order $order, string $oldStatus = null, string $newStatus = null)
    {
        $this->order = $order;
        $this->oldStatus = $oldStatus ?? $order->getOriginal('status') ?? $order->status;
        $this->newStatus = $newStatus ?? $order->status;

        // Lấy trạng thái mới
        $status = $this->newStatus;
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
        return [
            // Private channel for the specific order
            new PrivateChannel('order.' . $this->order->id),
            // Public channel for admin real-time updates
            new Channel('order-status-updates'),
            // Branch specific channel
            new Channel('branch-orders-' . $this->order->branch_id),
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
            'order_id' => $this->order->id,
            'order_code' => $this->order->order_code,
            'old_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
            'status' => $this->newStatus, // For backward compatibility
            'status_text' => $this->order->statusText,
            'payment_status' => $this->order->payment_status,
            'payment_status_text' => $this->order->paymentStatusText,
            'actual_delivery_time' => optional($this->order->actual_delivery_time)->format('H:i - d/m/Y'),
            'branch_id' => $this->order->branch_id,
            'branch_name' => optional($this->order->branch)->name,
            'customer_name' => optional($this->order->customer)->name,
            'total_amount' => $this->order->total_amount,
            'updated_at' => $this->order->updated_at->format('H:i - d/m/Y'),
        ];
    }
}
