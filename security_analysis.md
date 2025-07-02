# PHÂN TÍCH BẢO MẬT CHECKOUT SYSTEM

## 🚨 LỖ HỔNG BẢO MẬT VÀ CÁCH KHAI THÁC

### 1. DISCOUNT MANIPULATION (CRITICAL)

**Lỗ hổng:** 
- Server tin tưởng discount value từ client
- Không validate discount amount

**Cách khai thác:**
```javascript
// Gửi request qua browser console hoặc intercepting tool
fetch('/coupon/apply', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
    },
    body: JSON.stringify({
        coupon_code: 'FASTFOOD10',
        discount: 999999999  // ⚠️ Giá trị bất kỳ!
    })
});
```

**Tác động:** 
- User có thể nhận discount 999,999,999đ
- Loss tài chính nghiêm trọng

### 2. SHIPPING FEE INCONSISTENCY (HIGH)

**Vấn đề:**
- Frontend: Miễn phí ship > 100k (phí: 15k)
- Backend: Miễn phí ship > 200k (phí: 25k)

**Tác động:**
- User confusion
- Potential revenue loss
- Trust issues

### 3. SESSION SECURITY (MEDIUM)

**Lỗ hổng:**
- Discount lưu trong session có thể bị manipulate
- Không có expiry time cho discount

## 🛡️ KHUYẾN NGHỊ SỬA LỖI

### 1. FIX DISCOUNT VALIDATION (CRITICAL)

```php
public function apply(Request $request)
{
    $couponCode = $request->input('coupon_code');
    
    // ✅ Validate từ database
    $discountCode = DiscountCode::where('code', $couponCode)
        ->where('is_active', true)
        ->where('start_date', '<=', now())
        ->where('end_date', '>=', now())
        ->first();
        
    if (!$discountCode) {
        return response()->json(['success' => false, 'message' => 'Mã không hợp lệ']);
    }
    
    // ✅ Tính discount server-side
    $discount = $this->calculateDiscount($discountCode, $cartTotal);
    
    Session::put('discount', $discount);
    Session::put('discount_code_id', $discountCode->id);
    Session::put('discount_applied_at', now());
}
```

### 2. FIX SHIPPING CONSISTENCY

```php
// Tạo helper function chung
class PricingHelper 
{
    public static function calculateShipping($subtotal) 
    {
        return $subtotal > 200000 ? 0 : 25000; // Consistent logic
    }
}
```

### 3. ADD PRICE INTEGRITY CHECKS

```php
public function process(Request $request)
{
    // ✅ Validate giá không thay đổi
    $cartItems = CartItem::with(['variant'])->where('cart_id', $cart->id)->get();
    
    foreach ($cartItems as $item) {
        $currentPrice = $item->variant->fresh()->price;
        if ($currentPrice !== $item->variant->price) {
            throw new \Exception('Giá sản phẩm đã thay đổi. Vui lòng refresh trang.');
        }
    }
    
    // ✅ Re-validate discount
    if (session('discount')) {
        $isValidDiscount = $this->validateStoredDiscount();
        if (!$isValidDiscount) {
            session()->forget('discount');
            throw new \Exception('Mã giảm giá đã hết hạn.');
        }
    }
}
```

### 4. ADD AUDIT LOGGING

```php
// Log tất cả order để audit
Log::info('Order created', [
    'order_id' => $order->id,
    'user_id' => $userId,
    'original_subtotal' => $subtotal,
    'shipping_fee' => $shipping,
    'discount_applied' => $discount,
    'final_total' => $total,
    'ip_address' => $request->ip(),
    'user_agent' => $request->userAgent()
]);
```

## 📊 ĐÁNH GIÁ TỔNG QUAN

| Mức độ | Số lỗ hổng | Mô tả |
|--------|------------|-------|
| 🔴 Critical | 2 | Discount manipulation, Hardcoded logic |
| 🟡 High | 1 | Shipping inconsistency |
| 🟢 Medium | 1 | Session security |

**Điểm bảo mật hiện tại: 3/10** ⭐⭐⭐☆☆☆☆☆☆☆

**Sau khi fix: 8/10** ⭐⭐⭐⭐⭐⭐⭐⭐☆☆ 