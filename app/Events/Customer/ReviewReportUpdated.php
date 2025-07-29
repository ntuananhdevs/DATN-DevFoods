<?php

namespace App\Events\Customer;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\ProductReview;

class ReviewReportUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $review_id;
    public $report_count;

    public function __construct(ProductReview $review)
    {
        $this->review_id = $review->id;
        $this->report_count = $review->report_count;
    }

    public function broadcastOn()
    {
        return new Channel('review-reports');
    }

    public function broadcastAs(): string
    {
        return 'review-report-updated';
    }

    public function broadcastWith(): array
    {
        return [
            'review_id' => $this->review_id,
            'report_count' => $this->report_count,
        ];
    }
} 