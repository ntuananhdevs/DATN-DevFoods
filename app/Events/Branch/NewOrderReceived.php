<?php

namespace App\Events\Branch;

use App\Models\Order;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewOrderReceived implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $order;
    public $branchId;

    /**
     * Create a new event instance.
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
        $this->branchId = $order->branch_id;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn()
    {
        return [
            new PrivateChannel('branch.' . $this->branchId . '.orders'),
            new Channel('branch-orders-channel')
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'new-order-received';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'order' => [
                'id' => $this->order->id,
                'order_code' => $this->order->order_code,
                'status' => $this->order->status,
                'status_text' => $this->order->statusText,
                'status_color' => $this->order->statusColor,
                'customer_name' => $this->order->customerName,
                'customer_phone' => $this->order->customerPhone,
                'total_amount' => $this->order->total_amount,
                'order_date' => $this->order->order_date,
                'estimated_delivery_time' => $this->order->estimated_delivery_time,
                'points_earned' => $this->order->points_earned,
                'notes' => $this->order->notes,
                'customer' => $this->order->customer ? [
                    'id' => $this->order->customer->id,
                    'name' => $this->order->customer->name,
                    'phone' => $this->order->customer->phone,
                    'orders_count' => $this->order->customer->orders()->count(),
                    'last_order_date' => $this->order->customer->orders()->latest()->first()?->order_date?->format('Y-m-d')
                ] : null,
                'payment' => $this->order->payment ? [
                    'method_name' => $this->order->payment->paymentMethod?->name
                ] : null
            ],
            'branch_id' => $this->branchId,
            'timestamp' => now()->toISOString()
        ];
    }
} 