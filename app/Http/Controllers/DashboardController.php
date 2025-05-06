<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    // Dashboard - Analytics
    public function dashboard()
    {
        $pageConfigs = [
            'pageHeader' => false
        ];

        return view('admin.dashboard', [
            'pageConfigs' => $pageConfigs
        ]);
    }

    // Dashboard - Ecommerce
    public function dashboardEcommerce()
    {
        $pageConfigs = [
            'pageHeader' => false
        ];

        return view('admin.dashboard-ecommerce', [
            'pageConfigs' => $pageConfigs
        ]);
    }
}

