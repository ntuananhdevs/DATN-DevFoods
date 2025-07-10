<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\Order;

class AdminNewOrderNotification extends Notification
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
            'customer_name' => $this->order->customer->name ?? 'Khách hàng',
            'branch_name' => $this->order->branch->name ?? 'Chi nhánh không xác định',
            'total_amount' => $this->order->total_amount,
            'message' => "Đơn hàng mới #{$this->order->order_code}",
            'created_at' => now()->toDateTimeString(),
        ];
    }
} 