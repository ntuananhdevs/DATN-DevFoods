<?php

namespace App\Events\Customer;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\ReviewReply;

class ReviewReplyCreated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $reply;
    public $review_id;
    public $user_id;
    public $reply_date;
    public $reply_content;

    public function __construct(ReviewReply $reply)
    {
        $this->reply = $reply;
        $this->review_id = $reply->review_id;
        $this->user_id = $reply->user_id;
        $this->reply_date = $reply->reply_date;
        $this->reply_content = $reply->reply;
    }

    public function broadcastOn()
    {
        return new Channel('review-replies');
    }

    public function broadcastAs(): string
    {
        return 'review-reply-created';
    }

    public function broadcastWith(): array
    {
        return [
            'reply_id' => $this->reply->id,
            'review_id' => $this->review_id,
            'user_id' => $this->user_id,
            'reply_date' => $this->reply_date,
            'reply_content' => $this->reply_content,
            'user_name' => $this->reply->user ? $this->reply->user->name : null,
        ];
    }
} 