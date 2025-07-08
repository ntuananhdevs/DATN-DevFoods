<?php

namespace App\Http\Controllers\Driver;

use App\Events\OrderStatusUpdated;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderStatusHistory;
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

        // Define all relevant statuses for the driver interface
        $statuses = [
            'all' => 'Tất cả',
            'awaiting_driver' => 'Đơn mới', // Orders waiting for any driver
            'driver_assigned' => 'Đã được giao', // Orders assigned to THIS driver but not yet confirmed
            'driver_confirmed' => 'Đã xác nhận', // Orders confirmed by THIS driver, ready for pickup
            'driver_picked_up' => 'Đã lấy hàng', // Orders picked up by THIS driver, waiting for transit
            'in_transit' => 'Đang giao', // Orders currently being delivered by THIS driver
            'delivered' => 'Đã giao', // Orders delivered by THIS driver
            'item_received' => 'Khách đã nhận', // Orders confirmed received by customer for THIS driver
            'cancelled' => 'Đã hủy', // Orders cancelled (could be by customer, branch, or system)
            'order_failed' => 'Thất bại', // Orders that failed delivery for THIS driver
        ];

        $tabConfig = [ //
            'all' => [
                'label' => 'Tất cả',
                'statuses' => array_keys($statuses), // All statuses
            ],
            'new_orders' => [ // New tab for orders awaiting any driver (not yet assigned)
                'label' => 'Đơn mới',
                'statuses' => ['awaiting_driver'],
                'query_scope' => function ($query) {
                    $query->whereNull('driver_id'); // Only show orders not yet assigned to any driver
                }
            ],
            'in_progress' => [ // Orders assigned to or being handled by the current driver
                'label' => 'Đang xử lý',
                'statuses' => ['driver_assigned', 'driver_confirmed', 'driver_picked_up', 'in_transit'],
                'query_scope' => function ($query, $driverId) {
                    $query->where('driver_id', $driverId);
                }
            ],
            'completed' => [
                'label' => 'Đã hoàn thành',
                'statuses' => ['delivered', 'item_received'],
                'query_scope' => function ($query, $driverId) {
                    $query->where('driver_id', $driverId);
                }
            ],
            'cancelled' => [
                'label' => 'Đã hủy',
                'statuses' => ['cancelled', 'order_failed'], // Consider 'order_failed' here too
                'query_scope' => function ($query, $driverId) {
                    $query->where('driver_id', $driverId)->orWhereNull('driver_id'); // Can be cancelled before driver assigned
                }
            ],
        ];

        // Ensure currentTab is valid, default to 'all'
        if (!isset($tabConfig[$currentTab])) {
            $currentTab = 'all';
        }

        $baseQuery = Order::query();

        // Eager load relationships for display
        $baseQuery->with([
            'customer',
            'orderItems.productVariant.product.category',
            'branch' // Also load branch info
        ]);

        // Apply search filter
        if ($search) {
            $baseQuery->where(function ($query) use ($search) {
                $query->where('order_code', 'like', '%' . $search . '%')
                    ->orWhere('delivery_address', 'like', '%' . $search . '%')
                    ->orWhereHas('customer', function ($q) use ($search) {
                        $q->where('name', 'like', '%' . $search . '%')
                            ->orWhere('phone', 'like', '%' . $search . '%');
                    });
            });
        }

        // Calculate counts for each tab
        foreach ($tabConfig as $key => &$config) {
            $countQuery = Order::query();
            if (isset($config['query_scope'])) {
                $config['query_scope']($countQuery, $driverId); // Apply specific scope
            } elseif ($driverId && $key !== 'new_orders') { // For tabs directly tied to assigned driver
                $countQuery->where('driver_id', $driverId);
            }
            $countQuery->whereIn('status', $config['statuses']);
            $config['count'] = $countQuery->count();
        }

        // Apply filters for the current tab
        if (isset($tabConfig[$currentTab]['query_scope'])) {
            $tabConfig[$currentTab]['query_scope']($baseQuery, $driverId);
        } elseif ($driverId && $currentTab !== 'new_orders') {
            $baseQuery->where('driver_id', $driverId);
        }
        $baseQuery->whereIn('status', $tabConfig[$currentTab]['statuses']);

        // Always order by latest
        $orders = $baseQuery->latest()->paginate(10); //

        // Pass available tabs to the view
        $availableTabs = $tabConfig;

        if ($request->ajax()) {
            return response()->json([
                'orders' => view('driver.orders.partials.list', compact('orders', 'availableTabs'))->render(),
                'tabCounts' => collect($availableTabs)->mapWithKeys(fn($config, $key) => [$key => $config['count']]),
            ]);
        }

        return view('driver.orders.index', compact('orders', 'availableTabs', 'currentTab', 'search'));
    }

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

    /**
     * HÀM 1: Tài xế nhận đơn hàng (chuyển từ awaiting_driver sang driver_assigned).
     */
    public function accept(Order $order): JsonResponse
    {
        // Điều kiện: Đơn hàng phải đang chờ tài xế và chưa có tài xế nào nhận
        if ($order->status !== 'awaiting_driver' || $order->driver_id !== null) {
            return response()->json(['success' => false, 'message' => 'Đơn hàng này không còn khả dụng hoặc đã có người nhận.']);
        }

        $order->driver_id = Auth::guard('driver')->id();
        $order->status = 'driver_assigned'; // Correct status transition

        // You might want to remove this line, as it usually means they are on the way to pickup.
        // It's better handled in confirmPickup.
        // $order->estimated_delivery_time = Carbon::now()->addMinutes(30); // Example: 30 minutes from now

        return $this->processUpdate($order, 'Đã nhận đơn hàng! Vui lòng xác nhận để tiếp tục.');
    }

    /**
     * HÀM MỚI: Tài xế xác nhận đã được giao đơn (chuyển từ driver_assigned sang driver_confirmed).
     */
    public function confirmAssigned(Order $order): JsonResponse
    {
        if ($order->driver_id !== Auth::guard('driver')->id() || $order->status !== 'driver_assigned') {
            return response()->json(['success' => false, 'message' => 'Hành động không hợp lệ hoặc bạn không có quyền.']);
        }

        $order->status = 'driver_confirmed'; // New status transition
        return $this->processUpdate($order, 'Đã xác nhận đơn hàng! Vui lòng đi đến điểm lấy hàng.');
    }

    /**
     * HÀM 2: Tài xế xác nhận đã lấy hàng (chuyển từ driver_confirmed sang driver_picked_up).
     * Hoặc từ driver_assigned nếu bỏ qua bước driver_confirmed
     */
    public function confirmPickup(Order $order): JsonResponse
    {
        // Allow transition from both driver_assigned and driver_confirmed if flow allows skipping confirmAssigned
        if ($order->driver_id !== Auth::guard('driver')->id() || !in_array($order->status, ['driver_confirmed', 'driver_assigned'])) {
            return response()->json(['success' => false, 'message' => 'Hành động không hợp lệ hoặc bạn không có quyền.']);
        }

        $order->status = 'driver_picked_up'; // Correct status transition
        return $this->processUpdate($order, 'Đã lấy hàng thành công. Bắt đầu giao!');
    }

    /**
     * HÀM 3: Tài xế xác nhận đơn hàng đang trong quá trình giao (chuyển từ driver_picked_up sang in_transit).
     * (Thường sẽ tự động chuyển sau khi confirmPickup, nhưng có thể tách ra nếu muốn tài xế bấm thêm 1 nút)
     */
    public function startTransit(Order $order): JsonResponse
    {
        if ($order->driver_id !== Auth::guard('driver')->id() || $order->status !== 'driver_picked_up') {
            return response()->json(['success' => false, 'message' => 'Hành động không hợp lệ hoặc bạn không có quyền.']);
        }
        $order->status = 'in_transit'; //
        return $this->processUpdate($order, 'Đơn hàng đang trên đường giao.');
    }

    /**
     * HÀM 4: Tài xế xác nhận đã giao hàng thành công (chuyển từ in_transit sang delivered).
     */
    public function confirmDelivery(Order $order): JsonResponse
    {
        // Điều kiện: Phải là tài xế của đơn hàng và đơn hàng phải đang được vận chuyển.
        if ($order->driver_id !== Auth::guard('driver')->id() || $order->status !== 'in_transit') {
            return response()->json(['success' => false, 'message' => 'Hành động không hợp lệ.']);
        }

        $order->status = 'delivered'; //
        $order->actual_delivery_time = Carbon::now(); // Set actual delivery time
        return $this->processUpdate($order, 'Đã giao hàng thành công!');
    }

    /**
     * HÀM MỚI: Tài xế báo cáo đơn hàng thất bại.
     */
    public function failOrder(Request $request, Order $order): JsonResponse
    {
        if ($order->driver_id !== Auth::guard('driver')->id() || !in_array($order->status, ['driver_assigned', 'driver_confirmed', 'driver_picked_up', 'in_transit'])) {
            return response()->json(['success' => false, 'message' => 'Hành động không hợp lệ hoặc bạn không có quyền.']);
        }

        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $order->status = 'order_failed';
        // You might want to store the reason in order_cancellation or a new field
        // For simplicity, let's just add it to a note in history for now
        $order->notes = ($order->notes ? $order->notes . "\n" : '') . 'Tài xế báo cáo thất bại: ' . $request->reason;

        return $this->processUpdate($order, 'Đã báo cáo đơn hàng thất bại.');
    }


    /**
     * Hàm helper để cập nhật trạng thái và broadcast sự kiện.
     */
    private function processUpdate(Order $order, string $message): JsonResponse
    {
        $oldStatus = $order->getOriginal('status'); // Get original status before saving
        $order->save();
        $freshOrder = $order->fresh(); // Get the freshly saved order with updated attributes

        // Record status history
        OrderStatusHistory::create([
            'order_id' => $freshOrder->id,
            'old_status' => $oldStatus,
            'new_status' => $freshOrder->status,
            'changed_by' => Auth::guard('driver')->id(),
            'changed_by_role' => 'driver',
            'note' => $message,
            'changed_at' => now()
        ]);

        // Broadcast sự kiện cập nhật trạng thái đơn hàng (cho khách hàng và quản lý chi nhánh)
        event(new OrderStatusUpdated($freshOrder));

        return response()->json([
            'success' => true,
            'message' => $message,
            'order' => $freshOrder->load(['customer', 'branch', 'orderItems.productVariant.product']) // Load relationships for frontend
        ]);
    }
}
