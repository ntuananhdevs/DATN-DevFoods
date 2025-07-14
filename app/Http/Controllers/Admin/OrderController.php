<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Models\Branch;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['customer', 'branch']);

        // Lọc theo trạng thái nếu có
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Lọc theo mã đơn hàng
        if ($request->filled('order_code')) {
            $query->where('order_code', 'like', '%' . $request->order_code . '%');
        }

        // Lọc theo chi nhánh
        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        // Lọc theo ngày (nếu có)
        if ($request->filled('date')) {
            $date = \Carbon\Carbon::createFromFormat('d/m/Y', $request->date);
            $query->whereDate('created_at', $date);
        }

        $orders = $query->latest()->paginate(20);

        // Lấy danh sách chi nhánh
        $branches = Branch::all();

        // Đếm số lượng đơn theo từng trạng thái
        $counts = [
            'all' => Order::count(),
            'awaiting_confirmation' => Order::where('status', 'awaiting_confirmation')->count(),
            'awaiting_driver' => Order::where('status', 'awaiting_driver')->count(),
            'in_transit' => Order::where('status', 'in_transit')->count(),
            'delivered' => Order::where('status', 'delivered')->count(),
            'cancelled' => Order::where('status', 'cancelled')->count(),
            'refunded' => Order::where('status', 'refunded')->count(),
        ];

        return view('admin.order.index', [
            'orders' => $orders,
            'status' => $request->status,
            'order_code' => $request->order_code,
            'branches' => $branches,
            'counts' => $counts,
        ]);
    }

    public function show($orderId)
    {
        $order = Order::with(['customer', 'branch', 'driver'])->findOrFail($orderId);
        return view('admin.order.show', compact('order'));
    }

    public function getOrderRow($orderId)
    {
        $order = Order::with(['customer', 'branch'])->findOrFail($orderId);
        
        // Trả về HTML partial
        $html = view('admin.order._order_row', compact('order'))->render();
        
        return response($html)->header('Content-Type', 'text/html');
    }

    public function notificationItem($orderId)
    {
        $order = Order::with(['branch', 'customer'])->findOrFail($orderId);

        $notification = (object)[
            'id' => 'new-order-' . $order->id,
            'read_at' => null,
            'created_at' => now(),
            'data' => [
                'order_id' => $order->id,
                'message' => 'Đơn hàng mới #' . $order->order_code,
                'branch_name' => $order->branch->name ?? '',
                'customer_name' => $order->customer->name ?? '',
            ]
        ];

        $html = view('partials.admin._notification_items', ['notifications' => [$notification]])->render();
        return response($html)->header('Content-Type', 'text/html');
    }

    public function export()
    {
        // Tạm thời redirect về trang index
        return redirect()->route('admin.orders.index');
    }
} 