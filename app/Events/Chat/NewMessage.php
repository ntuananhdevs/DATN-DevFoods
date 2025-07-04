<?php

namespace App\Events\Chat;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class NewMessage implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $conversationId;

    public function __construct($message, $conversationId)
    {
        $this->message = $message;
        $this->conversationId = $conversationId;
    }

    public function broadcastOn()
    {
        $channels = [
            new Channel('chat.' . $this->conversationId),
            new Channel('admin.conversations'),
        ];

        // Nếu có branch_id thì broadcast cho branch
        $conversation = $this->message->conversation ?? null;
        if ($conversation && !empty($conversation->branch_id)) {
            $channels[] = new Channel('branch.' . $conversation->branch_id . '.conversations');
        }

        Log::info('[NewMessage] broadcastOn channels', [
            'channels' => array_map(function ($c) {
                return $c->name ?? (string)$c;
            }, $channels),
            'conversationId' => $this->conversationId,
        ]);

        return $channels;
    }

    public function broadcastAs()
    {
        return 'new-message';
    }

    public function broadcastWith()
    {
        Log::info('[NewMessage] broadcastWith', [
            'conversationId' => $this->conversationId,
            'message' => $this->message,
        ]);
        return [
            'message' => [
                'id' => $this->message->id,
                'conversation_id' => $this->message->conversation_id,
                'sender_id' => $this->message->sender_id,
                'message' => $this->message->message,
                'attachment' => $this->message->attachment ?? null,
                'attachment_type' => $this->message->attachment_type ?? null,
                'created_at' => $this->message->created_at,
                'sender' => [
                    'id' => $this->message->sender->id,
                    'full_name' => $this->message->sender->full_name,
                ],
                'conversation' => [
                    'id' => $this->message->conversation_id,
                ]
            ]
        ];
    }
}
