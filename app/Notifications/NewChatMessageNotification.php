<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class NewChatMessageNotification extends Notification
{
    use Queueable;

    protected $message;

    public function __construct($message)
    {
        $this->message = $message;
    }

    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'message' => 'Tin nhắn mới từ ' . ($this->message->sender->full_name ?? 'Khách'),
            'content' => $this->message->message,
            'conversation_id' => $this->message->conversation_id,
            'sender_name' => $this->message->sender->full_name ?? 'Khách',
            'title' => 'Tin nhắn mới',
            'created_at' => now()->toDateTimeString(),
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'message' => 'Tin nhắn mới từ ' . ($this->message->sender->full_name ?? 'Khách'),
            'content' => $this->message->message,
            'conversation_id' => $this->message->conversation_id,
            'sender_name' => $this->message->sender->full_name ?? 'Khách',
            'title' => 'Tin nhắn mới',
        ]);
    }
}
