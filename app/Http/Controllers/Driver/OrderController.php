<?php

namespace App\Http\Controllers\Driver;

use App\Events\OrderStatusUpdated;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class OrderController extends Controller
{
    /**
     * Hiển thị danh sách đơn hàng theo các tab trạng thái.
     */
    public function index(Request $request)
    {
        $driverId = Auth::guard('driver')->id();
        $search = $request->query('search');
        $currentTab = $request->query('tab', 'all'); // Mặc định là 'all'

        $statuses = [
            'all' => 'Tất cả',
            'driver_picked_up' => 'Đã nhận',
            'in_transit' => 'Đang giao',
            'delivered' => 'Đã giao',
            'cancelled' => 'Đã hủy',
        ];

        $tabConfig = [];
        $baseQuery = Order::query();

        // Cần eager load các mối quan hệ để hiển thị trên blade
        $baseQuery->with([
            'customer',
            'orderItems.productVariant.product.category' // Để lấy tên danh mục cho tags
        ]);

        // Tính toán số lượng cho từng tab
        // Thay đổi logic tính toán số lượng để xử lý 'all' trước
        $allOrdersCount = Order::query(); // Query riêng cho tổng tất cả
        if ($driverId) {
            $allOrdersCount->where('driver_id', $driverId);
        }
        if ($search) {
            $allOrdersCount->where(function ($query) use ($search) {
                $query->where('order_code', 'like', '%' . $search . '%')
                    ->orWhere('delivery_address', 'like', '%' . $search . '%')
                    ->orWhereHas('customer', function ($q) use ($search) {
                        $q->where('name', 'like', '%' . $search . '%')
                            ->orWhere('phone', 'like', '%' . $search . '%');
                    });
            });
        }
        $tabConfig['all']['count'] = $allOrdersCount->count();
        $tabConfig['all']['label'] = 'Tất cả'; // Gán nhãn cho 'all'

        foreach ($statuses as $key => $label) {
            if ($key === 'all') {
                continue; // Bỏ qua 'all' vì đã xử lý ở trên
            }

            $query = Order::query();
            if ($driverId) {
                $query->where('driver_id', $driverId);
            }
            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('order_code', 'like', '%' . $search . '%')
                        ->orWhere('delivery_address', 'like', '%' . $search . '%')
                        ->orWhereHas('customer', function ($subQ) use ($search) {
                            $subQ->where('name', 'like', '%' . $search . '%')
                                ->orWhere('phone', 'like', '%' . $search . '%');
                        });
                });
            }
            $query->where('status', $key);
            $tabConfig[$key]['count'] = $query->count();
            $tabConfig[$key]['label'] = $label;
        }

        // Lấy đơn hàng cho tab hiện tại
        $ordersQuery = Order::query();
        if ($driverId) {
            $ordersQuery->where('driver_id', $driverId);
        }

        if ($currentTab !== 'all') {
            $ordersQuery->where('status', $currentTab);
        }

        if ($search) {
            $ordersQuery->where(function ($query) use ($search) {
                $query->where('order_code', 'like', '%' . $search . '%')
                    ->orWhere('delivery_address', 'like', '%' . $search . '%')
                    ->orWhereHas('customer', function ($q) use ($search) {
                        $q->where('name', 'like', '%' . $search . '%')
                            ->orWhere('phone', 'like', '%' . $search . '%');
                    });
            });
        }

        $orders = $ordersQuery->orderBy('created_at', 'desc')->paginate(10);

        return view('driver.orders.index', compact('orders', 'tabConfig', 'currentTab', 'search'));
    }

    /**
     * Hiển thị chi tiết một đơn hàng.
     */
    public function show($orderId)
    {
        $driverId = Auth::guard('driver')->id();
        $order = Order::with(['customer', 'branch', 'orderItems.productVariant.product.primaryImage'])->findOrFail($orderId);

        // Logic mới:
        // 1. Nếu đơn hàng chưa có tài xế và đang chờ -> Cho phép xem để nhận đơn.
        // 2. Nếu đơn hàng đã có tài xế và là của mình -> Cho phép xem.
        // 3. Các trường hợp khác -> Không cho phép.
        if ($order->status !== 'awaiting_driver' && $order->driver_id != $driverId) {
            abort(403, 'Bạn không có quyền xem đơn hàng này.');
        }

        return view('driver.orders.show', compact('order'));
    }

    /**
     * Hiển thị trang điều hướng cho một đơn hàng.
     */
    public function navigate($orderId)
    {
        $driverId = Auth::guard('driver')->id();

        // Lấy thông tin đơn hàng để biết tọa độ của khách hàng và chi nhánh
        $order = Order::with(['customer.addresses', 'branch', 'address'])
            ->where('id', $orderId)
            ->where('driver_id', $driverId)
            ->firstOrFail();

        return view('driver.orders.navigate', compact('order'));
    }

    // === CÁC HÀNH ĐỘNG CỦA TÀI XẾ - ĐƯỢC SẮP XẾP LẠI LOGIC ===

    /**
     * HÀM HELPER: Xử lý các tác vụ chung.
     */
    private function processUpdate(Order $order, string $successMessage): JsonResponse
    {
        $order->save();
        $freshOrder = $order->fresh();
        broadcast(new OrderStatusUpdated($freshOrder));

        return response()->json([
            'success' => true,
            'message' => $successMessage,
            'order'   => $freshOrder
        ]);
    }

    // Khi tài xế nhận đơn:
    public function accept(Order $order): JsonResponse
    {
        if ($order->status !== 'awaiting_driver' || !is_null($order->driver_id)) {
            return response()->json(['success' => false, 'message' => 'Đơn hàng này không còn khả dụng.'], 400);
        }

        $order->driver_id = Auth::guard('driver')->id();
        $order->status = 'driver_picked_up'; // Đúng với enum mới

        return $this->processUpdate($order, 'Đã nhận đơn hàng! Đang đến điểm lấy hàng.');
    }

    // Khi tài xế xác nhận đã lấy hàng:
    public function confirmPickup(Order $order): JsonResponse
    {
        if ($order->driver_id !== Auth::guard('driver')->id() || $order->status !== 'driver_picked_up') {
            return response()->json(['success' => false, 'message' => 'Hành động không hợp lệ hoặc bạn không có quyền.'], 400);
        }

        $order->status = 'in_transit';

        return $this->processUpdate($order, 'Đã lấy hàng thành công. Bắt đầu giao!');
    }

    /**
     * HÀM 3: Tài xế xác nhận đã giao hàng thành công.
     * Trạng thái chuyển đổi: 'in_transit' -> 'delivered'
     */
    public function confirmDelivery(Order $order): JsonResponse
    {
        // Điều kiện: Phải là tài xế của đơn hàng và đơn hàng phải đang được vận chuyển.
        if ($order->driver_id !== Auth::guard('driver')->id() || $order->status !== 'in_transit') {
            return response()->json(['success' => false, 'message' => 'Hành động không hợp lệ.'], 400);
        }

        $order->status = 'delivered';
        $order->actual_delivery_time = Carbon::now();

        return $this->processUpdate($order, 'Đã giao hàng thành công!');
    }
}
