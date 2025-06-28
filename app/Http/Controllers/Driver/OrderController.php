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

    // --- CÁC HÀNH ĐỘNG CỦA TÀI XẾ (ĐÃ CẬP NHẬT) ---

    /**
     * Tài xế chấp nhận một đơn hàng đang chờ.
     * Trạng thái: awaiting_driver -> driver_picked_up
     */

    public function accept(Order $order): JsonResponse
    {
        // Logic kiểm tra đặc thù của việc "nhận đơn"
        if ($order->status !== 'awaiting_driver' || !is_null($order->driver_id)) {
            return response()->json(['success' => false, 'message' => 'Đơn hàng này không còn khả dụng hoặc đã có người nhận.'], 400);
        }

        // Gán tài xế và cập nhật trạng thái
        $order->driver_id = Auth::guard('driver')->id();
        $order->status = 'driver_picked_up'; // Tài xế đã nhận, và đang trên đường đến quán

        // Gọi hàm helper để xử lý các tác vụ chung
        return $this->processUpdate($order, 'Đã nhận đơn hàng thành công!');
    }

    /**
     * Tài xế xác nhận đã lấy hàng từ chi nhánh.
     */
    public function confirmPickup(Order $order): JsonResponse
    {
        // Kiểm tra quyền và trạng thái
        if ($order->driver_id !== Auth::guard('driver')->id() || $order->status !== 'driver_picked_up') {
            return response()->json(['success' => false, 'message' => 'Hành động không hợp lệ.'], 400);
        }

        // Cập nhật trạng thái
        $order->status = 'in_transit'; // Đang trên đường giao cho khách

        return $this->processUpdate($order, 'Đã lấy hàng. Bắt đầu giao!');
    }

    /**
     * Tài xế xác nhận đã giao hàng thành công.
     */
    public function confirmDelivery(Order $order): JsonResponse
    {
        // Kiểm tra quyền và trạng thái
        if ($order->driver_id !== Auth::guard('driver')->id() || $order->status !== 'in_transit') {
            return response()->json(['success' => false, 'message' => 'Hành động không hợp lệ.'], 400);
        }

        // Cập nhật trạng thái và thời gian giao hàng
        $order->status = 'delivered';
        $order->actual_delivery_time = Carbon::now();

        return $this->processUpdate($order, 'Đã giao hàng thành công!');
    }

    /**
     * HÀM HELPER MỚI: Xử lý các tác vụ chung để tránh lặp code.
     * @param Order $order - Đối tượng đơn hàng đã được thay đổi.
     * @param string $successMessage - Tin nhắn trả về khi thành công.
     * @return JsonResponse
     */
    private function processUpdate(Order $order, string $successMessage): JsonResponse
    {
        // 1. Lưu thay đổi vào database
        $order->save();

        // 2. Gửi sự kiện real-time đến các client khác (trừ người gửi)
        broadcast(new OrderStatusUpdated($order))->toOthers();

        // 3. Trả về phản hồi JSON cho frontend
        return response()->json([
            'success' => true,
            'message' => $successMessage
        ]);
    }

}