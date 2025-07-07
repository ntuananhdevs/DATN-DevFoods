<?php

namespace App\Events\Customer;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\ProductReview;

class ReviewDeleted implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $review_id;

    public function __construct(ProductReview $review)
    {
        $this->review_id = $review->id;
    }

    public function broadcastOn()
    {
        return new Channel('review-events');
    }

    public function broadcastAs(): string
    {
        return 'review-deleted';
    }

    public function broadcastWith(): array
    {
        return [
            'review_id' => $this->review_id,
        ];
    }
}
