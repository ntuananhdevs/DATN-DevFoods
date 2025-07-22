<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Shipping Fee Settings
    |--------------------------------------------------------------------------
    |
    | Cấu hình để tính phí vận chuyển dựa trên khoảng cách và giá trị đơn hàng.
    |
    */

    /**
     * Ngưỡng miễn phí vận chuyển.
     * Áp dụng miễn phí vận chuyển nếu tổng phụ của đơn hàng lớn hơn hoặc bằng giá trị này.
     */
    'free_shipping_threshold' => 200000, // 200,000 VND

    /**
     * Phí vận chuyển cơ bản.
     * Đây là mức phí cố định cho km đầu tiên.
     */
    'base_fee' => 10000, // 10,000 VND

    /**
     * Phí cho mỗi km bổ sung.
     * Mức phí này được tính cho mỗi km sau km đầu tiên.
     */
    'fee_per_km' => 5000, // 5,000 VND

    /**
     * Khoảng cách giao hàng tối đa (km).
     * Các địa chỉ vượt quá khoảng cách này sẽ không được chấp nhận.
     */
    'max_delivery_distance' => 7, // km

    /*
    |--------------------------------------------------------------------------
    | Estimated Delivery Time Settings
    |--------------------------------------------------------------------------
    */

    /**
     * Tốc độ di chuyển trung bình của tài xế (km/h).
     * Dùng để tính toán thời gian giao hàng dự kiến.
     */
    'average_speed_kmh' => 20,

    /**
     * Thời gian dự phòng (phút).
     * Thêm vào để bù cho các sự cố nhỏ như kẹt xe, thời tiết.
     */
    'buffer_time_minutes' => 10,

    /**
     * Thời gian chuẩn bị mặc định (phút)
      * Sử dụng khi một sản phẩm trong giỏ hàng không có thời gian chuẩn bị cụ thể.
      */
    'default_preparation_time' => 15,

    /*
    |--------------------------------------------------------------------------
    | Driver Commission Settings
    |--------------------------------------------------------------------------
    */

    /**
     * Tỷ lệ hoa hồng cho tài xế (%).
     * Tài xế sẽ nhận được phần trăm này từ phí giao hàng.
     */
    'driver_commission_rate' => 0.6, // 60% phí giao hàng
];