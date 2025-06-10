<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Data\MockDriverData; // Import our mock data class
use Carbon\Carbon; // Import Carbon for date/time handling

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $mockOrders = MockDriverData::getMockOrders();
        $initialStatus = $request->query('status', 'Chờ nhận');
        $tabStatuses = ["Chờ nhận", "Đang giao", "Đã hoàn thành", "Đã hủy"];

        $ordersByStatus = [];
        $statusCounts = [];

        foreach ($tabStatuses as $status) {
            $filtered = array_filter($mockOrders, function($order) use ($status) {
                return $order['status'] === $status;
            });
            // Sort by orderTime descending for 'Chờ nhận' and 'Đang giao'
            if ($status === 'Chờ nhận' || $status === 'Đang giao') {
                usort($filtered, function($a, $b) {
                    return strtotime($b['orderTime']) - strtotime($a['orderTime']);
                });
            }
            $ordersByStatus[$status] = $filtered;
            $statusCounts[$status] = count($filtered);
        }

        return view('driver.orders', compact('ordersByStatus', 'initialStatus', 'tabStatuses', 'statusCounts'));
    }

    public function show($orderId)
    {
        $mockOrders = MockDriverData::getMockOrders();
        $order = collect($mockOrders)->firstWhere('id', $orderId);

        if (!$order) {
            abort(404, 'Order not found');
        }

        return view('driver.order-detail', compact('order'));
    }

    public function updateStatus(Request $request, $orderId)
    {
        $newStatus = $request->input('status');

        $orders = MockDriverData::getMockOrders(); // Lấy tất cả đơn hàng mock

        $updated = false;
        foreach ($orders as $key => $order) {
            if ($order['id'] === $orderId) {
                $orders[$key]['status'] = $newStatus;
                // Cập nhật thời gian giao hàng ước tính nếu trạng thái là "Đang giao"
                if ($newStatus === 'Đang giao') {
                    $orders[$key]['estimatedDeliveryTime'] = Carbon::now()->addMinutes(30)->format('H:i');
                }
                // Cập nhật lại mock data (chỉ cho mục đích demo, không phải cách làm thực tế)
                MockDriverData::setMockOrders($orders);
                $updated = true;
                break;
            }
        }

        if ($updated) {
            return response()->json(['success' => true, 'message' => 'Trạng thái đơn hàng đã được cập nhật.']);
        } else {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy đơn hàng hoặc trạng thái không hợp lệ.'], 404);
        }
    }

    public function accept($orderId)
    {
        return response()->json(['message' => "Order {$orderId} accepted (mocked)."]);
    }

    public function startPickup($orderId)
    {
        return response()->json(['message' => "Order {$orderId} pickup started (mocked)."]);
    }

    public function confirmPickup($orderId)
    {
        return response()->json(['message' => "Order {$orderId} pickup confirmed (mocked)."]);
    }

    public function confirmDelivery($orderId)
    {
        return response()->json(['message' => "Order {$orderId} delivered (mocked)."]);
    }

    public function cancel($orderId)
    {
        return response()->json(['message' => "Order {$orderId} cancelled (mocked)."]);
    }

    public function available()
    {
        $mockOrders = MockDriverData::getMockOrders();
        $availableOrders = array_filter($mockOrders, function($order) {
            return $order['status'] === 'Chờ nhận';
        });
        return response()->json($availableOrders);
    }
}