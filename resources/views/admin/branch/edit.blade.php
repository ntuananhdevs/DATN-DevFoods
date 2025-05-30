@extends('layouts.admin.contentLayoutMaster')

@section('content')
<style>
    :root {
        --branch-primary: #4361ee;
        --branch-primary-light: #4895ef;
        --branch-primary-dark: #3f37c9;
        --branch-secondary: #4cc9f0;
        --branch-success: #4ade80;
        --branch-danger: #f43f5e;
        --branch-warning: #f59e0b;
        --branch-info: #3b82f6;
        --branch-light: #f9fafb;
        --branch-dark: #1f2937;
        --branch-gray: #6b7280;
        --branch-gray-light: #e5e7eb;
        --branch-gray-dark: #4b5563;
        --branch-white: #ffffff;
        --branch-black: #000000;
        --branch-border-radius: 12px;
        --branch-border-radius-sm: 8px;
        --branch-border-radius-lg: 16px;
        --branch-border-radius-xl: 24px;
        --branch-border-radius-full: 9999px;
        --branch-shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        --branch-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        --branch-shadow-md: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        --branch-shadow-lg: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        --branch-transition: all 0.3s ease;
        --branch-transition-fast: all 0.15s ease;
        --branch-transition-slow: all 0.5s ease;
    }

    .branch-form-container {
        max-width: 100%;
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
   
    a:hover { color: var(--branch-primary-dark); }

    .branch-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0.5rem 1rem;
        border-radius: var(--branch-border-radius);
        font-weight: 500;
        cursor: pointer;
        transition: var(--branch-transition-fast);
        border: none;
        font-size: 0.875rem;
        gap: 0.5rem;
    }

    .branch-btn-sm { padding: 0.375rem 0.75rem; font-size: 0.75rem; }
    .branch-btn-block { width: 100%; }
    .branch-btn-primary { background-color: var(--branch-primary); color: var(--branch-white); }
    .branch-btn-primary:hover { background-color: var(--branch-primary-dark); color: var(--branch-white); }
    .branch-btn-outline { background-color: transparent; color: var(--branch-gray-dark); border: 1px solid var(--branch-gray-light); }
    .branch-btn-outline:hover { background-color: var(--branch-gray-light); color: var(--branch-dark); }
    .branch-btn-danger { background-color: var(--branch-danger); color: var(--branch-white); }
    .branch-btn-danger:hover { background-color: #e11d48; color: var(--branch-white); }
    .branch-btn:disabled { opacity: 0.7; cursor: not-allowed; }

    .branch-page-header { margin-bottom: 1.5rem; }
    .branch-header-content { display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem; }
    .branch-header-left { display: flex; align-items: center; gap: 1rem; }
    .branch-header-icon { width: 3rem; height: 3rem; background-color: rgba(67, 97, 238, 0.1); color: var(--branch-primary); border-radius: var(--branch-border-radius-full); display: flex; align-items: center; justify-content: center; font-size: 1.25rem; }
    .branch-header-text p { color: var(--branch-gray); margin-top: 0.25rem; }
    .branch-header-actions { display: flex; gap: 0.75rem; }

    .branch-card { background-color: var(--branch-white); border-radius: var(--branch-border-radius-lg); box-shadow: var(--branch-shadow); overflow: hidden; transition: var(--branch-transition); margin-bottom: 1.5rem; }
    .branch-card:hover { box-shadow: var(--branch-shadow-md); transform: translateY(-2px); }
    .branch-card-header { display: flex; align-items: center; padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--branch-gray-light); gap: 0.75rem; }
    .branch-card-icon { width: 2.5rem; height: 2.5rem; background-color: rgba(67, 97, 238, 0.1); color: var(--branch-primary); border-radius: var(--branch-border-radius-full); display: flex; align-items: center; justify-content: center; font-size: 1rem; }
    .branch-card-header h3 { flex-grow: 1; }
    .branch-card-actions { display: flex; gap: 0.5rem; }
    .branch-card-body { padding: 1.5rem; }

    .branch-form-grid { display: grid; grid-template-columns: 1fr; gap: 1.5rem; }
    @media (min-width: 992px) { .branch-form-grid { grid-template-columns: 2fr 1fr; } }
    .branch-form-group { margin-bottom: 1.25rem; }
    .branch-form-group:last-child { margin-bottom: 0; }
    .branch-form-label { display: block; margin-bottom: 0.5rem; font-weight: 500; }
    .branch-form-label-icon { display: flex; align-items: center; gap: 0.5rem; }
    .branch-form-control { display: block; width: 100%; padding: 0.625rem 0.75rem; font-size: 0.875rem; line-height: 1.5; color: var(--branch-dark); background-color: var(--branch-white); background-clip: padding-box; border: 1px solid var(--branch-gray-light); border-radius: var(--branch-border-radius); transition: var(--branch-transition-fast); }
    .branch-form-control:focus { border-color: var(--branch-primary-light); outline: 0; box-shadow: 0 0 0 2px rgba(67, 97, 238, 0.25); }
    select.branch-form-control { appearance: none; background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e"); background-position: right 0.5rem center; background-repeat: no-repeat; background-size: 1.5em 1.5em; padding-right: 2.5rem; }
    textarea.branch-form-control { min-height: 100px; resize: vertical; }
    .branch-form-hint { margin-top: 0.375rem; font-size: 0.75rem; color: var(--branch-gray); }
    .branch-form-error { margin-top: 0.375rem; font-size: 0.75rem; color: var(--branch-danger); }
    .branch-form-check { display: flex; align-items: center; gap: 0.5rem; padding: 1rem; border: 1px solid var(--branch-gray-light); border-radius: var(--branch-border-radius); }
    .branch-form-check-content { flex-grow: 1; }
    .branch-form-check-label { font-weight: 500; }
    .branch-form-check-hint { font-size: 0.875rem; color: var(--branch-gray); }

    .branch-switch { position: relative; display: inline-block; width: 44px; height: 24px; }
    .branch-switch input { opacity: 0; width: 0; height: 0; }
    .branch-slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: var(--branch-gray-light); transition: var(--branch-transition-fast); border-radius: 34px; }
    .branch-slider:before { position: absolute; content: ""; height: 18px; width: 18px; left: 3px; bottom: 3px; background-color: white; transition: var(--branch-transition-fast); border-radius: 50%; }
    input:checked + .branch-slider { background-color: var(--branch-primary); }
    input:focus + .branch-slider { box-shadow: 0 0 1px var(--branch-primary); }
    input:checked + .branch-slider:before { transform: translateX(20px); }

    .branch-grid { display: grid; gap: 1.5rem; }
    .branch-grid-2 { grid-template-columns: 1fr; }
    @media (min-width: 768px) { .branch-grid-2 { grid-template-columns: 1fr 1fr; } }

    .branch-upload-label { cursor: pointer; display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem 0.75rem; background-color: var(--branch-gray-light); border-radius: var(--branch-border-radius); font-size: 0.875rem; transition: var(--branch-transition-fast); }
    .branch-upload-label:hover { background-color: var(--branch-gray); color: var(--branch-white); }
    .branch-upload-input { display: none; }
    .branch-image-preview-grid {
        display: flex;
        flex-wrap: nowrap;
        gap: 1rem;
        overflow-x: auto;
        padding-bottom: 0.5rem;
        scroll-behavior: smooth;
    }
    .branch-image-preview-item {
        position: relative;
        border-radius: var(--branch-border-radius);
        overflow: hidden;
        flex: 0 0 auto;
        width: 150px;
        aspect-ratio: 4/3;
        box-shadow: var(--branch-shadow-sm);
    }
    .branch-image-preview-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .branch-image-preview-overlay {
        position: absolute;
        inset: 0;
        background-color: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: var(--branch-transition);
    }
    .branch-image-preview-item:hover .branch-image-preview-overlay {
        opacity: 1;
    }
    .branch-image-preview-actions {
        display: flex;
        gap: 0.5rem;
    }
    .branch-image-preview-btn {
        width: 2rem;
        height: 2rem;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: var(--branch-white);
        color: var(--branch-dark);
        border: none;
        cursor: pointer;
        transition: var(--branch-transition-fast);
    }
    .branch-image-preview-btn:hover {
        transform: scale(1.1);
    }
    .branch-image-preview-btn.branch-remove-btn:hover {
        background-color: var(--branch-danger);
        color: var(--branch-white);
    }
    .branch-image-preview-btn.branch-primary-btn {
        background-color: var(--branch-warning);
        color: var(--branch-white);
    }
    .branch-image-preview-btn.branch-set-primary-btn:hover {
        background-color: var(--branch-warning);
        color: var(--branch-white);
    }
    .branch-image-preview-badge {
        position: absolute;
        top: 0.5rem;
        left: 0.5rem;
        padding: 0.25rem 0.5rem;
        background-color: var(--branch-warning);
        color: var(--branch-white);
        border-radius: var(--branch-border-radius-full);
        font-size: 0.625rem;
        font-weight: 600;
    }

    .branch-empty-state { text-align: center; padding: 3rem 1rem; }
    .branch-empty-icon { width: 4rem; height: 4rem; background-color: var(--branch-gray-light); color: var(--branch-gray); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; margin: 0 auto 1rem; }
    .branch-empty-title { margin-bottom: 0.5rem; font-weight: 500; }
    .branch-empty-text { color: var(--branch-gray); margin-bottom: 1.5rem; }

    .branch-preview-card { padding: 1rem; background-color: rgba(67, 97, 238, 0.05); border-radius: var(--branch-border-radius); border: 1px solid rgba(67, 97, 238, 0.1); margin-bottom: 1rem; }
    .branch-preview-header { display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.75rem; }
    .branch-preview-title { font-weight: 500; }
    .branch-preview-item { display: flex; align-items: flex-start; gap: 0.5rem; margin-bottom: 0.5rem; font-size: 0.875rem; color: var(--branch-gray-dark); }
    .branch-preview-item i { margin-top: 0.25rem; }
    .branch-preview-hours { display: flex; gap: 1rem; margin-top: 1rem; }
    .branch-preview-hour { flex: 1; padding: 0.75rem; border-radius: var(--branch-border-radius); text-align: center; }
    .branch-preview-hour.branch-opening { background-color: rgba(74, 222, 128, 0.1); border: 1px solid rgba(74, 222, 128, 0.2); }
    .branch-preview-hour.branch-closing { background-color: rgba(244, 63, 94, 0.1); border: 1px solid rgba(244, 63, 94, 0.2); }
    .branch-preview-hour-label { font-size: 0.75rem; color: var(--branch-gray); margin-bottom: 0.25rem; }
    .branch-preview-hour-value.branch-opening { color: var(--branch-success); font-weight: 500; }
    .branch-preview-hour-value.branch-closing { color: var(--branch-danger); font-weight: 500; }
    .branch-preview-status { display: flex; align-items: center; justify-content: space-between; padding: 0.75rem; border-radius: var(--branch-border-radius); border: 1px solid var(--branch-gray-light); margin-top: 1rem; }
    .branch-preview-status-label { font-weight: 500; }
    .branch-preview-status-value { padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 500; }
    .branch-preview-status-value.branch-active { background-color: rgba(74, 222, 128, 0.1); color: var(--branch-success); }
    .branch-preview-status-value.branch-inactive { background-color: rgba(244, 63, 94, 0.1); color: var(--branch-danger); }

    #branch-map {
        height: 300px !important;
        width: 100%;
        border-radius: var(--branch-border-radius);
        margin-bottom: 1rem;
        min-height: 300px;
        background-color: #f0f0f0;
    }
    .branch-map-coordinates { display: flex; gap: 1rem; }
    .branch-map-hint { margin-top: 0.5rem; font-size: 0.75rem; color: var(--branch-gray); }

    .branch-mb-6 { margin-bottom: 1.5rem; }
    .branch-space-y-6 > * + * { margin-top: 1.5rem; }
    .branch-flex { display: flex; }
    .branch-items-center { align-items: center; }
    .branch-justify-between { justify-content: space-between; }
    .branch-gap-2 { gap: 0.5rem; }
    .branch-gap-3 { gap: 0.75rem; }
    .branch-gap-4 { gap: 1rem; }
    .branch-flex-wrap { flex-wrap: wrap; }
    .branch-hidden { display: none; }
    .branch-text-center { text-align: center; }
    .branch-text-sm { font-size: 0.875rem; }
    .branch-text-xs { font-size: 0.75rem; }
    .branch-text-gray { color: var(--branch-gray); }
    .branch-text-primary { color: var(--branch-primary); }
    .branch-text-success { color: var(--branch-success); }
    .branch-text-danger { color: var(--branch-danger); }
    .branch-font-medium { font-weight: 500; }
    .branch-font-semibold { font-weight: 600; }
    .branch-w-full { width: 100%; }
    .branch-badge { display: inline-block; padding: 0.25rem 0.5rem; border-radius: var(--branch-border-radius-full); font-size: 0.75rem; font-weight: 500; }
    .branch-badge-info { background-color: rgba(59, 130, 246, 0.1); color: var(--branch-info); }
</style>

<!-- Include Leaflet CSS and JS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

<div class="branch-form-container">
    <div class="branch-mb-6">
        <div class="branch-flex branch-justify-between branch-items-center branch-flex-wrap branch-gap-4">
            <div class="branch-flex branch-items-center branch-gap-4">
                <div class="branch-header-icon">
                    <i class="fas fa-building"></i>
                </div>
                <div class="branch-header-text">
                    <h1>Chỉnh sửa chi nhánh</h1>
                    <p>Cập nhật thông tin chi nhánh {{ $branch->name }}</p>
                </div>
            </div>
            <div class="branch-header-actions">
                <a href="{{ route('admin.branches.index') }}" class="branch-btn branch-btn-outline">
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

        <div class="branch-form-grid">
            <div class="branch-space-y-6">
                <div class="branch-card">
                    <div class="branch-card-header">
                        <div class="branch-card-icon">
                            <i class="fas fa-info-circle"></i>
                        </div>
                        <h3>Thông tin cơ bản</h3>
                    </div>
                    <div class="branch-card-body">
                        <div class="branch-form-group">
                            <label for="name" class="branch-form-label branch-form-label-icon">
                                <i class="fas fa-building branch-text-primary"></i>
                                Tên chi nhánh
                            </label>
                            <input type="text" id="name" name="name" class="branch-form-control @error('name') is-invalid @enderror"
                                   placeholder="Nhập tên chi nhánh" value="{{ old('name', $branch->name) }}" maxlength="255">
                            @error('name')
                                <div class="branch-form-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="branch-form-group">
                            <label for="address" class="branch-form-label branch-form-label-icon">
                                <i class="fas fa-map-marker-alt branch-text-danger"></i>
                                Địa chỉ
                            </label>
                            <textarea id="address" name="address" class="branch-form-control @error('address') is-invalid @enderror"
                                      placeholder="Nhập địa chỉ chi nhánh" maxlength="255">{{ old('address', $branch->address) }}</textarea>
                            @error('address')
                                <div class="branch-form-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="branch-grid branch-grid-2">
                            <div class="branch-form-group">
                                <label for="phone" class="branch-form-label branch-form-label-icon">
                                    <i class="fas fa-phone branch-text-primary"></i>
                                    Số điện thoại
                                </label>
                                <input type="tel" id="phone" name="phone" class="branch-form-control @error('phone') is-invalid @enderror"
                                       placeholder="Nhập số điện thoại" value="{{ old('phone', $branch->phone) }}" pattern="[0-9\s\-\+\(\)]{10,}">
                                @error('phone')
                                    <div class="branch-form-error">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="branch-form-group">
                                <label for="email" class="branch-form-label branch-form-label-icon">
                                    <i class="fas fa-envelope branch-text-primary"></i>
                                    Email
                                </label>
                                <input type="email" id="email" name="email" class="branch-form-control @error('email') is-invalid @enderror"
                                       placeholder="Nhập email (không bắt buộc)" value="{{ old('email', $branch->email) }}">
                                @error('email')
                                    <div class="branch-form-error">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="branch-card">
                    <div class="branch-card-header">
                        <div class="branch-card-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <h3>Giờ hoạt động</h3>
                    </div>
                    <div class="branch-card-body">
                        <div class="branch-grid branch-grid-2">
                            <div class="branch-form-group">
                                <label for="opening_hour" class="branch-form-label branch-form-label-icon">
                                    <i class="fas fa-sun branch-text-success"></i>
                                    Giờ mở cửa
                                </label>
                                <input type="time" id="opening_hour" name="opening_hour"
                                       class="branch-form-control @error('opening_hour') is-invalid @enderror"
                                       value="{{ old('opening_hour', $branch->opening_hour) }}">
                                @error('opening_hour')
                                    <div class="branch-form-error">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="branch-form-group">
                                <label for="closing_hour" class="branch-form-label branch-form-label-icon">
                                    <i class="fas fa-moon branch-text-danger"></i>
                                    Giờ đóng cửa
                                </label>
                                <input type="time" id="closing_hour" name="closing_hour"
                                       class="branch-form-control @error('closing_hour') is-invalid @enderror"
                                       value="{{ old('closing_hour', $branch->closing_hour) }}">
                                @error('closing_hour')
                                    <div class="branch-form-error">{{ $message }}</div>
                                @enderror
                                <div class="branch-form-hint">Giờ đóng cửa phải sau giờ mở cửa</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="branch-card">
                    <div class="branch-card-header">
                        <div class="branch-card-icon">
                            <i class="fas fa-map-marked-alt"></i>
                        </div>
                        <h3>Vị trí chi nhánh</h3>
                    </div>
                    <div class="branch-card-body">
                        <div id="branch-map"></div>
                        <div class="branch-map-hint">Nhấp vào bản đồ để chọn vị trí chi nhánh</div>

                        <div class="branch-map-coordinates branch-grid branch-grid-2">
                            <div class="branch-form-group">
                                <label for="latitude" class="branch-form-label">
                                    <i class="fas fa-map-pin branch-text-primary"></i>
                                    Vĩ độ (Latitude)
                                </label>
                                <input type="text" id="latitude" name="latitude"
                                       class="branch-form-control @error('latitude') is-invalid @enderror"
                                       value="{{ old('latitude', $branch->latitude) }}" readonly>
                                @error('latitude')
                                    <div class="branch-form-error">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="branch-form-group">
                                <label for="longitude" class="branch-form-label">
                                    <i class="fas fa-map-pin branch-text-primary"></i>
                                    Kinh độ (Longitude)
                                </label>
                                <input type="text" id="longitude" name="longitude"
                                       class="branch-form-control @error('longitude') is-invalid @enderror"
                                       value="{{ old('longitude', $branch->longitude) }}" readonly>
                                @error('longitude')
                                    <div class="branch-form-error">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="branch-card">
                    <div class="branch-card-header">
                        <div class="branch-card-icon">
                            <i class="fas fa-images"></i>
                        </div>
                        <h3>Hình ảnh chi nhánh</h3>
                    </div>
                    <div class="branch-card-body">
                        <div class="branch-form-group">
                            <label for="images" class="branch-form-label branch-form-label-icon">
                                <i class="fas fa-upload branch-text-primary"></i>
                                Tải lên hình ảnh mới
                            </label>
                            <div class="branch-upload-label">
                                <input type="file" id="images" name="images[]" class="branch-upload-input @error('images') is-invalid @enderror"
                                       accept="image/jpeg,image/png,image/jpg,image/gif" multiple aria-label="Tải lên hình ảnh chi nhánh">
                                <span class="branch-upload-label-text">Chọn nhiều hình ảnh...</span>
                            </div>
                            <div class="branch-form-hint">Chấp nhận: JPEG, PNG, JPG, GIF. Tối đa 2MB mỗi ảnh.</div>
                            @error('images')
                                <div class="branch-form-error">{{ $message }}</div>
                            @enderror
                            @error('images.*')
                                <div class="branch-form-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div id="existingImages" class="branch-image-preview-grid {{ $branch->images->isEmpty() ? 'branch-hidden' : '' }}">
                            <h4>Hình ảnh hiện có:</h4>
                            <div id="existingImagesContainer" class="branch-flex branch-flex-wrap branch-gap-3">
                                @foreach($branch->images as $index => $image)
                                    <div class="branch-image-preview-item" data-existing-image-id="{{ $image->id }}">
                                        <img src="{{ Storage::url($image->image_path) }}" class="branch-image-preview-img" alt="Ảnh chi nhánh {{ $index + 1 }}">
                                        <div class="branch-image-preview-overlay">
                                            <div class="branch-image-preview-actions">
                                                <button type="button" class="branch-image-preview-btn branch-remove-btn" data-existing-image-id="{{ $image->id }}" aria-label="Xóa ảnh {{ $index + 1 }}">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </div>
                                        </div>
                                        @if($image->is_primary)
                                            <div class="branch-image-preview-badge">Ảnh chính</div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div id="imagePreview" class="branch-image-preview-grid branch-hidden">
                            <h4>Xem trước hình ảnh mới:</h4>
                            <div id="previewContainer" class="branch-flex branch-flex-wrap branch-gap-3"></div>
                            <div class="branch-form-group">
                                <label for="primary_image" class="branch-form-label">Chọn ảnh chính</label>
                                <select id="primary_image" name="primary_image" class="branch-form-control">
                                    @if($branch->images->isNotEmpty())
                                        @foreach($branch->images as $index => $image)
                                            <option value="{{ $image->id }}" {{ $image->is_primary ? 'selected' : '' }}>Ảnh hiện có {{ $index + 1 }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>

                        <div id="captionsContainer" class="branch-hidden">
                            <h4 class="branch-mb-3">Mô tả cho ảnh mới:</h4>
                            <div id="captionInputs"></div>
                        </div>

                        <div id="existingCaptionsContainer" class="{{ $branch->images->isEmpty() ? 'branch-hidden' : '' }}">
                            <h4 class="branch-mb-3">Mô tả cho ảnh hiện có:</h4>
                            <div id="existingCaptionInputs">
                                @foreach($branch->images as $index => $image)
                                    <div class="branch-form-group" data-existing-image-id="{{ $image->id }}">
                                        <label class="branch-form-label">Mô tả ảnh hiện có {{ $index + 1 }}:</label>
                                        <input type="text" class="branch-form-control" name="captions[{{ $image->id }}]"
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

            <div class="branch-space-y-6">
                <div class="branch-card">
                    <div class="branch-card-header">
                        <div class="branch-card-icon">
                            <i class="fas fa-hashtag"></i>
                        </div>
                        <h3>Mã chi nhánh</h3>
                    </div>
                    <div class="branch-card-body">
                        <div class="branch-text-center">
                            <div class="branch-badge branch-badge-info">
                                <i class="fas fa-info-circle"></i>
                                <span>{{ $branch->branch_code }}</span>
                            </div>
                            <p class="branch-text-sm branch-text-gray branch-mt-2">
                                Mã chi nhánh không thể chỉnh sửa
                            </p>
                        </div>
                    </div>
                </div>

                <div class="branch-card">
                    <div class="branch-card-header">
                        <div class="branch-card-icon">
                            <i class="fas fa-user-tie"></i>
                        </div>
                        <h3>Quản lý chi nhánh</h3>
                    </div>
                    <div class="branch-card-body">
                        <div class="branch-form-group">
                            <label for="manager_user_id" class="branch-form-label">
                                Chọn người quản lý
                            </label>
                            <select id="manager_user_id" name="manager_user_id"
                                    class="branch-form-control @error('manager_user_id') is-invalid @enderror">
                                <option value="">-- Chọn quản lý --</option>
                                @foreach($availableManagers as $manager)
                                    <option value="{{ $manager->id }}" {{ old('manager_user_id', $branch->manager_user_id) == $manager->id ? 'selected' : '' }}>
                                        {{ $manager->full_name }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="branch-form-hint">
                                Chỉ hiển thị những quản lý chưa được phân công hoặc quản lý hiện tại
                            </div>
                            @error('manager_user_id')
                                <div class="branch-form-error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="branch-card">
                    <div class="branch-card-header">
                        <div class="branch-card-icon">
                            <i class="fas fa-star"></i>
                        </div>
                        <h3>Trạng thái</h3>
                    </div>
                    <div class="branch-card-body">
                        <div class="branch-form-check">
                            <div class="branch-form-check-content">
                                <div class="branch-form-check-label">Trạng thái hoạt động</div>
                                <div class="branch-form-check-hint" id="statusHint">{{ $branch->active ? 'Chi nhánh đang hoạt động' : 'Chi nhánh ngưng hoạt động' }}</div>
                            </div>
                            <label class="branch-switch">
                                <input type="checkbox" id="active" name="active" {{ old('active', $branch->active) ? 'checked' : '' }} aria-label="Bật/tắt trạng thái hoạt động">
                                <span class="branch-slider"></span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="branch-card">
                    <div class="branch-card-body">
                        <button type="submit" id="submitButton" class="branch-btn branch-btn-primary branch-w-full">
                            <i class="fas fa-save"></i>
                            <span>Cập nhật chi nhánh</span>
                        </button>
                    </div>
                </div>

                <div class="branch-card">
                    <div class="branch-card-header">
                        <h3>Xem trước</h3>
                    </div>
                    <div class="branch-card-body">
                        <div class="branch-preview-card">
                            <div class="branch-preview-header">
                                <i class="fas fa-building branch-text-primary"></i>
                                <h4 class="branch-preview-title" id="previewName">{{ $branch->name }}</h4>
                            </div>
                            <div class="branch-preview-item">
                                <i class="fas fa-map-marker-alt branch-text-danger"></i>
                                <span id="previewAddress">{{ $branch->address }}</span>
                            </div>
                            <div class="branch-preview-item">
                                <i class="fas fa-phone branch-text-primary"></i>
                                <span id="previewPhone">{{ $branch->phone }}</span>
                            </div>
                            <div class="branch-preview-item {{ $branch->email ? '' : 'branch-hidden' }}" id="previewEmailContainer">
                                <i class="fas fa-envelope branch-text-primary"></i>
                                <span id="previewEmail">{{ $branch->email ?? 'Email' }}</span>
                            </div>
                            <div class="branch-preview-item" id="previewManagerContainer">
                                <i class="fas fa-user-tie branch-text-primary"></i>
                                <span id="previewManager">{{ $branch->manager ? $branch->manager->full_name : 'Chưa chọn quản lý' }}</span>
                            </div>
                        </div>

                        <div class="branch-preview-hours">
                            <div class="branch-preview-hour branch-opening">
                                <i class="fas fa-sun branch-text-success"></i>
                                <div class="branch-preview-hour-label">Mở cửa</div>
                                <div class="branch-preview-hour-value branch-opening" id="previewOpeningHour">{{ $branch->opening_hour }}</div>
                            </div>
                            <div class="branch-preview-hour branch-closing">
                                <i class="fas fa-moon branch-text-danger"></i>
                                <div class="branch-preview-hour-label">Đóng cửa</div>
                                <div class="branch-preview-hour-value branch-closing" id="previewClosingHour">{{ $branch->closing_hour }}</div>
                            </div>
                        </div>

                        <div class="branch-preview-status">
                            <div class="branch-preview-status-label">Trạng thái</div>
                            <div class="branch-preview-status-value {{ $branch->active ? 'branch-active' : 'branch-inactive' }}" id="previewStatus">{{ $branch->active ? 'Đang hoạt động' : 'Ngưng hoạt động' }}</div>
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
    const uploadLabelText = document.querySelector('.branch-upload-label-text');

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
        const defaultLat = parseFloat('{{ old('latitude', $branch->latitude) }}') || 21.0285;
        const defaultLng = parseFloat('{{ old('longitude', $branch->longitude) }}') || 105.8542;
        let lat = defaultLat;
        let lng = defaultLng;

        try {
            if (latitudeInput.value && longitudeInput.value) {
                const parsedLat = parseFloat(latitudeInput.value);
                const parsedLng = parseFloat(longitudeInput.value);
                if (!isNaN(parsedLat) && !isNaN(parsedLng) && parsedLat >= -90 && parsedLat <= 90 && parsedLng >= -180 && parsedLng <= 180) {
                    lat = parsedLat;
                    lng = parsedLng;
                }
            }

            map = L.map('branch-map', {
                center: [lat, lng],
                zoom: 13,
                zoomControl: true,
                scrollWheelZoom: false
            });

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                maxZoom: 19
            }).addTo(map);

            setTimeout(() => map.invalidateSize(), 300);

            if (latitudeInput.value && longitudeInput.value) {
                setMarker(lat, lng);
            }

            map.on('click', function(e) {
                setMarker(e.latlng.lat, e.latlng.lng);
            });

            window.addEventListener('resize', () => setTimeout(() => map.invalidateSize(), 100));
        } catch (error) {
            console.error('Map initialization failed:', error);
            latitudeInput.value = defaultLat.toFixed(6);
            longitudeInput.value = defaultLng.toFixed(6);
        }
    }

    function setMarker(lat, lng) {
        try {
            if (isNaN(lat) || isNaN(lng) || lat < -90 || lat > 90 || lng < -180 || lng > 180) {
                console.warn('Invalid coordinates:', lat, lng);
                return;
            }

            if (marker) map.removeLayer(marker);
            marker = L.marker([lat, lng]).addTo(map);
            map.panTo([lat, lng], { animate: true });
            latitudeInput.value = lat.toFixed(6);
            longitudeInput.value = lng.toFixed(6);
        } catch (error) {
            console.error('Failed to set marker:', error);
        }
    }

    function updatePreview() {
        previewName.textContent = nameInput.value || 'Tên chi nhánh';
        previewAddress.textContent = addressInput.value || 'Địa chỉ chi nhánh';
        previewPhone.textContent = phoneInput.value || 'Số điện thoại';
        previewEmail.textContent = emailInput.value || 'Email';
        previewEmailContainer.classList.toggle('branch-hidden', !emailInput.value);
        previewManager.textContent = managerSelect.value ? managerSelect.options[managerSelect.selectedIndex].text : 'Chưa chọn quản lý';
        previewOpeningHour.textContent = openingHourInput.value || '08:00';
        previewClosingHour.textContent = closingHourInput.value || '22:00';
        previewStatus.textContent = activeInput.checked ? 'Đang hoạt động' : 'Ngưng hoạt động';
        previewStatus.className = `branch-preview-status-value ${activeInput.checked ? 'branch-active' : 'branch-inactive'}`;
        statusHint.textContent = activeInput.checked ? 'Chi nhánh đang hoạt động' : 'Chi nhánh ngưng hoạt động';
    }

    function handleImageUpload(event) {
        const files = event.target.files;
        const maxImages = 10;
        const existingImagesCount = existingImagesContainer.querySelectorAll('.branch-image-preview-item').length;

        if (existingImagesCount + uploadedImages.length + files.length > maxImages) {
            alert(`Bạn chỉ có thể tải lên tối đa ${maxImages} hình ảnh (bao gồm cả ảnh hiện có).`);
            imagesInput.value = '';
            return;
        }

        const invalidFiles = [];
        const newImages = Array.from(files).filter((file, index) => {
            const isValid = file.type.match('image/(jpeg|png|jpg|gif)') && file.size <= 2048 * 1024;
            if (!isValid) invalidFiles.push(`Ảnh ${index + 1}: ${file.name}`);
            return isValid;
        });

        if (invalidFiles.length > 0) {
            alert(`Các hình ảnh không hợp lệ (phải là JPEG, PNG, JPG, GIF và nhỏ hơn 2MB):\n${invalidFiles.join('\n')}`);
            imagesInput.value = '';
            return;
        }

        if (newImages.length > 0) {
            uploadedImages = [...uploadedImages, ...newImages];
            updateFileInput();
            displayImagePreviews(uploadedImages);
            showImageSections();
            uploadLabelText.textContent = uploadedImages.length + ' ảnh đã chọn';
            updatePrimaryImageSelect();
            if (!primaryImageSelect.value && uploadedImages.length > 0) {
                primaryImageSelect.value = '0';
                updatePrimaryImage();
            }
        } else {
            imagesInput.value = '';
        }
    }

    function updateFileInput() {
        const dataTransfer = new DataTransfer();
        uploadedImages.forEach(file => dataTransfer.items.add(file));
        imagesInput.files = dataTransfer.files;
    }

    function displayImagePreviews(files) {
        previewContainer.innerHTML = '';
        captionInputs.innerHTML = '';

        files.forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const previewItem = document.createElement('div');
                previewItem.className = 'branch-image-preview-item';
                previewItem.dataset.newImageIndex = index;

                const img = document.createElement('img');
                img.src = e.target.result;
                img.className = 'branch-image-preview-img';
                img.alt = `Xem trước ảnh mới ${index + 1}`;

                const overlay = document.createElement('div');
                overlay.className = 'branch-image-preview-overlay';

                const actions = document.createElement('div');
                actions.className = 'branch-image-preview-actions';

                const removeBtn = document.createElement('button');
                removeBtn.className = 'branch-image-preview-btn branch-remove-btn';
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
                captionGroup.className = 'branch-form-group';
                captionGroup.dataset.newImageIndex = index;

                const captionLabel = document.createElement('label');
                captionLabel.className = 'branch-form-label';
                captionLabel.textContent = `Mô tả ảnh mới ${index + 1}:`;

                const captionInput = document.createElement('input');
                captionInput.type = 'text';
                captionInput.className = 'branch-form-control';
                captionInput.name = `captions[${index}]`;
                captionInput.maxLength = 255;
                captionInput.placeholder = 'Nhập mô tả cho ảnh...';
                captionInput.setAttribute('aria-label', `Mô tả cho ảnh mới ${index + 1}`);

                captionGroup.appendChild(captionLabel);
                captionGroup.appendChild(captionInput);
                captionInputs.appendChild(captionGroup);

                updatePrimaryImageSelect();
            };
            reader.readAsDataURL(file);
        });
    }

    function updatePrimaryImage() {
        document.querySelectorAll('.branch-image-preview-badge').forEach(badge => badge.remove());
        const selectedValue = primaryImageSelect.value;
        let selectedPreview = null;

        if (selectedValue && selectedValue.match(/^\d+$/)) {
            selectedPreview = existingImagesContainer.querySelector(`.branch-image-preview-item[data-existing-image-id="${selectedValue}"]`) ||
                             previewContainer.querySelector(`.branch-image-preview-item[data-new-image-index="${selectedValue}"]`);
        }

        if (selectedPreview) {
            const primaryBadge = document.createElement('div');
            primaryBadge.className = 'branch-image-preview-badge';
            primaryBadge.textContent = 'Ảnh chính';
            selectedPreview.appendChild(primaryBadge);
        }
    }

    function removeExistingImage(imageId) {
        deletedImageIds.push(imageId);
        const imageElement = existingImagesContainer.querySelector(`.branch-image-preview-item[data-existing-image-id="${imageId}"]`);
        if (imageElement) {
            imageElement.remove();
            const captionElement = existingCaptionInputs.querySelector(`.branch-form-group[data-existing-image-id="${imageId}"]`);
            if (captionElement) captionElement.remove();
        }
        if (existingImagesContainer.children.length === 0) {
            document.getElementById('existingImages').classList.add('branch-hidden');
            existingCaptionsContainer.classList.add('branch-hidden');
        }
        updatePrimaryImageSelect();
        if (primaryImageSelect.value === imageId) {
            primaryImageSelect.value = uploadedImages.length > 0 ? '0' : (existingImagesContainer.children.length > 0 ? existingImagesContainer.querySelector('.branch-image-preview-item').dataset.existingImageId : '');
        }
        updatePrimaryImage();
    }

    function removeNewImage(index) {
        uploadedImages.splice(index, 1);
        updateFileInput();

        if (uploadedImages.length > 0) {
            displayImagePreviews(uploadedImages);
            uploadLabelText.textContent = uploadedImages.length + ' ảnh đã chọn';
        } else {
            hideImageSections();
            imagesInput.value = '';
            uploadLabelText.textContent = 'Chọn nhiều hình ảnh...';
        }

        updatePrimaryImageSelect();
        if (primaryImageSelect.value === index.toString()) {
            primaryImageSelect.value = uploadedImages.length > 0 ? '0' : (existingImagesContainer.children.length > 0 ? existingImagesContainer.querySelector('.branch-image-preview-item').dataset.existingImageId : '');
        }
        updatePrimaryImage();
    }

    function updatePrimaryImageSelect() {
        const selectedValue = primaryImageSelect.value;
        primaryImageSelect.innerHTML = '';

        const existingImages = existingImagesContainer.querySelectorAll('.branch-image-preview-item');
        existingImages.forEach((item, index) => {
            const option = document.createElement('option');
            option.value = item.dataset.existingImageId;
            option.textContent = `Ảnh hiện có ${index + 1}`;
            if (item.dataset.existingImageId === selectedValue) option.selected = true;
            primaryImageSelect.appendChild(option);
        });

        uploadedImages.forEach((file, index) => {
            const option = document.createElement('option');
            option.value = index.toString();
            option.textContent = `Ảnh mới ${index + 1}`;
            if (index.toString() === selectedValue) option.selected = true;
            primaryImageSelect.appendChild(option);
        });

        if (primaryImageSelect.options.length === 0) {
            const option = document.createElement('option');
            option.value = '';
            option.textContent = 'Không có ảnh';
            primaryImageSelect.appendChild(option);
        }

        updatePrimaryImage();
    }

    function showImageSections() {
        imagePreview.classList.remove('branch-hidden');
        captionsContainer.classList.remove('branch-hidden');
    }

    function hideImageSections() {
        imagePreview.classList.add('branch-hidden');
        captionsContainer.classList.add('branch-hidden');
        uploadLabelText.textContent = 'Chọn nhiều hình ảnh...';
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

        document.querySelectorAll('.branch-remove-btn').forEach(button => {
            button.addEventListener('click', function(e) {
                e.stopPropagation();
                const imageId = this.dataset.existingImageId;
                removeExistingImage(imageId);
            });
        });

        initMap();

        form.addEventListener('submit', function(e) {
            if (openingHourInput.value && closingHourInput.value && openingHourInput.value >= closingHourInput.value) {
                e.preventDefault();
                alert('Giờ đóng cửa phải sau giờ mở cửa!');
                return;
            }

            const lat = parseFloat(latitudeInput.value);
            const lng = parseFloat(longitudeInput.value);
            if (isNaN(lat) || isNaN(lng) || lat < -90 || lat > 90 || lng < -180 || lng > 180) {
                e.preventDefault();
                alert('Vui lòng chọn một vị trí hợp lệ trên bản đồ!');
                return;
            }

            deletedImageIds.forEach(id => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'delete_images[]';
                input.value = id;
                form.appendChild(input);
            });
        });

        updatePrimaryImage();
    }

    initForm();
});
</script>
@endsection