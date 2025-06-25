<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\DiscountCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CouponController extends Controller
{
    public function apply(Request $request)
    {
        try {
            $request->validate(['coupon_code' => 'required|string']);
            $couponCode = $request->input('coupon_code');

            $coupon = DiscountCode::where('code', $couponCode)->first();

            // 1. Basic Validation
            if (!$coupon || !$coupon->isActiveNow()) {
                return response()->json(['success' => false, 'message' => 'Mã giảm giá không hợp lệ hoặc đã hết hạn.'], 404);
            }

            // 2. Usage Limit Validation
            if ($coupon->usage_type !== 'unlimited') {
                if ($coupon->max_total_usage !== null && $coupon->current_usage_count >= $coupon->max_total_usage) {
                    return response()->json(['success' => false, 'message' => 'Mã giảm giá đã hết lượt sử dụng.'], 422);
                }

                $user = Auth::user();
                if ($user && $coupon->max_usage_per_user !== null) {
                    $userUsage = $coupon->usageHistory()->where('user_id', $user->id)->count();
                    if ($userUsage >= $coupon->max_usage_per_user) {
                        return response()->json(['success' => false, 'message' => 'Bạn đã hết lượt sử dụng mã giảm giá này.'], 422);
                    }
                }
            }
            
            // 3. Cart & Subtotal Calculation
            $cart = $this->getUserCart();
            if (!$cart || $cart->items->isEmpty()) {
                return response()->json(['success' => false, 'message' => 'Giỏ hàng của bạn đang trống.'], 422);
            }
            $subtotal = $this->calculateCartSubtotal($cart);

            // 4. Minimum Requirement Validation
            if ($coupon->min_requirement_type === 'purchase_amount' && $subtotal < $coupon->min_requirement_value) {
                return response()->json(['success' => false, 'message' => 'Đơn hàng chưa đạt giá trị tối thiểu để áp dụng mã này.'], 422);
            }
            
            // 5. Calculate Discount
            $discountAmount = $this->calculateDiscount($coupon, $subtotal);

            // 6. Store in Session
            Session::put('discount', $discountAmount);
            Session::put('coupon_code', $coupon->code);

            return response()->json([
                'success' => true,
                'message' => 'Áp dụng mã giảm giá thành công!',
                'discount' => $discountAmount,
                'subtotal' => $subtotal,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi khi áp dụng mã giảm giá.'
            ], 500);
        }
    }

    private function getUserCart()
    {
        $userId = Auth::id();
        $sessionId = session()->getId();
        
        $cartQuery = Cart::with('items.variant.product', 'items.toppings');
        
        if ($userId) {
            $cartQuery->where('user_id', $userId);
        } else {
            $cartQuery->where('session_id', $sessionId);
        }
        
        return $cartQuery->where('status', 'active')->first();
    }

    private function calculateCartSubtotal(Cart $cart)
    {
        $subtotal = 0;
        foreach ($cart->items as $item) {
            $itemPrice = $item->variant->price;
            $itemPrice += $item->toppings->sum('price');
            $subtotal += $itemPrice * $item->quantity;
        }
        return $subtotal;
    }

    private function calculateDiscount(DiscountCode $coupon, $subtotal)
    {
        $discountAmount = 0;
        if ($coupon->discount_type === 'percentage') {
            $discountAmount = ($subtotal * $coupon->discount_value) / 100;
            if ($coupon->max_discount_amount && $discountAmount > $coupon->max_discount_amount) {
                $discountAmount = $coupon->max_discount_amount;
            }
        } elseif ($coupon->discount_type === 'fixed_amount') {
            $discountAmount = $coupon->discount_value;
        }

        // Ensure discount doesn't exceed subtotal
        return min($discountAmount, $subtotal);
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