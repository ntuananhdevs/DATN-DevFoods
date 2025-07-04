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
        // 1. Authorization: Kiểm tra quyền truy cập của khách hàng
        if ($order->customer_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền thực hiện hành động này.'
            ], 403); // Trả về mã lỗi 403 khi không có quyền
        }

        // 2. Validation: Kiểm tra dữ liệu đầu vào
        $validated = $request->validate([
            'status' => ['required', 'string', Rule::in(['cancelled', 'item_received'])],
            'rating' => 'nullable|integer|min:1|max:5',
            'review' => 'nullable|string|max:500'
        ]);
        $newStatus = $validated['status'];

        // 3. Logic: Kiểm tra trạng thái và điều kiện để thay đổi
        $canUpdate = false;
        $message = '';

        // Kiểm tra trạng thái hủy đơn
        if ($newStatus === 'cancelled' && $order->status === 'awaiting_confirmation') {
            $canUpdate = true;
            $message = 'Đã hủy đơn hàng thành công!';
            // Broadcast sự kiện hủy đơn đến các kênh liên quan
            broadcast(new OrderCancelledByCustomer($order->id))->toOthers();
        }
        // Kiểm tra trạng thái xác nhận đã nhận hàng
        elseif ($newStatus === 'item_received' && $order->status === 'delivered') {
            $canUpdate = true;
            // Lưu rating và review nếu có
            if ($request->rating) {
                $order->rating = $request->rating;
            }
            if ($request->review) {
                $order->review = $request->review;
            }
            $message = 'Đã xác nhận nhận hàng thành công!';
        }

        // Xử lý lỗi nếu không thể thay đổi trạng thái
        if (!$canUpdate) {
            return response()->json([
                'success' => false,
                'message' => 'Hành động không được phép hoặc trạng thái đơn hàng không hợp lệ.'
            ], 422); // Trả về mã lỗi 422 nếu không thể xử lý
        }

        // 4. Cập nhật trạng thái và lưu dữ liệu
        $order->status = $newStatus;
        if ($newStatus === 'item_received' && !$order->actual_delivery_time) {
            $order->actual_delivery_time = now();
        }
        $order->save();
        
        // Lấy dữ liệu mới nhất từ cơ sở dữ liệu
        $freshOrder = $order->fresh();

        // 5. Broadcast sự kiện cập nhật trạng thái đơn hàng
        broadcast(new OrderStatusUpdated($freshOrder))->toOthers();

        // Trả về kết quả thành công và dữ liệu đơn hàng đã được cập nhật
        return response()->json([
            'success' => true,
            'message' => $message,
            'order'   => $freshOrder
        ]);
    }
}