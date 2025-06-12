# Multi-Branch Logic Refactor

## Tổng quan
Đây là tài liệu mô tả việc refactor toàn bộ logic multi-branch trong hệ thống để cải thiện hiệu suất, bảo mật và khả năng bảo trì.

## Các thay đổi chính

### 1. Tạo BranchService (app/Services/BranchService.php)
- Tập trung hóa tất cả logic liên quan đến branch
- Caching để cải thiện hiệu suất
- Validation và error handling tốt hơn
- Các phương thức chính:
  - `getCurrentBranch()`: Lấy branch hiện tại
  - `setSelectedBranch()`: Đặt branch được chọn
  - `getActiveBranches()`: Lấy danh sách branch active (có cache)
  - `isValidBranch()`: Kiểm tra branch có hợp lệ không
  - `clearSelectedBranch()`: Xóa branch đã chọn
  - `findNearestBranch()`: Tìm branch gần nhất

### 2. Tạo BranchMiddleware (app/Http/Middleware/BranchMiddleware.php)
Thay thế `CheckSelectedBranch` middleware cũ với các cải tiến:
- **Performance**: Skip middleware cho các route không cần thiết (API, admin, static assets)
- **Validation**: Kiểm tra branch có active không, tự động clear nếu inactive
- **Error Handling**: Xử lý lỗi tốt hơn với logging
- **Caching**: Sử dụng cache cho active branches

### 3. Cập nhật Controllers
Refactor các controller để sử dụng `BranchService` thay vì truy cập session trực tiếp:

#### Customer Controllers:
- `Customer\ProductController`: Inject BranchService, sử dụng `getCurrentBranch()`
- `Customer\HomeController`: Tương tự ProductController
- `Api\Customer\CartController`: Thêm validation branch active
- `Api\Customer\ProductController`: Sử dụng BranchService
- `Api\Customer\BranchController`: Đã được refactor trước đó

### 4. Cập nhật Views
Refactor các Blade template để sử dụng biến shared từ middleware thay vì session trực tiếp:

#### Biến shared từ middleware:
- `$currentBranch`: Branch hiện tại (object)
- `$branches`: Danh sách active branches (cached)
- `$hasBranchSelected`: Boolean cho biết có branch được chọn không

#### Files đã cập nhật:
- `partials/customer/branch-selector-modal.blade.php`
- `layouts/customer/fullLayoutMaster.blade.php`
- `customer/shop/index.blade.php`
- `customer/shop/show.blade.php`

### 5. Cập nhật Bootstrap (bootstrap/app.php)
Thay thế `CheckSelectedBranch` bằng `BranchMiddleware` trong:
- Middleware alias
- Web middleware stack

## Lợi ích của refactor

### 1. Performance
- **Reduced Database Queries**: Cache active branches
- **Selective Execution**: Skip middleware cho routes không cần thiết
- **Optimized Queries**: Sử dụng eager loading và query optimization

### 2. Security & Reliability
- **Active Branch Validation**: Tự động kiểm tra và clear inactive branches
- **Error Recovery**: Xử lý lỗi gracefully, không break user experience
- **Race Condition Prevention**: Proper session handling

### 3. Maintainability
- **Centralized Logic**: Tất cả logic branch trong BranchService
- **Consistent API**: Unified interface cho branch operations
- **Better Testing**: Service có thể test dễ dàng
- **Clean Views**: Views không còn logic phức tạp

### 4. Developer Experience
- **Clear Separation**: Business logic tách khỏi presentation
- **Reusable Code**: BranchService có thể dùng ở nhiều nơi
- **Better Debugging**: Centralized logging và error handling

## Migration Notes

### Backward Compatibility
- Các API endpoints giữ nguyên interface
- Session structure không thay đổi
- Cookie handling tương thích

### Testing
Sau khi deploy, cần test:
1. Branch selection flow
2. Cart behavior khi đổi branch
3. Product availability theo branch
4. Performance improvement
5. Error handling scenarios

### Monitoring
Theo dõi:
- Database query count reduction
- Page load time improvement
- Error rates
- Cache hit rates

## Future Improvements

1. **Real-time Updates**: WebSocket cho branch status changes
2. **Advanced Caching**: Redis cache cho large scale
3. **A/B Testing**: Branch-specific features
4. **Analytics**: Branch performance metrics
5. **Mobile Optimization**: Location-based branch suggestions

## Rollback Plan
Nếu có vấn đề, có thể rollback bằng cách:
1. Revert `bootstrap/app.php` để dùng lại `CheckSelectedBranch`
2. Revert các controller changes
3. Revert view changes
4. Keep `BranchService` for future use

---

**Lưu ý**: Refactor này là bước đầu tiên trong việc modernize hệ thống. Các improvement khác sẽ được thực hiện dần dần.