<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\User;
use App\Models\Driver;
use Carbon\Carbon;

class DriverOrderSeeder extends Seeder
{
    /**
     * Chạy seeder cho database.
     */
    public function run(): void
    {
        // Vẫn tìm tài xế ID 1 và khách hàng ID 1 để làm mẫu
        $driver = Driver::find(1);
        $customer = User::find(1);

        if (!$driver || !$customer) {
            $this->command->error('Không tìm thấy Tài xế (ID=1) hoặc Khách hàng (ID=1). Vui lòng tạo trước.');
            return;
        }

        // --- Tạo các đơn hàng mẫu bằng Model::create() ---

        // Đơn 1: Đã giao, của tài xế 1
        Order::create([
            'customer_id' => $customer->id,
            'driver_id' => $driver->id,
            'branch_id' => 1,
            'delivery_address' => '123 Đường Pasteur, Quận 3, TP.HCM',
            'status' => 'delivered',
            'subtotal' => 180000,
            'delivery_fee' => 20000,
            'total_amount' => 200000,
            'driver_earning' => 16000, // 80% phí ship
            'order_date' => Carbon::now()->subDays(2),
            'delivery_date' => Carbon::now()->subDays(2)->addMinutes(28),
        ]);

        // Đơn 2: Đang giao, của tài xế 1
        Order::create([
            'customer_id' => $customer->id,
            'driver_id' => $driver->id,
            'branch_id' => 2,
            'delivery_address' => '456 Đường Lê Lợi, Quận 1, TP.HCM',
            'status' => 'delivering',
            'subtotal' => 250000,
            'delivery_fee' => 15000,
            'total_amount' => 265000,
            'driver_earning' => 12000,
            'order_date' => Carbon::now()->subHours(1),
        ]);

        // Đơn 2: Đang giao, của tài xế 1
        Order::create([
            'customer_id' => $customer->id,
            'driver_id' => $driver->id,
            'branch_id' => 2,
            'delivery_address' => '666 Đường Lê Lợi, Quận 1, TP.HCM',
            'status' => 'delivering',
            'subtotal' => 250000,
            'delivery_fee' => 15000,
            'total_amount' => 265000,
            'driver_earning' => 15000,
            'order_date' => Carbon::now()->subHours(1),
        ]);

        // Đơn 3: Đang chuẩn bị, của tài xế 1
        Order::create([
            'customer_id' => $customer->id,
            'driver_id' => $driver->id,
            'branch_id' => 1,
            'delivery_address' => '789 Đường Nguyễn Trãi, Quận 5, TP.HCM',
            'status' => 'processing',
            'subtotal' => 95000,
            'delivery_fee' => 18000,
            'total_amount' => 113000,
            'driver_earning' => 14400,
            'order_date' => Carbon::now(),
        ]);

        // Đơn 4: Mới, chưa có tài xế nhận
        Order::create([
            'customer_id' => $customer->id,
            'driver_id' => null,
            'branch_id' => 2,
            'delivery_address' => '101 Đường Trần Hưng Đạo, Quận 1, TP.HCM',
            'status' => 'pending',
            'subtotal' => 300000,
            'delivery_fee' => 25000,
            'total_amount' => 325000,
            'driver_earning' => 20000,
            'order_date' => Carbon::now(),
        ]);
    }
}