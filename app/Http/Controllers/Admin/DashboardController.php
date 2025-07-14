<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\ProductReview;

class DashboardController extends Controller
{
    // Dashboard - Analytics
    public function dashboard()
    {
        // Lấy ngày hiện tại
        $today = now()->startOfDay();
        $weekStart = now()->startOfWeek();
        $monthStart = now()->startOfMonth();
        $now = now();

        // Tổng số đơn hàng theo trạng thái
        $orderStatuses = [
            'new', 'awaiting_confirmation', 'confirmed', 'awaiting_driver', 'driver_picked_up', 'in_transit', 'delivered', 'item_received', 'cancelled', 'failed_delivery', 'delivery_incomplete', 'pending_refund', 'investigating', 'waiting_for_confirmation'
        ];
        $pendingStatuses = ['new', 'awaiting_confirmation', 'confirmed', 'awaiting_driver', 'waiting_for_confirmation'];

        // Đếm đơn hàng hôm nay/tuần/tháng theo trạng thái
        $ordersToday = \App\Models\Order::whereDate('order_date', $today)->get();
        $ordersWeek = \App\Models\Order::whereBetween('order_date', [$weekStart, $now])->get();
        $ordersMonth = \App\Models\Order::whereBetween('order_date', [$monthStart, $now])->get();

        $orderCountByStatus = [
            'today' => $ordersToday->groupBy('status')->map->count(),
            'week' => $ordersWeek->groupBy('status')->map->count(),
            'month' => $ordersMonth->groupBy('status')->map->count(),
        ];

        // Doanh thu đã thanh toán (chỉ tính đơn đã hoàn thành/thanh toán)
        $paidStatuses = ['delivered', 'item_received'];
        $revenue = [
            'today' => $ordersToday->whereIn('status', $paidStatuses)->sum('total_amount'),
            'week' => $ordersWeek->whereIn('status', $paidStatuses)->sum('total_amount'),
            'month' => $ordersMonth->whereIn('status', $paidStatuses)->sum('total_amount'),
        ];

        // Số đơn mỗi chi nhánh (tháng)
        $ordersByBranch = \App\Models\Order::whereBetween('order_date', [$monthStart, $now])
            ->selectRaw('branch_id, count(*) as total')
            ->groupBy('branch_id')
            ->with('branch')
            ->get();
        // Chuẩn hóa dữ liệu cho biểu đồ chi nhánh
        $branchChartData = $ordersByBranch->map(function($row) {
            return [
                'branch' => $row->branch?->name ?? 'Không xác định',
                'total' => $row->total
            ];
        });

        // Số đơn mỗi tài xế (tháng)
        $ordersByDriver = \App\Models\Order::whereBetween('order_date', [$monthStart, $now])
            ->whereNotNull('driver_id')
            ->selectRaw('driver_id, count(*) as total')
            ->groupBy('driver_id')
            ->with('driver')
            ->get();

        // Đơn hủy/lỗi (tháng)
        $cancelledOrders = \App\Models\Order::where('status', 'cancelled')
            ->whereBetween('order_date', [$monthStart, $now])
            ->with(['cancellation', 'branch', 'customer'])
            ->get();

        // Đơn hàng theo khung giờ (hôm nay)
        $ordersByHourRaw = \App\Models\Order::whereDate('order_date', $today)
            ->selectRaw('HOUR(order_date) as hour, count(*) as total')
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();
        // Đảm bảo đủ 24 giờ cho biểu đồ
        $ordersByHour = collect(range(0,23))->map(function($h) use ($ordersByHourRaw) {
            $row = $ordersByHourRaw->firstWhere('hour', $h);
            return [
                'hour' => $h,
                'total' => $row ? $row->total : 0
            ];
        });

        // Top 5 món ăn bán chạy nhất (theo số lượng, tháng)
        $topProducts = \App\Models\OrderItem::selectRaw('product_variant_id, SUM(quantity) as total')
            ->whereHas('order', function($q) use ($monthStart, $now) {
                $q->whereBetween('order_date', [$monthStart, $now]);
            })
            ->groupBy('product_variant_id')
            ->orderByDesc('total')
            ->with(['productVariant.product'])
            ->limit(5)
            ->get()
            ->map(function($item) {
                return [
                    'name' => $item->productVariant?->product?->name ?? 'Không xác định',
                    'total' => $item->total
                ];
            });

        // Số đơn đang chờ xử lý (tất cả thời gian, hoặc tháng)
        $pendingOrdersCount = \App\Models\Order::whereIn('status', $pendingStatuses)
            ->whereBetween('order_date', [$monthStart, $now])
            ->count();

        return view('admin.statistics.dashboard', [
            'orderCountByStatus' => $orderCountByStatus,
            'revenue' => $revenue,
            'ordersByBranch' => $ordersByBranch,
            'ordersByDriver' => $ordersByDriver,
            'cancelledOrders' => $cancelledOrders,
            'ordersByHour' => $ordersByHour,
            'branchChartData' => $branchChartData,
            'topProducts' => $topProducts,
            'pendingOrdersCount' => $pendingOrdersCount,
        ]);
    }


    public function branchStatistics(Request $request)
    {
        // Lấy danh sách chi nhánh
        $branches = \App\Models\Branch::all();
        $branchId = $request->input('branch_id', $branches->first()?->id);
        $selectedBranch = $branches->where('id', $branchId)->first();

        // Tổng đơn hàng theo ngày/tháng/năm cho chi nhánh
        $today = now()->startOfDay();
        $monthStart = now()->startOfMonth();
        $yearStart = now()->startOfYear();
        $now = now();

        $ordersQuery = \App\Models\Order::where('branch_id', $branchId);
        $ordersToday = (clone $ordersQuery)->whereDate('order_date', $today)->count();
        $ordersMonth = (clone $ordersQuery)->whereBetween('order_date', [$monthStart, $now])->count();
        $ordersYear = (clone $ordersQuery)->whereBetween('order_date', [$yearStart, $now])->count();

        // Tổng doanh thu (đơn đã giao/thành công)
        $paidStatuses = ['delivered', 'item_received'];
        $revenue = [
            'today' => (clone $ordersQuery)->whereDate('order_date', $today)->whereIn('status', $paidStatuses)->sum('total_amount'),
            'month' => (clone $ordersQuery)->whereBetween('order_date', [$monthStart, $now])->whereIn('status', $paidStatuses)->sum('total_amount'),
            'year' => (clone $ordersQuery)->whereBetween('order_date', [$yearStart, $now])->whereIn('status', $paidStatuses)->sum('total_amount'),
        ];

        // Biểu đồ đơn hàng theo ngày trong tháng
        $ordersByDayRaw = (clone $ordersQuery)
            ->whereBetween('order_date', [$monthStart, $now])
            ->selectRaw('DAY(order_date) as day, count(*) as total')
            ->groupBy('day')
            ->orderBy('day')
            ->get();
        $daysInMonth = now()->daysInMonth;
        $ordersByDay = collect(range(1, $daysInMonth))->map(function($d) use ($ordersByDayRaw) {
            $row = $ordersByDayRaw->firstWhere('day', $d);
            return [
                'day' => $d,
                'total' => $row ? $row->total : 0
            ];
        });

        // Món ăn bán chạy nhất tại chi nhánh (top 1)
        $topProduct = \App\Models\OrderItem::selectRaw('product_variant_id, SUM(quantity) as total')
            ->whereHas('order', function($q) use ($branchId, $monthStart, $now) {
                $q->where('branch_id', $branchId)
                  ->whereBetween('order_date', [$monthStart, $now]);
            })
            ->groupBy('product_variant_id')
            ->orderByDesc('total')
            ->with(['productVariant.product'])
            ->first();
        $topProductName = $topProduct?->productVariant?->product?->name ?? 'Không xác định';
        $topProductTotal = $topProduct?->total ?? 0;

        // Tỉ lệ đơn bị huỷ, đơn giao trễ
        $totalOrders = (clone $ordersQuery)->whereBetween('order_date', [$monthStart, $now])->count();
        $cancelledOrders = (clone $ordersQuery)->where('status', 'cancelled')->whereBetween('order_date', [$monthStart, $now])->count();
        $lateOrders = (clone $ordersQuery)
            ->whereIn('status', $paidStatuses)
            ->whereRaw('actual_delivery_time > estimated_delivery_time')
            ->whereBetween('order_date', [$monthStart, $now])
            ->count();
        $cancelledRate = $totalOrders > 0 ? round($cancelledOrders / $totalOrders * 100, 2) : 0;
        $lateRate = $totalOrders > 0 ? round($lateOrders / $totalOrders * 100, 2) : 0;

        return view('admin.statistics.branch', [
            'branches' => $branches,
            'selectedBranch' => $selectedBranch,
            'ordersToday' => $ordersToday,
            'ordersMonth' => $ordersMonth,
            'ordersYear' => $ordersYear,
            'revenue' => $revenue,
            'ordersByDay' => $ordersByDay,
            'topProductName' => $topProductName,
            'topProductTotal' => $topProductTotal,
            'cancelledRate' => $cancelledRate,
            'lateRate' => $lateRate,
        ]);
    }

    public function driverStatistics(Request $request)
    {
        $drivers = \App\Models\Driver::all();
        $branches = \App\Models\Branch::all();
        $branchId = $request->input('branch_id');
        $driverId = $request->input('driver_id');
        $from = $request->input('from', now()->startOfMonth()->toDateString());
        $to = $request->input('to', now()->toDateString());

        $ordersQuery = \App\Models\Order::query()
            ->whereBetween('order_date', [$from, $to])
            ->whereNotNull('driver_id');
        if ($branchId) {
            $ordersQuery->where('branch_id', $branchId);
        }
        if ($driverId) {
            $ordersQuery->where('driver_id', $driverId);
        }

        $paidStatuses = ['delivered', 'item_received'];
        $orders = $ordersQuery->with(['driver', 'branch', 'cancellation'])->get();

        // Gom theo tài xế
        $stats = $orders->groupBy('driver_id')->map(function($orders, $driverId) use ($paidStatuses) {
            $driver = $orders->first()->driver;
            $branch = $orders->first()->branch;
            $delivered = $orders->whereIn('status', $paidStatuses);
            $deliveredCount = $delivered->count();
            $deliveredToday = $delivered->where('order_date', '>=', now()->startOfDay())->count();
            $deliveredMonth = $delivered->where('order_date', '>=', now()->startOfMonth())->count();
            $deliveredYear = $delivered->where('order_date', '>=', now()->startOfYear())->count();
            $onTime = $delivered->where(function($o) {
                return $o->actual_delivery_time <= $o->estimated_delivery_time;
            })->count();
            $onTimeRate = $deliveredCount > 0 ? round($onTime / $deliveredCount * 100, 2) : 0;
            $cancelledByDriver = $orders->where('status', 'cancelled')->where('cancellation.cancelled_by_type', 'driver')->count();
            $revenue = $delivered->sum('total_amount');
            // Đánh giá trung bình
            $reviewAvg = \App\Models\ProductReview::whereIn('order_id', $delivered->pluck('id'))->avg('rating');
            return [
                'driver' => $driver,
                'branch' => $branch,
                'delivered_today' => $deliveredToday,
                'delivered_month' => $deliveredMonth,
                'delivered_year' => $deliveredYear,
                'on_time_rate' => $onTimeRate,
                'cancelled_by_driver' => $cancelledByDriver,
                'revenue' => $revenue,
                'review_avg' => round($reviewAvg, 2),
            ];
        });

        return view('admin.statistics.driver', [
            'drivers' => $drivers,
            'branches' => $branches,
            'stats' => $stats,
            'branchId' => $branchId,
            'driverId' => $driverId,
            'from' => $from,
            'to' => $to,
        ]);
    }

    public function orderStatistics(Request $request)
    {
        $branches = \App\Models\Branch::all();
        $branchId = $request->input('branch_id');
        $status = $request->input('status');
        $from = $request->input('from', now()->startOfMonth()->toDateString());
        $to = $request->input('to', now()->toDateString());

        $ordersQuery = \App\Models\Order::query()
            ->whereBetween('order_date', [$from, $to]);
        if ($branchId) {
            $ordersQuery->where('branch_id', $branchId);
        }
        if ($status) {
            $ordersQuery->where('status', $status);
        }
        $orders = $ordersQuery->get();

        // Tổng số đơn theo ngày/tháng/năm
        $today = now()->startOfDay();
        $monthStart = now()->startOfMonth();
        $yearStart = now()->startOfYear();
        $totalToday = $orders->where('order_date', '>=', $today)->count();
        $totalMonth = $orders->where('order_date', '>=', $monthStart)->count();
        $totalYear = $orders->where('order_date', '>=', $yearStart)->count();

        // Phân loại đơn theo trạng thái
        $statusGroups = [
            'processing' => ['new', 'awaiting_confirmation', 'confirmed', 'awaiting_driver', 'driver_picked_up', 'in_transit'],
            'delivered' => ['delivered', 'item_received'],
            'cancelled' => ['cancelled', 'failed_delivery', 'delivery_incomplete'],
        ];
        $orderStatusCount = [
            'processing' => $orders->whereIn('status', $statusGroups['processing'])->count(),
            'delivered' => $orders->whereIn('status', $statusGroups['delivered'])->count(),
            'cancelled' => $orders->whereIn('status', $statusGroups['cancelled'])->count(),
        ];

        // Biểu đồ số đơn theo từng ngày trong khoảng
        $ordersByDayRaw = (clone $ordersQuery)
            ->selectRaw('DATE(order_date) as day, count(*) as total')
            ->groupBy('day')
            ->orderBy('day')
            ->get();
        $dateRange = collect();
        $start = \Carbon\Carbon::parse($from);
        $end = \Carbon\Carbon::parse($to);
        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
            $dateRange->push($date->toDateString());
        }
        $ordersByDay = $dateRange->map(function($d) use ($ordersByDayRaw) {
            $row = $ordersByDayRaw->firstWhere('day', $d);
            return [
                'day' => $d,
                'total' => $row ? $row->total : 0
            ];
        });

        // Số đơn theo phương thức thanh toán
        $paymentMethods = ['cash' => 'Tiền mặt', 'bank_transfer' => 'Chuyển khoản', 'e_wallet' => 'Ví điện tử'];
        $ordersByPayment = [];
        foreach ($paymentMethods as $key => $label) {
            $ordersByPayment[$key] = $orders->where('payment_method', $key)->count();
        }

        // Đơn hàng nổi bật
        $largestOrder = $orders->sortByDesc('total_amount')->first();
        $longestOrder = $orders->filter(function($o) {
            return $o->actual_delivery_time && $o->estimated_delivery_time;
        })->sortByDesc(function($o) {
            return $o->actual_delivery_time->diffInMinutes($o->estimated_delivery_time, false);
        })->first();

        return view('admin.statistics.order', [
            'branches' => $branches,
            'orders' => $orders,
            'totalToday' => $totalToday,
            'totalMonth' => $totalMonth,
            'totalYear' => $totalYear,
            'orderStatusCount' => $orderStatusCount,
            'ordersByDay' => $ordersByDay,
            'ordersByPayment' => $ordersByPayment,
            'paymentMethods' => $paymentMethods,
            'largestOrder' => $largestOrder,
            'longestOrder' => $longestOrder,
            'branchId' => $branchId,
            'status' => $status,
            'from' => $from,
            'to' => $to,
        ]);
    }

    public function foodStatistics(Request $request)
    {
        $branches = \App\Models\Branch::all();
        $branchId = $request->input('branch_id');
        $from = $request->input('from', now()->startOfMonth()->toDateString());
        $to = $request->input('to', now()->toDateString());
        $sort = $request->input('sort', 'revenue'); // revenue|quantity

        $orderItemsQuery = \App\Models\OrderItem::query()
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->whereBetween('orders.order_date', [$from, $to]);
        if ($branchId) {
            $orderItemsQuery->where('orders.branch_id', $branchId);
        }

        // Thống kê sản phẩm: chỉ lấy order_items có product_variant_id khác null (bỏ qua combo)
        $items = (clone $orderItemsQuery)
            ->whereNotNull('order_items.product_variant_id')
            ->selectRaw('order_items.product_variant_id, SUM(order_items.quantity) as total_quantity, SUM(order_items.total_price) as total_revenue')
            ->groupBy('order_items.product_variant_id')
            ->with(['productVariant.product'])
            ->get();

        $foods = $items->map(function($item) {
            return [
                'name' => $item->productVariant?->product?->name ?? 'Không xác định',
                'variant' => $item->productVariant?->name,
                'total_quantity' => $item->total_quantity,
                'total_revenue' => $item->total_revenue,
                'product_id' => $item->productVariant?->product?->id,
            ];
        });
        $foods = $foods->sortByDesc($sort == 'quantity' ? 'total_quantity' : 'total_revenue')->values();

        $bestFood = $foods->first();
        $worstFood = $foods->last();

        // Biểu đồ món theo ngày
        $itemsByDay = (clone $orderItemsQuery)
            ->selectRaw('DATE(orders.order_date) as day, SUM(order_items.quantity) as total')
            ->groupBy('day')
            ->orderBy('day')
            ->get();
        $dateRange = collect();
        $start = \Carbon\Carbon::parse($from);
        $end = \Carbon\Carbon::parse($to);
        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
            $dateRange->push($date->toDateString());
        }
        $foodsByDay = $dateRange->map(function($d) use ($itemsByDay) {
            $row = $itemsByDay->firstWhere('day', $d);
            return [
                'day' => $d,
                'total' => $row ? $row->total : 0
            ];
        });

        // Biểu đồ món theo giờ
        $itemsByHour = (clone $orderItemsQuery)
            ->selectRaw('HOUR(orders.order_date) as hour, SUM(order_items.quantity) as total')
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();
        $foodsByHour = collect(range(0,23))->map(function($h) use ($itemsByHour) {
            $row = $itemsByHour->firstWhere('hour', $h);
            return [
                'hour' => $h,
                'total' => $row ? $row->total : 0
            ];
        });

        // So sánh món ăn giữa các chi nhánh
        $compareByBranch = (clone $orderItemsQuery)
            ->selectRaw('order_items.product_variant_id, orders.branch_id, SUM(order_items.quantity) as total')
            ->groupBy('order_items.product_variant_id', 'orders.branch_id')
            ->with(['productVariant.product'])
            ->get();

        return view('admin.statistics.food', [
            'branches' => $branches,
            'foods' => $foods,
            'bestFood' => $bestFood,
            'worstFood' => $worstFood,
            'foodsByDay' => $foodsByDay,
            'foodsByHour' => $foodsByHour,
            'compareByBranch' => $compareByBranch,
            'branchId' => $branchId,
            'from' => $from,
            'to' => $to,
            'sort' => $sort,
        ]);
    }

    public function customerStatistics(Request $request)
    {
        $from = $request->input('from', now()->startOfMonth()->toDateString());
        $to = $request->input('to', now()->toDateString());
        $minOrders = $request->input('min_orders', null);
        $area = $request->input('area', null);
        // Tổng số user mới trong tuần/tháng
        $weekStart = now()->startOfWeek()->toDateString();
        $newUsersWeek = User::whereHas('roles', function($q){ $q->where('name', 'customer'); })
            ->whereBetween('created_at', [$weekStart, $to]);
        $newUsersMonth = User::whereHas('roles', function($q){ $q->where('name', 'customer'); })
            ->whereBetween('created_at', [$from, $to]);
        if ($area) {
            $newUsersWeek->where('area', $area);
            $newUsersMonth->where('area', $area);
        }
        if ($minOrders) {
            $newUsersWeek->whereHas('orders', function($q) use ($minOrders) {
                $q->havingRaw('COUNT(*) >= ?', [$minOrders]);
            });
            $newUsersMonth->whereHas('orders', function($q) use ($minOrders) {
                $q->havingRaw('COUNT(*) >= ?', [$minOrders]);
            });
        }
        $newUsersWeekCount = $newUsersWeek->count();
        $newUsersMonthCount = $newUsersMonth->count();
        // Top 10 khách mua nhiều nhất
        $topCustomers = \App\Models\Order::selectRaw('customer_id, COUNT(*) as total_orders, SUM(total_amount) as total_spent')
            ->whereBetween('order_date', [$from, $to])
            ->when($area, function($q) use ($area) {
                $q->whereHas('customer', function($q2) use ($area) { $q2->where('area', $area); });
            })
            ->groupBy('customer_id')
            ->orderByDesc('total_orders')
            ->with('customer')
            ->limit(10)
            ->get();
        // Số đơn trung bình mỗi người
        $totalCustomers = User::whereHas('roles', function($q){ $q->where('name', 'customer'); })
            ->when($area, function($q) use ($area) { $q->where('area', $area); })
            ->count();
        $totalOrders = \App\Models\Order::whereBetween('order_date', [$from, $to])
            ->when($area, function($q) use ($area) {
                $q->whereHas('customer', function($q2) use ($area) { $q2->where('area', $area); });
            })
            ->count();
        $avgOrdersPerUser = $totalCustomers > 0 ? round($totalOrders / $totalCustomers, 2) : 0;
        // Tỉ lệ khách quay lại (có >1 đơn trong khoảng)
        $repeatCustomers = \App\Models\Order::select('customer_id')
            ->whereBetween('order_date', [$from, $to])
            ->groupBy('customer_id')
            ->havingRaw('COUNT(*) > 1')
            ->when($area, function($q) use ($area) {
                $q->whereHas('customer', function($q2) use ($area) { $q2->where('area', $area); });
            })
            ->count();
        $repeatRate = $totalCustomers > 0 ? round($repeatCustomers / $totalCustomers * 100, 2) : 0;
        // Mức chi tiêu trung bình mỗi khách
        $totalRevenue = \App\Models\Order::whereBetween('order_date', [$from, $to])
            ->when($area, function($q) use ($area) {
                $q->whereHas('customer', function($q2) use ($area) { $q2->where('area', $area); });
            })
            ->sum('total_amount');
        $avgSpending = $totalCustomers > 0 ? round($totalRevenue / $totalCustomers, 0) : 0;
        // Đánh giá người dùng cho hệ thống
        $avgRating = ProductReview::whereNull('product_id')
            ->whereBetween('review_date', [$from, $to])
            ->avg('rating');
        return view('admin.statistics.customer', [
            'newUsersWeekCount' => $newUsersWeekCount,
            'newUsersMonthCount' => $newUsersMonthCount,
            'topCustomers' => $topCustomers,
            'avgOrdersPerUser' => $avgOrdersPerUser,
            'repeatRate' => $repeatRate,
            'avgSpending' => $avgSpending,
            'avgRating' => $avgRating,
            'from' => $from,
            'to' => $to,
            'area' => $area,
            'minOrders' => $minOrders,
        ]);
    }
}
