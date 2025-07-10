<?php

namespace App\Jobs;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use App\Notifications\NewOrderAssigned;

class FindDriverForOrderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $order;

    /**
     * Create a new job instance.
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $order = $this->order;
        $lat = $order->address->latitude ?? $order->guest_latitude;
        $lng = $order->address->longitude ?? $order->guest_longitude;
        if (!$lat || !$lng) {
            Log::warning('Không tìm thấy toạ độ giao hàng khi tìm tài xế', ['order_id' => $order->id]);
            return;
        }

        // Số đơn tối đa mỗi tài xế có thể nhận
        $maxOrders = 5;

        // Tìm tài xế active, available, số đơn đang nhận < maxOrders, sắp xếp theo khoảng cách
        $drivers = \App\Models\Driver::where('status', 'active')
            ->where('is_available', true)
            ->get()
            ->filter(function ($driver) use ($maxOrders) {
                // Đếm số đơn chưa hoàn thành của tài xế
                $currentOrders = \App\Models\Order::where('driver_id', $driver->id)
                    ->whereIn('status', ['awaiting_driver', 'driver_assigned', 'driver_confirmed', 'driver_picked_up', 'in_transit'])
                    ->count();
                return $currentOrders < $maxOrders;
            })
            ->sortBy(function ($driver) use ($lat, $lng) {
                // Tính khoảng cách Haversine
                $distance = $this->haversine($lat, $lng, $driver->latitude, $driver->longitude);
                return $distance;
            });

        $driver = $drivers->first();
        if (!$driver) {
            Log::info('Không tìm thấy tài xế phù hợp cho đơn hàng', ['order_id' => $order->id]);
            // Có thể gửi notification cho admin/branch biết không có tài xế
            return;
        }

        // Gán đơn cho tài xế
        $order->driver_id = $driver->id;
        $order->status = 'awaiting_driver';
        $order->save();

        // (Tuỳ chọn) Cập nhật trạng thái tài xế nếu cần
        // $driver->is_available = false;
        // $driver->save();

        // Gửi notification cho tài xế
        if (method_exists($driver, 'notify')) {
            $driver->notify(new NewOrderAssigned($order));
        }
    }

    // Hàm tính khoảng cách Haversine (km)
    private function haversine($lat1, $lng1, $lat2, $lng2)
    {
        $earthRadius = 6371;
        $latFrom = deg2rad($lat1);
        $latTo = deg2rad($lat2);
        $lngFrom = deg2rad($lng1);
        $lngTo = deg2rad($lng2);
        $latDelta = $latTo - $latFrom;
        $lngDelta = $lngTo - $lngFrom;
        $a = sin($latDelta / 2) * sin($latDelta / 2) +
            cos($latFrom) * cos($latTo) *
            sin($lngDelta / 2) * sin($lngDelta / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return round($earthRadius * $c, 2);
    }
} 