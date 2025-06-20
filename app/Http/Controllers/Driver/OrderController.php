<?php

namespace App\Http\Controllers\Driver;

use App\Events\NewOrderAvailable;
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
        $tabStatuses = ['pending', 'delivering', 'delivered', 'cancelled']; // Các trạng thái thật
        $initialStatus = $request->query('status', 'pending');

        if (!in_array($initialStatus, $tabStatuses)) {
            $initialStatus = 'pending';
        }

        // Lấy các đơn hàng thuộc tab đang chọn
        $orders = Order::where('driver_id', $driverId)
                        ->where('status', $initialStatus)
                        ->latest()
                        ->paginate(10);

        // Đếm số lượng đơn hàng cho mỗi tab
        $statusCounts = [];
        foreach ($tabStatuses as $status) {
            $statusCounts[$status] = Order::where('driver_id', $driverId)->where('status', $status)->count();
        }

        return view('driver.orders.index', compact('orders', 'initialStatus', 'tabStatuses', 'statusCounts'));
    }

    /**
     * Hiển thị chi tiết một đơn hàng.
     */
    public function show(Order $order)
    {
        // Security check: Đảm bảo tài xế chỉ xem được đơn hàng của mình
        if ($order->driver_id !== Auth::guard('driver')->id()) {
            abort(403, 'Unauthorized action.');
        }

        return view('driver.orders.show', compact('order'));
    }

    /**
     * Tài xế chấp nhận một đơn hàng.
     */
    public function accept(Order $order)
    {
        // Logic kiểm tra xem đơn hàng có thể nhận không (ví dụ: đang là 'pending' và chưa có driver)
        if ($order->status === 'pending' && is_null($order->driver_id)) {
            $order->driver_id = Auth::guard('driver')->id();
            $order->status = 'processing'; // Chuyển sang trạng thái "Đang chuẩn bị"
            $order->save();

            // Gửi event Pusher cho khách hàng biết
            // CustomerOrderUpdated::dispatch($order);

            return response()->json(['success' => true, 'message' => 'Đã nhận đơn hàng thành công!']);
        }
        
        return response()->json(['success' => false, 'message' => 'Không thể nhận đơn hàng này.'], 400);
    }
    
    /**
     * Tài xế xác nhận đã lấy hàng.
     */
    public function confirmPickup(Order $order)
    {
        if ($order->driver_id === Auth::guard('driver')->id() && $order->status === 'processing') {
            $order->status = 'delivering'; // Chuyển sang "Đang giao hàng"
            $order->save();
            NewOrderAvailable::dispatch($order);
            return response()->json(['success' => true, 'message' => 'Đã lấy hàng. Bắt đầu giao!']);
        }
        return response()->json(['success' => false, 'message' => 'Hành động không hợp lệ.'], 400);
    }

    /**
     * Tài xế xác nhận đã giao hàng thành công.
     */
    public function confirmDelivery(Order $order)
    {
        if ($order->driver_id === Auth::guard('driver')->id() && $order->status === 'delivering') {
            $order->status = 'delivered';
            $order->actual_delivery_time = Carbon::now();
            $order->save();
            return response()->json(['success' => true, 'message' => 'Đã giao hàng thành công!']);
        }
        return response()->json(['success' => false, 'message' => 'Hành động không hợp lệ.'], 400);
    }

    /**
     * API để lấy các đơn hàng có sẵn cho tài xế nhận.
     */
    public function available()
    {
        // Lấy các đơn hàng chưa có tài xế và đang chờ
        $availableOrders = Order::whereNull('driver_id')
                                ->where('status', 'pending')
                                ->latest()
                                ->get();

        return response()->json($availableOrders);
    }
}