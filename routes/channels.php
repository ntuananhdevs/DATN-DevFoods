<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\Conversation;
use App\Models\Driver;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

// Log::info('--- [DEBUG] channels.php file was loaded ---');
// Log::info('[DEBUG] Auth Status Check:', [
//     'default_guard' => Auth::getDefaultDriver(),
//     'is_web_logged_in' => Auth::guard('web')->check(),
//     'web_user_id' => Auth::guard('web')->id(),
//     'is_driver_logged_in' => Auth::guard('driver')->check(),
//     'driver_user_id' => Auth::guard('driver')->id()
// ]);

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
    if ($user->role === 'admin') {
        return true;
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
    return in_array($user->role, ['admin']) ? [
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


Broadcast::channel('order.{orderId}', function ($user, $orderId) {
    $order = Order::find($orderId);
    if (!$order) {
        return false;
    }

    // 1. Cho phép Customer sở hữu đơn hàng được nghe
    if (Auth::guard('web')->check() && Auth::guard('web')->id() === $order->customer_id) {
        return true;
    }

    // 2. Cho phép Driver ĐÃ ĐƯỢC GÁN vào đơn hàng được nghe
    if (Auth::guard('driver')->check() && Auth::guard('driver')->id() === $order->driver_id) {
        return true;
    }

    // === BỔ SUNG ĐIỀU KIỆN MỚI ===
    // 3. Cho phép BẤT KỲ Driver nào cũng được nghe nếu đơn hàng đang ở trạng thái chờ
    if (Auth::guard('driver')->check() && in_array($order->status, ['confirmed', 'awaiting_driver'])) {
        return true;
    }

    return false;
});


/**
 * Kênh chung cho tất cả tài xế.
 */
Broadcast::channel('drivers', function ($user) {
    // Chỉ tài xế đã đăng nhập mới được nghe
    return $user instanceof Driver;
});




// Wishlist channel for a specific user
Broadcast::channel('user-wishlist-channel.{userId}', function ($user, $userId) {
    // Only the authenticated user with the matching ID can listen.
    return (int) $user->id === (int) $userId;
});

// Branch orders channel
Broadcast::channel('branch.{branchId}.orders', function ($user, $branchId) {
    // User đã được xác thực ở AuthController rồi, chỉ cần kiểm tra branch
    $userBranch = $user->branch;
    
    \Log::info('[Broadcast] branch.{branchId}.orders authorization', [
        'user_id' => $user->id,
        'user_branch_id' => $userBranch ? $userBranch->id : null,
        'requested_branch_id' => $branchId,
        'can_access' => $userBranch && (int)$userBranch->id === (int)$branchId
    ]);
    
    // Chỉ cần user có branch và branch_id khớp với channel
    return $userBranch && (int)$userBranch->id === (int)$branchId ? [
        'id' => $user->id,
        'name' => $user->full_name ?? $user->name ?? 'User',
        'branch_id' => $userBranch->id,
    ] : false;
});

// Public branch orders channel for general updates
Broadcast::channel('branch-orders-channel', function ($user = null) {
    // Allow all authenticated users to listen to general order updates
    return true;
});
