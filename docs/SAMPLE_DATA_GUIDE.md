# 🛠️ Hướng dẫn sử dụng Sample Data cho Form Đăng ký Tài xế

## 📋 Tổng quan

System Sample Data giúp bạn nhanh chóng điền thông tin mẫu vào form đăng ký tài xế để testing và demo, thay vì phải điền thủ công tất cả các trường.

## 🚀 Cách kích hoạt

### Tự động kích hoạt (Development)
- ✅ `localhost` hoặc `127.0.0.1`
- ✅ URL có parameter `?sample=true`
- ✅ LocalStorage có `enableSampleData=true`

### Kích hoạt thủ công
```javascript
// Trong console browser
localStorage.setItem('enableSampleData', 'true');
// Reload trang
```

## 🎯 Tính năng

### 📊 3 Template dữ liệu có sẵn

#### 👨 Template 1 - Nam (Xe máy)
- Họ tên: Nguyễn Văn Nam
- Phương tiện: Honda Winner X (Xe máy)
- Thành phố: Hồ Chí Minh

#### 👩 Template 2 - Nữ (Ô tô)
- Họ tên: Trần Thị Hoa  
- Phương tiện: Toyota Vios (Ô tô)
- Thành phố: Đà Nẵng

#### 🚴 Template 3 - Xe đạp
- Họ tên: Lê Minh Tuan
- Phương tiện: Giant ATX 830 (Xe đạp)
- Thành phố: Huế

### 🎛️ Control Panel

Khi được kích hoạt, sẽ xuất hiện panel điều khiển ở góc dưới bên phải với các nút:

- **👨 Mẫu Nam (Xe máy)** - Điền template 1
- **👩 Mẫu Nữ (Ô tô)** - Điền template 2  
- **🚴 Mẫu Xe đạp** - Điền template 3
- **📁 Tạo file mẫu** - Tạo file dummy cho upload
- **🗑️ Xóa toàn bộ** - Reset form về trạng thái ban đầu
- **❌ Ẩn panel** - Ẩn/hiện control panel

## 📝 Console Commands

Bạn có thể sử dụng các lệnh sau trong Console của browser:

```javascript
// Điền dữ liệu mẫu
fillSampleData('template1'); // hoặc template2, template3

// Tạo file mẫu cho upload
createSampleFiles();

// Xóa toàn bộ form
clearFormData();

// Ẩn/hiện control panel
toggleControlPanel();
```

## ⚙️ Cấu hình

### Thêm template mới

Chỉnh sửa file `public/js/driver-application-sample.js`:

```javascript
const sampleData = {
    // ... existing templates
    template4: {
        full_name: "Tên mới",
        email: "email@example.com",
        // ... thêm fields khác
    }
};
```

### Tùy chỉnh điều kiện hiển thị

```javascript
// Trong driver-application-sample.js
const showSampleControls = 
    window.location.hostname === 'localhost' || 
    window.location.hostname === '127.0.0.1' ||
    localStorage.getItem('enableSampleData') === 'true' ||
    window.location.search.includes('sample=true') ||
    // Thêm điều kiện của bạn ở đây
    window.location.search.includes('dev=true');
```

## 🎨 Custom Styling

CSS styling được định nghĩa trong `public/css/sample-control.css`. Bạn có thể:

- Thay đổi vị trí panel
- Tùy chỉnh màu sắc buttons
- Thêm animation mới
- Responsive cho mobile

## 🔧 Troubleshooting

### Panel không hiển thị?
1. ✅ Kiểm tra bạn đang ở localhost/development
2. ✅ Thêm `?sample=true` vào URL
3. ✅ Set localStorage: `localStorage.setItem('enableSampleData', 'true')`
4. ✅ Reload trang

### Files upload không hoạt động?
- File mẫu chỉ tạo empty files để test
- Nếu cần file thật, hãy upload manual sau khi điền data

### Data không được điền?
- ✅ Kiểm tra tên field trong HTML trùng với tên trong sampleData
- ✅ Mở Console để xem error logs
- ✅ Đảm bảo JavaScript không bị block

## 📱 Mobile Support

Panel tự động responsive trên mobile:
- Chiếm full width màn hình
- Buttons sắp xếp vertical
- Touch-friendly sizes

## 🚀 Production Notes

⚠️ **Quan trọng**: System này chỉ hiển thị trong development environment.

Để đảm bảo không hiển thị trong production:
1. ✅ Không set `enableSampleData` trong production
2. ✅ Không thêm `?sample=true` trong production URLs
3. ✅ System tự động detect localhost

## 💡 Best Practices

1. **Testing Forms**: Dùng để test validation và flow
2. **Demo**: Hoàn hảo cho việc demo features
3. **Development**: Tiết kiệm thời gian fill forms
4. **QA**: Standardize test data across team

## 🎯 Use Cases

- ✅ **Development**: Nhanh chóng test form
- ✅ **QA Testing**: Chuẩn hóa dữ liệu test
- ✅ **Demo**: Trình bày features cho client
- ✅ **Training**: Hướng dẫn team mới

---

🔗 **Related Files:**
- `public/js/driver-application-sample.js` - Main logic
- `public/css/sample-control.css` - Styling
- `resources/views/customer/hiring/application.blade.php` - Form page 