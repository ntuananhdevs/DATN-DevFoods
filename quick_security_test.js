/**
 * QUICK SECURITY TEST SCRIPT
 * Chạy script này trong browser console khi đang ở trang checkout
 * WARNING: Chỉ sử dụng để test bảo mật, không để exploit thật!
 */

console.log("🔍 Starting DATN-DevFoods Security Test...");

// 1. Test CSRF Token availability
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
if (!csrfToken) {
    console.error("❌ CSRF Token not found!");
} else {
    console.log("✅ CSRF Token found:", csrfToken);
}

// 2. Test Session Cookie
const sessionCookie = document.cookie.match(/laravel_session=([^;]+)/);
if (!sessionCookie) {
    console.error("❌ Session cookie not found!");
} else {
    console.log("✅ Session cookie found");
}

// 3. CRITICAL TEST: Discount Manipulation
async function testDiscountManipulation() {
    console.log("\n🚨 Testing Discount Manipulation Vulnerability...");
    
    const maliciousDiscount = 999999999; // 999 million VND
    
    try {
        const response = await fetch('/coupon/apply', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            credentials: 'same-origin',
            body: JSON.stringify({
                coupon_code: 'FASTFOOD10',
                discount: maliciousDiscount
            })
        });
        
        const result = await response.json();
        
        if (result.success && result.discount == maliciousDiscount) {
            console.log("🚨 CRITICAL VULNERABILITY CONFIRMED!");
            console.log("💰 Server accepted malicious discount:", result.discount.toLocaleString('vi-VN'), "đ");
            console.log("🔥 POTENTIAL FINANCIAL LOSS: EXTREME");
            
            // Check if discount appears on page
            setTimeout(() => {
                const discountElement = document.querySelector('[id*="discount"], [class*="discount"]');
                if (discountElement && discountElement.textContent.includes('999')) {
                    console.log("👁️ Malicious discount is VISIBLE on page!");
                }
                
                // Check total amount
                const totalElements = document.querySelectorAll('*');
                for (let el of totalElements) {
                    if (el.textContent.includes('Tổng cộng') || el.textContent.includes('Total')) {
                        const nextElement = el.nextElementSibling || el.parentElement.querySelector('[class*="price"], [class*="total"]');
                        if (nextElement) {
                            console.log("💵 Current total display:", nextElement.textContent);
                            if (nextElement.textContent.includes('-') || nextElement.textContent === '0đ') {
                                console.log("🚨 TOTAL IS ZERO OR NEGATIVE!");
                            }
                        }
                        break;
                    }
                }
            }, 1000);
            
        } else {
            console.log("✅ Server rejected malicious discount (GOOD)");
            console.log("Response:", result);
        }
        
    } catch (error) {
        console.error("❌ Error testing discount manipulation:", error);
    }
}

// 4. Test Multiple Discount Applications
async function testMultipleDiscounts() {
    console.log("\n🧪 Testing Multiple Discount Applications...");
    
    // Apply first discount
    const firstResponse = await fetch('/coupon/apply', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        credentials: 'same-origin',
        body: JSON.stringify({
            coupon_code: 'FASTFOOD10',
            discount: 50000
        })
    });
    
    const firstResult = await firstResponse.json();
    console.log("First discount result:", firstResult);
    
    // Apply second discount
    setTimeout(async () => {
        const secondResponse = await fetch('/coupon/apply', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            credentials: 'same-origin',
            body: JSON.stringify({
                coupon_code: 'FASTFOOD10',
                discount: 100000
            })
        });
        
        const secondResult = await secondResponse.json();
        console.log("Second discount result:", secondResult);
        
        if (secondResult.discount === (firstResult.discount + 100000)) {
            console.log("⚠️ VULNERABILITY: Discounts accumulate!");
        } else {
            console.log("✅ Discount gets overwritten (normal behavior)");
        }
    }, 1000);
}

// 5. Check for shipping inconsistency
function checkShippingLogic() {
    console.log("\n🚚 Checking Shipping Logic...");
    
    const pageSource = document.documentElement.outerHTML;
    
    // Look for frontend shipping logic
    const frontendShippingMatch = pageSource.match(/\$subtotal\s*>\s*(\d+)/);
    if (frontendShippingMatch) {
        console.log("📄 Frontend free shipping threshold:", parseInt(frontendShippingMatch[1]).toLocaleString('vi-VN'), "đ");
    }
    
    // Note about backend logic
    console.log("⚠️ Backend logic uses 200,000đ threshold (check in checkout processing)");
    
    if (frontendShippingMatch && parseInt(frontendShippingMatch[1]) !== 200000) {
        console.log("🚨 INCONSISTENCY DETECTED between frontend and backend shipping logic!");
    }
}

// 6. Session Storage Analysis
function analyzeSessionStorage() {
    console.log("\n🗂️ Analyzing Session Storage...");
    
    // Check localStorage
    console.log("📦 LocalStorage items:");
    for (let i = 0; i < localStorage.length; i++) {
        const key = localStorage.key(i);
        console.log(`  ${key}:`, localStorage.getItem(key));
    }
    
    // Check sessionStorage
    console.log("📦 SessionStorage items:");
    for (let i = 0; i < sessionStorage.length; i++) {
        const key = sessionStorage.key(i);
        console.log(`  ${key}:`, sessionStorage.getItem(key));
    }
    
    // Check cookies
    console.log("🍪 Relevant cookies:");
    const cookies = document.cookie.split(';');
    cookies.forEach(cookie => {
        if (cookie.includes('laravel') || cookie.includes('session') || cookie.includes('cart')) {
            console.log(`  ${cookie.trim()}`);
        }
    });
}

// 7. Generate Security Report
function generateSecurityReport() {
    console.log("\n📊 SECURITY TEST REPORT");
    console.log("=" .repeat(50));
    
    const vulnerabilities = [];
    
    // Check for CSRF protection
    if (!csrfToken) {
        vulnerabilities.push("❌ CSRF Token missing");
    }
    
    // Add detected vulnerabilities to report
    if (window.vulnerabilityDetected) {
        vulnerabilities.push("🚨 CRITICAL: Discount Manipulation Possible");
    }
    
    if (vulnerabilities.length === 0) {
        console.log("✅ No immediate vulnerabilities detected in this quick test");
    } else {
        console.log("🚨 VULNERABILITIES DETECTED:");
        vulnerabilities.forEach(vuln => console.log(`  ${vuln}`));
    }
    
    console.log("\n💡 RECOMMENDATIONS:");
    console.log("  1. Implement server-side discount validation");
    console.log("  2. Sync frontend/backend shipping logic");
    console.log("  3. Add audit logging for checkout processes");
    console.log("  4. Implement rate limiting for coupon applications");
    
    console.log("\n⚠️ REMEMBER: Report these findings to development team!");
}

// Run all tests
async function runAllTests() {
    try {
        await testDiscountManipulation();
        await new Promise(resolve => setTimeout(resolve, 2000));
        
        await testMultipleDiscounts();
        await new Promise(resolve => setTimeout(resolve, 2000));
        
        checkShippingLogic();
        analyzeSessionStorage();
        
        setTimeout(generateSecurityReport, 3000);
        
    } catch (error) {
        console.error("❌ Error running security tests:", error);
    }
}

// Auto-run if on checkout page
if (window.location.pathname.includes('checkout')) {
    console.log("🎯 Checkout page detected, starting automated tests...");
    runAllTests();
} else {
    console.log("ℹ️ Navigate to checkout page and run runAllTests() to start testing");
    console.log("ℹ️ Or run individual test functions:");
    console.log("  - testDiscountManipulation()");
    console.log("  - testMultipleDiscounts()");
    console.log("  - checkShippingLogic()");
    console.log("  - analyzeSessionStorage()");
}

// Make functions available globally
window.testDiscountManipulation = testDiscountManipulation;
window.testMultipleDiscounts = testMultipleDiscounts;
window.checkShippingLogic = checkShippingLogic;
window.analyzeSessionStorage = analyzeSessionStorage;
window.runAllTests = runAllTests;
window.generateSecurityReport = generateSecurityReport;

console.log("🔧 Security testing functions loaded. Type 'runAllTests()' to start comprehensive testing."); 