<?php

namespace App\Events\Branch;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\ProductReview;

class NewReviewNotification implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $review;
    public $branch_id;
    public $review_data;

    public function __construct(ProductReview $review)
    {
        $this->review = $review;
        $this->branch_id = $review->branch_id;
        $this->review_data = [
            'id' => $review->id,
            'user' => [
                'id' => $review->user->id,
                'name' => $review->user->full_name ?? 'Ẩn danh',
            ],
            'product' => $review->product ? [
                'id' => $review->product->id,
                'name' => $review->product->name
            ] : null,
            'combo' => $review->combo ? [
                'id' => $review->combo->id,
                'name' => $review->combo->name
            ] : null,
            'rating' => $review->rating,
            'review' => $review->review,
            'review_date' => $review->review_date,
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
        return 'new-review';
    }

    public function broadcastWith(): array
    {
        return [
            'review' => $this->review_data,
            'branch_id' => $this->branch_id,
            'message' => 'Có bình luận mới từ khách hàng',
        ];
    }
}