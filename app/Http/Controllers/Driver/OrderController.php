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
        $order = Order::with(['customer', 'branch', 'orderItems.productVariant.product.primaryImage', 'address'])->findOrFail($orderId);

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
        $deliveryAddress = $order->address->address_line ?? $order->guest_address;
        $notes = $order->notes;

        $type = $request->query('type');

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

        // Lấy tọa độ của đơn hàng hiện tại
        $currentLat = $currentOrder->address->latitude ?? $currentOrder->guest_latitude ?? null;
        $currentLng = $currentOrder->address->longitude ?? $currentOrder->guest_longitude ?? null;

        // Tìm các đơn hàng có thể ghép
        $potentialOrders = Order::with([
            'customer', 
            'address', 
            'branch',
            'orderItems.productVariant.product',
            'orderItems.combo',
            'orderItems.toppings'
        ])
            ->where('driver_id', $driverId)
            ->where('id', '!=', $currentOrderId)
            ->whereIn('status', ['driver_confirmed', 'waiting_driver_pick_up', 'driver_picked_up'])
            ->whereNull('batch_id') // Chưa được ghép
            ->get();

        // Lọc các đơn hàng trong bán kính 7km
        $batchableOrders = collect();
        
        if ($currentLat && $currentLng) {
            foreach ($potentialOrders as $order) {
                $orderLat = $order->address->latitude ?? $order->guest_latitude ?? null;
                $orderLng = $order->address->longitude ?? $order->guest_longitude ?? null;
                
                if ($orderLat && $orderLng) {
                    $distance = $this->calculateDistance($currentLat, $currentLng, $orderLat, $orderLng);
                    
                    // Chỉ thêm vào danh sách nếu khoảng cách <= 7km
                    if ($distance <= 7) {
                        $order->distance = $distance; // Thêm thuộc tính distance để hiển thị
                        $batchableOrders->push($order);
                    }
                }
            }
        } else {
            // Nếu không có tọa độ của đơn hiện tại, không cho phép ghép đơn
            $batchableOrders = collect();
        }

        return view('driver.orders.batchable', compact('currentOrder', 'batchableOrders'));
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
        $currentLat = $currentOrder->address->latitude ?? $currentOrder->guest_latitude ?? null;
        $currentLng = $currentOrder->address->longitude ?? $currentOrder->guest_longitude ?? null;

        if (!$currentLat || !$currentLng) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể xác định vị trí của đơn hàng hiện tại.'
            ], 400);
        }

        // Kiểm tra khoảng cách của các đơn hàng được chọn
        $invalidOrders = [];
        foreach ($orders as $order) {
            if ($order->id == $currentOrderId) continue; // Bỏ qua đơn hiện tại
            
            $orderLat = $order->address->latitude ?? $order->guest_latitude ?? null;
            $orderLng = $order->address->longitude ?? $order->guest_longitude ?? null;
            
            if (!$orderLat || !$orderLng) {
                $invalidOrders[] = $order->order_code . ' (không có tọa độ)';
                continue;
            }
            
            $distance = $this->calculateDistance($currentLat, $currentLng, $orderLat, $orderLng);
            
            if ($distance > 7) {
                $invalidOrders[] = $order->order_code . ' (cách ' . number_format($distance, 1) . 'km)';
            }
        }

        if (!empty($invalidOrders)) {
            return response()->json([
                'success' => false,
                'message' => 'Các đơn hàng sau không thể ghép vì vượt quá 7km: ' . implode(', ', $invalidOrders)
            ], 400);
        }

        // Tạo batch ID mới
        $batchId = 'BATCH_' . $driverId . '_' . time();
        
        // Cập nhật tất cả đơn hàng với batch_id và batch_order
        foreach ($orders as $index => $order) {
            $order->batch_id = $batchId;
            $order->batch_order = $index + 1;
            $order->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Đã ghép ' . count($allOrderIds) . ' đơn hàng thành công!',
            'batch_id' => $batchId,
            'redirect_url' => route('driver.orders.batch.navigate', ['batchId' => $batchId])
        ]);
    }

    /**
     * Hiển thị trang điều hướng cho batch
     */
    public function navigateBatch($batchId)
    {
        $driverId = Auth::guard('driver')->id();
        
        $batchOrders = Order::with([
            'customer.driverRatings', 
            'address', 
            'branch',
            'orderItems.productVariant.product',
            'orderItems.combo',
            'orderItems.toppings'
        ])
            ->where('batch_id', $batchId)
            ->where('driver_id', $driverId)
            ->orderBy('batch_order')
            ->get();
            
        if ($batchOrders->isEmpty()) {
            abort(404, 'Không tìm thấy batch này.');
        }

        return view('driver.orders.batch-navigate', compact('batchOrders', 'batchId'));
    }

    /**
     * Cập nhật trạng thái đơn hàng trong batch
     */
    public function updateBatchOrderStatus(Request $request, $batchId, $orderId)
    {
        $request->validate([
            'status' => 'required|string|in:driver_confirmed,waiting_driver_pick_up,driver_picked_up,in_transit,delivered,delivery_failed,item_received'
        ]);

        $driverId = Auth::guard('driver')->id();
        
        // Các trạng thái cần thay đổi đồng bộ cho tất cả đơn trong batch
        $syncStatuses = ['driver_confirmed', 'waiting_driver_pick_up', 'driver_picked_up', 'in_transit'];
        
        if (in_array($request->status, $syncStatuses)) {
            // Cập nhật tất cả đơn hàng trong batch cùng lúc
            $batchOrders = Order::where('batch_id', $batchId)
                ->where('driver_id', $driverId)
                ->get();
                
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
            $order = Order::where('id', $orderId)
                ->where('batch_id', $batchId)
                ->where('driver_id', $driverId)
                ->firstOrFail();

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
    public function disbandBatch($batchId)
    {
        $driverId = Auth::guard('driver')->id();
        
        $batchOrders = Order::where('batch_id', $batchId)
            ->where('driver_id', $driverId)
            ->get();
            
        if ($batchOrders->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy batch này.'
            ], 404);
        }

        // Xóa batch_id và batch_order
        foreach ($batchOrders as $order) {
            $order->batch_id = null;
            $order->batch_order = null;
            $order->save();
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
        
        // Lấy tọa độ của đơn hàng hiện tại
        $currentLat = $currentOrder->address->latitude ?? $currentOrder->guest_latitude ?? null;
        $currentLng = $currentOrder->address->longitude ?? $currentOrder->guest_longitude ?? null;
        
        if (!$currentLat || !$currentLng) {
            return; // Không thể tự động ghép nếu không có tọa độ
        }
        
        // Tìm các đơn hàng khác của tài xế này có thể ghép
        $potentialOrders = Order::where('driver_id', $driverId)
            ->where('id', '!=', $currentOrder->id)
            ->whereIn('status', ['driver_confirmed', 'waiting_driver_pick_up', 'driver_picked_up'])
            ->whereNull('batch_id') // Chưa được ghép
            ->get();

        // Lọc các đơn hàng trong bán kính 7km
        $batchableOrders = collect();
        
        foreach ($potentialOrders as $order) {
            $orderLat = $order->address->latitude ?? $order->guest_latitude ?? null;
            $orderLng = $order->address->longitude ?? $order->guest_longitude ?? null;
            
            if ($orderLat && $orderLng) {
                $distance = $this->calculateDistance($currentLat, $currentLng, $orderLat, $orderLng);
                
                // Chỉ thêm vào danh sách nếu khoảng cách <= 7km
                if ($distance <= 7) {
                    $batchableOrders->push($order);
                }
            }
        }
        
        // Giới hạn tối đa 4 đơn khác (tổng cộng 5 đơn)
        $batchableOrders = $batchableOrders->take(4);

        // Nếu có ít nhất 1 đơn hàng khác, tạo batch
        if ($batchableOrders->count() > 0) {
            // Tạo batch ID mới
            $batchId = 'BATCH_' . $driverId . '_' . time();
            
            // Thêm đơn hàng hiện tại vào batch
            $currentOrder->batch_id = $batchId;
            $currentOrder->batch_order = 1;
            $currentOrder->save();
            
            // Thêm các đơn hàng khác vào batch
            foreach ($batchableOrders as $index => $order) {
                $order->batch_id = $batchId;
                $order->batch_order = $index + 2; // Bắt đầu từ 2 vì đơn hiện tại là 1
                $order->save();
            }
            
            Log::info("Auto-created batch {$batchId} with " . ($batchableOrders->count() + 1) . " orders for driver {$driverId} within 7km radius");
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
