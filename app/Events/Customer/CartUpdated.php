<!-- <?php

        // namespace App\Events\Customer;

        // use Illuminate\Broadcasting\Channel;
        // use Illuminate\Broadcasting\InteractsWithSockets;
        // use Illuminate\Broadcasting\PresenceChannel;
        // use Illuminate\Broadcasting\PrivateChannel;
        // use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
        // use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
        // use Illuminate\Foundation\Events\Dispatchable;
        // use Illuminate\Queue\SerializesModels;

        // class CartUpdated implements ShouldBroadcastNow
        // {
        //     use Dispatchable, InteractsWithSockets, SerializesModels;

        //     public $userId;
        //     public $count;

        //     /**
        //      * Create a new event instance.
        //      */
        //     public function __construct($userId, $count)
        //     {
        //         $this->userId = $userId;
        //         $this->count = $count;
        //     }

        //     /**
        //      * Get the channels the event should broadcast on.
        //      *
        //      * @return array<int, \Illuminate\Broadcasting\Channel>
        //      */
        //     public function broadcastOn(): array
        //     {
        //         return [
        //             new Channel('user-cart-channel.' . $this->userId),
        //         ];
        //     }

        //     /**
        //      * The event's broadcast name.
        //      */
        //     public function broadcastAs(): string
        //     {
        //         return 'cart-updated';
        //     }

        //     /**
        //      * Get the data to broadcast.
        //      *
        //      * @return array
        //      */
        //     public function broadcastWith(): array
        //     {
        //         return [
        //             'count' => $this->count
        //         ];
        //     }
        // }
