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
        if ($coupon->current_usage_count >= $coupon->max_total_usage) {
             return response()->json(['success' => false, 'message' => 'Mã giảm giá đã hết lượt sử dụng.'], 422);
        }
        
        // --- 2.1 Validate User Usage Count ---
        if ($user) {
            $userUsageCount = \App\Models\DiscountUsageHistory::where('discount_code_id', $coupon->id)
                ->where('user_id', $user->id)
                ->count();
                
            if ($userUsageCount >= $coupon->max_usage_per_user) {
                return response()->json(['success' => false, 'message' => 'Bạn đã sử dụng hết lượt dùng mã giảm giá này.'], 422);
            }
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
        $buyNow = session('buy_now_checkout');
        
        // Kiểm tra nếu đang trong quá trình mua ngay (buy now)
        if ($buyNow) {
            // Tính toán subtotal cho mua ngay
            $subtotal = 0;
            if ($buyNow['type'] === 'product' && !empty($buyNow['variant_id'])) {
                $variant = \App\Models\ProductVariant::find($buyNow['variant_id']);
                if ($variant) {
                    $subtotal = $variant->price * $buyNow['quantity'];
                    // Thêm giá topping nếu có
                    if (!empty($buyNow['toppings'])) {
                        $toppings = \App\Models\Topping::whereIn('id', $buyNow['toppings'])->get();
                        $subtotal += $toppings->sum('price') * $buyNow['quantity'];
                    }
                }
            } elseif ($buyNow['type'] === 'combo' && !empty($buyNow['combo_id'])) {
                $combo = \App\Models\Combo::find($buyNow['combo_id']);
                if ($combo) {
                    $subtotal = $combo->price * $buyNow['quantity'];
                }
            }
            
            if ($subtotal <= 0) {
                return response()->json(['success' => false, 'message' => 'Không tìm thấy thông tin sản phẩm.'], 400);
            }
        } else {
            // Kiểm tra giỏ hàng thông thường
            $cart = Cart::with('items.variant.product', 'items.toppings')
                ->where(function($query) use ($user) {
                    if ($user) {
                        $query->where('user_id', $user->id);
                    } else {
                        $query->where('session_id', session()->getId());
                    }
                })
                ->where('status', 'active')
                ->first();
                
            if (!$cart || $cart->items->isEmpty()) {
                return response()->json(['success' => false, 'message' => 'Giỏ hàng của bạn đang trống.'], 400);
            }
            
            $subtotal = $cart->items->sum(function ($item) {
                return $item->variant->price * $item->quantity + $item->toppings->sum('price');
            });
        }

        // Kiểm tra điều kiện tối thiểu của mã giảm giá
        if ($coupon->min_requirement_type === 'order_total' && $subtotal < $coupon->min_requirement_value) {
            return response()->json(['success' => false, 'message' => 'Đơn hàng chưa đạt giá trị tối thiểu ' . number_format($coupon->min_requirement_value) . 'đ.'], 422);
        }
        
        // --- 6. Calculate the actual discount amount ---
        $discountableAmount = $subtotal; // Default: apply to whole order
        
        if ($coupon->applicable_scope === 'specific_items' && $coupon->products->isNotEmpty()) {
            $applicableItemIds = $coupon->products->pluck('product_id')->filter();
            $applicableCategoryIds = $coupon->products->pluck('category_id')->filter();
            
            if ($buyNow) {
                // Xử lý cho trường hợp mua ngay
                $discountableAmount = 0;
                
                if ($buyNow['type'] === 'product' && !empty($buyNow['variant_id'])) {
                    $variant = \App\Models\ProductVariant::with('product')->find($buyNow['variant_id']);
                    
                    if ($variant && ($applicableItemIds->contains($variant->product_id) || 
                        ($variant->product && $applicableCategoryIds->contains($variant->product->category_id)))) {
                        $discountableAmount = $variant->price * $buyNow['quantity'];
                    }
                } elseif ($buyNow['type'] === 'combo' && !empty($buyNow['combo_id'])) {
                    $combo = \App\Models\Combo::find($buyNow['combo_id']);
                    
                    if ($combo && $applicableItemIds->contains($combo->id)) {
                        $discountableAmount = $combo->price * $buyNow['quantity'];
                    }
                }
            } else {
                // Xử lý cho giỏ hàng thông thường
                $discountableAmount = $cart->items->where(function ($item) use ($applicableItemIds, $applicableCategoryIds) {
                    return $applicableItemIds->contains($item->variant->product_id) || 
                           ($item->variant->product && $applicableCategoryIds->contains($item->variant->product->category_id));
                })->sum(function ($item) {
                    return $item->variant->price * $item->quantity; // Note: toppings might not be discountable
                });
            }

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