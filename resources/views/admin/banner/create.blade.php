@extends('layouts/admin/contentLayoutMaster')

@section('content')
    <h3>Thêm banner mới</h3>
    <form action="{{ route('admin.banners.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
            <label class="form-label">Tiêu đề</label>
            <input type="text" name="title" class="form-control" value="{{ old('title') }}">
            @error('title')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Mô tả</label>
            <textarea name="description" class="form-control">{{ old('description') }}</textarea>
            @error('description')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Hình ảnh (tối đa 5MB)</label>
            <div class="image-upload-container" id="uploadBox">
                <span class="upload-text">Click để chọn hình ảnh</span>
                <input type="file" name="image_path" id="imageInput" accept="image/*">
            </div>
            <img id="previewImage" class="image-preview" src="#" alt="Preview Image" />
        </div>

        <div class="mb-3">
            <label class="form-label">Liên kết (phải bắt đầu bằng https://)</label>
            <input type="url" name="link" class="form-control" value="{{ old('link') }}">
            @error('link')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror

        </div>

        <div class="mb-3">
            <label class="form-label">Ngày bắt đầu</label>
            <input type="date" name="start_at" class="form-control " value="{{ old('start_at') }}">
            @error('start_at')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Ngày kết thúc</label>
            <input type="date" name="end_at" class="form-control " value="{{ old('end_at') }}">
            @error('end_at')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Trạng thái</label>
            <select name="is_active" class="form-control">
                <option value="1">Hiển thị</option>
                <option value="0">Ẩn</option>
            </select>
        </div>

        <div class="d-flex">
            <button type="submit" class="btn btn-success mr-1">Lưu</button>
            <a href="{{ route('admin.banners.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Quay lại
            </a>
        </div>
    </form>
@endsection
<style>
    .image-upload-container {
        position: relative;
        border: 2px dashed #4CAF50;
        padding: 40px;
        text-align: center;
        transition: 0.3s ease-in-out;
        border-radius: 10px;
        background: linear-gradient(135deg, #f0fff4, #e8f5e9);
        cursor: pointer;
    }

    .image-upload-container:hover {
        box-shadow: 0 0 20px rgba(76, 175, 80, 0.6);
        transform: scale(1.02);
    }

    .image-upload-container input[type="file"] {
        opacity: 0;
        position: absolute;
        top: 0;
        left: 0;
        height: 100%;
        width: 100%;
        cursor: pointer;
    }

    .image-upload-container .upload-text {
        font-size: 16px;
        color: #4CAF50;
        font-weight: bold;
    }

    .image-preview {
        margin-top: 15px;
        display: none;
        max-height: 200px;
        border-radius: 10px;
        animation: fadeIn 0.7s ease-in-out;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: scale(0.95);
        }

        to {
            opacity: 1;
            transform: scale(1);
        }
    }
</style>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const input = document.getElementById('imageInput');
        const preview = document.getElementById('previewImage');
        const uploadBox = document.getElementById('uploadBox');

        input.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                if (file.size > 5 * 1024 * 1024) {
                    alert("Ảnh phải nhỏ hơn 5MB!");
                    this.value = "";
                    preview.style.display = "none";
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = "block";
                };
                reader.readAsDataURL(file);
                uploadBox.style.borderColor = "#2196F3";
                uploadBox.style.boxShadow = "0 0 15px rgba(33, 150, 243, 0.6)";
            }
        });
    });
</script>
