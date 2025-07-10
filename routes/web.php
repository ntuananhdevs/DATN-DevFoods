<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Customer\CheckoutController;

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
