<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewOrderAssigned extends Notification implements ShouldQueue
{
    use Queueable;

    public $order;

    /**
     * Create a new notification instance.
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
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
            ->subject('Đơn hàng mới được gán - #' . $this->order->order_code)
            ->greeting('Xin chào ' . $notifiable->full_name . '!')
            ->line('Bạn có đơn hàng mới được gán:')
            ->line('Mã đơn hàng: #' . $this->order->order_code)
            ->line('Địa chỉ giao hàng: ' . $this->order->delivery_address)
            ->line('Tổng tiền: ' . number_format($this->order->total_amount, 0, ',', '.') . ' VNĐ')
            ->action('Xem chi tiết đơn hàng', url('/driver/orders/' . $this->order->id))
            ->line('Vui lòng xác nhận và đến điểm lấy hàng sớm nhất có thể.');
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
            'message' => 'Bạn có đơn hàng mới #' . $this->order->order_code . ' cần giao',
            'delivery_address' => $this->order->delivery_address,
            'total_amount' => $this->order->total_amount,
            'branch_name' => $this->order->branch->name ?? 'Chi nhánh',
            'customer_name' => $this->order->customer->name ?? 'Khách hàng',
            'type' => 'new_order_assigned'
        ];
    }
} 