<?php

namespace App\Events;

use App\Models\Product;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProductUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $product;
    public $action; // 'created', 'updated', 'deleted'

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Product $product, $action)
    {
        $this->product = $product;
        $this->action = $action;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('products');
    }

    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs()
    {
        return 'product.' . $this->action;
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith()
    {
        return [
            'id' => $this->product->id,
            'name' => $this->product->name,
            'image' => $this->product->image,
            'category' => $this->product->category ? $this->product->category->name : 'N/A',
            'base_price' => $this->product->base_price,
            'stock' => $this->product->stock,
            'action' => $this->action
        ];
    }
}