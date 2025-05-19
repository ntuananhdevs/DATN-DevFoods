<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;

//Admin
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\Auth\AuthController;
use App\Http\Controllers\Admin\BranchController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DriverController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\BannerController;

//Customer
use App\Http\Controllers\Customer\HomeController as CustomerHomeController;
use App\Http\Controllers\Customer\ProductController as CustomerProductController;
use App\Http\Controllers\Customer\CartController as CustomerCartController;
use App\Http\Controllers\Customer\AuthController as CustomerAuthController;
use App\Http\Controllers\Customer\UserController as CustomerUserController;

Route::prefix('/')->group(function () {
    Route::get('/', [CustomerHomeController::class, 'index'])->name('home');
    Route::get('shop/product', [CustomerProductController::class, 'index']);
    Route::get('shop/product/product-detail/{id}', [CustomerProductController::class, 'show']);
    
    // Route giỏ hàng
    Route::prefix('cart')->name('customer.cart.')->group(function () {
        Route::get('/', [CustomerCartController::class, 'index'])->name('index');
        Route::post('/add', [CustomerCartController::class, 'add'])->name('add');
        Route::post('/update', [CustomerCartController::class, 'update'])->name('update');
        Route::post('/remove', [CustomerCartController::class, 'remove'])->name('remove');
        Route::post('/clear', [CustomerCartController::class, 'clear'])->name('clear');
        Route::post('/ajax/update', [CustomerCartController::class, 'ajaxUpdate'])->name('ajax.update');
        Route::post('/ajax/remove', [CustomerCartController::class, 'ajaxRemove'])->name('ajax.remove');
        Route::get('/count', [CustomerCartController::class, 'count'])->name('count');
        Route::post('/selected', [CustomerCartController::class, 'saveSelectedItems'])->name('selected');
    });

    // Route Customer (login / logout / register)
    Route::get('/login', [CustomerAuthController::class, 'showLoginForm'])->name('customer.login');
    Route::post('/login', [CustomerAuthController::class, 'login'])->name('customer.login.submit');
    Route::get('/register', [CustomerAuthController::class, 'showRegisterForm'])->name('customer.register');
    Route::post('/register', [CustomerAuthController::class, 'register'])->name('customer.register.submit');
    Route::post('/logout', [CustomerAuthController::class, 'logout'])->name('customer.logout');

    // Route Customer (profile)
    Route::get('/profile', [CustomerUserController::class, 'showProfile'])->name('customer.profile');
    Route::get('/profile/update', [CustomerUserController::class, 'ShowForm'])->name('customer.profile.form');
    Route::post('/profile/update', [CustomerUserController::class, 'updateProfile'])->name('customer.profile.update');
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
    Route::resource('categories', CategoryController::class)->except(['destroy']);
    Route::prefix('categories')->name('categories.')->group(function () {
    Route::delete('{id}', [CategoryController::class, 'destroy'])->name('destroy');
    Route::patch('categories/{category}/toggle-status', [CategoryController::class, 'toggleStatus'])->name('toggle-status');
    Route::patch('categories/bulk-status-update', [CategoryController::class, 'bulkStatusUpdate'])->name('bulk-status-update');
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
        Route::get('/export', [UserController::class, 'export'])->name('export');
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
        Route::get('/show/{id}', [UserController::class, 'show'])->name('show');
        Route::delete('/delete/{id}', [UserController::class, 'destroy'])->name('destroy');
        Route::get('trash', [UserController::class, 'trash'])->name('trash');
        Route::post('{id}/restore', [UserController::class, 'restore'])->name('restore');
        Route::delete('{id}/force-delete', [UserController::class, 'forceDelete'])->name('force-delete');
        Route::get('/export', [UserController::class, 'export'])->name('export');
        Route::patch('/users/{id}/toggle-status', [UserController::class, 'toggleStatus'])->name('toggle-status');
        Route::patch('/users/bulk-status-update', [UserController::class, 'bulkStatusUpdate'])->name('bulk-status-update');
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

    // Driver Management
    Route::prefix('drivers')->name('drivers.')->group(function () {
        Route::get('/', [DriverController::class, 'index'])->name('index');
        Route::get('/applications', [DriverController::class, 'listApplications'])->name('applications.index');
        Route::get('/applications/{application}', [DriverController::class, 'viewApplicationDetails'])->name('applications.show');
        Route::post('/applications/{application}/approve', [DriverController::class, 'approveApplication'])->name('applications.approve');
        Route::post('/applications/{application}/reject', [DriverController::class, 'rejectApplication'])->name('applications.reject');
    });

     // Banner Management
     Route::prefix('banners')->name('banners.')->group(function () {
        Route::get('/', [BannerController::class, 'index'])->name('index');
        Route::get('/create', [BannerController::class, 'create'])->name('create');
        Route::post('/store', [BannerController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [BannerController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [BannerController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [BannerController::class, 'destroy'])->name('destroy');
        Route::patch('/{id}/toggle-status', [BannerController::class, 'toggleStatus'])->name('toggle-status');
        Route::patch('/bulk-status-update', [BannerController::class, 'bulkStatusUpdate'])->name('bulk-status-update');
    });
});

// Customer Cart Routes
Route::prefix('cart')->name('customer.cart.')->group(function () {
    Route::get('/', [CustomerCartController::class, 'index'])->name('index');
    Route::post('/add', [CustomerCartController::class, 'add'])->name('add');
    Route::post('/update', [CustomerCartController::class, 'update'])->name('update');
    Route::post('/update-batch', [CustomerCartController::class, 'updateBatch'])->name('update-batch');
    Route::post('/remove', [CustomerCartController::class, 'remove'])->name('remove');
    Route::post('/clear', [CustomerCartController::class, 'clear'])->name('clear');
});
