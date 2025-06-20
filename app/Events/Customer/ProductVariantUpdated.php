<?php

namespace App\Events\Customer;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProductVariantUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $productId;
    public $action; // 'created', 'updated', 'deleted'
    public $variantData;
    public $branchId;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($productId, $action, $variantData, $branchId)
    {
        $this->productId = $productId;
        $this->action = $action;
        $this->variantData = $variantData;
        $this->branchId = $branchId;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        \Log::debug('Broadcasting product variant update event on channel: branch-stock-channel', [
            'productId' => $this->productId,
            'action' => $this->action,
            'variantData' => $this->variantData,
            'branchId' => $this->branchId,
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
        return 'product-variant-updated';
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith(): array
    {
        return [
            'productId' => $this->productId,
            'action' => $this->action,
            'variantData' => $this->variantData,
            'branchId' => $this->branchId
        ];
    }
} 