<?php

namespace App\Events\Branch;

use App\Models\Order;
use App\Models\Driver;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DriverFound implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $order;
    public $driver;
    public $branchId;

    /**
     * Create a new event instance.
     */
    public function __construct(Order $order, Driver $driver)
    {
        $this->order = $order;
        $this->driver = $driver;
        $this->branchId = $order->branch_id;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn()
    {
        return [
            new PrivateChannel("branch.{$this->branchId}")
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'driver-found';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'order' => [
                'id' => $this->order->id,
                'code' => $this->order->order_code,
                'status' => 'driver_found',
                'driver' => [
                    'id' => $this->driver->id,
                    'name' => $this->driver->name,
                    'phone' => $this->driver->phone,
                    'avatar' => $this->driver->avatar
                ]
            ],
            'branch_id' => $this->branchId,
            'timestamp' => now()->toISOString()
        ];
    }
} 