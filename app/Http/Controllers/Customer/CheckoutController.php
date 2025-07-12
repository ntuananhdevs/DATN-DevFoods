<?php

namespace App\Http\Controllers\Customer;

use App\Events\NewOrderAvailable;
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

            foreach ($cartItems as $item) {
                $subtotal += $item->variant->price * $item->quantity;
                foreach ($item->toppings as $topping) {
                    $subtotal += $topping->price * $item->quantity;
                }
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
        $userAddresses = [];
        if ($userId) {
            $userAddresses = Address::where('user_id', $userId)
                ->orderBy('is_default', 'desc')
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return view('customer.checkout.index', compact('cartItems', 'subtotal', 'cart', 'userAddresses'));
    }
    
    /**
     * Process the checkout.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function process(Request $request)
    {
        // Validate checkout data
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'district' => 'required|string|max:100',
            'ward' => 'required|string|max:100',
            'address_id' => 'nullable|exists:addresses,id',
            'payment_method' => 'required|string|in:cod,vnpay,balance',
            'notes' => 'nullable|string',
            'terms' => 'required',
        ]);
        
        try {
            DB::beginTransaction();
            
            // Get user ID (if logged in) or generate a guest ID
            $userId = Auth::id();
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
            
            // Calculate totals
            $subtotal = 0;
            foreach ($cartItems as $item) {
                $subtotal += $item->variant->price * $item->quantity;
            }
            
            // Set a fixed shipping fee or based on subtotal
            $shipping = $subtotal > 200000 ? 0 : 25000; // Example: free shipping for orders over 200k
            
            // Apply discount if available
            $discount = session('discount', 0);
            
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
            
            // Create order
            $order = new Order();
            
            // Thiết lập thông tin người dùng - khác nhau giữa user và guest
            if ($userId) {
                $order->customer_id = $userId;
                
                // Nếu user chọn địa chỉ có sẵn từ dropdown
                if ($request->address_id) {
                    $address = Address::where('id', $request->address_id)
                        ->where('user_id', $userId)
                        ->first();
                    if ($address) {
                        $order->address_id = $address->id;
                    }
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
            
            // Thông tin chung
            $order->branch_id = $branchId;
            $order->order_code = 'ORD-' . strtoupper(substr(uniqid(), -8));
            $order->status = 'awaiting_confirmation';
            $order->delivery_fee = $shipping;
            $order->discount_amount = $discount;
            $order->subtotal = $subtotal;
            $order->total_amount = $total;
            $order->notes = $request->notes;
            $order->delivery_address = $request->address . ', ' . $request->ward . ', ' . $request->district . ', ' . $request->city;
            
            // Xử lý theo phương thức thanh toán
            if ($request->payment_method === 'vnpay') {
                $order->payment_method = 'vnpay';
                $order->status = 'pending_payment'; 
                $order->save();

                // Logic tạo URL VNPAY sẽ ở đây
                $vnp_Url = $this->createVnpayUrl($order, $request);

                DB::commit();

                // Redirect to VNPAY
                return redirect()->away($vnp_Url);

            } else if ($request->payment_method === 'balance') {
                // Xử lý thanh toán bằng số dư
                $order->payment_method = 'balance';
                $order->status = 'awaiting_confirmation'; // Đơn hàng chờ xác nhận từ nhà hàng
                $order->save();
                
                // Trừ tiền từ tài khoản user
                $user = Auth::user();
                $user->balance -= $total;
                $user->save();
                
            } else {
                // Xử lý các phương thức thanh toán khác (ví dụ: COD)
                $order->payment_method = $request->payment_method;
                $order->status = 'awaiting_confirmation';
                $order->save();
            }

            if ($order->status === 'awaiting_confirmation') {
                \App\Events\Branch\NewOrderReceived::dispatch($order);
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
            session()->forget('discount');
            session()->forget('cart_count');
            
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
                $order = Order::where('order_code', $orderCode)->first();

                if ($order && $order->status == 'pending_payment') {
                    $order->status = 'awaiting_confirmation'; // Đơn hàng chờ xác nhận từ nhà hàng
                    $order->save();

                    // Dispatch event cho branch
                    \App\Events\Order\NewOrderReceived::dispatch($order);

                    // Clear cart
                    $cart = Cart::where('user_id', $order->customer_id)
                                ->orWhere('session_id', session()->getId())
                                ->where('status', 'active')->first();
                    if ($cart) {
                        $cart->status = 'completed';
                        $cart->save();
                        session()->forget(['cart_count', 'discount']);
                    }
                }
                
                return redirect()->route('checkout.success', ['order_code' => $orderCode])
                                 ->with('success', 'Thanh toán thành công!');
            } else {
                $orderCode = $request->vnp_TxnRef;
                Order::where('order_code', $orderCode)->update(['status' => 'payment_failed']);
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
        $order = Order::where('order_code', $orderId)->first();
        $returnData = [];

        try {
            if ($secureHash == $vnp_SecureHash) {
                if ($order) {
                    if($order->total_amount == ($inputData['vnp_Amount'] / 100)) {
                         if ($order->status != 'processing' && $order->status != 'completed') {
                            if ($inputData['vnp_ResponseCode'] == '00' && $inputData['vnp_TransactionStatus'] == '00') {
                                $order->status = 'processing';
                            } else {
                                $order->status = 'payment_failed';
                            }
                            $order->save();
                            
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
        
        // Get order details
        $order = Order::with(['orderItems.productVariant.product'])
                    ->where('order_code', $orderCode)
                    ->first();
        
        if (!$order) {
            return redirect()->route('home')->with('error', 'Đơn hàng không tồn tại');
        }
        
        return view('customer.checkout.success', compact('order'));
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
}
