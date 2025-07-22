<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Hiển thị danh sách thông báo của customer
     */
    public function index()
    {

        $user = Auth::user();


        $customerNotifications = $user
            ? $user->notifications()->latest()->limit(10)->get()
            : collect();
        $customerUnreadCount = $user
            ? $user->unreadNotifications()->count()
            : 0;


        // Yêu cầu AJAX: trả về HTML + số lượng chưa đọc
        if (request()->ajax()) {
            $html = view('partials.customer._notification_items', [
                'customerNotifications' => $customerNotifications // Đúng tên biến blade!
            ])->render();

            return response()->json([
                'html'        => $html,
                'unreadCount' => $customerUnreadCount,
            ]);
        }

        // Trả về view danh sách
        return view('customer.notifications.index', [
            'customerNotifications' => $customerNotifications,
            'customerUnreadCount' => $customerUnreadCount,
        ]);
    }

    /**
     * Đánh dấu 1 thông báo là đã đọc
     */
    public function markAsRead($id)
    {
        $user = Auth::user();

        if ($user) {
            $notification = $user->notifications()->find($id);

            if ($notification && !$notification->read_at) {
                $notification->markAsRead();
            }
        }

        return response()->json(['success' => true]);
    }
}
