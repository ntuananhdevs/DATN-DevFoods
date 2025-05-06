<?php

use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
<<<<<<< HEAD
use App\Http\Controllers\Admin\RoleController;

Route::resource('admin/roles', RoleController::class);



=======
>>>>>>> 9b9f675225f77e5568d3f1dd1d4d67da2c3ab1f6

Route::get('/', [DashboardController::class, 'dashboard']);

// Route Dashboards
Route::get('/dashboard', [DashboardController::class, 'dashboard']);
<<<<<<< HEAD
=======

>>>>>>> 9b9f675225f77e5568d3f1dd1d4d67da2c3ab1f6
