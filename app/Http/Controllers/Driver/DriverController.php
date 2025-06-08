<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Data\MockDriverData; // Import our mock data class
use Carbon\Carbon; // Import Carbon for date/time handling

class DriverController extends Controller
{
    public function home()
    {
        $driver = (object) MockDriverData::$mockDriverProfile;
        $mockOrders = MockDriverData::getMockOrders();

        $today = Carbon::now()->locale('vi')->isoFormat('DD/MM/YYYY');

        $ordersToday = array_filter($mockOrders, function($order) use ($today) {
            return Carbon::parse($order['orderTime'])->locale('vi')->isoFormat('DD/MM/YYYY') === $today &&
                   ($order['status'] === "Đã hoàn thành" || $order['status'] === "Đang giao");
        });
        $totalEarnedToday = array_reduce($ordersToday, function($sum, $order) {
            return $sum + $order['driverEarnings'];
        }, 0);
        $totalKmToday = array_reduce($ordersToday, function($sum, $order) {
            return $sum + $order['distanceKm'];
        }, 0);

        $pendingOrders = array_filter($mockOrders, function($order) {
            return $order['status'] === "Chờ nhận";
        });
        $allPendingOrders = $pendingOrders; // For count
        $pendingOrders = array_slice($pendingOrders, 0, 3); // For display

        $allDeliveringOrders = array_filter($mockOrders, function($order) {
            return $order['status'] === "Đang giao";
        });

        return view('driver.dashboard', compact('driver', 'ordersToday', 'totalEarnedToday', 'totalKmToday', 'pendingOrders', 'allPendingOrders', 'allDeliveringOrders'));
    }

    public function profile()
    {
        $driver = (object) MockDriverData::$mockDriverProfile;
        return view('driver.profile', compact('driver'));
    }

    public function updateProfile(Request $request)
    {
        // In a real application, you would validate and save to a database.
        // For mock data, we can simulate an update (won't persist across requests).
        // For this example, we'll just return a success response.
        return response()->json(['message' => 'Profile updated successfully (mocked).']);
    }

    public function history(Request $request)
    {
        $mockDeliveryHistory = MockDriverData::getMockDeliveryHistory();
        $filter = $request->query('filter', 'all'); // Đổi tên biến từ initialFilter thành filter

        $filteredHistory = collect($mockDeliveryHistory)->filter(function($entry) use ($filter) {
            $entryDate = Carbon::parse($entry['orderTime']);
            $now = Carbon::now();

            if ($filter === "today") {
                return $entryDate->isSameDay($now);
            }
            if ($filter === "week") {
                return $entryDate->between($now->startOfWeek(), $now->endOfWeek());
            }
            if ($filter === "month") {
                return $entryDate->isSameMonth($now) && $entryDate->isSameYear($now);
            }
            return true; // 'all'
        })->sortByDesc(function($entry) {
            return Carbon::parse($entry['orderTime'])->timestamp;
        })->values()->all(); // Sắp xếp và reset keys

        $totalEarnings = array_reduce($filteredHistory, function($sum, $entry) {
            return $sum + $entry['driverEarnings'];
        }, 0);

        $totalOrders = count($filteredHistory);

        $ratings = array_filter(array_column($filteredHistory, 'rating'));
        $averageRating = count($ratings) > 0 ? number_format(array_sum($ratings) / count($ratings), 1) : 'N/A';

        return view('driver.history', compact('filteredHistory', 'filter', 'totalEarnings', 'totalOrders', 'averageRating'));
    }

    public function earnings()
    {
        // This would typically fetch real earnings data
        return view('driver.earnings'); // Assuming you have an earnings view
    }

    public function notifications()
    {
        $notifications = MockDriverData::getMockNotifications();
        // Sắp xếp thông báo theo thời gian mới nhất
        usort($notifications, function($a, $b) {
            return strtotime($b['timestamp']) - strtotime($a['timestamp']);
        });

        $unreadCount = count(array_filter($notifications, function($notif) {
            return !$notif['read'];
        }));

        return view('driver.notifications', compact('notifications', 'unreadCount'));
    }

    // API methods (would return JSON)
    public function getProfile()
    {
        return response()->json(MockDriverData::$mockDriverProfile);
    }

    public function getStats()
    {
        $mockOrders = MockDriverData::getMockOrders();
        $today = Carbon::now()->locale('vi')->isoFormat('DD/MM/YYYY');

        $ordersToday = array_filter($mockOrders, function($order) use ($today) {
            return Carbon::parse($order['orderTime'])->locale('vi')->isoFormat('DD/MM/YYYY') === $today &&
                   ($order['status'] === "Đã hoàn thành" || $order['status'] === "Đang giao");
        });
        $totalEarnedToday = array_reduce($ordersToday, function($sum, $order) {
            return $sum + $order['driverEarnings'];
        }, 0);
        $totalKmToday = array_reduce($ordersToday, function($sum, $order) {
            return $sum + $order['distanceKm'];
        }, 0);

        return response()->json([
            'ordersTodayCount' => count($ordersToday),
            'totalEarnedToday' => $totalEarnedToday,
            'totalKmToday' => $totalKmToday,
        ]);
    }
}