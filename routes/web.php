<?php

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->group(function() {
    //dashboard
    Route::group(['prefix' => '/'], function() {
        Route::get('/', [DashboardController::class, 'dashboard'])->name('dashboard');
    });
    //users
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
