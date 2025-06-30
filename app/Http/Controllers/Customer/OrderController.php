<?php

namespace App\Http\Controllers\Customer;

use App\Events\OrderCancelledByCustomer;
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
        // 1. Authorization
        if ($order->customer_id !== Auth::id()) {
            // Trả về lỗi JSON với mã 403 (Forbidden)
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền thực hiện hành động này.'
            ], 403);
        }

        // 2. Validation
        $validated = $request->validate([
            'status' => ['required', 'string', Rule::in(['cancelled', 'item_received'])],
        ]);
        $newStatus = $validated['status'];

        // 3. Logic
        $canUpdate = false;
        $message = '';
        if ($newStatus === 'cancelled' && $order->status === 'awaiting_confirmation') {
            $canUpdate = true;
            $message = 'Đã hủy đơn hàng thành công!';
            // Broadcast sự kiện hủy đơn đến các kênh liên quan
            broadcast(new OrderCancelledByCustomer($order->id))->toOthers();
        } elseif ($newStatus === 'item_received' && $order->status === 'delivered') {
            $canUpdate = true;
            $message = 'Đã xác nhận nhận hàng thành công!';
        }

        // THAY ĐỔI 1: Xử lý lỗi và trả về JSON
        if (!$canUpdate) {
            return response()->json([
                'success' => false,
                'message' => 'Hành động không được phép hoặc trạng thái đơn hàng không hợp lệ.'
            ], 422); // 422: Unprocessable Entity, mã lỗi hợp lý cho việc không thể xử lý
        }

        // 4. Update and Save
        $order->status = $newStatus;
        if ($newStatus === 'item_received' && !$order->actual_delivery_time) {
            $order->actual_delivery_time = now();
        }
        $order->save();
        
        // THAY ĐỔI 2: Lấy dữ liệu mới nhất từ DB để đảm bảo tính toàn vẹn
        $freshOrder = $order->fresh();

        // 5. Broadcast the event
        broadcast(new OrderStatusUpdated($freshOrder))->toOthers();

        // THAY ĐỔI 3: Trả về JSON khi thành công
        // Gửi về object 'order' để JavaScript có thể cập nhật UI
        return response()->json([
            'success' => true,
            'message' => $message,
            'order'   => $freshOrder
        ]);
    }
}