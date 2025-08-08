<?php

namespace App\Events\Customer;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\ProductReview;

class ReviewCreated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $review;
    public $product_id;
    public $combo_id;
    public $user_id;
    public $review_data;

    public function __construct(ProductReview $review)
    {
        $this->review = $review;
        $this->product_id = $review->product_id;
        $this->combo_id = $review->combo_id;
        $this->user_id = $review->user_id;
        $this->review_data = [
            'id' => $review->id,
            'user_id' => $review->user_id, // Thêm dòng này
            'user' => [
                'id' => $review->user->id,
                'name' => $review->user->name,
                'avatar' => $review->user->avatar ?? null,
            ],
            'rating' => $review->rating,
            'review' => $review->review,
            'review_date' => $review->review_date,
            'review_image' => $review->review_image ? (\Storage::disk('s3')->url($review->review_image)) : null,
            'is_verified_purchase' => $review->is_verified_purchase,
            'helpful_count' => $review->helpful_count,
            'report_count' => $review->report_count,
            'is_featured' => $review->is_featured,
            'branch' => $review->branch ? [
                'id' => $review->branch->id,
                'name' => $review->branch->name
            ] : null,
            'purchased_variant_attributes' => $review->purchased_variant_attributes,
            'replies' => [], // Replies can be loaded if needed
        ];
    }

    public function broadcastOn()
    {
        // Broadcast to a channel for the product or combo
        if ($this->product_id) {
            return new Channel('product-reviews.' . $this->product_id);
        } elseif ($this->combo_id) {
            return new Channel('combo-reviews.' . $this->combo_id);
        }
        return new Channel('product-reviews');
    }

    public function broadcastAs(): string
    {
        return 'review-created';
    }

    public function broadcastWith(): array
    {
        return [
            'review' => $this->review_data,
            'product_id' => $this->product_id,
            'combo_id' => $this->combo_id,
        ];
    }
} 