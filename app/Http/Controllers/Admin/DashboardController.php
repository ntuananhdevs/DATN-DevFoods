<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    // Dashboard - Analytics
    public function dashboard()
    {
        return view('admin.dashboard.index');
    }

    // Dashboard - Ecommerce
    public function analytics()
    {
        return view('admin.dashboard.analytics');
    }
    public function ecommerce()
    {
        return view('admin.dashboard.ecommerce');
    }
    public function store_analytics()
    {
        return view('admin.dashboard.store_analytics');
    }
}
