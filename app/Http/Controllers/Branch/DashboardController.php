<?php

namespace App\Http\Controllers\Branch;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Branch;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $manager = Auth::guard('manager')->user();
        $branch = $manager ? $manager->branch : null;

        if (!$branch) {
            abort(403, 'Bạn chưa được gán chi nhánh.');
        }

        // Thống kê tổng quan
        $totalRevenue = Order::where('branch_id', $branch->id)
            ->where('status', 'completed')
            ->sum('total_amount');
            
        $orderCount = Order::where('branch_id', $branch->id)->count();
        
        $averageOrderValue = $orderCount > 0 ? $totalRevenue / $orderCount : 0;
        
        // Tính tỷ lệ chuyển đổi (đơn hàng hoàn thành / tổng đơn hàng)
        $completedOrders = Order::where('branch_id', $branch->id)
            ->where('status', 'completed')
            ->count();
        $conversionRate = $orderCount > 0 ? ($completedOrders / $orderCount) * 100 : 0;

        // So sánh với tháng trước
        $currentMonth = Carbon::now()->startOfMonth();
        $lastMonth = Carbon::now()->subMonth()->startOfMonth();
        
        $currentMonthRevenue = Order::where('branch_id', $branch->id)
            ->where('status', 'completed')
            ->whereBetween('created_at', [$currentMonth, Carbon::now()])
            ->sum('total_amount');
            
        $lastMonthRevenue = Order::where('branch_id', $branch->id)
            ->where('status', 'completed')
            ->whereBetween('created_at', [$lastMonth, $currentMonth])
            ->sum('total_amount');
            
        $revenueGrowth = $lastMonthRevenue > 0 ? 
            (($currentMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100 : 0;

        $currentMonthOrders = Order::where('branch_id', $branch->id)
            ->whereBetween('created_at', [$currentMonth, Carbon::now()])
            ->count();
            
        $lastMonthOrders = Order::where('branch_id', $branch->id)
            ->whereBetween('created_at', [$lastMonth, $currentMonth])
            ->count();
            
        $orderGrowth = $lastMonthOrders > 0 ? 
            (($currentMonthOrders - $lastMonthOrders) / $lastMonthOrders) * 100 : 0;

        // Sản phẩm bán chạy (top 5) - sử dụng raw query để tránh lỗi relationship
        $topProducts = DB::table('products')
            ->join('product_variants', 'products.id', '=', 'product_variants.product_id')
            ->join('branch_stocks', 'product_variants.id', '=', 'branch_stocks.product_variant_id')
            ->leftJoin('order_items', 'product_variants.id', '=', 'order_items.product_variant_id')
            ->leftJoin('orders', function ($join) use ($branch) {
                $join->on('order_items.order_id', '=', 'orders.id')
                    ->where('orders.branch_id', $branch->id)
                    ->where('orders.status', 'completed');
            })
            ->where('branch_stocks.branch_id', $branch->id)
            ->select(
                'products.id',
                'products.name',
                'products.base_price',
                DB::raw('COUNT(order_items.id) as sold_count'),
                DB::raw('SUM(order_items.total_price) as total_revenue')
            )
            ->groupBy('products.id', 'products.name', 'products.base_price')
            ->orderByDesc('sold_count')
            ->take(5)
            ->get();

        // Đơn hàng gần đây (5 đơn hàng mới nhất)
        $recentOrders = Order::where('branch_id', $branch->id)
            ->with('customer')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Đơn hàng đang xử lý hôm nay
        $pendingOrdersToday = Order::where('branch_id', $branch->id)
            ->whereIn('status', ['pending', 'processing', 'shipping'])
            ->whereDate('created_at', Carbon::today())
            ->count();

        // Mục tiêu doanh thu (giả sử mục tiêu 60 triệu/tháng)
        $monthlyTarget = 60000000; // 60 triệu
        $targetProgress = $monthlyTarget > 0 ? ($currentMonthRevenue / $monthlyTarget) * 100 : 0;

        // Khuyến mãi đang chạy (cần thêm model Promotion nếu có)
        $activePromotions = 3; // Giá trị mẫu, cần thay bằng query thực tế

        // Doanh thu theo tháng (12 tháng gần nhất)
        $monthlyRevenue = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $revenue = Order::where('branch_id', $branch->id)
                ->where('status', 'completed')
                ->whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->sum('total_amount');
                
            $monthlyRevenue[] = [
                'month' => $month->format('M'),
                'revenue' => $revenue
            ];
        }

        // Doanh thu theo danh mục
        $categoryRevenue = DB::table('categories')
            ->leftJoin('products', 'categories.id', '=', 'products.category_id')
            ->leftJoin('product_variants', 'products.id', '=', 'product_variants.product_id')
            ->leftJoin('order_items', 'product_variants.id', '=', 'order_items.product_variant_id')
            ->leftJoin('orders', function ($join) use ($branch) {
                $join->on('order_items.order_id', '=', 'orders.id')
                    ->where('orders.branch_id', $branch->id)
                    ->where('orders.status', 'completed');
            })
            ->select(
                'categories.name as category_name',
                DB::raw('SUM(order_items.total_price) as total_revenue')
            )
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('total_revenue')
            ->get();

        return view('branch.dashboard', compact(
            'branch',
            'totalRevenue',
            'orderCount',
            'averageOrderValue',
            'conversionRate',
            'revenueGrowth',
            'orderGrowth',
            'topProducts',
            'recentOrders',
            'pendingOrdersToday',
            'targetProgress',
            'currentMonthRevenue',
            'monthlyTarget',
            'activePromotions',
            'monthlyRevenue',
            'categoryRevenue'
        ));
    }
}
