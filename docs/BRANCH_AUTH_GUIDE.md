# Hướng dẫn Authentication cho Branch

## Tổng quan

Hệ thống authentication cho Branch đã được tách riêng khỏi Admin, tương tự như cách làm với Customer và Driver. Branch giờ có hệ thống đăng nhập/đăng xuất riêng biệt.

## Cấu trúc mới

### 1. Controllers
- `app/Http/Controllers/Branch/Auth/AuthController.php` - Xử lý đăng nhập/đăng xuất cho branch

### 2. Views
- `resources/views/branch/auth/login.blade.php` - Form đăng nhập branch
- `resources/views/layouts/branch/auth.blade.php` - Layout cho trang auth
- `resources/views/layouts/branch/contentLayoutMaster.blade.php` - Layout chính cho branch dashboard
- `resources/views/partials/branch/header.blade.php` - Header riêng cho branch

### 3. Middleware
- `app/Http/Middleware/Branch/BranchAuth.php` - Middleware kiểm tra authentication cho branch

### 4. Routes
- Routes authentication được định nghĩa trong `routes/branch.php`

## Cách sử dụng

### 1. Đăng nhập Branch
- URL: `/branch/login`
- Chỉ user có role `manager` mới có thể đăng nhập
- Sau khi đăng nhập thành công sẽ chuyển đến `/branch/dashboard`

### 2. Đăng xuất Branch
- URL: `/branch/logout` (POST)
- Sau khi đăng xuất sẽ chuyển về `/branch/login`

### 3. Truy cập các trang Branch
- Tất cả các trang branch đều được bảo vệ bởi middleware `auth:manager` và `role:manager`
- Nếu chưa đăng nhập sẽ tự động chuyển về trang login

## Thay đổi so với trước

### 1. Admin AuthController
- Đã loại bỏ phần xử lý manager khỏi Admin AuthController
- Admin chỉ xử lý user có role `spadmin`

### 2. Branch AuthController
- Chỉ xử lý user có role `manager`
- Sử dụng guard `manager`
- Có thông báo lỗi riêng cho branch

### 3. Layout
- Branch sử dụng layout riêng nhưng vẫn dùng chung sidebar và footer với admin
- Header riêng với logout button trỏ đến `branch.logout`

## Bảo mật

### 1. Rate Limiting
- Giới hạn 5 lần đăng nhập sai trong 60 giây
- Hiển thị countdown khi bị khóa

### 2. Session Management
- Sử dụng guard riêng `manager`
- Regenerate session sau khi đăng nhập thành công
- Invalidate session khi đăng xuất

### 3. Role-based Access
- Chỉ user có role `manager` mới có thể truy cập
- Middleware kiểm tra cả authentication và role

## Troubleshooting

### 1. Lỗi "Bạn không có quyền truy cập"
- Kiểm tra user có role `manager` không
- Đảm bảo user được assign đúng role

### 2. Lỗi redirect loop
- Kiểm tra middleware configuration trong `bootstrap/app.php`
- Đảm bảo routes được định nghĩa đúng

### 3. Lỗi layout không hiển thị
- Kiểm tra file layout có tồn tại không
- Đảm bảo path trong `@extends()` đúng

## Testing

### 1. Test đăng nhập
```bash
# Test với user manager
curl -X POST /branch/login -d "email=manager@example.com&password=password"

# Test với user không có quyền
curl -X POST /branch/login -d "email=admin@example.com&password=password"
```

### 2. Test middleware
```bash
# Test truy cập trang được bảo vệ khi chưa đăng nhập
curl /branch/dashboard
# Should redirect to /branch/login
```

## Lưu ý

1. **Guard Configuration**: Đảm bảo guard `manager` được cấu hình đúng trong `config/auth.php`
2. **Role System**: Hệ thống sử dụng Spatie Laravel Permission, đảm bảo role `manager` đã được tạo
3. **Database**: Kiểm tra bảng `users` có cột `role` hoặc relationship với bảng roles
4. **Cache**: Clear cache sau khi thay đổi middleware: `php artisan config:clear` 