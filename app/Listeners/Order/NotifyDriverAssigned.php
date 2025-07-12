<?php

namespace App\Listeners\Order;

use App\Events\Order\DriverAssigned;
use App\Notifications\NewOrderAssigned;
use Illuminate\Support\Facades\Log;

class NotifyDriverAssigned
{
    /**
     * Handle the event.
     */
    public function handle(DriverAssigned $event)
    {
        $order = $event->order;
        $driver = $event->driver;

        Log::info('NotifyDriverAssigned listener được kích hoạt', [
            'order_id' => $order->id,
            'driver_id' => $driver->id,
            'driver_name' => $driver->full_name
        ]);

        try {
            // Gửi notification cho tài xế
            if (method_exists($driver, 'notify')) {
                $driver->notify(new NewOrderAssigned($order));
                
                Log::info('Đã gửi notification thành công cho tài xế', [
                    'driver_id' => $driver->id,
                    'driver_name' => $driver->full_name,
                    'order_id' => $order->id,
                    'order_code' => $order->order_code
                ]);
            } else {
                Log::warning('Driver model không có method notify', [
                    'driver_id' => $driver->id
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Lỗi khi gửi notification cho tài xế', [
                'driver_id' => $driver->id,
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);
        }
    }
} 