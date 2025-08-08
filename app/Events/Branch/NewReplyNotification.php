<?php

namespace App\Events\Branch;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\ReviewReply;

class NewReplyNotification implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $reply;
    public $review_id;
    public $branch_id;
    public $reply_data;

    public function __construct(ReviewReply $reply)
    {
        $this->reply = $reply;
        $this->review_id = $reply->review_id;
        $this->branch_id = $reply->review->branch_id;
        $this->reply_data = [
            'id' => $reply->id,
            'review_id' => $reply->review_id,
            'user_id' => $reply->user_id, // Add user_id for comparison
            'user' => [
                'id' => $reply->user->id,
                'name' => $reply->user->full_name ?? 'Chi nhánh',
            ],
            'reply' => $reply->reply,
            'reply_date' => $reply->reply_date,
            'is_official' => $reply->is_official,
        ];
    }

    public function broadcastOn()
    {
        // Broadcast to both branch and customer channels
        return [
            new Channel('branch-reviews.' . $this->branch_id),
            new Channel('review-replies.' . $this->review_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'new-reply';
    }

    public function broadcastWith(): array
    {
        return [
            'reply' => $this->reply_data,
            'review_id' => $this->review_id,
            'branch_id' => $this->branch_id,
            'message' => 'Có phản hồi mới từ chi nhánh',
        ];
    }
}