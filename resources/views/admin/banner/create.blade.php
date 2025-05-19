@extends('layouts/admin/contentLayoutMaster')

@section('content')
    <div class="banner-form-container">
        <h1 class="banner-form-title">Thêm Banner Mới</h1>

        <form class="banner-form" action="{{ route('admin.banners.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="banner-form-group">
                <label class="banner-form-label" for="title">Tiêu đề banner</label>
                <input class="banner-form-input @error('title') is-invalid @enderror" type="text" id="title"
                    name="title" value="{{ old('title') }}">
                @error('title')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="banner-form-group">
                <label class="banner-form-label">Ảnh banner</label>
                
                <div class="banner-form-tabs">
                    <div class="banner-form-tab active" data-tab="upload">Upload ảnh</div>
                    <div class="banner-form-tab" data-tab="link">Nhập link ảnh</div>
                </div>
                
                <div class="banner-form-tab-content active" data-tab-content="upload">
                    <div class="banner-form-file-wrapper">
                        <label class="banner-form-file-button" for="image_path">Chọn file ảnh</label>
                        <input class="banner-form-file @error('image_path') is-invalid @enderror" type="file" id="image_path"
                            name="image_path" accept="image/*">
                        @error('image_path')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="banner-form-preview">
                        <img class="banner-form-preview-img" id="image-preview" style="display: none;">
                        <div class="banner-form-preview-placeholder" id="preview-placeholder">Xem trước ảnh banner</div>
                    </div>
                </div>
                
                <div class="banner-form-tab-content" data-tab-content="link">
                    <input class="banner-form-input @error('image_link') is-invalid @enderror" type="url" 
                        id="image_link" name="image_link" placeholder="Nhập link ảnh" value="{{ old('image_link') }}">
                    @error('image_path')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                    <div class="banner-form-preview">
                        <img class="banner-form-preview-img" id="link-preview" style="display: none;">
                        <div class="banner-form-preview-placeholder" id="link-placeholder">Xem trước ảnh từ link</div>
                    </div>
                </div>
            </div>

            <div class="banner-form-group">
                <label class="banner-form-label" for="description">Mô tả banner</label>
                <textarea class="banner-form-textarea @error('description') is-invalid @enderror" id="description" name="description">{{ old('description') }}</textarea>
                @error('description')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="banner-form-group">
                <label class="banner-form-label" for="link">Link khi click banner</label>
                <input class="banner-form-input @error('link') is-invalid @enderror" type="url" id="link"
                    name="link" placeholder=" `https://example.com` " value="{{ old('link') }}">
                @error('link')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="banner-form-row">
                <div class="banner-form-group">
                    <label class="banner-form-label" for="is_active">Trạng thái hiển thị</label>
                    <select class="banner-form-select @error('is_active') is-invalid @enderror" id="is_active"
                        name="is_active">
                        <option value="1" {{ old('is_active') == '1' ? 'selected' : '' }}>Hiển thị</option>
                        <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>Ẩn</option>
                    </select>
                    @error('is_active')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="banner-form-group">
                    <label class="banner-form-label" for="order">Vị trí hiển thị</label>
                    <select class="banner-form-select @error('order') is-invalid @enderror" id="order" name="order">
                        <option value="0" {{ old('order') == '0' ? 'selected' : '' }}>Đầu trang</option>
                        <option value="1" {{ old('order') == '1' ? 'selected' : '' }}>Giữa trang</option>
                        <option value="2" {{ old('order') == '2' ? 'selected' : '' }}>Cuối trang</option>
                    </select>
                    @error('order')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="banner-form-row">
                <div class="banner-form-group">
                    <label class="banner-form-label" for="start_at">Thời gian bắt đầu hiển thị</label>
                    <input class="banner-form-input @error('start_at') is-invalid @enderror" type="date" id="start_at"
                        name="start_at" value="{{ old('start_at') }}">
                    @error('start_at')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="banner-form-group">
                    <label class="banner-form-label" for="end_at">Thời gian kết thúc hiển thị</label>
                    <input class="banner-form-input @error('end_at') is-invalid @enderror" type="date" id="end_at"
                        name="end_at" value="{{ old('end_at') }}">
                    @error('end_at')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <button class="banner-form-button" type="submit">Lưu Banner</button>
        </form>
    @endsection
    <style>
        .is-invalid {
            border-color: #dc3545 !important;
        }

        .invalid-feedback {
            display: block;
            width: 100%;
            margin-top: 0.25rem;
            font-size: 0.875em;
            color: #dc3545;
        }

        .banner-form-container {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            background-color: #ffffff;
        }

        .banner-form-title {
            color: #333;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }

        .banner-form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .banner-form-group {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .banner-form-label {
            font-weight: bold;
            color: #555;
        }

        .banner-form-input,
        .banner-form-textarea,
        .banner-form-select {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            width: 100%;
        }

        .banner-form-textarea {
            min-height: 100px;
            resize: vertical;
        }

        .banner-form-input:focus,
        .banner-form-textarea:focus,
        .banner-form-select:focus {
            outline: none;
            border-color: #4a90e2;
            box-shadow: 0 0 3px rgba(74, 144, 226, 0.3);
        }

        .banner-form-row {
            display: flex;
            gap: 15px;
        }

        .banner-form-row .banner-form-group {
            flex: 1;
        }

        .banner-form-button {
            background-color: #4a90e2;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            margin-top: 10px;
            transition: background-color 0.3s;
        }

        .banner-form-button:hover {
            background-color: #3a7bc8;
        }

        .banner-form-preview {
            margin-top: 15px;
            border: 1px dashed #ddd;
            padding: 15px;
            border-radius: 4px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 150px;
            width: 100%;
            box-sizing: border-box;
        }

        .banner-form-preview-img {
            width: 100%;
            height: 100%;
            max-height: 300px;
            object-fit: contain;
            display: none;
        }

        .banner-form-tabs {
            display: flex;
            margin-bottom: 10px;
        }

        .banner-form-tab {
            padding: 8px 15px;
            background-color: #f5f5f5;
            border: 1px solid #ddd;
            cursor: pointer;
            border-radius: 4px 4px 0 0;
            margin-right: 5px;
        }

        .banner-form-tab.active {
            background-color: #fff;
            border-bottom-color: #fff;
            font-weight: bold;
        }

        .banner-form-tab-content {
            display: none;
        }

        .banner-form-tab-content.active {
            display: block;
        }

        .banner-form-file-wrapper {
            position: relative;
            overflow: hidden;
            display: inline-block;
            width: 100%;
        }

        .banner-form-file-button {
            background-color: #f5f5f5;
            color: #333;
            border: 1px solid #ddd;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
            display: inline-block;
            text-align: center;
            width: 100%;
        }

        .banner-form-file-button:hover {
            background-color: #e9e9e9;
        }

        .banner-form-file {
            position: absolute;
            font-size: 100px;
            right: 0;
            top: 0;
            opacity: 0;
            cursor: pointer;
        }

        .banner-form-file-name {
            margin-top: 5px;
            font-size: 14px;
            color: #666;
        }

        .banner-form-preview-placeholder {
            color: #999;
            text-align: center;
        }

        @media (max-width: 768px) {
            .banner-form-row {
                flex-direction: column;
            }
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Tab switching
            const tabs = document.querySelectorAll('.banner-form-tab');
            tabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    const tabName = this.getAttribute('data-tab');
                    
                    // Update active tab
                    tabs.forEach(t => t.classList.remove('active'));
                    this.classList.add('active');
                    
                    // Update active content
                    document.querySelectorAll('.banner-form-tab-content').forEach(content => {
                        content.classList.remove('active');
                    });
                    document.querySelector(`.banner-form-tab-content[data-tab-content="${tabName}"]`)
                        .classList.add('active');
                });
            });
            
            // Handle form submission
            document.querySelector('.banner-form').addEventListener('submit', function(e) {
                const activeTab = document.querySelector('.banner-form-tab.active').getAttribute('data-tab');
                
                if (activeTab === 'upload') {
                    // Remove image_link field if uploading file
                    document.getElementById('image_link').disabled = true;
                    document.getElementById('image_link').value = '';
                } else {
                    // Remove image_path field if using link
                    document.getElementById('image_path').disabled = true;
                    // Đảm bảo không có file được chọn
                    document.getElementById('image_path').value = '';
                }
            });
            
            // Image upload preview
            const imageInput = document.getElementById('image_path');
            const imagePreview = document.getElementById('image-preview');
            const previewPlaceholder = document.getElementById('preview-placeholder');
            
            imageInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                
                if (file) {
                    const reader = new FileReader();
                    
                    reader.onload = function(e) {
                        imagePreview.src = e.target.result;
                        imagePreview.style.display = 'block';
                        imagePreview.style.maxWidth = '100%';
                        imagePreview.style.maxHeight = '300px';
                        imagePreview.style.objectFit = 'contain';
                        imagePreview.style.marginTop = '10px';
                        previewPlaceholder.style.display = 'none';
                    };
                    
                    reader.readAsDataURL(file);
                } else {
                    imagePreview.src = '';
                    imagePreview.style.display = 'none';
                    previewPlaceholder.style.display = 'block';
                }
            });
            
            // Link image preview
            const imageLink = document.getElementById('image_link');
            const linkPreview = document.getElementById('link-preview');
            const linkPlaceholder = document.getElementById('link-placeholder');
            
            imageLink.addEventListener('input', function(e) {
                const url = this.value.trim();
                
                if (url) {
                    linkPreview.src = url;
                    linkPreview.style.display = 'block';
                    linkPreview.style.maxWidth = '100%';
                    linkPreview.style.maxHeight = '300px';
                    linkPreview.style.objectFit = 'contain';
                    linkPreview.style.marginTop = '10px';
                    linkPlaceholder.style.display = 'none';
                } else {
                    linkPreview.src = '';
                    linkPreview.style.display = 'none';
                    linkPlaceholder.style.display = 'block';
                }
            });
        });
    </script>
