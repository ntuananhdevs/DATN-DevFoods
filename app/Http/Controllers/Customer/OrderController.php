<?php

namespace App\Http\Controllers\Customer;

use App\Events\OrderCancelledByCustomer; // Ensure this event is correctly imported.
use App\Events\OrderStatusUpdated;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Http\JsonResponse; // Add this import


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
    public function updateStatus(Request $request, Order $order): JsonResponse
    {
        // 1. Bảo mật: Đảm bảo khách hàng chỉ có thể cập nhật đơn hàng của chính mình.
        if ($order->customer_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền truy cập đơn hàng này.'
            ], 403);
        }

        // 2. Validate yêu cầu
        $request->validate([
            'status' => ['required', 'string', Rule::in(['cancelled', 'item_received'])],
            'reason' => 'required_if:status,cancelled|string|max:500', // Lý do hủy
            // 'rating' => 'nullable|integer|min:1|max:5', // Đánh giá sao
            // 'review' => 'nullable|string|max:1000' // Đánh giá văn bản
        ]);

        $newStatus = $request->status;
        $canUpdate = false;
        $message = '';
        $oldStatus = $order->status; // Lấy trạng thái cũ trước khi cập nhật

        // 3. Logic chuyển đổi trạng thái
        // Khách hàng hủy đơn hàng (chỉ khi đơn đang chờ xác nhận)
        if ($newStatus === 'cancelled' && $order->status === 'awaiting_confirmation') {
            $canUpdate = true;
            $message = 'Đơn hàng của bạn đã được hủy thành công.';
            // Dispatch event for driver about customer cancellation
            event(new OrderCancelledByCustomer($order->id)); //
        }
        // Khách hàng xác nhận đã nhận hàng (chỉ khi đơn đã được giao)
        elseif ($newStatus === 'item_received' && $order->status === 'delivered') {
            $canUpdate = true;
            // Lưu rating và review nếu có
            // if ($request->rating) {
            //     $order->rating = $request->rating;
            // }
            // if ($request->review) {
            //     $order->review = $request->review;
            // }
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
        // broadcast(new OrderStatusUpdated($freshOrder))->toOthers(); // Remove .toOthers() if not needed or if it causes issues
        event(new OrderStatusUpdated($freshOrder)); //

        // Trả về kết quả thành công và dữ liệu đơn hàng đã được cập nhật
        return response()->json([
            'success' => true,
            'message' => $message,
            'order'   => $freshOrder
        ]);
    }

    /**
     * Trả về partial danh sách đơn hàng cho AJAX reload.
     */
    public function listPartial()
    {
        $orders = Order::where('customer_id', Auth::id())
            ->latest()
            ->paginate(10);
        return view('customer.orders.partials.list', compact('orders'))->render();
    }

    /**
     * Hiển thị form nhập mã đơn hàng để theo dõi.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function showTrackingForm()
    {
        return view('customer.track.index');
    }

    /**
     * Hiển thị trang theo dõi đơn hàng cho khách (không cần đăng nhập).
     *
     * @param string $order_code
     * @return \Illuminate\Contracts\View\View
     */
    public function orderTrackingForGuest(Request $request, $order_code = null)
    {
        // Nếu có form submission, validate Turnstile
        if ($request->isMethod('post')) {
            $validated = $request->validate([
                'order_code' => 'required|string|max:20',
                'cf-turnstile-response' => ['required', new \App\Rules\TurnstileRule()],
            ]);
            
            $order_code = $validated['order_code'];
            
            // Redirect to GET route với order_code
            return redirect()->route('customer.order.track', ['order_code' => $order_code]);
        }

        $order = Order::where('order_code', $order_code)->first();

        if (!$order) {
            return view('customer.track.show', ['error' => 'Không tìm thấy đơn hàng với mã này.']);
        }

        // Rút gọn tên khách hàng để bảo mật
        if ($order->customer_id && $order->customer) {
             $order->customer_name_short = \Illuminate\Support\Str::limit($order->customer->name, 3, '***');
        } else {
             $order->customer_name_short = \Illuminate\Support\Str::limit($order->guest_name, 3, '***');
        }

        // Rút gọn địa chỉ để bảo mật
        if ($order->address_id && $order->address) {
            $order->delivery_address_short = '..., ' . $order->address->ward . ', ' . $order->address->district;
        } else {
            $order->delivery_address_short = '..., ' . $order->guest_ward . ', ' . $order->guest_district;
        }

        // Lấy và dịch trạng thái hiện tại
        $currentStatus = $this->translateStatus($order->status);
        $lastUpdateTime = $order->updated_at;


        return view('customer.track.show', compact('order', 'currentStatus', 'lastUpdateTime'));
    }

    /**
     * Build the status timeline for an order.
     *
     * @param Order $order
     * @return \Illuminate\Support\Collection
     */
    private function buildStatusTimeline(Order $order)
    {
        $timeline = collect();

        // Thêm trạng thái ban đầu
        $timeline->push([
            'status' => 'Đã đặt hàng',
            'time' => $order->created_at,
            'is_current' => false,
            'is_completed' => true,
        ]);

        // Thêm các trạng thái từ lịch sử
        foreach ($order->orderStatusHistories->sortBy('changed_at') as $history) {
             $timeline->push([
                'status' => $this->translateStatus($history->new_status),
                'time' => $history->changed_at,
                'is_current' => false,
                'is_completed' => true,
            ]);
        }

        // Đánh dấu trạng thái hiện tại
        if ($timeline->isNotEmpty()) {
            $lastStatus = $timeline->last();
            if ($lastStatus) {
                $lastStatus['is_current'] = true;
                $timeline->pop();
                $timeline->push($lastStatus);
            }
        }
        
        return $timeline;
    }

    /**
     * Translate order status into Vietnamese.
     */
    private function translateStatus($status)
    {
        $translations = [
            'awaiting_confirmation' => 'Chờ xác nhận',
            'confirmed' => 'Đã xác nhận',
            'awaiting_driver' => 'Đang tìm tài xế',
            'driver_assigned' => 'Tài xế đã nhận đơn',
            'driver_confirmed' => 'Tài xế đã xác nhận',
            'driver_picked_up' => 'Tài xế đã lấy hàng',
            'in_transit' => 'Đang giao hàng',
            'delivered' => 'Đã giao hàng',
            'item_received' => 'Đã nhận hàng',
            'cancelled' => 'Đã hủy',
            'refunded' => 'Đã hoàn tiền',
            'payment_failed' => 'Thanh toán thất bại',
        ];

        return $translations[$status] ?? ucfirst(str_replace('_', ' ', $status));
    }
}