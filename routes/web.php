<?php

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\Auth\LogoutController;

// Route Auth
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'login'])->name('login.submit');
});

// Route cho admin 
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');

    // Đăng xuất
    Route::post('logout', [LogoutController::class, 'logout'])->name('logout');
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
