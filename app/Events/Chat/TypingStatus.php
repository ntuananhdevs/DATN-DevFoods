<?php

namespace App\Events\Chat;


use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Support\Facades\Log;

class TypingStatus implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels;

    public $conversationId;
    public $userId;
    public $isTyping;
    public $userType;
    public $userName;

    public function __construct($conversationId, $userId, $isTyping, $userType, $userName)
    {
        $this->conversationId = $conversationId;
        $this->userId = $userId;
        $this->isTyping = $isTyping;
        $this->userType = $userType;
        $this->userName = $userName;
    }

    public function broadcastOn()
    {
        Log::info('[EVENT] TypingStatus broadcastOn', [
            'conversationId' => $this->conversationId
        ]);
        return new PresenceChannel('chat.' . $this->conversationId);
    }

    public function broadcastWith()
    {
        Log::info('[EVENT] TypingStatus broadcastWith', [
            'data' => [
                'conversation_id' => $this->conversationId,
                'user_id' => $this->userId,
                'is_typing' => $this->isTyping,
                'user_type' => $this->userType,
                'user_name' => $this->userName,
            ]
        ]);
        return [
            'conversation_id' => $this->conversationId,
            'user_id' => $this->userId,
            'is_typing' => $this->isTyping,
            'user_type' => $this->userType,
            'user_name' => $this->userName,
        ];
    }

    public function broadcastAs()
    {
        Log::info('[EVENT] TypingStatus broadcastAs', [
            'event' => 'UserTyping'
        ]);
        return 'UserTyping';
    }
}
