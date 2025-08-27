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
    private $maxAttempts = 60; // Giảm số lần thử để tăng tốc độ
    private $delaySeconds = 1; // Giảm delay để tìm nhanh hơn
    // Tìm tài xế trong bán kính, tăng dần theo số lần thử
    private $searchRadiusStart = 5; // km - bắt đầu với bán kính lớn hơn để tìm nhanh
    private $searchRadiusStep = 2; // km - tăng nhanh hơn
    private $searchRadiusMax = 30; // km - mở rộng phạm vi tìm kiếm

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
                SELECT /*+ USE_INDEX(d, idx_drivers_status_available) USE_INDEX(dl, idx_driver_locations_driver_id) */ 
                       d.*, dl.latitude, dl.longitude,
                       (6371 * acos(cos(radians(?)) * cos(radians(dl.latitude)) * 
                       cos(radians(dl.longitude) - radians(?)) + 
                       sin(radians(?)) * sin(radians(dl.latitude)))) AS distance,
                       COALESCE(order_counts.current_orders, 0) as current_orders,
                       COALESCE(delivering_orders.delivering_count, 0) as delivering_orders
                FROM drivers d FORCE INDEX (PRIMARY)
                INNER JOIN driver_locations dl FORCE INDEX (PRIMARY) ON d.id = dl.driver_id
                LEFT JOIN (
                    SELECT driver_id, COUNT(*) as current_orders
                    FROM orders USE INDEX (idx_orders_driver_status)
                    WHERE status IN ('awaiting_confirmation', 'confirmed', 'awaiting_driver', 'driver_confirmed', 'waiting_driver_pick_up', 'driver_picked_up')
                    GROUP BY driver_id
                ) order_counts ON d.id = order_counts.driver_id
                LEFT JOIN (
                    SELECT driver_id, COUNT(*) as delivering_count
                    FROM orders USE INDEX (idx_orders_driver_status)
                    WHERE status = 'in_transit'
                    GROUP BY driver_id
                ) delivering_orders ON d.id = delivering_orders.driver_id
                WHERE d.status = 'active' 
                    AND d.is_available = 1
                    AND COALESCE(order_counts.current_orders, 0) < ?
                    AND COALESCE(delivering_orders.delivering_count, 0) = 0
                    AND d.id NOT IN ({$excludeDriversStr})
                HAVING distance <= ?
                ORDER BY distance ASC, current_orders ASC
                LIMIT 25
            ", [$lat, $lng, $lat, $maxOrders, $searchRadius]);
            
            // Lọc và tính điểm ưu tiên cho tài xế
            $suitableDrivers = [];
            $maxDistanceBetweenOrders = 5; // km - khoảng cách tối đa giữa các đơn hàng của cùng tài xế
            
            foreach ($drivers as $driver) {
                $isAreaSuitable = false;
                
                if ($driver->current_orders == 0) {
                    // Tài xế chưa có đơn nào, có thể gán
                    $isAreaSuitable = true;
                } else {
                    // Kiểm tra khoảng cách với các đơn hàng hiện tại của tài xế
                    $isAreaSuitable = $this->checkDriverOrdersInSameArea($driver->id, $lat, $lng, $maxDistanceBetweenOrders);
                }
                
                if ($isAreaSuitable) {
                    // Tính điểm ưu tiên: khoảng cách gần hơn và ít đơn hơn = điểm cao hơn
                    $distanceScore = max(0, 20 - $driver->distance); // Điểm khoảng cách (0-20)
                    $orderScore = max(0, 10 - $driver->current_orders); // Điểm số đơn (0-10)
                    $driver->priority_score = $distanceScore + $orderScore;
                    
                    $suitableDrivers[] = $driver;
                }
            }
            
            // Sắp xếp theo điểm ưu tiên (cao nhất trước)
            usort($suitableDrivers, function($a, $b) {
                if ($a->priority_score == $b->priority_score) {
                    return $a->distance <=> $b->distance; // Nếu điểm bằng nhau, ưu tiên gần hơn
                }
                return $b->priority_score <=> $a->priority_score; // Điểm cao hơn trước
            });
            
            // Log chỉ khi có vấn đề hoặc thành công
            if (empty($suitableDrivers)) {
                Log::info('Không tìm được tài xế phù hợp', [
                    'order_id' => $this->order->id,
                    'original_drivers_count' => count($drivers)
                ]);
            }
            
            $drivers = $suitableDrivers;
            
            // Nếu không tìm được tài xế phù hợp sau khi lọc theo khu vực
            if (empty($drivers)) {
                Log::warning('Không tìm được tài xế phù hợp trong khu vực lân cận, áp dụng fallback', [
                    'order_id' => $this->order->id,
                    'pickup_coordinates' => [$lat, $lng],
                    'max_distance_between_orders' => $maxDistanceBetweenOrders,
                    'attempt' => $attempt
                ]);
                
                // Fallback: Nới rộng điều kiện tìm kiếm
                if ($attempt >= 5) {
                    // Sau 5 lần thử, bỏ qua ràng buộc khu vực và tăng số đơn tối đa
                    $fallbackMaxOrders = min($maxOrders + 5, 25); // Tăng tối đa 5 đơn
                    $fallbackRadius = min($searchRadius + 5, 30); // Tăng bán kính thêm 5km
                    
                    Log::info('Áp dụng fallback: nới rộng điều kiện', [
                        'fallback_max_orders' => $fallbackMaxOrders,
                        'fallback_radius' => $fallbackRadius,
                        'original_max_orders' => $maxOrders,
                        'original_radius' => $searchRadius
                    ]);
                    
                    // Tìm lại driver với điều kiện nới rộng
                    $fallbackDrivers = DB::select("
                        SELECT /*+ USE_INDEX(d, idx_drivers_status_available) USE_INDEX(dl, idx_driver_locations_driver_id) */ 
                               d.*, dl.latitude, dl.longitude,
                               (6371 * acos(cos(radians(?)) * cos(radians(dl.latitude)) * 
                               cos(radians(dl.longitude) - radians(?)) + 
                               sin(radians(?)) * sin(radians(dl.latitude)))) AS distance,
                               COALESCE(order_counts.current_orders, 0) as current_orders,
                               COALESCE(delivering_orders.delivering_count, 0) as delivering_orders
                        FROM drivers d FORCE INDEX (PRIMARY)
                        INNER JOIN driver_locations dl FORCE INDEX (PRIMARY) ON d.id = dl.driver_id
                        LEFT JOIN (
                            SELECT driver_id, COUNT(*) as current_orders
                            FROM orders USE INDEX (idx_orders_driver_status)
                            WHERE status IN ('awaiting_confirmation', 'confirmed', 'awaiting_driver', 'driver_confirmed', 'waiting_driver_pick_up', 'driver_picked_up')
                            GROUP BY driver_id
                        ) order_counts ON d.id = order_counts.driver_id
                        LEFT JOIN (
                            SELECT driver_id, COUNT(*) as delivering_count
                            FROM orders USE INDEX (idx_orders_driver_status)
                            WHERE status = 'in_transit'
                            GROUP BY driver_id
                        ) delivering_orders ON d.id = delivering_orders.driver_id
                        WHERE d.status = 'active' 
                            AND d.is_available = 1
                            AND COALESCE(order_counts.current_orders, 0) < ?
                            AND COALESCE(delivering_orders.delivering_count, 0) = 0
                            AND d.id NOT IN ({$excludeDriversStr})
                        HAVING distance <= ?
                        ORDER BY distance ASC, current_orders ASC
                        LIMIT 30
                    ", [$lat, $lng, $lat, $fallbackMaxOrders, $fallbackRadius]);
                    
                    // Sử dụng tất cả driver tìm được (bỏ qua ràng buộc khu vực)
                    $drivers = $fallbackDrivers;
                    
                    Log::info('Kết quả fallback tìm kiếm:', [
                        'fallback_drivers_found' => count($drivers)
                    ]);
                }
            }
        
            Log::info('Tìm được tài xế phù hợp (không đang giao hàng):', [
                'count' => count($drivers),
                'search_radius_km' => $searchRadius,
                'max_orders' => $maxOrders,
                'excluded_drivers' => count($excludeDriverIds)
            ]);
            
            // Log thông tin về việc lọc tài xế theo trạng thái giao hàng
            foreach ($drivers as $driver) {
                Log::info('Tài xế được chọn:', [
                    'driver_id' => $driver->id,
                    'current_orders' => $driver->current_orders,
                    'delivering_orders' => $driver->delivering_orders,
                    'distance_km' => round($driver->distance, 2)
                ]);
            }

            $driver = !empty($drivers) ? $drivers[0] : null;
            
            if (!$driver) {
                // Thống kê chi tiết về tài xế
                $totalActiveDrivers = \App\Models\Driver::where('status', 'active')->count();
                $availableDrivers = \App\Models\Driver::where('status', 'active')->where('is_available', true)->count();
                $deliveringDrivers = \App\Models\Driver::where('status', 'active')
                    ->whereHas('orders', function($query) {
                        $query->where('status', 'in_transit');
                    })->count();
                
                Log::info('Không tìm thấy tài xế phù hợp cho đơn hàng', [
                    'order_id' => $order->id,
                    'total_active_drivers' => $totalActiveDrivers,
                    'available_drivers' => $availableDrivers,
                    'drivers_currently_delivering' => $deliveringDrivers,
                    'attempt' => $attempt,
                    'excluded_drivers' => count($excludeDriverIds),
                    'search_radius_km' => $searchRadius
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
                    
                    // Kiểm tra tài xế không đang giao hàng
                    $deliveringOrders = \App\Models\Order::where('driver_id', $driver->id)
                        ->where('status', 'in_transit')
                        ->count();
                        
                    if ($deliveringOrders > 0) {
                        throw new \Exception('Driver đang giao hàng, không thể gán đơn mới');
                    }
                    
                    // Đếm các đơn hàng hiện tại (chỉ các trạng thái được phép)
                    $currentOrders = \App\Models\Order::where('driver_id', $driver->id)
                        ->whereIn('status', ['awaiting_confirmation', 'confirmed', 'awaiting_driver', 'driver_confirmed', 'waiting_driver_pick_up', 'driver_picked_up'])
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

    /**
     * Rà soát và khởi động lại job cho các đơn hàng bị treo hoặc đơn cũ đang tìm tài xế
     * 
     * @param bool $includeOldOrders Có bao gồm đơn hàng cũ hơn 2 giờ không
     * @return array Thống kê kết quả
     */
    public static function reviewStuckOrders($includeOldOrders = false)
    {
        $lockKey = 'reviewing_stuck_orders';
        
        // Kiểm tra xem có process nào đang review không
        if (!Cache::add($lockKey, true, now()->addMinutes(10))) {
            Log::info('Có process khác đang review stuck orders, bỏ qua');
            return ['status' => 'skipped', 'reason' => 'Another process is reviewing'];
        }
        
        try {
            Log::info('Bắt đầu rà soát các đơn hàng bị treo', [
                'include_old_orders' => $includeOldOrders
            ]);
            
            // Xác định khoảng thời gian tìm kiếm
            $timeLimit = $includeOldOrders ? now()->subDays(7) : now()->subHours(2);
            
            // Tìm các đơn hàng đang tìm tài xế hoặc bị treo
            $stuckOrders = Order::where(function($query) {
                $query->where('status', 'confirmed')
                      ->orWhere('status', 'awaiting_driver');
            })
            ->whereNull('driver_id')
            ->where('created_at', '>=', $timeLimit)
            ->get();
            
            Log::info('Tìm thấy đơn hàng cần rà soát', [
                'count' => $stuckOrders->count(),
                'time_limit' => $timeLimit->toDateTimeString()
            ]);
            
            $processedCount = 0;
            $restartedCount = 0;
            
            foreach ($stuckOrders as $order) {
                try {
                    // Kiểm tra xem có job nào đang xử lý order này không
                    $jobLockKey = "processing_order_{$order->id}";
                    $existingLock = Cache::get($jobLockKey);
                    
                    if ($existingLock) {
                        // Kiểm tra lock có quá cũ không (quá 10 phút)
                        if (isset($existingLock['started_at'])) {
                            $lockStartTime = \Carbon\Carbon::parse($existingLock['started_at']);
                            if ($lockStartTime->diffInMinutes(now()) > 10) {
                                Log::warning('Phát hiện lock cũ cho order, xóa và restart job', [
                                    'order_id' => $order->id,
                                    'lock_age_minutes' => $lockStartTime->diffInMinutes(now())
                                ]);
                                Cache::forget($jobLockKey);
                                
                                // Restart job
                                self::dispatch($order, 1);
                                $restartedCount++;
                            } else {
                                Log::info('Order đang được xử lý bởi job khác', [
                                    'order_id' => $order->id,
                                    'lock_age_minutes' => $lockStartTime->diffInMinutes(now())
                                ]);
                            }
                        } else {
                            // Lock không có thông tin thời gian, xóa và restart
                            Cache::forget($jobLockKey);
                            self::dispatch($order, 1);
                            $restartedCount++;
                        }
                    } else {
                        // Không có lock, kiểm tra thời gian tạo order
                        $orderAge = $order->created_at->diffInMinutes(now());
                        
                        if ($orderAge > 10) { // Đơn hàng tồn tại hơn 10 phút mà chưa có tài xế
                            Log::info('Restart job cho đơn hàng bị treo', [
                                'order_id' => $order->id,
                                'order_age_minutes' => $orderAge,
                                'status' => $order->status
                            ]);
                            
                            // Xóa cache failed drivers để bắt đầu lại từ đầu
                            Cache::forget("failed_drivers_order_{$order->id}");
                            
                            // Dispatch job mới
                            self::dispatch($order, 1);
                            $restartedCount++;
                        }
                    }
                    
                    $processedCount++;
                    
                } catch (\Exception $e) {
                    Log::error('Lỗi khi xử lý stuck order', [
                        'order_id' => $order->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }
            
            $result = [
                'status' => 'completed',
                'total_found' => $stuckOrders->count(),
                'processed' => $processedCount,
                'restarted' => $restartedCount,
                'include_old_orders' => $includeOldOrders,
                'time_limit' => $timeLimit->toDateTimeString()
            ];
            
            Log::info('Hoàn thành rà soát stuck orders', $result);
            
            return $result;
            
        } catch (\Exception $e) {
            Log::error('Lỗi trong quá trình review stuck orders', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return [
                'status' => 'error',
                'error' => $e->getMessage()
            ];
        } finally {
            // Luôn giải phóng lock
            Cache::forget($lockKey);
        }
    }
    
    /**
     * Dọn dẹp cache cho các đơn hàng đã hoàn thành
     * 
     * @return array Thống kê kết quả
     */
    public static function cleanupOrderCache()
    {
        $lockKey = 'cleaning_order_cache';
        
        if (!Cache::add($lockKey, true, now()->addMinutes(5))) {
            Log::info('Có process khác đang cleanup cache, bỏ qua');
            return ['status' => 'skipped', 'reason' => 'Another process is cleaning'];
        }
        
        try {
            Log::info('Bắt đầu dọn dẹp cache cho đơn hàng đã hoàn thành');
            
            // Lấy tất cả cache keys có pattern failed_drivers_order_*
            $cacheKeys = [];
            $cleanedCount = 0;
            
            // Tìm các đơn hàng đã hoàn thành hoặc đã có tài xế
            $completedOrders = Order::whereNotNull('driver_id')
                ->orWhereIn('status', ['completed', 'cancelled', 'delivered'])
                ->pluck('id')
                ->toArray();
            
            foreach ($completedOrders as $orderId) {
                $cacheKey = "failed_drivers_order_{$orderId}";
                if (Cache::has($cacheKey)) {
                    Cache::forget($cacheKey);
                    $cleanedCount++;
                }
                
                // Cũng xóa processing lock nếu có
                $lockKey = "processing_order_{$orderId}";
                if (Cache::has($lockKey)) {
                    Cache::forget($lockKey);
                }
            }
            
            $result = [
                'status' => 'completed',
                'cleaned_count' => $cleanedCount,
                'checked_orders' => count($completedOrders)
            ];
            
            Log::info('Hoàn thành dọn dẹp cache', $result);
            
            return $result;
            
        } catch (\Exception $e) {
            Log::error('Lỗi trong quá trình cleanup cache', [
                'error' => $e->getMessage()
            ]);
            
            return [
                'status' => 'error',
                'error' => $e->getMessage()
            ];
        } finally {
            Cache::forget($lockKey);
        }
    }

    /**
     * Rà soát lại các đơn hàng chưa tìm thấy tài xế
     * Phương thức này sẽ tự động kiểm tra và dispatch lại job cho các đơn hàng
     * đã quá thời gian chờ tài xế
     */
    public static function reviewPendingOrders()
    {
        try {
            // Thời gian chờ tối đa trước khi rà soát lại (phút)
            $maxWaitingMinutes = 10;
            
            // Tìm các đơn hàng chưa có tài xế và đã quá thời gian chờ
            $pendingOrders = \App\Models\Order::where('driver_id', null)
                ->whereIn('status', ['confirmed', 'awaiting_driver'])
                ->where('created_at', '<=', now()->subMinutes($maxWaitingMinutes))
                ->where('created_at', '>=', now()->subHours(2)) // Chỉ rà soát đơn trong 2 giờ gần đây
                ->get();
            
            Log::info('Bắt đầu rà soát đơn hàng chưa có tài xế', [
                'total_pending_orders' => $pendingOrders->count(),
                'max_waiting_minutes' => $maxWaitingMinutes
            ]);
            
            foreach ($pendingOrders as $order) {
                // Kiểm tra xem có job nào đang xử lý order này không
                $jobLockKey = "processing_order_{$order->id}";
                $existingLock = Cache::get($jobLockKey);
                
                if ($existingLock) {
                    // Kiểm tra lock có quá timeout không
                    if (isset($existingLock['started_at'])) {
                        $lockStartTime = \Carbon\Carbon::parse($existingLock['started_at']);
                        if ($lockStartTime->diffInMinutes(now()) <= 5) {
                            // Lock vẫn còn hiệu lực, bỏ qua order này
                            Log::info('Order đang được xử lý bởi job khác, bỏ qua', [
                                'order_id' => $order->id,
                                'lock_info' => $existingLock
                            ]);
                            continue;
                        } else {
                            // Lock đã timeout, xóa và tiếp tục
                            Cache::forget($jobLockKey);
                            Log::warning('Phát hiện lock timeout trong rà soát, đã xóa', [
                                'order_id' => $order->id,
                                'timeout_minutes' => $lockStartTime->diffInMinutes(now())
                            ]);
                        }
                    }
                }
                
                // Kiểm tra số lần đã thử tìm tài xế
                $failedDrivers = Cache::get("failed_drivers_order_{$order->id}", []);
                $attemptCount = count($failedDrivers) + 1;
                
                // Nếu đã thử quá nhiều lần, bỏ qua
                if ($attemptCount > 30) {
                    Log::warning('Order đã thử quá nhiều lần, bỏ qua rà soát', [
                        'order_id' => $order->id,
                        'attempt_count' => $attemptCount
                    ]);
                    continue;
                }
                
                // Dispatch lại job tìm tài xế
                Log::info('Dispatch lại job tìm tài xế cho order trong rà soát', [
                    'order_id' => $order->id,
                    'order_code' => $order->order_code,
                    'waiting_minutes' => now()->diffInMinutes($order->created_at),
                    'retry_attempt' => $attemptCount
                ]);
                
                self::dispatch($order, $attemptCount)->delay(now()->addSeconds(rand(1, 10)));
            }
            
            Log::info('Hoàn thành rà soát đơn hàng chưa có tài xế', [
                'processed_orders' => $pendingOrders->count()
            ]);
            
        } catch (\Exception $e) {
            Log::error('Lỗi trong quá trình rà soát đơn hàng chưa có tài xế', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
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

    /**
     * Kiểm tra xem đơn hàng mới có nằm trong khu vực lân cận với các đơn hàng hiện tại của tài xế
     */
    private function checkDriverOrdersInSameArea($driverId, $newOrderLat, $newOrderLng, $maxDistance)
    {
        // Lấy tọa độ của các đơn hàng hiện tại của tài xế (loại trừ đơn đang giao)
        $currentOrders = DB::select("
            SELECT o.id, 
                   b.latitude as pickup_latitude, 
                   b.longitude as pickup_longitude,
                   COALESCE(a.latitude, o.guest_latitude) as delivery_latitude,
                   COALESCE(a.longitude, o.guest_longitude) as delivery_longitude
            FROM orders o
            LEFT JOIN branches b ON o.branch_id = b.id
            LEFT JOIN addresses a ON o.address_id = a.id
            WHERE o.driver_id = ?
                AND o.status IN ('awaiting_confirmation', 'confirmed', 'awaiting_driver', 'driver_confirmed', 'waiting_driver_pick_up', 'driver_picked_up')
        ", [$driverId]);

        Log::info('Kiểm tra khu vực đơn hàng cho tài xế:', [
            'driver_id' => $driverId,
            'current_orders_count' => count($currentOrders),
            'new_order_coordinates' => [$newOrderLat, $newOrderLng],
            'max_distance' => $maxDistance
        ]);

        if (empty($currentOrders)) {
            Log::info('Tài xế không có đơn hàng hiện tại, có thể gán');
            return true; // Không có đơn hàng hiện tại, có thể gán
        }

        // Kiểm tra khoảng cách với từng đơn hàng hiện tại
        foreach ($currentOrders as $order) {
            // Kiểm tra khoảng cách với điểm lấy hàng
            $distanceToPickup = $this->haversine(
                $newOrderLat, $newOrderLng,
                $order->pickup_latitude, $order->pickup_longitude
            );

            // Kiểm tra khoảng cách với điểm giao hàng
            $distanceToDelivery = $this->haversine(
                $newOrderLat, $newOrderLng,
                $order->delivery_latitude, $order->delivery_longitude
            );

            Log::info('Kiểm tra khoảng cách với đơn hàng hiện tại:', [
                'current_order_id' => $order->id,
                'distance_to_pickup' => $distanceToPickup,
                'distance_to_delivery' => $distanceToDelivery,
                'max_distance' => $maxDistance
            ]);

            // Nếu đơn mới nằm trong khu vực lân cận (gần điểm lấy hoặc giao của đơn hiện tại)
            if ($distanceToPickup <= $maxDistance || $distanceToDelivery <= $maxDistance) {
                Log::info('Đơn hàng mới nằm trong khu vực lân cận', [
                    'suitable' => true,
                    'reason' => $distanceToPickup <= $maxDistance ? 'gần điểm lấy hàng' : 'gần điểm giao hàng'
                ]);
                return true;
            }
        }

        Log::info('Đơn hàng mới không nằm trong khu vực lân cận', ['suitable' => false]);
        return false; // Đơn mới không nằm trong khu vực lân cận
    }
}