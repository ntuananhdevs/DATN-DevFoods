<?php

namespace App\Events\Customer;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ToppingPriceUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $toppingId;
    public $toppingName;
    public $oldPrice;
    public $newPrice;
    public $branchId;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($toppingId, $toppingName, $oldPrice, $newPrice, $branchId)
    {
        $this->toppingId = $toppingId;
        $this->toppingName = $toppingName;
        $this->oldPrice = $oldPrice;
        $this->newPrice = $newPrice;
        $this->branchId = $branchId;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        \Log::debug('Broadcasting topping price update event on channel: branch-stock-channel', [
            'toppingId' => $this->toppingId,
            'toppingName' => $this->toppingName,
            'oldPrice' => $this->oldPrice,
            'newPrice' => $this->newPrice,
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
        return 'topping-price-updated';
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith(): array
    {
        return [
            'toppingId' => $this->toppingId,
            'toppingName' => $this->toppingName,
            'oldPrice' => $this->oldPrice,
            'newPrice' => $this->newPrice,
            'branchId' => $this->branchId
        ];
    }
} 