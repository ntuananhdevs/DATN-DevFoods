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
use Illuminate\Support\Facades\Log;

class OrderStatusUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Order $order;
    public $branchId;
    public $driverId;
    public $isCancelledByCustomer = false;

    /**
     * Create a new event instance.
     */
    public function __construct(Order $order, $isCancelledByCustomer = false)
    {
        $this->order = $order;
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
        
        // Đối với kênh order.{id} và kênh customer.{id}.orders, sử dụng tên 'OrderStatusUpdated' để khớp với client
        if (request()->is('branch/*') || strpos(request()->path(), 'customer') !== false) {
            return 'OrderStatusUpdated';
        }
        
        return 'order-status-updated'; // Explicitly name the event for the listener
    }

    /**
     * Get the data to broadcast.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        if ($this->isCancelledByCustomer) {
            // Load relationships for cancelled order
            $this->order->load(['payment', 'orderItems', 'address', 'branch', 'cancellation']);
            
            // Calculate total items count
            $itemsCount = $this->order->orderItems->sum('quantity');
            
            $data = [
                'order' => [
                    'id' => $this->order->id,
                    'code' => $this->order->order_code,
                    'order_code' => $this->order->order_code,
                    'status' => $this->order->status,
                    'status_text' => $this->order->statusText,
                    'status_color' => $this->order->statusColor,
                    'customer_name' => $this->order->customerName,
                    'customer_phone' => $this->order->customerPhone,
                    'total_amount' => $this->order->total_amount,
                    'order_date' => $this->order->order_date,
                    'items_count' => $itemsCount,
                    'cancellation_reason' => $this->order->cancellation ? $this->order->cancellation->reason : null,
                    'customer' => $this->order->customer ? [
                        'id' => $this->order->customer->id,
                        'name' => $this->order->customer->name,
                        'phone' => $this->order->customer->phone,
                    ] : null,
                    'payment' => $this->order->payment ? [
                        'id' => $this->order->payment->id,
                        'method' => $this->order->payment->payment_method,
                        'payment_method' => $this->order->payment->payment_method,
                        'payment_status' => $this->order->payment->payment_status,
                        'payment_amount' => $this->order->payment->payment_amount,
                    ] : null,
                    'branch' => $this->order->branch ? [
                        'id' => $this->order->branch->id,
                        'name' => $this->order->branch->name,
                    ] : null,
                ],
                'branch_id' => $this->branchId,
            ];
            
            // Nếu đang broadcast cho driver, chỉ cần gửi order_id
            if ($this->driverId) {
                return ['order_id' => $this->order->id];
            }
            
            Log::info('OrderStatusUpdated (cancelled by customer) broadcast data', [
                'order_id' => $this->order->id,
                'branch_id' => $this->branchId
            ]);
            
            return $data;
        }
        
        // Default broadcast data for regular status updates
        // Load relationships for order
        $this->order->load(['payment', 'orderItems', 'address', 'branch']);
        
        // Calculate total items count
        $itemsCount = $this->order->orderItems->sum('quantity');
        
        return [
            'order' => [
                'id' => $this->order->id,
                'code' => $this->order->order_code,
                'order_code' => $this->order->order_code,
                'status' => $this->order->status,
                'status_text' => $this->order->statusText,
                'status_color' => $this->order->statusColor,
                'customer_id' => $this->order->customer_id,
                'branch_id' => $this->order->branch_id,
                'customer_name' => $this->order->customerName,
                'customer_phone' => $this->order->customerPhone,
                'total_amount' => $this->order->total_amount,
                'order_date' => $this->order->order_date,
                'items_count' => $itemsCount,
                'estimated_delivery_time' => $this->order->estimated_delivery_time,
                'actual_delivery_time' => $this->order->actual_delivery_time,
                'payment' => $this->order->payment ? [
                    'id' => $this->order->payment->id,
                    'method' => $this->order->payment->payment_method,
                    'payment_method' => $this->order->payment->payment_method,
                    'payment_status' => $this->order->payment->payment_status,
                    'payment_amount' => $this->order->payment->payment_amount,
                ] : null,
                'branch' => $this->order->branch ? [
                    'id' => $this->order->branch->id,
                    'name' => $this->order->branch->name,
                ] : null,
            ],
            'branch_id' => $this->order->branch_id,
            'status' => $this->order->status,
            'status_text' => $this->order->status_text,
            'status_color' => $this->order->status_color, // Gửi cả object/mảng màu sắc
            'status_icon' => $this->order->status_icon,   // Gửi cả icon để cập nhật giao diện
            'actual_delivery_time' => optional($this->order->actual_delivery_time)->format('H:i - d/m/Y'),
        ];
    }
}
