<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    // Dashboard - Analytics
    public function dashboard()
    {
        $pageConfigs = [
            'pageHeader' => false
        ];

        return view('admin.dashboard.dashboard', [
            'pageConfigs' => $pageConfigs
        ]);
    }

    // Dashboard - Ecommerce
    public function dashboardEcommerce()
    {
        $pageConfigs = [
            'pageHeader' => false
        ];

        return view('dashboard.admin.dashboard-ecommerce', [
            'pageConfigs' => $pageConfigs
        ]);
    }
}

