<?php

use App\Http\Controllers\Customer\HomeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Customer\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index']);
Route::get('shop/product', [ProductController::class,'index']);
Route::get('shop/product/product-detail', [ProductController::class,'show']);

// Route Dashboards
Route::get('/dashboard', [DashboardController::class, 'dashboard']);

