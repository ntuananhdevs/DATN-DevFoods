<?php

namespace App\Http\Controllers\Customer;

use App\Events\Order\NewOrderReceived;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderAddress;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Cart;
use App\Models\Branch;
use App\Models\Address;
use App\Services\BranchService;
use App\Services\ShippingService;
use Illuminate\Support\Facades\Log;
use App\Mail\EmailFactory;
use App\Services\OrderSnapshotService;
use App\Models\GeneralSetting;
use Carbon\Carbon;

class CheckoutController extends Controller
{
    protected $branchService;

    public function __construct(BranchService $branchService)
    {
        $this->branchService = $branchService;
    }
    /**
     * Display the checkout page.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Nếu không có from_buy_now=1 thì xóa session buy_now_checkout
        if (!$request->has('from_buy_now') || $request->input('from_buy_now') != 1) {
            session()->forget('buy_now_checkout');
        }
        $buyNow = session('buy_now_checkout');
        if ($buyNow) {
            $cartItems = collect();
            if ($buyNow['type'] === 'product') {
                $variant = null;
                if (!empty($buyNow['variant_id'])) {
                    $variant = \App\Models\ProductVariant::with([
                        'product',
                        'variantValues.attribute'
                    ])->find($buyNow['variant_id']);
                }
                if ($variant) {
                    // Debug log for Buy Now variant
                    \Log::debug('Buy Now variant loaded:', [
                        'variant_id' => $variant->id,
                        'product_name' => $variant->product->name ?? 'unknown',
                        'variant_values_count' => $variant->variantValues ? $variant->variantValues->count() : 0,
                        'variant_values' => $variant->variantValues ? $variant->variantValues->map(function($vv) {
                            return [
                                'id' => $vv->id,
                                'value' => $vv->value,
                                'attribute_name' => $vv->attribute->name ?? 'unknown'
                            ];
                        })->toArray() : []
                    ]);
                    
                    $item = new \stdClass();
                    $item->variant = $variant;
                    $item->quantity = $buyNow['quantity'];
                    $item->toppings = collect();
                    if (!empty($buyNow['toppings'])) {
                        $item->toppings = \App\Models\Topping::whereIn('id', $buyNow['toppings'])->get();
                    }
                    $item->combo = null;
                    $cartItems->push($item);
                }
            } elseif ($buyNow['type'] === 'combo') {
                $combo = \App\Models\Combo::find($buyNow['combo_id']);
                if ($combo) {
                    $item = new \stdClass();
                    $item->variant = null;
                    $item->combo = $combo;
                    $item->quantity = $buyNow['quantity'];
                    $item->toppings = collect();
                    $cartItems->push($item);
                }
            }
            
            // Calculate subtotal for Buy Now items
            $subtotal = $this->calculateBuyNowSubtotal($cartItems);
            // Nếu không có sản phẩm hợp lệ trong buy now
            if ($cartItems->isEmpty()) {
                return redirect()->route('products.index')->with('error', 'Sản phẩm bạn chọn không hợp lệ hoặc đã hết hàng.');
            }
            $cart = null;
            $userAddresses = null;
            $userId = \Auth::id();
            if ($userId) {
                $userAddresses = \App\Models\Address::where('user_id', $userId)
                    ->orderBy('is_default', 'desc')
                    ->orderBy('created_at', 'desc')
                    ->get();
            }
            $currentBranch = $this->branchService->getCurrentBranch();
            
            // Get delivery time configuration
            $deliveryConfig = [
                'defaultPreparationTime' => GeneralSetting::getDefaultPreparationTime(),
                'averageSpeedKmh' => GeneralSetting::getAverageSpeedKmh(),
                'bufferTime' => GeneralSetting::getBufferTimeMinutes()
            ];
            
            return view('customer.checkout.index', compact('cartItems', 'subtotal', 'cart', 'userAddresses', 'currentBranch', 'deliveryConfig'));
        }

        $cartItems = [];
        $subtotal = 0;

        $userId = Auth::id();
        $sessionId = session()->getId();
        
        // Validate user exists if authenticated
        if ($userId) {
            $userExists = \App\Models\User::where('id', $userId)->exists();
            if (!$userExists) {
                // User doesn't exist, clear authentication and use session-based cart
                Auth::logout();
                $userId = null;
                Log::warning('User ID ' . $userId . ' does not exist in CheckoutController, falling back to session-based cart');
            }
        }

        $cartQuery = Cart::query()->where('status', 'active');
        if ($userId) {
            $cartQuery->where('user_id', $userId);
        } else {
            $cartQuery->where('session_id', $sessionId);
        }
        $cart = $cartQuery->first();

        // Lấy danh sách id sản phẩm được chọn
        $selectedIds = $request->cart_item_ids;

        if ($cart) {
            $query = CartItem::with([
                'variant.product.images',
                'variant.variantValues.attribute',
                'combo', // Thêm eager load cho combo
                'toppings'
            ])->where('cart_id', $cart->id);

            if ($selectedIds && is_array($selectedIds)) {
                $query->whereIn('id', $selectedIds);
            }

            $cartItems = $query->get();

            // === NEW: tính subtotal bằng hàm applyDiscountsToCartItems ===
            $subtotal = $this->applyDiscountsToCartItems($cartItems);

            foreach ($cartItems as $item) {
                // Xử lý cho sản phẩm lẻ
                if ($item->variant && $item->variant->product) {
                    // Đặt primary image
                    $item->variant->product->primary_image = $item->variant->product->images
                        ->where('is_primary', true)
                        ->first() ?? $item->variant->product->images->first();
                }
                // Xử lý cho combo (nếu cần)
                elseif ($item->combo) {
                    // Combo không cần xử lý primary image ở đây
                }
            }
        } else {
            $cartItems = collect([]);
        }

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng của bạn đang trống. Vui lòng thêm sản phẩm trước khi thanh toán.');
        }

        // Load user addresses if authenticated
        $userAddresses = null;
        if ($userId) {
            $userAddresses = Address::where('user_id', $userId)
                ->orderBy('is_default', 'desc')
                ->orderBy('created_at', 'desc')
                ->get();
        }

        // Get current selected branch for distance calculation
        $currentBranch = $this->branchService->getCurrentBranch();
        
        // Get delivery time configuration
        $deliveryConfig = [
            'defaultPreparationTime' => GeneralSetting::getDefaultPreparationTime(),
            'averageSpeedKmh' => GeneralSetting::getAverageSpeedKmh(),
            'bufferTime' => GeneralSetting::getBufferTimeMinutes()
        ];

        return view('customer.checkout.index', compact('cartItems', 'subtotal', 'cart', 'userAddresses', 'currentBranch', 'deliveryConfig'));
    }
    
    /**
     * Process the checkout.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function process(Request $request)
    {
        // Get user ID to determine validation rules
        $userId = Auth::id();
        
        // Validate user exists if authenticated
        if ($userId) {
            $userExists = \App\Models\User::where('id', $userId)->exists();
            if (!$userExists) {
                // User doesn't exist, clear authentication and use session-based cart
                Auth::logout();
                $userId = null;
                Log::warning('User ID ' . $userId . ' does not exist in CheckoutController process, falling back to session-based cart');
            }
        }
        
        // Validate checkout data with different rules for authenticated vs guest users
        if ($userId) {
            // For authenticated users, require address_id selection
            $userAddresses = \App\Models\Address::where('user_id', $userId)->count();
            if ($userAddresses > 0) {
                // User đã có địa chỉ, xử lý như cũ
                $validated = $request->validate([
                    'address_id' => 'required|exists:addresses,id',
                    'payment_method' => 'required|string|in:cod,vnpay,balance',
                    'notes' => 'nullable|string',
                    'terms' => 'required',
                    // Hidden fields that get populated by JavaScript
                    'full_name' => 'required|string|max:255',
                    'phone' => 'required|string|max:20',
                    'email' => 'required|email|max:255',
                    'address' => 'required|string|max:255',
                    'city' => 'required|string|max:100',
                    'district' => 'required|string|max:100',
                    'ward' => 'required|string|max:100',
                ]);
                // Verify the address belongs to the user
                $address = Address::where('id', $request->address_id)
                    ->where('user_id', $userId)
                    ->first();
                if (!$address) {
                    throw new \Exception('Địa chỉ được chọn không hợp lệ.');
                }
            } else {
                // User chưa có địa chỉ, validate các trường thông tin giao hàng và tạo mới địa chỉ
                $validated = $request->validate([
                    'full_name' => 'required|string|max:255',
                    'phone' => 'required|string|max:20',
                    'email' => 'required|email|max:255',
                    'address' => 'required|string|max:255',
                    'city' => 'required|string|max:100',
                    'district' => 'required|string|max:100',
                    'ward' => 'required|string|max:100',
                    'latitude' => 'required|numeric',
                    'longitude' => 'required|numeric',
                    'payment_method' => 'required|string|in:cod,vnpay,balance',
                    'notes' => 'nullable|string',
                    'terms' => 'required',
                ]);
                // Tạo địa chỉ mới cho user
                $address = Address::create([
                    'user_id' => $userId,
                    'recipient_name' => $validated['full_name'],
                    'phone_number' => $validated['phone'],
                    'address_line' => $validated['address'],
                    'city' => $validated['city'],
                    'district' => $validated['district'],
                    'ward' => $validated['ward'],
                    'latitude' => $validated['latitude'],
                    'longitude' => $validated['longitude'],
                    'is_default' => true,
                ]);
                
                // Lưu address_id vào request để sử dụng khi tạo đơn hàng
                $request->merge(['address_id' => $address->id]);
            }
        } else {
            // For guest users, require manual input including coordinates
            $validated = $request->validate([
                'full_name' => 'required|string|max:255',
                'phone' => 'required|string|max:20',
                'email' => 'required|email|max:255',
                'address' => 'required|string|max:255',
                'city' => 'required|string|max:100',
                'district' => 'required|string|max:100',
                'ward' => 'required|string|max:100',
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric',
                'payment_method' => 'required|string|in:cod,vnpay,balance',
                'notes' => 'nullable|string',
                'terms' => 'required',
            ]);
        }
        
        try {
            DB::beginTransaction();
            
            // Kiểm tra xem có phải là "buy now" không
            $buyNow = session('buy_now_checkout');
            $cartItems = collect();
            $cart = null;
            $selectedIds = null; // Khởi tạo biến selectedIds
            
            if ($buyNow) {
                // Xử lý "buy now" - tạo cart items từ session
                if ($buyNow['type'] === 'product') {
                    $variant = null;
                    if (!empty($buyNow['variant_id'])) {
                        $variant = \App\Models\ProductVariant::with([
                            'product',
                            'variantValues.attribute'
                        ])->find($buyNow['variant_id']);
                    }
                    if ($variant) {
                        $item = new \stdClass();
                        $item->variant = $variant;
                        $item->quantity = $buyNow['quantity'];
                        $item->toppings = collect();
                        if (!empty($buyNow['toppings'])) {
                            $item->toppings = \App\Models\Topping::whereIn('id', $buyNow['toppings'])->get();
                        }
                        $item->combo = null;
                        $cartItems->push($item);
                    }
                } elseif ($buyNow['type'] === 'combo') {
                    $combo = \App\Models\Combo::find($buyNow['combo_id']);
                    if ($combo) {
                        $item = new \stdClass();
                        $item->variant = null;
                        $item->combo = $combo;
                        $item->quantity = $buyNow['quantity'];
                        $item->toppings = collect();
                        $cartItems->push($item);
                    }
                }
                
                if ($cartItems->isEmpty()) {
                    throw new \Exception('Sản phẩm bạn chọn không hợp lệ hoặc đã hết hàng.');
                }
            } else {
                // Xử lý giỏ hàng thông thường
                $sessionId = session()->getId();
                
                $cartQuery = Cart::query()->where('status', 'active');
                
                if ($userId) {
                    $cartQuery->where('user_id', $userId);
                } else {
                    $cartQuery->where('session_id', $sessionId);
                }
                
                $cart = $cartQuery->first();
                
                if (!$cart) {
                    throw new \Exception('Không tìm thấy giỏ hàng.');
                }
                
                // Lấy danh sách id sản phẩm được chọn từ request
                $selectedIds = $request->cart_item_ids;
                $cartItemsQuery = CartItem::with(['variant', 'combo', 'toppings.topping'])
                    ->where('cart_id', $cart->id);
                if ($selectedIds && is_array($selectedIds)) {
                    $cartItemsQuery->whereIn('id', $selectedIds);
                }
                $cartItems = $cartItemsQuery->get();
                
                if ($cartItems->isEmpty()) {
                    throw new \Exception('Giỏ hàng của bạn đang trống.');
                }
            }
            
            // Calculate totals - Different logic for Buy Now vs regular cart
            if ($buyNow) {
                // Buy Now: Apply discount only to products, not combos
                $subtotal = $this->calculateBuyNowSubtotal($cartItems);
            } else {
                // Regular cart: Apply discounts to all items
                $currentBranch = $this->branchService->getCurrentBranch();
                $branchId = $currentBranch ? $currentBranch->id : null;
                $subtotal = $this->applyDiscountsToCartItems($cartItems, $branchId);
            }
            
            // Calculate shipping using ShippingService - FIX: Use consistent service-based calculation
            $currentBranch = $this->branchService->getCurrentBranch();
            if (!$currentBranch || !$currentBranch->latitude || !$currentBranch->longitude) {
                throw new \Exception('Không thể xác định vị trí chi nhánh để tính phí vận chuyển.');
            }

            // Lấy tọa độ của địa chỉ giao hàng
            $deliveryLat = null;
            $deliveryLon = null;
            if ($userId) {
                if (empty($address)) { // $address được lấy từ đầu hàm
                    throw new \Exception('Địa chỉ giao hàng không hợp lệ.');
                }
                $deliveryLat = $address->latitude;
                $deliveryLon = $address->longitude;
            } else {
                // Đối với guest, lấy tọa độ từ form input
                $deliveryLat = $validated['latitude'];
                $deliveryLon = $validated['longitude'];
            }

            if(is_null($deliveryLat) || is_null($deliveryLon)) {
                 throw new \Exception('Không có đủ thông tin tọa độ để tính phí vận chuyển.');
            }

            // Tính khoảng cách
            $distance = ShippingService::getDistance(
                $currentBranch->latitude,
                $currentBranch->longitude,
                $deliveryLat,
                $deliveryLon
            );

            // Kiểm tra giới hạn khoảng cách giao hàng
            $maxDeliveryDistance = GeneralSetting::getMaxDeliveryDistance();
            if ($distance > $maxDeliveryDistance) {
                throw new \Exception("Địa chỉ giao hàng nằm ngoài vùng phục vụ. Khoảng cách tối đa: {$maxDeliveryDistance}km, khoảng cách thực tế: " . round($distance, 1) . "km");
            }

            // Tính phí vận chuyển
            $shipping = ShippingService::calculateFee($subtotal, $distance);

            // Tính thời gian giao hàng dự kiến
            $estimatedMinutes = ShippingService::calculateEstimatedDeliveryTime($cartItems, $distance);
            $estimatedDeliveryTime = now()->addMinutes($estimatedMinutes);
            
            // Apply discount if available
            $discount = session('coupon_discount_amount', 0);
            
            // Calculate total
            $total = $subtotal + $shipping - $discount;
            
            // Validate balance payment method
            if ($request->payment_method === 'balance') {
                if (!$userId) {
                    throw new \Exception('Bạn cần đăng nhập để sử dụng số dư tài khoản.');
                }
                
                $user = Auth::user();
                if ($user->balance < $total) {
                    throw new \Exception('Số dư tài khoản không đủ. Số dư hiện tại: ' . number_format($user->balance) . 'đ, cần: ' . number_format($total) . 'đ');
                }
            }
            
            // Lấy branch hiện tại từ session
            $currentBranch = $this->branchService->getCurrentBranch();
            if (!$currentBranch) {
                throw new \Exception('Vui lòng chọn chi nhánh trước khi thanh toán.');
            }
            $branchId = $currentBranch->id;

            // BƯỚC 1: Tạo bản ghi thanh toán (Payment) trước
            $payment = new \App\Models\Payment([
                'payment_method' => $request->payment_method,
                'payer_name' => $userId ? Auth::user()->name : $request->full_name,
                'payer_email' => $userId ? Auth::user()->email : $request->email,
                'payer_phone' => $userId ? Auth::user()->phone : $request->phone,
                'payment_amount' => $total,
                'txn_ref' => 'PAY-' . strtoupper(uniqid()), // Mã giao dịch tạm thời
                'payment_status' => ($request->payment_method === 'balance') ? 'completed' : 'pending',
                'ip_address' => $request->ip(),
            ]);
            $payment->save();

            // BƯỚC 2: Tạo đơn hàng (Order) và liên kết với Payment
            $order = new Order();
            
            // Thiết lập thông tin người dùng - khác nhau giữa user và guest
            if ($userId) {
                $order->customer_id = $userId;
                
                // For authenticated users, use the selected address
                $selectedAddress = Address::where('id', $request->address_id)
                    ->where('user_id', $userId)
                    ->first();
                
                if ($selectedAddress) {
                    $order->address_id = $selectedAddress->id;
                    
                    // Log để debug
                    \Illuminate\Support\Facades\Log::info('Setting address_id for order', [
                        'order_code' => $order->order_code,
                        'address_id' => $selectedAddress->id,
                        'address_data' => $selectedAddress->toArray()
                    ]);
                } else {
                    // Log lỗi nếu không tìm thấy địa chỉ
                    \Illuminate\Support\Facades\Log::warning('Address not found for order', [
                        'order_code' => $order->order_code,
                        'requested_address_id' => $request->address_id,
                        'user_id' => $userId
                    ]);
                }
            } else {
                // Thông tin guest
                $order->guest_name = $request->full_name;
                $order->guest_phone = $request->phone;
                $order->guest_email = $request->email;
                $order->guest_address = $request->address;
                $order->guest_district = $request->district;
                $order->guest_ward = $request->ward;
                $order->guest_city = $request->city;
            }
            
            $order->branch_id = $branchId;

            // Tạo mã đơn hàng: 8 ký tự ngẫu nhiên
            $order->order_code = strtoupper(\Illuminate\Support\Str::random(8));

            $order->payment_id = $payment->id; // << QUAN TRỌNG: Gán payment_id
            $order->status = 'awaiting_confirmation';
            $order->estimated_delivery_time = $estimatedDeliveryTime;
            $order->delivery_fee = $shipping;
            $order->discount_amount = $discount;
            $order->subtotal = $subtotal;
            $order->total_amount = $total;
            $order->notes = $request->notes;
            $order->delivery_address = $request->address . ', ' . $request->ward . ', ' . $request->district . ', ' . $request->city;

            // Cập nhật lại txn_ref của payment để dùng chung order_code cho dễ tra cứu
            $payment->txn_ref = $order->order_code;
            
            // Lưu order trước để có ID cho orderItems
            $order->save();
            $payment->save();
            
            // Create order items - Hỗ trợ cả buy now và giỏ hàng thông thường
            foreach ($cartItems as $cartItem) {
                $orderItem = new OrderItem();
                $orderItem->order_id = $order->id;
                $orderItem->quantity = $cartItem->quantity;

                if ($buyNow) {
                    // Xử lý cho "buy now"
                    if ($cartItem->variant) {
                        $orderItem->product_variant_id = $cartItem->variant->id;
                        // Use final_price (after discount) if available, otherwise original price
                        $unitPrice = isset($cartItem->final_price) ? $cartItem->final_price : $cartItem->variant->price;
                        // Subtract toppings price from final_price to get base unit price
                        $toppingsPrice = 0;
                        if ($cartItem->toppings && $cartItem->toppings->count() > 0) {
                            $toppingsPrice = $cartItem->toppings->sum('price');
                        }
                        $orderItem->unit_price = $unitPrice - $toppingsPrice;
                        $orderItem->total_price = $orderItem->unit_price * $cartItem->quantity;
                    } elseif ($cartItem->combo) {
                        $orderItem->combo_id = $cartItem->combo->id;
                        // For combo Buy Now: always use original price (no discount)
                        $orderItem->unit_price = $cartItem->combo->price;
                        $orderItem->total_price = $orderItem->unit_price * $cartItem->quantity;
                    } else {
                        continue;
                    }
                } else {
                    // Xử lý cho giỏ hàng thông thường - use final_price if available
                    if ($cartItem->product_variant_id) {
                        $orderItem->product_variant_id = $cartItem->product_variant_id;
                        $originalPrice = $cartItem->variant ? $cartItem->variant->price : 0;
                        $unitPrice = isset($cartItem->final_price) ? $cartItem->final_price : $originalPrice;
                        // Subtract toppings price from final_price to get base unit price
                        $toppingsPrice = 0;
                        if ($cartItem->toppings && $cartItem->toppings->count() > 0) {
                            foreach ($cartItem->toppings as $topping) {
                                if (isset($topping->topping) && $topping->topping) {
                                    $toppingsPrice += $topping->topping->price * ($topping->quantity ?? 1);
                                }
                            }
                        }
                        $orderItem->unit_price = $unitPrice - $toppingsPrice;
                        $orderItem->total_price = $orderItem->unit_price * $cartItem->quantity;
                    } elseif ($cartItem->combo_id) {
                        $orderItem->combo_id = $cartItem->combo_id;
                        $originalPrice = $cartItem->combo ? $cartItem->combo->price : 0;
                        $orderItem->unit_price = isset($cartItem->final_price) ? $cartItem->final_price : $originalPrice;
                        $orderItem->total_price = $orderItem->unit_price * $cartItem->quantity;
                    } else {
                        continue;
                    }
                }
                $orderItem->save();

                // Thêm topping cho order item nếu có
                if ($cartItem->toppings && $cartItem->toppings->count() > 0) {
                    if ($buyNow) {
                        // Xử lý topping cho "buy now"
                        foreach ($cartItem->toppings as $topping) {
                            $orderItem->toppings()->create([
                                'topping_id' => $topping->id,
                                'quantity' => 1, // Mặc định quantity = 1 cho buy now
                                'price' => $topping->price,
                            ]);
                        }
                    } else {
                        // Xử lý topping cho giỏ hàng thông thường
                        foreach ($cartItem->toppings as $cartTopping) {
                            $orderItem->toppings()->create([
                                'topping_id' => $cartTopping->topping_id,
                                'quantity' => $cartTopping->quantity,
                                'price' => $cartTopping->topping ? $cartTopping->topping->price : 0,
                            ]);
                        }
                    }
                }
            }
            
            // Đảm bảo đơn hàng đã được lưu trước khi snapshot
            if (!$order->exists) {
                $order->save();
            }
            
            // Đảm bảo load đầy đủ các quan hệ cần thiết cho snapshot
            $order->load(['orderItems.toppings', 'address']);
            
            // Log thông tin đơn hàng trước khi snapshot
            \Illuminate\Support\Facades\Log::info('Order before snapshot', [
                'order_id' => $order->id,
                'order_code' => $order->order_code,
                'address_id' => $order->address_id,
                'has_address' => $order->address ? true : false
            ]);
            
            // Snapshot dữ liệu đơn hàng để đảm bảo tính bất biến
            OrderSnapshotService::snapshotOrder($order);
            
            // Xử lý theo phương thức thanh toán
            if ($request->payment_method === 'vnpay') {
                $order->status = 'pending_payment'; // Sử dụng trạng thái pending_payment cho đơn hàng chưa thanh toán
                $order->save();

                // Logic tạo URL VNPAY sẽ ở đây
                $vnp_Url = $this->createVnpayUrl($order, $request); // createVnpayUrl vẫn dùng order

                DB::commit();

                // Redirect to VNPAY
                return redirect()->away($vnp_Url);

            } else if ($request->payment_method === 'balance') {
                // Xử lý thanh toán bằng số dư
                $order->status = 'awaiting_confirmation'; // Đơn hàng chờ xác nhận từ nhà hàng
                $order->save();
                
                // Trừ tiền từ tài khoản user
                $user = Auth::user();
                $user->balance -= $total;
                $user->save();
                
            } else { // COD
                $order->status = 'awaiting_confirmation';
                $order->save();
            }

            if ($order->status === 'awaiting_confirmation') {
                NewOrderReceived::dispatch($order);
                
                // Send confirmation email asynchronously only for confirmed orders
                dispatch(function() use ($order) {
                    EmailFactory::sendOrderConfirmation($order);
                });
            }
            
            // Clear cart or buy now session after order is placed
            if ($buyNow) {
                // Clear buy now session
                session()->forget('buy_now_checkout');
            } else {
                // Chỉ xóa cart items khi đơn hàng đã được xác nhận (không phải pending_payment)
                if ($order->status === 'awaiting_confirmation' && $cart && isset($selectedIds) && is_array($selectedIds)) {
                    // Xóa các CartItem đã được thanh toán
                    \App\Models\CartItem::where('cart_id', $cart->id)
                        ->whereIn('id', $selectedIds)
                        ->delete();
                    // Cập nhật lại cart_count trong session ngay lập tức
                    $cartCount = $cart->items()->count();
                    session(['cart_count' => $cartCount]);
                    // Nếu giỏ hàng không còn sản phẩm nào thì mới chuyển trạng thái completed
                    if ($cart->items()->count() == 0) {
                        $cart->status = 'completed';
                        $cart->save();
                        session()->forget(['coupon_discount_amount', 'discount']);
                    }
                }
            }
            
            // Clear discount after order is placed
            session()->forget('coupon_discount_amount');
            
            DB::commit();
            
            // Redirect to success page
            return redirect()->route('checkout.success', ['order_code' => $order->order_code])
                        ->with('success', 'Đơn hàng của bạn đã được đặt thành công!');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                        ->with('error', 'Đã có lỗi xảy ra: ' . $e->getMessage())
                        ->withInput();
        }
    }

    private function createVnpayUrl($order, $request)
    {
        // Lấy thông tin từ config
        $vnp_TmnCode = config('vnpay.tmn_code');
        $vnp_HashSecret = config('vnpay.hash_secret');
        $vnp_Url = config('vnpay.url');
        $vnp_Returnurl = route('checkout.vnpay_return'); // Sẽ tạo route này sau

        // Thông tin đơn hàng
        $vnp_TxnRef = $order->order_code; // Mã đơn hàng
        $vnp_OrderInfo = "Thanh toán đơn hàng " . $order->order_code;
        $vnp_OrderType = 'billpayment';
        $vnp_Amount = $order->total_amount * 100; // VNPAY yêu cầu amount * 100
        $vnp_Locale = 'vn';
        $vnp_IpAddr = $request->ip();
        
        $inputData = array(
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_OrderType" => $vnp_OrderType,
            "vnp_ReturnUrl" => $vnp_Returnurl,
            "vnp_TxnRef" => $vnp_TxnRef,
        );

        if (isset($vnp_BankCode) && $vnp_BankCode != "") {
            $inputData['vnp_BankCode'] = $vnp_BankCode;
        }
        
        ksort($inputData);
        $query = "";
        $i = 0;
        $hashdata = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }

        $vnp_Url = $vnp_Url . "?" . $query;
        if (isset($vnp_HashSecret)) {
            $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
            $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
        }
        
        return $vnp_Url;
    }

    public function vnpayReturn(Request $request)
    {
        $vnp_HashSecret = config('vnpay.hash_secret');
        $inputData = $request->all();
        $vnp_SecureHash = $inputData['vnp_SecureHash'];
        unset($inputData['vnp_SecureHash']);

        ksort($inputData);
        $hashData = "";
        $i = 0;
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashData .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashData .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
        }

        $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);

        if ($secureHash == $vnp_SecureHash) {
            if ($request->vnp_ResponseCode == '00') {
                $orderCode = $request->vnp_TxnRef;
                $order = Order::with('payment')->where('order_code', $orderCode)->first();

                if ($order && $order->payment && $order->payment->payment_status == 'pending') {
                    // Cập nhật trạng thái Payment
                    $order->payment->payment_status = 'completed';
                    $order->payment->response_code = $request->vnp_ResponseCode;
                    $order->payment->transaction_id = $request->vnp_TransactionNo;
                    $order->payment->bank_code = $request->vnp_BankCode;
                    $order->payment->payment_date = now();
                    $order->payment->save();

                    // Cập nhật trạng thái Order
                    $order->status = 'awaiting_confirmation';
                    $order->save();

                    // Snapshot dữ liệu đơn hàng để đảm bảo tính bất biến (nếu chưa có)
                    $order->load(['orderItems.toppings']);
                    if (!$order->orderItems->first() || !$order->orderItems->first()->hasSnapshotData()) {
                        OrderSnapshotService::snapshotOrder($order);
                    }
                    
                    // Dispatch event cho branch
                    NewOrderReceived::dispatch($order);

                    // Send confirmation email asynchronously
                    dispatch(function() use ($order) {
                        EmailFactory::sendOrderConfirmation($order);
                    });

                    // Clear cart items that were ordered
                    if ($order->customer_id) {
                        // For authenticated users
                        $cart = Cart::where('user_id', $order->customer_id)
                                    ->where('status', 'active')->first();
                    } else {
                        // For guest users (though VNPAY typically requires authentication)
                        $cart = Cart::where('session_id', session()->getId())
                                    ->where('status', 'active')->first();
                    }
                    
                    if ($cart) {
                        // Get cart item IDs from order snapshot data to know which items to remove
                        $orderItems = $order->orderItems;
                        $cartItemIdsToDelete = [];
                        
                        foreach ($orderItems as $orderItem) {
                            // Try to find matching cart items based on variant/combo and remove them
                            if ($orderItem->product_variant_id) {
                                $cartItems = \App\Models\CartItem::where('cart_id', $cart->id)
                                    ->where('product_variant_id', $orderItem->product_variant_id)
                                    ->get();
                                foreach ($cartItems as $cartItem) {
                                    $cartItemIdsToDelete[] = $cartItem->id;
                                }
                            } elseif ($orderItem->combo_id) {
                                $cartItems = \App\Models\CartItem::where('cart_id', $cart->id)
                                    ->where('combo_id', $orderItem->combo_id)
                                    ->get();
                                foreach ($cartItems as $cartItem) {
                                    $cartItemIdsToDelete[] = $cartItem->id;
                                }
                            }
                        }
                        
                        // Remove the cart items
                        if (!empty($cartItemIdsToDelete)) {
                            \App\Models\CartItem::whereIn('id', array_unique($cartItemIdsToDelete))->delete();
                        }
                        
                        // Update cart count in session
                        $cartCount = $cart->items()->count();
                        session(['cart_count' => $cartCount]);
                        
                        // If cart is empty, mark as completed
                        if ($cart->items()->count() == 0) {
                            $cart->status = 'completed';
                            $cart->save();
                            session()->forget(['coupon_discount_amount', 'discount']);
                        }
                    }
                }
                
                return redirect()->route('checkout.success', ['order_code' => $orderCode])
                                 ->with('success', 'Thanh toán thành công!');
            } else {
                $orderCode = $request->vnp_TxnRef;
                $order = Order::with('payment')->where('order_code', $orderCode)->first();
                if ($order) {
                    $order->status = 'payment_failed';
                    $order->save();
                    if ($order->payment) {
                        $order->payment->payment_status = 'failed';
                        $order->payment->response_code = $request->vnp_ResponseCode;
                        $order->payment->save();
                    }
                }
                return redirect()->route('cart.index')
                                 ->with('error', 'Thanh toán không thành công. Vui lòng thử lại.');
            }
        } else {
             return redirect()->route('cart.index')
                              ->with('error', 'Chữ ký không hợp lệ. Giao dịch có thể đã bị thay đổi.');
        }
    }

    public function vnpayIpn(Request $request)
    {
        // This is server-to-server notification, more reliable
        $vnp_HashSecret = config('vnpay.hash_secret');
        $inputData = $request->all();
        $vnp_SecureHash = $inputData['vnp_SecureHash'];
        unset($inputData['vnp_SecureHash']);

        ksort($inputData);
        $hashData = "";
        $i = 0;
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashData .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashData .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
        }

        $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);
        $orderId = $inputData['vnp_TxnRef'];
        $order = Order::with('payment')->where('order_code', $orderId)->first();
        $returnData = [];

        try {
            if ($secureHash == $vnp_SecureHash) {
                if ($order && $order->payment) {
                    if($order->payment->payment_amount == ($inputData['vnp_Amount'] / 100)) {
                         if ($order->payment->payment_status != 'completed') {
                            if ($inputData['vnp_ResponseCode'] == '00' && $inputData['vnp_TransactionStatus'] == '00') {
                                $order->status = 'awaiting_confirmation'; // hoặc 'processing' tùy vào logic của bạn
                                $order->payment->payment_status = 'completed';
                                
                                // Clear cart items that were ordered
                                if ($order->customer_id) {
                                    // For authenticated users
                                    $cart = \App\Models\Cart::where('user_id', $order->customer_id)
                                                ->where('status', 'active')->first();
                                } else {
                                    // For guest users (though VNPAY typically requires authentication)
                                    $cart = \App\Models\Cart::where('session_id', session()->getId())
                                                ->where('status', 'active')->first();
                                }
                                
                                if ($cart) {
                                    // Get cart item IDs from order snapshot data to know which items to remove
                                    $orderItems = $order->orderItems;
                                    $cartItemIdsToDelete = [];
                                    
                                    foreach ($orderItems as $orderItem) {
                                        // Try to find matching cart items based on variant/combo and remove them
                                        if ($orderItem->product_variant_id) {
                                            $cartItems = \App\Models\CartItem::where('cart_id', $cart->id)
                                                ->where('product_variant_id', $orderItem->product_variant_id)
                                                ->get();
                                            foreach ($cartItems as $cartItem) {
                                                $cartItemIdsToDelete[] = $cartItem->id;
                                            }
                                        } elseif ($orderItem->combo_id) {
                                            $cartItems = \App\Models\CartItem::where('cart_id', $cart->id)
                                                ->where('combo_id', $orderItem->combo_id)
                                                ->get();
                                            foreach ($cartItems as $cartItem) {
                                                $cartItemIdsToDelete[] = $cartItem->id;
                                            }
                                        }
                                    }
                                    
                                    // Remove the cart items
                                    if (!empty($cartItemIdsToDelete)) {
                                        \App\Models\CartItem::whereIn('id', array_unique($cartItemIdsToDelete))->delete();
                                    }
                                    
                                    // If cart is empty, mark as completed
                                    if ($cart->items()->count() == 0) {
                                        $cart->status = 'completed';
                                        $cart->save();
                                    }
                                }
                            } else {
                                $order->status = 'payment_failed';
                                $order->payment->payment_status = 'failed';
                            }
                            $order->save();
                            $order->payment->save();
                            
                            $returnData['RspCode'] = '00';
                            $returnData['Message'] = 'Confirm Success';
                         } else {
                            $returnData['RspCode'] = '02';
                            $returnData['Message'] = 'Order already confirmed';
                         }
                    } else {
                        $returnData['RspCode'] = '04';
                        $returnData['Message'] = 'Invalid amount';
                    }
                } else {
                    $returnData['RspCode'] = '01';
                    $returnData['Message'] = 'Order not found';
                }
            } else {
                $returnData['RspCode'] = '97';
                $returnData['Message'] = 'Invalid signature';
            }
        } catch (\Exception $e) {
            $returnData['RspCode'] = '99';
            $returnData['Message'] = 'Unknown error';
        }
        
        return response()->json($returnData);
    }
    
    /**
     * Display the order success page.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function success(Request $request)
    {
        $orderCode = $request->order_code;
        
        if (!$orderCode) {
            return redirect()->route('home')->with('error', 'Không tìm thấy thông tin đơn hàng');
        }
        
        // Get order details with comprehensive relationships
        $order = Order::with([
            'customer',
            'branch',
            'address',
            'payment', // Eager load payment
            'orderItems.productVariant.product.images',
            'orderItems.productVariant.variantValues.attribute',
            'orderItems.combo', // Load combo info
            'orderItems.toppings.topping'
        ])
        ->where('order_code', $orderCode)
        ->first();
        
        if (!$order) {
            return redirect()->route('home')->with('error', 'Đơn hàng không tồn tại');
        }
        
        // Process order items to add primary images and topping information
        foreach ($order->orderItems as $item) {
            
            if ($item->productVariant && $item->productVariant->product) {
                $product = $item->productVariant->product;
            
                // Set primary image
                $product->primary_image = $product->images
                    ->where('is_primary', true)
                    ->first() ?? $product->images->first();
                
                // Calculate item total including toppings
                $basePrice = $item->unit_price;
                $toppingsPrice = $item->toppings->sum(fn($t) => $t->topping->price ?? 0);
                $item->item_total_with_toppings = ($basePrice * $item->quantity) + ($toppingsPrice * $item->quantity);
                
                // Format variant description
                if ($item->productVariant->variant_description) {
                    $item->variant_display = $item->productVariant->variant_description;
                } else {
                    $item->variant_display = $item->productVariant->variantValues->pluck('value')->join(', ');
                }
            } elseif ($item->combo) {
                // It's a combo. Create a fake product structure for the view.
                $fakeProduct = new \stdClass();
                $fakeProduct->name = $item->combo->name . ' (Combo)';
                $fakeProduct->images = collect(); // Empty collection
                $fakeProduct->primary_image = (object)[
                    'img' => $item->combo->image ?? 'images/default-combo.png'
                ];

                $item->productVariant = new \stdClass();
                $item->productVariant->product = $fakeProduct;
                
                $item->variant_display = $item->combo->description ?? 'Gói sản phẩm đặc biệt';
                $item->item_total_with_toppings = $item->total_price;
                $item->toppings = collect(); // Ensure toppings is an empty collection
            } else {
                // Invalid item, create a placeholder to avoid crashing the view
                $fakeProduct = new \stdClass();
                $fakeProduct->name = 'Sản phẩm không hợp lệ';
                $fakeProduct->images = collect();
                $fakeProduct->primary_image = (object)[
                    'img' => 'images/default-topping.svg'
                ];

                $item->productVariant = new \stdClass();
                $item->productVariant->product = $fakeProduct;

                $item->variant_display = 'Vui lòng liên hệ hỗ trợ.';
                $item->item_total_with_toppings = 0;
                $item->toppings = collect();
            }
        }
        
        // Calculate estimated delivery time info
        $estimatedTime = null;
        $timeRange = null;
        if ($order->estimated_delivery_time) {
            $estimatedTime = \Carbon\Carbon::parse($order->estimated_delivery_time);
            $timeRange = [
                'start' => $estimatedTime->format('H:i'),
                'end' => $estimatedTime->addMinutes(15)->format('H:i'),
                'date' => $estimatedTime->format('d/m/Y')
            ];
        }
        
        // Get payment method display text
        $paymentMethods = [
            'cod' => 'Thanh toán khi nhận hàng (COD)',
            'vnpay' => 'Thanh toán qua VNPAY',
            'balance' => 'Thanh toán bằng số dư tài khoản'
        ];
        $order->payment_method_text = $paymentMethods[$order->payment->payment_method] ?? 'Không xác định';
        
        return view('customer.checkout.success', compact('order', 'timeRange'));
    }

    /**
     * Xử lý Mua ngay cho combo
     */
    public function comboBuyNow(Request $request)
    {
        $request->validate([
            'combo_id' => 'required|integer|exists:combos,id',
            'quantity' => 'required|integer|min:1'
        ]);
        session()->forget('buy_now_checkout');
        session(['buy_now_checkout' => [
            'type' => 'combo',
            'combo_id' => $request->combo_id,
            'quantity' => $request->quantity
        ]]);
        return response()->json([
            'success' => true,
            'redirect_url' => route('checkout.index', ['from_buy_now' => 1])
        ]);
    }

    /**
     * Xử lý Mua ngay cho sản phẩm
     */
    public function productBuyNow(Request $request)
    {
        // Debug log for Buy Now request
        \Log::debug('Buy Now request:', $request->all());
        
        $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'variant_id' => 'nullable|integer|exists:product_variants,id',
            'toppings' => 'nullable|array'
        ]);
        
        // Log the variant being saved to session
        \Log::debug('Buy Now saving to session:', [
            'type' => 'product',
            'product_id' => $request->product_id,
            'variant_id' => $request->variant_id,
            'toppings' => $request->toppings ?? [],
            'quantity' => $request->quantity
        ]);
        
        session()->forget('buy_now_checkout');
        session(['buy_now_checkout' => [
            'type' => 'product',
            'product_id' => $request->product_id,
            'variant_id' => $request->variant_id,
            'toppings' => $request->toppings ?? [],
            'quantity' => $request->quantity
        ]]);
        return response()->json([
            'success' => true,
            'redirect_url' => route('checkout.index', ['from_buy_now' => 1])
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    /**
     * Apply best discount for each cart item (same logic as CartController) and return updated subtotal
     */
    private function applyDiscountsToCartItems($cartItems, $branchId = null)
    {
        $now = \Carbon\Carbon::now();
        $currentTime = $now->format('H:i:s');

        // === LOAD ACTIVE DISCOUNT CODES (same as CartController) ===
        $activeDiscountCodesQuery = \App\Models\DiscountCode::where('is_active', true)
            ->where('start_date', '<=', $now)
            ->where('end_date', '>=', $now)
            ->where(function($query) use ($branchId) {
                if ($branchId) {
                    $query->whereDoesntHave('branches')
                          ->orWhereHas('branches', function($q) use ($branchId) {
                              $q->where('branches.id', $branchId);
                          });
                }
            });

        $activeDiscountCodesQuery->where(function($query) {
            $query->where('usage_type', 'public');
            if (\Illuminate\Support\Facades\Auth::check()) {
                $query->orWhere(function($q) {
                    $q->where('usage_type', 'personal')
                       ->whereHas('users', function($uq){
                           $uq->where('user_id', \Illuminate\Support\Facades\Auth::id());
                       });
                });
            }
        });

        $activeDiscountCodes = $activeDiscountCodesQuery->with(['products' => function($q){
            $q->with(['product', 'category']);
        }])->get()->filter(function($discountCode) use ($currentTime) {
            if ($discountCode->valid_from_time && $discountCode->valid_to_time) {
                $from = \Carbon\Carbon::parse($discountCode->valid_from_time)->format('H:i:s');
                $to   = \Carbon\Carbon::parse($discountCode->valid_to_time)->format('H:i:s');
                if ($from < $to) {
                    if (!($currentTime >= $from && $currentTime <= $to)) return false;
                } else {
                    if (!($currentTime >= $from || $currentTime <= $to)) return false;
                }
            }
            return true;
        });

        // === CALCULATE MIN PRICE FOR EACH PRODUCT (needed for product_price requirement) ===
        foreach ($cartItems as $item) {
            // Xử lý cho sản phẩm lẻ
            if ($item->variant && $item->variant->product) {
                $product = $item->variant->product;
                $product->min_price = $product->base_price;
                if ($product->variants && $product->variants->count()) {
                    $variantPrices = [];
                    foreach ($product->variants as $variant) {
                        $variantPrice = $product->base_price;
                        if ($variant->variantValues && $variant->variantValues->count()) {
                            $variantPrice += $variant->variantValues->sum('price_adjustment');
                        }
                        $variantPrices[] = $variantPrice;
                    }
                    if ($variantPrices) {
                        $product->min_price = min($variantPrices);
                    }
                }
            }
            // Xử lý cho combo - combo không cần tính min_price
            elseif ($item->combo) {
                // Combo không cần xử lý min_price
            }
        }

        // === APPLY DISCOUNT TO EACH ITEM ===
        $subtotal = 0;
        foreach ($cartItems as $item) {
            // Xử lý cho sản phẩm lẻ
            if ($item->variant) {
                $originPrice = $item->variant->price;

                $applicableDiscounts = $activeDiscountCodes->filter(function($discountCode) use ($item) {
                    // Scope ALL
                    if (($discountCode->applicable_scope === 'all') || ($discountCode->applicable_items === 'all_items')) {
                        if ($discountCode->min_requirement_type && $discountCode->min_requirement_value > 0) {
                            if ($discountCode->min_requirement_type === 'product_price') {
                                if ($item->variant->product->min_price < $discountCode->min_requirement_value) {
                                    return false;
                                }
                            }
                        }
                        return true;
                    }
                    // Scope specific products/categories
                    $applies = $discountCode->products->contains(function($dp) use ($item){
                        if ($dp->product_id === $item->variant->product->id) return true;
                        if ($dp->category_id === $item->variant->product->category_id) return true;
                        return false;
                    });
                    if ($applies && $discountCode->min_requirement_type === 'product_price' && $discountCode->min_requirement_value > 0) {
                        if ($item->variant->product->min_price < $discountCode->min_requirement_value) {
                            return false;
                        }
                    }
                    return $applies;
                });

                // Get best discount value
                $maxValue = 0;
                foreach ($applicableDiscounts as $d) {
                    $value = 0;
                    if ($d->discount_type === 'fixed_amount') {
                        $value = $d->discount_value;
                    } elseif ($d->discount_type === 'percentage') {
                        $value = $originPrice * $d->discount_value / 100;
                    }
                    if ($value > $maxValue) $maxValue = $value;
                }

                $finalPrice = max(0, $originPrice - $maxValue);
                
                // Handle toppings - different structure for Buy Now vs regular cart
                $toppingsPrice = 0;
                if ($item->toppings && $item->toppings->count() > 0) {
                    foreach ($item->toppings as $topping) {
                        // Buy Now: direct Topping model
                        if (isset($topping->price)) {
                            $toppingsPrice += $topping->price;
                        }
                        // Regular cart: CartTopping with topping relation
                        elseif (isset($topping->topping) && $topping->topping) {
                            $toppingsPrice += $topping->topping->price * ($topping->quantity ?? 1);
                        }
                    }
                }
                
                $finalPrice += $toppingsPrice;
                $item->final_price = $finalPrice;
                $subtotal += $finalPrice * $item->quantity;
            }
            // Xử lý cho combo
            elseif ($item->combo) {
                $originPrice = $item->combo->price;

                $applicableDiscounts = $activeDiscountCodes->filter(function($discountCode) use ($item) {
                    // Scope ALL
                    if (($discountCode->applicable_scope === 'all') || ($discountCode->applicable_items === 'all_items')) {
                        return true;
                    }
                    // Scope specific products/categories - combo có thể áp dụng discount cho category
                    $applies = $discountCode->products->contains(function($dp) use ($item){
                        if ($dp->category_id === $item->combo->category_id) return true;
                        return false;
                    });
                    return $applies;
                });

                // Get best discount value
                $maxValue = 0;
                foreach ($applicableDiscounts as $d) {
                    $value = 0;
                    if ($d->discount_type === 'fixed_amount') {
                        $value = $d->discount_value;
                    } elseif ($d->discount_type === 'percentage') {
                        $value = $originPrice * $d->discount_value / 100;
                    }
                    if ($value > $maxValue) $maxValue = $value;
                }

                $finalPrice = max(0, $originPrice - $maxValue);
                $item->final_price = $finalPrice;
                $subtotal += $finalPrice * $item->quantity;
            }
        }

        return $subtotal;
    }

    /**
     * Calculate subtotal for Buy Now items
     * Apply discount only to products, not combos
     */
    private function calculateBuyNowSubtotal($cartItems)
    {
        $subtotal = 0;
        
        foreach ($cartItems as $item) {
            if ($item->variant) {
                // For products: apply discount
                $currentBranch = $this->branchService->getCurrentBranch();
                $branchId = $currentBranch ? $currentBranch->id : null;
                
                // Create a temporary collection with just this product item
                $tempItems = collect([$item]);
                $discountedSubtotal = $this->applyDiscountsToCartItems($tempItems, $branchId);
                $subtotal += $discountedSubtotal;
                
            } elseif ($item->combo) {
                // For combos: no discount, use original price
                $comboPrice = $item->combo->price * $item->quantity;
                $subtotal += $comboPrice;
                
                // Set final_price for consistency (used in order creation)
                $item->final_price = $item->combo->price;
            }
        }
        
        return $subtotal;
    }

    /**
     * Tiếp tục thanh toán cho đơn hàng chưa thanh toán
     */
    public function continuePayment(Order $order)
    {
        // Kiểm tra quyền truy cập
        if (Auth::check()) {
            if ($order->customer_id !== Auth::id()) {
                abort(403, 'Bạn không có quyền truy cập đơn hàng này.');
            }
        } else {
            // Nếu là guest, kiểm tra session hoặc redirect về trang đăng nhập
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để tiếp tục thanh toán.');
        }

        // Kiểm tra trạng thái đơn hàng
        if ($order->status !== 'pending_payment') {
            return redirect()->route('customer.orders.show', $order)
                ->with('error', 'Đơn hàng này không thể tiếp tục thanh toán.');
        }

        // Kiểm tra payment method
        if (!$order->payment || $order->payment->payment_method !== 'vnpay') {
            return redirect()->route('customer.orders.show', $order)
                ->with('error', 'Đơn hàng này không sử dụng phương thức thanh toán VNPay.');
        }

        try {
            // Log thông tin debug
            \Illuminate\Support\Facades\Log::info('Creating VNPay URL for existing order', [
                'order_id' => $order->id,
                'order_code' => $order->order_code,
                'total_amount' => $order->total_amount,
                'vnpay_config' => [
                    'tmn_code' => config('vnpay.tmn_code'),
                    'url' => config('vnpay.url'),
                    'hash_secret_exists' => !empty(config('vnpay.hash_secret'))
                ]
            ]);
            
            // Tạo lại URL VNPay cho đơn hàng
            $vnp_Url = $this->createVnpayUrlForExistingOrder($order);
            
            \Illuminate\Support\Facades\Log::info('VNPay URL created successfully', [
                'order_id' => $order->id,
                'vnp_url_length' => strlen($vnp_Url)
            ]);
            
            return redirect()->away($vnp_Url);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error creating VNPay URL for existing order', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->route('customer.orders.show', $order)
                ->with('error', 'Có lỗi xảy ra khi tạo liên kết thanh toán. Vui lòng thử lại.');
        }
    }

    /**
     * Tạo URL VNPay cho đơn hàng đã tồn tại
     */
    private function createVnpayUrlForExistingOrder(Order $order)
    {
        $vnp_TmnCode = config('vnpay.tmn_code');
        $vnp_HashSecret = config('vnpay.hash_secret');
        $vnp_Url = config('vnpay.url');
        $vnp_ReturnUrl = route('checkout.vnpay_return');
        
        // Validate config values
        if (empty($vnp_TmnCode)) {
            throw new \Exception('VNPAY TMN Code is not configured');
        }
        if (empty($vnp_HashSecret)) {
            throw new \Exception('VNPAY Hash Secret is not configured');
        }
        if (empty($vnp_Url)) {
            throw new \Exception('VNPAY URL is not configured');
        }

        $vnp_TxnRef = $order->order_code; // Sử dụng order_code làm txn_ref
        $vnp_OrderInfo = 'Thanh toán đơn hàng ' . $order->order_code;
        $vnp_OrderType = 'billpayment';
        $vnp_Amount = $order->total_amount * 100; // VNPay yêu cầu số tiền tính bằng đồng
        $vnp_Locale = 'vn';
        $vnp_BankCode = '';
        $vnp_IpAddr = request()->ip() ?: '127.0.0.1'; // Fallback to localhost if IP not available
        
        // Validate required fields
        if (empty($vnp_TxnRef)) {
            throw new \Exception('Order code is required');
        }
        if ($vnp_Amount <= 0) {
            throw new \Exception('Order amount must be greater than 0');
        }

        $inputData = array(
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_OrderType" => $vnp_OrderType,
            "vnp_ReturnUrl" => $vnp_ReturnUrl,
            "vnp_TxnRef" => $vnp_TxnRef,
        );

        if (isset($vnp_BankCode) && $vnp_BankCode != "") {
            $inputData['vnp_BankCode'] = $vnp_BankCode;
        }

        ksort($inputData);
        
        \Illuminate\Support\Facades\Log::info('VNPay input data', [
            'input_data' => $inputData
        ]);
        
        $query = "";
        $i = 0;
        $hashdata = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }

        \Illuminate\Support\Facades\Log::info('VNPay hash data', [
            'hashdata' => $hashdata,
            'query' => $query
        ]);

        $vnp_Url = $vnp_Url . "?" . $query;
        if (isset($vnp_HashSecret)) {
            $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
            $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
            
            \Illuminate\Support\Facades\Log::info('VNPay secure hash created', [
                'secure_hash' => $vnpSecureHash,
                'final_url_length' => strlen($vnp_Url)
            ]);
        }

        return $vnp_Url;
    }
}