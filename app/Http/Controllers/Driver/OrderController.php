<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class OrderController extends Controller
{
    /**
     * Hiển thị danh sách đơn hàng theo các tab trạng thái.
     */
    public function index(Request $request)
    {
        $driverId = Auth::guard('driver')->id();
        $tabStatuses = ['processing', 'delivering', 'delivered', 'cancelled'];
        $initialStatus = $request->query('status', 'processing');
        
        if (!in_array($initialStatus, $tabStatuses)) {
            $initialStatus = 'processing';
        }

        // Query cơ bản với các thông tin eager-load cần thiết
        $query = Order::with('customer')
                      ->where('driver_id', $driverId)
                      ->where('status', $initialStatus);

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

        // Lấy danh sách đơn hàng và phân trang
        $orders = $query->latest()->paginate(10);

        // Đếm số lượng cho mỗi tab
        $statusCounts = [];
        foreach ($tabStatuses as $status) {
            $statusCounts[$status] = Order::where('driver_id', $driverId)->where('status', $status)->count();
        }

        return view('driver.orders.index', compact('orders', 'initialStatus', 'tabStatuses', 'statusCounts'));
    }

    /**
     * Hiển thị chi tiết một đơn hàng.
     */
    public function show($orderId) // Thay đổi để nhận orderId từ route
    {
        $driverId = Auth::guard('driver')->id();

        // Lấy chi tiết đơn hàng, kèm theo các thông tin liên quan
        $order = Order::with(['customer', 'branch', 'orderItems.productVariant.product.images'])
                        ->findOrFail($orderId);

        // Logic mới: Chỉ báo lỗi 403 khi đơn hàng đã có người nhận VÀ người đó không phải là mình
        if ($order->driver_id !== null && $order->driver_id != $driverId) {
            abort(403, 'Bạn không có quyền xem đơn hàng này.');
        }

        // Nếu đơn hàng chưa có người nhận (driver_id = null) hoặc là của mình thì cho phép xem
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

    // --- CÁC HÀNH ĐỘNG CỦA TÀI XẾ ---

    public function accept(Order $order)
    {
        if ($order->status === 'pending' && is_null($order->driver_id)) {
            $order->driver_id = Auth::guard('driver')->id();
            $order->status = 'processing';
            $order->save();
            // Gửi event real-time cho khách hàng (nếu có)
            return response()->json(['success' => true, 'message' => 'Đã nhận đơn hàng thành công!']);
        }
        return response()->json(['success' => false, 'message' => 'Đơn hàng này không còn khả dụng.'], 400);
    }

    public function confirmPickup(Order $order)
    {
        if ($order->driver_id === Auth::guard('driver')->id() && $order->status === 'processing') {
            $order->status = 'delivering';
            $order->save();
            // Gửi event real-time cho khách hàng (nếu có)
            return response()->json(['success' => true, 'message' => 'Đã lấy hàng. Bắt đầu giao!']);
        }
        return response()->json(['success' => false, 'message' => 'Hành động không hợp lệ.'], 400);
    }

    public function confirmDelivery(Order $order)
    {
        if ($order->driver_id === Auth::guard('driver')->id() && $order->status === 'delivering') {
            $order->status = 'delivered';
            $order->delivery_date = Carbon::now();
            $order->save();
            // Logic cộng tiền vào ví tài xế, tính toán...
            return response()->json(['success' => true, 'message' => 'Đã giao hàng thành công!']);
        }
        return response()->json(['success' => false, 'message' => 'Hành động không hợp lệ.'], 400);
    }
}