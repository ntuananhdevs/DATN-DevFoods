<?php

namespace App\Data;

use Carbon\Carbon;

class MockDriverData
{
    public static $mockDriverProfile = [
        "id" => "driver001",
        "name" => "Nguyễn Văn Tài",
        "avatarUrl" => "/placeholder.svg?width=128&height=128",
        "isActive" => true,
        "vehicle" => "Honda Wave Alpha",
        "licensePlate" => "59-T1 123.45",
        "idCardNumber" => "012345678910",
        "bankAccount" => [
            "bankName" => "Vietcombank",
            "accountNumber" => "0071000123456",
            "accountHolderName" => "NGUYEN VAN TAI",
        ],
        "phone" => "0987654321",
    ];

    private static function generateItems()
    {
        $foodItems = [
            ["name" => "Cơm gà xối mỡ", "price" => 45000],
            ["name" => "Pizza Hải Sản", "price" => 150000],
            ["name" => "Bún bò Huế", "price" => 50000],
            ["name" => "Trà sữa trân châu", "price" => 40000],
            ["name" => "Phở Bò Tái", "price" => 55000],
        ];
        $numItems = rand(1, 2);
        $selectedItems = [];
        for ($i = 0; $i < $numItems; $i++) {
            $item = $foodItems[array_rand($foodItems)];
            $selectedItems[] = array_merge($item, ["quantity" => rand(1, 2)]);
        }
        return $selectedItems;
    }

    public static function calculateShippingFee($distanceKm)
    {
        if ($distanceKm <= 0) return 0;
        if ($distanceKm <= 3) return 15000;
        if ($distanceKm <= 5) return 20000;
        return 20000 + ceil($distanceKm - 5) * 5000;
    }

    private static function calculateTotalAmount($items)
    {
        return array_reduce($items, function ($sum, $item) {
            return $sum + $item['price'] * $item['quantity'];
        }, 0);
    }

    public static function getMockOrders()
    {
        $orders = [
            [
                "id" => "DH001",
                "customerName" => "Trần Thị Bích",
                "deliveryAddress" => "123 Đường Sư Vạn Hạnh, P.12, Q.10, TP. HCM",
                "pickupBranch" => "Chi nhánh 1",
                "customerPhone" => "0901234567",
                "orderTime" => "2025-06-07 10:00",
                "status" => "Chờ nhận",
                "items" => self::generateItems(),
                "distanceKm" => 2.5,
                "pickupCoordinates" => ["lat" => 10.774, "lng" => 106.668],
                "deliveryCoordinates" => ["lat" => 10.765, "lng" => 106.675],
            ],
            [
                "id" => "DH002",
                "customerName" => "Lê Văn Cường",
                "deliveryAddress" => "456 Đường Nguyễn Trãi, P.8, Q.5, TP. HCM",
                "pickupBranch" => "Chi nhánh 2",
                "customerPhone" => "0912345678",
                "orderTime" => "2025-06-07 10:15",
                "status" => "Chờ nhận",
                "items" => self::generateItems(),
                "distanceKm" => 4.0,
                "pickupCoordinates" => ["lat" => 10.755, "lng" => 106.66],
                "deliveryCoordinates" => ["lat" => 10.74, "lng" => 106.65],
            ],
            [
                "id" => "DH003",
                "customerName" => "Phạm Thị Dung",
                "deliveryAddress" => "789 Đường Cách Mạng Tháng Tám, P.15, Q.10, TP. HCM",
                "pickupBranch" => "Chi nhánh 1",
                "customerPhone" => "0923456789",
                "orderTime" => "2025-06-07 09:30",
                "status" => "Đang giao",
                "items" => self::generateItems(),
                "distanceKm" => 3.2,
                "estimatedDeliveryTime" => "2025-06-07 10:45",
                "pickupCoordinates" => ["lat" => 10.774, "lng" => 106.668],
                "deliveryCoordinates" => ["lat" => 10.785, "lng" => 106.68],
            ],
            [
                "id" => "DH004",
                "customerName" => "Hoàng Văn Em",
                "deliveryAddress" => "101 Đường Lê Văn Sỹ, P.13, Q.Phú Nhuận, TP. HCM",
                "pickupBranch" => "Chi nhánh 3",
                "customerPhone" => "0934567890",
                "orderTime" => "2025-06-06 18:00",
                "status" => "Đã hoàn thành",
                "items" => self::generateItems(),
                "distanceKm" => 6.1,
                "pickupCoordinates" => ["lat" => 10.79, "lng" => 106.678],
                "deliveryCoordinates" => ["lat" => 10.8, "lng" => 106.69],
            ],
            [
                "id" => "DH005",
                "customerName" => "Võ Thị Lan",
                "deliveryAddress" => "234 Đường 3 Tháng 2, P.10, Q.10, TP. HCM",
                "pickupBranch" => "Chi nhánh 1",
                "customerPhone" => "0945678901",
                "orderTime" => "2025-06-07 11:00",
                "status" => "Chờ nhận",
                "items" => self::generateItems(),
                "distanceKm" => 1.5,
                "notes" => "Gọi trước khi giao 5 phút.",
                "pickupCoordinates" => ["lat" => 10.774, "lng" => 106.668],
                "deliveryCoordinates" => ["lat" => 10.77, "lng" => 106.67],
            ],
        ];

        return array_map(function ($order) {
            $totalAmount = self::calculateTotalAmount($order['items']);
            $shippingFee = self::calculateShippingFee($order['distanceKm']);
            return array_merge($order, [
                "totalAmount" => $totalAmount,
                "shippingFee" => $shippingFee,
                "finalTotal" => $totalAmount + $shippingFee,
                "driverEarnings" => $shippingFee,
            ]);
        }, $orders);
    }

    public static function getMockDeliveryHistory()
    {
        $history = array_filter(self::getMockOrders(), function ($order) {
            return $order['status'] === "Đã hoàn thành";
        });

        return array_map(function ($order) {
            return array_merge($order, [
                "rating" => rand(3, 5),
                "customerFeedback" => (rand(0, 1) > 0.5) ? "Giao hàng nhanh, tài xế thân thiện." : "Đồ ăn ngon, shipper nhiệt tình.",
            ]);
        }, $history);
    }

    public static function getMockNotifications()
    {
        $notifications = [
            [
                "id" => "notif001",
                "type" => "new_order",
                "title" => "Đơn hàng mới!",
                "message" => "Có đơn hàng mới DH001 đang chờ bạn nhận tại Chi nhánh 1.",
                "timestamp" => "2025-06-07 10:01",
                "read" => false,
                "orderId" => "DH001",
                "link" => "/driver/orders/DH001",
            ],
            [
                "id" => "notif002",
                "type" => "status_update",
                "title" => "Cập nhật đơn hàng DH003",
                "message" => "Đơn hàng DH003 đã được cập nhật trạng thái: Đang giao.",
                "timestamp" => "2025-06-07 09:35",
                "read" => true,
                "orderId" => "DH003",
            ],
            [
                "id" => "notif003",
                "type" => "earning_report",
                "title" => "Báo cáo thu nhập",
                "message" => "Thu nhập ngày 06/06/2025 của bạn là 150.000đ. Chi tiết...",
                "timestamp" => "2025-06-07 08:00",
                "read" => true,
                "link" => "/driver/history",
            ],
            [
                "id" => "notif004",
                "type" => "system_message",
                "title" => "Bảo trì hệ thống",
                "message" => "Hệ thống sẽ bảo trì từ 02:00 đến 03:00 ngày 08/06/2025.",
                "timestamp" => "2025-06-06 17:00",
                "read" => false,
            ],
        ];

        // Sort by timestamp descending
        usort($notifications, function ($a, $b) {
            return strtotime($b['timestamp']) - strtotime($a['timestamp']);
        });

        return $notifications;
    }
}