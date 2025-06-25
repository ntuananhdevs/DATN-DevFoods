<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CouponController extends Controller
{
    public function apply(Request $request)
    {
        try {
            $couponCode = $request->input('coupon_code');
            $discount = $request->input('discount');

            // Validate coupon code
            if ($couponCode !== 'FASTFOOD10') {
                return response()->json([
                    'success' => false,
                    'message' => 'Mã giảm giá không hợp lệ'
                ]);
            }

            // Store discount in session
            Session::put('discount', $discount);
            Session::put('coupon_code', $couponCode);

            return response()->json([
                'success' => true,
                'message' => 'Áp dụng mã giảm giá thành công',
                'discount' => $discount
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi khi áp dụng mã giảm giá'
            ], 500);
        }
    }

    public function remove()
    {
        Session::forget(['discount', 'coupon_code']);
        
        return response()->json([
            'success' => true,
            'message' => 'Đã xóa mã giảm giá'
        ]);
    }
} 