<?php

namespace App\Services;

use App\Models\GeneralSetting;

class ShippingService
{
    /**
     * Tính toán phí vận chuyển dựa trên tổng phụ của đơn hàng và khoảng cách.
     *
     * @param float $subtotal Tổng phụ của đơn hàng.
     * @param float $distanceInKm Khoảng cách giao hàng tính bằng km.
     * @return float Phí vận chuyển. Trả về -1 nếu khoảng cách không hợp lệ.
     */
    public static function calculateFee(float $subtotal, float $distanceInKm): float
    {
        $threshold = GeneralSetting::getFreeShippingThreshold();
        $baseFee = GeneralSetting::getShippingBaseFee();
        $feePerKm = GeneralSetting::getShippingFeePerKm();
        $maxDistance = GeneralSetting::getMaxDeliveryDistance();

        // Miễn phí vận chuyển cho các đơn hàng trên ngưỡng giá trị.
        if ($subtotal >= $threshold) {
            return 0;
        }

        // Kiểm tra xem khoảng cách có vượt quá giới hạn cho phép không.
        if ($distanceInKm > $maxDistance) {
            // Trả về một giá trị đặc biệt để báo hiệu khoảng cách không hợp lệ.
            // Việc xác thực cuối cùng sẽ được xử lý ở tầng controller.
            return -1;
        }

        // Không tính phí cho khoảng cách bằng 0.
        if ($distanceInKm <= 0) {
            return 0;
        }

        // Áp dụng phí cơ bản cho km đầu tiên.
        if ($distanceInKm <= 1) {
            return $baseFee;
        }

        // Tính phí cho các km tiếp theo. Sử dụng ceil() để làm tròn lên,
        // đảm bảo rằng bất kỳ phần nào của một km cũng được tính là một km đầy đủ.
        $additionalKms = ceil($distanceInKm) - 1;
        $shippingFee = $baseFee + ($additionalKms * $feePerKm);

        return $shippingFee;
    }

    /**
     * Tính khoảng cách giữa hai điểm tọa độ trên Trái Đất bằng công thức Haversine.
     *
     * @param float $lat1 Vĩ độ của điểm 1.
     * @param float $lon1 Kinh độ của điểm 1.
     * @param float $lat2 Vĩ độ của điểm 2.
     * @param float $lon2 Kinh độ của điểm 2.
     * @return float Khoảng cách giữa hai điểm tính bằng kilômét.
     */
    public static function getDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        if (($lat1 == $lat2) && ($lon1 == $lon2)) {
            return 0;
        }

        $earthRadius = 6371; // Bán kính Trái Đất tính bằng km.

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $lat1Rad = deg2rad($lat1);
        $lat2Rad = deg2rad($lat2);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             sin($dLon / 2) * sin($dLon / 2) * cos($lat1Rad) * cos($lat2Rad);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    /**
     * Tính toán thời gian giao hàng dự kiến (phút).
     *
     * @param \Illuminate\Database\Eloquent\Collection $cartItems Các mặt hàng trong giỏ hàng.
     * @param float $distanceInKm Khoảng cách giao hàng tính bằng km.
     * @return int Tổng thời gian dự kiến tính bằng phút.
     */
    public static function calculateEstimatedDeliveryTime($cartItems, float $distanceInKm): int
    {
        // 1. Tìm thời gian chuẩn bị lâu nhất từ các món trong giỏ hàng.
        $maxPreparationTime = $cartItems->reduce(function ($max, $item) {
            $time = $item->variant->product->preparation_time_minutes ?? 0;
            return $time > $max ? $time : $max;
        }, 0);
        
        // Nếu không có sản phẩm nào có thời gian, sử dụng giá trị mặc định.
        if ($maxPreparationTime == 0) {
            $maxPreparationTime = GeneralSetting::getDefaultPreparationTime();
        }

        // 2. Tính thời gian di chuyển.
        $averageSpeed = GeneralSetting::getAverageSpeedKmh();
        $travelTime = 0;
        if ($averageSpeed > 0 && $distanceInKm > 0) {
            $travelTime = ($distanceInKm / $averageSpeed) * 60; // Đổi sang phút
        }

        // 3. Lấy thời gian dự phòng từ database.
        $bufferTime = GeneralSetting::getBufferTimeMinutes();

        // 4. Cộng tổng và làm tròn lên.
        $totalMinutes = $maxPreparationTime + $travelTime + $bufferTime;

        return (int) ceil($totalMinutes);
    }
} 