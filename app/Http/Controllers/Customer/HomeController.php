<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class HomeController extends Controller
{

    public function index()
    {
        // Get selected branch ID from session
        $selectedBranchId = session('selected_branch');
        
        // Query for products
        $query = Product::with(['category', 'images' => function($query) {
            $query->orderBy('is_primary', 'desc');
        }])
        ->where('status', 'selling');
        
        // Filter products by selected branch (only show products available in this branch)
        if ($selectedBranchId) {
            $query->whereHas('variants', function($q) use ($selectedBranchId) {
                $q->whereHas('branchStocks', function($q2) use ($selectedBranchId) {
                    $q2->where('branch_id', $selectedBranchId)
                       ->where('stock_quantity', '>', 0);
                });
            });
        }
        
        // Get products
        $products = $query->get();
        
        $categories = Category::withCount('products')->get();
        $banners = Banner::where('is_active', 1)->get();
        
        return view('customer.home', compact('products', 'categories', 'banners'));
    }
    
}

