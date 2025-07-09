<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Models\Branch;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::query();

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
        $order = \App\Models\Order::with(['customer', 'branch'])->findOrFail($orderId);
        // Tạm thời dump ra thông tin order, bạn có thể trả về view chi tiết sau
        return view('admin.order.show', compact('order'));
    }
} 