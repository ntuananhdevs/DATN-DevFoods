<?php

namespace App\Notifications\Customer;

use App\Models\ReviewReply;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReviewRepliedNotification extends Notification
{
    use Queueable;

    protected $reply;

    /**
     * Create a new notification instance.
     *
     * @param \App\Models\ReviewReply $reply
     */
    public function __construct(ReviewReply $reply)
    {
        $this->reply = $reply;
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
        $review = $this->reply->review;
        $productOrComboName = $review->product ? $review->product->name : ($review->combo ? $review->combo->name : 'một sản phẩm');

        $url = '#';
        if ($review->product) {
            $url = route('products.show', ['slug' => $review->product->slug]) . '#reply-' . $this->reply->id;
        } elseif ($review->combo && $review->combo->slug) {
            // Assuming you have a route for combos like `combos.show` and combo has a slug
            $url = route('combos.show', ['slug' => $review->combo->slug]) . '#reply-' . $this->reply->id;
        }

        return [
            'type' => 'review_replied',
            'replier_id' => $this->reply->user->id,
            'replier_name' => $this->reply->user->name,
            'review_id' => $review->id,
            'reply_id' => $this->reply->id,
            'message' => "{$this->reply->user->name} đã trả lời bình luận của bạn về \"{$productOrComboName}\".",
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
        $review = $this->reply->review;
        $productOrComboName = $review->product ? $review->product->name : ($review->combo ? $review->combo->name : 'một sản phẩm');

        $url = '#';
        if ($review->product) {
            $url = route('products.show', ['slug' => $review->product->slug]) . '#reply-' . $this->reply->id;
        } elseif ($review->combo && $review->combo->slug) {
            $url = route('combos.show', ['slug' => $review->combo->slug]) . '#reply-' . $this->reply->id;
        }

        return new BroadcastMessage([
            'message' => "{$this->reply->user->name} đã trả lời bình luận của bạn về \"{$productOrComboName}\".",
            'url' => $url,
        ]);
    }
} 