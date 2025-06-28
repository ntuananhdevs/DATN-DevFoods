<?php

namespace App\Http\Controllers\Customer;

use App\Events\OrderStatusUpdated;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class OrderController extends Controller
{
    /**
     * Hiển thị trang liệt kê tất cả đơn hàng của khách hàng đã đăng nhập.
     */
    public function index()
    {
        $orders = Order::where('customer_id', Auth::id())
                        ->latest() // Sắp xếp đơn hàng mới nhất lên đầu
                        ->paginate(10); // Phân trang, mỗi trang 10 đơn hàng

        return view('customer.orders.index', compact('orders'));
    }

    /**
     * Hiển thị trang chi tiết của một đơn hàng cụ thể.
     */
    public function show(Order $order)
    {
        // Bảo mật: Đảm bảo khách hàng chỉ có thể xem đơn hàng của chính mình.
        if ($order->customer_id !== Auth::id()) {
            abort(403, 'BẠN KHÔNG CÓ QUYỀN TRUY CẬP ĐƠN HÀNG NÀY.');
        }

        // Tải sẵn các relationship để tối ưu truy vấn
        $order->load([
            'branch',
            'driver',
            'payment.paymentMethod', // Tải cả phương thức thanh toán
            'orderItems.productVariant.product.primaryImage',
            'orderItems.productVariant.variantValues.attribute',
            'orderItems.toppings' // Đảm bảo bạn có quan hệ này trong model OrderItem
        ]);

        return view('customer.orders.show', compact('order'));
    }

    /**
     * Update the status of an order.
     * This single method handles cancelling, confirming receipt, etc.
     */
    public function updateStatus(Request $request, Order $order)
    {
        // 1. Authorization (giữ nguyên)
        if ($order->customer_id !== Auth::id()) {
            abort(403, 'UNAUTHORIZED ACTION.');
        }

        // 2. Validation (giữ nguyên)
        $validated = $request->validate([
            'status' => [
                'required',
                'string',
                Rule::in(['cancelled', 'item_received']),
            ],
        ]);

        $newStatus = $validated['status'];

        // 3. Logic (giữ nguyên)
        $canUpdate = false;
        $message = ''; // Chuẩn bị message để gửi về
        if ($newStatus === 'cancelled' && $order->status === 'awaiting_confirmation') {
            $canUpdate = true;
            $message = 'Đã hủy đơn hàng thành công!';
        } elseif ($newStatus === 'item_received' && $order->status === 'delivered') {
            $canUpdate = true;
            $message = 'Đã xác nhận nhận hàng thành công!';
        }

        if (!$canUpdate) {
            // Gửi về toast báo lỗi
            $toast = ['type' => 'error', 'title' => 'Thất bại', 'message' => 'Hành động không được phép.'];
            return back()->with('toast', $toast);
        }

        // 4. Update and Save (giữ nguyên)
        $order->status = $newStatus;
        if ($newStatus === 'item_received' && !$order->actual_delivery_time) {
            $order->actual_delivery_time = now();
        }
        $order->save();
        
        // 5. Broadcast the event (giữ nguyên)
        broadcast(new OrderStatusUpdated($order))->toOthers();

        // THAY ĐỔI DUY NHẤT Ở ĐÂY: Gửi về session 'toast' với cấu trúc mảng
        $toast = [
            'type' => 'success', // 'success', 'error', 'info', 'warning'
            'title' => 'Thành công',
            'message' => $message
        ];

        return back()->with('toast', $toast);
    }
}