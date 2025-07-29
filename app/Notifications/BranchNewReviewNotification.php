<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;
use App\Models\ProductReview;

class BranchNewReviewNotification extends Notification
{
    protected $review;

    public function __construct(ProductReview $review)
    {
        $this->review = $review;
    }

    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    public function broadcastOn()
    {
        return ['private-branch.' . ($this->review->branch_id ?? 0)];
    }

    public function toDatabase($notifiable)
    {
        $productOrComboName = $this->review->product ? $this->review->product->name : ($this->review->combo ? $this->review->combo->name : 'một sản phẩm');
        $url = '#';
        if ($this->review->product && $this->review->product->slug) {
            $url = route('products.show', ['slug' => $this->review->product->slug]) . '#review-' . $this->review->id;
        } elseif ($this->review->combo && $this->review->combo->slug) {
            $url = route('combos.show', ['slug' => $this->review->combo->slug]) . '#review-' . $this->review->id;
        }
        return [
            'type' => 'branch_new_review',
            'review_id' => $this->review->id,
            'user_id' => $this->review->user_id,
            'branch_id' => $this->review->branch_id,
            'message' => "Có bình luận mới về \"{$productOrComboName}\" tại chi nhánh.",
            'url' => $url,
        ];
    }

    public function toBroadcast($notifiable)
    {
        $productOrComboName = $this->review->product ? $this->review->product->name : ($this->review->combo ? $this->review->combo->name : 'một sản phẩm');
        $url = '#';
        if ($this->review->product && $this->review->product->slug) {
            $url = route('products.show', ['slug' => $this->review->product->slug]) . '#review-' . $this->review->id;
        } elseif ($this->review->combo && $this->review->combo->slug) {
            $url = route('combos.show', ['slug' => $this->review->combo->slug]) . '#review-' . $this->review->id;
        }
        return new BroadcastMessage([
            'message' => "Có bình luận mới về \"{$productOrComboName}\" tại chi nhánh.",
            'url' => $url,
        ]);
    }
} 