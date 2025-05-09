<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class HomeController extends Controller
{

    public function index()
    {
        $products = Product::with('category')->get();
        $categories = Category::withCount('products')->get();
        return view('customer.home', compact('products', 'categories'));
    }
}

