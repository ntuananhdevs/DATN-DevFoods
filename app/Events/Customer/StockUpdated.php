<?php

namespace App\Events\Customer;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StockUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $branchId;
    public $productVariantId;
    public $stockQuantity;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($branchId, $productVariantId, $stockQuantity)
    {
        $this->branchId = $branchId;
        $this->productVariantId = $productVariantId;
        $this->stockQuantity = $stockQuantity;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        \Log::debug('Broadcasting stock update event on channel: branch-stock-channel', [
            'branchId' => $this->branchId,
            'productVariantId' => $this->productVariantId,
            'stockQuantity' => $this->stockQuantity,
            'event_name' => $this->broadcastAs(),
            'broadcast_data' => $this->broadcastWith()
        ]);

        // Log Pusher configuration
        \Log::debug('Pusher configuration:', [
            'app_id' => config('broadcasting.connections.pusher.app_id'),
            'key' => config('broadcasting.connections.pusher.key'),
            'secret' => config('broadcasting.connections.pusher.secret'),
            'cluster' => config('broadcasting.connections.pusher.options.cluster'),
            'driver' => config('broadcasting.default')
        ]);

        return new Channel('branch-stock-channel');
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        $eventName = 'stock-updated';
        \Log::debug('Broadcasting stock update event with name: ' . $eventName, [
            'event_class' => get_class($this),
            'broadcast_name' => $eventName
        ]);
        return $eventName;
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
            'productVariantId' => $this->productVariantId,
            'stockQuantity' => $this->stockQuantity
        ];
        \Log::debug('Broadcasting stock update event with data:', [
            'data' => $data,
            'event_name' => $this->broadcastAs(),
            'channel' => 'branch-stock-channel'
        ]);
        return $data;
    }
}