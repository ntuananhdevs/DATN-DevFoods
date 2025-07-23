<?php

namespace App\Services;

class ShippingService
{
    /**
     * Calculate distance between two points using Haversine formula
     */
    public static function getDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // Earth radius in kilometers

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon/2) * sin($dLon/2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));

        return $earthRadius * $c; // Distance in kilometers
    }

    /**
     * Calculate shipping fee based on subtotal and distance
     */
    public static function calculateFee($subtotal, $distance)
    {
        // Free shipping for orders over 200,000 VND
        if ($subtotal >= 200000) {
            return 0;
        }

        // Base shipping fee
        $baseFee = 15000;

        // Additional fee per km after 5km
        if ($distance > 5) {
            $additionalFee = ($distance - 5) * 2000;
            $baseFee += $additionalFee;
        }

        // Maximum delivery distance is 20km
        if ($distance > 20) {
            return -1; // Outside delivery area
        }

        return $baseFee;
    }

    /**
     * Calculate estimated delivery time in minutes
     */
    public static function calculateEstimatedDeliveryTime($cartItems, $distance)
    {
        // Base preparation time: 15 minutes
        $preparationTime = 15;

        // Additional time based on number of items
        $itemCount = 0;
        foreach ($cartItems as $item) {
            $itemCount += $item->quantity;
        }

        // Add 2 minutes per item
        $preparationTime += $itemCount * 2;

        // Delivery time based on distance (assuming 30km/h average speed)
        $deliveryTime = ($distance / 30) * 60; // Convert to minutes

        return $preparationTime + $deliveryTime;
    }
}