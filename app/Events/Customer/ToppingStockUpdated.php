<?php

namespace App\Events\Customer;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ToppingStockUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $branchId;
    public $toppingId;
    public $stockQuantity;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($branchId, $toppingId, $stockQuantity)
    {
        $this->branchId = $branchId;
        $this->toppingId = $toppingId;
        $this->stockQuantity = $stockQuantity;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        \Log::debug('Broadcasting topping stock update event on channel: branch-stock-channel', [
            'branchId' => $this->branchId,
            'toppingId' => $this->toppingId,
            'stockQuantity' => $this->stockQuantity,
            'event_name' => $this->broadcastAs(),
            'broadcast_data' => $this->broadcastWith()
        ]);

        return new Channel('branch-stock-channel');
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'topping-stock-updated';
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith(): array
    {
        $data = [
            'branchId' => $this->branchId,
            'toppingId' => $this->toppingId,
            'stockQuantity' => $this->stockQuantity
        ];
        \Log::debug('Broadcasting topping stock update event with data:', [
            'data' => $data,
            'event_name' => $this->broadcastAs(),
            'channel' => 'branch-stock-channel'
        ]);
        return $data;
    }
} 