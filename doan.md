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
**Luồng nghiệp vụ:**
- Kiểm tra trùng email/phone, gửi OTP, xác thực OTP, tạo tài khoản, tự động đăng nhập, ghi log đăng ký thành công/thất bại.
- Nếu OTP sai quá 5 lần, khóa chức năng đăng ký trong 10 phút.
- Nếu đăng ký thành công, gửi email chào mừng và tạo session user.
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
**Luồng nghiệp vụ:**
- Lưu hồ sơ ứng tuyển, chờ admin duyệt, gửi thông báo trạng thái đơn ứng tuyển.
- Nếu bị từ chối, gửi lý do qua email, cho phép nộp lại sau 7 ngày.
- Nếu được duyệt, tạo tài khoản driver, yêu cầu đổi mật khẩu lần đầu đăng nhập.
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
**Luồng nghiệp vụ:**
- Kiểm tra tồn kho realtime từng chi nhánh khi thêm vào giỏ hàng.
- Nếu sản phẩm hết hàng hoặc ngừng bán, không cho thêm vào cart.
- Nếu khách chưa đăng nhập, lưu cart tạm thời bằng session/localStorage, đồng bộ lên server khi đăng nhập.
- Hỗ trợ thêm sản phẩm vào wish list (yêu thích):
    - Nếu đã đăng nhập, lưu vào bảng `favorites`.
    - Nếu chưa đăng nhập, lưu tạm trên client, đồng bộ khi đăng nhập.
- Cho phép bình luận sản phẩm:
    - Chỉ khách đã mua mới được bình luận (check qua order_items).
    - Bình luận được duyệt tự động nếu không chứa từ khóa cấm, hoặc chờ admin duyệt nếu nghi ngờ spam.
    - Mỗi bình luận có thể like/dislike, admin có thể ẩn/xóa bình luận vi phạm.
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
**Luồng nghiệp vụ:**
- Kiểm tra lại tồn kho lần cuối trước khi tạo order.
- Nếu thanh toán online, tạo payment record với trạng thái `pending`, chỉ cập nhật order sang `awaiting_confirmation` khi nhận callback thành công từ VNPay.
- Nếu thanh toán thất bại, rollback toàn bộ order và gửi thông báo lỗi.
- Ghi log lịch sử giao dịch cho từng bước.
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

### 2.3 LUỒNG XỬ LÝ ĐƠN HÀNG (BRANCH MANAGER)
**Luồng nghiệp vụ:**
- Khi nhận đơn mới, kiểm tra trạng thái hoạt động của chi nhánh (open/closed), nếu closed thì tự động hủy đơn.
- Khi xác nhận đơn, trừ tồn kho, ghi nhận lịch sử thay đổi tồn kho.
- Nếu từ chối đơn, cập nhật trạng thái order, hoàn lại tồn kho nếu đã trừ.
- Khi tìm tài xế, ghi nhận log từng lần phân công/thất bại, ưu tiên tài xế có reliability cao.

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
**Luồng nghiệp vụ:**
- Khi tài xế nhận đơn, cập nhật trạng thái tài xế sang `delivering`, không cho nhận đơn mới.
- Nếu tài xế hủy đơn, tăng số lần hủy/ngày, nếu vượt ngưỡng thì khóa tài khoản tạm thời.
- Khi giao hàng thành công, cộng thưởng doanh thu cho tài xế, ghi nhận lịch sử thu nhập.
- Nếu khách hàng xác nhận nhận hàng, cập nhật trạng thái order và gửi đánh giá tài xế.

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
**Luồng nghiệp vụ:**
- Khi admin duyệt tài xế, kiểm tra hồ sơ, lịch sử ứng tuyển, blacklist.
- Khi quản lý sản phẩm, kiểm tra trạng thái tồn kho toàn hệ thống, cảnh báo sản phẩm sắp hết hàng.
- Khi thống kê, cho phép lọc theo nhiều tiêu chí (thời gian, chi nhánh, trạng thái đơn, doanh thu).

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

### 2.6 LUỒNG NGHIỆP VỤ BÌNH LUẬN SẢN PHẨM
**Luồng hoạt động:**
1. Khách hàng đã mua sản phẩm truy cập trang chi tiết sản phẩm.
2. Nhập nội dung bình luận, gửi lên server.
3. Server kiểm tra quyền bình luận, lọc nội dung, lưu vào bảng `reviews` với trạng thái `pending` hoặc `approved`.
4. Nếu bình luận hợp lệ, hiển thị ngay; nếu nghi ngờ spam, chờ admin duyệt.
5. Admin có thể duyệt, ẩn, hoặc xóa bình luận.
**Luồng nghiệp vụ:**
- Mỗi khách chỉ được bình luận 1 lần/sản phẩm, có thể chỉnh sửa hoặc xóa bình luận của mình.
- Bình luận có thể nhận like/dislike, hệ thống tự động ẩn bình luận bị dislike quá nhiều.
- Ghi log lịch sử chỉnh sửa/xóa bình luận.

### 2.7 LUỒNG NGHIỆP VỤ WISH LIST (YÊU THÍCH)
**Luồng hoạt động:**
1. Khách hàng nhấn nút "Yêu thích" trên sản phẩm.
2. Nếu đã đăng nhập, lưu vào bảng `favorites` (user_id, product_id).
3. Nếu chưa đăng nhập, lưu tạm trên client, đồng bộ khi đăng nhập.
4. Trang "Sản phẩm yêu thích" truy vấn danh sách từ bảng `favorites`.
**Luồng nghiệp vụ:**
- Mỗi user chỉ được yêu thích 1 lần/sản phẩm.
- Khi bỏ yêu thích, xóa record khỏi bảng `favorites`.
- Hỗ trợ đồng bộ wish list giữa nhiều thiết bị khi đăng nhập.

### 2.8 LUỒNG NGHIỆP VỤ ĐĂNG NHẬP TÀI XẾ
**Luồng hoạt động:**
1. Tài xế truy cập trang đăng nhập, nhập email/password.
2. Server xác thực thông tin, kiểm tra trạng thái tài khoản (active/locked/pending).
3. Nếu đúng, tạo session và chuyển sang dashboard tài xế.
4. Nếu sai, tăng số lần đăng nhập sai, nếu vượt ngưỡng thì khóa tài khoản tạm thời.
**Luồng nghiệp vụ:**
- Khi đăng nhập thành công, cập nhật trạng thái online, ghi log thời gian đăng nhập.
- Nếu tài xế bị khóa, gửi email hướng dẫn mở khóa.
- Hỗ trợ xác thực 2 lớp (OTP) nếu phát hiện đăng nhập bất thường.

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

## 7. HỆ THỐNG BÌNH LUẬN VÀ BÁO CÁO VI PHẠM

### 7.1 LUỒNG BÌNH LUẬN SẢN PHẨM

#### 7.1.1 Quy trình tạo bình luận
**Luồng nghiệp vụ:**
- Chỉ khách hàng đã mua sản phẩm/combo tại chi nhánh cụ thể mới được bình luận.
- Kiểm tra trạng thái đơn hàng: `delivered` hoặc `item_received`.
- Mỗi khách hàng chỉ được bình luận 1 lần cho mỗi sản phẩm tại mỗi chi nhánh.
- Bình luận có thể kèm theo hình ảnh (tối đa 2MB).
- Hệ thống tự động kiểm tra nội dung có từ ngữ cấm hay không.

**Bước 1: Kiểm tra quyền bình luận**
```php
// ProductController@review
public function review(Request $request, $id) {
    $user = $request->user();
    $type = $request->input('type'); // 'product' hoặc 'combo'
    $branchId = $request->input('branch_id');
    
    // Kiểm tra đã mua sản phẩm tại chi nhánh này chưa
    if ($type === 'product') {
        $order = Order::where('customer_id', $user->id)
            ->whereIn('status', ['delivered', 'item_received'])
            ->where('branch_id', $branchId)
            ->whereHas('orderItems.productVariant', function($q) use ($id) {
                $q->where('product_id', $id);
            })
            ->first();
    } else {
        $order = Order::where('customer_id', $user->id)
            ->whereIn('status', ['delivered', 'item_received'])
            ->where('branch_id', $branchId)
            ->whereHas('orderItems', function($q) use ($id) {
                $q->where('combo_id', $id);
            })
            ->first();
    }
    
    if (!$order) {
        return response()->json([
            'message' => 'Bạn chỉ có thể đánh giá sản phẩm đã mua tại chi nhánh này!'
        ], 403);
    }
}
```

**Bước 2: Validate và lưu bình luận**
```php
$request->validate([
    'rating' => 'required|integer|min:1|max:5',
    'review' => ['nullable', 'string', 'max:2000', new ForbiddenWords],
    'review_image' => 'nullable|image|max:2048',
    'branch_id' => 'required|exists:branches,id'
]);

$review = new ProductReview();
$review->user_id = $user->id;
$review->order_id = $order->id;
$review->branch_id = $branchId;
$review->rating = $request->input('rating');
$review->review = $request->input('review');
$review->is_verified_purchase = true;

if ($request->hasFile('review_image')) {
    $path = $request->file('review_image')->store('reviews', 's3');
    $review->review_image = $path;
}

$review->save();
```

#### 7.1.2 Hiển thị và tương tác bình luận
**Các tính năng:**
- Hiển thị bình luận theo thời gian (mới nhất trước)
- Hệ thống "Helpful" (hữu ích) cho bình luận
- Trả lời bình luận từ Branch Manager
- Real-time updates qua WebSocket

**Logic đánh dấu "Helpful":**
```php
// ProductController@markHelpful
public function markHelpful($id) {
    $user = auth()->user();
    $review = ProductReview::findOrFail($id);
    
    // Kiểm tra đã đánh dấu helpful chưa
    $existing = ReviewHelpful::where('user_id', $user->id)
                            ->where('review_id', $review->id)
                            ->first();
    
    if ($existing) {
        return response()->json([
            'success' => false,
            'message' => 'Bạn đã đánh dấu hữu ích rồi!'
        ]);
    }
    
    ReviewHelpful::create([
        'user_id' => $user->id,
        'review_id' => $review->id
    ]);
    
    $review->increment('helpful_count');
    
    return response()->json([
        'success' => true,
        'helpful_count' => $review->helpful_count
    ]);
}
```

### 7.2 HỆ THỐNG BÁO CÁO VI PHẠM

#### 7.2.1 Quy trình báo cáo bình luận vi phạm
**Luồng nghiệp vụ:**
- Chỉ khách hàng đã mua sản phẩm mới có quyền báo cáo bình luận.
- Mỗi user chỉ được báo cáo 1 lần cho mỗi bình luận.
- Hệ thống phân loại báo cáo: spam, inappropriate, fake, offensive, other.
- Khi có ≥5 báo cáo, Branch Manager có thể xóa bình luận.

**Bước 1: Tạo báo cáo vi phạm**
```php
// ProductController@reportReview
public function reportReview(Request $request, $id) {
    $user = $request->user();
    $review = ProductReview::findOrFail($id);
    
    // Kiểm tra quyền báo cáo (đã mua sản phẩm)
    $hasPurchased = false;
    if ($review->product_id) {
        $hasPurchased = Order::where('customer_id', $user->id)
            ->whereIn('status', ['delivered', 'item_received'])
            ->whereHas('orderItems.productVariant', function($q) use ($review) {
                $q->where('product_id', $review->product_id);
            })
            ->exists();
    } elseif ($review->combo_id) {
        $hasPurchased = Order::where('customer_id', $user->id)
            ->whereIn('status', ['delivered', 'item_received'])
            ->whereHas('orderItems', function($q) use ($review) {
                $q->where('combo_id', $review->combo_id);
            })
            ->exists();
    }
    
    if (!$hasPurchased) {
        return response()->json([
            'success' => false,
            'message' => 'Bạn chỉ có thể báo cáo đánh giá của sản phẩm đã mua!'
        ], 403);
    }
    
    // Kiểm tra đã báo cáo chưa
    $existingReport = ReviewReport::where('user_id', $user->id)
                                 ->where('review_id', $review->id)
                                 ->first();
    
    if ($existingReport) {
        return response()->json([
            'success' => false,
            'message' => 'Bạn đã báo cáo bình luận này rồi!'
        ], 409);
    }
    
    // Tạo báo cáo
    ReviewReport::create([
        'user_id' => $user->id,
        'review_id' => $review->id,
        'reason_type' => $request->reason_type,
        'reason_detail' => $request->reason_detail
    ]);
    
    // Cập nhật số lượng báo cáo
    $review->increment('report_count');
    
    // Broadcast real-time update
    broadcast(new ReviewReportUpdated($review));
}
```

#### 7.2.2 Quản lý báo cáo vi phạm (Branch Manager)
**Luồng xử lý:**
- Branch Manager xem danh sách báo cáo vi phạm của chi nhánh.
- Xem chi tiết từng báo cáo và nội dung bình luận bị báo cáo.
- Chỉ được xóa bình luận khi có ≥5 báo cáo.
- Ghi log mọi hành động xóa bình luận.

**Logic xử lý báo cáo:**
```php
// Branch\ReviewController@reports
public function reports(Request $request) {
    $branchId = auth('manager')->user()->branch->id;
    
    $reports = ReviewReport::with(['review.user', 'review.product', 'review.combo'])
        ->whereHas('review', function($q) use ($branchId) {
            $q->where('branch_id', $branchId);
        })
        ->when($request->input('reason_type'), fn($q) => $q->where('reason_type', $request->reason_type))
        ->orderByDesc('created_at')
        ->paginate(20);
        
    return view('branch.reviews.reports', compact('reports'));
}

// Xóa bình luận vi phạm
public function deleteReview($reviewId) {
    $branchId = auth('manager')->user()->branch->id;
    
    $review = ProductReview::with('reports')
        ->where('branch_id', $branchId)
        ->findOrFail($reviewId);
    
    // Kiểm tra số lượng báo cáo
    if ($review->reports->count() < 5) {
        return response()->json([
            'success' => false,
            'message' => 'Cần ít nhất 5 báo cáo để có thể xóa bình luận này!'
        ], 400);
    }
    
    // Ghi log trước khi xóa
    Log::info('Branch Manager deleted review', [
        'review_id' => $review->id,
        'branch_id' => $branchId,
        'manager_id' => auth('manager')->id(),
        'report_count' => $review->reports->count()
    ]);
    
    // Observer sẽ tự động trigger event khi delete
    $review->delete();
    
    return response()->json([
        'success' => true,
        'message' => 'Đã xóa bình luận vi phạm thành công!'
    ]);
}
```

### 7.3 HỆ THỐNG TRẢ LỜI BÌNH LUẬN

#### 7.3.1 Branch Manager trả lời bình luận
**Luồng nghiệp vụ:**
- Branch Manager có thể trả lời bất kỳ bình luận nào tại chi nhánh của mình.
- Mỗi bình luận có thể có nhiều phản hồi.
- Phản hồi được đánh dấu `is_official = true` để phân biệt với reply của customer.
- Real-time notification đến customer khi có phản hồi mới.

**Logic trả lời bình luận:**
```php
// Branch\ReviewController@reply
public function reply(Request $request, $reviewId) {
    $branchId = auth('manager')->user()->branch->id;
    
    $review = ProductReview::where('branch_id', $branchId)
                          ->findOrFail($reviewId);
    
    $request->validate([
        'reply' => 'required|string|max:2000'
    ]);
    
    $reply = ReviewReply::create([
        'review_id' => $review->id,
        'user_id' => auth('manager')->id(),
        'reply' => $request->reply,
        'is_official' => true,
        'reply_date' => now()
    ]);
    
    // Gửi thông báo đến customer
    if ($review->user) {
        $review->user->notify(new ReviewReplyNotification($reply));
    }
    
    // Broadcast real-time update
    broadcast(new ReviewReplyCreated($reply));
    
    return response()->json([
        'success' => true,
        'message' => 'Phản hồi thành công!',
        'reply' => $reply
    ]);
}
```

### 7.4 REAL-TIME UPDATES VÀ NOTIFICATIONS

#### 7.4.1 WebSocket Events cho hệ thống bình luận
**Các events chính:**
```php
// Events/Customer/ReviewCreated.php
class ReviewCreated implements ShouldBroadcastNow {
    public function broadcastOn() {
        return [
            new Channel('product-reviews.' . $this->review->product_id),
            new PrivateChannel('branch.' . $this->review->branch_id)
        ];
    }
}

// Events/Branch/ReviewReplyCreated.php
class ReviewReplyCreated implements ShouldBroadcastNow {
    public function broadcastOn() {
        return [
            new Channel('review-replies'),
            new PrivateChannel('customer.' . $this->reply->review->user_id)
        ];
    }
}

// Events/ReviewReportUpdated.php
class ReviewReportUpdated implements ShouldBroadcastNow {
    public function broadcastOn() {
        return [
            new Channel('review-reports'),
            new PrivateChannel('branch.' . $this->review->branch_id)
        ];
    }
}
```

#### 7.4.2 JavaScript xử lý real-time
**Frontend JavaScript:**
```javascript
// Lắng nghe review mới
const reviewChannel = pusher.subscribe('product-reviews.' + productId);
reviewChannel.bind('review-created', function(data) {
    // Thêm review mới vào danh sách
    const reviewHtml = renderReviewHtml(data.review);
    document.getElementById('reviews-container').prepend(reviewHtml);
    
    // Cập nhật số lượng review
    updateReviewCount(data.review_count);
});

// Lắng nghe reply mới
const replyChannel = pusher.subscribe('review-replies');
replyChannel.bind('review-reply-created', function(data) {
    const replyHtml = renderReplyHtml(data.reply);
    document.getElementById('replies-' + data.reply.review_id).append(replyHtml);
});

// Lắng nghe cập nhật báo cáo (cho Branch Manager)
const reportChannel = pusher.subscribe('review-reports');
reportChannel.bind('review-report-updated', function(data) {
    updateReportCount(data.review_id, data.report_count);
    
    // Hiển thị nút xóa nếu đủ 5 báo cáo
    if (data.report_count >= 5) {
        showDeleteButton(data.review_id);
    }
});
```

### 7.5 BẢO MẬT VÀ VALIDATION

#### 7.5.1 Kiểm tra nội dung bình luận
**Rule ForbiddenWords:**
```php
// Rules/ForbiddenWords.php
class ForbiddenWords implements Rule {
    private $forbiddenWords = [
        'spam', 'fake', 'scam', 'cheat', 'hack',
        // Thêm các từ khóa cấm khác
    ];
    
    public function passes($attribute, $value) {
        $content = strtolower($value);
        
        foreach ($this->forbiddenWords as $word) {
            if (strpos($content, $word) !== false) {
                return false;
            }
        }
        
        return true;
    }
    
    public function message() {
        return 'Nội dung chứa từ ngữ không phù hợp.';
    }
}
```

#### 7.5.2 Rate Limiting
**Middleware cho bình luận:**
```php
// Middleware/ReviewRateLimit.php
class ReviewRateLimit {
    public function handle($request, Closure $next) {
        $userId = auth()->id();
        $key = 'review_limit:' . $userId;
        
        // Giới hạn 5 bình luận/giờ
        if (Cache::get($key, 0) >= 5) {
            return response()->json([
                'message' => 'Bạn đã đạt giới hạn bình luận. Vui lòng thử lại sau.'
            ], 429);
        }
        
        Cache::increment($key, 1);
        Cache::expire($key, 3600); // 1 giờ
        
        return $next($request);
    }
}
```

### 7.6 ANALYTICS VÀ REPORTING

#### 7.6.1 Thống kê bình luận
**Dashboard cho Branch Manager:**
```php
// Branch\DashboardController
public function reviewAnalytics() {
    $branchId = auth('manager')->user()->branch->id;
    
    $stats = [
        'total_reviews' => ProductReview::where('branch_id', $branchId)->count(),
        'average_rating' => ProductReview::where('branch_id', $branchId)->avg('rating'),
        'reviews_this_month' => ProductReview::where('branch_id', $branchId)
                                           ->whereMonth('created_at', now()->month)
                                           ->count(),
        'pending_reports' => ReviewReport::whereHas('review', function($q) use ($branchId) {
                                $q->where('branch_id', $branchId);
                            })->count(),
        'rating_distribution' => ProductReview::where('branch_id', $branchId)
                                            ->selectRaw('rating, COUNT(*) as count')
                                            ->groupBy('rating')
                                            ->pluck('count', 'rating')
    ];
    
    return view('branch.analytics.reviews', compact('stats'));
}
```

#### 7.6.2 Báo cáo vi phạm cho Admin
**Admin có thể xem tổng quan báo cáo vi phạm:**
```php
// Admin\ReviewController
public function violationReports() {
    $reports = ReviewReport::with(['review.branch', 'review.user', 'user'])
        ->selectRaw('reason_type, COUNT(*) as count')
        ->groupBy('reason_type')
        ->get();
    
    $topViolatedBranches = Branch::withCount(['reviews as violation_count' => function($q) {
            $q->whereHas('reports');
        }])
        ->orderByDesc('violation_count')
        ->limit(10)
        ->get();
    
    return view('admin.reports.violations', compact('reports', 'topViolatedBranches'));
}
```

---

## 8. KẾT LUẬN

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

---

## 9. CÂU HỎI THƯỜNG GẶP KHI BẢO VỆ ĐỒ ÁN

### 9.1 CÂU HỎI VỀ KIẾN TRÚC HỆ THỐNG

**Q1: Tại sao em chọn Laravel framework cho dự án này thay vì các framework khác như Django hay Spring Boot?**

**Trả lời:**
- **Ecosystem phong phú**: Laravel có Eloquent ORM mạnh mẽ, Blade templating engine, và hệ thống middleware hoàn chỉnh
- **Real-time capabilities**: Tích hợp sẵn Broadcasting với Pusher/WebSocket cho tính năng real-time
- **Security built-in**: CSRF protection, SQL injection prevention, XSS protection
- **Rapid development**: Artisan CLI, Migration system, Seeding giúp phát triển nhanh
- **Community support**: Documentation đầy đủ, community lớn, nhiều packages

**Q2: Em có thể giải thích về Event-Driven Architecture trong hệ thống không?**

**Trả lời:**
```php
// Ví dụ: Khi có đơn hàng mới
event(new NewOrderReceived($order));

// Các Listeners tự động xử lý:
- SendOrderNotificationToCustomer
- SendOrderNotificationToBranch  
- UpdateInventoryStock
- FindDriverForOrder
- LogOrderActivity
```

**Lợi ích:**
- **Decoupling**: Các module không phụ thuộc trực tiếp vào nhau
- **Scalability**: Dễ thêm tính năng mới mà không ảnh hưởng code cũ
- **Maintainability**: Code dễ bảo trì và debug
- **Testability**: Có thể test từng event/listener riêng biệt

**Q3: Làm thế nào để đảm bảo tính nhất quán dữ liệu khi có nhiều request đồng thời?**

**Trả lời:**
```php
// 1. Database Transactions
DB::transaction(function() use ($order, $driverId) {
    $updated = Order::where('id', $order->id)
                   ->where('driver_id', null)
                   ->update(['driver_id' => $driverId]);
    
    if (!$updated) {
        throw new Exception('Order already taken');
    }
});

// 2. Optimistic Locking
$order = Order::where('id', $orderId)
             ->where('version', $currentVersion)
             ->first();

if (!$order) {
    throw new ConcurrencyException();
}

// 3. Redis Locks
$lock = Cache::lock('order:' . $orderId, 10);
if ($lock->get()) {
    // Process order
    $lock->release();
}
```

### 9.2 CÂU HỎI VỀ BUSINESS LOGIC

**Q4: Thuật toán tìm tài xế gần nhất hoạt động như thế nào?**

**Trả lời:**
```sql
-- Sử dụng Haversine Formula
SELECT *, (
    6371 * acos(
        cos(radians(?)) * cos(radians(latitude)) *
        cos(radians(longitude) - radians(?)) +
        sin(radians(?)) * sin(radians(latitude))
    )
) AS distance
FROM drivers
WHERE status = 'active' 
  AND is_available = true
  AND reliability_score >= 80
HAVING distance <= 10
ORDER BY distance ASC, reliability_score DESC
LIMIT 5
```

**Quy trình:**
1. Tính khoảng cách từ chi nhánh đến tất cả tài xế available
2. Lọc tài xế trong bán kính 10km
3. Sắp xếp theo khoảng cách và độ tin cậy
4. Gửi thông báo đến top 5 tài xế
5. Tài xế đầu tiên accept sẽ được assign

**Q5: Xử lý trường hợp thanh toán thất bại như thế nào?**

**Trả lời:**
```php
public function handlePaymentCallback(Request $request) {
    $order = Order::where('order_code', $request->vnp_TxnRef)->first();
    
    if ($request->vnp_ResponseCode == '00') {
        // Thanh toán thành công
        DB::transaction(function() use ($order) {
            $order->update(['status' => 'awaiting_confirmation']);
            $order->payment->update(['status' => 'completed']);
            event(new NewOrderReceived($order));
        });
    } else {
        // Thanh toán thất bại
        DB::transaction(function() use ($order) {
            $order->update(['status' => 'payment_failed']);
            $order->payment->update(['status' => 'failed']);
            
            // Hoàn lại stock nếu đã trừ
            $this->restoreInventory($order);
            
            // Gửi thông báo lỗi
            $order->customer->notify(new PaymentFailedNotification($order));
        });
    }
}
```

**Q6: Làm sao tính delivery fee chính xác?**

**Trả lời:**
```php
public function calculateDeliveryFee($branchId, $customerAddress) {
    $branch = Branch::find($branchId);
    
    // Tính khoảng cách
    $distance = $this->calculateDistance(
        $branch->latitude, $branch->longitude,
        $customerAddress->latitude, $customerAddress->longitude
    );
    
    // Phí cơ bản: 15k cho 3km đầu
    $baseFee = 15000;
    
    // Phí thêm: 5k/km cho quãng đường vượt quá 3km
    $extraDistance = max(0, $distance - 3);
    $extraFee = $extraDistance * 5000;
    
    // Phí thời gian cao điểm (17h-19h): +20%
    $currentHour = now()->hour;
    $rushHourMultiplier = ($currentHour >= 17 && $currentHour <= 19) ? 1.2 : 1;
    
    return ($baseFee + $extraFee) * $rushHourMultiplier;
}
```

### 9.3 CÂU HỎI VỀ SECURITY

**Q7: Em đã implement những biện pháp bảo mật nào?**

**Trả lời:**

**1. Authentication & Authorization:**
```php
// Multi-guard authentication
'guards' => [
    'web' => ['driver' => 'session', 'provider' => 'users'],
    'manager' => ['driver' => 'session', 'provider' => 'managers'],
    'driver' => ['driver' => 'session', 'provider' => 'drivers'],
    'api' => ['driver' => 'sanctum', 'provider' => 'users']
]

// Middleware kiểm tra quyền
class CheckOrderOwnership {
    public function handle($request, Closure $next) {
        $order = Order::find($request->route('id'));
        
        if (auth()->guard('customer')->check()) {
            if ($order->customer_id !== auth()->id()) {
                abort(403);
            }
        }
        
        return $next($request);
    }
}
```

**2. Input Validation & Sanitization:**
```php
// Custom validation rules
class ForbiddenWords implements Rule {
    public function passes($attribute, $value) {
        $forbiddenWords = ['spam', 'fake', 'scam'];
        $content = strtolower($value);
        
        foreach ($forbiddenWords as $word) {
            if (strpos($content, $word) !== false) {
                return false;
            }
        }
        return true;
    }
}

// Rate limiting
class ReviewRateLimit {
    public function handle($request, Closure $next) {
        $key = 'review_limit:' . auth()->id();
        
        if (Cache::get($key, 0) >= 5) {
            return response()->json(['error' => 'Rate limit exceeded'], 429);
        }
        
        Cache::increment($key, 1);
        Cache::expire($key, 3600);
        
        return $next($request);
    }
}
```

**3. Data Protection:**
```php
// Encryption cho sensitive data
protected $casts = [
    'phone' => 'encrypted',
    'address' => 'encrypted'
];

// HTTPS enforcement
class ForceHttps {
    public function handle($request, Closure $next) {
        if (!$request->secure() && app()->environment('production')) {
            return redirect()->secure($request->getRequestUri());
        }
        return $next($request);
    }
}
```

**Q8: Làm thế nào để prevent SQL Injection và XSS?**

**Trả lời:**

**SQL Injection Prevention:**
```php
// ✅ GOOD: Sử dụng Eloquent ORM
$users = User::where('email', $email)->get();

// ✅ GOOD: Parameter binding
$users = DB::select('SELECT * FROM users WHERE email = ?', [$email]);

// ❌ BAD: Raw query
$users = DB::select("SELECT * FROM users WHERE email = '$email'");
```

**XSS Prevention:**
```php
// Blade template tự động escape
{{ $user->name }} // Tự động escape
{!! $user->name !!} // Raw output (cẩn thận)

// Custom sanitization
class SanitizeInput {
    public function handle($request, Closure $next) {
        $input = $request->all();
        
        array_walk_recursive($input, function(&$value) {
            if (is_string($value)) {
                $value = strip_tags($value);
                $value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
            }
        });
        
        $request->merge($input);
        return $next($request);
    }
}
```

### 9.4 CÂU HỎI VỀ PERFORMANCE

**Q9: Em đã tối ưu performance như thế nào?**

**Trả lời:**

**1. Database Optimization:**
```php
// Eager Loading để tránh N+1 Query
$orders = Order::with([
    'customer', 
    'orderItems.product', 
    'branch',
    'driver'
])->get();

// Database Indexing
Schema::table('orders', function (Blueprint $table) {
    $table->index(['status', 'created_at']);
    $table->index(['customer_id', 'status']);
    $table->index(['branch_id', 'status']);
    $table->index(['driver_id', 'status']);
});

// Query optimization
$orders = Order::select(['id', 'order_code', 'status', 'total_amount'])
              ->where('status', 'delivered')
              ->whereDate('created_at', today())
              ->limit(100)
              ->get();
```

**2. Caching Strategy:**
```php
// Cache frequently accessed data
$categories = Cache::remember('categories.active', 3600, function() {
    return Category::where('status', true)
                  ->with('products')
                  ->get();
});

// Cache user sessions
$user = Cache::remember('user.' . $userId, 1800, function() use ($userId) {
    return User::with('addresses', 'favorites')->find($userId);
});

// Cache API responses
class CacheApiResponse {
    public function handle($request, Closure $next) {
        $key = 'api:' . md5($request->fullUrl());
        
        if (Cache::has($key)) {
            return response()->json(Cache::get($key));
        }
        
        $response = $next($request);
        
        if ($response->status() === 200) {
            Cache::put($key, $response->getData(), 300);
        }
        
        return $response;
    }
}
```

**3. Frontend Optimization:**
```javascript
// Lazy loading cho images
const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            const img = entry.target;
            img.src = img.dataset.src;
            observer.unobserve(img);
        }
    });
});

// Debounce cho search
let searchTimeout;
function handleSearch(query) {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        performSearch(query);
    }, 300);
}

// Virtual scrolling cho long lists
function renderVirtualList(items, containerHeight, itemHeight) {
    const visibleCount = Math.ceil(containerHeight / itemHeight);
    const startIndex = Math.floor(scrollTop / itemHeight);
    const endIndex = Math.min(startIndex + visibleCount, items.length);
    
    return items.slice(startIndex, endIndex);
}
```

**Q10: Làm thế nào để handle high traffic?**

**Trả lời:**

**1. Horizontal Scaling:**
```yaml
# Docker Compose cho multiple app instances
version: '3.8'
services:
  app1:
    image: devfoods:latest
    environment:
      - APP_ENV=production
  app2:
    image: devfoods:latest
    environment:
      - APP_ENV=production
  
  nginx:
    image: nginx:alpine
    volumes:
      - ./nginx.conf:/etc/nginx/nginx.conf
    depends_on:
      - app1
      - app2
```

**2. Queue System:**
```php
// Background job processing
class ProcessOrderJob implements ShouldQueue {
    public function handle() {
        // Heavy processing logic
        $this->sendNotifications();
        $this->updateInventory();
        $this->generateInvoice();
    }
}

// Dispatch job
dispatch(new ProcessOrderJob($order))->delay(now()->addSeconds(30));
```

**3. CDN & Asset Optimization:**
```php
// Asset versioning
mix.js('resources/js/app.js', 'public/js')
   .css('resources/css/app.css', 'public/css')
   .version();

// Image optimization
class OptimizeImages {
    public function handle($image) {
        $optimized = Image::make($image)
                         ->resize(800, 600, function($constraint) {
                             $constraint->aspectRatio();
                             $constraint->upsize();
                         })
                         ->encode('webp', 80);
        
        return $optimized;
    }
}
```

### 9.5 CÂU HỎI VỀ TESTING

**Q11: Em có viết test cho dự án không? Có thể demo một số test case?**

**Trả lời:**

**1. Unit Tests:**
```php
// tests/Unit/OrderCalculationTest.php
class OrderCalculationTest extends TestCase {
    public function test_calculate_delivery_fee() {
        $branch = Branch::factory()->create([
            'latitude' => 21.0285,
            'longitude' => 105.8542
        ]);
        
        $address = Address::factory()->create([
            'latitude' => 21.0245,
            'longitude' => 105.8412
        ]);
        
        $calculator = new DeliveryFeeCalculator();
        $fee = $calculator->calculate($branch->id, $address);
        
        $this->assertEquals(15000, $fee); // 3km = base fee
    }
    
    public function test_order_total_calculation() {
        $order = Order::factory()->create();
        $order->orderItems()->create([
            'product_variant_id' => 1,
            'quantity' => 2,
            'price' => 50000
        ]);
        
        $this->assertEquals(100000, $order->subtotal);
    }
}
```

**2. Feature Tests:**
```php
// tests/Feature/OrderFlowTest.php
class OrderFlowTest extends TestCase {
    public function test_customer_can_place_order() {
        $customer = User::factory()->create();
        $product = Product::factory()->create();
        
        $response = $this->actingAs($customer)
                        ->post('/checkout/process', [
                            'items' => [[
                                'product_id' => $product->id,
                                'quantity' => 1
                            ]],
                            'address_id' => 1,
                            'payment_method' => 'cod'
                        ]);
        
        $response->assertStatus(200);
        $this->assertDatabaseHas('orders', [
            'customer_id' => $customer->id,
            'status' => 'awaiting_confirmation'
        ]);
    }
}
```

**3. API Tests:**
```php
// tests/Feature/ApiTest.php
class ApiTest extends TestCase {
    public function test_api_authentication() {
        $user = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;
        
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json'
        ])->get('/api/user');
        
        $response->assertStatus(200)
                ->assertJson([
                    'id' => $user->id,
                    'email' => $user->email
                ]);
    }
}
```

### 9.6 CÂU HỎI CHUYÊN SÂU VỀ LUỒNG NGHIỆP VỤ

**Hội đồng thường tập trung vào luồng nghiệp vụ vì đây là phần thể hiện:**
- Hiểu biết sâu về domain business
- Khả năng phân tích và thiết kế hệ thống
- Xử lý các edge cases và exception handling
- Tư duy logic và problem-solving skills

#### 9.6.1 Luồng đặt hàng chi tiết

**Q12: Mô tả chi tiết luồng đặt hàng từ khi customer chọn sản phẩm đến khi nhận hàng?**

**Trả lời:**

**Phase 1: Pre-Order (Chuẩn bị đặt hàng)**
```php
// 1. Customer browse products
$products = Product::with(['variants', 'images', 'reviews'])
    ->where('branch_id', $selectedBranchId)
    ->where('status', 'selling')
    ->get();

// 2. Check stock availability real-time
foreach ($products as $product) {
    $product->has_stock = $product->variants->contains(function($variant) use ($selectedBranchId) {
        return $variant->branchStocks
            ->where('branch_id', $selectedBranchId)
            ->where('stock_quantity', '>', 0)
            ->count() > 0;
    });
}

// 3. Add to cart with validation
public function addToCart(Request $request) {
    // Validate stock before adding
    $stock = BranchStock::where('branch_id', $request->branch_id)
                       ->where('variant_id', $request->variant_id)
                       ->first();
    
    if (!$stock || $stock->stock_quantity < $request->quantity) {
        throw new InsufficientStockException();
    }
    
    // Add to cart (session-based for guests, DB for authenticated users)
    CartItem::create([
        'cart_id' => $this->getOrCreateCart()->id,
        'product_variant_id' => $request->variant_id,
        'quantity' => $request->quantity,
        'price' => $variant->calculatePrice(), // Include toppings
        'toppings' => json_encode($request->toppings ?? [])
    ]);
}
```

**Phase 2: Checkout Process**
```php
public function processCheckout(Request $request) {
    DB::beginTransaction();
    try {
        // 1. Final stock validation
        $cart = $this->getCurrentCart();
        foreach ($cart->items as $item) {
            $this->validateStockAvailability($item);
        }
        
        // 2. Calculate pricing
        $subtotal = $cart->calculateSubtotal();
        $deliveryFee = $this->calculateDeliveryFee($request->address_id, $request->branch_id);
        $discount = $this->applyDiscountCodes($request->discount_codes, $subtotal);
        $total = $subtotal + $deliveryFee - $discount;
        
        // 3. Create order
        $order = Order::create([
            'customer_id' => auth()->id(),
            'branch_id' => $request->branch_id,
            'address_id' => $request->address_id,
            'order_code' => $this->generateUniqueOrderCode(),
            'subtotal' => $subtotal,
            'delivery_fee' => $deliveryFee,
            'discount_amount' => $discount,
            'total_amount' => $total,
            'payment_method' => $request->payment_method,
            'status' => 'pending_payment',
            'estimated_delivery_time' => $this->calculateEstimatedDeliveryTime()
        ]);
        
        // 4. Create order items
        foreach ($cart->items as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_variant_id' => $item->product_variant_id,
                'quantity' => $item->quantity,
                'unit_price' => $item->price,
                'total_price' => $item->price * $item->quantity,
                'toppings' => $item->toppings,
                'special_instructions' => $item->special_instructions
            ]);
        }
        
        // 5. Process payment
        if ($request->payment_method === 'vnpay') {
            $paymentUrl = $this->processVNPayPayment($order);
            DB::commit();
            return redirect($paymentUrl);
        } else {
            // COD payment
            $order->update(['status' => 'awaiting_confirmation']);
            event(new NewOrderReceived($order));
            DB::commit();
        }
        
    } catch (Exception $e) {
        DB::rollback();
        throw $e;
    }
}
```

**Phase 3: Order Processing**
```php
// Branch Manager confirms order
public function confirmOrder($orderId) {
    $order = Order::with('orderItems.productVariant')->findOrFail($orderId);
    
    // Double-check stock availability
    foreach ($order->orderItems as $item) {
        $stock = BranchStock::where('branch_id', $order->branch_id)
                           ->where('variant_id', $item->product_variant_id)
                           ->lockForUpdate() // Prevent race conditions
                           ->first();
        
        if ($stock->stock_quantity < $item->quantity) {
            throw new InsufficientStockException("Product {$item->productVariant->product->name} is out of stock");
        }
    }
    
    // Reserve stock
    foreach ($order->orderItems as $item) {
        BranchStock::where('branch_id', $order->branch_id)
                   ->where('variant_id', $item->product_variant_id)
                   ->decrement('stock_quantity', $item->quantity);
        
        // Log stock movement
        StockMovement::create([
            'branch_id' => $order->branch_id,
            'variant_id' => $item->product_variant_id,
            'type' => 'sale',
            'quantity' => -$item->quantity,
            'reference_type' => 'order',
            'reference_id' => $order->id
        ]);
    }
    
    $order->update([
        'status' => 'confirmed',
        'confirmed_at' => now(),
        'confirmed_by' => auth('manager')->id()
    ]);
    
    // Find driver
    dispatch(new FindDriverForOrderJob($order));
    
    event(new OrderConfirmed($order));
}
```

**Q13: Xử lý trường hợp nào khi có nhiều tài xế cùng accept một đơn hàng?**

**Trả lời:**
```php
// Job: FindDriverForOrderJob
public function handle() {
    $nearbyDrivers = $this->findNearbyDrivers($this->order);
    
    foreach ($nearbyDrivers as $driver) {
        // Send notification to driver
        $driver->notify(new NewOrderAvailable($this->order, 120)); // 2 minutes timeout
        
        // Wait for response with timeout
        $response = $this->waitForDriverResponse($driver, $this->order, 120);
        
        if ($response === 'accepted') {
            // Use database transaction with optimistic locking
            try {
                DB::transaction(function() use ($driver) {
                    $updated = Order::where('id', $this->order->id)
                                   ->where('driver_id', null) // Ensure no driver assigned yet
                                   ->where('status', 'confirmed')
                                   ->update([
                                       'driver_id' => $driver->id,
                                       'status' => 'driver_assigned',
                                       'driver_assigned_at' => now()
                                   ]);
                    
                    if (!$updated) {
                        throw new OrderAlreadyAssignedException();
                    }
                    
                    // Update driver status
                    $driver->update(['is_available' => false]);
                });
                
                // Notify other drivers that order is taken
                $this->notifyOtherDrivers($nearbyDrivers, $driver, 'order_taken');
                
                event(new DriverAssigned($this->order, $driver));
                return;
                
            } catch (OrderAlreadyAssignedException $e) {
                // Order already taken by another driver
                $driver->notify(new OrderNoLongerAvailable($this->order));
                continue;
            }
        }
    }
    
    // No driver found
    $this->order->update(['status' => 'awaiting_driver']);
    
    // Retry after 5 minutes
    dispatch(new FindDriverForOrderJob($this->order))->delay(now()->addMinutes(5));
}
```

**Q14: Làm thế nào để handle trường hợp customer hủy đơn ở các giai đoạn khác nhau?**

**Trả lời:**
```php
public function cancelOrder(Request $request, $orderId) {
    $order = Order::with('orderItems')->findOrFail($orderId);
    $reason = $request->input('reason');
    
    // Check if order can be cancelled
    if (!$this->canBeCancelled($order)) {
        throw new OrderCannotBeCancelledException(
            "Order cannot be cancelled at status: {$order->status}"
        );
    }
    
    DB::transaction(function() use ($order, $reason) {
        switch ($order->status) {
            case 'pending_payment':
            case 'awaiting_confirmation':
                // No stock reserved yet, simple cancellation
                $this->processCancellation($order, $reason, 'customer');
                break;
                
            case 'confirmed':
            case 'awaiting_driver':
                // Stock already reserved, need to restore
                $this->restoreStock($order);
                $this->processCancellation($order, $reason, 'customer');
                break;
                
            case 'driver_assigned':
            case 'driver_confirmed':
                // Driver already assigned, need to notify and compensate
                $this->restoreStock($order);
                $this->notifyDriverOfCancellation($order);
                $this->compensateDriver($order); // Small compensation for inconvenience
                $this->processCancellation($order, $reason, 'customer');
                break;
                
            case 'driver_picked_up':
            case 'in_transit':
                // Cannot cancel once driver picked up
                throw new OrderCannotBeCancelledException(
                    'Order cannot be cancelled after driver pickup'
                );
                
            default:
                throw new OrderCannotBeCancelledException(
                    "Invalid order status for cancellation: {$order->status}"
                );
        }
    });
    
    event(new OrderCancelled($order, 'customer', $reason));
}

private function restoreStock($order) {
    foreach ($order->orderItems as $item) {
        BranchStock::where('branch_id', $order->branch_id)
                   ->where('variant_id', $item->product_variant_id)
                   ->increment('stock_quantity', $item->quantity);
        
        // Log stock restoration
        StockMovement::create([
            'branch_id' => $order->branch_id,
            'variant_id' => $item->product_variant_id,
            'type' => 'return',
            'quantity' => $item->quantity,
            'reference_type' => 'order_cancellation',
            'reference_id' => $order->id
        ]);
    }
}

private function canBeCancelled($order) {
    $cancellableStatuses = [
        'pending_payment',
        'awaiting_confirmation', 
        'confirmed',
        'awaiting_driver',
        'driver_assigned',
        'driver_confirmed'
    ];
    
    return in_array($order->status, $cancellableStatuses);
}
```

#### 9.6.2 Luồng quản lý tồn kho

**Q15: Hệ thống quản lý tồn kho hoạt động như thế nào? Xử lý concurrency như thế nào?**

**Trả lời:**

**Real-time Stock Management:**
```php
class StockManager {
    public function updateStock($branchId, $variantId, $quantity, $type, $reference = null) {
        DB::transaction(function() use ($branchId, $variantId, $quantity, $type, $reference) {
            // Lock row to prevent race conditions
            $stock = BranchStock::where('branch_id', $branchId)
                               ->where('variant_id', $variantId)
                               ->lockForUpdate()
                               ->first();
            
            if (!$stock) {
                throw new StockNotFoundException();
            }
            
            $oldQuantity = $stock->stock_quantity;
            
            switch ($type) {
                case 'sale':
                    if ($stock->stock_quantity < $quantity) {
                        throw new InsufficientStockException();
                    }
                    $stock->decrement('stock_quantity', $quantity);
                    break;
                    
                case 'restock':
                    $stock->increment('stock_quantity', $quantity);
                    break;
                    
                case 'adjustment':
                    $stock->update(['stock_quantity' => $quantity]);
                    break;
                    
                case 'return':
                    $stock->increment('stock_quantity', $quantity);
                    break;
            }
            
            // Log stock movement
            StockMovement::create([
                'branch_id' => $branchId,
                'variant_id' => $variantId,
                'type' => $type,
                'quantity' => $type === 'sale' ? -$quantity : $quantity,
                'old_quantity' => $oldQuantity,
                'new_quantity' => $stock->fresh()->stock_quantity,
                'reference_type' => $reference['type'] ?? null,
                'reference_id' => $reference['id'] ?? null,
                'created_by' => auth()->id(),
                'notes' => $reference['notes'] ?? null
            ]);
            
            // Check for low stock alerts
            $this->checkLowStockAlert($stock);
            
            // Broadcast real-time update
            broadcast(new StockUpdated($stock));
        });
    }
    
    private function checkLowStockAlert($stock) {
        $variant = $stock->productVariant;
        $lowStockThreshold = $variant->low_stock_threshold ?? 10;
        
        if ($stock->stock_quantity <= $lowStockThreshold && $stock->stock_quantity > 0) {
            // Send alert to branch manager
            $branch = $stock->branch;
            $branch->notify(new LowStockAlert($stock, $variant));
            
            // Log alert
            StockAlert::create([
                'branch_id' => $stock->branch_id,
                'variant_id' => $stock->variant_id,
                'type' => 'low_stock',
                'current_quantity' => $stock->stock_quantity,
                'threshold' => $lowStockThreshold,
                'status' => 'active'
            ]);
        } elseif ($stock->stock_quantity === 0) {
            // Out of stock alert
            $branch = $stock->branch;
            $branch->notify(new OutOfStockAlert($stock, $variant));
            
            StockAlert::create([
                'branch_id' => $stock->branch_id,
                'variant_id' => $stock->variant_id,
                'type' => 'out_of_stock',
                'current_quantity' => 0,
                'status' => 'active'
            ]);
        }
    }
}
```

**Inventory Forecasting:**
```php
class InventoryForecaster {
    public function predictStockNeeds($branchId, $days = 7) {
        // Analyze historical sales data
        $salesData = OrderItem::whereHas('order', function($q) use ($branchId) {
                $q->where('branch_id', $branchId)
                  ->where('status', 'delivered')
                  ->whereBetween('created_at', [now()->subDays(30), now()]);
            })
            ->selectRaw('product_variant_id, AVG(quantity) as avg_daily_sales')
            ->groupBy('product_variant_id')
            ->get();
        
        $predictions = [];
        
        foreach ($salesData as $data) {
            $currentStock = BranchStock::where('branch_id', $branchId)
                                     ->where('variant_id', $data->product_variant_id)
                                     ->value('stock_quantity');
            
            $predictedUsage = $data->avg_daily_sales * $days;
            $recommendedRestock = max(0, $predictedUsage - $currentStock + 20); // 20 units buffer
            
            if ($recommendedRestock > 0) {
                $predictions[] = [
                    'variant_id' => $data->product_variant_id,
                    'current_stock' => $currentStock,
                    'predicted_usage' => $predictedUsage,
                    'recommended_restock' => $recommendedRestock,
                    'priority' => $currentStock <= $data->avg_daily_sales * 2 ? 'high' : 'medium'
                ];
            }
        }
        
        return collect($predictions)->sortByDesc('priority');
    }
}
```

### 9.7 CHIẾN LƯỢC PHÒNG TRÁNH KHI BỊ HỎI DỒN DẬP

#### 9.6.1 Chuẩn bị trước khi bảo vệ

**1. Tạo mindmap tổng quan:**
```
DevFoods System
├── Architecture
│   ├── Laravel Framework
│   ├── Event-Driven Design
│   ├── Multi-Guard Auth
│   └── Real-time Features
├── Core Features
│   ├── Order Management
│   ├── Driver Assignment
│   ├── Payment Processing
│   └── Review System
├── Technical Highlights
│   ├── Performance Optimization
│   ├── Security Measures
│   ├── Testing Strategy
│   └── Scalability Design
└── Business Value
    ├── User Experience
    ├── Operational Efficiency
    └── Revenue Growth
```

**2. Chuẩn bị câu trả lời ngắn gọn (30 giây/câu):**
- **Elevator Pitch**: "DevFoods là hệ thống đặt đồ ăn real-time với 4 actors, sử dụng Laravel và Event-Driven Architecture, tối ưu cho performance và security."
- **Key Features**: "Real-time tracking, intelligent driver matching, secure payment, comprehensive review system."
- **Technical Stack**: "Laravel, MySQL, Redis, Pusher WebSocket, VNPay integration."

#### 9.6.2 Kỹ thuật trả lời khi bị hỏi dồn dập

**1. Kỹ thuật "Bridge" (Cầu nối):**
```
Hội đồng: "Tại sao không dùng microservices?"
Trả lời: "Đây là câu hỏi rất hay về architecture. Với quy mô hiện tại của DevFoods, monolithic Laravel cho phép rapid development và easier maintenance. Tuy nhiên, em đã thiết kế system với event-driven architecture để dễ dàng migrate sang microservices khi scale up. Ví dụ như Order Service, User Service có thể tách riêng..."
```

**2. Kỹ thuật "Acknowledge + Redirect":**
```
Hội đồng: "Security của em có vấn đề gì không?"
Trả lời: "Em hiểu thầy/cô quan tâm về security - đây là priority số 1 của em. Em đã implement multi-layer security: authentication với Laravel Sanctum, authorization với custom middleware, input validation với custom rules, rate limiting, và HTTPS enforcement. Cụ thể..."
```

**3. Kỹ thuật "Specific Example":**
```
Hội đồng: "Performance có tốt không?"
Trả lời: "Em có metrics cụ thể: API response time < 200ms, page load < 2s, database queries optimized với eager loading giảm từ N+1 xuống 3-4 queries. Em cũng implement caching strategy với Redis, ví dụ cache categories trong 1 giờ, user sessions trong 30 phút..."
```

**4. Kỹ thuật "Future Vision":**
```
Hội đồng: "Có thể scale không?"
Trả lời: "Absolutely! Em đã design với scalability in mind. Hiện tại có thể handle 1000+ concurrent users. Để scale lên 10k users, em sẽ implement horizontal scaling với load balancer, database sharding, và microservices architecture. Em cũng đã research về Kubernetes deployment..."
```

#### 9.6.3 Xử lý các tình huống khó

**1. Khi không biết câu trả lời:**
```
"Đây là một câu hỏi rất thú vị mà em chưa research sâu. Tuy nhiên, dựa trên hiểu biết hiện tại, em nghĩ có thể approach bằng cách... Em sẽ research thêm về vấn đề này sau khi bảo vệ."
```

**2. Khi bị hỏi về công nghệ chưa dùng:**
```
"Em chưa implement GraphQL trong project này vì REST API đáp ứng đủ requirements. Tuy nhiên, em đã research và thấy GraphQL có advantages như flexible queries và reduced over-fetching. Nếu có cơ hội, em muốn apply vào version 2.0."
```

**3. Khi bị challenge về design decisions:**
```
"Em hiểu concern của thầy/cô. Lý do em chọn approach này là vì [lý do 1], [lý do 2]. Trade-off là [nhược điểm], nhưng em đã mitigate bằng [giải pháp]. Alternative approach có thể là [phương án khác], nhưng sẽ có [trade-off khác]."
```

#### 9.6.4 Body Language và Presentation Skills

**1. Giữ bình tĩnh:**
- Thở sâu trước khi trả lời
- Maintain eye contact
- Speak slowly và clearly
- Use hand gestures appropriately

**2. Active listening:**
- Nod để show understanding
- "Vâng, em hiểu ý thầy/cô là..."
- Clarify nếu không hiểu: "Thầy/cô có thể elaborate thêm về..."

**3. Confidence building:**
- Prepare demo scenarios
- Practice với friends/family
- Record yourself presenting
- Know your code inside out

#### 9.6.5 Emergency Backup Plans

**1. Khi demo bị lỗi:**
```
"Em có backup screenshots và video demo. Lỗi này có thể do network/environment issue. Em có thể explain flow thông qua code và architecture diagram."
```

**2. Khi bị hỏi quá sâu về một topic:**
```
"Đây là deep technical question. Em có thể explain high-level approach và show code implementation. Nếu thầy/cô muốn discuss chi tiết hơn, em rất sẵn lòng schedule follow-up session."
```

**3. Khi time pressure:**
```
"Em hiểu time constraint. Let me quickly highlight key points: [point 1], [point 2], [point 3]. Em có detailed documentation nếu thầy/cô muốn review thêm."
```

---

Tài liệu này cung cấp cái nhìn toàn diện về hệ thống DevFoods, bao gồm các câu hỏi thường gặp và chiến lược bảo vệ đồ án hiệu quả. Với sự chuẩn bị kỹ lưỡng này, bạn sẽ tự tin trả lời mọi câu hỏi của hội đồng. Chúc bạn bảo vệ thành công!