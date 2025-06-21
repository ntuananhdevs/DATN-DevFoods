<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Driver\DriverController;
use App\Http\Controllers\Driver\OrderController;
use App\Http\Controllers\Driver\Auth\AuthController as DriverAuthController;

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
        Route::post('/resend-otp', [DriverAuthController::class, 'resendOTP'])->name('resend_otp');
        
        // Reset Password
        Route::get('/reset-password/{driver_id}', [DriverAuthController::class, 'showResetPasswordForm'])->name('reset_password');
        Route::post('/reset-password/{driver_id}', [DriverAuthController::class, 'processResetPassword'])->name('reset_password.submit');
    });
    
    // Authenticated driver routes
    Route::middleware(['auth:driver'])->group(function () {
        // Dashboard
        // Dashboard
        Route::get('/', [DriverController::class, 'home'])->name('home');
        Route::get('/dashboard', [DriverController::class, 'home'])->name('dashboard');
        
        // Authentication
        Route::post('/logout', [DriverAuthController::class, 'logout'])->name('logout');
        Route::post('/change-password', [DriverAuthController::class, 'changePassword'])->name('change_password');
        
        // Orders management
        // Orders management
        Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('orders/{orderId}', [OrderController::class, 'show'])->name('orders.show');
        Route::post('/orders/{order}/accept', [OrderController::class, 'accept'])->name('orders.accept');
        Route::post('/orders/{order}/start-pickup', [OrderController::class, 'startPickup'])->name('orders.start_pickup');
        Route::post('/orders/{order}/confirm-pickup', [OrderController::class, 'confirmPickup'])->name('orders.confirm_pickup');
        Route::post('/orders/{order}/confirm-delivery', [OrderController::class, 'confirmDelivery'])->name('orders.confirm_delivery');
        Route::post('/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
        Route::get('orders/{orderId}/navigate', [OrderController::class, 'navigate'])->name('orders.navigate');
         // --- CÁC HÀNH ĐỘNG POST ĐỂ XÁC NHẬN ---
        // Đây là các route mà JavaScript sẽ gọi đến
        Route::post('/{order}/accept', [OrderController::class, 'accept'])->name('accept');
        Route::post('/{order}/confirm-pickup', [OrderController::class, 'confirmPickup'])->name('confirm_pickup');
        Route::post('/{order}/confirm-delivery', [OrderController::class, 'confirmDelivery'])->name('confirm_delivery');
        Route::post('/{order}/cancel', [OrderController::class, 'cancel'])->name('cancel');
    
        // Driver profile and history
        Route::get('/profile', [DriverController::class, 'profile'])->name('profile');
        Route::put('/profile', [DriverController::class, 'updateProfile'])->name('profile.update');
        Route::get('/history', [DriverController::class, 'history'])->name('history');
        Route::get('/earnings', [DriverController::class, 'earnings'])->name('earnings');
        Route::get('/notifications', [DriverController::class, 'notifications'])->name('notifications');
        Route::post('/status/toggle', [DriverController::class, 'toggleStatus'])->name('status.toggle');
        Route::get('/earnings/query', [DriverController::class, 'queryEarnings'])->name('earnings.query');
        
        // API routes for mobile app
        Route::prefix('api')->name('api.')->group(function () {
            Route::get('/orders/available', [OrderController::class, 'available'])->name('orders.available');
            Route::get('/profile', [DriverController::class, 'getProfile'])->name('profile');
            Route::get('/stats', [DriverController::class, 'getStats'])->name('stats');
        });
    });
});
