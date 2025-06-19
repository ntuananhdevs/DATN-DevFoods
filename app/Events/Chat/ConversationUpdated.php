<?php

namespace App\Events\Chat;

use App\Models\Conversation;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ConversationUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $conversation;
    public $updateType;

    public function __construct(Conversation $conversation, $updateType = 'status_changed')
    {
        $this->conversation = $conversation;
        $this->updateType = $updateType;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn()
    {
        $channels = [
            new PrivateChannel('chat.' . $this->conversation->id),
        ];

        // Broadcast to admin channel
        $channels[] = new PrivateChannel('admin.conversations');

        // Broadcast to branch channel if assigned
        if ($this->conversation->branch_id) {
            $channels[] = new PrivateChannel('branch.' . $this->conversation->branch_id . '.conversations');
        }

        return $channels;
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith()
    {
        // Lấy message cuối cùng (nếu có)
        $lastMessage = $this->conversation->messages()->latest('sent_at')->first();

        return [
            'conversation' => [
                'id' => $this->conversation->id,
                'status' => $this->conversation->status,
                'branch_id' => $this->conversation->branch_id,
                'customer' => $this->conversation->customer ? [
                    'id' => $this->conversation->customer->id,
                    'full_name' => $this->conversation->customer->full_name,
                    'email' => $this->conversation->customer->email,
                ] : null,
                'assigned_to' => $this->conversation->assigned_to,
                'updated_at' => $this->conversation->updated_at,
            ],
            'last_message' => $lastMessage ? [
                'id' => $lastMessage->id,
                'conversation_id' => $lastMessage->conversation_id,
                'sender_id' => $lastMessage->sender_id,
                'message' => $lastMessage->message,
                'created_at' => $lastMessage->created_at,
                'sender' => $lastMessage->sender ? [
                    'id' => $lastMessage->sender->id,
                    'full_name' => $lastMessage->sender->full_name,
                ] : null,
            ] : null,
            'update_type' => $this->updateType,
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs()
    {
        return 'conversation.updated';
    }
}
