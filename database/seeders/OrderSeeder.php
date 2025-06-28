<?php
// File: database/seeders/OrderSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // !! THAY THẾ DỮ LIỆU GIẢ ĐỊNH Ở ĐÂY !!
        $customerId = 41; 
        $branchIds = [1, 2, 3]; 
        $driverIds = [1, 2]; 
        $productVariants = [
            ['id' => 1, 'price' => 89000],
            ['id' => 5, 'price' => 35000],
            ['id' => 10, 'price' => 122000],
            ['id' => 12, 'price' => 20000],
        ];

        // DANH SÁCH TRẠNG THÁI MỚI
        $orderStatuses = [
            'awaiting_confirmation', 'confirmed', 'awaiting_driver', 'driver_picked_up',
            'in_transit', 'delivered', 'item_received', 'cancelled'
        ];
        
        if (empty($branchIds) || empty($driverIds) || empty($productVariants)) {
            $this->command->info('Vui lòng cung cấp dữ liệu giả định trong OrderSeeder trước khi chạy.');
            return;
        }

        DB::table('orders')->where('customer_id', $customerId)->delete();

        for ($i = 0; $i < 15; $i++) {
            DB::transaction(function () use ($customerId, $branchIds, $driverIds, $productVariants, $orderStatuses, $i) {
                
                $status = $orderStatuses[array_rand($orderStatuses)];
                
                $order = Order::create([
                    'order_code' => 'DH' . str_pad($customerId . ($i + 1), 6, '0', STR_PAD_LEFT),
                    'customer_id' => $customerId,
                    'branch_id' => $branchIds[array_rand($branchIds)],
                    'driver_id' => in_array($status, ['awaiting_confirmation', 'confirmed', 'cancelled']) ? null : $driverIds[array_rand($driverIds)],
                    'address_id' => null,
                    'payment_id' => 1,
                    'delivery_fee' => 15000,
                    'tax_amount' => 0,
                    'status' => $status,
                    'subtotal' => 0,
                    'total_amount' => 0,
                    'delivery_address' => '123 Đường ABC, Phường Bến Nghé, Quận 1, TP.HCM',
                    'notes' => ($i % 3 == 0) ? 'Giao hàng cẩn thận, dễ vỡ.' : null,
                    'order_date' => Carbon::now()->subDays($i)->subHours(rand(1, 10)),
                    'created_at' => Carbon::now()->subDays($i)->subHours(rand(1, 10)),
                    'updated_at' => Carbon::now()->subDays($i),
                ]);

                // ... (Phần tạo order_items giữ nguyên như cũ)
                 $subtotal = 0;
                $itemsCount = rand(1, 3);
                for ($j = 0; $j < $itemsCount; $j++) {
                    $variant = $productVariants[array_rand($productVariants)];
                    $quantity = rand(1, 2);
                    $totalPrice = $variant['price'] * $quantity;
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_variant_id' => $variant['id'],
                        'quantity' => $quantity,
                        'unit_price' => $variant['price'],
                        'total_price' => $totalPrice,
                    ]);
                    $subtotal += $totalPrice;
                }
                $order->subtotal = $subtotal;
                $order->total_amount = $subtotal + $order->delivery_fee;
                $order->save();
            });
        }

        $this->command->info('Order seeder for customer ID ' . $customerId . ' ran successfully with new statuses!');
    }
}
