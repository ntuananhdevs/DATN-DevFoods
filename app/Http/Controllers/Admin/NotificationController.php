<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $user = Auth::guard('admin')->user();

        $notifications = $user ? $user->notifications()->latest()->limit(10)->get() : collect();

        if (request()->ajax()) {
            $html = view('partials.admin._notification_items', [
                'adminNotifications' => $notifications // Đúng tên biến blade cần!
            ])->render();
            $unreadCount = $user ? $user->unreadNotifications()->count() : 0;

            return response()->json([
                'html' => $html,
                'unreadCount' => $unreadCount,
            ]);
        }

        return view('admin.notifications.index', [
            'adminNotifications' => $user ? $user->notifications()->latest()->paginate(20) : collect(),
        ]);
    }

    public function markAsRead($id)
    {
        $user = Auth::guard('admin')->user();

        if ($user) {
            $notification = $user->notifications()->find($id);
            if ($notification && !$notification->read_at) {
                $notification->markAsRead();
            }
        }

        return response()->json(['success' => true]);
    }
}
