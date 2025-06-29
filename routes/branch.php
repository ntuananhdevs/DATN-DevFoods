<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Branch\BranchChatController;
use App\Http\Controllers\Branch\BranchDashboardController;
use App\Http\Controllers\Branch\BranchProductController;
use App\Http\Controllers\Branch\BranchStaffController;
use App\Http\Controllers\Branch\BranchOrderController;
use App\Http\Controllers\Branch\BranchRevenueController;
use App\Http\Controllers\Branch\BranchCategoryController;

Route::middleware(['auth:manager', 'role:manager'])->prefix('branch')->group(function () {
    Route::get('/', [BranchDashboardController::class, 'index'])->name('branch.dashboard');
    Route::get('/orders', [BranchOrderController::class, 'index'])->name('branch.orders');
    Route::get('/products', [BranchProductController::class, 'index'])->name('branch.products');
    Route::get('/categories', [BranchCategoryController::class, 'index'])->name('branch.categories');
    Route::get('/staff', [BranchStaffController::class, 'index'])->name('branch.staff');

    Route::get('/combos', [BranchProductController::class, 'indexCombo'])->name('branch.combos');
    Route::get('/toppings', [BranchProductController::class, 'indexTopping'])->name('branch.toppings');



    // Branch Chat Routes
    Route::prefix('chat')->name('branch.chat.')->group(function () {
        Route::get('/', [BranchChatController::class, 'index'])->name('index');
        Route::get('/api/conversation/{id}', [BranchChatController::class, 'apiGetConversation'])->name('conversation');
        Route::post('/send-message', [BranchChatController::class, 'sendMessage'])->name('send');
        Route::post('/update-status', [BranchChatController::class, 'updateStatus'])->name('status');
        Route::post('/typing', [BranchChatController::class, 'typingIndicator'])->name('typing');
    });
});
