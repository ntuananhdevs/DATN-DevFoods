<?php

namespace App\Services;

use App\Models\OrderItem;
use App\Models\OrderItemTopping;
use App\Models\ProductVariant;
use App\Models\Combo;
use App\Models\Topping;

class OrderSnapshotService
{
    /**
     * Snapshot dữ liệu sản phẩm/combo cho order item
     */
    public static function snapshotOrderItem(OrderItem $orderItem)
    {
        if ($orderItem->product_variant_id) {
            self::snapshotProductVariant($orderItem);
        } elseif ($orderItem->combo_id) {
            self::snapshotCombo($orderItem);
        }
    }

    /**
     * Snapshot dữ liệu product variant
     */
    private static function snapshotProductVariant(OrderItem $orderItem)
    {
        $variant = ProductVariant::with(['product', 'variantValues.attribute'])
            ->find($orderItem->product_variant_id);
        
        if (!$variant || !$variant->product) {
            return;
        }

        $product = $variant->product;
        
        // Lấy price_adjustment từ variant values
        $priceAdjustment = $variant->variantValues->sum('price_adjustment');
        
        // Snapshot thông tin sản phẩm
        $orderItem->update([
            'product_name_snapshot' => $product->name,
            'variant_name_snapshot' => $variant->variant_description, // Sử dụng variant_description thay vì name
            'variant_attributes_snapshot' => self::getVariantAttributes($variant)
        ]);
        
        // Log để debug
        \Illuminate\Support\Facades\Log::info('Snapshot Product Variant', [
            'product_id' => $product->id,
            'product_name' => $product->name,
            'variant_id' => $variant->id,
            'variant_name' => $variant->variant_description, // Sử dụng variant_description thay vì name
            'price_adjustment' => $priceAdjustment,
            'variant_values' => $variant->variantValues->toArray()
        ]);
    }

    /**
     * Snapshot dữ liệu combo
     */
    private static function snapshotCombo(OrderItem $orderItem)
    {
        $combo = Combo::with(['comboItems.productVariant.product'])
            ->find($orderItem->combo_id);
        
        if (!$combo) {
            return;
        }

        // Snapshot thông tin combo
        $orderItem->update([
            'combo_name_snapshot' => $combo->name,
            'combo_items_snapshot' => self::getComboItems($combo)
        ]);
        
        // Log để debug
        \Illuminate\Support\Facades\Log::info('Snapshot Combo', [
            'combo_id' => $combo->id,
            'combo_name' => $combo->name,
            'combo_items' => self::getComboItems($combo)
        ]);
    }

    /**
     * Snapshot dữ liệu topping
     */
    public static function snapshotOrderItemTopping(OrderItemTopping $orderItemTopping)
    {
        $topping = Topping::find($orderItemTopping->topping_id);
        
        if (!$topping) {
            return;
        }

        $orderItemTopping->update([
            'topping_name_snapshot' => $topping->name,
            'topping_unit_price_snapshot' => $topping->price
        ]);
    }

    /**
     * Lấy thông tin attributes của variant
     */
    private static function getVariantAttributes($variant)
    {
        $attributes = [];
        
        if ($variant->variantValues) {
            foreach ($variant->variantValues as $variantValue) {
                if ($variantValue->attribute) {
                    $attributes[$variantValue->attribute->name] = $variantValue->value;
                }
            }
        }
        
        return $attributes;
    }

    /**
     * Lấy thông tin items trong combo
     */
    private static function getComboItems($combo)
    {
        $items = [];
        
        if ($combo->comboItems) {
            foreach ($combo->comboItems as $comboItem) {
                if ($comboItem->productVariant && $comboItem->productVariant->product) {
                    $variant = $comboItem->productVariant;
                    $product = $variant->product;
                    
                    // Lấy price_adjustment từ variant values
                    $priceAdjustment = $variant->variantValues->sum('price_adjustment');
                    
                    // Log thông tin từng item trong combo
                    \Illuminate\Support\Facades\Log::info('Combo Item', [
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'variant_id' => $variant->id,
                        'variant_name' => $variant->variant_description, // Sử dụng variant_description thay vì name
                        'base_price' => $product->base_price,
                        'price_adjustment' => $priceAdjustment,
                        'quantity' => $comboItem->quantity
                    ]);
                    
                    $items[] = [
                        'product_name_snapshot' => $product->name,
                        'variant_name_snapshot' => $variant->variant_description, // Sử dụng variant_description thay vì name
                        'quantity' => $comboItem->quantity
                    ];
                }
            }
        }
        
        return $items;
    }

    /**
     * Snapshot toàn bộ dữ liệu cho một đơn hàng
     */
    public static function snapshotOrder($order)
    {
        // Log thông tin đơn hàng trước khi snapshot
        \Illuminate\Support\Facades\Log::info('Starting order snapshot', [
            'order_id' => $order->id,
            'order_code' => $order->order_code,
            'address_id' => $order->address_id,
            'has_address_relation' => $order->relationLoaded('address'),
            'has_address' => $order->address ? true : false
        ]);
        
        // Snapshot địa chỉ giao hàng
        self::snapshotDeliveryAddress($order);
        
        // Snapshot từng order item
        foreach ($order->orderItems as $orderItem) {
            self::snapshotOrderItem($orderItem);
            
            // Snapshot toppings của order item
            foreach ($orderItem->toppings as $orderItemTopping) {
                self::snapshotOrderItemTopping($orderItemTopping);
            }
        }
        
        // Log kết quả snapshot
        \Illuminate\Support\Facades\Log::info('Order snapshot completed', [
            'order_id' => $order->id,
            'order_code' => $order->order_code,
            'has_delivery_address_snapshot' => !empty($order->delivery_address_line_snapshot),
            'delivery_address_snapshot' => $order->delivery_address_line_snapshot,
            'delivery_recipient_name_snapshot' => $order->delivery_recipient_name_snapshot
        ]);
    }

    /**
     * Snapshot thông tin địa chỉ giao hàng
     */
    private static function snapshotDeliveryAddress($order)
    {
        // Nếu có address_id (khách hàng đã đăng ký)
        if ($order->address_id) {
            // Đảm bảo address đã được load
            if (!$order->relationLoaded('address')) {
                $order->load('address');
            }
            
            if ($order->address) {
                $address = $order->address;
                
                $order->update([
                    'delivery_address_line_snapshot' => $address->address_line,
                    'delivery_ward_snapshot' => $address->ward,
                    'delivery_district_snapshot' => $address->district,
                    'delivery_province_snapshot' => $address->city,
                    'delivery_phone_snapshot' => $address->phone_number,
                    'delivery_recipient_name_snapshot' => $address->recipient_name,
                ]);
                
                // Log để debug
                \Illuminate\Support\Facades\Log::info('Snapshot Delivery Address from registered address', [
                    'order_id' => $order->id,
                    'address_id' => $order->address_id,
                    'address_data' => $address->toArray()
                ]);
                
                return;
            }
        }
        
        // Nếu không có address_id hoặc không tìm thấy address, sử dụng thông tin khách vãng lai
        $order->update([
            'delivery_address_line_snapshot' => $order->guest_address,
            'delivery_ward_snapshot' => $order->guest_ward,
            'delivery_district_snapshot' => $order->guest_district,
            'delivery_province_snapshot' => $order->guest_city,
            'delivery_phone_snapshot' => $order->guest_phone,
            'delivery_recipient_name_snapshot' => $order->guest_name,
        ]);
        
        // Log để debug
        \Illuminate\Support\Facades\Log::info('Snapshot Delivery Address from guest info', [
            'order_id' => $order->id,
            'guest_data' => [
                'name' => $order->guest_name,
                'phone' => $order->guest_phone,
                'address' => $order->guest_address,
                'ward' => $order->guest_ward,
                'district' => $order->guest_district,
                'city' => $order->guest_city
            ]
        ]);
    }
}