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
        // 1. Lấy thông tin tài xế đang đăng nhập
        $driver = Auth::guard('driver')->user();

        // 2. Lấy thu nhập từ các đơn đã giao thành công TRONG NGÀY
        $ordersDeliveredToday = Order::where('driver_id', $driver->id)
            ->where('status', 'delivered')
            ->whereDate('delivery_date', Carbon::today())
            ->get();
        $totalEarnedToday = $ordersDeliveredToday->sum('driver_earning');

        // 3. Lấy các đơn hàng tài xế đang xử lý (đang chuẩn bị hoặc đang giao)
        $processingOrders = Order::where('driver_id', $driver->id)
            ->whereIn('status', ['processing', 'delivering'])
            ->latest()->get();
            
        // 4. Lấy 5 đơn hàng mới có sẵn cho tất cả tài xế
        $availableOrders = Order::whereNull('driver_id')
            ->where('status', 'pending')
            ->latest()->take(5)->get();

        // 5. Lấy số thông báo chưa đọc (nếu có)
        // $unreadNotificationsCount = $driver->unreadNotifications()->count();

        // 6. Gửi tất cả dữ liệu đến view
        return view('driver.dashboard', compact(
            'driver',
            'ordersDeliveredToday',
            'totalEarnedToday',
            'processingOrders',
            'availableOrders',
            // 'unreadNotificationsCount'
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

        $query = Order::where('driver_id', $driverId)
                      ->where('status', 'delivered');

        switch ($filter) {
            case 'today':
                $query->whereDate('delivery_date', Carbon::today());
                break;
            case 'week':
                $query->whereBetween('delivery_date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                break;
            case 'month':
                $query->whereMonth('delivery_date', Carbon::now()->month);
                break;
        }

        $filteredHistory = $query->latest('delivery_date')->get();
        
        $totalEarnings = $filteredHistory->sum('driver_earning');
        $totalOrders = $filteredHistory->count();
        // Giả sử đơn hàng có rating từ khách hàng
        // $averageRating = $filteredHistory->avg('rating'); 

        return view('driver.history', compact('filteredHistory', 'filter', 'totalEarnings', 'totalOrders'));
    }

    /**
     * Hiển thị trang thu nhập.
     */
    public function earnings()
    {
        // (Bạn sẽ thêm logic cho trang chi tiết thu nhập ở đây)
        return view('driver.earnings');
    }

    /**
     * Hiển thị thông báo.
     */
    public function notifications()
    {
        $driver = Auth::guard('driver')->user();
        $notifications = $driver->notifications()->get();
        $unreadCount = $driver->unreadNotifications()->count();

        return view('driver.notifications', compact('notifications', 'unreadCount'));
    }

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

        $query = Order::where('driver_id', $driverId)->where('status', 'delivered');
        
        switch ($period) {
            case 'week':
                $query->whereBetween('delivery_date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                break;
            case 'month':
                $query->whereMonth('delivery_date', Carbon::now()->month);
                break;
            default: // today
                $query->whereDate('delivery_date', Carbon::today());
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