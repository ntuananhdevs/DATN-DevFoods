<?php

namespace App\Events\Customer;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class FavoriteUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $userId;
    public $productId;
    public $isFavorite;
    public $count;

    /**
     * Create a new event instance.
     */
    public function __construct($userId, $productId, $isFavorite, $count)
    {
        $this->userId = $userId;
        $this->productId = $productId;
        $this->isFavorite = $isFavorite;
        $this->count = $count;
        
        \Log::info('FavoriteUpdated event constructed', [
            'user_id' => $userId,
            'product_id' => $productId,
            'is_favorite' => $isFavorite,
            'count' => $count
        ]);
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        $channel = 'user-wishlist-channel.' . $this->userId;
        \Log::info('Broadcasting on channel', ['channel' => $channel]);
        return [
            new PrivateChannel($channel),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'favorite-updated';
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith(): array
    {
        $data = [
            'user_id' => $this->userId,
            'product_id' => $this->productId,
            'is_favorite' => $this->isFavorite,
            'count' => $this->count
        ];
        \Log::info('Broadcasting with data', $data);
        return $data;
    }
} 