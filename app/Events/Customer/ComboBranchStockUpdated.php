<?php

namespace App\Events\Customer;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ComboBranchStockUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $combo_id;
    public $branch_id;
    public $quantity;

    /**
     * Create a new event instance.
     */
    public function __construct($combo_id, $branch_id, $quantity)
    {
        $this->combo_id = $combo_id;
        $this->branch_id = $branch_id;
        $this->quantity = $quantity;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn()
    {
        return new Channel('combo-branch-stock-channel');
    }

    public function broadcastWith()
    {
        return [
            'combo_id' => $this->combo_id,
            'branch_id' => $this->branch_id,
            'quantity' => $this->quantity,
        ];
    }

    public function broadcastAs()
    {
        return 'combo-branch-stock-updated';
    }
} 