<?php

use App\Http\Controllers\Customer\HomeController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Customer\ProductController;
use Illuminate\Support\Facades\Route;


Route::prefix('/')->name('customer.')->group(function() {
    Route::get('/', [HomeController::class, 'index']);
    Route::get('shop/product', [ProductController::class,'index']);
    Route::get('shop/product/product-detail', [ProductController::class,'show']);
});
Route::prefix('admin')->name('admin.')->group(function() {
    
    Route::group(['prefix' => '/'], function() {
        Route::get('/', [DashboardController::class, 'dashboard'])->name('dashboard');
    });

    Route::group(['prefix' => 'users'], function() {
        Route::get('/', [UserController::class, 'index'])->name('users.index');  

        Route::get('/create', [UserController::class, 'create'])->name('users.create');

        Route::post('/store', [UserController::class, 'store'])->name('users.store');

        Route::get('/edit/{id}', [UserController::class, 'edit'])->name('users.edit');

        Route::put('/update/{id}', [UserController::class, 'update'])->name('users.update');

        Route::get('/show/{id}', [UserController::class, 'show'])->name('users.show');

        Route::delete('/delete/{id}', [UserController::class, 'destroy'])->name('users.destroy');

        Route::get('/trashed', [UserController::class, 'trashed'])->name('users.trashed');

        Route::patch('/restore/{id}', [UserController::class, 'restore'])->name('users.restore');
        
        Route::delete('/force-delete/{id}', [UserController::class, 'forceDelete'])->name('users.forceDelete');
    });
});
