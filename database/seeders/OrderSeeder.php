<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderItemTopping;
use App\Models\OrderStatusHistory;
use App\Models\OrderCancellation;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\User;
use App\Models\Branch;
use App\Models\Driver;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Combo;
use App\Models\Topping;
use App\Models\Address;
use App\Models\DiscountCode;
use Carbon\Carbon;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Tạo Payment Methods trước
        $this->createPaymentMethods();
        
        // Tạo Payments
        $this->createPayments();
        
        // Tạo Orders và các bảng liên quan
        $this->createOrders();
    }

    private function createPaymentMethods()
    {
        // Check if payment methods already exist to avoid duplicates
        if (PaymentMethod::count() > 0) {
            return; // Skip if payment methods already exist
        }

        $paymentMethods = [
            ['name' => 'Tiền mặt', 'description' => 'Thanh toán bằng tiền mặt khi nhận hàng'],
            ['name' => 'MOMO', 'description' => 'Thanh toán qua ví điện tử MOMO'],
            ['name' => 'ZaloPay', 'description' => 'Thanh toán qua ví điện tử ZaloPay'],
            ['name' => 'VNPay', 'description' => 'Thanh toán qua cổng VNPay'],
            ['name' => 'Bank Transfer', 'description' => 'Chuyển khoản ngân hàng'],
        ];

        foreach ($paymentMethods as $method) {
            PaymentMethod::create($method);
        }
    }

    private function createPayments()
    {
        $paymentMethods = PaymentMethod::all();
        $users = User::all(); // Use all users instead of only customers

        // Check if payments already exist to avoid duplicates
        if (Payment::count() > 0) {
            return; // Skip if payments already exist
        }

        foreach ($users as $user) {
            // Tạo 1-3 payments cho mỗi user
            $paymentCount = rand(1, 3);
            
            for ($i = 0; $i < $paymentCount; $i++) {
                $amount = rand(50000, 500000);
                $status = ['pending', 'completed', 'failed', 'refunded'][rand(0, 3)];
                
                // Generate unique transaction reference
                $timestamp = microtime(true) * 1000; // Use microtime for more precision
                $random = mt_rand(1000, 9999);
                $txnRef = 'TXN' . (int)$timestamp . $random;
                
                Payment::create([
                    'payment_method_id' => $paymentMethods->random()->id,
                    'payer_name' => $user->name,
                    'payer_email' => $user->email,
                    'payer_phone' => $user->phone,
                    'txn_ref' => $txnRef,
                    'transaction_id' => $status === 'completed' ? 'TXN' . (int)$timestamp . mt_rand(10000, 99999) : null,
                    'response_code' => $status === 'completed' ? '00' : null,
                    'bank_code' => null,
                    'payment_amount' => $amount,
                    'payment_currency' => 'VND',
                    'payment_status' => $status,
                    'payment_date' => $status === 'completed' ? Carbon::now()->subDays(rand(1, 30)) : null,
                    'payment_method_detail' => null,
                    'gateway_response' => null,
                    'ip_address' => '127.0.0.1',
                    'callback_data' => null,
                ]);
            }
        }
    }

    private function createOrders()
    {
        $branches = Branch::all();
        $users = User::all(); // Use all users instead of only customers
        $drivers = Driver::all();
        $payments = Payment::where('payment_status', 'completed')->get();
        $discountCodes = DiscountCode::where('is_active', true)->get();
        $addresses = Address::all();

        // Chuẩn bị bộ đếm cho từng chi nhánh
        $branchOrderCounters = [];

        // Chỉ chọn 1 chi nhánh duy nhất
        $branch = Branch::first();
        if (!$branch) {
            throw new \Exception('No branches found. Please run BranchSeeder first.');
        }
        
        if ($users->isEmpty()) {
            throw new \Exception('No users found. Please run UserSeeder or FastFoodSeeder first.');
        }
        
        if ($drivers->isEmpty()) {
            throw new \Exception('No drivers found. Please run DriverSeeder first.');
        }
        
        if ($payments->isEmpty()) {
            throw new \Exception('No completed payments found. Please run PaymentSeeder first.');
        }
        
        if ($addresses->isEmpty()) {
            throw new \Exception('No addresses found. Please run AddressSeeder first.');
        }

        // Check if orders already exist to avoid duplicates

        // Các trạng thái mới cho enum
        $statuses = [
            'awaiting_confirmation',
            'awaiting_driver',
            'driver_picked_up',
            'in_transit',
            'delivered',
            'item_received',
            'cancelled',
            'refunded'
        ];
        $statusWeights = [15, 15, 10, 10, 20, 10, 10, 10]; // Tỷ lệ phân bố trạng thái

        echo "Creating 50 orders...\n";

        // Loại bỏ user admin khỏi danh sách user tạo order
        $users = $users->filter(function($user) {
            return $user->email !== 'admin@devfoods.com';
        })->values();
        // Lấy danh sách user, driver, address, payment thành mảng để random lặp lại nếu thiếu
        $userArr = $users->all();
        $driverArr = $drivers->all();
        $paymentArr = $payments->all();
        $addressArr = $addresses->all();
        $discountCodeArr = $discountCodes->all();
        $userCount = count($userArr);
        $driverCount = count($driverArr);
        $paymentCount = count($paymentArr);
        $addressCount = count($addressArr);
        $discountCodeCount = count($discountCodeArr);

        // Tạo 50 đơn hàng mẫu
        for ($i = 1; $i <= 50; $i++) {
            $customer = $userArr[($i-1)%$userCount];
            $driver = $driverArr[($i-1)%$driverCount];
            $payment = $paymentArr[($i-1)%$paymentCount];
            $address = $addressArr[($i-1)%$addressCount];
            $discountCode = $discountCodeCount > 0 ? $discountCodeArr[($i-1)%$discountCodeCount] : null;
            
            // Chọn trạng thái theo tỷ lệ
            $status = $this->getRandomStatus($statuses, $statusWeights);
            if (!$status || !in_array($status, $statuses)) {
                $status = 'awaiting_confirmation';
            }
            
            // Tạo thời gian đơn hàng
            $orderDate = Carbon::now()->subDays(rand(0, 30))->subHours(rand(0, 23))->subMinutes(rand(0, 59));
            $estimatedDelivery = $orderDate->copy()->addMinutes(rand(30, 90));
            $actualDelivery = null;
            
            // Nếu trạng thái là delivered, item_received thì mới có actualDelivery
            if (in_array($status, ['delivered', 'item_received'])) {
                $actualDelivery = $estimatedDelivery->copy()->addMinutes(rand(-15, 30));
            }

            // Tính toán giá trị đơn hàng
            $subtotal = rand(50000, 300000);
            $deliveryFee = rand(0, 20000);
            $discountAmount = $discountCode ? rand(0, $subtotal * 0.2) : 0;
            $taxAmount = ($subtotal - $discountAmount) * 0.1;
            $totalAmount = $subtotal + $deliveryFee - $discountAmount + $taxAmount;
            $pointsEarned = floor($totalAmount / 1000);

            // Xử lý order_code theo branch_code
            $branchCode = $branch->branch_code;
            if (!isset($branchOrderCounters[$branchCode])) {
                $branchOrderCounters[$branchCode] = 1;
            } else {
                $branchOrderCounters[$branchCode]++;
            }
            $orderCode = $branchCode . str_pad($branchOrderCounters[$branchCode], 4, '0', STR_PAD_LEFT);

            $order = Order::create([
                'order_code' => $orderCode,
                'customer_id' => $customer->id,
                'branch_id' => $branch->id,
                'driver_id' => $status === 'delivery' || $status === 'completed' ? $driver->id : null,
                'address_id' => $address->id,
                'discount_code_id' => $discountCode ? $discountCode->id : null,
                'payment_id' => $payment->id,
                'guest_name' => null, // Sử dụng customer thật
                'guest_phone' => null,
                'guest_email' => null,
                'guest_address' => null,
                'guest_ward' => null,
                'guest_district' => null,
                'guest_city' => null,
                'guest_latitude' => null,
                'guest_longitude' => null,
                'estimated_delivery_time' => $estimatedDelivery,
                'actual_delivery_time' => $actualDelivery,
                'delivery_fee' => $deliveryFee,
                'discount_amount' => $discountAmount,
                'tax_amount' => $taxAmount,
                'order_date' => $orderDate,
                'delivery_date' => $actualDelivery,
                'status' => $status,
                'points_earned' => $pointsEarned,
                'subtotal' => $subtotal,
                'total_amount' => $totalAmount,
                'delivery_address' => $address->full_address,
                'notes' => $this->getRandomNotes(),
            ]);

            echo "Created order {$i}: {$order->order_code}\n";

            // Tạo Order Items
            $this->createOrderItems($order);

            // Tạo Order Status History
            $this->createOrderStatusHistory($order, $status, $orderDate);

            // Tạo Order Cancellation nếu đơn hàng bị hủy
            if ($status === 'cancelled') {
                $this->createOrderCancellation($order, $orderDate);
            }
        }

        echo "Finished creating orders.\n";
    }

    private function createOrderItems(Order $order)
    {
        $products = Product::with('variants')->get();
        $combos = Combo::all();
        $toppings = Topping::all();

        // Check if required collections are not empty
        if ($products->isEmpty()) {
            throw new \Exception('No products found. Please run FastFoodSeeder first.');
        }
        
        if ($combos->isEmpty()) {
            throw new \Exception('No combos found. Please run FastFoodSeeder first.');
        }
        
        if ($toppings->isEmpty()) {
            throw new \Exception('No toppings found. Please run FastFoodSeeder first.');
        }

        // Tạo 1-4 items cho mỗi đơn hàng
        $itemCount = rand(1, 4);
        
        for ($i = 0; $i < $itemCount; $i++) {
            $isCombo = rand(0, 1) === 1 && $combos->count() > 0;
            
            if ($isCombo) {
                $combo = $combos->random();
                $unitPrice = $combo->price;
                $quantity = rand(1, 3);
                $totalPrice = $unitPrice * $quantity;

                $orderItem = OrderItem::create([
                    'order_id' => $order->id,
                    'product_variant_id' => null,
                    'combo_id' => $combo->id,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'total_price' => $totalPrice,
                ]);
            } else {
                $product = $products->random();
                $variant = $product->variants->random();
                $unitPrice = $variant->price;
                $quantity = rand(1, 3);
                $totalPrice = $unitPrice * $quantity;

                $orderItem = OrderItem::create([
                    'order_id' => $order->id,
                    'product_variant_id' => $variant->id,
                    'combo_id' => null,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'total_price' => $totalPrice,
                ]);

                // Thêm toppings cho product (50% khả năng)
                if (rand(0, 1) === 1 && $toppings->count() > 0) {
                    $toppingCount = rand(1, 3);
                    $selectedToppings = $toppings->random($toppingCount);
                    
                    foreach ($selectedToppings as $topping) {
                        OrderItemTopping::create([
                            'order_item_id' => $orderItem->id,
                            'topping_id' => $topping->id,
                            'quantity' => rand(1, 2),
                            'price' => $topping->price,
                        ]);
                    }
                }
            }
        }
    }

    private function createOrderStatusHistory(Order $order, string $finalStatus, Carbon $orderDate)
    {
        // Check if order status history already exists for this order to avoid duplicates
        if (OrderStatusHistory::where('order_id', $order->id)->count() > 0) {
            return; // Skip if order status history already exists for this order
        }

        $statuses = ['pending', 'processing', 'ready', 'delivery', 'completed'];
        $currentTime = $orderDate->copy();
        $changedBy = User::whereHas('roles', function($query) {
            $query->where('name', 'admin');
        })->first() ?? User::first();

        // Luôn có status đầu tiên là pending
        OrderStatusHistory::create([
            'order_id' => $order->id,
            'old_status' => null,
            'new_status' => 'pending',
            'changed_by' => $changedBy->id,
            'changed_by_role' => 'system',
            'note' => 'Đơn hàng được tạo',
            'changed_at' => $currentTime,
        ]);

        $currentTime->addMinutes(rand(1, 5));

        // Tạo lịch sử theo trạng thái cuối cùng
        foreach ($statuses as $status) {
            if ($status === $finalStatus) {
                break;
            }

            OrderStatusHistory::create([
                'order_id' => $order->id,
                'old_status' => $statuses[array_search($status, $statuses) - 1] ?? null,
                'new_status' => $status,
                'changed_by' => $changedBy->id,
                'changed_by_role' => 'branch_manager',
                'note' => $this->getStatusNote($status),
                'changed_at' => $currentTime,
            ]);

            $currentTime->addMinutes(rand(5, 30));
        }

        // Nếu đơn hàng bị hủy, thêm status cancelled
        if ($finalStatus === 'cancelled') {
            OrderStatusHistory::create([
                'order_id' => $order->id,
                'old_status' => 'pending',
                'new_status' => 'cancelled',
                'changed_by' => $changedBy->id,
                'changed_by_role' => 'branch_manager',
                'note' => 'Đơn hàng bị hủy',
                'changed_at' => $currentTime->addMinutes(rand(1, 10)),
            ]);
        }
    }

    private function createOrderCancellation(Order $order, Carbon $orderDate)
    {
        // Check if order cancellation already exists for this order to avoid duplicates
        if (OrderCancellation::where('order_id', $order->id)->exists()) {
            return; // Skip if order cancellation already exists for this order
        }

        $cancellationTypes = ['customer_cancel', 'driver_cancel', 'restaurant_cancel', 'system_cancel'];
        $cancellationStages = ['before_processing', 'processing', 'ready_for_delivery', 'during_delivery'];
        $reasons = [
            'Khách hàng thay đổi kế hoạch',
            'Không có tài xế khả dụng',
            'Hết nguyên liệu',
            'Lỗi hệ thống',
            'Khách hàng không trả lời điện thoại',
            'Địa chỉ giao hàng không chính xác',
            'Thời tiết xấu',
            'Khách hàng yêu cầu hủy',
        ];

        OrderCancellation::create([
            'order_id' => $order->id,
            'cancelled_by' => User::first()->id,
            'cancellation_type' => $cancellationTypes[array_rand($cancellationTypes)],
            'cancellation_date' => $orderDate->addMinutes(rand(5, 30)),
            'reason' => $reasons[array_rand($reasons)],
            'cancellation_stage' => $cancellationStages[array_rand($cancellationStages)],
            'penalty_applied' => rand(0, 1) === 1,
            'penalty_amount' => rand(0, 1) === 1 ? rand(10000, 50000) : 0,
            'points_deducted' => rand(0, 1) === 1 ? rand(10, 100) : 0,
            'evidence' => null,
            'notes' => rand(0, 1) === 1 ? 'Ghi chú bổ sung về việc hủy đơn hàng' : null,
        ]);
    }

    private function getRandomStatus(array $statuses, array $weights): string
    {
        $totalWeight = array_sum($weights);
        $random = rand(1, $totalWeight);
        $currentWeight = 0;

        foreach ($statuses as $index => $status) {
            $currentWeight += $weights[$index];
            if ($random <= $currentWeight) {
                return $status;
            }
        }

        return $statuses[0];
    }

    private function getRandomNotes(): ?string
    {
        $notes = [
            'Không hành, ít muối',
            'Giao trước 12h',
            'Gọi điện trước khi giao',
            'Không giao hàng vào giờ nghỉ trưa',
            'Để ở cổng, không cần gọi điện',
            'Giao hàng cẩn thận, món dễ vỡ',
            'Không cay, ít dầu',
            'Thêm tương ớt',
            'Giao hàng nhanh nhất có thể',
            'Không cần khay đựng',
            null, // 30% khả năng không có ghi chú
            null,
            null,
        ];

        return $notes[array_rand($notes)];
    }

    private function getStatusNote(string $status): string
    {
        $notes = [
            'awaiting_confirmation' => 'Đơn hàng chờ xác nhận',
            'awaiting_driver' => 'Đang tìm tài xế',
            'driver_picked_up' => 'Tài xế đã nhận đơn',
            'in_transit' => 'Đơn hàng đang được giao',
            'delivered' => 'Đã giao đến địa chỉ',
            'item_received' => 'Khách đã nhận hàng',
            'cancelled' => 'Đơn hàng đã bị hủy',
            'refunded' => 'Đơn hàng đã được hoàn tiền',
        ];

        return $notes[$status] ?? 'Cập nhật trạng thái đơn hàng';
    }
}
