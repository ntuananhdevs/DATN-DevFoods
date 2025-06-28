<?php

namespace App\Http\Controllers\Branch;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Branch;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;

class BranchDashboardController extends Controller
{
    public function index()
    {
        $manager = Auth::user();
        $branch = Branch::where('manager_user_id', $manager->id)->first();

        // Thống kê
        $totalRevenue = Order::where('branch_id', $branch->id)->sum('total_amount');
        $orderCount = Order::where('branch_id', $branch->id)->count();
        $productCount = $branch->products()->count();

        // Sản phẩm bán chạy (top 5)
        $topProducts = Product::whereHas('branchStocks', function ($q) use ($branch) {
            $q->where('branch_id', $branch->id);
        })
            ->withCount(['branchStocks as sold' => function ($q) use ($branch) {
                $q->where('branch_id', $branch->id);
            }])
            ->orderByDesc('sold')
            ->take(5)
            ->get();

        return view('branch.dashboard', compact(
            'branch',
            'totalRevenue',
            'orderCount',
            'productCount',

            'topProducts'
        ));
    }
}
