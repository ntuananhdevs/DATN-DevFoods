<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\Order;

class CustomerOrderSuccessNotification extends Notification
{
    use Queueable;

    protected $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'order_id' => $this->order->id,
            'order_code' => $this->order->order_code,
            'branch_name' => $this->order->branch->name ?? 'Chi nhánh không xác định',
            'total_amount' => $this->order->total_amount,
            'message' => 'Bạn đã đặt hàng thành công',
            'type' => 'order_success',
            'created_at' => now()->toDateTimeString(),
        ];
    }
}