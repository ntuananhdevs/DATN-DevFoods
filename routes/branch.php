<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Branch\BranchChatController;
use App\Http\Controllers\Branch\DashboardController;
use App\Http\Controllers\Branch\BranchProductController;
use App\Http\Controllers\Branch\BranchStaffController;
use App\Http\Controllers\Branch\OrderController as BranchOrderController;
use App\Http\Controllers\Branch\BranchCategoryController;
use App\Http\Controllers\Branch\Auth\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Branch\DriverAssignmentController;
use App\Http\Controllers\Branch\NotificationController;

// Branch Authentication Routes
Route::prefix('branch')->name('branch.')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

// Branch Protected Routes
Route::middleware(['branch.auth'])->prefix('branch')->name('branch.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/driver-statistics', [DashboardController::class, 'driverStatistics'])->name('driver-statistics');
    Route::get('/order-statistics', [DashboardController::class, 'orderStatistics'])->name('order-statistics');
    Route::get('/food-statistics', [DashboardController::class, 'foodStatistics'])->name('food-statistics');
    Route::get('/customer-statistics', [DashboardController::class, 'customerStatistics'])->name('customer-statistics');

    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/', [BranchOrderController::class, 'index'])->name('index');
        Route::get('/{id}', [BranchOrderController::class, 'show'])->name('show');
        Route::post('/{id}/update-status', [BranchOrderController::class, 'updateStatus'])->name('update-status');
        Route::post('/{id}/cancel', [BranchOrderController::class, 'cancel'])->name('cancel');
        Route::post('/{id}/confirm', [BranchOrderController::class, 'confirmOrder'])->name('confirm');
        Route::get('/{id}/card', [BranchOrderController::class, 'card'])->name('card');

        // Driver assignment routes
        Route::post('/{id}/find-driver', [DriverAssignmentController::class, 'findDriver'])->name('find-driver');
        Route::post('/{id}/auto-assign-driver', [DriverAssignmentController::class, 'autoAssignNearestDriver'])->name('auto-assign-driver');
        Route::post('/{id}/driver-rejection', [DriverAssignmentController::class, 'handleDriverRejection'])->name('driver-rejection');
    });
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

    // Broadcasting authentication route for branch
    Route::post('/broadcasting/auth', function (Request $request) {
        Log::info('[Broadcasting] Auth request received', [
            'channel' => $request->input('channel_name'),
            'socket_id' => $request->input('socket_id'),
            'user' => Auth::guard('manager')->user(),
            'session' => $request->session()->all()
        ]);

        try {
            $result = Broadcast::auth($request);
            Log::info('[Broadcasting] Auth successful', ['result' => $result]);
            return $result;
        } catch (Exception $e) {
            Log::error('[Broadcasting] Auth failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    })->middleware(['web']);

    Route::get('notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('branch.notifications.read');
});
