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
        $variant = ProductVariant::with(['product', 'variantValues.variantAttribute'])
            ->find($orderItem->product_variant_id);
        
        if (!$variant || !$variant->product) {
            return;
        }

        $product = $variant->product;
        
        // Tính giá = giá gốc sản phẩm + giá biến thể
        $variantPrice = $product->price + ($variant->price_adjustment ?? 0);
        
        // Snapshot thông tin sản phẩm
        $orderItem->update([
            'product_name' => $product->name,
            'product_sku' => $product->sku,
            'product_description' => $product->description,
            'product_image' => $product->image,
            'variant_name' => $variant->name,
            'variant_attributes' => self::getVariantAttributes($variant),
            'variant_price' => $variantPrice
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
            'combo_name' => $combo->name,
            'combo_description' => $combo->description,
            'combo_image' => $combo->image,
            'combo_items' => self::getComboItems($combo),
            'combo_price' => $combo->price
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
            'topping_name' => $topping->name,
            'topping_sku' => $topping->sku,
            'topping_description' => $topping->description,
            'topping_image' => $topping->image,
            'topping_unit_price' => $topping->price
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
                if ($variantValue->variantAttribute) {
                    $attributes[$variantValue->variantAttribute->name] = $variantValue->value;
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
                    $items[] = [
                        'product_name' => $comboItem->productVariant->product->name,
                        'variant_name' => $comboItem->productVariant->name,
                        'quantity' => $comboItem->quantity,
                        'price' => $comboItem->productVariant->price
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
    }

    /**
     * Snapshot thông tin địa chỉ giao hàng
     */
    private static function snapshotDeliveryAddress($order)
    {
        // Nếu có address_id (khách hàng đã đăng ký)
        if ($order->address_id && $order->address) {
            $address = $order->address;
            
            $order->update([
                'delivery_address_line' => $address->address_line,
                'delivery_ward' => $address->ward,
                'delivery_district' => $address->district,
                'delivery_province' => $address->city, // Sửa từ province thành city
                'delivery_phone' => $address->phone_number, // Sửa từ phone thành phone_number
                'delivery_recipient_name' => $address->recipient_name,
            ]);
        }
        // Nếu là khách vãng lai (guest)
        else {
            $order->update([
                'delivery_address_line' => $order->guest_address,
                'delivery_ward' => $order->guest_ward,
                'delivery_district' => $order->guest_district,
                'delivery_province' => $order->guest_city,
                'delivery_phone' => $order->guest_phone,
                'delivery_recipient_name' => $order->guest_name,
            ]);
        }
    }
}