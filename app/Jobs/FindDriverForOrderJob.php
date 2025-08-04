<?php

namespace App\Jobs;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Events\Order\DriverAssigned;
use App\Events\Order\OrderStatusUpdated;
use App\Notifications\NoDriverAvailable;

class FindDriverForOrderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $order;
    private $attempt;
    
    // Cấu hình để hỗ trợ đa luồng và xử lý nhiều job đồng thời
    public $timeout = 120; // Timeout cho mỗi job
    public $tries = 3; // Số lần thử lại nếu job fail
    public $backoff = [10, 30, 60]; // Delay giữa các lần retry
    private $maxAttempts = 60; // Tăng số lần thử để tìm tài xế nhanh hơn
    private $delaySeconds = 3; // Giảm delay để tìm nhanh hơn
    // Tìm tài xế trong bán kính, tăng dần theo số lần thử
    private $searchRadiusStart = 2; // km - bắt đầu với bán kính nhỏ hơn
    private $searchRadiusStep = 1; // km - tăng từ từ
    private $searchRadiusMax = 20; // km - mở rộng phạm vi tìm kiếm

    /**
     * Create a new job instance.
     */
    public function __construct(Order $order, $attempt = 1)
    {
        $this->order = $order;
        $this->attempt = $attempt;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $order = $this->order;
        $attempt = $this->attempt;
        $maxAttempts = $this->maxAttempts;
        $delaySeconds = $this->delaySeconds;
        $searchRadius = min($this->searchRadiusStart + ($attempt - 1) * $this->searchRadiusStep, $this->searchRadiusMax);
        // Tăng số đơn tối đa cho phép để xử lý nhiều đơn cùng lúc
        $maxOrders = ($attempt > 30) ? 20 : (($attempt > 15) ? 15 : 12);

        // Kiểm tra xem có job khác đang xử lý order này không
        $jobLockKey = "processing_order_{$order->id}";
        
        // Kiểm tra và dọn dẹp lock cũ nếu quá timeout (5 phút)
        $existingLock = Cache::get($jobLockKey);
        if ($existingLock && isset($existingLock['started_at'])) {
            $lockStartTime = \Carbon\Carbon::parse($existingLock['started_at']);
            if ($lockStartTime->diffInMinutes(now()) > 5) {
                Log::warning('Phát hiện lock cũ quá timeout, xóa lock', [
                    'order_id' => $order->id,
                    'old_lock' => $existingLock,
                    'timeout_minutes' => $lockStartTime->diffInMinutes(now())
                ]);
                Cache::forget($jobLockKey);
            }
        }
        
        // Sử dụng atomic operation để đảm bảo chỉ một job có thể acquire lock
        $lockAcquired = Cache::add($jobLockKey, [
            'attempt' => $attempt,
            'started_at' => now()->toISOString(),
            'job_id' => uniqid(),
            'process_id' => getmypid()
        ], now()->addMinutes(5));
        
        if (!$lockAcquired) {
            $existingLock = Cache::get($jobLockKey);
            Log::info('Có job khác đang xử lý order này, bỏ qua', [
                'order_id' => $order->id,
                'current_attempt' => $attempt,
                'existing_lock' => $existingLock
            ]);
            return;
        }
        
        Log::info('Đã acquire lock cho order', [
            'order_id' => $order->id,
            'attempt' => $attempt,
            'lock_key' => $jobLockKey
        ]);
        
        try {
            Log::info('Bắt đầu tìm tài xế cho đơn hàng', [
                'order_id' => $order->id,
                'order_code' => $order->order_code,
                'branch_id' => $order->branch_id,
                'attempt' => $attempt,
                'search_radius_km' => $searchRadius,
                'max_orders_per_driver' => $maxOrders
            ]);

            // Kiểm tra order đã được gán driver chưa
            $currentOrder = \App\Models\Order::find($order->id);
            if ($currentOrder && $currentOrder->driver_id !== null) {
                Log::info('Order đã được gán driver, dừng job', [
                    'order_id' => $order->id,
                    'driver_id' => $currentOrder->driver_id,
                    'attempt' => $attempt
                ]);
                // Xóa cache danh sách driver đã thử để không còn job nào retry
                Cache::forget("failed_drivers_order_{$order->id}");
                return;
            }

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

            // Lấy danh sách driver đã thử gán (để tránh thử lại)
            $excludeDriverIds = Cache::get("failed_drivers_order_{$order->id}", []);
            $excludeDriversStr = !empty($excludeDriverIds) ? implode(',', $excludeDriverIds) : '0';
        
            $drivers = DB::select("
                SELECT d.*, dl.latitude, dl.longitude,
                       (6371 * acos(cos(radians(?)) * cos(radians(dl.latitude)) * 
                       cos(radians(dl.longitude) - radians(?)) + 
                       sin(radians(?)) * sin(radians(dl.latitude)))) AS distance,
                       COALESCE(order_counts.current_orders, 0) as current_orders
                FROM drivers d
                INNER JOIN driver_locations dl ON d.id = dl.driver_id
                LEFT JOIN (
                    SELECT driver_id, COUNT(*) as current_orders
                    FROM orders 
                    WHERE status IN ('awaiting_driver', 'driver_assigned', 'driver_confirmed', 'driver_picked_up', 'in_transit')
                    GROUP BY driver_id
                ) order_counts ON d.id = order_counts.driver_id
                WHERE d.status = 'active' 
                    AND d.is_available = 1
                    AND COALESCE(order_counts.current_orders, 0) < ?
                    AND d.id NOT IN ({$excludeDriversStr})
                HAVING distance <= ?
                ORDER BY distance ASC, RAND()
                LIMIT 15
            ", [$lat, $lng, $lat, $maxOrders, $searchRadius]);
        
            Log::info('Tìm được tài xế phù hợp:', [
                'count' => count($drivers),
                'search_radius_km' => $searchRadius,
                'max_orders' => $maxOrders,
                'excluded_drivers' => count($excludeDriverIds)
            ]);

            $driver = !empty($drivers) ? $drivers[0] : null;
            
            if (!$driver) {
                Log::info('Không tìm thấy tài xế phù hợp cho đơn hàng', [
                    'order_id' => $order->id,
                    'total_drivers_checked' => \App\Models\Driver::where('status', 'active')->count(),
                    'available_drivers' => \App\Models\Driver::where('status', 'active')->where('is_available', true)->count(),
                    'attempt' => $attempt,
                    'excluded_drivers' => count($excludeDriverIds)
                ]);
                
                if ($attempt < $maxAttempts) {
                    // Re-dispatch job with delay, nhưng trước đó phải release lock
                    Cache::forget($jobLockKey);
                    
                    Log::info('Retry tìm tài xế, sẽ thử lại sau', [
                        'order_id' => $order->id,
                        'next_attempt' => $attempt + 1,
                        'delay_seconds' => $delaySeconds
                    ]);
                    self::dispatch($order, $attempt + 1)->delay(now()->addSeconds($delaySeconds));
                    return;
                } else {
                    // Gửi notification cho branch về việc không có tài xế
                    $this->notifyBranchNoDriver($order, 'Không có tài xế phù hợp sau nhiều lần thử');
                    return;
                }
            }

            // Gán đơn cho tài xế với database lock để tránh race condition
            try {
                DB::transaction(function () use ($order, $driver, $maxOrders) {
                    // Kiểm tra lại order chưa được gán driver
                    $currentOrder = \App\Models\Order::lockForUpdate()->find($order->id);
                    if ($currentOrder->driver_id !== null) {
                        throw new \Exception('Order đã được gán cho driver khác');
                    }
                    
                    // Kiểm tra lại driver vẫn available và chưa vượt quá số đơn
                    $currentDriver = \App\Models\Driver::lockForUpdate()->find($driver->id);
                    if (!$currentDriver->is_available || $currentDriver->status !== 'active') {
                        throw new \Exception('Driver không còn available');
                    }
                    
                    $currentOrders = \App\Models\Order::where('driver_id', $driver->id)
                        ->whereIn('status', ['awaiting_driver', 'driver_assigned', 'driver_confirmed', 'driver_picked_up', 'in_transit'])
                        ->count();
                        
                    if ($currentOrders >= $maxOrders) {
                        throw new \Exception('Driver đã vượt quá số đơn tối đa');
                    }
                    
                    // Gán đơn cho tài xế
                    $currentOrder->driver_id = $driver->id;
                    $currentOrder->status = 'awaiting_driver';
                    $currentOrder->save();
                    
                    // Cập nhật lại object order để sử dụng ở phần sau
                    $this->order = $currentOrder;
                    
                    // Xóa cache danh sách driver đã thử
                    Cache::forget("failed_drivers_order_{$order->id}");
                    
                    // Gửi event thông báo đã gán driver
                    event(new DriverAssigned($currentOrder, $currentOrder->driver));
                    event(new OrderStatusUpdated($currentOrder, false, 'confirmed', 'awaiting_driver'));
                    
                    Log::info('Đã gán đơn hàng cho tài xế thành công', [
                        'order_id' => $order->id,
                        'driver_id' => $driver->id,
                        'driver_name' => $currentDriver->full_name ?? 'N/A',
                        'attempt' => $this->attempt
                    ]);
                });
            } catch (\Exception $e) {
                Log::warning('Không thể gán đơn cho driver do race condition', [
                    'order_id' => $order->id,
                    'driver_id' => $driver->id,
                    'error' => $e->getMessage(),
                    'attempt' => $this->attempt
                ]);
                
                // Kiểm tra lại xem order đã được gán driver chưa
                $recheckOrder = \App\Models\Order::find($order->id);
                if ($recheckOrder && $recheckOrder->driver_id !== null) {
                    Log::info('Order đã được gán driver bởi job khác, dừng retry', [
                        'order_id' => $order->id,
                        'driver_id' => $recheckOrder->driver_id,
                        'attempt' => $this->attempt
                    ]);
                    // Xóa cache danh sách driver đã thử
                    Cache::forget("failed_drivers_order_{$order->id}");
                    return;
                }
                
                // Thêm driver hiện tại vào danh sách đã thử
                $failedDrivers = Cache::get("failed_drivers_order_{$order->id}", []);
                $failedDrivers[] = $driver->id;
                Cache::put("failed_drivers_order_{$order->id}", $failedDrivers, now()->addMinutes(30));
                
                // Retry tìm driver khác nếu còn attempt
                if ($this->attempt < $this->maxAttempts) {
                    // Release lock trước khi retry
                    Cache::forget($jobLockKey);
                    self::dispatch($order, $this->attempt + 1)->delay(now()->addSeconds($this->delaySeconds));
                } else {
                    $this->notifyBranchNoDriver($order, 'Không thể gán driver sau nhiều lần thử');
                }
                return;
            }
        } catch (\Exception $e) {
            Log::error('Lỗi không mong muốn trong FindDriverForOrderJob', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        } finally {
            // Luôn giải phóng lock khi kết thúc
            Cache::forget($jobLockKey);
        }
    }
    /**
     * Xử lý khi job thất bại
     */
    public function failed(\Throwable $exception)
    {
        Log::error('FindDriverForOrderJob failed', [
            'order_id' => $this->order->id,
            'attempt' => $this->attempt,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString()
        ]);
        
        // Có thể thông báo cho admin hoặc customer về việc không tìm được tài xế
        // event(new OrderDriverNotFound($this->order));
    }
    
    /**
     * Lấy unique ID cho job để tránh duplicate
     * Bao gồm attempt để cho phép retry nhưng tránh duplicate cùng attempt
     */
    public function uniqueId()
    {
        return 'find-driver-order-' . $this->order->id . '-attempt-' . $this->attempt;
    }
    
    /**
     * Thời gian giữ unique job (seconds)
     */
    public function uniqueFor()
    {
        return 30; // 30 giây - giảm thời gian để cho phép retry nhanh hơn
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