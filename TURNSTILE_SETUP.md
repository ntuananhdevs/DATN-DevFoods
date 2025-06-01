# Cloudflare Turnstile Integration Guide

## 📋 Tổng quan

Cloudflare Turnstile là một giải pháp CAPTCHA hiện đại, thân thiện với người dùng và bảo mật cao. Nó thay thế cho reCAPTCHA truyền thống với trải nghiệm người dùng tốt hơn.

## 🚀 Cài đặt

### 1. Cài đặt Package

```bash
composer require ryangjchandler/laravel-cloudflare-turnstile
```

### 2. Cấu hình Environment

Thêm vào file `.env`:

```env
# Cloudflare Turnstile Configuration
TURNSTILE_SITE_KEY=your_site_key_here
TURNSTILE_SECRET_KEY=your_secret_key_here
TURNSTILE_THEME=light
TURNSTILE_SIZE=normal
```

### 3. Lấy Site Key và Secret Key

1. Truy cập [Cloudflare Dashboard](https://dash.cloudflare.com/)
2. Chọn **Turnstile** từ sidebar
3. Tạo một site mới
4. Copy Site Key và Secret Key vào file `.env`

## 🔧 Cấu hình

### File cấu hình: `config/turnstile.php`

```php
<?php

return [
    'site_key' => env('TURNSTILE_SITE_KEY'),
    'secret_key' => env('TURNSTILE_SECRET_KEY'),
    'verify_url' => 'https://challenges.cloudflare.com/turnstile/v0/siteverify',
    'theme' => env('TURNSTILE_THEME', 'light'),
    'size' => env('TURNSTILE_SIZE', 'normal'),
];
```

## 📝 Sử dụng

### 1. Trong Blade Template

#### Cách 1: Sử dụng Component

```blade
<x-turnstile />
```

#### Cách 2: Sử dụng Directive

```blade
@turnstile
```

#### Cách 3: Manual Implementation

```blade
<div class="cf-turnstile" 
     data-sitekey="{{ config('turnstile.site_key') }}"
     data-theme="{{ config('turnstile.theme') }}"
     data-size="{{ config('turnstile.size') }}"
     data-callback="onTurnstileCallback">
</div>

<script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
```

### 2. Trong Controller

#### Sử dụng TurnstileRule

```php
use App\Rules\TurnstileRule;

public function store(Request $request)
{
    $request->validate([
        'name' => 'required|string',
        'email' => 'required|email',
        'cf-turnstile-response' => ['required', new TurnstileRule()],
    ]);
    
    // Process form...
}
```

#### Sử dụng Middleware

```php
// Trong routes/web.php
Route::post('/contact', [ContactController::class, 'store'])
    ->middleware('turnstile');
```

### 3. JavaScript Handling

```javascript
let turnstileToken = null;

// Callback khi Turnstile thành công
window.onTurnstileCallback = function(token) {
    turnstileToken = token;
    document.getElementById('submitBtn').disabled = false;
};

// Callback khi có lỗi
window.onTurnstileError = function() {
    turnstileToken = null;
    document.getElementById('submitBtn').disabled = true;
    alert('Xác minh bảo mật thất bại. Vui lòng thử lại.');
};

// Callback khi hết hạn
window.onTurnstileExpired = function() {
    turnstileToken = null;
    document.getElementById('submitBtn').disabled = true;
    alert('Xác minh bảo mật đã hết hạn. Vui lòng thực hiện lại.');
};

// Kiểm tra trước khi submit form
document.querySelector('form').addEventListener('submit', function(e) {
    if (!turnstileToken) {
        e.preventDefault();
        alert('Vui lòng hoàn thành xác minh bảo mật');
        return false;
    }
});
```

## 🎨 Tùy chỉnh

### Themes

- `light` - Giao diện sáng (mặc định)
- `dark` - Giao diện tối
- `auto` - Tự động theo hệ thống

### Sizes

- `normal` - Kích thước bình thường (mặc định)
- `compact` - Kích thước nhỏ gọn

### Custom Styling

```css
.cf-turnstile {
    margin: 20px 0;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
```

## 🔒 Bảo mật

### Best Practices

1. **Luôn validate server-side**: Không bao giờ chỉ dựa vào client-side validation
2. **Bảo mật Secret Key**: Không expose secret key ra client
3. **Rate limiting**: Kết hợp với rate limiting để chống spam
4. **IP validation**: Sử dụng IP validation khi cần thiết

### Validation Rule

```php
// app/Rules/TurnstileRule.php
public function validate(string $attribute, mixed $value, Closure $fail): void
{
    $response = Http::asForm()->post(config('turnstile.verify_url'), [
        'secret' => config('turnstile.secret_key'),
        'response' => $value,
        'remoteip' => request()->ip(),
    ]);

    if (!$response->successful() || !$response->json('success')) {
        $fail('Xác minh CAPTCHA thất bại. Vui lòng thử lại.');
    }
}
```

## 🛠️ Helper Functions

### TurnstileHelper Class

```php
use App\Helpers\TurnstileHelper;

// Kiểm tra xem Turnstile có được bật không
if (TurnstileHelper::isEnabled()) {
    // Show Turnstile
}

// Verify response manually
$isValid = TurnstileHelper::verify($response, $remoteIp);

// Get configuration
$siteKey = TurnstileHelper::getSiteKey();
$theme = TurnstileHelper::getTheme();
$size = TurnstileHelper::getSize();
```

## 🐛 Troubleshooting

### Lỗi thường gặp

1. **"Invalid site key"**
   - Kiểm tra TURNSTILE_SITE_KEY trong .env
   - Đảm bảo domain được cấu hình đúng trong Cloudflare

2. **"Secret key not found"**
   - Kiểm tra TURNSTILE_SECRET_KEY trong .env
   - Chạy `php artisan config:clear`

3. **"Verification failed"**
   - Kiểm tra network connection
   - Verify secret key đúng
   - Kiểm tra IP whitelist

### Debug Mode

```php
// Trong .env
APP_DEBUG=true

// Trong TurnstileRule
Log::info('Turnstile Response:', $response->json());
```

## 📊 Monitoring

### Cloudflare Analytics

1. Truy cập Cloudflare Dashboard
2. Chọn Turnstile site
3. Xem analytics và metrics
4. Monitor success/failure rates

### Laravel Logging

```php
// Log successful verifications
Log::info('Turnstile verification successful', [
    'ip' => request()->ip(),
    'user_agent' => request()->userAgent(),
]);

// Log failed verifications
Log::warning('Turnstile verification failed', [
    'ip' => request()->ip(),
    'response' => $turnstileResponse,
]);
```

## 🚀 Production Deployment

### Checklist

- [ ] Site key và secret key đã được cấu hình
- [ ] Domain đã được thêm vào Cloudflare Turnstile
- [ ] SSL certificate đã được cài đặt
- [ ] Rate limiting đã được cấu hình
- [ ] Monitoring đã được thiết lập
- [ ] Backup plan cho trường hợp Turnstile down

### Performance Optimization

```javascript
// Lazy load Turnstile script
const script = document.createElement('script');
script.src = 'https://challenges.cloudflare.com/turnstile/v0/api.js';
script.async = true;
script.defer = true;
document.head.appendChild(script);
```

## 📚 Tài liệu tham khảo

- [Cloudflare Turnstile Documentation](https://developers.cloudflare.com/turnstile/)
- [Laravel Validation Documentation](https://laravel.com/docs/validation)
- [HTTP Client Documentation](https://laravel.com/docs/http-client)

## 🤝 Đóng góp

Nếu bạn gặp vấn đề hoặc có đề xuất cải thiện, vui lòng tạo issue hoặc pull request.

---

**Lưu ý**: Đây là tài liệu cho môi trường development. Trong production, hãy đảm bảo tất cả các biến môi trường được cấu hình đúng và bảo mật. 