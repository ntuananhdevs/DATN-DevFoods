<?php

namespace App\Events\Branch;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\ProductReview;

class ReviewDeletedNotification implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $review_id;
    public $branch_id;
    public $review_data;

    public function __construct(ProductReview $review)
    {
        $this->review_id = $review->id;
        $this->branch_id = $review->branch_id;
        $this->review_data = [
            'id' => $review->id,
            'user' => [
                'name' => $review->user->full_name ?? 'Ẩn danh',
            ],
            'product' => $review->product ? [
                'name' => $review->product->name
            ] : null,
            'combo' => $review->combo ? [
                'name' => $review->combo->name
            ] : null,
            'branch_id' => $review->branch_id,
        ];
    }

    public function broadcastOn()
    {
        // Broadcast to branch channel
        return new Channel('branch-reviews.' . $this->branch_id);
    }

    public function broadcastAs(): string
    {
        return 'review-deleted';
    }

    public function broadcastWith(): array
    {
        return [
            'review_id' => $this->review_id,
            'review' => $this->review_data,
            'branch_id' => $this->branch_id,
            'message' => 'Một bình luận đã bị xóa',
        ];
    }
}