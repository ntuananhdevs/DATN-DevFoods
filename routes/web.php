<?php

use Illuminate\Support\Facades\Route;

//Admin
use App\Http\Controllers\Customer\HomeController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\Auth\AuthController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\RoleController;

//Customer
use App\Http\Controllers\Customer\HomeController as CustomerHomeController;
use App\Http\Controllers\Admin\DriverController;
use App\Http\Controllers\Customer\ProductController as CustomerProductController;
use App\Http\Controllers\Customer\CartController as CustomerCartController;

Route::prefix('/')->group(function () {
    Route::get('/', [HomeController::class, 'index']);
    Route::get('shop/product', [CustomerProductController::class, 'index']);
    Route::get('shop/product/product-detail/{id}', [CustomerProductController::class, 'show']);
    Route::get('cart', [CustomerCartController::class, 'index']);
    Route::post('/cart/add', [CustomerCartController::class, 'add'])->name('cart.add');
});
// Route Auth (login / logout)
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [AuthController::class, 'login'])->name('login.submit');
});

// Route chỉ dành cho admin sau khi đăng nhập và có role:admin
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/', [DashboardController::class, 'dashboard'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');

    // Đăng xuất
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');

    // Categories Management
    Route::resource('categories', CategoryController::class);

    // Users Management
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
    });

    // Roles Management
    Route::prefix('roles')->name('roles.')->group(function () {
        Route::get('/', [RoleController::class, 'index'])->name('index');
        Route::get('/create', [RoleController::class, 'create'])->name('create');
        Route::post('/store', [RoleController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [RoleController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [RoleController::class, 'update'])->name('update');
        Route::get('/show/{id}', [RoleController::class, 'show'])->name('show');
        Route::delete('/delete/{id}', [RoleController::class, 'destroy'])->name('destroy');
    });

    // Users Management
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/create', [UserController::class, 'create'])->name('create');
        Route::post('/store', [UserController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [UserController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [UserController::class, 'update'])->name('update');
        Route::get('/show/{id}', [UserController::class, 'show'])->name('show');
        Route::delete('/delete/{id}', [UserController::class, 'destroy'])->name('destroy');
        Route::get('trash', [UserController::class, 'trash'])->name('trash');
        Route::post('{id}/restore', [UserController::class, 'restore'])->name('restore');
        Route::delete('{id}/force-delete', [UserController::class, 'forceDelete'])->name('force-delete');
        Route::get('/export', [UserController::class, 'export'])->name('export'); // Thêm dòng này
    });

    // Products Management
    Route::prefix('products')->name('products.')->group(function () {
        Route::get('/', [ProductController::class, 'index'])->name('index');
        Route::get('/create', [ProductController::class, 'create'])->name('create');
        Route::post('/store', [ProductController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [ProductController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [ProductController::class, 'update'])->name('update');
        Route::get('/show/{id}', [ProductController::class, 'show'])->name('show');
        Route::delete('/delete/{id}', [ProductController::class, 'destroy'])->name('destroy');
        Route::get('/trashed', [ProductController::class, 'trashed'])->name('trashed');
        Route::patch('/restore/{id}', [ProductController::class, 'restore'])->name('restore');
        Route::delete('/force-delete/{id}', [ProductController::class, 'forceDelete'])->name('forceDelete');
        Route::get('/export', [ProductController::class, 'export'])->name('export');
    });

    // Driver Application Management
    Route::prefix('drivers')->name('drivers.')->group(function () {
        Route::get('/', [DriverController::class, 'index'])->name('index');
        Route::get('/applications', [DriverController::class, 'listApplications'])->name('applications.index');
        Route::get('/applications/{application}', [DriverController::class, 'viewApplicationDetails'])->name('applications.show');
        Route::post('/applications/{application}/approve', [DriverController::class, 'approve'])->name('applications.approve');
        Route::post('/applications/{application}/reject', [DriverController::class, 'rejectApplication'])->name('applications.reject');
    });
});

<<<<<<<<< Temporary merge branch 1
=========
Route::group(['prefix' => 'admin/users', 'as' => 'admin.users.'], function() {
    Route::get('/search', [UserController::class, 'search'])->name('search');
});
>>>>>>>>> Temporary merge branch 2
