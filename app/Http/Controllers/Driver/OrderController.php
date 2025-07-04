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

        // Cập nhật các tab hiển thị cho Driver
        $tabConfig = [
            'in_progress' => ['label' => 'Đang thực hiện', 'statuses' => ['driver_picked_up', 'in_transit']],
            'completed' => ['label' => 'Đã hoàn thành', 'statuses' => ['delivered', 'item_received']],
            'problem' => ['label' => 'Đơn sự cố', 'statuses' => ['cancelled', 'failed_delivery', 'delivery_incomplete']],
        ];
        
        $currentTab = $request->query('tab', 'in_progress');
        if (!array_key_exists($currentTab, $tabConfig)) {
            $currentTab = 'in_progress';
        }

        // Query cơ bản với các thông tin eager-load cần thiết
        $query = Order::with(['customer', 'branch'])
                      ->where('driver_id', $driverId)
                      ->whereIn('status', $tabConfig[$currentTab]['statuses']);

        // THÊM LOGIC TÌM KIẾM
        if ($request->filled('search')) {
            $searchTerm = $request->input('search');
            $query->where(function($q) use ($searchTerm) {
                $q->where('id', 'like', "%{$searchTerm}%") // Tìm theo ID đơn hàng
                  ->orWhere('delivery_address', 'like', "%{$searchTerm}%") // Tìm theo địa chỉ
                  ->orWhereHas('customer', function($customerQuery) use ($searchTerm) {
                      $customerQuery->where('full_name', 'like', "%{$searchTerm}%"); // Tìm theo tên khách hàng
                  });
            });
        }

        $orders = $query->latest()->paginate(10);

        // Đếm số lượng cho mỗi tab
        $statusCounts = [];
        foreach ($tabConfig as $key => $config) {
            $statusCounts[$key] = Order::where('driver_id', $driverId)->whereIn('status', $config['statuses'])->count();
        }

        return view('driver.orders.index', compact('orders', 'currentTab', 'tabConfig', 'statusCounts'));
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