<?php

namespace App\Http\Controllers\Driver;

use App\Events\Order\OrderStatusUpdated;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $driverId = Auth::guard('driver')->id();
        $search = $request->query('search');
        $currentTab = $request->query('tab', 'all'); // Mặc định là 'all'

        // Define all relevant statuses for the driver interface
        $statuses = [
            'all' => 'Tất cả',
            // 'awaiting_confirmation' => 'Chờ xác nhận', // New status
            'awaiting_driver' => 'Chờ tài xế', // New status
            // 'driver_assigned' => 'Đã giao tài xế', // New status
            // 'driver_confirmed' => 'Tài xế xác nhận', // New status
            // 'driver_picked_up' => 'Đã lấy hàng',
            'in_transit' => 'Đang giao',
            'delivered' => 'Đã giao',
            'item_received' => 'Khách đã nhận', // New status
            // 'cancelled' => 'Đã hủy',
            // 'refunded' => 'Đã hoàn tiền', // New status
            // 'payment_failed' => 'TT thất bại', // New status
            // 'payment_received' => 'TT đã nhận', // New status
            // 'order_failed' => 'Đơn hàng thất bại' // New status
        ];

        $tabConfig = [];
        $baseQuery = Order::query();

        // Eager load relationships for display
        $baseQuery->with([
            'customer',
            'orderItems.productVariant.product.category'
        ]);

        // Calculate count for 'all' tab
        $allOrdersCount = Order::query();
        if ($driverId) {
            // For 'all' tab, a driver should only see orders they are involved in (assigned, confirmed, picked up, in transit, delivered, received, cancelled by them or if they were assigned to it)
            $allOrdersCount->where('driver_id', $driverId)
                ->orWhereNull('driver_id') // Include orders awaiting driver if driver is online and looking for new orders
                ->whereIn('status', ['awaiting_driver', 'driver_assigned', 'driver_confirmed', 'driver_picked_up', 'in_transit', 'delivered', 'item_received', 'cancelled', 'order_failed']);
            // The above logic for 'all' might need fine-tuning based on exact business rules for what 'all' means for a driver.
            // For now, it includes orders they are assigned to or that are awaiting *any* driver.
        } else {
            // If no driverId, perhaps show only orders awaiting driver, or nothing. For now, assume a driver is always logged in.
            // This part might need adjustment if anonymous access is considered or 'all' is truly global.
        }

        if ($search) {
            $allOrdersCount->where(function ($query) use ($search) {
                $query->where('order_code', 'like', '%' . $search . '%')
                    ->orWhere('delivery_address', 'like', '%' . $search . '%')
                    ->orWhereHas('customer', function ($q) use ($search) {
                        $q->where('full_name', 'like', '%' . $search . '%')
                            ->orWhere('phone', 'like', '%' . $search . '%');
                    });
            });
        }
        $tabConfig['all']['count'] = $allOrdersCount->count();
        $tabConfig['all']['label'] = 'Tất cả';

        foreach ($statuses as $key => $label) {
            if ($key === 'all') {
                continue;
            }

            $query = Order::query();
            // Apply driver filter for specific status tabs.
            // Only orders that are 'awaiting_driver' are not yet assigned to a specific driver.
            if ($key !== 'awaiting_driver' && $driverId) {
                $query->where('driver_id', $driverId);
            }
            // For 'awaiting_driver' tab, we only show unassigned orders
            if ($key === 'awaiting_driver') {
                $query->whereNull('driver_id');
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

        // Get orders for the current tab
        $ordersQuery = Order::query();
        $ordersQuery->with([
            'customer',
            'orderItems.productVariant.product.primaryImage', // Add primaryImage to load for product image
            'orderItems.productVariant.variantValues.attribute',
            'orderItems.toppings',
            'branch' // To show branch info if needed
        ]);

        if ($currentTab !== 'all') {
            $ordersQuery->where('status', $currentTab);
            // Apply driver_id filter for all tabs EXCEPT 'awaiting_driver'
            if ($currentTab !== 'awaiting_driver' && $driverId) {
                $ordersQuery->where('driver_id', $driverId);
            }
            // If the current tab is 'awaiting_driver', ensure driver_id is null
            if ($currentTab === 'awaiting_driver') {
                $ordersQuery->whereNull('driver_id');
            }
        } else {
            // Logic for 'all' tab
            if ($driverId) {
                $ordersQuery->where('driver_id', $driverId)
                    ->orWhereNull('driver_id')
                    ->whereIn('status', ['awaiting_driver', 'driver_assigned', 'driver_confirmed', 'driver_picked_up', 'in_transit', 'delivered', 'item_received', 'cancelled', 'order_failed']);
            }
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
