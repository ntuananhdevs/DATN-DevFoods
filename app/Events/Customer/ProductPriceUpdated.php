<?php

namespace App\Events\Customer;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProductPriceUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $productId;
    public $basePrice;
    public $branchId;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($productId, $basePrice, $branchId)
    {
        $this->productId = $productId;
        $this->basePrice = $basePrice;
        $this->branchId = $branchId;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        \Log::debug('Broadcasting product price update event on channel: branch-stock-channel', [
            'productId' => $this->productId,
            'basePrice' => $this->basePrice,
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
        return 'product-price-updated';
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
            'basePrice' => $this->basePrice,
            'branchId' => $this->branchId
        ];
    }
} 