<?php

namespace App\Http\Controllers\Driver;

use App\Events\Order\OrderStatusUpdated;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $driverId = Auth::guard('driver')->id();
        $search = $request->query('search');
        $currentTab = $request->query('tab', 'all'); // Mặc định là 'all'

        // Define all relevant statuses for the driver interface
        $statuses = [
            'all' => 'Tất cả',
            // 'awaiting_confirmation' => 'Chờ xác nhận', // New status
            'awaiting_driver' => 'Chờ tài xế', // New status
            // 'driver_assigned' => 'Đã giao tài xế', // New status
            // 'driver_confirmed' => 'Tài xế xác nhận', // New status
            // 'driver_picked_up' => 'Đã lấy hàng',
            'in_transit' => 'Đang giao',
            'delivered' => 'Đã giao',
            'item_received' => 'Khách đã nhận', // New status
            // 'cancelled' => 'Đã hủy',
            // 'refunded' => 'Đã hoàn tiền', // New status
            // 'payment_failed' => 'TT thất bại', // New status
            // 'payment_received' => 'TT đã nhận', // New status
            // 'order_failed' => 'Đơn hàng thất bại' // New status
        ];

        $tabConfig = [];
        $baseQuery = Order::query();

        // Eager load relationships for display
        $baseQuery->with([
            'customer',
            'orderItems.productVariant.product.category'
        ]);

        // Calculate count for 'all' tab
        $allOrdersCount = Order::query();
        if ($driverId) {
            // For 'all' tab, a driver should only see orders they are involved in (assigned, confirmed, picked up, in transit, delivered, received, cancelled by them or if they were assigned to it)
            $allOrdersCount->where('driver_id', $driverId)
                ->orWhereNull('driver_id') // Include orders awaiting driver if driver is online and looking for new orders
                ->whereIn('status', ['awaiting_driver', 'driver_assigned', 'driver_confirmed', 'driver_picked_up', 'in_transit', 'delivered', 'item_received', 'cancelled', 'order_failed']);
            // The above logic for 'all' might need fine-tuning based on exact business rules for what 'all' means for a driver.
            // For now, it includes orders they are assigned to or that are awaiting *any* driver.
        } else {
            // If no driverId, perhaps show only orders awaiting driver, or nothing. For now, assume a driver is always logged in.
            // This part might need adjustment if anonymous access is considered or 'all' is truly global.
        }

        if ($search) {
            $allOrdersCount->where(function ($query) use ($search) {
                $query->where('order_code', 'like', '%' . $search . '%')
                    ->orWhere('delivery_address', 'like', '%' . $search . '%')
                    ->orWhereHas('customer', function ($q) use ($search) {
                        $q->where('full_name', 'like', '%' . $search . '%')
                            ->orWhere('phone', 'like', '%' . $search . '%');
                    });
            });
        }
        $tabConfig['all']['count'] = $allOrdersCount->count();
        $tabConfig['all']['label'] = 'Tất cả';

        foreach ($statuses as $key => $label) {
            if ($key === 'all') {
                continue;
            }

            $query = Order::query();
            // Apply driver filter for specific status tabs.
            // Only orders that are 'awaiting_driver' are not yet assigned to a specific driver.
            if ($key !== 'awaiting_driver' && $driverId) {
                $query->where('driver_id', $driverId);
            }
            // For 'awaiting_driver' tab, we only show unassigned orders
            if ($key === 'awaiting_driver') {
                $query->where('driver_id', $driverId);
            }


            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('order_code', 'like', '%' . $search . '%')
                        ->orWhere('delivery_address', 'like', '%' . $search . '%')
                        ->orWhereHas('customer', function ($subQ) use ($search) {
                            $subQ->where('full_name', 'like', '%' . $search . '%')
                                ->orWhere('phone', 'like', '%' . $search . '%');
                        });
                });
            }
            $query->where('status', $key);
            $tabConfig[$key]['count'] = $query->count();
            $tabConfig[$key]['label'] = $label;
        }

        // Get orders for the current tab
        $ordersQuery = Order::query();
        $ordersQuery->with([
            'customer',
            'orderItems.productVariant.product.primaryImage', // Add primaryImage to load for product image
            'orderItems.productVariant.variantValues.attribute',
            'orderItems.toppings',
            'branch' // To show branch info if needed
        ]);

        if ($currentTab !== 'all') {
            $ordersQuery->where('status', $currentTab);
            // Apply driver_id filter for all tabs EXCEPT 'awaiting_driver'
            if ($currentTab !== 'awaiting_driver' && $driverId) {
                $ordersQuery->where('driver_id', $driverId);
            }
            // If the current tab is 'awaiting_driver', ensure driver_id is null
            if ($currentTab === 'awaiting_driver') {
                $ordersQuery->where('driver_id', $driverId);
            }
        } else {
            // Logic for 'all' tab
            $ordersQuery->where(function ($q) use ($driverId) {
                $q->where('driver_id', $driverId)
                    ->orWhere(function ($q2) {
                        $q2->whereNull('driver_id')
                            ->whereIn('status', [
                                'awaiting_driver',
                                'driver_assigned',
                                'driver_confirmed',
                                'driver_picked_up',
                                'in_transit',
                                'delivered',
                                'item_received',
                                'cancelled',
                                'order_failed'
                            ]);
                    });
            });
        }


        if ($search) {
            $ordersQuery->where(function ($query) use ($search) {
                $query->where('order_code', 'like', '%' . $search . '%')
                    ->orWhere('delivery_address', 'like', '%' . $search . '%')
                    ->orWhereHas('customer', function ($q) use ($search) {
                        $q->where('full_name', 'like', '%' . $search . '%')
                            ->orWhere('phone', 'like', '%' . $search . '%');
                    });
            });
        }

        $orders = $ordersQuery->orderBy('created_at', 'desc')->paginate(10);

        return view('driver.orders.index', compact('orders', 'tabConfig', 'currentTab', 'search'));
    }


    /**
     * Hiển thị chi tiết một đơn hàng.
     */
    public function show($orderId)
    {
        $driverId = Auth::guard('driver')->id();
        $order = Order::with(['customer', 'branch', 'orderItems.productVariant.product.primaryImage', 'address'])
            ->select([
                'orders.*',
                'delivery_address_line_snapshot',
                'delivery_ward_snapshot', 
                'delivery_district_snapshot',
                'delivery_province_snapshot',
                'delivery_phone_snapshot',
                'delivery_recipient_name_snapshot'
            ])
            ->findOrFail($orderId);

        // Logic mới:
        // 1. Nếu đơn hàng chưa có tài xế và đang chờ -> Cho phép xem để nhận đơn.
        // 2. Nếu đơn hàng đã có tài xế và là của mình -> Cho phép xem.
        // 3. Các trường hợp khác -> Không cho phép.
        if ($order->status !== 'awaiting_driver' && $order->driver_id != $driverId) {
            abort(403, 'Bạn không có quyền xem đơn hàng này.');
        }

        // Ưu tiên lấy vĩ độ/kinh độ từ address, nếu không có thì lấy từ guest_latitude/guest_longitude
        $latitude = $order->address && $order->address->latitude ? $order->address->latitude : $order->guest_latitude;
        $longitude = $order->address && $order->address->longitude ? $order->address->longitude : $order->guest_longitude;
        $branchLat = $order->branch->latitude ?? null;
        $branchLng = $order->branch->longitude ?? null;

        return view('driver.orders.show', compact('order', 'latitude', 'longitude', 'branchLat', 'branchLng'));
    }

    /**
     * Hiển thị trang điều hướng cho một đơn hàng.
     */
    public function navigate($orderId, Request $request)
    {
        $driverId = Auth::guard('driver')->id();
        $order = Order::with(['customer', 'address', 'branch'])
            ->where('id', $orderId)
            ->where('driver_id', $driverId)
            ->firstOrFail();

        $latitude = $order->address && $order->address->latitude ? $order->address->latitude : $order->guest_latitude;
        $longitude = $order->address && $order->address->longitude ? $order->address->longitude : $order->guest_longitude;
        $branchLat = $order->branch->latitude ?? null;
        $branchLng = $order->branch->longitude ?? null;

        $customerName = $order->customer->full_name ?? $order->guest_name;
        $customerPhone = $order->customer->phone ?? $order->guest_phone;
        $deliveryAddress = $order->delivery_address;
        $notes = $order->notes;

        $type = $request->query('type');
        
        // Tự động xác định type dựa trên trạng thái đơn hàng
        if (!$type) {
            // Nếu đơn hàng chưa được lấy (pickup), thì type = 'branch'
            // Nếu đơn hàng đã được lấy và đang giao, thì type = 'delivery'
            if (in_array($order->status, ['driver_confirmed', 'waiting_driver_pick_up'])) {
                $type = 'branch';
            } else {
                $type = 'delivery';
            }
        }

        $branchName = $order->branch->name ?? '';
        $branchPhone = $order->branch->phone ?? '';
        $branchAddress = $order->branch->address ?? '';

        return view('driver.orders.navigate', compact(
            'order', 'latitude', 'longitude', 'branchLat', 'branchLng', 'customerName', 'customerPhone', 'deliveryAddress', 'notes', 'type',
            'branchName', 'branchPhone', 'branchAddress'
        ));
    }

    // === CÁC HÀNH ĐỘNG CỦA TÀI XẾ - ĐƯỢC SẮP XẾP LẠI LOGIC ===

    /**
     * HÀM HELPER: Xử lý các tác vụ chung.
     */
    private function processUpdate(Order $order, string $successMessage, string $oldStatus = null): JsonResponse
    {
        if ($oldStatus === null) {
            $oldStatus = $order->getOriginal('status');
        }
        $order->save();
        $freshOrder = $order->fresh();
        broadcast(new OrderStatusUpdated($freshOrder, false, $oldStatus, $freshOrder->status));

        return response()->json([
            'success' => true,
            'message' => $successMessage,
            'order'   => $freshOrder
        ]);
    }

    /**
     * Tài xế xác nhận nhận đơn (từ trạng thái awaiting_driver -> driver_confirmed)
     * Tự động ghép đơn nếu có đơn hàng khác phù hợp
     */
    public function confirm(Order $order): JsonResponse
    {
        if ($order->driver_id !== Auth::guard('driver')->id() || $order->status !== 'awaiting_driver') {
            return response()->json(['success' => false, 'message' => 'Đơn hàng không khả dụng để xác nhận.'], 400);
        }

        $oldStatus = $order->status;
        $order->status = 'driver_confirmed';
        
        // Tự động tìm và ghép đơn hàng
        $this->autoCreateBatch($order);
        
        return $this->processUpdate($order, 'Bạn đã xác nhận nhận đơn. Hãy bắt đầu đến điểm lấy hàng!', $oldStatus);
    }

    /**
     * Tài xế bắt đầu di chuyển đến điểm lấy hàng (driver_confirmed -> waiting_driver_pick_up)
     */
    public function startPickup(Order $order): JsonResponse
    {
        if ($order->driver_id !== Auth::guard('driver')->id() || $order->status !== 'driver_confirmed') {
            return response()->json(['success' => false, 'message' => 'Bạn không thể thực hiện hành động này.'], 400);
        }

        $oldStatus = $order->status;
        $order->status = 'waiting_driver_pick_up';
        return $this->processUpdate($order, 'Bạn đang trên đường đến điểm lấy hàng!', $oldStatus);
    }

    /**
     * Tài xế xác nhận đã lấy hàng (waiting_driver_pick_up -> driver_picked_up)
     */
    public function confirmPickup(Order $order, Request $request)
    {
        if ($order->driver_id !== Auth::guard('driver')->id() || $order->status !== 'waiting_driver_pick_up') {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Bạn không thể thực hiện hành động này.'], 400);
            }
            return redirect()->back()->with('error', 'Bạn không thể thực hiện hành động này.');
        }

        $oldStatus = $order->status;
        $order->status = 'driver_picked_up';

        if ($request->expectsJson() || $request->ajax()) {
            return $this->processUpdate($order, 'Bạn đã lấy hàng thành công! Đang giao hàng.', $oldStatus);
        }

        // For non-AJAX requests, save and broadcast manually
        $order->save();
        $freshOrder = $order->fresh();
        broadcast(new OrderStatusUpdated($freshOrder, false, $oldStatus, $freshOrder->status));

        return redirect()->route('driver.orders.show', $order->id)
            ->with('success', 'Bạn đã lấy hàng thành công!');
    }

    /**
     * Tài xế bắt đầu giao hàng (driver_picked_up -> in_transit)
     */
    public function startDelivery(Order $order): JsonResponse
    {
        if ($order->driver_id !== Auth::guard('driver')->id() || $order->status !== 'driver_picked_up') {
            return response()->json(['success' => false, 'message' => 'Bạn không thể thực hiện hành động này.'], 400);
        }

        $oldStatus = $order->status;
        $order->status = 'in_transit';
        return $this->processUpdate($order, 'Bạn đang giao hàng!', $oldStatus);
    }

    /**
     * Tài xế xác nhận giao thành công (in_transit -> delivered)
     */
    public function confirmDelivery(Order $order, Request $request)
    {
        if ($order->driver_id !== Auth::guard('driver')->id() || $order->status !== 'in_transit') {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Bạn không thể thực hiện hành động này.'], 400);
            }
            return redirect()->back()->with('error', 'Bạn không thể thực hiện hành động này.');
        }

        $oldStatus = $order->status;
        $order->status = 'delivered';
        $order->actual_delivery_time = Carbon::now();
        
        // Tính toán thu nhập cho tài xế 
        if (is_null($order->driver_earning) && $order->delivery_fee > 0) {
            $commissionRate = config('shipping.driver_commission_rate', 0.6);
            $order->driver_earning = $order->delivery_fee * $commissionRate;
        }

        if ($request->expectsJson() || $request->ajax()) {
            return $this->processUpdate($order, 'Đã giao hàng thành công!', $oldStatus);
        }

        // For non-AJAX requests, save and broadcast manually
        $order->save();
        $freshOrder = $order->fresh();
        broadcast(new OrderStatusUpdated($freshOrder, false, $oldStatus, $freshOrder->status));

        return redirect()->route('driver.orders.show', $order->id)
            ->with('success', 'Đã giao hàng thành công!');
    }

    /**
     * Tài xế từ chối đơn (awaiting_driver)
     */
    public function reject(Order $order): JsonResponse
    {
        if ($order->driver_id !== Auth::guard('driver')->id() || $order->status !== 'awaiting_driver') {
            return response()->json(['success' => false, 'message' => 'Bạn không thể từ chối đơn này.'], 400);
        }

        $order->status = 'cancelled';
        $order->driver_id = null;

        $order->save();

        return response()->json([
            'success' => true,
            'message' => 'Bạn đã từ chối đơn hàng.',
            'redirect_url' => route('driver.dashboard') // ← Đây là đường dẫn về trang tài xế
        ]);
    }

    /**
     * Hiển thị danh sách đơn hàng có thể ghép
     */
    public function showBatchableOrders(Request $request)
    {
        $driverId = Auth::guard('driver')->id();
        $currentOrderId = $request->query('current_order_id');
        
        $currentOrder = Order::with([
            'customer', 
            'address', 
            'branch',
            'orderItems.productVariant.product',
            'orderItems.combo',
            'orderItems.toppings'
        ])->findOrFail($currentOrderId);
        
        // Kiểm tra quyền
        if ($currentOrder->driver_id !== $driverId) {
            abort(403, 'Bạn không có quyền thực hiện hành động này.');
        }

        // Sử dụng phương thức mới từ Model để lấy các đơn có thể ghép
        $batchableOrders = $currentOrder->getBatchableOrders();
        
        // Nếu có đơn có thể ghép, tự động tạo batch và chuyển đến trang batch-navigate
        if ($batchableOrders->isNotEmpty()) {
            // Tạo batch group ID
            $batchGroupId = $currentOrder->getBatchGroupId();
            
            // Tự động ghép tất cả đơn có thể ghép
            $batchTime = now();
            $currentOrder->updated_at = $batchTime;
            $currentOrder->save();
            
            foreach ($batchableOrders as $order) {
                $order->updated_at = $batchTime;
                $order->save();
            }
            
            // Chuyển hướng đến trang batch-navigate
            return redirect()->route('driver.orders.batch.navigate', ['batchGroupId' => $batchGroupId]);
        }
        
        // Nếu không có đơn nào có thể ghép, chuyển về trang chi tiết đơn hàng
        return redirect()->route('driver.orders.show', $currentOrder->id)
            ->with('info', 'Hiện tại không có đơn hàng nào có thể ghép với đơn này.');
    }

    /**
     * Tạo batch mới hoặc thêm vào batch hiện có
     */
    public function createBatch(Request $request)
    {
        $request->validate([
            'current_order_id' => 'required|exists:orders,id',
            'selected_orders' => 'required|array|min:1',
            'selected_orders.*' => 'exists:orders,id'
        ]);

        $driverId = Auth::guard('driver')->id();
        $currentOrderId = $request->current_order_id;
        $selectedOrderIds = $request->selected_orders;
        
        // Thêm đơn hàng hiện tại vào danh sách
        $allOrderIds = array_merge([$currentOrderId], $selectedOrderIds);
        
        // Kiểm tra tất cả đơn hàng thuộc về tài xế này
        $orders = Order::with(['address'])
            ->whereIn('id', $allOrderIds)
            ->where('driver_id', $driverId)
            ->whereIn('status', ['driver_confirmed', 'waiting_driver_pick_up', 'driver_picked_up'])
            ->get();
            
        if ($orders->count() !== count($allOrderIds)) {
            return response()->json([
                'success' => false,
                'message' => 'Một số đơn hàng không hợp lệ để ghép.'
            ], 400);
        }

        // Lấy đơn hàng hiện tại để kiểm tra khoảng cách
        $currentOrder = $orders->firstWhere('id', $currentOrderId);
        $batchableOrders = $currentOrder->getBatchableOrders();
        
        // Kiểm tra các đơn hàng được chọn có trong danh sách có thể ghép không
        $validSelectedIds = $batchableOrders->pluck('id')->toArray();
        $invalidSelectedIds = array_diff($selectedOrderIds, $validSelectedIds);
        
        if (!empty($invalidSelectedIds)) {
            $invalidOrders = Order::whereIn('id', $invalidSelectedIds)->pluck('order_code')->toArray();
            return response()->json([
                'success' => false,
                'message' => 'Các đơn hàng sau không thể ghép: ' . implode(', ', $invalidOrders)
            ], 400);
        }

        // Tạo batch group ID
        $batchGroupId = $currentOrder->getBatchGroupId();
        
        // Đánh dấu các đơn hàng đã được ghép bằng cách cập nhật updated_at cùng thời điểm
        $batchTime = now();
        foreach ($orders as $order) {
            $order->updated_at = $batchTime;
            $order->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Đã ghép ' . count($allOrderIds) . ' đơn hàng thành công!',
            'batch_group_id' => $batchGroupId,
            'redirect_url' => route('driver.orders.batch.navigate', ['batchGroupId' => $batchGroupId])
        ]);
    }

    /**
     * Hiển thị trang điều hướng cho batch
     */
    public function navigateBatch($batchGroupId)
    {
        $driverId = Auth::guard('driver')->id();
        
        // Tìm đơn hàng đầu tiên trong batch để lấy thông tin
        $currentOrder = Order::with([
            'customer.driverRatings', 
            'address', 
            'branch',
            'orderItems.productVariant.product',
            'orderItems.combo',
            'orderItems.toppings'
        ])
            ->where('driver_id', $driverId)
            ->whereRaw("CONCAT('BATCH_', driver_id, '_', DATE_FORMAT(updated_at, '%Y%m%d%H')) = ?", [$batchGroupId])
            ->firstOrFail();
            
        // Lấy tất cả đơn hàng trong batch
        $batchOrders = $currentOrder->getBatchOrders();

        return view('driver.orders.batch-navigate', compact('batchOrders', 'batchGroupId', 'currentOrder'));
    }

    /**
     * Cập nhật trạng thái đơn hàng trong batch
     */
    public function updateBatchOrderStatus(Request $request, $batchGroupId, $orderId)
    {
        $request->validate([
            'status' => 'required|string|in:driver_confirmed,waiting_driver_pick_up,driver_picked_up,in_transit,delivered,delivery_failed,item_received'
        ]);

        $driverId = Auth::guard('driver')->id();
        
        $order = Order::where('id', $orderId)
            ->where('batch_id', $batchId)
            ->where('driver_id', $driverId)
            ->firstOrFail();
        // Các trạng thái cần thay đổi đồng bộ cho tất cả đơn trong batch
        $syncStatuses = ['driver_confirmed', 'waiting_driver_pick_up', 'driver_picked_up', 'in_transit'];
        
        if (in_array($request->status, $syncStatuses)) {
            // Cập nhật tất cả đơn hàng trong batch cùng lúc
            $batchOrders = $order->getBatchOrders();
                
            if ($batchOrders->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy batch này.'
                ], 404);
            }
            
            $oldStatuses = [];
            foreach ($batchOrders as $batchOrder) {
                $oldStatuses[$batchOrder->id] = $batchOrder->status;
                $batchOrder->status = $request->status;
                $batchOrder->save();
                
                // Broadcast event cho từng đơn
                $freshOrder = $batchOrder->fresh();
                broadcast(new \App\Events\Order\OrderStatusUpdated($freshOrder, false, $oldStatuses[$batchOrder->id], $freshOrder->status));
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Đã cập nhật trạng thái cho tất cả ' . count($batchOrders) . ' đơn hàng trong batch!',
                'orders_updated' => count($batchOrders)
            ]);
            
        } else {
            // Chỉ cập nhật đơn hàng cụ thể (cho delivered, delivery_failed, item_received)
            $oldStatus = $order->status;
            $order->status = $request->status;
            
            if ($request->status === 'delivered') {
                $order->actual_delivery_time = Carbon::now();
                
                // Tính toán thu nhập cho tài xế
                if (is_null($order->driver_earning) && $order->delivery_fee > 0) {
                    $commissionRate = config('shipping.driver_commission_rate', 0.6);
                    $order->driver_earning = $order->delivery_fee * $commissionRate;
                }
            }

            return $this->processUpdate($order, 'Đã cập nhật trạng thái đơn hàng!', $oldStatus);
        }
    }

    /**
     * Hủy ghép đơn
     */
    public function disbandBatch($batchGroupId)
    {
        $driverId = Auth::guard('driver')->id();
        
        // Tìm đơn hàng đầu tiên trong batch
        $order = Order::where('driver_id', $driverId)
            ->whereRaw("CONCAT('BATCH_', driver_id, '_', DATE_FORMAT(updated_at, '%Y%m%d%H')) = ?", [$batchGroupId])
            ->firstOrFail();
            
        $batchOrders = $order->getBatchOrders();
            
        if ($batchOrders->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy batch này.'
            ], 404);
        }

        // Tách các đơn hàng bằng cách cập nhật updated_at khác nhau
        foreach ($batchOrders as $index => $batchOrder) {
            $batchOrder->updated_at = now()->subMinutes($index * 10); // Tách thời gian 10 phút
            $batchOrder->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Đã hủy ghép đơn thành công!',
            'redirect_url' => route('driver.orders.index')
        ]);
    }

    /**
     * Tự động tìm và ghép đơn hàng khi tài xế nhận đơn
     */
    private function autoCreateBatch(Order $currentOrder)
    {
        $driverId = $currentOrder->driver_id;
        
        // Sử dụng phương thức mới từ Model
        $batchableOrders = $currentOrder->getBatchableOrders();
        
        // Giới hạn tối đa 4 đơn khác (tổng cộng 5 đơn)
        $batchableOrders = $batchableOrders->take(4);

        // Nếu có ít nhất 1 đơn hàng khác, tạo batch
        if ($batchableOrders->count() > 0) {
            // Đánh dấu các đơn hàng đã được ghép bằng cách cập nhật updated_at cùng thời điểm
            $batchTime = now();
            $currentOrder->updated_at = $batchTime;
            $currentOrder->save();
            
            // Cập nhật thời gian cho các đơn hàng khác
            foreach ($batchableOrders as $order) {
                $order->updated_at = $batchTime;
                $order->save();
            }
            
            $batchGroupId = $currentOrder->getBatchGroupId();
            Log::info("Auto-created batch {$batchGroupId} with " . ($batchableOrders->count() + 1) . " orders for driver {$driverId} within 7km radius");
        }
    }

    /**
     * Tính khoảng cách giữa hai điểm sử dụng công thức Haversine
     * 
     * @param float $lat1 Vĩ độ điểm 1
     * @param float $lng1 Kinh độ điểm 1
     * @param float $lat2 Vĩ độ điểm 2
     * @param float $lng2 Kinh độ điểm 2
     * @return float Khoảng cách tính bằng km
     */
    private function calculateDistance($lat1, $lng1, $lat2, $lng2)
    {
        $earthRadius = 6371; // Bán kính Trái Đất tính bằng km
        
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        
        $a = sin($dLat/2) * sin($dLat/2) + 
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * 
             sin($dLng/2) * sin($dLng/2);
             
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        
        return $earthRadius * $c;
    }
}
