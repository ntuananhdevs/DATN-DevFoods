<?php

namespace App\Http\Controllers\Branch;

use App\Events\NewOrderAvailable;
use App\Events\OrderStatusUpdated;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Branch;
use App\Models\Order;

class BranchOrderController extends Controller
{
    public function index(Request $request)
    {
        $manager = Auth::guard('manager')->user();
        $branch = $manager ? $manager->branch : null;
        $orders = $branch ? Order::where('branch_id', $branch->id)->get() : collect();
        return view('branch.orders', compact('orders', 'branch'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:confirmed,cancelled_by_branch',
        ]);

        $newStatus = $request->status;

        // Chỉ cho phép cập nhật khi đơn hàng đang chờ xác nhận
        if ($order->status !== 'awaiting_confirmation') {
            return back()->with('error', 'Hành động không hợp lệ.');
        }

        $order->status = $newStatus;
        $order->save();

        // 1. Luôn thông báo cho khách hàng về sự thay đổi trạng thái
        broadcast(new OrderStatusUpdated($order));

        // 2. Nếu chi nhánh XÁC NHẬN đơn hàng
        if ($newStatus === 'confirmed') {
            // Kích hoạt sự kiện để tất cả tài xế nhận được thông báo đơn hàng mới
            broadcast(new NewOrderAvailable($order));
            return back()->with('success', 'Đã xác nhận đơn hàng và gửi thông báo tới tài xế.');
        }

        return back()->with('success', 'Đã cập nhật trạng thái đơn hàng.');
    }
}
