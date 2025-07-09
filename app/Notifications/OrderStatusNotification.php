<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class OrderStatusNotification extends Notification
{
    use Queueable;

    protected $order;
    protected $status;
    protected $message;
    protected $extra;

    public function __construct($order, $status, $message, $extra = [])
    {
        $this->order = $order;
        $this->status = $status;
        $this->message = $message;
        $this->extra = $extra;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return array_merge([
            'order_code' => $this->order->order_code,
            'status' => $this->status,
            'message' => $this->message,
            'customer_name' => $this->order->customer->name ?? '',
        ], $this->extra);
    }
} 