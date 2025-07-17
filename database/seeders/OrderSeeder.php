<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderItemTopping;
use App\Models\OrderStatusHistory;
use App\Models\OrderCancellation;
use App\Models\Payment;
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
use Illuminate\Support\Facades\DB;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Xóa dữ liệu cũ để tránh duplicate
        echo "Cleaning old data...\n";
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        OrderItemTopping::truncate();
        OrderItem::truncate();
        OrderCancellation::truncate();
        OrderStatusHistory::truncate();
        Order::truncate();
        Payment::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
        
        // Tạo Payments
        $this->createPayments();
        
        // Tạo Orders và các bảng liên quan
        $this->createOrders();
    }

    private function createPayments()
    {
        $user = User::find(6);
        if (!$user) {
            $user = User::create([
                'user_name' => 'customer',
                'full_name' => 'Nguyen Kha Banh',
                'email' => 'khabanh@devfoods.com',
                'phone' => '0123456789',
                'password' => bcrypt('customer'),
                'balance' => 0,
                'total_spending' => 0,
                'total_orders' => 0,
                'active' => true,
            ]);
        } else {
            // Cập nhật số điện thoại nếu user chưa có
            if (!$user->phone) {
                $user->update(['phone' => '0123456789']);
            }
        }

        // Không cần check nữa vì đã truncate ở trên

        echo "Creating payments...\n";

        // Tạo 10 payments với tỷ lệ hợp lý
        for ($i = 0; $i < 10; $i++) {
            $amount = rand(50000, 500000);
            
            // Tăng tỷ lệ completed payments (60% completed, 20% pending, 10% failed, 10% refunded)
            $statusWeights = [20, 60, 10, 10]; // pending, completed, failed, refunded
            $statuses = ['pending', 'completed', 'failed', 'refunded'];
            $status = $this->getRandomStatus($statuses, $statusWeights);
            
            $timestamp = microtime(true) * 1000;
            $random = mt_rand(1000, 9999);
            $txnRef = 'TXN' . (int)$timestamp . $random;
            $paymentMethodEnum = ['cod', 'vnpay', 'balance'];
            $paymentMethod = $paymentMethodEnum[array_rand($paymentMethodEnum)];
            
            Payment::create([
                'payment_method' => $paymentMethod,
                'payer_name' => $user->full_name,
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

        $completedPayments = Payment::where('payment_status', 'completed')->count();
        $totalPayments = Payment::count();
        echo "Created {$totalPayments} payments, {$completedPayments} completed.\n";
    }

    private function createOrders()
    {
        $branch = Branch::find(1);
        echo "Branch ID 1: " . ($branch ? "FOUND - " . $branch->name : "NOT FOUND") . "\n";
        if (!$branch) {
            throw new \Exception('Không tìm thấy branch id=1. Vui lòng chạy BranchSeeder trước.');
        }
        $user = User::find(6);
        echo "User ID 6: " . ($user ? "FOUND - " . $user->email : "NOT FOUND") . "\n";
        if (!$user) {
            throw new \Exception('Không tìm thấy user id=6. Vui lòng chạy UserSeeder trước.');
        }
        $driver = Driver::find(1);
        if (!$driver) {
            throw new \Exception('Không tìm thấy driver id=1. Vui lòng chạy DriverSeeder trước.');
        }
        $payments = Payment::where('payment_status', 'completed')->get();
        echo "Completed payments: " . $payments->count() . "\n";
        $discountCodes = DiscountCode::where('is_active', true)->get();
        $addresses = Address::all();
        echo "Total addresses: " . $addresses->count() . "\n";

        // Chuẩn bị bộ đếm cho từng chi nhánh
        $branchOrderCounters = [];

        if (!$branch || !$user || !$driver || $addresses->isEmpty()) {
            throw new \Exception('Thiếu dữ liệu test: branch, user, driver, address.');
        }

        // Lọc địa chỉ trong bán kính 10km từ chi nhánh
        $branchLat = $branch->latitude;
        $branchLng = $branch->longitude;
        $addresses = $addresses->filter(function($address) use ($branchLat, $branchLng) {
            return $this->calculateDistance($branchLat, $branchLng, $address->latitude, $address->longitude) <= 10;
        })->values();

        // Check if orders already exist to avoid duplicates

        // Các trạng thái mới cho enum
        $statuses = [
            'awaiting_confirmation',
            'delivered',
            'item_received',
            'cancelled',
            'refunded'
        ];
        $statusWeights = [25, 25, 20, 15, 15]; // Cập nhật lại tỷ lệ cho phù hợp

        echo "Creating 50 orders...\n";

        // Lấy danh sách user, driver, address, payment thành mảng để random lặp lại nếu thiếu
        $userArr = [$user];
        $driverArr = [$driver];
        $paymentArr = $payments->all();
        $addressArr = $addresses->all();
        $discountCodeArr = $discountCodes->all();
        $userCount = 1;
        $driverCount = 1;
        $paymentCount = count($paymentArr);
        $addressCount = count($addressArr);
        $discountCodeCount = count($discountCodeArr);

        if ($addressCount === 0) {
            echo "Không có địa chỉ nào cho user id=1. Bỏ qua tạo order!\n";
            return;
        }

        if ($paymentCount === 0) {
            echo "Không có payment completed nào. Tạo order không có payment.\n";
        }

        // Tạo 50 đơn hàng mẫu
        for ($i = 1; $i <= 50; $i++) {
            $customer = $userArr[($i-1)%$userCount];
            $driver = $driverArr[0]; // Luôn lấy driver có id = 1
            $payment = $paymentCount > 0 ? $paymentArr[($i-1)%$paymentCount] : null;
            $address = $addressArr[($i-1)%$addressCount];
            $discountCode = $discountCodeCount > 0 ? $discountCodeArr[($i-1)%$discountCodeCount] : null;
            
            // Chọn trạng thái theo tỷ lệ
            $status = $this->getRandomStatus($statuses, $statusWeights);
            if (!$status || !in_array($status, $statuses)) {
                $status = 'awaiting_confirmation';
            }
            
            // Tạo thời gian đơn hàng
            $orderDate = Carbon::now()->subDays(rand(0, 30))->subHours(rand(0, 23))->subMinutes(rand(0, 59));
            $estimatedDelivery = $orderDate->copy()->addMinutes(rand(10, 40));
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
                'driver_id' => 1, // Gán tất cả đơn hàng cho tài xế id = 1
                'address_id' => $address->id,
                'discount_code_id' => $discountCode ? $discountCode->id : null,
                'payment_id' => $payment ? $payment->id : null,
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

            echo "Created order {$i}: {$order->order_code} - Payment ID: " . ($payment ? $payment->id : 'NULL') . "\n";

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

    // Thêm hàm tính khoảng cách Haversine
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // km
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat/2) * sin($dLat/2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon/2) * sin($dLon/2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        $distance = $earthRadius * $c;
        return $distance;
    }
}
