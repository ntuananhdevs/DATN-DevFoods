<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NoDriverAvailable extends Notification implements ShouldQueue
{
    use Queueable;

    public $order;
    public $reason;

    /**
     * Create a new notification instance.
     */
    public function __construct(Order $order, $reason = 'Không có tài xế phù hợp')
    {
        $this->order = $order;
        $this->reason = $reason;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Không tìm được tài xế - Đơn hàng #' . $this->order->order_code)
            ->greeting('Thông báo từ hệ thống')
            ->line('Không thể tìm được tài xế phù hợp cho đơn hàng:')
            ->line('Mã đơn hàng: #' . $this->order->order_code)
            ->line('Lý do: ' . $this->reason)
            ->line('Vui lòng kiểm tra lại hoặc liên hệ admin để hỗ trợ.')
            ->action('Xem chi tiết đơn hàng', url('/branch/orders/' . $this->order->id));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'order_id' => $this->order->id,
            'order_code' => $this->order->order_code,
            'message' => 'Không tìm được tài xế cho đơn hàng #' . $this->order->order_code,
            'reason' => $this->reason,
            'customer_name' => $this->order->customer->name ?? 'Khách hàng',
            'total_amount' => $this->order->total_amount,
            'type' => 'no_driver_available'
        ];
    }
} 