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
        // Get cart items
        $cartItems = [];
        $subtotal = 0;
        
        // Determine if user is authenticated or using session
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
        
        // Get cart items with their relationships
        if ($cart) {
            $cartItems = CartItem::with([
                'variant.product.images',
                'variant.variantValues.attribute',
                'toppings'
            ])->where('cart_id', $cart->id)->get();
            
            // Calculate subtotal
            foreach ($cartItems as $item) {
                $subtotal += $item->variant->price * $item->quantity;
                
                // Set primary image for display
                $item->variant->product->primary_image = $item->variant->product->images
                    ->where('is_primary', true)
                    ->first() ?? $item->variant->product->images->first();
            }
        } else {
            // Create an empty collection if no cart found
            $cartItems = collect([]);
        }
        
        // If cart is empty, redirect to cart page
        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng của bạn đang trống. Vui lòng thêm sản phẩm trước khi thanh toán.');
        }

        return view('customer.checkout.index', compact('cartItems', 'subtotal', 'cart'));
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
            'shipping_method' => 'required|string|in:standard,express',
            'payment_method' => 'required|string|in:cod,bank_transfer,credit_card,e_wallet',
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
            
            // Calculate shipping
            $shipping = $request->shipping_method === 'express' ? 30000 : ($subtotal > 100000 ? 0 : 15000);
            
            // Apply discount if available
            $discount = session('discount', 0);
            
            // Calculate total
            $total = $subtotal + $shipping - $discount;
            
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
                
                // Nếu user có địa chỉ lưu trong DB, sử dụng nó
                $address = Address::where('user_id', $userId)->first();
                if ($address) {
                    $order->address_id = $address->id;
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
            $order->order_number = 'ORD-' . strtoupper(uniqid());
            $order->status = 'pending';
            $order->delivery_fee = $shipping;
            $order->discount_amount = $discount;
            $order->subtotal = $subtotal;
            $order->total_amount = $total;
            $order->notes = $request->notes;
            $order->delivery_address = $request->address . ', ' . $request->ward . ', ' . $request->district . ', ' . $request->city;
            $order->save();

            if ($order->status === 'pending' && is_null($order->driver_id)) {
                \App\Events\NewOrderAvailable::dispatch($order);
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
            return redirect()->route('checkout.success', ['order_number' => $order->order_number])
                        ->with('success', 'Đơn hàng của bạn đã được đặt thành công!');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                        ->with('error', 'Đã có lỗi xảy ra: ' . $e->getMessage())
                        ->withInput();
        }
    }
    
    /**
     * Display the order success page.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function success(Request $request)
    {
        $orderNumber = $request->order_number;
        
        // Get order details
        $order = Order::with(['orderItems.productVariant.product'])
                    ->where('order_number', $orderNumber)
                    ->first();
        
        if (!$order) {
            return redirect()->route('home');
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
