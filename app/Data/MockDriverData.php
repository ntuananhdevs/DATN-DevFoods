<?php

namespace App\Data;

class MockDriverData
{
    public static $mockDriverProfile = [
        'id' => 'driver001',
        'full_name' => 'Nguyễn Văn Tài',
        'email' => 'driver@example.com',
        'phone_number' => '0987654321',
        'avatar_url' => '/placeholder.svg?width=128&height=128',
        'is_active' => true,
        'vehicle' => 'Honda Wave Alpha',
        'license_plate' => '59-T1 123.45',
        'id_card_number' => '012345678910',
        'bank_account' => [
            'bank_name' => 'Vietcombank',
            'account_number' => '0071000123456',
            'account_holder_name' => 'NGUYEN VAN TAI',
        ],
    ];

    public static function getMockOrders()
    {
        return [
            [
                'id' => 'ORD001',
                'customer_name' => 'Nguyễn Văn A',
                'customer_phone' => '0901234567',
                'delivery_address' => '123 Đường ABC, Quận 1, TP.HCM',
                'restaurant_name' => 'Nhà hàng XYZ',
                'restaurant_address' => '456 Đường DEF, Quận 2, TP.HCM',
                'items' => [
                    ['name' => 'Cơm gà xối mỡ', 'quantity' => 1, 'price' => 45000],
                    ['name' => 'Nước ngọt', 'quantity' => 1, 'price' => 15000]
                ],
                'total_amount' => 60000,
                'delivery_fee' => 15000,
                'driverEarnings' => 12000,
                'distanceKm' => 3.5,
                'estimated_time' => 25,
                'status' => 'Chờ nhận',
                'orderTime' => now()->subHours(1)->toDateTimeString(),
                'pickup_time' => null,
                'delivery_time' => null,
                'notes' => 'Giao hàng cẩn thận'
            ],
            [
                'id' => 'ORD002',
                'customer_name' => 'Trần Thị B',
                'customer_phone' => '0912345678',
                'delivery_address' => '789 Đường GHI, Quận 3, TP.HCM',
                'restaurant_name' => 'Quán ăn ABC',
                'restaurant_address' => '321 Đường JKL, Quận 4, TP.HCM',
                'items' => [
                    ['name' => 'Phở bò', 'quantity' => 2, 'price' => 50000],
                    ['name' => 'Chả cá', 'quantity' => 1, 'price' => 25000]
                ],
                'total_amount' => 125000,
                'delivery_fee' => 20000,
                'driverEarnings' => 16000,
                'distanceKm' => 5.2,
                'estimated_time' => 35,
                'status' => 'Đang giao',
                'orderTime' => now()->subHours(2)->toDateTimeString(),
                'pickup_time' => now()->subMinutes(30)->toDateTimeString(),
                'delivery_time' => null,
                'notes' => ''
            ],
            [
                'id' => 'ORD003',
                'customer_name' => 'Lê Văn C',
                'customer_phone' => '0923456789',
                'delivery_address' => '456 Đường MNO, Quận 5, TP.HCM',
                'restaurant_name' => 'Cơm tấm Sài Gòn',
                'restaurant_address' => '654 Đường PQR, Quận 6, TP.HCM',
                'items' => [
                    ['name' => 'Cơm tấm sườn bì', 'quantity' => 1, 'price' => 35000],
                    ['name' => 'Trà đá', 'quantity' => 1, 'price' => 5000]
                ],
                'total_amount' => 40000,
                'delivery_fee' => 12000,
                'driverEarnings' => 10000,
                'distanceKm' => 2.8,
                'estimated_time' => 20,
                'status' => 'Đã hoàn thành',
                'orderTime' => now()->subHours(4)->toDateTimeString(),
                'pickup_time' => now()->subHours(3)->subMinutes(45)->toDateTimeString(),
                'delivery_time' => now()->subHours(3)->subMinutes(20)->toDateTimeString(),
                'notes' => 'Đã giao thành công'
            ]
        ];
    }

    public static function getMockDeliveryHistory()
    {
        return [
            [
                'id' => 'ORD003',
                'date' => now()->subDays(1)->toDateString(),
                'customer_name' => 'Lê Văn C',
                'delivery_address' => '456 Đường MNO, Quận 5, TP.HCM',
                'total_amount' => 40000,
                'driver_earnings' => 10000,
                'distance_km' => 2.8,
                'status' => 'Đã hoàn thành',
                'rating' => 5
            ],
            [
                'id' => 'ORD004',
                'date' => now()->subDays(2)->toDateString(),
                'customer_name' => 'Phạm Thị D',
                'delivery_address' => '789 Đường STU, Quận 7, TP.HCM',
                'total_amount' => 85000,
                'driver_earnings' => 17000,
                'distance_km' => 4.5,
                'status' => 'Đã hoàn thành',
                'rating' => 4
            ],
            [
                'id' => 'ORD005',
                'date' => now()->subDays(3)->toDateString(),
                'customer_name' => 'Hoàng Văn E',
                'delivery_address' => '123 Đường VWX, Quận 8, TP.HCM',
                'total_amount' => 65000,
                'driver_earnings' => 13000,
                'distance_km' => 3.2,
                'status' => 'Đã hoàn thành',
                'rating' => 5
            ]
        ];
    }
}