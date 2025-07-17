<?php

namespace App\Http\Controllers\Branch;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Branch;
use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\Models\OrderCancellation;
use App\Events\Order\OrderStatusUpdated;
use App\Events\Order\NewOrderReceived;
use App\Events\Order\OrderConfirmed;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $branch = Auth::guard('manager')->user()->branch;

        if (!$branch) {
            return redirect()->back()->with('error', 'Không tìm thấy thông tin chi nhánh');
        }

        // Handle check_new parameter for polling fallback
        if ($request->has('check_new') && $request->check_new === 'true') {
            $lastOrderTime = $request->input('last_order_time');
            $hasNewOrders = false;

            if ($lastOrderTime) {
                $hasNewOrders = Order::where('branch_id', $branch->id)
                    ->where('created_at', '>', $lastOrderTime)
                    ->exists();
            } else {
                // If no last_order_time provided, check if there are any orders created in the last 5 minutes
                $hasNewOrders = Order::where('branch_id', $branch->id)
                    ->where('created_at', '>', now()->subMinutes(5))
                    ->exists();
            }

            return response()->json([
                'hasNewOrders' => $hasNewOrders,
                'timestamp' => now()->toISOString()
            ]);
        }

        // Lấy vị trí chi nhánh
        $branchLat = $branch->latitude;
        $branchLng = $branch->longitude;

        // Build query
        $query = Order::with([
            'customer',
            'driver',
            'orderItems.productVariant.product',
            'orderItems.combo',
            'orderItems.toppings.topping',
            'statusHistory.changedBy',
            'cancellation.cancelledBy',
            'payment',
            'address' // Đảm bảo load address
        ])->where('branch_id', $branch->id);

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_code', 'like', "%{$search}%")
                    ->orWhere('guest_name', 'like', "%{$search}%")
                    ->orWhere('guest_phone', 'like', "%{$search}%")
                    ->orWhereHas('customer', function ($customerQuery) use ($search) {
                        $customerQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    });
            });
        }

        // Status filter
        if ($request->filled('status') && $request->status !== 'all') {
            if ($request->status === 'awaiting_driver') {
                $query->whereIn('status', [
                    'confirmed',
                    'awaiting_driver',
                    'driver_confirmed',
                    'waiting_driver_pick_up',
                    'driver_picked_up'
                ]);
            } else {
                $query->where('status', $request->status);
            }
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
            $query->whereHas('payment', function ($q) use ($request) {
                $q->where('payment_method', $request->payment_method);
            });
        }

        // Luôn sắp xếp đơn hàng mới nhất lên đầu
        $query->orderBy('order_date', 'desc');

        // Get orders with pagination
        $orders = $query->paginate(20);

        // Tính khoảng cách cho từng order
        foreach ($orders as $order) {
            $orderLat = $order->address->latitude ?? null;
            $orderLng = $order->address->longitude ?? null;
            if ($branchLat && $branchLng && $orderLat && $orderLng) {
                $order->distance_km = $this->calculateDistance($branchLat, $branchLng, $orderLat, $orderLng);
            } else {
                $order->distance_km = null;
            }
        }

        // Get status counts
        $statusCounts = [
            'all' => Order::where('branch_id', $branch->id)->count(),
            'awaiting_confirmation' => Order::where('branch_id', $branch->id)->where('status', 'awaiting_confirmation')->count(),
            'awaiting_driver' => Order::where('branch_id', $branch->id)
                ->whereIn('status', [
                    'confirmed',
                    'awaiting_driver',
                    'driver_confirmed',
                    'waiting_driver_pick_up',
                    'driver_picked_up'
                ])->count(),
            'confirmed' => Order::where('branch_id', $branch->id)->where('status', 'confirmed')->count(),
            'driver_assigned' => Order::where('branch_id', $branch->id)->where('status', 'driver_assigned')->count(),
            'driver_confirmed' => Order::where('branch_id', $branch->id)->where('status', 'driver_confirmed')->count(),
            'waiting_driver_pick_up' => Order::where('branch_id', $branch->id)->where('status', 'waiting_driver_pick_up')->count(),
            'driver_picked_up' => Order::where('branch_id', $branch->id)->where('status', 'driver_picked_up')->count(),
            'in_transit' => Order::where('branch_id', $branch->id)->where('status', 'in_transit')->count(),
            'delivered' => Order::where('branch_id', $branch->id)->where('status', 'delivered')->count(),
            'item_received' => Order::where('branch_id', $branch->id)->where('status', 'item_received')->count(),
            'cancelled' => Order::where('branch_id', $branch->id)->where('status', 'cancelled')->count(),
            'refunded' => Order::where('branch_id', $branch->id)->where('status', 'refunded')->count(),
            'payment_failed' => Order::where('branch_id', $branch->id)->where('status', 'payment_failed')->count(),
            'payment_received' => Order::where('branch_id', $branch->id)->where('status', 'payment_received')->count(),
            'order_failed' => Order::where('branch_id', $branch->id)->where('status', 'order_failed')->count(),
        ];

        // Get payment methods for filter
        $paymentMethods = [
            ['key' => 'all', 'label' => 'Tất cả'],
            ['key' => 'cod', 'label' => 'Tiền mặt'],
            ['key' => 'vnpay', 'label' => 'VNPay'],
            ['key' => 'balance', 'label' => 'Số dư tài khoản'],
        ];

        if ($request->ajax()) {
            return response()->view('branch.orders.partials.orders_grid', compact('orders'));
        }
        return view('branch.orders.index', compact('orders', 'statusCounts', 'paymentMethods', 'branch'));
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
            'payment',
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

        // Validate status transition (UPDATED logic for new statuses)
        $validTransitions = $this->getValidStatusTransitions($oldStatus);
        if (!in_array($newStatus, $validTransitions)) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể chuyển từ trạng thái ' . $this->getStatusText($oldStatus) . ' sang ' . $this->getStatusText($newStatus)
            ], 400);
        }

        // Additional validations for specific status changes (UPDATED logic)
        $validationResult = $this->validateStatusChange($order, $newStatus);
        if (!$validationResult['valid']) {
            return response()->json([
                'success' => false,
                'message' => $validationResult['message']
            ], 400);
        }

        // Update order status
        $order->update(['status' => $newStatus]);

        // Nếu trạng thái mới là 'confirmed' hoặc 'awaiting_driver', dispatch event OrderConfirmed
        if (in_array($newStatus, ['confirmed'])) {
            event(new OrderConfirmed($order));
        }

        // Handle specific status actions

        // Cập nhật trạng thái đơn hàng
        $order->status = $newStatus;
        $order->save();

        // Lấy lại đơn hàng với dữ liệu mới nhất
        $freshOrder = $order->fresh();

        // Ghi lại lịch sử trạng thái
        OrderStatusHistory::create([
            'order_id' => $order->id,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'changed_by' => Auth::guard('manager')->id(),
            'changed_by_role' => 'branch_manager',
            'note' => $note ?? $this->getDefaultNote($oldStatus, $newStatus),
            'changed_at' => now()
        ]);

        // Broadcast sự kiện cập nhật trạng thái đơn hàng
        event(new OrderStatusUpdated($freshOrder)); //

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật trạng thái đơn hàng thành công.',
            'order' => $freshOrder // Trả về đơn hàng đã cập nhật
        ]);
    }

    /**
     * Get valid status transitions for current status
     */
    private function getValidStatusTransitions($currentStatus)
    {
        $transitions = [
            'awaiting_confirmation' => ['awaiting_driver', 'cancelled'],
            'awaiting_driver' => ['in_transit', 'cancelled'],
            'in_transit' => ['delivered', 'cancelled'],
            'delivered' => ['refunded'],
            'cancelled' => [],
            'refunded' => [],
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
            'awaiting_confirmation' => 'Chờ xác nhận',
            'awaiting_driver' => 'Chờ tài xế',
            'in_transit' => 'Đang giao',
            'delivered' => 'Đã giao',
            'cancelled' => 'Đã hủy',
            'refunded' => 'Đã hoàn tiền',
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

    /**
     * Tính khoảng cách giữa 2 điểm lat/lng (Haversine formula, trả về km)
     */
    private function calculateDistance($lat1, $lng1, $lat2, $lng2)
    {
        $earthRadius = 6371; // km
        $latFrom = deg2rad($lat1);
        $latTo = deg2rad($lat2);
        $lonFrom = deg2rad($lng1);
        $lonTo = deg2rad($lng2);
        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;
        $a = sin($latDelta / 2) * sin($latDelta / 2) +
            cos($latFrom) * cos($latTo) *
            sin($lonDelta / 2) * sin($lonDelta / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return round($earthRadius * $c, 1);
    }

    /**
     * Xác nhận đơn hàng và dispatch event để tìm tài xế tự động
     */
    public function confirmOrder(Request $request, $id)
    {
        $branch = Auth::guard('manager')->user()->branch;
        $order = Order::where('branch_id', $branch->id)
            ->where('id', $id)
            ->firstOrFail();

        Log::info('Bắt đầu xác nhận đơn hàng', ['order_id' => $order->id]);

        if ($order->status !== 'awaiting_confirmation') {
            Log::warning('Trạng thái không hợp lệ khi xác nhận', ['order_id' => $order->id, 'status' => $order->status]);
            return response()->json([
                'success' => false,
                'message' => 'Chỉ có thể xác nhận đơn hàng đang chờ xác nhận',
                'current_status' => $order->status
            ], 400);
        }

        try {
            Log::info('Cập nhật trạng thái confirmed', ['order_id' => $order->id]);
            $order->update(['status' => 'confirmed']);
            $order->refresh();

            OrderStatusHistory::create([
                'order_id' => $order->id,
                'old_status' => 'awaiting_confirmation',
                'new_status' => 'confirmed',
                'changed_by' => Auth::guard('manager')->id(),
                'changed_by_role' => 'branch_manager',
                'note' => 'Xác nhận đơn hàng',
                'changed_at' => now()
            ]);

            // Broadcast event để cập nhật realtime
            event(new OrderStatusUpdated($order, 'awaiting_confirmation', 'confirmed'));

            // Dispatch event để tìm tài xế tự động
            event(new OrderConfirmed($order));

            Log::info('Đã xác nhận đơn hàng và dispatch event tìm tài xế', ['order_id' => $order->id]);

            return response()->json([
                'success' => true,
                'message' => 'Đã xác nhận đơn hàng, đang tìm tài xế...',
                'driver' => null,
                'new_status' => 'confirmed',
                'order_code' => $order->order_code ?? $order->id,
            ]);
        } catch (\Exception $e) {
            Log::error('Lỗi xác nhận đơn hàng', [
                'order_id' => $order->id,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage(),
                'current_status' => $order->status
            ], 500);
        }
    }



    /**
     * Trả về HTML partial card cho 1 order (dùng cho realtime)
     */
    public function card($id)
    {
        $branch = Auth::guard('manager')->user()->branch;
        $order = \App\Models\Order::with(['orderItems', 'payment', 'address'])
            ->where('branch_id', $branch->id)
            ->findOrFail($id);
        return view('branch.orders.partials.order_card', compact('order'));
    }
}
