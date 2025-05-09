<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    // Dashboard - Analytics
    public function index()
    {
        return view("customer.shop.product-list");
    }
    public function show()
    {
        return view("customer.shop.product-detail");
    }
}

