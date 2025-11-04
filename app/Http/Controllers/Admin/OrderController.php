<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Models\Branch;
use Illuminate\Support\Facades\Log;
use App\Events\Order\OrderStatusUpdated;
use App\Events\Order\OrderConfirmed;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['customer', 'branch', 'payment'])
            ->where('status', '!=', 'pending_payment'); // Ẩn đơn hàng chưa thanh toán

        // Lọc theo trạng thái nếu có
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Lọc theo mã đơn hàng
        if ($request->filled('order_code')) {
            $query->where('order_code', 'like', '%' . $request->order_code . '%');
        }

        // Lọc theo tên khách hàng
        if ($request->filled('customer_name')) {
            $query->whereHas('customer', function($q) use ($request) {
                $q->where('full_name', 'like', '%' . $request->customer_name . '%')
                  ->orWhere('email', 'like', '%' . $request->customer_name . '%')
                  ->orWhere('phone', 'like', '%' . $request->customer_name . '%');
            });
        }

        // Lọc theo chi nhánh
        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        // Lọc theo ngày đơn (từ ngày - đến ngày)
        if ($request->filled('date_from')) {
            try {
                $dateFrom = \Carbon\Carbon::createFromFormat('d/m/Y', $request->date_from)->startOfDay();
                $query->where('created_at', '>=', $dateFrom);
            } catch (\Exception $e) {
                // Ignore invalid date format
            }
        }

        if ($request->filled('date_to')) {
            try {
                $dateTo = \Carbon\Carbon::createFromFormat('d/m/Y', $request->date_to)->endOfDay();
                $query->where('created_at', '<=', $dateTo);
            } catch (\Exception $e) {
                // Ignore invalid date format
            }
        }

        // Lọc theo ngày đơn (ngày cụ thể - để tương thích với code cũ)
        if ($request->filled('date') && !$request->filled('date_from') && !$request->filled('date_to')) {
            try {
                $date = \Carbon\Carbon::createFromFormat('d/m/Y', $request->date);
                $query->whereDate('created_at', $date);
            } catch (\Exception $e) {
                // Ignore invalid date format
            }
        }

        // Lọc theo phương thức thanh toán
        if ($request->filled('payment_method')) {
            $query->whereHas('payment', function($q) use ($request) {
                if ($request->payment_method === 'cash') {
                    // Tìm cả 'cash' và 'cod' để backward compatibility
                    $q->whereIn('payment_method', ['cash', 'cod']);
                } else {
                    $q->where('payment_method', $request->payment_method);
                }
            });
        }

        // Lọc theo trạng thái thanh toán
        if ($request->filled('payment_status')) {
            $query->whereHas('payment', function($q) use ($request) {
                $q->where('payment_status', $request->payment_status);
            });
        }

        // Lọc theo khoảng giá trị đơn hàng
        if ($request->filled('total_from')) {
            $query->where('total_amount', '>=', $request->total_from);
        }

        if ($request->filled('total_to')) {
            $query->where('total_amount', '<=', $request->total_to);
        }

        // Sắp xếp
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        
        $allowedSorts = ['created_at', 'total_amount', 'order_code'];
        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortOrder);
        } else {
            $query->latest();
        }

        $orders = $query->paginate(20);

        // Lấy danh sách chi nhánh
        $branches = Branch::all();

        // Đếm số lượng đơn theo từng trạng thái (áp dụng các bộ lọc hiện tại)
        $baseQuery = Order::query()->where('status', '!=', 'pending_payment');
        
        // Áp dụng các bộ lọc (trừ status) để đếm chính xác
        if ($request->filled('order_code')) {
            $baseQuery->where('order_code', 'like', '%' . $request->order_code . '%');
        }
        if ($request->filled('customer_name')) {
            $baseQuery->whereHas('customer', function($q) use ($request) {
                $q->where('full_name', 'like', '%' . $request->customer_name . '%')
                  ->orWhere('email', 'like', '%' . $request->customer_name . '%')
                  ->orWhere('phone', 'like', '%' . $request->customer_name . '%');
            });
        }
        if ($request->filled('branch_id')) {
            $baseQuery->where('branch_id', $request->branch_id);
        }
        if ($request->filled('date_from')) {
            try {
                $dateFrom = \Carbon\Carbon::createFromFormat('d/m/Y', $request->date_from)->startOfDay();
                $baseQuery->where('created_at', '>=', $dateFrom);
            } catch (\Exception $e) {}
        }
        if ($request->filled('date_to')) {
            try {
                $dateTo = \Carbon\Carbon::createFromFormat('d/m/Y', $request->date_to)->endOfDay();
                $baseQuery->where('created_at', '<=', $dateTo);
            } catch (\Exception $e) {}
        }
        if ($request->filled('date') && !$request->filled('date_from') && !$request->filled('date_to')) {
            try {
                $date = \Carbon\Carbon::createFromFormat('d/m/Y', $request->date);
                $baseQuery->whereDate('created_at', $date);
            } catch (\Exception $e) {}
        }
        if ($request->filled('payment_method')) {
            $baseQuery->whereHas('payment', function($q) use ($request) {
                if ($request->payment_method === 'cash') {
                    // Tìm cả 'cash' và 'cod' để backward compatibility
                    $q->whereIn('payment_method', ['cash', 'cod']);
                } else {
                    $q->where('payment_method', $request->payment_method);
                }
            });
        }
        if ($request->filled('payment_status')) {
            $baseQuery->whereHas('payment', function($q) use ($request) {
                $q->where('payment_status', $request->payment_status);
            });
        }
        if ($request->filled('total_from')) {
            $baseQuery->where('total_amount', '>=', $request->total_from);
        }
        if ($request->filled('total_to')) {
            $baseQuery->where('total_amount', '<=', $request->total_to);
        }

        $counts = [
            'all' => (clone $baseQuery)->count(),
            'awaiting_confirmation' => (clone $baseQuery)->where('status', 'awaiting_confirmation')->count(),
            'confirmed' => (clone $baseQuery)->where('status', 'confirmed')->count(),
            'awaiting_driver' => (clone $baseQuery)->where('status', 'awaiting_driver')->count(),
            'driver_confirmed' => (clone $baseQuery)->where('status', 'driver_confirmed')->count(),
            'waiting_driver_pick_up' => (clone $baseQuery)->where('status', 'waiting_driver_pick_up')->count(),
            'driver_picked_up' => (clone $baseQuery)->where('status', 'driver_picked_up')->count(),
            'in_transit' => (clone $baseQuery)->where('status', 'in_transit')->count(),
            'delivered' => (clone $baseQuery)->where('status', 'delivered')->count(),
            'item_received' => (clone $baseQuery)->where('status', 'item_received')->count(),
            'cancelled' => (clone $baseQuery)->where('status', 'cancelled')->count(),
            'refunded' => (clone $baseQuery)->where('status', 'refunded')->count(),
        ];

        // Nếu là AJAX request, trả về JSON
        if ($request->ajax() || $request->has('ajax')) {
            $html = '';
            
            if ($orders->count() > 0) {
                foreach ($orders as $order) {
                    $html .= view('admin.order._order_row', compact('order'))->render();
                }
            } else {
                $html = '<tr id="empty-state"><td colspan="9" class="text-center py-8 text-gray-500">Không có đơn hàng nào.</td></tr>';
            }
            
            return response()->json([
                'html' => $html,
                'counts' => $counts,
                'pagination' => [
                    'from' => $orders->firstItem(),
                    'to' => $orders->lastItem(),
                    'total' => $orders->total(),
                    'links' => $orders->appends($request->except('page'))->links()->render()
                ]
            ]);
        }
        
        return view('admin.order.index', [
            'orders' => $orders,
            'status' => $request->status,
            'order_code' => $request->order_code,
            'customer_name' => $request->customer_name,
            'date_from' => $request->date_from,
            'date_to' => $request->date_to,
            'date' => $request->date,
            'payment_method' => $request->payment_method,
            'payment_status' => $request->payment_status,
            'total_from' => $request->total_from,
            'total_to' => $request->total_to,
            'sort_by' => $request->sort_by,
            'sort_order' => $request->sort_order,
            'branches' => $branches,
            'counts' => $counts,
        ]);
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

    /**
     * Display the specified order.
     */
    public function show($id)
    {
        $order = Order::with([
            'customer',
            'driver',
            'branch',
            'payment',
            'address',
            'orderItems.productVariant.product.primaryImage',
            'orderItems.productVariant.variantValues.attribute',
            'orderItems.combo',
            'orderItems.toppings.topping',
            'statusHistory.changedBy',
            'cancellation.cancelledBy'
        ])->select([
            'orders.*',
            'delivery_address_line_snapshot',
            'delivery_ward_snapshot',
            'delivery_district_snapshot',
            'delivery_province_snapshot',
            'delivery_phone_snapshot',
            'delivery_recipient_name_snapshot'
        ])->findOrFail($id);

        return view('admin.order.show', compact('order'));
    }

    /**
     * Export orders to Excel
     */
    public function export()
    {
        // Tạm thời redirect về trang index
        return redirect()->route('admin.orders.index');
    }

    public function updateStatus(Request $request, $orderId)
    {
        $order = Order::findOrFail($orderId);
        
        $request->validate([
            'status' => 'required|string|in:pending_payment,awaiting_confirmation,confirmed,awaiting_driver,driver_confirmed,waiting_driver_pick_up,driver_picked_up,in_transit,delivered,item_received,cancelled,refunded,payment_failed,payment_received,order_failed'
        ]);
        
        $newStatus = $request->status;
        $oldStatus = $order->status;
        
        // Validate status transition
        $allowedTransitions = [
            'pending_payment' => ['awaiting_confirmation', 'cancelled'],
            'awaiting_confirmation' => ['confirmed', 'cancelled'],
            'confirmed' => ['awaiting_driver', 'cancelled'],
            'awaiting_driver' => ['driver_confirmed', 'cancelled'],
            'driver_confirmed' => ['waiting_driver_pick_up', 'cancelled'],
            'waiting_driver_pick_up' => ['driver_picked_up', 'cancelled'],
            'driver_picked_up' => ['in_transit', 'cancelled'],
            'in_transit' => ['delivered', 'cancelled'],
            'delivered' => ['item_received', 'refunded'],
            'item_received' => ['refunded'],
            'cancelled' => ['refunded'],
            'refunded' => [],
            'payment_failed' => ['payment_received', 'cancelled'],
            'payment_received' => ['confirmed'],
            'order_failed' => ['cancelled']
        ];
        
        if (!isset($allowedTransitions[$oldStatus]) || !in_array($newStatus, $allowedTransitions[$oldStatus])) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể chuyển từ trạng thái "' . $order->statusText . '" sang "' . $this->getStatusText($newStatus) . '"'
            ], 400);
        }
        
        // Update order status
        $order->update(['status' => $newStatus]);
        
        // Dispatch event for real-time updates
        if ($newStatus === 'confirmed') {
            \Log::info('Broadcasting OrderConfirmed event', [
                'order_id' => $order->id,
                'order_code' => $order->order_code,
                'status' => $newStatus
            ]);
            broadcast(new OrderConfirmed($order));
        } else {
            \Log::info('Broadcasting OrderStatusUpdated event', [
                'order_id' => $order->id,
                'order_code' => $order->order_code,
                'old_status' => $oldStatus,
                'new_status' => $newStatus
            ]);
            broadcast(new OrderStatusUpdated($order, false, $oldStatus, $newStatus));
        }
        
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Cập nhật trạng thái đơn hàng thành công!',
                'order' => [
                    'id' => $order->id,
                    'status' => $order->status,
                    'status_text' => $order->statusText,
                    'status_color' => $order->statusColor,
                    'status_icon' => $order->statusIcon,
                ]
            ]);
        }
        
        return redirect()->route('admin.orders.show', $orderId)
            ->with('success', 'Cập nhật trạng thái đơn hàng thành công!');
    }
    
    private function getStatusText($status)
    {
        $statusMap = [
            'pending_payment' => 'Chưa thanh toán',
            'awaiting_confirmation' => 'Chờ xác nhận',
            'confirmed' => 'Đã xác nhận',
            'awaiting_driver' => 'Chờ tài xế',
            'driver_confirmed' => 'Tài xế đã xác nhận',
            'waiting_driver_pick_up' => 'Chờ tài xế lấy hàng',
            'driver_picked_up' => 'Tài xế đã lấy hàng',
            'in_transit' => 'Đang giao hàng',
            'delivered' => 'Đã giao hàng',
            'item_received' => 'Đã nhận hàng',
            'cancelled' => 'Đã hủy',
            'refunded' => 'Đã hoàn tiền',
            'payment_failed' => 'Thanh toán thất bại',
            'payment_received' => 'Đã thanh toán',
            'order_failed' => 'Đơn hàng thất bại'
        ];
        
        return $statusMap[$status] ?? $status;
    }

    public function cancel($orderId)
    {
        $order = Order::findOrFail($orderId);
        $oldStatus = $order->status;
        $newStatus = 'cancelled';
        
        $order->status = $newStatus;
        $order->save();

        // Broadcast event for real-time updates
        broadcast(new OrderStatusUpdated($order, false, $oldStatus, $newStatus));

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Đơn hàng đã được hủy thành công!',
                'order' => [
                    'id' => $order->id,
                    'status' => $order->status,
                    'status_text' => $order->statusText,
                    'status_color' => $order->statusColor,
                    'status_icon' => $order->statusIcon,
                ]
            ]);
        }

        return redirect()->route('admin.orders.show', $orderId)
            ->with('success', 'Đơn hàng đã được hủy thành công!');
    }

    public function getCounts(Request $request)
    {
        // Tạo base query với các bộ lọc hiện tại
        $baseQuery = Order::query();
        
        // Áp dụng các bộ lọc (trừ status) để đếm chính xác
        if ($request->filled('order_code')) {
            $baseQuery->where('order_code', 'like', '%' . $request->order_code . '%');
        }
        if ($request->filled('customer_name')) {
            $baseQuery->whereHas('customer', function($q) use ($request) {
                $q->where('full_name', 'like', '%' . $request->customer_name . '%')
                  ->orWhere('email', 'like', '%' . $request->customer_name . '%')
                  ->orWhere('phone', 'like', '%' . $request->customer_name . '%');
            });
        }
        if ($request->filled('branch_id')) {
            $baseQuery->where('branch_id', $request->branch_id);
        }
        if ($request->filled('date_from')) {
            try {
                $dateFrom = \Carbon\Carbon::createFromFormat('d/m/Y', $request->date_from)->startOfDay();
                $baseQuery->where('created_at', '>=', $dateFrom);
            } catch (\Exception $e) {}
        }
        if ($request->filled('date_to')) {
            try {
                $dateTo = \Carbon\Carbon::createFromFormat('d/m/Y', $request->date_to)->endOfDay();
                $baseQuery->where('created_at', '<=', $dateTo);
            } catch (\Exception $e) {}
        }
        if ($request->filled('date') && !$request->filled('date_from') && !$request->filled('date_to')) {
            try {
                $date = \Carbon\Carbon::createFromFormat('d/m/Y', $request->date);
                $baseQuery->whereDate('created_at', $date);
            } catch (\Exception $e) {}
        }
        if ($request->filled('payment_method')) {
            $baseQuery->whereHas('payment', function($q) use ($request) {
                if ($request->payment_method === 'cash') {
                    // Tìm cả 'cash' và 'cod' để backward compatibility
                    $q->whereIn('payment_method', ['cash', 'cod']);
                } else {
                    $q->where('payment_method', $request->payment_method);
                }
            });
        }
        if ($request->filled('payment_status')) {
            $baseQuery->whereHas('payment', function($q) use ($request) {
                $q->where('payment_status', $request->payment_status);
            });
        }
        if ($request->filled('total_from')) {
            $baseQuery->where('total_amount', '>=', $request->total_from);
        }
        if ($request->filled('total_to')) {
            $baseQuery->where('total_amount', '<=', $request->total_to);
        }

        $counts = [
            'all' => (clone $baseQuery)->count(),
            'awaiting_confirmation' => (clone $baseQuery)->where('status', 'awaiting_confirmation')->count(),
            'confirmed' => (clone $baseQuery)->where('status', 'confirmed')->count(),
            'awaiting_driver' => (clone $baseQuery)->where('status', 'awaiting_driver')->count(),
            'driver_confirmed' => (clone $baseQuery)->where('status', 'driver_confirmed')->count(),
            'waiting_driver_pick_up' => (clone $baseQuery)->where('status', 'waiting_driver_pick_up')->count(),
            'driver_picked_up' => (clone $baseQuery)->where('status', 'driver_picked_up')->count(),
            'in_transit' => (clone $baseQuery)->where('status', 'in_transit')->count(),
            'delivered' => (clone $baseQuery)->where('status', 'delivered')->count(),
            'item_received' => (clone $baseQuery)->where('status', 'item_received')->count(),
            'cancelled' => (clone $baseQuery)->where('status', 'cancelled')->count(),
            'refunded' => (clone $baseQuery)->where('status', 'refunded')->count(),
            'payment_failed' => (clone $baseQuery)->where('status', 'payment_failed')->count(),
            'payment_received' => (clone $baseQuery)->where('status', 'payment_received')->count(),
            'order_failed' => (clone $baseQuery)->where('status', 'order_failed')->count(),
        ];

        return response()->json([
            'success' => true,
            'counts' => $counts
        ]);
    }

    /**
     * Get order details for AJAX requests
     */
    public function details($id)
    {
        $order = Order::with([
            'customer',
            'driver',
            'branch',
            'payment',
            'orderItems.product',
            'orderItems.toppings',
            'orderItems.combo'
        ])->findOrFail($id);

        return response()->json([
            'success' => true,
            'order' => [
                'id' => $order->id,
                'order_code' => $order->order_code,
                'status' => $order->status,
                'status_text' => $order->statusText,
                'status_color' => $order->statusColor,
                'status_icon' => $order->statusIcon,
                'total_amount' => $order->total_amount,
                'estimated_delivery_time' => $order->estimated_delivery_time,
                'actual_delivery_time' => $order->actual_delivery_time,
                'customer' => $order->customer,
                'driver' => $order->driver,
                'branch' => $order->branch,
                'payment' => $order->payment,
                'order_items' => $order->orderItems,
                'delivery_address' => $order->delivery_address,
                'created_at' => $order->created_at,
                'updated_at' => $order->updated_at,
            ]
        ]);
    }

    /**
     * Refresh order status for AJAX requests
     */
    public function refreshStatus($id)
    {
        $order = Order::with(['customer', 'driver', 'branch', 'payment'])->findOrFail($id);
        
        // Get available transitions
        $currentStatus = $order->status;
        $allowedTransitions = [
            'pending_payment' => ['awaiting_confirmation', 'cancelled'],
            'awaiting_confirmation' => ['confirmed', 'cancelled'],
            'confirmed' => ['awaiting_driver', 'cancelled'],
            'awaiting_driver' => ['driver_confirmed', 'cancelled'],
            'driver_confirmed' => ['waiting_driver_pick_up', 'cancelled'],
            'waiting_driver_pick_up' => ['driver_picked_up', 'cancelled'],
            'driver_picked_up' => ['in_transit', 'cancelled'],
            'in_transit' => ['delivered', 'cancelled'],
            'delivered' => ['item_received', 'refunded'],
            'item_received' => ['refunded'],
            'cancelled' => ['refunded'],
            'refunded' => [],
            'payment_failed' => ['payment_received', 'cancelled'],
            'payment_received' => ['confirmed'],
            'order_failed' => ['cancelled']
        ];

        $transitions = $allowedTransitions[$currentStatus] ?? [];
        $transitionsWithText = [];

        foreach ($transitions as $status) {
            $transitionsWithText[] = [
                'status' => $status,
                'text' => $this->getStatusText($status)
            ];
        }

        return response()->json([
            'success' => true,
            'order' => [
                'id' => $order->id,
                'status' => $order->status,
                'status_text' => $order->statusText,
                'status_color' => $order->statusColor,
                'status_icon' => $order->statusIcon,
                'estimated_delivery_time' => $order->estimated_delivery_time,
                'actual_delivery_time' => $order->actual_delivery_time,
                'driver' => $order->driver,
                'updated_at' => $order->updated_at,
            ],
            'available_transitions' => $transitionsWithText
        ]);
    }

    /**
     * Get available status transitions for an order
     */
    public function availableTransitions($id)
    {
        $order = Order::findOrFail($id);
        $currentStatus = $order->status;

        $allowedTransitions = [
            'awaiting_confirmation' => ['confirmed', 'cancelled'],
            'confirmed' => ['awaiting_driver', 'cancelled'],
            'awaiting_driver' => ['driver_confirmed', 'cancelled'],
            'driver_confirmed' => ['waiting_driver_pick_up', 'cancelled'],
            'waiting_driver_pick_up' => ['driver_picked_up', 'cancelled'],
            'driver_picked_up' => ['in_transit', 'cancelled'],
            'in_transit' => ['delivered', 'cancelled'],
            'delivered' => ['item_received', 'refunded'],
            'item_received' => ['refunded'],
            'cancelled' => ['refunded'],
            'refunded' => [],
            'payment_failed' => ['payment_received', 'cancelled'],
            'payment_received' => ['confirmed'],
            'order_failed' => ['cancelled']
        ];

        $transitions = $allowedTransitions[$currentStatus] ?? [];
        $transitionsWithText = [];

        foreach ($transitions as $status) {
            $transitionsWithText[] = [
                'status' => $status,
                'text' => $this->getStatusText($status)
            ];
        }

        return response()->json([
            'success' => true,
            'current_status' => $currentStatus,
            'current_status_text' => $this->getStatusText($currentStatus),
            'available_transitions' => $transitionsWithText
        ]);
    }
}