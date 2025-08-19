# DOAN.MD - HỆ THỐNG DEVFOODS
## Tài liệu Luồng Hoạt Động và Logic Hệ Thống

---

## 1. TỔNG QUAN HỆ THỐNG

### 1.1 Mô tả hệ thống
DevFoods là hệ thống đặt đồ ăn trực tuyến với 4 đối tượng người dùng chính:
- **Customer (Khách hàng)**: Đặt đồ ăn, theo dõi đơn hàng
- **Branch Manager (Quản lý chi nhánh)**: Quản lý đơn hàng, sản phẩm tại chi nhánh
- **Driver (Tài xế)**: Nhận và giao đơn hàng
- **Admin (Quản trị viên)**: Quản lý toàn bộ hệ thống

### 1.2 Kiến trúc hệ thống
```
Frontend (Blade Templates + JavaScript)
├── Customer Interface
├── Branch Manager Interface  
├── Driver Interface
└── Admin Interface

Backend (Laravel)
├── Controllers (Customer, Branch, Driver, Admin)
├── Models (User, Order, Product, Driver, Branch...)
├── Events & Listeners (Real-time notifications)
├── Jobs (Background processing)
└── Services (Business logic)

Database (MySQL)
├── Users, Drivers, Branches
├── Products, Categories, Combos
├── Orders, OrderItems, Payments
└── Chat, Notifications
```

---

## 2. LUỒNG HOẠT ĐỘNG CHI TIẾT

### 2.1 LUỒNG ĐĂNG KÝ VÀ ĐĂNG NHẬP

#### 2.1.1 Đăng ký Customer
**Bước 1: Khách hàng truy cập trang đăng ký**
- Route: `GET /customer/register`
- Controller: `CustomerAuthController@showRegisterForm`

**Bước 2: Nhập thông tin và gửi OTP**
- Validate: email, phone, password
- Gửi OTP qua email/SMS
- Lưu thông tin tạm thời trong session

**Bước 3: Xác thực OTP**
- Route: `POST /customer/verify-otp`
- Tạo tài khoản trong database
- Tự động đăng nhập

**Logic xử lý:**
```php
// CustomerAuthController
public function register(Request $request) {
    // 1. Validate dữ liệu
    $validated = $request->validate([
        'email' => 'required|email|unique:users',
        'phone' => 'required|unique:users',
        'password' => 'required|min:6'
    ]);
    
    // 2. Gửi OTP
    $otp = rand(100000, 999999);
    dispatch(new SendOTPJob($validated['email'], $otp));
    
    // 3. Lưu session
    session(['registration_data' => $validated, 'otp' => $otp]);
    
    return redirect()->route('customer.verify-otp');
}
```

#### 2.1.2 Đăng ký Driver (Tài xế)
**Bước 1: Nộp đơn ứng tuyển**
- Route: `GET /hiring-driver/apply`
- Form: Thông tin cá nhân, CCCD, bằng lái, ảnh xe

**Bước 2: Admin duyệt đơn**
- Route: `POST /admin/drivers/applications/{id}/approve`
- Tạo tài khoản Driver với status = 'approved'
- Gửi email thông báo kết quả

**Logic duyệt đơn:**
```php
// Admin\DriverApplicationController
public function approve($id) {
    $application = DriverApplication::findOrFail($id);
    
    // Tạo tài khoản driver
    $driver = Driver::create([
        'email' => $application->email,
        'full_name' => $application->full_name,
        'phone_number' => $application->phone,
        'status' => 'approved',
        'application_id' => $application->id
    ]);
    
    // Gửi thông báo
    $driver->notify(new DriverApplicationStatusUpdated('approved'));
}
```

### 2.2 LUỒNG ĐẶT HÀNG (CUSTOMER)

#### 2.2.1 Chọn sản phẩm và thêm vào giỏ hàng
**Bước 1: Duyệt sản phẩm**
- Route: `GET /customer/products`
- Hiển thị sản phẩm theo category, branch
- Filter: giá, đánh giá, availability

**Bước 2: Thêm vào giỏ hàng**
- Route: `POST /customer/cart/add`
- Validate: product_id, variant_id, quantity, toppings
- Kiểm tra stock tại branch được chọn

**Logic thêm giỏ hàng:**
```php
// Customer\CartController
public function addToCart(Request $request) {
    $validated = $request->validate([
        'product_id' => 'required|exists:products,id',
        'variant_id' => 'required|exists:product_variants,id',
        'quantity' => 'required|integer|min:1',
        'branch_id' => 'required|exists:branches,id'
    ]);
    
    // Kiểm tra stock
    $stock = BranchStock::where('branch_id', $validated['branch_id'])
                       ->where('variant_id', $validated['variant_id'])
                       ->first();
    
    if (!$stock || $stock->stock_quantity < $validated['quantity']) {
        return response()->json(['error' => 'Không đủ hàng trong kho']);
    }
    
    // Thêm vào cart
    $cart = Cart::firstOrCreate(['customer_id' => auth()->id()]);
    
    CartItem::create([
        'cart_id' => $cart->id,
        'product_variant_id' => $validated['variant_id'],
        'quantity' => $validated['quantity'],
        'price' => $variant->price
    ]);
}
```

#### 2.2.2 Checkout và thanh toán
**Bước 1: Xem lại giỏ hàng**
- Route: `GET /customer/checkout`
- Hiển thị: sản phẩm, địa chỉ giao hàng, phương thức thanh toán
- Tính: subtotal, delivery_fee, discount, total

**Bước 2: Xác nhận đơn hàng**
- Route: `POST /customer/checkout/process`
- Validate địa chỉ, payment method
- Tạo Order và OrderItems

**Logic checkout:**
```php
// Customer\CheckoutController
public function processCheckout(Request $request) {
    DB::beginTransaction();
    try {
        // 1. Validate và tính toán
        $cart = Cart::with('items')->where('customer_id', auth()->id())->first();
        $subtotal = $cart->items->sum(fn($item) => $item->price * $item->quantity);
        $deliveryFee = $this->calculateDeliveryFee($request->address_id);
        $total = $subtotal + $deliveryFee;
        
        // 2. Tạo đơn hàng
        $order = Order::create([
            'customer_id' => auth()->id(),
            'branch_id' => $request->branch_id,
            'address_id' => $request->address_id,
            'subtotal' => $subtotal,
            'delivery_fee' => $deliveryFee,
            'total_amount' => $total,
            'status' => 'pending_payment',
            'order_code' => $this->generateOrderCode()
        ]);
        
        // 3. Tạo order items
        foreach ($cart->items as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_variant_id' => $item->product_variant_id,
                'quantity' => $item->quantity,
                'price' => $item->price
            ]);
        }
        
        // 4. Xử lý thanh toán
        if ($request->payment_method === 'vnpay') {
            return $this->processVNPayPayment($order);
        } else {
            $order->update(['status' => 'awaiting_confirmation']);
            event(new NewOrderReceived($order));
        }
        
        DB::commit();
    } catch (Exception $e) {
        DB::rollback();
        throw $e;
    }
}
```

### 2.3 LUỒNG XỬ LÝ ĐơN HÀNG (BRANCH MANAGER)

#### 2.3.1 Nhận và xác nhận đơn hàng
**Bước 1: Nhận thông báo đơn hàng mới**
- Event: `NewOrderReceived`
- Real-time notification qua WebSocket
- Hiển thị trong dashboard branch

**Bước 2: Xem chi tiết đơn hàng**
- Route: `GET /branch/orders/{id}`
- Hiển thị: thông tin khách hàng, sản phẩm, địa chỉ giao hàng
- Kiểm tra stock availability

**Bước 3: Xác nhận hoặc từ chối đơn hàng**
- Route: `POST /branch/orders/{id}/confirm`
- Route: `POST /branch/orders/{id}/cancel`

**Logic xác nhận đơn hàng:**
```php
// Branch\OrderController
public function confirmOrder($id) {
    $order = Order::findOrFail($id);
    
    if ($order->status !== 'awaiting_confirmation') {
        return response()->json(['error' => 'Đơn hàng không thể xác nhận']);
    }
    
    // Kiểm tra stock
    foreach ($order->orderItems as $item) {
        $stock = BranchStock::where('branch_id', $order->branch_id)
                           ->where('variant_id', $item->product_variant_id)
                           ->first();
        
        if (!$stock || $stock->stock_quantity < $item->quantity) {
            return response()->json(['error' => 'Không đủ hàng trong kho']);
        }
    }
    
    // Cập nhật trạng thái và trừ stock
    $order->update(['status' => 'confirmed']);
    
    foreach ($order->orderItems as $item) {
        BranchStock::where('branch_id', $order->branch_id)
                   ->where('variant_id', $item->product_variant_id)
                   ->decrement('stock_quantity', $item->quantity);
    }
    
    // Tìm tài xế
    dispatch(new FindDriverForOrderJob($order));
    
    event(new OrderConfirmed($order));
}
```

#### 2.3.2 Tìm và phân công tài xế
**Bước 1: Tự động tìm tài xế**
- Job: `FindDriverForOrderJob`
- Tìm tài xế gần nhất, đang available
- Gửi thông báo đến tài xế

**Bước 2: Tài xế xác nhận nhận đơn**
- Timeout: 2 phút không phản hồi → tìm tài xế khác
- Nếu từ chối → tìm tài xế khác

**Logic tìm tài xế:**
```php
// Jobs\FindDriverForOrderJob
public function handle() {
    $order = $this->order;
    $branch = $order->branch;
    
    // Tìm tài xế trong bán kính 10km
    $drivers = Driver::select('drivers.*')
        ->selectRaw('(
            6371 * acos(
                cos(radians(?)) * cos(radians(latitude)) *
                cos(radians(longitude) - radians(?)) +
                sin(radians(?)) * sin(radians(latitude))
            )
        ) AS distance', [$branch->latitude, $branch->longitude, $branch->latitude])
        ->where('status', 'active')
        ->where('is_available', true)
        ->having('distance', '<=', 10)
        ->orderBy('distance')
        ->limit(5)
        ->get();
    
    foreach ($drivers as $driver) {
        // Gửi thông báo đến tài xế
        $driver->notify(new NewOrderAvailable($order));
        
        // Đợi phản hồi trong 2 phút
        $response = $this->waitForDriverResponse($driver, $order, 120);
        
        if ($response === 'accepted') {
            $order->update([
                'driver_id' => $driver->id,
                'status' => 'driver_assigned'
            ]);
            return;
        }
    }
    
    // Không tìm được tài xế
    $order->update(['status' => 'awaiting_driver']);
}
```

### 2.4 LUỒNG GIAO HÀNG (DRIVER)

#### 2.4.1 Nhận thông báo đơn hàng mới
**Bước 1: Nhận notification**
- Event: `NewOrderAvailable`
- Push notification trên mobile app
- Hiển thị popup xác nhận

**Bước 2: Xác nhận nhận đơn**
- Route: `POST /driver/orders/{id}/accept`
- Timeout: 2 phút

#### 2.4.2 Quy trình giao hàng
**Các trạng thái đơn hàng của tài xế:**
1. `awaiting_driver` → Chờ tài xế nhận
2. `driver_assigned` → Đã phân công tài xế
3. `driver_confirmed` → Tài xế xác nhận
4. `waiting_driver_pick_up` → Chờ tài xế đến lấy hàng
5. `driver_picked_up` → Đã lấy hàng
6. `in_transit` → Đang giao hàng
7. `delivered` → Đã giao hàng
8. `item_received` → Khách hàng xác nhận nhận hàng

**Logic giao hàng:**
```php
// Driver\OrderController
public function acceptOrder($id) {
    $order = Order::findOrFail($id);
    $driverId = Auth::guard('driver')->id();
    
    if ($order->status !== 'awaiting_driver') {
        return response()->json(['error' => 'Đơn hàng không khả dụng']);
    }
    
    $order->update([
        'driver_id' => $driverId,
        'status' => 'driver_confirmed'
    ]);
    
    event(new OrderStatusUpdated($order, false, 'awaiting_driver', 'driver_confirmed'));
}

public function confirmPickup($id) {
    $order = Order::findOrFail($id);
    
    if ($order->status !== 'waiting_driver_pick_up') {
        return response()->json(['error' => 'Không thể xác nhận lấy hàng']);
    }
    
    $order->update(['status' => 'driver_picked_up']);
    event(new OrderStatusUpdated($order));
}

public function startDelivery($id) {
    $order = Order::findOrFail($id);
    
    $order->update(['status' => 'in_transit']);
    event(new OrderStatusUpdated($order));
}

public function confirmDelivery($id) {
    $order = Order::findOrFail($id);
    
    $order->update([
        'status' => 'delivered',
        'actual_delivery_time' => now()
    ]);
    
    // Tính toán thu nhập cho tài xế
    $driverEarning = $this->calculateDriverEarning($order);
    $order->update(['driver_earning' => $driverEarning]);
    
    event(new OrderStatusUpdated($order));
}
```

### 2.5 LUỒNG QUẢN TRỊ HỆ THỐNG (ADMIN)

#### 2.5.1 Quản lý người dùng
**Quản lý Customer:**
- Xem danh sách, thống kê hoạt động
- Khóa/mở khóa tài khoản
- Xem lịch sử đơn hàng

**Quản lý Driver:**
- Duyệt đơn ứng tuyển
- Quản lý trạng thái hoạt động
- Xem báo cáo hiệu suất
- Xử lý khiếu nại

**Quản lý Branch Manager:**
- Tạo tài khoản quản lý chi nhánh
- Phân quyền truy cập
- Giám sát hoạt động chi nhánh

#### 2.5.2 Quản lý sản phẩm và kho
**Quản lý sản phẩm:**
```php
// Admin\ProductController
public function store(Request $request) {
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'category_id' => 'required|exists:categories,id',
        'description' => 'required|string',
        'status' => 'required|in:selling,coming_soon,discontinued'
    ]);
    
    $product = Product::create($validated);
    
    // Tạo variants
    foreach ($request->variants as $variantData) {
        $variant = ProductVariant::create([
            'product_id' => $product->id,
            'price' => $variantData['price'],
            'sku' => $variantData['sku']
        ]);
        
        // Phân bổ stock cho các chi nhánh
        foreach (Branch::all() as $branch) {
            BranchStock::create([
                'branch_id' => $branch->id,
                'variant_id' => $variant->id,
                'stock_quantity' => 0
            ]);
        }
    }
}
```

#### 2.5.3 Báo cáo và thống kê
**Dashboard Analytics:**
- Doanh thu theo ngày/tháng/năm
- Số lượng đơn hàng
- Top sản phẩm bán chạy
- Hiệu suất chi nhánh
- Đánh giá tài xế

---

## 3. HỆ THỐNG THÔNG BÁO REAL-TIME

### 3.1 WebSocket Integration
**Cấu hình Broadcasting:**
```php
// config/broadcasting.php
'pusher' => [
    'driver' => 'pusher',
    'key' => env('PUSHER_APP_KEY'),
    'secret' => env('PUSHER_APP_SECRET'),
    'app_id' => env('PUSHER_APP_ID'),
    'options' => [
        'cluster' => env('PUSHER_APP_CLUSTER'),
        'useTLS' => true,
    ],
],
```

**Channels định nghĩa:**
- `order.{order_id}` - Thông báo cho đơn hàng cụ thể
- `branch.{branch_id}` - Thông báo cho chi nhánh
- `driver.{driver_id}` - Thông báo cho tài xế
- `customer.{customer_id}` - Thông báo cho khách hàng

### 3.2 Event System
**Các Events chính:**
```php
// Events/Order/NewOrderReceived.php
class NewOrderReceived implements ShouldBroadcastNow {
    public function broadcastOn() {
        return [
            new PrivateChannel('branch.' . $this->order->branch_id),
            new PrivateChannel('admin.orders')
        ];
    }
}

// Events/Order/OrderStatusUpdated.php
class OrderStatusUpdated implements ShouldBroadcastNow {
    public function broadcastOn() {
        $channels = [
            new PrivateChannel('order.' . $this->order->id)
        ];
        
        if ($this->order->customer_id) {
            $channels[] = new PrivateChannel('customer.' . $this->order->customer_id);
        }
        
        if ($this->order->driver_id) {
            $channels[] = new PrivateChannel('driver.' . $this->order->driver_id);
        }
        
        return $channels;
    }
}
```

---

## 4. HỆ THỐNG CHAT VÀ HỖ TRỢ

### 4.1 Chat Customer - Branch
**Luồng tạo cuộc trò chuyện:**
1. Customer tạo conversation với branch
2. Branch manager nhận thông báo
3. Real-time messaging qua WebSocket
4. Lưu trữ tin nhắn trong database

**Logic chat:**
```php
// Customer\ChatController
public function createConversation(Request $request) {
    $conversation = Conversation::create([
        'customer_id' => auth()->id(),
        'branch_id' => $request->branch_id,
        'status' => 'new',
        'subject' => $request->subject
    ]);
    
    return response()->json(['conversation_id' => $conversation->id]);
}

public function sendMessage(Request $request) {
    $message = ChatMessage::create([
        'conversation_id' => $request->conversation_id,
        'sender_id' => auth()->id(),
        'sender_type' => 'customer',
        'message' => $request->message,
        'status' => 'sent'
    ]);
    
    broadcast(new MessageSent($message));
}
```

---

## 5. HỆ THỐNG THANH TOÁN

### 5.1 Các phương thức thanh toán
1. **COD (Cash on Delivery)** - Thanh toán khi nhận hàng
2. **VNPay** - Thanh toán online
3. **Balance** - Thanh toán bằng số dư tài khoản

### 5.2 Luồng thanh toán VNPay
```php
// Customer\CheckoutController
public function processVNPayPayment($order) {
    $vnpay = new VNPayService();
    
    $paymentUrl = $vnpay->createPaymentUrl([
        'vnp_Amount' => $order->total_amount * 100,
        'vnp_OrderInfo' => 'Thanh toán đơn hàng ' . $order->order_code,
        'vnp_TxnRef' => $order->order_code,
        'vnp_ReturnUrl' => route('customer.checkout.vnpay.return')
    ]);
    
    return redirect($paymentUrl);
}

public function vnpayReturn(Request $request) {
    $vnpay = new VNPayService();
    
    if ($vnpay->validateSignature($request->all())) {
        $order = Order::where('order_code', $request->vnp_TxnRef)->first();
        
        if ($request->vnp_ResponseCode == '00') {
            $order->update(['status' => 'awaiting_confirmation']);
            $order->payment->update(['payment_status' => 'completed']);
            
            event(new NewOrderReceived($order));
        } else {
            $order->update(['status' => 'payment_failed']);
        }
    }
}
```

---

## 6. CÂU HỎI NÂNG CAO CHO DỰ ÁN

### 6.1 Câu hỏi về Kiến trúc và Thiết kế

**Q1: Tại sao sử dụng Event-Driven Architecture trong hệ thống này?**
- **Trả lời**: Event-Driven giúp tách biệt các module, dễ mở rộng và bảo trì. Khi có đơn hàng mới, system có thể đồng thời:
  - Gửi thông báo đến branch
  - Gửi email xác nhận đến customer  
  - Cập nhật analytics
  - Tìm tài xế
  - Không cần coupling giữa các module

**Q2: Làm thế nào để đảm bảo tính nhất quán dữ liệu khi có nhiều tài xế cùng nhận một đơn hàng?**
- **Trả lời**: Sử dụng Database Transaction và Optimistic Locking:
```php
DB::transaction(function() use ($order, $driverId) {
    $updated = Order::where('id', $order->id)
                   ->where('driver_id', null)
                   ->where('status', 'awaiting_driver')
                   ->update([
                       'driver_id' => $driverId,
                       'status' => 'driver_assigned'
                   ]);
    
    if (!$updated) {
        throw new Exception('Đơn hàng đã được nhận bởi tài xế khác');
    }
});
```

**Q3: Xử lý như thế nào khi hệ thống có lượng đơn hàng lớn (10,000+ đơn/ngày)?**
- **Trả lời**: 
  - Sử dụng Queue Jobs cho các tác vụ nặng
  - Database indexing cho các truy vấn thường xuyên
  - Redis cache cho session và frequently accessed data
  - Database sharding theo region/branch
  - CDN cho static assets

### 6.2 Câu hỏi về Business Logic

**Q4: Thuật toán tìm tài xế gần nhất hoạt động như thế nào?**
- **Trả lời**: Sử dụng Haversine Formula:
```sql
SELECT *, (
    6371 * acos(
        cos(radians(?)) * cos(radians(latitude)) *
        cos(radians(longitude) - radians(?)) +
        sin(radians(?)) * sin(radians(latitude))
    )
) AS distance
FROM drivers
WHERE status = 'active' AND is_available = true
HAVING distance <= 10
ORDER BY distance, rating DESC
```

**Q5: Xử lý trường hợp tài xế hủy đơn hàng sau khi đã nhận?**
- **Trả lời**:
  - Tăng `cancellation_count` của tài xế
  - Giảm `reliability_score`
  - Nếu hủy > 3 lần/ngày → tạm khóa tài khoản
  - Tự động tìm tài xế khác
  - Gửi thông báo đến customer về delay

**Q6: Làm sao tính toán delivery fee chính xác?**
- **Trả lời**:
```php
public function calculateDeliveryFee($branchId, $customerAddress) {
    $branch = Branch::find($branchId);
    $distance = $this->calculateDistance(
        $branch->latitude, $branch->longitude,
        $customerAddress->latitude, $customerAddress->longitude
    );
    
    $baseFee = 15000; // 15k cho 3km đầu
    $extraFee = max(0, ($distance - 3) * 5000); // 5k/km cho quãng đường thêm
    
    return $baseFee + $extraFee;
}
```

### 6.3 Câu hỏi về Security

**Q7: Làm thế nào để bảo mật API endpoints?**
- **Trả lời**:
  - JWT Authentication cho mobile apps
  - CSRF Protection cho web forms
  - Rate Limiting để chống DDoS
  - Input validation và sanitization
  - SQL Injection prevention với Eloquent ORM

**Q8: Xử lý như thế nào khi có người dùng cố gắng truy cập đơn hàng không phải của họ?**
- **Trả lời**:
```php
// Middleware: CheckOrderOwnership
public function handle($request, Closure $next) {
    $order = Order::find($request->route('id'));
    
    if (auth()->guard('customer')->check()) {
        if ($order->customer_id !== auth()->id()) {
            abort(403, 'Unauthorized access');
        }
    } elseif (auth()->guard('driver')->check()) {
        if ($order->driver_id !== auth()->guard('driver')->id()) {
            abort(403, 'Unauthorized access');
        }
    }
    
    return $next($request);
}
```

### 6.4 Câu hỏi về Performance

**Q9: Tối ưu hóa truy vấn database như thế nào?**
- **Trả lời**:
```php
// BAD: N+1 Query Problem
$orders = Order::all();
foreach ($orders as $order) {
    echo $order->customer->name; // N queries
}

// GOOD: Eager Loading
$orders = Order::with(['customer', 'orderItems.product'])->get();

// Index cho các trường thường xuyên query
Schema::table('orders', function (Blueprint $table) {
    $table->index(['status', 'created_at']);
    $table->index(['customer_id', 'status']);
    $table->index(['branch_id', 'status']);
});
```

**Q10: Caching strategy cho hệ thống này?**
- **Trả lời**:
```php
// Cache products by category
$products = Cache::remember('products.category.' . $categoryId, 3600, function() use ($categoryId) {
    return Product::where('category_id', $categoryId)
                  ->where('status', 'selling')
                  ->with('variants', 'images')
                  ->get();
});

// Cache branch operating hours
$isOpen = Cache::remember('branch.hours.' . $branchId, 1800, function() use ($branchId) {
    return $this->checkBranchOperatingHours($branchId);
});
```

### 6.5 Câu hỏi về Scalability

**Q11: Làm thế nào để scale hệ thống khi mở rộng ra nhiều thành phố?**
- **Trả lời**:
  - Database sharding theo region
  - Microservices architecture (Order Service, User Service, Payment Service)
  - Load balancer cho multiple app servers
  - Separate queue workers cho từng region
  - CDN cho static content

**Q12: Xử lý real-time notifications cho hàng nghìn users đồng thời?**
- **Trả lời**:
  - Sử dụng Redis cho WebSocket session management
  - Horizontal scaling cho WebSocket servers
  - Message queuing với Redis/RabbitMQ
  - Database connection pooling

### 6.6 Câu hỏi về Error Handling

**Q13: Xử lý lỗi khi payment gateway không phản hồi?**
- **Trả lời**:
```php
try {
    $response = $paymentGateway->processPayment($paymentData);
} catch (PaymentTimeoutException $e) {
    // Đánh dấu payment pending, retry sau
    $order->payment->update(['status' => 'processing']);
    dispatch(new RetryPaymentJob($order))->delay(now()->addMinutes(5));
} catch (PaymentFailedException $e) {
    $order->update(['status' => 'payment_failed']);
    // Gửi thông báo đến customer
}
```

**Q14: Backup và disaster recovery strategy?**
- **Trả lời**:
  - Daily automated database backups
  - Real-time database replication
  - File storage backup (images, documents)
  - Application code versioning với Git
  - Infrastructure as Code với Docker/Kubernetes

---

## 7. KẾT LUẬN

Hệ thống DevFoods được thiết kế với kiến trúc modular, sử dụng các design patterns và best practices của Laravel. Các luồng hoạt động được tối ưu hóa để đảm bảo trải nghiệm người dùng tốt nhất và khả năng mở rộng cao.

**Điểm mạnh của hệ thống:**
- Event-driven architecture dễ mở rộng
- Real-time notifications nâng cao UX
- Security được ưu tiên từ đầu
- Database design tối ưu cho performance
- Comprehensive error handling

**Hướng phát triển tương lai:**
- AI/ML cho recommendation system
- Mobile apps với React Native/Flutter
- Microservices architecture
- Advanced analytics và reporting
- Integration với third-party services (Google Maps, Firebase, etc.)

Tài liệu này cung cấp cái nhìn toàn diện về hệ thống DevFoods, giúp bạn chuẩn bị tốt cho việc bảo vệ dự án tốt nghiệp. Chúc bạn thành công!