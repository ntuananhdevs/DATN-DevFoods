<?php

namespace App\Jobs;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use App\Events\Order\DriverAssigned;
use App\Events\Order\OrderStatusUpdated;
use App\Notifications\NoDriverAvailable;

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
        
        Log::info('Bắt đầu tìm tài xế cho đơn hàng', [
            'order_id' => $order->id,
            'order_code' => $order->order_code,
            'branch_id' => $order->branch_id
        ]);

        // Lấy toạ độ giao hàng
        $lat = $order->address->latitude ?? $order->guest_latitude;
        $lng = $order->address->longitude ?? $order->guest_longitude;
        
        if (!$lat || !$lng) {
            Log::warning('Không tìm thấy toạ độ giao hàng khi tìm tài xế', [
                'order_id' => $order->id,
                'lat' => $lat,
                'lng' => $lng
            ]);
            
            // Gửi notification cho branch về lỗi
            $this->notifyBranchNoDriver($order, 'Thiếu thông tin địa chỉ giao hàng');
            return;
        }

        // Số đơn tối đa mỗi tài xế có thể nhận
        $maxOrders = 10;

        // Tìm tài xế active, available, số đơn đang nhận < maxOrders
        $driversRaw = \App\Models\Driver::where('status', 'active')
            ->where('is_available', true)
            ->with('location')
            ->get();
        Log::info('Tổng số tài xế active, available:', ['count' => $driversRaw->count()]);
        $drivers = $driversRaw->filter(function ($driver) use ($maxOrders) {
            $currentOrders = \App\Models\Order::where('driver_id', $driver->id)
                ->whereIn('status', ['awaiting_driver', 'driver_assigned', 'driver_confirmed', 'driver_picked_up', 'in_transit'])
                ->count();
            if ($currentOrders >= $maxOrders) {
                \Log::info('Loại tài xế do quá số đơn:', [
                    'driver_id' => $driver->id,
                    'name' => $driver->full_name,
                    'current_orders' => $currentOrders
                ]);
                return false;
            }
            return true;
        })
        ->filter(function ($driver) {
            if ($driver->location === null) {
                \Log::info('Loại tài xế do không có vị trí:', [
                    'driver_id' => $driver->id,
                    'name' => $driver->full_name
                ]);
                return false;
            }
            return true;
        })
        ->sortBy(function ($driver) use ($lat, $lng) {
            $distance = $this->haversine($lat, $lng, $driver->location->latitude, $driver->location->longitude);
            \Log::info('Tài xế hợp lệ:', [
                'driver_id' => $driver->id,
                'name' => $driver->full_name,
                'lat' => $driver->location->latitude,
                'lng' => $driver->location->longitude,
                'distance_km' => $distance
            ]);
            return $distance;
        });

        $driver = $drivers->first();
        
        if (!$driver) {
            Log::info('Không tìm thấy tài xế phù hợp cho đơn hàng', [
                'order_id' => $order->id,
                'total_drivers_checked' => \App\Models\Driver::where('status', 'active')->count(),
                'available_drivers' => \App\Models\Driver::where('status', 'active')->where('is_available', true)->count()
            ]);
            
            // Gửi notification cho branch về việc không có tài xế
            $this->notifyBranchNoDriver($order, 'Không có tài xế phù hợp');
            return;
        }

        // Gán đơn cho tài xế
        $order->driver_id = $driver->id;
        $order->status = 'awaiting_driver';
        $order->save();

        Log::info('Đã gán đơn hàng cho tài xế thành công', [
            'order_id' => $order->id,
            'driver_id' => $driver->id,
            'driver_name' => $driver->full_name,
            'distance_km' => $this->haversine($lat, $lng, $driver->location->latitude, $driver->location->longitude)
        ]);

        // Dispatch event để thông báo cho tài xế
        event(new DriverAssigned($order, $driver));
        Log::info('Đã dispatch event DriverAssigned', [
            'driver_id' => $driver->id,
            'order_id' => $order->id
        ]);

        // Broadcast event để cập nhật realtime
        event(new OrderStatusUpdated($order, 'confirmed', 'awaiting_driver'));
    }

    /**
     * Gửi notification cho branch khi không tìm được tài xế
     */
    private function notifyBranchNoDriver($order, $reason)
    {
        try {
            $branch = $order->branch;
            if ($branch && method_exists($branch, 'notify')) {
                $branch->notify(new NoDriverAvailable($order, $reason));
                Log::warning('Đã gửi notification cho branch về việc không tìm được tài xế', [
                    'order_id' => $order->id,
                    'reason' => $reason,
                    'branch_id' => $branch->id
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Lỗi khi gửi notification cho branch', [
                'error' => $e->getMessage(),
                'order_id' => $order->id
            ]);
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