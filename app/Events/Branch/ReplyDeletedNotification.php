<?php

namespace App\Events\Branch;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\ReviewReply;

class ReplyDeletedNotification implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $reply_id;
    public $review_id;
    public $branch_id;
    public $reply_data;

    public function __construct(ReviewReply $reply)
    {
        $this->reply_id = $reply->id;
        $this->review_id = $reply->review_id;
        $this->branch_id = $reply->review->branch_id;
        $this->reply_data = [
            'id' => $reply->id,
            'review_id' => $reply->review_id,
            'user_id' => $reply->user_id,
            'user' => [
                'id' => $reply->user->id,
                'name' => $reply->user->full_name ?? 'Chi nhánh',
            ],
            'reply' => $reply->reply,
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
        return 'reply-deleted';
    }

    public function broadcastWith(): array
    {
        return [
            'reply_id' => $this->reply_id,
            'reply' => $this->reply_data,
            'review_id' => $this->review_id,
            'branch_id' => $this->branch_id,
            'message' => 'Một phản hồi đã bị xóa',
        ];
    }
}