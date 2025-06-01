@extends('layouts.admin.contentLayoutMaster')

@section('content')
    <style>
        /* Existing CSS styles remain unchanged */
        :root {
            --primary: #4361ee;
            --primary-light: #4895ef;
            --primary-dark: #3f37c9;
            --secondary: #4cc9f0;
            --success: #4ade80;
            --danger: #f43f5e;
            --warning: #f59e0b;
            --info: #3b82f6;
            --light: #f9fafb;
            --dark: #1f2937;
            --gray: #6b7280;
            --gray-light: #e5e7eb;
            --gray-dark: #4b5563;
            --white: #ffffff;
            --black: #000000;

            --border-radius: 12px;
            --border-radius-sm: 8px;
            --border-radius-lg: 16px;
            --border-radius-xl: 24px;
            --border-radius-full: 9999px;

            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-md: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --shadow-lg: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);

            --transition: all 0.3s ease;
            --transition-fast: all 0.15s ease;
            --transition-slow: all 0.5s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', 'Segoe UI', Roboto, -apple-system, BlinkMacSystemFont, sans-serif;
            color: var(--dark);
            background-color: #f5f7fa;
            line-height: 1.5;
        }

        .branch-form-container {
            max-width: 1280px;
            margin: 0 auto;
            padding: 2rem 1rem;
        }

        h1, h2, h3, h4, h5, h6 {
            margin: 0;
            font-weight: 600;
            line-height: 1.2;
        }

        h1 { font-size: 1.5rem; }
        h2 { font-size: 1.25rem; }
        h3 { font-size: 1.125rem; }
        h4 { font-size: 1rem; }
        p { margin: 0; line-height: 1.5; }
        a { color: var(--primary); text-decoration: none; transition: var(--transition-fast); }
        a:hover { color: var(--primary-dark); }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.5rem 1rem;
            border-radius: var(--border-radius);
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition-fast);
            border: none;
            font-size: 0.875rem;
            gap: 0.5rem;
        }

        .btn-sm { padding: 0.375rem 0.75rem; font-size: 0.75rem; }
        .btn-block { width: 100%; }
        .btn-primary { background-color: var(--primary); color: var(--white); }
        .btn-primary:hover { background-color: var(--primary-dark); color: var(--white); }
        .btn-outline { background-color: transparent; color: var(--gray-dark); border: 1px solid var(--gray-light); }
        .btn-outline:hover { background-color: var(--gray-light); color: var(--dark); }
        .btn-danger { background-color: var(--danger); color: var(--white); }
        .btn-danger:hover { background-color: #e11d48; color: var(--white); }
        .btn:disabled { opacity: 0.7; cursor: not-allowed; }

        .page-header { margin-bottom: 1.5rem; }
        .header-content { display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem; }
        .header-left { display: flex; align-items: center; gap: 1rem; }
        .header-icon { width: 3rem; height: 3rem; background-color: rgba(67, 97, 238, 0.1); color: var(--primary); border-radius: var(--border-radius-full); display: flex; align-items: center; justify-content: center; font-size: 1.25rem; }
        .header-text p { color: var(--gray); margin-top: 0.25rem; }
        .header-actions { display: flex; gap: 0.75rem; }

        .card { background-color: var(--white); border-radius: var(--border-radius-lg); box-shadow: var(--shadow); overflow: hidden; transition: var(--transition); margin-bottom: 1.5rem; }
        .card:hover { box-shadow: var(--shadow-md); transform: translateY(-2px); }
        .card-header { display: flex; align-items: center; padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--gray-light); gap: 0.75rem; }
        .card-icon { width: 2.5rem; height: 2.5rem; background-color: rgba(67, 97, 238, 0.1); color: var(--primary); border-radius: var(--border-radius-full); display: flex; align-items: center; justify-content: center; font-size: 1rem; }
        .card-header h3 { flex-grow: 1; }
        .card-actions { display: flex; gap: 0.5rem; }
        .card-body { padding: 1.5rem; }

        .form-grid { display: grid; grid-template-columns: 1fr; gap: 1.5rem; }
        @media (min-width: 992px) { .form-grid { grid-template-columns: 2fr 1fr; } }
        .form-group { margin-bottom: 1.25rem; }
        .form-group:last-child { margin-bottom: 0; }
        .form-label { display: block; margin-bottom: 0.5rem; font-weight: 500; }
        .form-label-icon { display: flex; align-items: center; gap: 0.5rem; }
        .form-control { display: block; width: 100%; padding: 0.625rem 0.75rem; font-size: 0.875rem; line-height: 1.5; color: var(--dark); background-color: var(--white); background-clip: padding-box; border: 1px solid var(--gray-light); border-radius: var(--border-radius); transition: var(--transition-fast); }
        .form-control:focus { border-color: var(--primary-light); outline: 0; box-shadow: 0 0 0 2px rgba(67, 97, 238, 0.25); }
        select.form-control { appearance: none; background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e"); background-position: right 0.5rem center; background-repeat: no-repeat; background-size: 1.5em 1.5em; padding-right: 2.5rem; }
        textarea.form-control { min-height: 100px; resize: vertical; }
        .form-hint { margin-top: 0.375rem; font-size: 0.75rem; color: var(--gray); }
        .form-error { margin-top: 0.375rem; font-size: 0.75rem; color: var(--danger); }
        .form-check { display: flex; align-items: center; gap: 0.5rem; padding: 1rem; border: 1px solid var(--gray-light); border-radius: var(--border-radius); }
        .form-check-content { flex-grow: 1; }
        .form-check-label { font-weight: 500; }
        .form-check-hint { font-size: 0.875rem; color: var(--gray); }

        .switch { position: relative; display: inline-block; width: 44px; height: 24px; }
        .switch input { opacity: 0; width: 0; height: 0; }
        .slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: var(--gray-light); transition: var(--transition-fast); border-radius: 34px; }
        .slider:before { position: absolute; content: ""; height: 18px; width: 18px; left: 3px; bottom: 3px; background-color: white; transition: var(--transition-fast); border-radius: 50%; }
        input:checked + .slider { background-color: var(--primary); }
        input:focus + .slider { box-shadow: 0 0 1px var(--primary); }
        input:checked + .slider:before { transform: translateX(20px); }

        .grid { display: grid; gap: 1.5rem; }
        .grid-2 { grid-template-columns: 1fr; }
        @media (min-width: 768px) { .grid-2 { grid-template-columns: 1fr 1fr; } }

        .upload-label { cursor: pointer; display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem 0.75rem; background-color: var(--gray-light); border-radius: var(--border-radius); font-size: 0.875rem; transition: var(--transition-fast); }
        .upload-label:hover { background-color: var(--gray); color: var(--white); }
        .upload-input { display: none; }
        .image-preview-grid {
            display: flex;
            flex-wrap: nowrap;
            gap: 1rem;
            overflow-x: auto;
            padding-bottom: 0.5rem;
            scroll-behavior: smooth;
        }
        .image-preview-item {
            position: relative;
            border-radius: var(--border-radius);
            overflow: hidden;
            flex: 0 0 auto;
            width: 150px;
            aspect-ratio: 4/3;
            box-shadow: var(--shadow-sm);
        }
        .image-preview-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .image-preview-overlay {
            position: absolute;
            inset: 0;
            background-color: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: var(--transition);
        }
        .image-preview-item:hover .image-preview-overlay {
            opacity: 1;
        }
        .image-preview-actions {
            display: flex;
            gap: 0.5rem;
        }
        .image-preview-btn {
            width: 2rem;
            height: 2rem;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: var(--white);
            color: var(--dark);
            border: none;
            cursor: pointer;
            transition: var(--transition-fast);
        }
        .image-preview-btn:hover {
            transform: scale(1.1);
        }
        .image-preview-btn.remove-btn:hover {
            background-color: var(--danger);
            color: var(--white);
        }
        .image-preview-btn.primary-btn {
            background-color: var(--warning);
            color: var(--white);
        }
        .image-preview-btn.set-primary-btn:hover {
            background-color: var(--warning);
            color: var(--white);
        }
        .image-preview-badge {
            position: absolute;
            top: 0.5rem;
            left: 0.5rem;
            padding: 0.25rem 0.5rem;
            background-color: var(--warning);
            color: var(--white);
            border-radius: var(--border-radius-full);
            font-size: 0.625rem;
            font-weight: 600;
        }

        .empty-state { text-align: center; padding: 3rem 1rem; }
        .empty-icon { width: 4rem; height: 4rem; background-color: var(--gray-light); color: var(--gray); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; margin: 0 auto 1rem; }
        .empty-title { margin-bottom: 0.5rem; font-weight: 500; }
        .empty-text { color: var(--gray); margin-bottom: 1.5rem; }

        .preview-card { padding: 1rem; background-color: rgba(67, 97, 238, 0.05); border-radius: var(--border-radius); border: 1px solid rgba(67, 97, 238, 0.1); margin-bottom: 1rem; }
        .preview-header { display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.75rem; }
        .preview-title { font-weight: 500; }
        .preview-item { display: flex; align-items: flex-start; gap: 0.5rem; margin-bottom: 0.5rem; font-size: 0.875rem; color: var(--gray-dark); }
        .preview-item i { margin-top: 0.25rem; }
        .preview-hours { display: flex; gap: 1rem; margin-top: 1rem; }
        .preview-hour { flex: 1; padding: 0.75rem; border-radius: var(--border-radius); text-align: center; }
        .preview-hour.opening { background-color: rgba(74, 222, 128, 0.1); border: 1px solid rgba(74, 222, 128, 0.2); }
        .preview-hour.closing { background-color: rgba(244, 63, 94, 0.1); border: 1px solid rgba(244, 63, 94, 0.2); }
        .preview-hour-label { font-size: 0.75rem; color: var(--gray); margin-bottom: 0.25rem; }
        .preview-hour-value.opening { color: var(--success); font-weight: 500; }
        .preview-hour-value.closing { color: var(--danger); font-weight: 500; }
        .preview-status { display: flex; align-items: center; justify-content: space-between; padding: 0.75rem; border-radius: var(--border-radius); border: 1px solid var(--gray-light); margin-top: 1rem; }
        .preview-status-label { font-weight: 500; }
        .preview-status-value { padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 500; }
        .preview-status-value.active { background-color: rgba(74, 222, 128, 0.1); color: var(--success); }
        .preview-status-value.inactive { background-color: rgba(244, 63, 94, 0.1); color: var(--danger); }

        #map {
            height: 300px !important;
            width: 100%;
            border-radius: var(--border-radius);
            margin-bottom: 1rem;
            min-height: 300px;
            background-color: #f0f0f0;
        }
        .map-coordinates { display: flex; gap: 1rem; }
        .map-hint { margin-top: 0.5rem; font-size: 0.75rem; color: var(--gray); }

        .mb-6 { margin-bottom: 1.5rem; }
        .space-y-6 > * + * { margin-top: 1.5rem; }
        .flex { display: flex; }
        .items-center { align-items: center; }
        .justify-between { justify-content: space-between; }
        .gap-2 { gap: 0.5rem; }
        .gap-3 { gap: 0.75rem; }
        .gap-4 { gap: 1rem; }
        .flex-wrap { flex-wrap: wrap; }
        .hidden { display: none; }
        .text-center { text-align: center; }
        .text-sm { font-size: 0.875rem; }
        .text-xs { font-size: 0.75rem; }
        .text-gray { color: var(--gray); }
        .text-primary { color: var(--primary); }
        .text-success { color: var(--success); }
        .text-danger { color: var(--danger); }
        .font-medium { font-weight: 500; }
        .font-semibold { font-weight: 600; }
        .w-full { width: 100%; }
        .badge { display: inline-block; padding: 0.25rem 0.5rem; border-radius: var(--border-radius-full); font-size: 0.75rem; font-weight: 500; }
        .badge-info { background-color: rgba(59, 130, 246, 0.1); color: var(--info); }
    </style>
</head>
<body>
<div class="branch-form-container">
    <div class="mb-6">
        <div class="flex justify-between items-center flex-wrap gap-4">
            <div class="flex items-center gap-4">
                <div class="header-icon">
                    <i class="fas fa-building"></i>
                </div>
                <div class="header-text">
                    <h1>Chỉnh sửa chi nhánh</h1>
                    <p>Cập nhật thông tin chi nhánh {{ $branch->name }}</p>
                </div>
            </div>
            <div class="header-actions">
                <a href="{{ route('admin.branches.index') }}" class="btn btn-outline">
                    <i class="fas fa-arrow-left"></i>
                    <span>Quay lại</span>
                </a>
            </div>
        </div>
    </div>
@if ($errors->any())
    <div class="alert alert-danger" style="background-color: #fef2f2; border: 1px solid #fee2e2; border-radius: 8px; padding: 1rem; margin-bottom: 1.5rem;">
        <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
            <i class="fas fa-exclamation-circle" style="color: #dc2626;"></i>
            <h4 style="color: #dc2626; font-weight: 600; margin: 0;">Đã xảy ra lỗi!</h4>
        </div>
        <ul style="list-style-type: none; margin: 0; padding: 0;">
            @foreach ($errors->all() as $error)
                <li style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem;">
                    <i class="fas fa-times" style="margin-right: 0.5rem;"></i>
                    {{ $error }}
                </li>
            @endforeach
        </ul>
    </div>
@endif

    <form id="branchForm" action="{{ route('admin.branches.update', $branch->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="form-grid">
            <div class="space-y-6">
                <div class="card">
                    <div class="card-header">
                        <div class="card-icon">
                            <i class="fas fa-info-circle"></i>
                        </div>
                        <h3>Thông tin cơ bản</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="name" class="form-label form-label-icon">
                                <i class="fas fa-building text-primary"></i>
                                Tên chi nhánh
                            </label>
                            <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror"
                                   placeholder="Nhập tên chi nhánh" value="{{ old('name', $branch->name) }}" maxlength="255">
                            @error('name')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="address" class="form-label form-label-icon">
                                <i class="fas fa-map-marker-alt text-danger"></i>
                                Địa chỉ
                            </label>
                            <textarea id="address" name="address" class="form-control @error('address') is-invalid @enderror"
                                      placeholder="Nhập địa chỉ chi nhánh" maxlength="255">{{ old('address', $branch->address) }}</textarea>
                            @error('address')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="grid grid-2">
                            <div class="form-group">
                                <label for="phone" class="form-label form-label-icon">
                                    <i class="fas fa-phone text-primary"></i>
                                    Số điện thoại
                                </label>
                                <input type="tel" id="phone" name="phone" class="form-control @error('phone') is-invalid @enderror"
                                       placeholder="Nhập số điện thoại" value="{{ old('phone', $branch->phone) }}" pattern="[0-9\s\-\+\(\)]{10,}">
                                @error('phone')
                                    <div class="form-error">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="email" class="form-label form-label-icon">
                                    <i class="fas fa-envelope text-primary"></i>
                                    Email
                                </label>
                                <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                       placeholder="Nhập email (không bắt buộc)" value="{{ old('email', $branch->email) }}">
                                @error('email')
                                    <div class="form-error">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <div class="card-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <h3>Giờ hoạt động</h3>
                    </div>
                    <div class="card-body">
                        <div class="grid grid-2">
                            <div class="form-group">
                                <label for="opening_hour" class="form-label form-label-icon">
                                    <i class="fas fa-sun text-success"></i>
                                    Giờ mở cửa
                                </label>
                                <input type="time" id="opening_hour" name="opening_hour"
                                       class="form-control @error('opening_hour') is-invalid @enderror"
                                       value="{{ old('opening_hour', $branch->opening_hour) }}">
                                @error('opening_hour')
                                    <div class="form-error">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="closing_hour" class="form-label form-label-icon">
                                    <i class="fas fa-moon text-danger"></i>
                                    Giờ đóng cửa
                                </label>
                                <input type="time" id="closing_hour" name="closing_hour"
                                       class="form-control @error('closing_hour') is-invalid @enderror"
                                       value="{{ old('closing_hour', $branch->closing_hour) }}">
                                @error('closing_hour')
                                    <div class="form-error">{{ $message }}</div>
                                @enderror
                                <div class="form-hint">Giờ đóng cửa phải sau giờ mở cửa</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <div class="card-icon">
                            <i class="fas fa-map-marked-alt"></i>
                        </div>
                        <h3>Vị trí chi nhánh</h3>
                    </div>
                    <div class="card-body">
                        <div id="map"></div>
                        <div class="map-hint">Nhấp vào bản đồ để chọn vị trí chi nhánh</div>

                        <div class="map-coordinates grid grid-2">
                            <div class="form-group">
                                <label for="latitude" class="form-label">
                                    <i class="fas fa-map-pin text-primary"></i>
                                    Vĩ độ (Latitude)
                                </label>
                                <input type="text" id="latitude" name="latitude"
                                       class="form-control @error('latitude') is-invalid @enderror"
                                       value="{{ old('latitude', $branch->latitude) }}" readonly>
                                @error('latitude')
                                    <div class="form-error">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="longitude" class="form-label">
                                    <i class="fas fa-map-pin text-primary"></i>
                                    Kinh độ (Longitude)
                                </label>
                                <input type="text" id="longitude" name="longitude"
                                       class="form-control @error('longitude') is-invalid @enderror"
                                       value="{{ old('longitude', $branch->longitude) }}" readonly>
                                @error('longitude')
                                    <div class="form-error">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <div class="card-icon">
                            <i class="fas fa-images"></i>
                        </div>
                        <h3>Hình ảnh chi nhánh</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="images" class="form-label form-label-icon">
                                <i class="fas fa-upload text-primary"></i>
                                Tải lên hình ảnh mới
                            </label>
                            <div class="upload-label">
                                <input type="file" id="images" name="images[]" class="upload-input @error('images') is-invalid @enderror"
                                       accept="image/jpeg,image/png,image/jpg,image/gif" multiple aria-label="Tải lên hình ảnh chi nhánh">
                                <span class="upload-label-text">Chọn nhiều hình ảnh...</span>
                            </div>
                            <div class="form-hint">Chấp nhận: JPEG, PNG, JPG, GIF. Tối đa 2MB mỗi ảnh.</div>
                            @error('images')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                            @error('images.*')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div id="existingImages" class="image-preview-grid {{ $branch->images->isEmpty() ? 'hidden' : '' }}">
                            <h4>Hình ảnh hiện có:</h4>
                            <div id="existingImagesContainer" class="flex flex-wrap gap-3">
                                @foreach($branch->images as $index => $image)
                                    <div class="image-preview-item" data-existing-image-id="{{ $image->id }}">
                                        <img src="{{ Storage::url($image->image_path) }}" class="image-preview-img" alt="Ảnh chi nhánh {{ $index + 1 }}">
                                        <div class="image-preview-overlay">
                                            <div class="image-preview-actions">
                                                <button type="button" class="image-preview-btn remove-btn" data-existing-image-id="{{ $image->id }}" aria-label="Xóa ảnh {{ $index + 1 }}">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </div>
                                        </div>
                                        @if($image->is_primary)
                                            <div class="image-preview-badge">Ảnh chính</div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div id="imagePreview" class="image-preview-grid hidden">
                            <h4>Xem trước hình ảnh mới:</h4>
                            <div id="previewContainer" class="flex flex-wrap gap-3"></div>
                            <div class="form-group">
                                <label for="primary_image" class="form-label">Chọn ảnh chính</label>
                                <select id="primary_image" name="primary_image" class="form-control">
                                    @if($branch->images->isNotEmpty())
                                        @foreach($branch->images as $index => $image)
                                            <option value="{{ $image->id }}" {{ $image->is_primary ? 'selected' : '' }}>Ảnh hiện có {{ $index + 1 }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>

                        <div id="captionsContainer" class="hidden">
                            <h4 class="mb-3">Mô tả cho ảnh mới:</h4>
                            <div id="captionInputs"></div>
                        </div>

                        <div id="existingCaptionsContainer" class="{{ $branch->images->isEmpty() ? 'hidden' : '' }}">
                            <h4 class="mb-3">Mô tả cho ảnh hiện có:</h4>
                            <div id="existingCaptionInputs">
                                @foreach($branch->images as $index => $image)
                                    <div class="form-group" data-existing-image-id="{{ $image->id }}">
                                        <label class="form-label">Mô tả ảnh hiện có {{ $index + 1 }}:</label>
                                        <input type="text" class="form-control" name="captions[{{ $image->id }}]"
                                               maxlength="255" placeholder="Nhập mô tả cho ảnh..."
                                               value="{{ old('captions.' . $image->id, $image->caption) }}"
                                               aria-label="Mô tả cho ảnh hiện có {{ $index + 1 }}">
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="card">
                    <div class="card-header">
                        <div class="card-icon">
                            <i class="fas fa-hashtag"></i>
                        </div>
                        <h3>Mã chi nhánh</h3>
                    </div>
                    <div class="card-body">
                        <div class="text-center">
                            <div class="badge badge-info">
                                <i class="fas fa-info-circle"></i>
                                <span>{{ $branch->branch_code }}</span>
                            </div>
                            <p class="text-sm text-gray mt-2">
                                Mã chi nhánh không thể chỉnh sửa
                            </p>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <div class="card-icon">
                            <i class="fas fa-user-tie"></i>
                        </div>
                        <h3>Quản lý chi nhánh</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="manager_user_id" class="form-label">
                                Chọn người quản lý
                            </label>
                            <select id="manager_user_id" name="manager_user_id"
                                    class="form-control @error('manager_user_id') is-invalid @enderror">
                                <option value="">-- Chọn quản lý --</option>
                                @foreach($availableManagers as $manager)
                                    <option value="{{ $manager->id }}" {{ old('manager_user_id', $branch->manager_user_id) == $manager->id ? 'selected' : '' }}>
                                        {{ $manager->full_name }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-hint">
                                Chỉ hiển thị những quản lý chưa được phân công hoặc quản lý hiện tại
                            </div>
                            @error('manager_user_id')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <div class="card-icon">
                            <i class="fas fa-star"></i>
                        </div>
                        <h3>Trạng thái</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-check">
                            <div class="form-check-content">
                                <div class="form-check-label">Trạng thái hoạt động</div>
                                <div class="form-check-hint" id="statusHint">{{ $branch->active ? 'Chi nhánh đang hoạt động' : 'Chi nhánh ngưng hoạt động' }}</div>
                            </div>
                            <label class="switch">
                                <input type="checkbox" id="active" name="active" {{ old('active', $branch->active) ? 'checked' : '' }} aria-label="Bật/tắt trạng thái hoạt động">
                                <span class="slider"></span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <button type="submit" id="submitButton" class="btn btn-primary w-full">
                            <i class="fas fa-save"></i>
                            <span>Cập nhật chi nhánh</span>
                        </button>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h3>Xem trước</h3>
                    </div>
                    <div class="card-body">
                        <div class="preview-card">
                            <div class="preview-header">
                                <i class="fas fa-building text-primary"></i>
                                <h4 class="preview-title" id="previewName">{{ $branch->name }}</h4>
                            </div>
                            <div class="preview-item">
                                <i class="fas fa-map-marker-alt text-danger"></i>
                                <span id="previewAddress">{{ $branch->address }}</span>
                            </div>
                            <div class="preview-item">
                                <i class="fas fa-phone text-primary"></i>
                                <span id="previewPhone">{{ $branch->phone }}</span>
                            </div>
                            <div class="preview-item {{ $branch->email ? '' : 'hidden' }}" id="previewEmailContainer">
                                <i class="fas fa-envelope text-primary"></i>
                                <span id="previewEmail">{{ $branch->email ?? 'Email' }}</span>
                            </div>
                            <div class="preview-item" id="previewManagerContainer">
                                <i class="fas fa-user-tie text-primary"></i>
                                <span id="previewManager">{{ $branch->manager ? $branch->manager->full_name : 'Chưa chọn quản lý' }}</span>
                            </div>
                        </div>

                        <div class="preview-hours">
                            <div class="preview-hour opening">
                                <i class="fas fa-sun text-success"></i>
                                <div class="preview-hour-label">Mở cửa</div>
                                <div class="preview-hour-value opening" id="previewOpeningHour">{{ $branch->opening_hour }}</div>
                            </div>
                            <div class="preview-hour closing">
                                <i class="fas fa-moon text-danger"></i>
                                <div class="preview-hour-label">Đóng cửa</div>
                                <div class="preview-hour-value closing" id="previewClosingHour">{{ $branch->closing_hour }}</div>
                            </div>
                        </div>

                        <div class="preview-status">
                            <div class="preview-status-label">Trạng thái</div>
                            <div class="preview-status-value {{ $branch->active ? 'active' : 'inactive' }}" id="previewStatus">{{ $branch->active ? 'Đang hoạt động' : 'Ngưng hoạt động' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('branchForm');
    const nameInput = document.getElementById('name');
    const addressInput = document.getElementById('address');
    const phoneInput = document.getElementById('phone');
    const emailInput = document.getElementById('email');
    const openingHourInput = document.getElementById('opening_hour');
    const closingHourInput = document.getElementById('closing_hour');
    const activeInput = document.getElementById('active');
    const managerSelect = document.getElementById('manager_user_id');
    const latitudeInput = document.getElementById('latitude');
    const longitudeInput = document.getElementById('longitude');
    const imagesInput = document.getElementById('images');
    const primaryImageSelect = document.getElementById('primary_image');
    const previewContainer = document.getElementById('previewContainer');
    const existingImagesContainer = document.getElementById('existingImagesContainer');
    const imagePreview = document.getElementById('imagePreview');
    const captionsContainer = document.getElementById('captionsContainer');
    const captionInputs = document.getElementById('captionInputs');
    const existingCaptionsContainer = document.getElementById('existingCaptionsContainer');
    const existingCaptionInputs = document.getElementById('existingCaptionInputs');
    const submitButton = document.getElementById('submitButton');
    const uploadLabelText = document.querySelector('.upload-label-text');

    const previewName = document.getElementById('previewName');
    const previewAddress = document.getElementById('previewAddress');
    const previewPhone = document.getElementById('previewPhone');
    const previewEmail = document.getElementById('previewEmail');
    const previewEmailContainer = document.getElementById('previewEmailContainer');
    const previewManager = document.getElementById('previewManager');
    const previewManagerContainer = document.getElementById('previewManagerContainer');
    const previewOpeningHour = document.getElementById('previewOpeningHour');
    const previewClosingHour = document.getElementById('previewClosingHour');
    const previewStatus = document.getElementById('previewStatus');
    const statusHint = document.getElementById('statusHint');

    let uploadedImages = [];
    let deletedImageIds = [];
    let map;
    let marker;

    function initMap() {
        const defaultLat = 21.0285;
        const defaultLng = 105.8542;
        let lat = defaultLat;
        let lng = defaultLng;

        try {
            // Validate existing coordinates
            if (latitudeInput.value && longitudeInput.value) {
                const parsedLat = parseFloat(latitudeInput.value);
                const parsedLng = parseFloat(longitudeInput.value);
                if (!isNaN(parsedLat) && !isNaN(parsedLng) &&
                    parsedLat >= -90 && parsedLat <= 90 &&
                    parsedLng >= -180 && parsedLng <= 180) {
                    lat = parsedLat;
                    lng = parsedLng;
                }
            }

            // Initialize map
            const mapContainer = document.getElementById('map');
            if (!mapContainer) {
                console.error('Map container not found');
                return;
            }

            map = L.map('map', {
                center: [lat, lng],
                zoom: 13,
                zoomControl: true,
                scrollWheelZoom: false
            });

            // Add tile layer
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                maxZoom: 19,
                tileSize: 256,
                zoomOffset: 0
            }).addTo(map);

            // Ensure map resizes correctly
            setTimeout(() => {
                map.invalidateSize();
            }, 300);

            // Set initial marker if coordinates exist
            if (latitudeInput.value && longitudeInput.value) {
                setMarker(lat, lng);
            }

            // Add click event for marker placement
            map.on('click', function(e) {
                setMarker(e.latlng.lat, e.latlng.lng);
            });

            // Handle window resize
            window.addEventListener('resize', () => {
                setTimeout(() => map.invalidateSize(), 100);
            });
        } catch (error) {
            console.error('Map initialization failed:', error);
            latitudeInput.value = defaultLat.toFixed(6);
            longitudeInput.value = defaultLng.toFixed(6);
        }
    }

    function setMarker(lat, lng) {
        try {
            // Validate coordinates
            if (isNaN(lat) || isNaN(lng) || lat < -90 || lat > 90 || lng < -180 || lng > 180) {
                console.warn('Invalid coordinates:', lat, lng);
                return;
            }

            // Remove existing marker
            if (marker) {
                map.removeLayer(marker);
            }

            // Add new marker
            marker = L.marker([lat, lng]).addTo(map);
            map.panTo([lat, lng], { animate: true });

            // Update input fields
            latitudeInput.value = lat.toFixed(6);
            longitudeInput.value = lng.toFixed(6);
        } catch (error) {
            console.error('Failed to set marker:', error);
        }
    }

    function initForm() {
        updatePreview();

        nameInput.addEventListener('input', updatePreview);
        addressInput.addEventListener('input', updatePreview);
        phoneInput.addEventListener('input', updatePreview);
        emailInput.addEventListener('input', updatePreview);
        managerSelect.addEventListener('change', updatePreview);
        openingHourInput.addEventListener('input', updatePreview);
        closingHourInput.addEventListener('input', updatePreview);
        activeInput.addEventListener('change', updatePreview);
        imagesInput.addEventListener('change', handleImageUpload);
        primaryImageSelect.addEventListener('change', updatePrimaryImage);

        initMap();

        document.querySelectorAll('.remove-btn').forEach(button => {
            button.addEventListener('click', function(e) {
                e.stopPropagation();
                const imageId = this.dataset.existingImageId;
                removeExistingImage(imageId);
            });
        });

        form.addEventListener('submit', function(e) {
            if (openingHourInput.value && closingHourInput.value && openingHourInput.value >= closingHourInput.value) {
                e.preventDefault();
                alert('Giờ đóng cửa phải sau giờ mở cửa!');
                return;
            }

            // Validate coordinates before submission
            const lat = parseFloat(latitudeInput.value);
            const lng = parseFloat(longitudeInput.value);
            if (isNaN(lat) || isNaN(lng) || lat < -90 || lat > 90 || lng < -180 || lng > 180) {
                e.preventDefault();
                alert('Vui lòng chọn một vị trí hợp lệ trên bản đồ!');
                return;
            }

            // Append deleted image IDs to form submission
            deletedImageIds.forEach(id => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'delete_images[]';
                input.value = id;
                form.appendChild(input);
            });
        });

        // Initialize primary image badge
        updatePrimaryImage();
    }

    function updatePreview() {
        previewName.textContent = nameInput.value || 'Tên chi nhánh';
        previewAddress.textContent = addressInput.value || 'Địa chỉ chi nhánh';
        previewPhone.textContent = phoneInput.value || 'Số điện thoại';

        if (emailInput.value) {
            previewEmail.textContent = emailInput.value;
            previewEmailContainer.classList.remove('hidden');
        } else {
            previewEmailContainer.classList.add('hidden');
        }

        if (managerSelect.value) {
            const selectedOption = managerSelect.options[managerSelect.selectedIndex];
            previewManager.textContent = selectedOption.text;
            previewManagerContainer.classList.remove('hidden');
        } else {
            previewManager.textContent = 'Chưa chọn quản lý';
            previewManagerContainer.classList.remove('hidden');
        }

        previewOpeningHour.textContent = openingHourInput.value || '08:00';
        previewClosingHour.textContent = closingHourInput.value || '22:00';

        if (activeInput.checked) {
            previewStatus.textContent = 'Đang hoạt động';
            previewStatus.className = 'preview-status-value active';
            statusHint.textContent = 'Chi nhánh đang hoạt động';
        } else {
            previewStatus.textContent = 'Ngưng hoạt động';
            previewStatus.className = 'preview-status-value inactive';
            statusHint.textContent = 'Chi nhánh ngưng hoạt động';
        }
    }

    function handleImageUpload(event) {
        const files = event.target.files;
        const maxImages = 10;

        if (files.length > maxImages) {
            alert(`Bạn chỉ có thể tải lên tối đa ${maxImages} hình ảnh.`);
            imagesInput.value = '';
            return;
        }

        const invalidFiles = [];
        uploadedImages = Array.from(files).filter((file, index) => {
            const isValid = file.type.match('image/(jpeg|png|jpg|gif)') && file.size <= 2048 * 1024;
            if (!isValid) invalidFiles.push(`Ảnh ${index + 1}: ${file.name}`);
            return isValid;
        });

        if (invalidFiles.length > 0) {
            alert(`Các hình ảnh không hợp lệ (phải là JPEG, PNG, JPG, GIF và nhỏ hơn 2MB):\n${invalidFiles.join('\n')}`);
            imagesInput.value = '';
            return;
        }

        if (uploadedImages.length > 0) {
            displayImagePreviews(uploadedImages);
            showImageSections();
            uploadLabelText.textContent = uploadedImages.length + ' ảnh đã chọn';
            // Set the first new image as primary by default
            primaryImageSelect.value = '0';
            updatePrimaryImage();
        } else {
            hideImageSections();
            imagesInput.value = '';
            uploadLabelText.textContent = 'Chọn nhiều hình ảnh...';
        }
    }

    function displayImagePreviews(files) {
        previewContainer.innerHTML = '';
        captionInputs.innerHTML = '';
        updatePrimaryImageSelect();

        files.forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const previewItem = document.createElement('div');
                previewItem.className = 'image-preview-item';
                previewItem.dataset.newImageIndex = index;

                const img = document.createElement('img');
                img.src = e.target.result;
                img.className = 'image-preview-img';
                img.alt = `Xem trước ảnh mới ${index + 1}`;

                const overlay = document.createElement('div');
                overlay.className = 'image-preview-overlay';

                const actions = document.createElement('div');
                actions.className = 'image-preview-actions';

                const removeBtn = document.createElement('button');
                removeBtn.className = 'image-preview-btn remove-btn';
                removeBtn.innerHTML = '<i class="fas fa-trash-alt"></i>';
                removeBtn.setAttribute('aria-label', `Xóa ảnh mới ${index + 1}`);
                removeBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    removeNewImage(index);
                });

                actions.appendChild(removeBtn);
                overlay.appendChild(actions);
                previewItem.appendChild(img);
                previewItem.appendChild(overlay);

                previewContainer.appendChild(previewItem);

                const captionGroup = document.createElement('div');
                captionGroup.className = 'form-group';
                captionGroup.dataset.newImageIndex = index;

                const captionLabel = document.createElement('label');
                captionLabel.className = 'form-label';
                captionLabel.textContent = `Mô tả ảnh mới ${index + 1}:`;

                const captionInput = document.createElement('input');
                captionInput.type = 'text';
                captionInput.className = 'form-control';
                captionInput.name = `captions[${index}]`;
                captionInput.maxLength = 255;
                captionInput.placeholder = 'Nhập mô tả cho ảnh...';
                captionInput.setAttribute('aria-label', `Mô tả cho ảnh mới ${index + 1}`);

                captionGroup.appendChild(captionLabel);
                captionGroup.appendChild(captionInput);
                captionInputs.appendChild(captionGroup);
            };
            reader.readAsDataURL(file);
        });

        // Ensure primary image badge is updated after all images are loaded
        setTimeout(updatePrimaryImage, 0);
    }

    function updatePrimaryImage() {
        // Remove all existing badges
        const existingBadges = document.querySelectorAll('.image-preview-badge');
        existingBadges.forEach(badge => badge.remove());

        const selectedValue = primaryImageSelect.value;
        let selectedPreview = null;

        if (selectedValue && selectedValue.match(/^\d+$/)) {
            if (existingImagesContainer.querySelector(`.image-preview-item[data-existing-image-id="${selectedValue}"]`)) {
                // Existing image selected
                selectedPreview = existingImagesContainer.querySelector(`.image-preview-item[data-existing-image-id="${selectedValue}"]`);
            } else if (previewContainer.querySelector(`.image-preview-item[data-new-image-index="${selectedValue}"]`)) {
                // New image selected
                selectedPreview = previewContainer.querySelector(`.image-preview-item[data-new-image-index="${selectedValue}"]`);
            }
        }

        if (selectedPreview) {
            const primaryBadge = document.createElement('div');
            primaryBadge.className = 'image-preview-badge';
            primaryBadge.textContent = 'Ảnh chính';
            selectedPreview.appendChild(primaryBadge);
        }
    }

    function removeExistingImage(imageId) {
        deletedImageIds.push(imageId);
        const imageElement = existingImagesContainer.querySelector(`.image-preview-item[data-existing-image-id="${imageId}"]`);
        if (imageElement) {
            imageElement.remove();
            const captionElement = existingCaptionInputs.querySelector(`.form-group[data-existing-image-id="${imageId}"]`);
            if (captionElement) captionElement.remove();
        }
        if (existingImagesContainer.children.length === 0) {
            document.getElementById('existingImages').classList.add('hidden');
            existingCaptionsContainer.classList.add('hidden');
        }
        updatePrimaryImageSelect();
        if (primaryImageSelect.value === imageId) {
            primaryImageSelect.value = uploadedImages.length > 0 ? '0' : (existingImagesContainer.children.length > 0 ? existingImagesContainer.querySelector('.image-preview-item').dataset.existingImageId : '');
        }
        updatePrimaryImage();
    }

    function removeNewImage(index) {
        uploadedImages.splice(index, 1);

        const dataTransfer = new DataTransfer();
        uploadedImages.forEach(file => dataTransfer.items.add(file));
        imagesInput.files = dataTransfer.files;

        if (uploadedImages.length > 0) {
            displayImagePreviews(uploadedImages);
            uploadLabelText.textContent = uploadedImages.length + ' ảnh đã chọn';
        } else {
            hideImageSections();
            imagesInput.value = '';
            uploadLabelText.textContent = 'Chọn nhiều hình ảnh...';
        }

        if (primaryImageSelect.value === index.toString()) {
            primaryImageSelect.value = uploadedImages.length > 0 ? '0' : (existingImagesContainer.children.length > 0 ? existingImagesContainer.querySelector('.image-preview-item').dataset.existingImageId : '');
        }
        updatePrimaryImage();
    }

    function updatePrimaryImageSelect() {
        const selectedValue = primaryImageSelect.value;
        primaryImageSelect.innerHTML = '';

        // Add existing images
        const existingImages = existingImagesContainer.querySelectorAll('.image-preview-item');
        existingImages.forEach((item, index) => {
            const option = document.createElement('option');
            option.value = item.dataset.existingImageId;
            option.textContent = `Ảnh hiện có ${index + 1}`;
            if (item.dataset.existingImageId === selectedValue) {
                option.selected = true;
            }
            primaryImageSelect.appendChild(option);
        });

        // Add new images
        uploadedImages.forEach((file, index) => {
            const option = document.createElement('option');
            option.value = index.toString();
            option.textContent = `Ảnh mới ${index + 1}`;
            if (index.toString() === selectedValue) {
                option.selected = true;
            }
            primaryImageSelect.appendChild(option);
        });

        // If no images are available, set a default option
        if (primaryImageSelect.options.length === 0) {
            const option = document.createElement('option');
            option.value = '';
            option.textContent = 'Không có ảnh';
            primaryImageSelect.appendChild(option);
        }

        // Update primary image badge
        updatePrimaryImage();
    }

    function showImageSections() {
        imagePreview.classList.remove('hidden');
        captionsContainer.classList.remove('hidden');
    }

    function hideImageSections() {
        imagePreview.classList.add('hidden');
        captionsContainer.classList.add('hidden');
        uploadLabelText.textContent = 'Chọn nhiều hình ảnh...';
    }

    initForm();
});
</script>
@endsection