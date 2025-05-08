<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Auth\AuthController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\RoleController;

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

    // Categories Management (CRUD)
    Route::resource('categories', CategoryController::class);

    // Users Management
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
    });

    // Roles Management
    Route::prefix('roles')->name('roles.')->group(function () {
        // Hiển thị danh sách vai trò
        Route::get('/', [RoleController::class, 'index'])->name('index');
        // Tạo mới vai trò
        Route::get('/create', [RoleController::class, 'create'])->name('create');
        Route::post('/store', [RoleController::class, 'store'])->name('store');
        // Sửa vai trò
        Route::get('/edit/{id}', [RoleController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [RoleController::class, 'update'])->name('update');
        // Chi tiết vai trò
        Route::get('/show/{id}', [RoleController::class, 'show'])->name('show');
        // Xóa vai trò
        Route::delete('/delete/{id}', [RoleController::class, 'destroy'])->name('destroy');
    });

    // Users Management
    Route::prefix('users')->name('users.')->group(function () {
        // Hiển thị danh sách người dùng
        Route::get('/', [UserController::class, 'index'])->name('index');
        // Tạo người dùng mới
        Route::get('/create', [UserController::class, 'create'])->name('create');
        Route::post('/store', [UserController::class, 'store'])->name('store');
        // Sửa thông tin người dùng
        Route::get('/edit/{id}', [UserController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [UserController::class, 'update'])->name('update');
        // Chi tiết người dùng
        Route::get('/show/{id}', [UserController::class, 'show'])->name('show');
        // Xóa người dùng
        Route::delete('/delete/{id}', [UserController::class, 'destroy'])->name('destroy');
        // Hiển thị danh sách người dùng đã xóa
        Route::get('/trashed', [UserController::class, 'trashed'])->name('trashed');
        // Khôi phục người dùng đã xóa
        Route::patch('/restore/{id}', [UserController::class, 'restore'])->name('restore');
        // Xóa vĩnh viễn người dùng
        Route::delete('/force-delete/{id}', [UserController::class, 'forceDelete'])->name('forceDelete');
    });
});
