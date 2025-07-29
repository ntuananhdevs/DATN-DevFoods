<?php

namespace App\Notifications\Customer;

use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;
use App\Models\ProductReview;
use App\Models\User;

class ReviewLikedNotification extends Notification
{
    protected $review;
    protected $liker;

    /**
     * Create a new notification instance.
     *
     * @param \App\Models\ProductReview $review
     * @param \App\Models\User $liker
     */
    public function __construct(ProductReview $review, User $liker)
    {
        $this->review = $review;
        $this->liker = $liker;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
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
            'type' => 'review_liked',
            'liker_id' => $this->liker->id,
            'liker_name' => $this->liker->name,
            'review_id' => $this->review->id,
            'message' => "{$this->liker->name} đã thích bình luận của bạn về \"{$productOrComboName}\".",
            'url' => $url,
        ];
    }

    /**
     * Get the broadcastable representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\BroadcastMessage
     */
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
            'message' => "{$this->liker->name} đã thích bình luận của bạn về \"{$productOrComboName}\".",
            'url' => $url,
        ]);
    }
} 