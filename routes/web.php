<?php

use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'dashboardAnalytics']);

// Route Dashboards
Route::get('/dashboard-analytics', [DashboardController::class, 'dashboardAnalytics']);
Route::get('/dashboard-ecommerce', [DashboardController::class, 'dashboardEcommerce']);

