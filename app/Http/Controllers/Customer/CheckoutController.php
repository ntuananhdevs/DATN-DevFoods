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
use App\Mail\EmailFactory;

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
        $cartItems = [];
        $subtotal = 0;

        $userId = Auth::id();
        $sessionId = session()->getId();

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
                'toppings'
            ])->where('cart_id', $cart->id);

            if ($selectedIds && is_array($selectedIds)) {
                $query->whereIn('id', $selectedIds);
            }

            $cartItems = $query->get();

            // === NEW: tính subtotal bằng hàm applyDiscountsToCartItems ===
            $subtotal = $this->applyDiscountsToCartItems($cartItems);

            foreach ($cartItems as $item) {
                // Đặt primary image
                $item->variant->product->primary_image = $item->variant->product->images
                    ->where('is_primary', true)
                    ->first() ?? $item->variant->product->images->first();
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

        return view('customer.checkout.index', compact('cartItems', 'subtotal', 'cart', 'userAddresses', 'currentBranch'));
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
        
        // Validate checkout data with different rules for authenticated vs guest users
        if ($userId) {
            // For authenticated users, require address_id selection
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
            // For guest users, require manual input
            $validated = $request->validate([
                'full_name' => 'required|string|max:255',
                'phone' => 'required|string|max:20',
                'email' => 'required|email|max:255',
                'address' => 'required|string|max:255',
                'city' => 'required|string|max:100',
                'district' => 'required|string|max:100',
                'ward' => 'required|string|max:100',
                'payment_method' => 'required|string|in:cod,vnpay,balance',
                'notes' => 'nullable|string',
                'terms' => 'required',
            ]);
        }
        
        try {
            DB::beginTransaction();
            
            // Get session ID for cart lookup
            $sessionId = session()->getId();
            
            // Query the cart based on user_id or session_id
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
            
            // Get cart items
            $cartItems = CartItem::with(['variant.product'])
                ->where('cart_id', $cart->id)
                ->get();
            
            if ($cartItems->isEmpty()) {
                throw new \Exception('Giỏ hàng của bạn đang trống.');
            }
            
            // Calculate totals - FIX: Include toppings for consistency with view
            // Tính subtotal đồng bộ với view (đã bao gồm discount + topping)
            $subtotal = $this->applyDiscountsToCartItems($cartItems);
            
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
                // Đối với guest, ta cần có tọa độ. Giả sử nó được gửi lên từ form
                // Nếu không, ta không thể tính phí theo khoảng cách.
                // For now, we'll assume guest checkout doesn't support distance-based fees
                // unless we implement address->lat/lng conversion on the fly.
                 throw new \Exception('Tính năng đặt hàng cho khách chưa hỗ trợ phí vận chuyển theo khoảng cách.');
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

            // Tính phí vận chuyển
            $shipping = ShippingService::calculateFee($subtotal, $distance);
            if ($shipping < 0) {
                 throw new \Exception('Địa chỉ giao hàng nằm ngoài vùng phục vụ.');
            }

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
            
            // Xử lý theo phương thức thanh toán
            if ($request->payment_method === 'vnpay') {
                $order->status = 'pending_payment'; 
                $order->save();
                $payment->save(); // Lưu lại txn_ref mới

                // Logic tạo URL VNPAY sẽ ở đây
                $vnp_Url = $this->createVnpayUrl($order, $request); // createVnpayUrl vẫn dùng order

                DB::commit();

                // Redirect to VNPAY
                return redirect()->away($vnp_Url);

            } else if ($request->payment_method === 'balance') {
                // Xử lý thanh toán bằng số dư
                $order->status = 'awaiting_confirmation'; // Đơn hàng chờ xác nhận từ nhà hàng
                $order->save();
                $payment->save(); // Lưu lại txn_ref mới
                
                // Trừ tiền từ tài khoản user
                $user = Auth::user();
                $user->balance -= $total;
                $user->save();
                
            } else { // COD
                $order->status = 'awaiting_confirmation';
                $order->save();
                $payment->save(); // Lưu lại txn_ref mới
            }

            if ($order->status === 'awaiting_confirmation') {
                NewOrderReceived::dispatch($order);
            }
            
            // Create order items
            foreach ($cartItems as $cartItem) {
                $orderItem = new OrderItem();
                $orderItem->order_id = $order->id;
                $orderItem->product_variant_id = $cartItem->variant_id;
                $orderItem->quantity = $cartItem->quantity;
                $orderItem->unit_price = $cartItem->variant->price;
                $orderItem->total_price = $cartItem->variant->price * $cartItem->quantity;
                $orderItem->save();
            }
            
            // Clear cart after order is placed by marking it as completed
            $cart->status = 'completed';
            $cart->save();
            
            // Clear discount after order is placed
            session()->forget('coupon_discount_amount');
            session()->forget('cart_count');
            
            DB::commit();
            
            // Send confirmation email
            EmailFactory::sendOrderConfirmation($order);
            
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

                    // Dispatch event cho branch
                    NewOrderReceived::dispatch($order);

                    // Send confirmation email
                    EmailFactory::sendOrderConfirmation($order);

                    // Clear cart
                    $cart = Cart::where('user_id', $order->customer_id)
                                ->orWhere('session_id', session()->getId())
                                ->where('status', 'active')->first();
                    if ($cart) {
                        $cart->status = 'completed';
                        $cart->save();
                        session()->forget(['coupon_discount_amount', 'cart_count', 'discount']);
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

        // === APPLY DISCOUNT TO EACH ITEM ===
        $subtotal = 0;
        foreach ($cartItems as $item) {
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
            $finalPrice += $item->toppings->sum(function($t){return $t->topping->price ?? 0;});
            $item->final_price = $finalPrice;
            $subtotal += $finalPrice * $item->quantity;
        }

        return $subtotal;
    }
}
