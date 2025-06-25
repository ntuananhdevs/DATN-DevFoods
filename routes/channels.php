<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\Conversation;
use App\Models\User;
use Illuminate\Support\Facades\Log;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Chat conversation channel
Broadcast::channel('chat.{conversationId}', function ($user, $conversationId) {
    $conversation = Conversation::find($conversationId);

    if (!$conversation) {
        return false;
    }

    // Super admin can access all conversations
    if ($user->role === 'super_admin') {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'role' => $user->role,
        ];
    }

    // Branch users can access conversations assigned to their branch
    if (in_array($user->role, ['branch_manager', 'branch_staff']) && $conversation->branch_id === $user->branch_id) {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'role' => $user->role,
            'branch_id' => $user->branch_id,
        ];
    }

    // Customers can only access their own conversations
    if ($user->role === 'customer' && $conversation->customer_id === $user->id) {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'role' => $user->role,
        ];
    }

    return false; // Hoặc thêm logic kiểm tra quyền truy cập
});

// Admin conversations channel
Broadcast::channel('admin.conversations', function ($user) {
    return in_array($user->role, ['admin', 'super_admin', 'spadmin']) ? [
        'id' => $user->id,
        'name' => $user->name,
        'role' => $user->role,
    ] : false;
});

// Branch conversations channel
Broadcast::channel('branch.{branchId}.conversations', function ($user, $branchId) {
    return (in_array($user->role, ['branch_staff', 'branch_admin']) && $user->branch_id == $branchId) ? [
        'id' => $user->id,
        'name' => $user->name,
        'role' => $user->role,
        'branch_id' => $user->branch_id,
    ] : false;
});


Broadcast::channel('driver.{driverId}', function ($driver, $driverId) {
    // Chỉ tài xế đã đăng nhập và có ID trùng khớp mới có thể nghe kênh này
    return (int) $driver->id === (int) $driverId;
});
// Presence chat channel
Broadcast::channel('presence-chat.{conversationId}', function ($user, $conversationId) {
    Log::info('[Broadcast] presence-chat', [
        'user_id' => $user->id,
        'user_role' => $user->role ?? null,
        'conversation_id' => $conversationId,
        'user' => $user,
    ]);
    // Kiểm tra quyền truy cập vào conversation này
    $conversation = Conversation::find($conversationId);
    if (!$conversation) {
        Log::warning('[Broadcast] Conversation not found', ['conversation_id' => $conversationId]);
        return false;
    }
    // Super admin có quyền truy cập tất cả
    if (($user->role ?? null) === 'super_admin') {
        Log::info('[Broadcast] Super admin truy cập', ['user_id' => $user->id]);
        return [
            'id' => $user->id,
            'name' => $user->name ?? $user->full_name ?? 'User',
            'role' => $user->role ?? null,
        ];
    }
    // Branch users
    if (in_array($user->role ?? '', ['branch_manager', 'branch_staff']) && $conversation->branch_id === ($user->branch_id ?? null)) {
        Log::info('[Broadcast] Branch user truy cập', ['user_id' => $user->id]);
        return [
            'id' => $user->id,
            'name' => $user->name ?? $user->full_name ?? 'User',
            'role' => $user->role ?? null,
            'branch_id' => $user->branch_id ?? null,
        ];
    }
    // Customer
    if (($user->role ?? null) === 'customer' && $conversation->customer_id === $user->id) {
        Log::info('[Broadcast] Customer truy cập', ['user_id' => $user->id]);
        return [
            'id' => $user->id,
            'name' => $user->name ?? $user->full_name ?? 'User',
            'role' => $user->role ?? null,
        ];
    }
    Log::warning('[Broadcast] Truy cập bị từ chối', ['user_id' => $user->id, 'role' => $user->role ?? null, 'conversation_id' => $conversationId]);
    return false;
});

// Discount codes channel - public channel for all users
Broadcast::channel('discounts', function ($user = null) {
    // Allow all users (including unauthenticated) to listen to discount updates
    return true;
});
