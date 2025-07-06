<?php

namespace App\Events\Customer;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\ReviewReply;

class ReviewReplyDeleted implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $reply_id;
    public $review_id;
    public $user_id;

    public function __construct(ReviewReply $reply)
    {
        $this->reply_id = $reply->id;
        $this->review_id = $reply->review_id;
        $this->user_id = $reply->user_id;
    }

    public function broadcastOn()
    {
        return new Channel('review-replies');
    }

    public function broadcastAs(): string
    {
        return 'review-reply-deleted';
    }

    public function broadcastWith(): array
    {
        return [
            'reply_id' => $this->reply_id,
            'review_id' => $this->review_id,
            'user_id' => $this->user_id,
        ];
    }
} 