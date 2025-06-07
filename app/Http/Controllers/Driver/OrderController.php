<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Models\OrderItem;
use Carbon\Carbon;

class OrderController extends Controller
{
    /**
     * Display a listing of orders for the driver.
     */
    public function index(Request $request)
    {
        $driver = Auth::guard('driver')->user();
        
        if (!$driver) {
            return redirect()->route('driver.login')->with('error', 'Vui lòng đăng nhập để tiếp tục.');
        }
        
        $status = $request->get('status', 'all');
        
        $query = Order::with(['customer', 'branch', 'orderItems.product'])
            ->where(function($q) use ($driver) {
                $q->where('driver_id', $driver->id)
                  ->orWhere('driver_id', null);
            });
        
        if ($status !== 'all') {
            $query->where('status', $status);
        }
        
        $orders = $query->orderBy('created_at', 'desc')->paginate(20);
        
        return view('driver.orders.index', compact('orders', 'status'));
    }
    
    /**
     * Show the specified order.
     */
    public function show($id)
    {
        $driver = Auth::guard('driver')->user();
        
        if (!$driver) {
            return redirect()->route('driver.login')->with('error', 'Vui lòng đăng nhập để tiếp tục.');
        }
        
        $order = Order::with(['customer', 'branch', 'orderItems.product', 'driver'])
            ->where(function($q) use ($driver) {
                $q->where('driver_id', $driver->id)
                  ->orWhere('driver_id', null);
            })
            ->findOrFail($id);
        
        return view('driver.orders.show', compact('order'));
    }
    
    /**
     * Accept an order for delivery.
     */
    public function accept($id)
    {
        $driver = Auth::guard('driver')->user();
        
        if (!$driver) {
            return redirect()->route('driver.login')->with('error', 'Vui lòng đăng nhập để tiếp tục.');
        }
        
        $order = Order::where('status', 'pending')
            ->where('driver_id', null)
            ->findOrFail($id);
        
        $order->update([
            'driver_id' => $driver->id,
            'status' => 'accepted',
            'accepted_at' => now(),
        ]);
        
        return redirect()->route('driver.orders.show', $order->id)
            ->with('success', 'Đã nhận đơn hàng thành công!');
    }
    
    /**
     * Start pickup process.
     */
    public function startPickup($id)
    {
        $driver = Auth::guard('driver')->user();
        
        if (!$driver) {
            return redirect()->route('driver.login')->with('error', 'Vui lòng đăng nhập để tiếp tục.');
        }
        
        $order = Order::where('driver_id', $driver->id)
            ->where('status', 'accepted')
            ->findOrFail($id);
        
        $order->update([
            'status' => 'picking_up',
            'pickup_started_at' => now(),
        ]);
        
        return redirect()->route('driver.orders.show', $order->id)
            ->with('success', 'Đã bắt đầu lấy hàng!');
    }
    
    /**
     * Confirm pickup completion.
     */
    public function confirmPickup($id)
    {
        $driver = Auth::guard('driver')->user();
        
        if (!$driver) {
            return redirect()->route('driver.login')->with('error', 'Vui lòng đăng nhập để tiếp tục.');
        }
        
        $order = Order::where('driver_id', $driver->id)
            ->where('status', 'picking_up')
            ->findOrFail($id);
        
        $order->update([
            'status' => 'delivering',
            'picked_up_at' => now(),
        ]);
        
        return redirect()->route('driver.orders.show', $order->id)
            ->with('success', 'Đã lấy hàng thành công! Bắt đầu giao hàng.');
    }
    
    /**
     * Confirm delivery completion.
     */
    public function confirmDelivery($id)
    {
        $driver = Auth::guard('driver')->user();
        
        if (!$driver) {
            return redirect()->route('driver.login')->with('error', 'Vui lòng đăng nhập để tiếp tục.');
        }
        
        $order = Order::where('driver_id', $driver->id)
            ->where('status', 'delivering')
            ->findOrFail($id);
        
        // Calculate driver earning (example: 10% of shipping fee)
        $driverEarning = $order->shipping_fee * 0.1;
        
        $order->update([
            'status' => 'delivered',
            'delivered_at' => now(),
            'driver_earning' => $driverEarning,
        ]);
        
        return redirect()->route('driver.orders.show', $order->id)
            ->with('success', 'Đã giao hàng thành công!');
    }
    
    /**
     * Cancel an order.
     */
    public function cancel($id, Request $request)
    {
        $driver = Auth::guard('driver')->user();
        
        if (!$driver) {
            return redirect()->route('driver.login')->with('error', 'Vui lòng đăng nhập để tiếp tục.');
        }
        
        $order = Order::where('driver_id', $driver->id)
            ->whereIn('status', ['accepted', 'picking_up'])
            ->findOrFail($id);
        
        $request->validate([
            'cancel_reason' => 'required|string|max:500',
        ]);
        
        $order->update([
            'status' => 'pending',
            'driver_id' => null,
            'cancel_reason' => $request->cancel_reason,
            'cancelled_at' => now(),
        ]);
        
        return redirect()->route('driver.orders.index')
            ->with('success', 'Đã hủy đơn hàng!');
    }
    
    /**
     * Get available orders for AJAX requests.
     */
    public function available()
    {
        $orders = Order::with(['customer', 'branch'])
            ->where('status', 'pending')
            ->where('driver_id', null)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($order) {
                return [
                    'id' => $order->id,
                    'order_time' => $order->created_at->format('H:i d/m/Y'),
                    'pickup_branch' => $order->branch->name ?? 'N/A',
                    'delivery_address' => $order->delivery_address,
                    'customer_name' => $order->customer_name,
                    'customer_phone' => $order->customer_phone,
                    'shipping_fee' => $order->shipping_fee ?? 0,
                    'total_amount' => $order->total_amount,
                    'distance' => '2.5', // This should be calculated
                ];
            });
        
        return response()->json($orders);
    }
}