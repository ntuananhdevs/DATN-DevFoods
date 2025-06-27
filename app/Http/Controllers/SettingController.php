<?php

namespace App\Http\Controllers;

use App\Models\GeneralSetting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    /**
     * Display general settings
     */
    public function index()
    {
        $settings = [
            'tax_rate' => GeneralSetting::getTaxRate(),
            'free_shipping_threshold' => GeneralSetting::getFreeShippingThreshold(),
            'shipping_fee' => GeneralSetting::getShippingFee(),
            'currency' => GeneralSetting::getCurrency(),
            'order_auto_cancel_time' => GeneralSetting::getOrderAutoCancelTime(),
        ];

        return response()->json($settings);
    }

    /**
     * Calculate order totals including tax and shipping
     */
    public function calculateOrderTotal(Request $request)
    {
        $subtotal = $request->input('subtotal', 0);
        
        // Calculate tax
        $taxAmount = GeneralSetting::calculateTax($subtotal);
        
        // Calculate shipping fee
        $shippingFee = GeneralSetting::calculateShippingFee($subtotal);
        
        // Calculate total
        $total = $subtotal + $taxAmount + $shippingFee;
        
        return response()->json([
            'subtotal' => $subtotal,
            'tax_rate' => GeneralSetting::getTaxRate() . '%',
            'tax_amount' => $taxAmount,
            'shipping_fee' => $shippingFee,
            'free_shipping_qualified' => GeneralSetting::qualifiesForFreeShipping($subtotal),
            'free_shipping_threshold' => GeneralSetting::getFreeShippingThreshold(),
            'total' => $total,
            'currency' => GeneralSetting::getCurrency(),
        ]);
    }

    /**
     * Update a setting
     */
    public function updateSetting(Request $request)
    {
        $request->validate([
            'key' => 'required|string',
            'value' => 'required|string',
            'description' => 'nullable|string',
        ]);

        $success = GeneralSetting::set(
            $request->key,
            $request->value,
            $request->description
        );

        if ($success) {
            return response()->json([
                'message' => 'Cài đặt đã được cập nhật thành công',
                'key' => $request->key,
                'value' => $request->value,
            ]);
        }

        return response()->json([
            'message' => 'Có lỗi xảy ra khi cập nhật cài đặt'
        ], 500);
    }

    /**
     * Get specific setting by key
     */
    public function getSetting($key)
    {
        $value = GeneralSetting::get($key);
        
        if ($value !== null) {
            return response()->json([
                'key' => $key,
                'value' => $value,
            ]);
        }

        return response()->json([
            'message' => 'Không tìm thấy cài đặt'
        ], 404);
    }
}