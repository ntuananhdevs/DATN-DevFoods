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

        // CẬP NHẬT: Lấy thu nhập từ các đơn có trạng thái 'delivered' hoặc 'item_received'
        // Vẫn giữ nguyên logic này để hiển thị ban đầu trên dashboard
        $ordersDeliveredToday = Order::where('driver_id', $driver->id)
            ->whereIn('status', ['delivered', 'item_received'])
            ->whereDate('actual_delivery_time', Carbon::today())
            ->get();
        $totalEarnedToday = $ordersDeliveredToday->sum('driver_earning');

        // CẬP NHẬT: Lấy các đơn hàng tài xế đang xử lý
        $processingOrders = Order::where('driver_id', $driver->id)
            ->whereIn('status', ['driver_picked_up', 'in_transit'])
            ->latest()->get();

        // CẬP NHẬT: Lấy các đơn hàng mới đang chờ tài xế
        $availableOrders = Order::whereNull('driver_id')
            ->where('status', 'awaiting_driver')
            ->latest()->take(5)->get();

        return view('driver.dashboard', compact(
            'driver',
            'ordersDeliveredToday',
            'totalEarnedToday',
            'processingOrders',
            'availableOrders',
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
            ->whereIn('status', ['delivered', 'item_received']);

        switch ($filter) {
            case 'today':
                $query->whereDate('actual_delivery_time', Carbon::today());
                break;
            case 'week':
                $query->whereBetween('actual_delivery_time', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                break;
            case 'month':
                $query->whereMonth('actual_delivery_time', Carbon::now()->month);
                break;
                // Case 'all' không cần thêm điều kiện ngày
        }

        $filteredHistory = $query->latest('actual_delivery_time')->get();

        $totalEarnings = $filteredHistory->sum('driver_earning');
        $totalOrders = $filteredHistory->count();

        return view('driver.history', compact('filteredHistory', 'filter', 'totalEarnings', 'totalOrders'));
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
                $query->whereMonth('actual_delivery_time', Carbon::now()->month);
                $label = 'tháng này';
                break;
            default: // today
                $query->whereDate('actual_delivery_time', Carbon::today());
                break;
        }

        $completedOrders = $query->get();

        $stats = [
            'total_earnings' => $completedOrders->sum('driver_earning'),
            'total_orders' => $completedOrders->count(),
            'total_tips' => $completedOrders->sum('tip_amount'), // Giả sử có cột tip_amount
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
    public function toggleStatus(Request $request)
    {
        $request->validate(['is_available' => 'required|boolean']);
        $driver = $request->user('driver'); // Lấy driver đang đăng nhập
        $driver->is_available = $request->is_available;
        $driver->save();

        return response()->json([
            'success' => true,
            'is_available' => (bool)$driver->is_available,
            'message' => $driver->is_available ? 'Bạn đã Online.' : 'Bạn đã Offline.',
        ]);
    }

    // Phương thức để lấy thu nhập
    public function queryEarnings(Request $request)
    {
        $driverId = $request->user('driver')->id;
        $period = $request->query('period', 'today');

        // CẬP NHẬT QUAN TRỌNG: Thay đổi where('status', 'delivered') thành whereIn và sử dụng actual_delivery_time
        $query = Order::where('driver_id', $driverId)->whereIn('status', ['delivered', 'item_received']);

        switch ($period) {
            case 'week':
                $query->whereBetween('actual_delivery_time', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                break;
            case 'month':
                $query->whereMonth('actual_delivery_time', Carbon::now()->month);
                break;
            default: // today
                $query->whereDate('actual_delivery_time', Carbon::today());
                break;
        }

        $totalEarnings = $query->sum('driver_earning');
        $orderCount = $query->count();

        return response()->json([
            'earnings' => number_format($totalEarnings, 0, ',', '.') . ' đ',
            'order_count' => $orderCount . ' đơn đã giao',
        ]);
    }
}
