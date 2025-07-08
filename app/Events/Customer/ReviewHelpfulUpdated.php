<?php

namespace App\Events\Customer;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\ProductReview;

class ReviewHelpfulUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $review_id;
    public $helpful_count;

    public function __construct(ProductReview $review)
    {
        $this->review_id = $review->id;
        $this->helpful_count = $review->helpful_count;
    }

    public function broadcastOn()
    {
        return new Channel('review-helpful');
    }

    public function broadcastAs(): string
    {
        return 'review-helpful-updated';
    }

    public function broadcastWith(): array
    {
        return [
            'review_id' => $this->review_id,
            'helpful_count' => $this->helpful_count,
        ];
    }
} 