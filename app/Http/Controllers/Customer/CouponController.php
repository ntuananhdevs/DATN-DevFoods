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
        
        // Kiểm tra số lượng sản phẩm khác nhau trong giỏ hàng
        if (!$buyNow) {
            // Đếm số lượng sản phẩm khác nhau trong giỏ hàng
            $distinctProductCount = $cart->items->pluck('variant.product_id')->unique()->count();
            
            // Nếu có từ 2 sản phẩm khác nhau trở lên, chỉ cho phép áp dụng mã giảm giá có min_requirement_type là order_amount
            if ($distinctProductCount >= 2 && ($coupon->min_requirement_type !== 'order_amount' && $coupon->min_requirement_type !== null)) {
                return response()->json(['success' => false, 'message' => 'Với đơn hàng có từ 2 sản phẩm khác nhau trở lên, chỉ áp dụng được mã giảm giá theo giá trị đơn hàng.'], 422);
            }
            // Nếu chỉ có 1 sản phẩm, cho phép áp dụng tất cả các loại mã giảm giá phù hợp (bao gồm mã giảm giá cho danh mục cụ thể)
        }
        
        // --- 6. Calculate the actual discount amount ---
        $discountableAmount = $subtotal; // Default: apply to whole order
        
        if ($coupon->applicable_scope === 'specific_items' && $coupon->products->isNotEmpty()) {
            $applicableItemIds = $coupon->specificProducts()->pluck('product_id')->filter();
            $applicableCategoryIds = $coupon->specificCategories()->pluck('category_id')->filter();
            
            // Debug: Ghi log để kiểm tra
            \Illuminate\Support\Facades\Log::info('Coupon Applicable Category IDs: ' . json_encode($applicableCategoryIds->toArray()));
            
            if ($buyNow) {
                // Xử lý cho trường hợp mua ngay
                $discountableAmount = 0;
                
                if ($buyNow['type'] === 'product' && !empty($buyNow['variant_id'])) {
                    $variant = \App\Models\ProductVariant::with('product')->find($buyNow['variant_id']);
                    
                    $productId = $variant->product_id;
                    $categoryId = $variant->product ? $variant->product->category_id : null;
                    
                    // Debug: Ghi log để kiểm tra
                    \Illuminate\Support\Facades\Log::info('Buy Now Product ID: ' . $productId . ', Category ID: ' . $categoryId);
                    \Illuminate\Support\Facades\Log::info('Buy Now Category Check: ' . ($categoryId && $applicableCategoryIds->contains($categoryId) ? 'true' : 'false'));
                    
                    if ($variant && ($applicableItemIds->contains($productId) || 
                        ($categoryId && $applicableCategoryIds->contains($categoryId)))) {
                        $discountableAmount = $variant->price * $buyNow['quantity'];
                    }
                } elseif ($buyNow['type'] === 'combo' && !empty($buyNow['combo_id'])) {
                    // Không áp dụng mã giảm giá cho combo trừ khi mã giảm giá áp dụng cho tất cả sản phẩm
                    if ($coupon->applicable_items === 'all_items') {
                        $combo = \App\Models\Combo::find($buyNow['combo_id']);
                        if ($combo) {
                            $discountableAmount = $combo->price * $buyNow['quantity'];
                        }
                    }
                }
            } else {
                // Xử lý cho giỏ hàng thông thường
                $discountableAmount = $cart->items->where(function ($item) use ($applicableItemIds, $applicableCategoryIds, $coupon) {
                    // Nếu là combo, chỉ áp dụng khi mã giảm giá áp dụng cho tất cả sản phẩm
                    if ($item->combo_id) {
                        return $coupon->applicable_items === 'all_items';
                    }
                    
                        // Đối với sản phẩm thông thường, kiểm tra xem có thuộc danh sách sản phẩm hoặc danh mục được áp dụng không
                    $productId = $item->variant->product_id;
                    $categoryId = $item->variant->product ? $item->variant->product->category_id : null;
                    
                    // Debug: Ghi log để kiểm tra
                    \Illuminate\Support\Facades\Log::info('Cart Item Product ID: ' . $productId . ', Category ID: ' . $categoryId);
                    
                    return $applicableItemIds->contains($productId) || 
                           ($categoryId && $applicableCategoryIds->contains($categoryId));
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
        } elseif ($coupon->discount_type === 'fixed_amount') {
            $discountAmount = $coupon->discount_value;
        } elseif ($coupon->discount_type === 'free_shipping') {
            // Đối với free_shipping, giá trị giảm giá sẽ là phí vận chuyển
            // Giá trị này sẽ được tính toán và áp dụng trong CheckoutController
            $discountAmount = 0; // Tạm thời đặt là 0, sẽ được cập nhật trong quá trình thanh toán
        }

        // Ensure discount doesn't exceed the amount it applies to
        $discountAmount = min($discountAmount, $discountableAmount);

        // --- 7. Store in session and respond ---
        $sessionData = [
            'coupon_code' => $coupon->code,
            'coupon_discount_amount' => $discountAmount,
            'coupon_type' => $coupon->discount_type
        ];
        
        // Lưu thêm thông tin phần trăm giảm giá nếu là loại percentage
        if ($coupon->discount_type === 'percentage') {
            $sessionData['coupon_discount_percentage'] = $coupon->discount_value;
        }
        
        session($sessionData);

        return response()->json([
            'success' => true,
            'message' => 'Áp dụng mã giảm giá thành công!',
            'coupon' => [
                'code' => $coupon->code,
                'discount_amount' => $discountAmount,
                'type' => $coupon->discount_type
            ]
        ]);
    }

    /**
     * Remove the applied discount code from the session.
     */
    public function remove()
    {
        session()->forget(['coupon_code', 'coupon_discount_amount', 'coupon_type']);

        return response()->json([
            'success' => true,
            'message' => 'Đã gỡ mã giảm giá.'
        ]);
    }
}