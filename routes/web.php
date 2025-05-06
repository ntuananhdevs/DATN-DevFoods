<?php

use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\RoleController;

Route::resource('admin/roles', RoleController::class);




Route::get('/', [DashboardController::class, 'dashboard']);

// Route Dashboards
Route::get('/dashboard', [DashboardController::class, 'dashboard']);
