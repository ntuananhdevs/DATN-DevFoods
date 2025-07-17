<?php

namespace App\Http\Controllers\Branch;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $user = Auth::guard('manager')->user();
        $branch = $user && $user->branch ? $user->branch : null;
        $notifications = $branch ? $branch->notifications()->latest()->limit(10)->get() : collect();
        if (request()->ajax()) {
            $html = view('partials.branch._notification_items', ['branchNotifications' => $notifications])->render();
            $unreadCount = $branch ? $branch->unreadNotifications()->count() : 0;
            return response()->json([
                'html' => $html,
                'unreadCount' => $unreadCount,
            ]);
        }
        return view('branch.notifications.index', [
            'notifications' => $branch ? $branch->notifications()->latest()->paginate(20) : collect(),
        ]);
    }

    public function markAsRead($id)
    {
        $user = Auth::guard('manager')->user();
        $branch = $user && $user->branch ? $user->branch : null;
        if ($branch) {
            $notification = $branch->notifications()->find($id);
            if ($notification && !$notification->read_at) {
                $notification->markAsRead();
            }
        }
        return response()->json(['success' => true]);
    }
} 