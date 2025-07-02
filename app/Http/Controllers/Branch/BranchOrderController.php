<?php

namespace App\Http\Controllers\Branch;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Branch;
use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\Models\OrderCancellation;
use App\Events\OrderStatusUpdated;
use App\Events\Branch\NewOrderReceived;

class BranchOrderController extends Controller
{
    public function index(Request $request)
    {
        $branch = Auth::guard('manager')->user()->branch;
        
        if (!$branch) {
            return redirect()->back()->with('error', 'Không tìm thấy thông tin chi nhánh');
        }

        // Build query
        $query = Order::with([
            'customer',
            'driver',
            'orderItems.productVariant.product',
            'orderItems.combo',
            'orderItems.toppings.topping',
            'statusHistory.changedBy',
            'cancellation.cancelledBy',
            'payment.paymentMethod'
        ])->where('branch_id', $branch->id);

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('order_code', 'like', "%{$search}%")
                  ->orWhere('guest_name', 'like', "%{$search}%")
                  ->orWhere('guest_phone', 'like', "%{$search}%")
                  ->orWhereHas('customer', function($customerQuery) use ($search) {
                      $customerQuery->where('name', 'like', "%{$search}%")
                                   ->orWhere('phone', 'like', "%{$search}%");
                  });
            });
        }

        // Status filter
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Date range filter
        if ($request->filled('date_from')) {
            $query->whereDate('order_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('order_date', '<=', $request->date_to);
        }

        // Payment method filter
        if ($request->filled('payment_method') && $request->payment_method !== 'all') {
            $query->whereHas('payment.paymentMethod', function($q) use ($request) {
                $q->where('name', $request->payment_method);
            });
        }

        // Luôn sắp xếp đơn hàng mới nhất lên đầu
        $query->orderBy('order_date', 'desc');

        // Get orders with pagination
        $orders = $query->paginate(20);

        // Get status counts
        $statusCounts = [
            'all' => Order::where('branch_id', $branch->id)->count(),
            'awaiting_confirmation' => Order::where('branch_id', $branch->id)->where('status', 'awaiting_confirmation')->count(),
            'awaiting_driver' => Order::where('branch_id', $branch->id)->where('status', 'awaiting_driver')->count(),
            'in_transit' => Order::where('branch_id', $branch->id)->where('status', 'in_transit')->count(),
            'delivered' => Order::where('branch_id', $branch->id)->where('status', 'delivered')->count(),
            'cancelled' => Order::where('branch_id', $branch->id)->where('status', 'cancelled')->count(),
            'refunded' => Order::where('branch_id', $branch->id)->where('status', 'refunded')->count(),
        ];

        // Get payment methods for filter
        $paymentMethods = \App\Models\PaymentMethod::where('active', true)->get();

        if ($request->ajax()) {
            return view('branch.orders.partials.grid', compact('orders'))->render();
        }
        return view('branch.orders.index', compact('orders', 'statusCounts', 'paymentMethods'));
    }

    public function show($id)
    {
        $branch = Auth::guard('manager')->user()->branch;
        
        $order = Order::with([
            'customer',
            'driver',
            'orderItems.productVariant.product',
            'orderItems.combo',
            'orderItems.toppings.topping',
            'statusHistory.changedBy',
            'cancellation.cancelledBy',
            'payment.paymentMethod',
            'address'
        ])->where('branch_id', $branch->id)
          ->where('id', $id)
          ->firstOrFail();

        return view('branch.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, $id)
    {
        $branch = Auth::guard('manager')->user()->branch;
        
        $order = Order::where('branch_id', $branch->id)
                     ->where('id', $id)
                     ->firstOrFail();

        $oldStatus = $order->status;
        $newStatus = $request->status;
        $note = $request->note;

        // Validate status transition
        $validTransitions = $this->getValidStatusTransitions($oldStatus);
        if (!in_array($newStatus, $validTransitions)) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể chuyển từ trạng thái ' . $this->getStatusText($oldStatus) . ' sang ' . $this->getStatusText($newStatus)
            ], 400);
        }

        // Additional validations for specific status changes
        $validationResult = $this->validateStatusChange($order, $newStatus);
        if (!$validationResult['valid']) {
            return response()->json([
                'success' => false,
                'message' => $validationResult['message']
            ], 400);
        }

        // Update order status
        $order->update(['status' => $newStatus]);

        // Handle specific status actions
        $this->handleStatusSpecificActions($order, $newStatus);

        // Create status history
        OrderStatusHistory::create([
            'order_id' => $order->id,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'changed_by' => Auth::guard('manager')->id(),
            'changed_by_role' => 'branch_manager',
            'note' => $note ?: $this->getDefaultNote($oldStatus, $newStatus),
            'changed_at' => now()
        ]);

        // Broadcast order status update event
        event(new OrderStatusUpdated($order, $oldStatus, $newStatus));

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật trạng thái thành công',
            'new_status' => $newStatus,
            'status_text' => $this->getStatusText($newStatus)
        ]);
    }

    /**
     * Get valid status transitions for current status
     */
    private function getValidStatusTransitions($currentStatus)
    {
        $transitions = [
            'pending' => ['processing', 'cancelled'],
            'processing' => ['ready', 'cancelled'],
            'ready' => ['delivery', 'cancelled'],
            'delivery' => ['completed', 'cancelled'],
            'completed' => [],
            'cancelled' => []
        ];

        return $transitions[$currentStatus] ?? [];
    }

    /**
     * Validate if status change is allowed
     */
    private function validateStatusChange($order, $newStatus)
    {
        // Check if order can be cancelled
        if ($newStatus === 'cancelled') {
            if (in_array($order->status, ['completed', 'cancelled'])) {
                return [
                    'valid' => false,
                    'message' => 'Không thể hủy đơn hàng đã hoàn thành hoặc đã hủy'
                ];
            }
        }

        // Check if order can be completed
        if ($newStatus === 'completed') {
            if ($order->status !== 'delivery') {
                return [
                    'valid' => false,
                    'message' => 'Chỉ có thể hoàn thành đơn hàng đang giao'
                ];
            }
        }

        // Check if order can be delivered
        if ($newStatus === 'delivery') {
            if ($order->status !== 'ready') {
                return [
                    'valid' => false,
                    'message' => 'Chỉ có thể giao đơn hàng đã sẵn sàng'
                ];
            }
        }

        return ['valid' => true, 'message' => ''];
    }

    /**
     * Handle specific actions for status changes
     */
    private function handleStatusSpecificActions($order, $newStatus)
    {
        switch ($newStatus) {
            case 'processing':
                // Có thể thêm logic gửi thông báo cho khách hàng
                break;
            
            case 'ready':
                // Có thể thêm logic tìm tài xế
                break;
            
            case 'delivery':
                // Có thể thêm logic gán tài xế nếu chưa có
                break;
            
            case 'completed':
                // Cập nhật thời gian giao hàng thực tế
                $order->update(['actual_delivery_time' => now()]);
                break;
            
            case 'cancelled':
                // Tạo bản ghi hủy đơn hàng
                OrderCancellation::create([
                    'order_id' => $order->id,
                    'cancelled_by' => Auth::guard('manager')->id(),
                    'cancellation_type' => 'restaurant_cancel',
                    'cancellation_date' => now(),
                    'reason' => 'Hủy bởi chi nhánh',
                    'cancellation_stage' => $this->getCancellationStage($order->getOriginal('status')),
                    'notes' => 'Hủy đơn hàng từ thao tác nhanh'
                ]);
                break;
        }
    }

    /**
     * Get default note for status change
     */
    private function getDefaultNote($oldStatus, $newStatus)
    {
        $notes = [
            'pending' => [
                'processing' => 'Xác nhận đơn hàng',
                'cancelled' => 'Hủy đơn hàng'
            ],
            'processing' => [
                'ready' => 'Sẵn sàng giao hàng',
                'cancelled' => 'Hủy đơn hàng'
            ],
            'ready' => [
                'delivery' => 'Giao cho tài xế',
                'cancelled' => 'Hủy đơn hàng'
            ],
            'delivery' => [
                'completed' => 'Hoàn thành giao hàng',
                'cancelled' => 'Hủy đơn hàng'
            ]
        ];

        return $notes[$oldStatus][$newStatus] ?? 'Thay đổi trạng thái đơn hàng';
    }

    /**
     * Get status text for display
     */
    private function getStatusText($status)
    {
        $statusTexts = [
            'pending' => 'Chờ xác nhận',
            'processing' => 'Đang xử lý',
            'ready' => 'Sẵn sàng giao',
            'delivery' => 'Đang giao hàng',
            'completed' => 'Hoàn thành',
            'cancelled' => 'Đã hủy'
        ];

        return $statusTexts[$status] ?? $status;
    }

    public function cancel(Request $request, $id)
    {
        $branch = Auth::guard('manager')->user()->branch;
        
        $order = Order::where('branch_id', $branch->id)
                     ->where('id', $id)
                     ->firstOrFail();

        // Create cancellation record
        OrderCancellation::create([
            'order_id' => $order->id,
            'cancelled_by' => Auth::guard('manager')->id(),
            'cancellation_type' => 'restaurant_cancel',
            'cancellation_date' => now(),
            'reason' => $request->reason,
            'cancellation_stage' => $this->getCancellationStage($order->status),
            'notes' => $request->notes
        ]);

        // Update order status
        $order->update(['status' => 'cancelled']);

        // Create status history
        OrderStatusHistory::create([
            'order_id' => $order->id,
            'old_status' => $order->getOriginal('status'),
            'new_status' => 'cancelled',
            'changed_by' => Auth::guard('manager')->id(),
            'changed_by_role' => 'branch_manager',
            'note' => 'Hủy đơn hàng: ' . $request->reason,
            'changed_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Hủy đơn hàng thành công'
        ]);
    }

    private function getCancellationStage($status)
    {
        $stageMap = [
            'pending' => 'before_processing',
            'processing' => 'processing',
            'ready' => 'ready_for_delivery',
            'delivery' => 'during_delivery'
        ];

        return $stageMap[$status] ?? 'before_processing';
    }
}
