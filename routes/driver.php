<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Driver\DriverController;
use App\Http\Controllers\Driver\OrderController;
use App\Http\Controllers\Driver\Auth\AuthController as DriverAuthController;
use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Driver Routes
|--------------------------------------------------------------------------
|
| Here is where you can register driver routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "driver" middleware group.
|
*/

// Driver authentication routes
Route::prefix('driver')->name('driver.')->group(function () {
    // Guest routes (not authenticated)
    Route::middleware('guest:driver')->group(function () {
        Route::get('/login', [DriverAuthController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [DriverAuthController::class, 'login'])->name('login.submit');

        // Forgot Password
        Route::get('/forgot-password', [DriverAuthController::class, 'showForgotPasswordForm'])->name('forgot_password');
        Route::post('/forgot-password', [DriverAuthController::class, 'SendOTP'])->name('send_otp');

        // Verify OTP
        Route::get('/verify-otp/{driver_id}', [DriverAuthController::class, 'showVerifyOtpForm'])->name('verify_otp');
        Route::post('/verify-otp', [DriverAuthController::class, 'verifyOtp'])->name('verify_otp.submit');

        // Reset Password
        Route::get('/reset-password/{token}', [DriverAuthController::class, 'showResetPasswordForm'])->name('reset_password');
        Route::post('/reset-password', [DriverAuthController::class, 'resetPassword'])->name('reset_password.submit');
    });

    // Authenticated driver routes
    Route::middleware('auth:driver')->group(function () {
        Route::post('/logout', [DriverAuthController::class, 'logout'])->name('logout');
        // Thêm route đổi mật khẩu lần đầu cho tài xế
        Route::post('/change-password', [DriverAuthController::class, 'changePassword'])->name('change_password');


        // Driver Dashboard
        Route::get('/', [DriverController::class, 'home'])->name('dashboard');

        // Order Management for Drivers
        Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order}/show', [OrderController::class, 'show'])->name('orders.show');
        Route::get('/orders/{order}/navigate', [OrderController::class, 'navigate'])->name('orders.navigate');

        // New/Updated Status Actions
        Route::post('/orders/{order}/confirm', [OrderController::class, 'confirm'])->name('orders.confirm');
        Route::post('/orders/{order}/start-pickup', [OrderController::class, 'startPickup'])->name('orders.start-pickup');
        Route::post('/orders/{order}/confirm-pickup', [OrderController::class, 'confirmPickup'])->name('orders.confirm-pickup');
        Route::post('/orders/{order}/start-delivery', [OrderController::class, 'startDelivery'])->name('orders.start-delivery');
        Route::post('/orders/{order}/confirm-delivery', [OrderController::class, 'confirmDelivery'])->name('orders.confirm-delivery');
        Route::post('/orders/{order}/reject', [OrderController::class, 'reject'])->name('orders.reject');
        
        // General order status update
        Route::post('/orders/{order}/update-status', [OrderController::class, 'updateOrderStatus'])->name('orders.update-status');

        // Batch Orders (Ghép đơn)
        Route::get('/orders/batchable', [OrderController::class, 'showBatchableOrders'])->name('orders.batchable');
        Route::post('/orders/batch/create', [OrderController::class, 'createBatch'])->name('orders.batch.create');
        Route::get('/orders/batch/{batchGroupId}/navigate', [OrderController::class, 'navigateBatch'])->name('orders.batch.navigate');
        Route::post('/orders/batch/{batchGroupId}/{orderId}/update-status', [OrderController::class, 'updateBatchOrderStatus'])->name('orders.batch.update-status');
        Route::delete('/orders/batch/{batchGroupId}/disband', [OrderController::class, 'disbandBatch'])->name('orders.batch.disband');

        // Driver profile and history
        Route::get('/profile', [DriverController::class, 'profile'])->name('profile');
        Route::put('/profile', [DriverController::class, 'updateProfile'])->name('profile.update');
        Route::get('/history', [DriverController::class, 'history'])->name('history');
        Route::get('/earnings', [DriverController::class, 'earnings'])->name('earnings');
        Route::get('/notifications', [DriverController::class, 'notifications'])->name('notifications');
        Route::post('/status/toggle', [DriverController::class, 'setAvailability'])->name('status.setAvailability'); // Renamed/Updated route for clarity
        Route::get('/earnings/query', [DriverController::class, 'queryEarnings'])->name('earnings.query');

        // API routes for mobile app
        Route::prefix('api')->name('api.')->group(function () {
            Route::get('/orders/available', [OrderController::class, 'available'])->name('orders.available');
            Route::get('/profile', [DriverController::class, 'getProfile'])->name('profile');
            Route::get('/stats', [DriverController::class, 'getStats'])->name('stats');
        });
    });

    // Broadcasting authentication route for drivers
    // This channel handles private-driver-specific channels like 'drivers'
    Route::post('/broadcasting/auth', function (\Illuminate\Http\Request $request) {
        return Broadcast::auth($request);
    })->middleware(['auth:driver']); // Only apply driver authentication middleware

});
