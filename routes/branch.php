<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Branch\BranchChatController;

Route::prefix('branch')->middleware(['auth'])->group(function () {
    Route::get('/chat', [BranchChatController::class, 'index'])->name('branch.chat.index');
    Route::get('/chat/api/conversation/{id}', [BranchChatController::class, 'apiGetConversation'])->name('branch.chat.conversation');
    Route::post('/chat/send-message', [BranchChatController::class, 'sendMessage'])->name('branch.chat.send');
    Route::post('/chat/update-status', [BranchChatController::class, 'updateStatus'])->name('branch.chat.status');
    Route::post('/chat/typing', [BranchChatController::class, 'typing'])->name('branch.chat.typing');
});
