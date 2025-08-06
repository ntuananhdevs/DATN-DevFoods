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
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class OrderStatusUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Order $order;
    public $branchId;
    public $driverId;
    public $isCancelledByCustomer = false;
    public string $oldStatus;
    public string $newStatus;

    /**
     * Create a new event instance.
     */
    public function __construct(Order $order, $isCancelledByCustomer = false, string $oldStatus = null, string $newStatus = null)
    {
        $this->order = $order;
        $this->oldStatus = $oldStatus ?? $order->getOriginal('status') ?? $order->status;
        $this->newStatus = $newStatus ?? $order->status;
        $this->branchId = $order->branch_id;
        $this->driverId = $order->driver_id;
        $this->isCancelledByCustomer = $isCancelledByCustomer;

        if ($isCancelledByCustomer) {
            Log::info('OrderStatusUpdated (cancelled by customer) event constructed', [
                'order_id' => $order->id,
                'order_code' => $order->order_code,
                'branch_id' => $this->branchId,
                'driver_id' => $this->driverId,
                'status' => $order->status
            ]);
        }

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
        if ($this->isCancelledByCustomer) {
            return 'Đơn hàng đã bị hủy bởi khách hàng';
        }
        
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
        $channels = [
            // Original channel for specific order page
            new PrivateChannel('order.' . $this->order->id),
            // Branch channel for real-time updates
            new Channel('branch-orders-channel'),
            // Admin channel for real-time updates - Admin cần nhận tất cả cập nhật
            new Channel('admin-orders-channel'),
            // General order status updates channel for JavaScript listeners
            new Channel('order-status-updates'),
        ];
        
        // Add customer-specific channel for global notifications
        if ($this->order->customer_id) {
            $channels[] = new PrivateChannel('customer.' . $this->order->customer_id . '.orders');
        }
        
        // Add driver-specific channel if order is cancelled by customer
        if ($this->isCancelledByCustomer && $this->driverId) {
            $channels[] = new PrivateChannel('driver.' . $this->driverId);
        }
        
        if ($this->isCancelledByCustomer) {
            Log::info('OrderStatusUpdated (cancelled by customer) broadcasting on channels', [
                'channels' => array_map(function($channel) {
                    return $channel->name;
                }, $channels),
                'order_id' => $this->order->id,
                'branch_id' => $this->branchId
            ]);
        }
        
        Log::info('OrderStatusUpdated broadcasting on channels', [
            'channels' => array_map(function($channel) {
                return $channel->name;
            }, $channels),
            'order_id' => $this->order->id,
            'old_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
            'branch_id' => $this->branchId
        ]);
        
        return $channels;
    }

    /**
     * The name of the event to broadcast.
     */
    public function broadcastAs(): string
    {
        if ($this->isCancelledByCustomer) {
            // Broadcast as order-cancelled-by-customer for branch channel
            // and as order-cancelled-event for driver channel
            return $this->driverId ? 'order-cancelled-event' : 'order-cancelled-by-customer';
        }
        
        return 'order-status-updated'; // Consistent event name for all channelszz
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
            'order' => [
                'id' => $this->order->id,
                'status' => $this->order->status,
                'customer_id' => $this->order->customer_id,
                'branch_id' => $this->order->branch_id,
                'order_code' => $this->order->order_code,
            ],
            'order_id' => $this->order->id, // Add for backward compatibility
            'old_status' => $this->oldStatus,
            'status' => $this->order->status,
            'status_text' => $this->order->status_text,
            'status_color' => $this->order->status_color, // Gửi cả object/mảng màu sắc
            'status_icon' => $this->order->status_icon,   // Gửi cả icon để cập nhật giao diện
            'actual_delivery_time' => optional($this->order->actual_delivery_time)->format('H:i - d/m/Y'),
            'branch_id' => $this->order->branch_id,
            'branch_name' => optional($this->order->branch)->name,
            'customer_name' => optional($this->order->customer)->name,
            'total_amount' => $this->order->total_amount,
            'updated_at' => $this->order->updated_at->format('H:i - d/m/Y'),
        ];
    }
}
