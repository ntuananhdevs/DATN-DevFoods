<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Order;
use App\Models\Driver;
use App\Models\Branch;
use Carbon\Carbon;

class DriverController extends Controller
{
    /**
     * Show the driver dashboard/home page.
     */
    public function home()
    {
        $driver = Auth::guard('driver')->user();
        
        if (!$driver) {
            return redirect()->route('driver.login')->with('error', 'Vui lòng đăng nhập để tiếp tục.');
        }
        
        // Get pending orders available for pickup
        $pendingOrders = Order::with(['branch', 'user'])
            ->where('status', 'pending')
            ->whereNull('driver_id')
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get()
            ->map(function ($order) {
                return [
                    'id' => $order->id,
                    'order_time' => $order->created_at->format('H:i d/m/Y'),
                    'status' => 'Chờ nhận',
                    'pickup_branch' => $order->branch->name ?? 'Chi nhánh không xác định',
                    'pickup_address' => $order->branch->address ?? 'Địa chỉ không xác định',
                    'delivery_address' => $order->delivery_address,
                    'customer_name' => $order->customer_name,
                    'customer_phone' => $order->customer_phone,
                    'shipping_fee' => $order->shipping_fee ?? 0,
                    'total_amount' => $order->total_amount ?? 0,
                    'distance' => $this->calculateDistance($order) . ' km',
                    'estimated_time' => $this->estimateDeliveryTime($order) . ' phút',
                ];
            });
        
        $allPendingOrders = Order::where('status', 'pending')
            ->whereNull('driver_id')
            ->get();
        
        // Get order counts for different statuses
        $orderCounts = [
            'pending' => Order::where('status', 'pending')->whereNull('driver_id')->count(),
            'accepted' => Order::where('status', 'accepted')->where('driver_id', $driver->id)->count(),
            'picking_up' => Order::where('status', 'picking_up')->where('driver_id', $driver->id)->count(),
            'delivering' => Order::where('status', 'delivering')->where('driver_id', $driver->id)->count(),
        ];
        
        // Prepare driver data for the view
        $driverData = $this->getDriverData($driver);
        
        // Calculate today's stats
        $todayStats = $this->getTodayStats($driver);
        
        return view('driver.home', compact('pendingOrders', 'allPendingOrders', 'orderCounts', 'driverData', 'todayStats'));
    }
    
    /**
     * Get driver data formatted for views
     */
    private function getDriverData($driver)
    {
        return [
            'id' => $driver->id,
            'name' => $driver->full_name ?? $driver->name ?? 'Tài xế',
            'phone' => $driver->phone_number ?? $driver->phone ?? 'N/A',
            'email' => $driver->email ?? 'N/A',
            'vehicle' => $driver->vehicle_type ?? 'Xe máy',
            'license_plate' => $driver->license_plate ?? 'N/A',
            'id_card_number' => $driver->id_card_number ?? 'N/A',
            'bank_account' => $driver->bank_account ?? 'N/A',
            'is_active' => $driver->is_active ?? true,
            'status' => $driver->is_active ? 'Đang hoạt động' : 'Nghỉ',
            'avatar' => $driver->avatar ?? 'https://via.placeholder.com/80x80',
            'rating' => $driver->rating ?? 5.0,
            'total_orders' => $driver->total_orders ?? 0,
        ];
    }
    
    /**
     * Get today's statistics for driver
     */
    private function getTodayStats($driver)
    {
        $today = Carbon::today();
        
        $todayOrders = Order::where('driver_id', $driver->id)
            ->whereDate('created_at', $today)
            ->get();
        
        $completedOrders = $todayOrders->where('status', 'delivered');
        
        $totalOrders = $todayOrders->count();
        $completedCount = $completedOrders->count();
        $completionRate = $totalOrders > 0 ? round(($completedCount / $totalOrders) * 100, 1) : 0;
        
        return [
            'orders_count' => $totalOrders,
            'completed_count' => $completedCount,
            'earnings' => (float)($completedOrders->sum('driver_earning') ?? 0),
            'distance' => (float)$this->calculateTotalDistance($completedOrders),
            'completion_rate' => $completionRate,
            'average_rating' => $this->calculateAverageRating($driver, $today),
        ];
    }
    
    /**
     * Calculate distance for an order (placeholder implementation)
     */
    private function calculateDistance($order)
    {
        // This should be implemented with actual distance calculation
        // For now, return a random distance between 1-10 km
        return number_format(rand(10, 100) / 10, 1);
    }
    
    /**
     * Estimate delivery time for an order
     */
    private function estimateDeliveryTime($order)
    {
        // Basic estimation: 5 minutes per km + 10 minutes preparation
        $distance = floatval($this->calculateDistance($order));
        return round(($distance * 5) + 10);
    }
    
    /**
     * Calculate total distance for completed orders
     */
    private function calculateTotalDistance($orders)
    {
        // Placeholder implementation
        return $orders->count() * 2.5;
    }
    
    /**
     * Calculate average rating for driver
     */
    private function calculateAverageRating($driver, $date = null)
    {
        // Placeholder implementation
        return 4.8;
    }
    
    /**
     * Show driver profile page.
     */
    public function profile()
    {
        $driver = Auth::guard('driver')->user();
        
        if (!$driver) {
            return redirect()->route('driver.login')->with('error', 'Vui lòng đăng nhập để tiếp tục.');
        }
        
        $driverData = $this->getDriverData($driver);
        $stats = $this->getDriverStats($driver);
        
        return view('driver.profile', compact('driver', 'driverData', 'stats'));
    }
    
    /**
     * Update driver profile.
     */
    public function updateProfile(Request $request)
    {
        $driver = Auth::guard('driver')->user();
        
        if (!$driver) {
            return redirect()->route('driver.login')->with('error', 'Vui lòng đăng nhập để tiếp tục.');
        }
        
        $validator = Validator::make($request->all(), [
            'full_name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20|unique:drivers,phone_number,' . $driver->id,
            'email' => 'nullable|email|max:255|unique:drivers,email,' . $driver->id,
            'vehicle_type' => 'required|string|max:100',
            'license_plate' => 'required|string|max:20',
            'bank_account' => 'nullable|string|max:50',
        ], [
            'full_name.required' => 'Vui lòng nhập họ tên',
            'phone_number.required' => 'Vui lòng nhập số điện thoại',
            'phone_number.unique' => 'Số điện thoại đã được sử dụng',
            'email.email' => 'Email không hợp lệ',
            'email.unique' => 'Email đã được sử dụng',
            'vehicle_type.required' => 'Vui lòng nhập loại xe',
            'license_plate.required' => 'Vui lòng nhập biển số xe',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Vui lòng kiểm tra lại thông tin!');
        }
        
        $driver->update($request->only([
            'full_name', 'phone_number', 'email', 
            'vehicle_type', 'license_plate', 'bank_account'
        ]));
        
        return redirect()->route('driver.profile')
            ->with('success', 'Cập nhật thông tin thành công!');
    }
    
    /**
     * Show driver order history.
     */
    public function history(Request $request)
    {
        $driver = Auth::guard('driver')->user();
        
        if (!$driver) {
            return redirect()->route('driver.login')->with('error', 'Vui lòng đăng nhập để tiếp tục.');
        }
        
        $query = Order::with(['branch', 'user'])
            ->where('driver_id', $driver->id)
            ->whereIn('status', ['delivered', 'cancelled']);
        
        // Filter by status if provided
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }
        
        // Filter by date range if provided
        if ($request->has('from_date') && $request->from_date != '') {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        
        if ($request->has('to_date') && $request->to_date != '') {
            $query->whereDate('created_at', '<=', $request->to_date);
        }
        
        $orders = $query->orderBy('created_at', 'desc')->paginate(20);
        
        $stats = [
            'total_orders' => Order::where('driver_id', $driver->id)->whereIn('status', ['delivered', 'cancelled'])->count(),
            'delivered_orders' => Order::where('driver_id', $driver->id)->where('status', 'delivered')->count(),
            'cancelled_orders' => Order::where('driver_id', $driver->id)->where('status', 'cancelled')->count(),
        ];
        
        return view('driver.history', compact('orders', 'stats'));
    }
    
    /**
     * Show driver earnings page.
     */
    public function earnings(Request $request)
    {
        $driver = Auth::guard('driver')->user();
        
        if (!$driver) {
            return redirect()->route('driver.login')->with('error', 'Vui lòng đăng nhập để tiếp tục.');
        }
        
        $today = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();
        $thisYear = Carbon::now()->startOfYear();
        
        $earnings = [
            'today' => Order::where('driver_id', $driver->id)
                ->where('status', 'delivered')
                ->whereDate('delivered_at', $today)
                ->sum('driver_earning') ?? 0,
                
            'this_month' => Order::where('driver_id', $driver->id)
                ->where('status', 'delivered')
                ->where('delivered_at', '>=', $thisMonth)
                ->sum('driver_earning') ?? 0,
                
            'this_year' => Order::where('driver_id', $driver->id)
                ->where('status', 'delivered')
                ->where('delivered_at', '>=', $thisYear)
                ->sum('driver_earning') ?? 0,
                
            'total' => Order::where('driver_id', $driver->id)
                ->where('status', 'delivered')
                ->sum('driver_earning') ?? 0,
        ];
        
        // Get recent earnings (last 30 days)
        $recentEarnings = Order::where('driver_id', $driver->id)
            ->where('status', 'delivered')
            ->where('delivered_at', '>=', Carbon::now()->subDays(30))
            ->selectRaw('DATE(delivered_at) as date, SUM(driver_earning) as total')
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->get();
        
        return view('driver.earnings', compact('earnings', 'recentEarnings'));
    }
    
    /**
     * Get driver statistics
     */
    private function getDriverStats($driver)
    {
        return [
            'total_orders' => Order::where('driver_id', $driver->id)->count(),
            'completed_orders' => Order::where('driver_id', $driver->id)->where('status', 'delivered')->count(),
            'cancelled_orders' => Order::where('driver_id', $driver->id)->where('status', 'cancelled')->count(),
            'total_earnings' => Order::where('driver_id', $driver->id)->where('status', 'delivered')->sum('driver_earning') ?? 0,
            'average_rating' => $this->calculateAverageRating($driver),
            'completion_rate' => $this->calculateCompletionRate($driver),
        ];
    }
    
    /**
     * Calculate completion rate for driver
     */
    private function calculateCompletionRate($driver)
    {
        $totalOrders = Order::where('driver_id', $driver->id)->whereIn('status', ['delivered', 'cancelled'])->count();
        $completedOrders = Order::where('driver_id', $driver->id)->where('status', 'delivered')->count();
        
        return $totalOrders > 0 ? round(($completedOrders / $totalOrders) * 100, 1) : 0;
    }
    
    // API Methods for mobile app
    
    /**
     * Get driver profile data (API)
     */
    public function getProfile()
    {
        $driver = Auth::guard('driver')->user();
        
        if (!$driver) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        
        $driverData = $this->getDriverData($driver);
        $stats = $this->getDriverStats($driver);
        
        return response()->json([
            'success' => true,
            'data' => [
                'driver' => $driverData,
                'stats' => $stats,
            ]
        ]);
    }
    
    /**
     * Get driver statistics (API)
     */
    public function getStats()
    {
        $driver = Auth::guard('driver')->user();
        
        if (!$driver) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        
        $todayStats = $this->getTodayStats($driver);
        $overallStats = $this->getDriverStats($driver);
        
        return response()->json([
            'success' => true,
            'data' => [
                'today' => $todayStats,
                'overall' => $overallStats,
            ]
        ]);
    }
    
    /**
     * Show driver notifications page
     */
    public function notifications()
    {
        $driver = Auth::guard('driver')->user();
        
        if (!$driver) {
            return redirect()->route('driver.login')->with('error', 'Vui lòng đăng nhập để tiếp tục.');
        }
        
        // Sample notifications data - replace with actual notification logic
        $notifications = [
            [
                'id' => 1,
                'title' => 'Đơn hàng mới',
                'message' => 'Bạn có đơn hàng mới cần giao tại quận 1',
                'type' => 'order',
                'created_at' => now()->subMinutes(5),
                'read' => false
            ],
            [
                'id' => 2,
                'title' => 'Cập nhật hệ thống',
                'message' => 'Hệ thống sẽ bảo trì từ 2:00 - 4:00 sáng ngày mai',
                'type' => 'system',
                'created_at' => now()->subHours(2),
                'read' => true
            ],
            [
                'id' => 3,
                'title' => 'Thưởng hoàn thành',
                'message' => 'Bạn đã hoàn thành 50 đơn hàng trong tháng này',
                'type' => 'achievement',
                'created_at' => now()->subDays(1),
                'read' => false
            ]
        ];
        
        $unreadCount = collect($notifications)->where('read', false)->count();
        
        return view('driver.notifications', compact('notifications', 'unreadCount'));
    }
}