<?php

namespace App\Events\Customer;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ComboStockUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $branchId;
    public $comboId;
    public $stockQuantity;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($branchId, $comboId, $stockQuantity)
    {
        $this->branchId = $branchId;
        $this->comboId = $comboId;
        $this->stockQuantity = $stockQuantity;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('combo-branch-stock-channel');
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'combo-stock-updated';
    }

    /**
     * The data to broadcast.
     */
    public function broadcastWith()
    {
        return [
            'branchId' => $this->branchId,
            'comboId' => $this->comboId,
            'stockQuantity' => $this->stockQuantity,
        ];
    }
} 