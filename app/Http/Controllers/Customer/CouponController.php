<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DiscountCode;
use App\Models\Cart;
use Illuminate\Support\Facades\Auth;
use App\Services\BranchService;

class CouponController extends Controller
{
    protected $branchService;

    public function __construct(BranchService $branchService)
    {
        $this->branchService = $branchService;
    }

    /**
     * Apply a discount code to the cart, with comprehensive validation.
     */
    public function apply(Request $request)
    {
        $request->validate(['coupon_code' => 'required|string']);
        $couponCode = $request->coupon_code;
        $now = now();
        $user = Auth::user();
        $currentBranch = $this->branchService->getCurrentBranch();

        if (!$currentBranch) {
            return response()->json(['success' => false, 'message' => 'Vui lòng chọn chi nhánh giao hàng trước.'], 400);
        }

        // --- 1. Find the coupon with all its relationships ---
        $coupon = DiscountCode::with(['branches', 'products.product', 'products.category', 'users'])
            ->where('code', $couponCode)
            ->where('is_active', true)
            ->where('start_date', '<=', $now)
            ->where('end_date', '>=', $now)
            ->first();

        if (!$coupon) {
            return response()->json(['success' => false, 'message' => 'Mã giảm giá không hợp lệ hoặc đã hết hạn.'], 404);
        }

        // --- 2. Validate Usage Count ---
        if ($coupon->uses_count >= $coupon->max_uses) {
             return response()->json(['success' => false, 'message' => 'Mã giảm giá đã hết lượt sử dụng.'], 422);
        }

        // --- 3. Validate Branch ---
        if ($coupon->branches->isNotEmpty() && !$coupon->branches->contains('id', $currentBranch->id)) {
            return response()->json(['success' => false, 'message' => 'Mã không áp dụng tại chi nhánh này.'], 422);
        }
        
        // --- 4. Validate User ---
        if ($coupon->usage_type === 'personal' && !$coupon->users->contains('id', $user->id)) {
            return response()->json(['success' => false, 'message' => 'Mã giảm giá không dành cho bạn.'], 422);
        }
        
        // --- 5. Validate Cart Content & Requirements ---
        $cart = Cart::with('items.variant.product', 'items.toppings')->where('user_id', $user->id)->where('status', 'active')->first();
        if (!$cart || $cart->items->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Giỏ hàng của bạn đang trống.'], 400);
        }

        $subtotal = $cart->items->sum(function ($item) {
            return $item->variant->price * $item->quantity + $item->toppings->sum('price');
        });

        if ($coupon->min_requirement_type === 'order_total' && $subtotal < $coupon->min_requirement_value) {
            return response()->json(['success' => false, 'message' => 'Đơn hàng chưa đạt giá trị tối thiểu ' . number_format($coupon->min_requirement_value) . 'đ.'], 422);
        }
        
        // --- 6. Calculate the actual discount amount ---
        $discountableAmount = $subtotal; // Default: apply to whole order
        if ($coupon->applicable_scope === 'specific_items' && $coupon->products->isNotEmpty()) {
            $applicableItemIds = $coupon->products->pluck('product_id')->filter();
            $applicableCategoryIds = $coupon->products->pluck('category_id')->filter();

            $discountableAmount = $cart->items->where(function ($item) use ($applicableItemIds, $applicableCategoryIds) {
                return $applicableItemIds->contains($item->variant->product_id) || $applicableCategoryIds->contains($item->variant->product->category_id);
            })->sum(function ($item) {
                 return $item->variant->price * $item->quantity; // Note: toppings might not be discountable
            });

            if ($discountableAmount <= 0) {
                 return response()->json(['success' => false, 'message' => 'Mã không áp dụng cho sản phẩm trong giỏ hàng.'], 422);
            }
        }
        
        $discountAmount = 0;
        if ($coupon->discount_type === 'percentage') {
            $discountAmount = ($discountableAmount * $coupon->discount_value) / 100;
            if ($coupon->max_discount_amount > 0 && $discountAmount > $coupon->max_discount_amount) {
                $discountAmount = $coupon->max_discount_amount;
            }
        } else { // fixed_amount
            $discountAmount = $coupon->discount_value;
        }

        // Ensure discount doesn't exceed the amount it applies to
        $discountAmount = min($discountAmount, $discountableAmount);

        // --- 7. Store in session and respond ---
        session([
            'coupon_code' => $coupon->code,
            'coupon_discount_amount' => $discountAmount
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Áp dụng mã giảm giá thành công!',
            'coupon' => [
                'code' => $coupon->code,
                'discount_amount' => $discountAmount
            ]
        ]);
    }

    /**
     * Remove the applied discount code from the session.
     */
    public function remove()
    {
        session()->forget(['coupon_code', 'coupon_discount_amount']);

        return response()->json([
            'success' => true,
            'message' => 'Đã gỡ mã giảm giá.'
        ]);
    }
} 