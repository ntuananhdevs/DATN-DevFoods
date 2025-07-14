<?php

namespace App\Events\Customer;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewNotification implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $customerId;
    public $message;

    public function __construct($customerId, $message)
    {
        $this->customerId = $customerId;
        $this->message = $message;
    }

    public function broadcastOn()
    {
        return ['customer.' . $this->customerId . '.notifications'];
    }

    public function broadcastAs()
    {
        return 'new-message';
    }
}
