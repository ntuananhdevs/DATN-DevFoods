<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Branch\BranchChatController;

Route::prefix('branch')->middleware(['auth'])->group(function () {


    // Branch Chat Routes 
    Route::prefix('chat')->name('branch.chat.')->group(function () {
        Route::get('/', [BranchChatController::class, 'index'])->name('index');
        Route::get('/api/conversation/{id}', [BranchChatController::class, 'apiGetConversation'])->name('conversation');
        Route::post('/send-message', [BranchChatController::class, 'sendMessage'])->name('send');
        Route::post('/update-status', [BranchChatController::class, 'updateStatus'])->name('status');
    });
});
