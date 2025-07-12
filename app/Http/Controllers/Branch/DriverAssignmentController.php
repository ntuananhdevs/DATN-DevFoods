<?php

namespace App\Http\Controllers\Branch;

use App\Events\Branch\AwaitingDriver;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Driver;
use App\Models\OrderStatusHistory;
use App\Events\OrderStatusUpdated;
use App\Events\Branch\DriverFound;
use App\Events\DriverAssigned;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DriverAssignmentController extends Controller
{
    /**
     * Maximum number of active orders a driver can handle at once
     */
    const MAX_ACTIVE_ORDERS_PER_DRIVER = 3;

    /**
     * Maximum distance (in km) for driver search
     */
    const MAX_SEARCH_DISTANCE = 10;

    /**
     * Find a suitable driver for an order
     */
    public function findDriver(Request $request, $id)
    {
        $branch = Auth::guard('manager')->user()->branch;
        $order = Order::where('branch_id', $branch->id)
            ->where('id', $id)
            ->with(['address'])
            ->firstOrFail();

        Log::info('Tìm tài xế cho đơn hàng', ['order_id' => $order->id, 'status' => $order->status]);

        if ($order->status !== 'confirmed') {
            return response()->json([
                'success' => false,
                'message' => 'Chỉ có thể tìm tài xế cho đơn hàng đã xác nhận',
                'current_status' => $order->status
            ], 400);
        }

        $lat = $order->address->latitude ?? $order->guest_latitude;
        $lng = $order->address->longitude ?? $order->guest_longitude;

        if (!$lat || !$lng) {
            return response()->json([
                'success' => false,
                'message' => 'Đơn hàng thiếu thông tin địa chỉ giao hàng (latitude/longitude)'
            ], 400);
        }

        try {
            $driver = $this->findNearestAvailableDriver($lat, $lng);
            // Luôn broadcast event cho tài xế
            event(new AwaitingDriver($order));
            if ($driver) {
                $oldStatus = $order->status;
                $order->update([
                    'status' => 'awaiting_driver',
                    // Không gán driver_id ở bước này
                ]);
                // Create status history
                OrderStatusHistory::create([
                    'order_id' => $order->id,
                    'old_status' => $oldStatus,
                    'new_status' => 'awaiting_driver',
                    'changed_by' => Auth::guard('manager')->id(),
                    'changed_by_role' => 'branch_manager',
                    'note' => 'Chuyển sang chờ tài xế nhận đơn',
                    'changed_at' => now()
                ]);
                // Broadcast events
                event(new OrderStatusUpdated($order, $oldStatus, 'awaiting_driver'));
                Log::info('Đã broadcast đơn hàng chờ tài xế', [
                    'order_id' => $order->id
                ]);
                return response()->json([
                    'success' => true,
                    'message' => 'Đã broadcast đơn hàng chờ tài xế',
                    'new_status' => 'awaiting_driver'
                ]);
            } else {
                Log::info('Không tìm được tài xế phù hợp', ['order_id' => $order->id]);
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy tài xế phù hợp trong khu vực',
                    'driver' => null
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Lỗi khi tìm tài xế', [
                'order_id' => $order->id,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Handle driver rejection of an order
     */
    public function handleDriverRejection(Request $request, $id)
    {
        $branch = Auth::guard('manager')->user()->branch;
        $order = Order::where('branch_id', $branch->id)
            ->where('id', $id)
            ->firstOrFail();

        if (!in_array($order->status, ['driver_assigned', 'awaiting_driver'])) {
            return response()->json([
                'success' => false,
                'message' => 'Đơn hàng không ở trạng thái chờ tài xế'
            ], 400);
        }

        // Reset order to confirmed status
        $oldStatus = $order->status;
        $order->update([
            'status' => 'confirmed',
            'driver_id' => null
        ]);

        // Create status history
        OrderStatusHistory::create([
            'order_id' => $order->id,
            'old_status' => $oldStatus,
            'new_status' => 'confirmed',
            'changed_by' => Auth::guard('manager')->id(),
            'changed_by_role' => 'branch_manager',
            'note' => 'Tài xế từ chối đơn hàng: ' . ($request->reason ?? 'Không có lý do'),
            'changed_at' => now()
        ]);

        // Broadcast event
        event(new OrderStatusUpdated($order, $oldStatus, 'confirmed'));

        // Try to find another driver automatically
        $lat = $order->address->latitude ?? $order->guest_latitude;
        $lng = $order->address->longitude ?? $order->guest_longitude;

        if ($lat && $lng) {
            $driver = $this->findNearestAvailableDriver($lat, $lng, [$order->driver_id]);

            if ($driver) {
                $order->update([
                    'status' => 'driver_assigned',
                    'driver_id' => $driver->id
                ]);

                // Create status history
                OrderStatusHistory::create([
                    'order_id' => $order->id,
                    'old_status' => 'confirmed',
                    'new_status' => 'driver_assigned',
                    'changed_by' => Auth::guard('manager')->id(),
                    'changed_by_role' => 'branch_manager',
                    'note' => 'Tìm thấy tài xế phù hợp khác',
                    'changed_at' => now()
                ]);

                // Broadcast events
                event(new OrderStatusUpdated($order, 'confirmed', 'driver_assigned'));
                event(new DriverFound($order, $driver));

                return response()->json([
                    'success' => true,
                    'message' => 'Đã tìm thấy tài xế khác',
                    'driver' => [
                        'id' => $driver->id,
                        'name' => $driver->name,
                        'phone' => $driver->phone,
                        'avatar' => $driver->avatar_url
                    ],
                    'new_status' => 'driver_assigned'
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Đã ghi nhận tài xế từ chối, đang tìm tài xế khác',
            'new_status' => 'confirmed'
        ]);
    }

    /**
     * Find the nearest available driver for an order
     * 
     * @param float $lat Delivery latitude
     * @param float $lng Delivery longitude
     * @param array $excludeDriverIds Driver IDs to exclude from search
     * @return Driver|null
     */
    private function findNearestAvailableDriver($lat, $lng, $excludeDriverIds = [])
    {
        if (!$lat || !$lng) {
            return null;
        }

        // Get online drivers with location data
        $query = Driver::where('status', 'online')
            ->where('is_available', true)
            ->whereHas('location')
            ->with('location');

        // Exclude specific drivers if needed
        if (!empty($excludeDriverIds)) {
            $query->whereNotIn('id', $excludeDriverIds);
        }

        $drivers = $query->get();

        if ($drivers->isEmpty()) {
            Log::info('Không có tài xế nào đang hoạt động');
            return null;
        }

        $nearestDriver = null;
        $shortestDistance = PHP_FLOAT_MAX;

        foreach ($drivers as $driver) {
            // Skip drivers who already have maximum orders
            $activeOrdersCount = Order::where('driver_id', $driver->id)
                ->whereIn('status', [
                    'driver_assigned',
                    'driver_confirmed',
                    'driver_picked_up',
                    'in_transit'
                ])
                ->count();

            if ($activeOrdersCount >= self::MAX_ACTIVE_ORDERS_PER_DRIVER) {
                Log::info('Tài xế đã đạt giới hạn đơn hàng', [
                    'driver_id' => $driver->id,
                    'active_orders' => $activeOrdersCount,
                    'max_orders' => self::MAX_ACTIVE_ORDERS_PER_DRIVER
                ]);
                continue;
            }

            $location = $driver->location;
            if (!$location) continue;

            $distance = $this->calculateDistance(
                $lat,
                $lng,
                $location->latitude,
                $location->longitude
            );

            // Only consider drivers within the maximum search distance
            if ($distance <= self::MAX_SEARCH_DISTANCE && $distance < $shortestDistance) {
                $shortestDistance = $distance;
                $nearestDriver = $driver;
            }
        }

        if ($nearestDriver) {
            Log::info('Tìm thấy tài xế gần nhất', [
                'driver_id' => $nearestDriver->id,
                'distance' => $shortestDistance,
                'lat' => $lat,
                'lng' => $lng
            ]);
        } else {
            Log::info('Không tìm thấy tài xế nào trong phạm vi ' . self::MAX_SEARCH_DISTANCE . 'km');
        }

        return $nearestDriver;
    }



    /**
     * Calculate distance between two lat/lng points using Haversine formula (in km)
     */
    private function calculateDistance($lat1, $lng1, $lat2, $lng2)
    {
        $earthRadius = 6371; // km
        $latFrom = deg2rad($lat1);
        $latTo = deg2rad($lat2);
        $lonFrom = deg2rad($lng1);
        $lonTo = deg2rad($lng2);
        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;
        $a = sin($latDelta / 2) * sin($latDelta / 2) + cos($latFrom) * cos($latTo) * sin($lonDelta / 2) * sin($lonDelta / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return round($earthRadius * $c, 1);
    }
}
