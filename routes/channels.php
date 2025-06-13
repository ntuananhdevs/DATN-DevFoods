<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\Conversation;
use App\Models\User;

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

    return true; // Hoặc thêm logic kiểm tra quyền truy cập
});

// Admin conversations channel
Broadcast::channel('admin.conversations', function ($user) {
    return $user->role === 'sp_admin' ? [
        'id' => $user->id,
        'name' => $user->name,
        'role' => $user->role,
    ] : false;
});

// Branch conversations channel
Broadcast::channel('branch.{branchId}.conversations', function ($user, $branchId) {
    return (in_array($user->role, ['branch_manager', 'branch_staff']) && $user->branch_id == $branchId) ? [
        'id' => $user->id,
        'name' => $user->name,
        'role' => $user->role,
        'branch_id' => $user->branch_id,
    ] : false;
});

// Online users presence channel
Broadcast::channel('online-users', function ($user) {
    return [
        'id' => $user->id,
        'name' => $user->name,
        'role' => $user->role,
        'avatar' => $user->avatar ?? null,
    ];
});
