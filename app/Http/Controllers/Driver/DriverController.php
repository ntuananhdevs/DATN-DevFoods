<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Driver;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DriverController extends Controller
{
    /**
     * Hiển thị trang dashboard chính của tài xế.
     */
    public function home()
    {
        $driver = Auth::guard('driver')->user();
        $driverId = $driver->id;

        // Lấy thu nhập từ các đơn có trạng thái 'delivered' hoặc 'item_received' hôm nay
        $ordersDeliveredToday = Order::where('driver_id', $driver->id)
            ->whereIn('status', ['delivered', 'item_received'])
            ->whereDate('actual_delivery_time', Carbon::today())
            ->get();
        $totalEarnedToday = $ordersDeliveredToday->sum('driver_earning');
        $deliveredOrdersCountToday = $ordersDeliveredToday->count();

        // Tính thu nhập trung bình mỗi đơn hôm nay
        $averageEarningPerOrder = $deliveredOrdersCountToday > 0 ?
            $totalEarnedToday / $deliveredOrdersCountToday : 0;

        // Lấy các đơn hàng tài xế đang xử lý
        $processingOrders = Order::where('driver_id', $driver->id)
            ->whereIn('status', ['driver_assigned', 'driver_confirmed', 'driver_picked_up', 'in_transit'])
            ->select([
                'orders.*',
                'delivery_address_line_snapshot',
                'delivery_ward_snapshot', 
                'delivery_district_snapshot',
                'delivery_province_snapshot',
                'delivery_phone_snapshot',
                'delivery_recipient_name_snapshot'
            ])
            ->latest()->get();

        // Lấy các đơn hàng mới đang chờ tài xế
        $availableOrders = Order::where(function ($q) use ($driverId) {
            $q->whereNull('driver_id')
                ->orWhere(function ($q2) use ($driverId) {
                    $q2->where('driver_id', $driverId)
                        ->where('status', 'awaiting_driver');
                });
        })
            ->where('status', 'awaiting_driver')
            ->select([
                'orders.*',
                'delivery_address_line_snapshot',
                'delivery_ward_snapshot', 
                'delivery_district_snapshot',
                'delivery_province_snapshot',
                'delivery_phone_snapshot',
                'delivery_recipient_name_snapshot'
            ])
            ->get();

        // Kiểm tra điều kiện ghép đơn
        $batchableOrdersInfo = [];
        foreach ($processingOrders as $order) {
            if (in_array($order->status, ['driver_assigned', 'driver_confirmed', 'waiting_driver_pick_up', 'driver_picked_up'])) {
                $batchableOrders = $order->getBatchableOrders();
                if ($batchableOrders->count() > 0) {
                    $batchableOrdersInfo[$order->id] = [
                        'order' => $order,
                        'batchable_count' => $batchableOrders->count(),
                        'batchable_orders' => $batchableOrders
                    ];
                }
            }
        }

        return view('driver.dashboard', compact(
            'driver',
            'totalEarnedToday',
            'deliveredOrdersCountToday',
            'averageEarningPerOrder',
            'processingOrders',
            'availableOrders',
            'batchableOrdersInfo'
        ));
    }


    /**
     * Hiển thị trang hồ sơ tài xế.
     */
    public function profile()
    {
        return view('driver.profile', ['driver' => Auth::guard('driver')->user()]);
    }

    /**
     * Xử lý cập nhật hồ sơ.
     */
    public function updateProfile(Request $request)
    {
        // (Bạn sẽ thêm logic validate và cập nhật ở đây)
        return response()->json(['message' => 'Hồ sơ đã được cập nhật.']);
    }

    /**
     * Hiển thị lịch sử đơn hàng đã hoàn thành.
     */
    public function history(Request $request)
    {
        $driverId = Auth::guard('driver')->id();
        $filter = $request->query('filter', 'all');

        // CẬP NHẬT: Sử dụng cả 'delivered' và 'item_received' và 'actual_delivery_time'
        $query = Order::where('driver_id', $driverId)
            ->whereIn('status', ['delivered', 'item_received']); //

        switch ($filter) { //
            case 'today':
                $query->whereDate('actual_delivery_time', Carbon::today()); //
                break;
            case 'week':
                $query->whereBetween('actual_delivery_time', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]); //
                break;
            case 'month':
                $query->whereMonth('actual_delivery_time', Carbon::now()->month)
                    ->whereYear('actual_delivery_time', Carbon::now()->year);
                break;
                // Case 'all' không cần thêm điều kiện ngày
        }

        $filteredHistory = $query->latest('actual_delivery_time')->get();
        $totalEarnings = $filteredHistory->sum('driver_earning');

        return view('driver.history', compact('filteredHistory', 'totalEarnings', 'filter'));
    }

    /**
     * Hiển thị trang thu nhập.
     */
    public function earnings(Request $request)
    {
        $driverId = Auth::guard('driver')->id();
        $filter = $request->query('filter', 'today');

        // CẬP NHẬT: Sử dụng cả 'delivered' và 'item_received' và 'actual_delivery_time'
        $query = Order::where('driver_id', $driverId)->whereIn('status', ['delivered', 'item_received']);
        $label = 'hôm nay';

        switch ($filter) {
            case 'week':
                $query->whereBetween('actual_delivery_time', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                $label = 'tuần này';
                break;
            case 'month':
                $query->whereMonth('actual_delivery_time', Carbon::now()->month)
                    ->whereYear('actual_delivery_time', Carbon::now()->year);
                $label = 'tháng này';
                break;
            default: // today
                $query->whereDate('actual_delivery_time', Carbon::today());
                break;
        }

        $completedOrders = $query->get();

        // Tính toán tips từ delivery_fee (giả sử 10% delivery_fee là tips)
        $totalTips = $completedOrders->sum('delivery_fee') * 0.1;

        $stats = [
            'total_earnings' => $completedOrders->sum('driver_earning'),
            'total_orders' => $completedOrders->count(),
            'total_tips' => $totalTips,
            'avg_per_order' => $completedOrders->count() > 0 ? $completedOrders->sum('driver_earning') / $completedOrders->count() : 0,
        ];

        return view('driver.earnings', compact('stats', 'filter', 'label'));
    }

    /**
     * Hiển thị thông báo.
     */
    // public function notifications()
    // {
    //     $driver = Auth::guard('driver')->user();
    //     $notifications = $driver->notifications()->get();
    //     $unreadCount = $driver->unreadNotifications()->count();

    //     return view('driver.notifications', compact('notifications', 'unreadCount'));
    // }

    // --- Các phương thức API ---

    /**
     * API để bật/tắt trạng thái hoạt động.
     */
    // Phương thức để bật/tắt trạng thái
    public function setAvailability(Request $request)
    { 
        $driver = Auth::guard('driver')->user(); 
        if (!$driver) { 
            return response()->json(['success' => false, 'message' => 'Không tìm thấy tài xế đang đăng nhập'], 404); 
        }

        $request->validate([ 
            'is_available' => 'required|boolean', 
        ]);

        // Lưu trạng thái cũ trước khi cập nhật
        $oldStatus = $driver->driver_status;
        
        // Cập nhật trạng thái
        $driver->is_available = $request->is_available; 
        $driver->save(); 
        
        // Phát sự kiện cập nhật trạng thái tài xế
        event(new \App\Events\Driver\DriverStatusUpdated($driver, $oldStatus));

        return response()->json([ 
            'success' => true,
            'is_available' => (bool)$driver->is_available, 
            'status' => $driver->driver_status,
            'message' => $driver->is_available ? 'Bạn đã sẵn sàng nhận đơn.' : 'Bạn đã ngừng nhận đơn.', 
        ]);
    }
}
