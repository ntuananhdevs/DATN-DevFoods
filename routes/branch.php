<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Branch\BranchChatController;
use App\Http\Controllers\Branch\BranchDashboardController;
use App\Http\Controllers\Branch\BranchProductController;
use App\Http\Controllers\Branch\BranchStaffController;
use App\Http\Controllers\Branch\BranchOrderController;
use App\Http\Controllers\Branch\BranchRevenueController;
use App\Http\Controllers\Branch\BranchCategoryController;
use App\Http\Controllers\Branch\Auth\AuthController;
use Illuminate\Support\Facades\Auth;

Route::middleware(['auth:manager', 'role:manager'])->prefix('branch')->group(function () {
    Route::get('/', [BranchDashboardController::class, 'index'])->name('branch.dashboard');// Nhóm các route liên quan đến đơn hàng
    Route::prefix('orders')->name('orders.')->group(function() {
        Route::get('/', [BranchOrderController::class, 'index'])->name('index');
        Route::post('/{order}/update-status', [BranchOrderController::class, 'updateStatus'])->name('updateStatus');
    });
    Route::get('/products', [BranchProductController::class, 'index'])->name('branch.products');
    Route::get('/categories', [BranchCategoryController::class, 'index'])->name('branch.categories');
    Route::get('/staff', [BranchStaffController::class, 'index'])->name('branch.staff');

    // Branch Authentication Routes
    Route::prefix('branch')->name('branch.')->group(function () {
        Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    });

    // Test route để kiểm tra user
    Route::get('/branch/test', function() {
        $user = Auth::guard('manager')->user();
        if ($user) {
            echo "User found: " . $user->name . "<br>";
            echo "Email: " . $user->email . "<br>";
            echo "Roles: " . $user->roles->pluck('name')->implode(', ') . "<br>";
            echo "Branch: " . ($user->branch ? $user->branch->name : 'No branch') . "<br>";
        } else {
            echo "No user found in manager guard";
        }
    });

    // Branch Protected Routes
    Route::middleware(['branch.auth'])->prefix('branch')->name('branch.')->group(function () {
        Route::get('/', [BranchDashboardController::class, 'index'])->name('dashboard');
        Route::get('/orders', [BranchOrderController::class, 'index'])->name('orders');
        Route::get('/products', [BranchProductController::class, 'index'])->name('products');
        Route::get('/categories', [BranchCategoryController::class, 'index'])->name('categories');
        Route::get('/staff', [BranchStaffController::class, 'index'])->name('staff');

        Route::get('/combos', [BranchProductController::class, 'indexCombo'])->name('combos');
        Route::get('/toppings', [BranchProductController::class, 'indexTopping'])->name('toppings');

        // Branch Chat Routes
        Route::prefix('chat')->name('chat.')->group(function () {
            Route::get('/', [BranchChatController::class, 'index'])->name('index');
            Route::get('/api/conversation/{id}', [BranchChatController::class, 'apiGetConversation'])->name('conversation');
            Route::post('/send-message', [BranchChatController::class, 'sendMessage'])->name('send');
            Route::post('/update-status', [BranchChatController::class, 'updateStatus'])->name('status');
        });
    });
});