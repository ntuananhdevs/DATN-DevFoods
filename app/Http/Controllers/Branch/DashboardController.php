<?php

namespace App\Http\Controllers\Branch;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Branch;
use App\Models\Driver;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $manager = Auth::guard('manager')->user();
        $branch = $manager ? $manager->branch : null;
        if (!$branch) {
            abort(403, 'Bạn chưa được gán chi nhánh.');
        }

        $today = now()->startOfDay();
        $weekStart = now()->startOfWeek();
        $monthStart = now()->startOfMonth();
        $now = now();

        $orderStatuses = [
            'new', 'awaiting_confirmation', 'confirmed', 'awaiting_driver', 'driver_picked_up', 'in_transit', 'delivered', 'item_received', 'cancelled', 'failed_delivery', 'delivery_incomplete', 'pending_refund', 'investigating', 'waiting_for_confirmation'
        ];
        $pendingStatuses = ['new', 'awaiting_confirmation', 'confirmed', 'awaiting_driver', 'waiting_for_confirmation'];
        $paidStatuses = ['delivered', 'item_received'];

        // Đếm đơn hàng hôm nay/tuần/tháng theo trạng thái
        $ordersToday = Order::where('branch_id', $branch->id)->whereDate('order_date', $today)->get();
        $ordersWeek = Order::where('branch_id', $branch->id)->whereBetween('order_date', [$weekStart, $now])->get();
        $ordersMonth = Order::where('branch_id', $branch->id)->whereBetween('order_date', [$monthStart, $now])->get();

        $orderCountByStatus = [
            'today' => $ordersToday->groupBy('status')->map->count(),
            'week' => $ordersWeek->groupBy('status')->map->count(),
            'month' => $ordersMonth->groupBy('status')->map->count(),
        ];

        $revenue = [
            'today' => $ordersToday->whereIn('status', $paidStatuses)->sum('total_amount'),
            'week' => $ordersWeek->whereIn('status', $paidStatuses)->sum('total_amount'),
            'month' => $ordersMonth->whereIn('status', $paidStatuses)->sum('total_amount'),
        ];

        // Số đơn mỗi tài xế (tháng)
        $ordersByDriver = Order::where('branch_id', $branch->id)
            ->whereBetween('order_date', [$monthStart, $now])
            ->whereNotNull('driver_id')
            ->selectRaw('driver_id, count(*) as total')
            ->groupBy('driver_id')
            ->with('driver')
            ->get();

        // Đơn hủy/lỗi (tháng)
        $cancelledOrders = Order::where('branch_id', $branch->id)
            ->where('status', 'cancelled')
            ->whereBetween('order_date', [$monthStart, $now])
            ->with(['cancellation', 'branch', 'customer'])
            ->get();

        // Đơn hàng theo khung giờ (hôm nay)
        $ordersByHourRaw = Order::where('branch_id', $branch->id)
            ->whereDate('order_date', $today)
            ->selectRaw('HOUR(order_date) as hour, count(*) as total')
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();
        $ordersByHour = collect(range(0,23))->map(function($h) use ($ordersByHourRaw) {
            $row = $ordersByHourRaw->firstWhere('hour', $h);
            return [
                'hour' => $h,
                'total' => $row ? $row->total : 0
            ];
        });

        // Top 5 món ăn bán chạy nhất (theo số lượng, tháng)
        $topProducts = OrderItem::selectRaw('product_variant_id, SUM(quantity) as total')
            ->whereHas('order', function($q) use ($branch, $monthStart, $now) {
                $q->where('branch_id', $branch->id)
                  ->whereBetween('order_date', [$monthStart, $now]);
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

        // Số đơn đang chờ xử lý (tháng)
        $pendingOrdersCount = Order::where('branch_id', $branch->id)
            ->whereIn('status', $pendingStatuses)
            ->whereBetween('order_date', [$monthStart, $now])
            ->count();

        // Số đơn mỗi chi nhánh (ở đây chỉ có 1 chi nhánh)
        $branchChartData = collect([
            [
                'branch' => $branch->name,
                'total' => $ordersMonth->count()
            ]
        ]);

        return view('branch.statistics.dashboard', [
            'orderCountByStatus' => $orderCountByStatus,
            'revenue' => $revenue,
            'ordersByDriver' => $ordersByDriver,
            'cancelledOrders' => $cancelledOrders,
            'ordersByHour' => $ordersByHour,
            'branchChartData' => $branchChartData,
            'topProducts' => $topProducts,
            'pendingOrdersCount' => $pendingOrdersCount,
        ]);
    }

    public function driverStatistics(Request $request)
    {
        $manager = Auth::guard('manager')->user();
        $branch = $manager ? $manager->branch : null;
        if (!$branch) {
            abort(403, 'Bạn chưa được gán chi nhánh.');
        }

        // $drivers = Driver::where('branch_id', $branch->id)->get();
        $drivers = Driver::whereIn('id', Order::where('branch_id', $branch->id)
            ->whereNotNull('driver_id')
            ->pluck('driver_id')
            ->unique()
            ->filter())
            ->get();
        $driverId = $request->input('driver_id');
        $from = $request->input('from', now()->startOfMonth()->toDateString());
        $to = $request->input('to', now()->toDateString());

        $ordersQuery = Order::query()
            ->where('branch_id', $branch->id)
            ->whereBetween('order_date', [$from, $to])
            ->whereNotNull('driver_id');
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

        return view('branch.statistics.driver', [
            'drivers' => $drivers,
            'stats' => $stats,
            'driverId' => $driverId,
            'from' => $from,
            'to' => $to,
        ]);
    }

    public function foodStatistics(Request $request)
    {
        $manager = Auth::guard('manager')->user();
        $branch = $manager ? $manager->branch : null;
        if (!$branch) {
            abort(403, 'Bạn chưa được gán chi nhánh.');
        }

        $from = $request->input('from', now()->startOfMonth()->toDateString());
        $to = $request->input('to', now()->toDateString());
        $sort = $request->input('sort', 'revenue'); // revenue|quantity

        $orderItemsQuery = OrderItem::query()
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.branch_id', $branch->id)
            ->whereBetween('orders.order_date', [$from, $to]);

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
        $start = Carbon::parse($from);
        $end = Carbon::parse($to);
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

        return view('branch.statistics.food', [
            'foods' => $foods,
            'bestFood' => $bestFood,
            'worstFood' => $worstFood,
            'foodsByDay' => $foodsByDay,
            'foodsByHour' => $foodsByHour,
            'from' => $from,
            'to' => $to,
            'sort' => $sort,
        ]);
    }

    public function orderStatistics(Request $request)
    {
        $manager = Auth::guard('manager')->user();
        $branch = $manager ? $manager->branch : null;
        if (!$branch) {
            abort(403, 'Bạn chưa được gán chi nhánh.');
        }

        $status = $request->input('status');
        $from = $request->input('from', now()->startOfMonth()->toDateString());
        $to = $request->input('to', now()->toDateString());

        $ordersQuery = Order::query()
            ->where('branch_id', $branch->id)
            ->whereBetween('order_date', [$from, $to]);
        if ($status) {
            if ($status === 'processing') {
                $ordersQuery->whereIn('status', ['new', 'awaiting_confirmation', 'confirmed', 'awaiting_driver', 'driver_picked_up', 'in_transit']);
            } elseif ($status === 'delivered') {
                $ordersQuery->whereIn('status', ['delivered', 'item_received']);
            } elseif ($status === 'cancelled') {
                $ordersQuery->whereIn('status', ['cancelled', 'failed_delivery', 'delivery_incomplete']);
            } else {
                $ordersQuery->where('status', $status);
            }
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
        $start = Carbon::parse($from);
        $end = Carbon::parse($to);
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

        return view('branch.statistics.order', [
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
            'status' => $status,
            'from' => $from,
            'to' => $to,
        ]);
    }

    public function customerStatistics(Request $request)
    {
        $manager = Auth::guard('manager')->user();
        $branch = $manager ? $manager->branch : null;
        if (!$branch) {
            abort(403, 'Bạn chưa được gán chi nhánh.');
        }

        $from = $request->input('from', now()->startOfMonth()->toDateString());
        $to = $request->input('to', now()->toDateString());
        $minOrders = $request->input('min_orders', null);
        $area = $request->input('area', null);

        // Tổng số user mới trong tuần/tháng (chỉ tính những user có đơn ở chi nhánh này)
        $weekStart = now()->startOfWeek()->toDateString();
        $newUsersWeek = User::whereHas('roles', function($q){ $q->where('name', 'customer'); })
            ->whereHas('orders', function($q) use ($branch) {
                $q->where('branch_id', $branch->id);
            })
            ->whereBetween('created_at', [$weekStart, $to]);
        $newUsersMonth = User::whereHas('roles', function($q){ $q->where('name', 'customer'); })
            ->whereHas('orders', function($q) use ($branch) {
                $q->where('branch_id', $branch->id);
            })
            ->whereBetween('created_at', [$from, $to]);

        if ($area) {
            $newUsersWeek->where('area', $area);
            $newUsersMonth->where('area', $area);
        }
        if ($minOrders) {
            $newUsersWeek->whereHas('orders', function($q) use ($minOrders, $branch) {
                $q->where('branch_id', $branch->id)
                  ->havingRaw('COUNT(*) >= ?', [$minOrders]);
            });
            $newUsersMonth->whereHas('orders', function($q) use ($minOrders, $branch) {
                $q->where('branch_id', $branch->id)
                  ->havingRaw('COUNT(*) >= ?', [$minOrders]);
            });
        }
        $newUsersWeekCount = $newUsersWeek->count();
        $newUsersMonthCount = $newUsersMonth->count();

        // Top 10 khách mua nhiều nhất (chỉ tính đơn ở chi nhánh này)
        $topCustomers = Order::selectRaw('customer_id, COUNT(*) as total_orders, SUM(total_amount) as total_spent')
            ->where('branch_id', $branch->id)
            ->whereBetween('order_date', [$from, $to])
            ->when($area, function($q) use ($area) {
                $q->whereHas('customer', function($q2) use ($area) { $q2->where('area', $area); });
            })
            ->groupBy('customer_id')
            ->orderByDesc('total_orders')
            ->with('customer')
            ->limit(10)
            ->get();

        // Số đơn trung bình mỗi người (chỉ tính đơn ở chi nhánh này)
        $totalCustomers = User::whereHas('roles', function($q){ $q->where('name', 'customer'); })
            ->whereHas('orders', function($q) use ($branch) {
                $q->where('branch_id', $branch->id);
            })
            ->when($area, function($q) use ($area) { $q->where('area', $area); })
            ->count();
        $totalOrders = Order::where('branch_id', $branch->id)
            ->whereBetween('order_date', [$from, $to])
            ->when($area, function($q) use ($area) {
                $q->whereHas('customer', function($q2) use ($area) { $q2->where('area', $area); });
            })
            ->count();
        $avgOrdersPerUser = $totalCustomers > 0 ? round($totalOrders / $totalCustomers, 2) : 0;

        // Tỉ lệ khách quay lại (có >1 đơn trong khoảng, chỉ tính đơn ở chi nhánh này)
        $repeatCustomers = Order::select('customer_id')
            ->where('branch_id', $branch->id)
            ->whereBetween('order_date', [$from, $to])
            ->groupBy('customer_id')
            ->havingRaw('COUNT(*) > 1')
            ->when($area, function($q) use ($area) {
                $q->whereHas('customer', function($q2) use ($area) { $q2->where('area', $area); });
            })
            ->count();
        $repeatRate = $totalCustomers > 0 ? round($repeatCustomers / $totalCustomers * 100, 2) : 0;

        // Mức chi tiêu trung bình mỗi khách (chỉ tính đơn ở chi nhánh này)
        $totalRevenue = Order::where('branch_id', $branch->id)
            ->whereBetween('order_date', [$from, $to])
            ->when($area, function($q) use ($area) {
                $q->whereHas('customer', function($q2) use ($area) { $q2->where('area', $area); });
            })
            ->sum('total_amount');
        $avgSpending = $totalCustomers > 0 ? round($totalRevenue / $totalCustomers, 0) : 0;

        // Đánh giá người dùng cho hệ thống (chỉ tính đánh giá từ đơn ở chi nhánh này)
        $avgRating = \App\Models\ProductReview::whereNull('product_id')
            ->whereHas('order', function($q) use ($branch) {
                $q->where('branch_id', $branch->id);
            })
            ->whereBetween('review_date', [$from, $to])
            ->avg('rating');

        return view('branch.statistics.customer', [
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
