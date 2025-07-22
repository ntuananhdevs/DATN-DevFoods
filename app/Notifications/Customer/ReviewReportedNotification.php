<?php

namespace App\Notifications\Customer;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\ProductReview;
use App\Models\User;

class ReviewReportedNotification extends Notification
{
    protected $review;
    protected $reporter;
    protected $reason;

    /**
     * Create a new notification instance.
     *
     * @param \App\Models\ProductReview $review
     * @param \App\Models\User $reporter
     * @param string $reason
     */
    public function __construct(ProductReview $review, User $reporter, $reason)
    {
        $this->review = $review;
        $this->reporter = $reporter;
        $this->reason = $reason;
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
        
        $url = '#'; // Admin should handle this, maybe a link to admin review management
        if (auth('admin')->check()) {
            $url = route('admin.reviews.report.show', $this->review->id);
        }


        return [
            'type' => 'review_reported',
            'reporter_id' => $this->reporter->id,
            'reporter_name' => $this->reporter->name,
            'review_id' => $this->review->id,
            'message' => "Bình luận của bạn về \"{$productOrComboName}\" đã bị báo cáo vì lý do: {$this->reason}.",
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
        if (auth('admin')->check()) {
            $url = route('admin.reviews.report.show', $this->review->id);
        }

        return new BroadcastMessage([
            'message' => "Bình luận của bạn về \"{$productOrComboName}\" đã bị báo cáo vì lý do: {$this->reason}.",
            'url' => $url,
        ]);
    }
} 