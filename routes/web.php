<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Customer\CheckoutController;
use App\Http\Controllers\Customer\ProfileController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

Route::get('/checkout/success', [App\Http\Controllers\Customer\CheckoutController::class, 'success'])->name('checkout.success');
Route::post('/checkout/process', [App\Http\Controllers\Customer\CheckoutController::class, 'process'])->name('checkout.process');

//VNPAY Routes
Route::get('/checkout/vnpay-return', [App\Http\Controllers\Customer\CheckoutController::class, 'vnpayReturn'])->name('checkout.vnpay_return');
Route::get('/checkout/vnpay-ipn', [App\Http\Controllers\Customer\CheckoutController::class, 'vnpayIpn'])->name('checkout.vnpay_ipn');

Route::get('/refresh-csrf', function () {
    return response()->json(['csrf_token' => csrf_token()]);
});

Route::get('/customer/profile/branches-map', [ProfileController::class, 'getBranchesForMap']);

Route::get('/test-notification-debug', function () {
    return view('test-notification-debug');
})->middleware('auth');

Route::post('/api/test-notification', function () {
    $order = \App\Models\Order::where('customer_id', auth()->id())->latest()->first();
    if (!$order) {
        return response()->json(['error' => 'No order found'], 404);
    }
    
    // Trigger the event
    event(new \App\Events\Order\OrderStatusUpdated($order));
    
    return response()->json([
        'success' => true,
        'order_id' => $order->id,
        'status' => $order->status,
        'customer_id' => $order->customer_id
    ]);
})->middleware('auth');
