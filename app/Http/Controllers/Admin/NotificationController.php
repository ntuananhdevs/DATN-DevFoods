<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $admin = Auth::user();
        $notifications = $admin ? $admin->notifications()->latest()->paginate(20) : collect();
        return view('admin.notifications.index', [
            'notifications' => $notifications,
        ]);
    }

    public function markAsRead($id)
    {
        $admin = Auth::user();
        if ($admin) {
            $notification = $admin->notifications()->find($id);
            if ($notification && !$notification->read_at) {
                $notification->markAsRead();
            }
        }
        return response()->json(['success' => true]);
    }
} 