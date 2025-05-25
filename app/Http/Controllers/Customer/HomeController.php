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
        $products = Product::with('category')->get();
        $categories = Category::withCount('products')->get();
        $banners = Banner::where('is_active', 1)->get();
        return view('customer.home', compact('products', 'categories', 'banners'));
    }
    
}

