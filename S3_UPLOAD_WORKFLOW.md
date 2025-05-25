# Luồng hoạt động Upload AWS S3 - Laravel Project

## 🏗️ Tổng quan kiến trúc

```
User Interface (Frontend) 
    ↓
Laravel Controller (Backend)
    ↓  
AWS S3 Storage (Cloud)
```

## 📋 Chi tiết từng bước

### 1. **Frontend - Giao diện người dùng**

#### 1.1 Upload Form (`upload.blade.php`)
```html
<!-- Drag & Drop Area -->
<div class="upload-area" id="uploadArea">
    <input type="file" id="imageInput" name="image" accept="image/*">
</div>
```

#### 1.2 JavaScript Event Handlers
```javascript
// Xử lý click để chọn file
uploadArea.addEventListener('click', () => imageInput.click());

// Xử lý drag & drop
uploadArea.addEventListener('drop', (e) => {
    const files = e.dataTransfer.files;
    handleFileSelect(files[0]);
});
```

#### 1.3 File Validation (Client-side)
```javascript
function handleFileSelect(file) {
    // Kiểm tra loại file
    if (!file.type.startsWith('image/')) {
        alert('Vui lòng chọn file ảnh!');
        return;
    }
    
    // Kiểm tra kích thước (max 2MB)
    if (file.size > 2 * 1024 * 1024) {
        alert('File quá lớn!');
        return;
    }
    
    // Preview ảnh
    const reader = new FileReader();
    reader.onload = (e) => {
        previewImg.src = e.target.result;
    };
    reader.readAsDataURL(file);
}
```

#### 1.4 AJAX Upload Request
```javascript
// Gửi FormData đến Laravel
const formData = new FormData();
formData.append('image', fileInput.files[0]);
formData.append('_token', csrfToken);

fetch('/test/upload', {
    method: 'POST',
    body: formData
})
```

---

### 2. **Backend - Laravel Controller**

#### 2.1 Route Definition (`web.php`)
```php
Route::prefix('test')->name('test.')->group(function () {
    Route::post('/upload', [TestController::class, 'uploadImage']);
    Route::get('/images', [TestController::class, 'listImages']);
    Route::delete('/images', [TestController::class, 'deleteImage']);
});
```

#### 2.2 Controller Method (`TestController.php`)
```php
public function uploadImage(Request $request)
{
    // 1. Validation
    $request->validate([
        'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
    ]);

    // 2. Generate unique filename
    $filename = Str::uuid() . '.' . $image->getClientOriginalExtension();
    
    // 3. Upload to S3
    $path = Storage::disk('s3')->put('test-uploads/' . $filename, file_get_contents($image));
    
    // 4. Get public URL
    $url = Storage::disk('s3')->url('test-uploads/' . $filename);
    
    // 5. Return response
    return response()->json([
        'success' => true,
        'url' => $url,
        'filename' => $filename
    ]);
}
```

---

### 3. **AWS S3 Configuration**

#### 3.1 Laravel Filesystem Config (`config/filesystems.php`)
```php
's3' => [
    'driver' => 's3',
    'key' => env('AWS_ACCESS_KEY_ID'),
    'secret' => env('AWS_SECRET_ACCESS_KEY'),
    'region' => env('AWS_DEFAULT_REGION'),
    'bucket' => env('AWS_BUCKET'),
    'url' => env('AWS_URL'),
],
```

#### 3.2 Environment Variables (`.env`)
```env
AWS_ACCESS_KEY_ID=AKIA...
AWS_SECRET_ACCESS_KEY=xyz123...
AWS_DEFAULT_REGION=ap-southeast-1
AWS_BUCKET=my-laravel-uploads
AWS_URL=https://my-laravel-uploads.s3.ap-southeast-1.amazonaws.com
```

---

## 🔄 Luồng hoạt động chi tiết

### **Phase 1: User Interaction**
1. User mở trang `/test/upload`
2. User kéo thả file hoặc click chọn file
3. JavaScript validate file (type, size)
4. Hiển thị preview ảnh
5. User click "Upload"

### **Phase 2: Frontend Processing**
1. JavaScript tạo FormData object
2. Thêm CSRF token và file vào FormData
3. Gửi POST request đến `/test/upload`
4. Hiển thị progress bar

### **Phase 3: Backend Processing**
1. Laravel nhận request tại route `/test/upload`
2. TestController::uploadImage() được gọi
3. Validate file (server-side validation)
4. Generate UUID làm tên file unique
5. Gọi Storage::disk('s3')->put() để upload

### **Phase 4: AWS S3 Processing**
1. Laravel SDK gửi request đến AWS S3 API
2. AWS S3 nhận file và lưu vào bucket
3. AWS S3 trả về response confirmation
4. Laravel nhận response từ AWS

### **Phase 5: Response & Display**
1. Laravel tạo public URL cho file
2. Trả về JSON response với URL và metadata
3. Frontend nhận response
4. Hiển thị ảnh đã upload và link URL
5. Refresh danh sách ảnh

---

## 🛠️ Các thành phần kỹ thuật

### **Dependencies**
```json
{
    "league/flysystem-aws-s3-v3": "^3.0",
    "aws/aws-sdk-php": "^3.0"
}
```

### **Security Features**
- ✅ CSRF Protection
- ✅ File type validation
- ✅ File size limits
- ✅ IAM permissions
- ✅ S3 bucket policies

### **Error Handling**
```php
try {
    $path = Storage::disk('s3')->put($filename, $content);
} catch (\Exception $e) {
    return response()->json([
        'success' => false,
        'message' => 'Upload failed: ' . $e->getMessage()
    ], 500);
}
```

---

## 📊 Data Flow Diagram

```
┌─────────────┐    HTTP POST     ┌─────────────┐    AWS API     ┌─────────────┐
│   Browser   │ ──────────────► │   Laravel   │ ──────────────► │   AWS S3    │
│             │                 │ Controller  │                 │   Bucket    │
└─────────────┘                 └─────────────┘                 └─────────────┘
       ▲                               │                               │
       │         JSON Response         │                               │
       └───────────────────────────────┘                               │
                                       │                               │
                                       ▼                               │
                              ┌─────────────┐                         │
                              │  Database   │                         │
                              │ (Optional)  │                         │
                              └─────────────┘                         │
                                                                      │
       ┌─────────────┐    Public URL     ┌─────────────┐              │
       │    CDN      │ ◄──────────────── │ S3 Storage  │ ◄────────────┘
       │ (Optional)  │                   │             │
       └─────────────┘                   └─────────────┘
```

---

## 🔧 APIs Available

### **Web Routes**
- `GET /test/upload` - Upload form
- `POST /test/upload` - Upload image
- `GET /test/images` - List images
- `DELETE /test/images` - Delete image
- `GET /test/connection` - Test S3 connection

### **API Routes**
- `POST /api/test/upload` - Upload (for mobile/SPA)
- `GET /api/test/images` - List images (JSON)
- `DELETE /api/test/images/{filename}` - Delete specific image
- `GET /api/test/connection` - Test connection (JSON)

---

## 💡 Benefits

1. **Scalability**: S3 có thể handle unlimited storage
2. **Performance**: CDN integration cho faster loading
3. **Security**: IAM controls và bucket policies
4. **Cost-effective**: Pay per use model
5. **Reliability**: 99.999999999% durability
6. **Global**: Multiple regions available

---

## 🚀 Demo Flow

1. **Show the upload page**: `http://localhost:8000/test/upload`
2. **Test connection**: Click "Test kết nối S3"
3. **Upload image**: Drag & drop or click to select
4. **View result**: See uploaded image and public URL
5. **List images**: View all uploaded files
6. **Delete image**: Remove files from S3

---

## 📱 Mobile/API Integration

```javascript
// Example API call from mobile app
const formData = new FormData();
formData.append('image', imageFile);

fetch('/api/test/upload', {
    method: 'POST',
    headers: {
        'Authorization': 'Bearer ' + token,
    },
    body: formData
})
.then(response => response.json())
.then(data => {
    console.log('Image uploaded:', data.url);
});
```
