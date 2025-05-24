<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm Chi Nhánh Mới</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <style>
        /* Variables */
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

        /* Base Styles */
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

        /* Typography */
        h1, h2, h3, h4, h5, h6 {
            margin: 0;
            font-weight: 600;
            line-height: 1.2;
        }

        h1 {
            font-size: 1.5rem;
        }

        h2 {
            font-size: 1.25rem;
        }

        h3 {
            font-size: 1.125rem;
        }

        h4 {
            font-size: 1rem;
        }

        p {
            margin: 0;
            line-height: 1.5;
        }

        a {
            color: var(--primary);
            text-decoration: none;
            transition: var(--transition-fast);
        }

        a:hover {
            color: var(--primary-dark);
        }

        /* Buttons */
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

        .btn-sm {
            padding: 0.375rem 0.75rem;
            font-size: 0.75rem;
        }

        .btn-block {
            width: 100%;
        }

        .btn-primary {
            background-color: var(--primary);
            color: var(--white);
        }

        .btn-primary:hover {
            background-color: var(--primary-dark);
            color: var(--white);
        }

        .btn-outline {
            background-color: transparent;
            color: var(--gray-dark);
            border: 1px solid var(--gray-light);
        }

        .btn-outline:hover {
            background-color: var(--gray-light);
            color: var(--dark);
        }

        .btn-danger {
            background-color: var(--danger);
            color: var(--white);
        }

        .btn-danger:hover {
            background-color: #e11d48;
            color: var(--white);
        }

        .btn:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }

        /* Page Header */
        .page-header {
            margin-bottom: 1.5rem;
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .header-icon {
            width: 3rem;
            height: 3rem;
            background-color: rgba(67, 97, 238, 0.1);
            color: var(--primary);
            border-radius: var(--border-radius-full);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
        }

        .header-text p {
            color: var(--gray);
            margin-top: 0.25rem;
        }

        .header-actions {
            display: flex;
            gap: 0.75rem;
        }

        /* Cards */
        .card {
            background-color: var(--white);
            border-radius: var(--border-radius-lg);
            box-shadow: var(--shadow);
            overflow: hidden;
            transition: var(--transition);
            margin-bottom: 1.5rem;
        }

        .card:hover {
            box-shadow: var(--shadow-md);
            transform: translateY(-2px);
        }

        .card-header {
            display: flex;
            align-items: center;
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid var(--gray-light);
            gap: 0.75rem;
        }

        .card-icon {
            width: 2.5rem;
            height: 2.5rem;
            background-color: rgba(67, 97, 238, 0.1);
            color: var(--primary);
            border-radius: var(--border-radius-full);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
        }

        .card-header h3 {
            flex-grow: 1;
        }

        .card-actions {
            display: flex;
            gap: 0.5rem;
        }

        .card-body {
            padding: 1.5rem;
        }

        /* Form Elements */
        .form-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1.5rem;
        }

        @media (min-width: 992px) {
            .form-grid {
                grid-template-columns: 2fr 1fr;
            }
        }

        .form-group {
            margin-bottom: 1.25rem;
        }

        .form-group:last-child {
            margin-bottom: 0;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }

        .form-label-icon {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .form-control {
            display: block;
            width: 100%;
            padding: 0.625rem 0.75rem;
            font-size: 0.875rem;
            line-height: 1.5;
            color: var(--dark);
            background-color: var(--white);
            background-clip: padding-box;
            border: 1px solid var(--gray-light);
            border-radius: var(--border-radius);
            transition: var(--transition-fast);
        }

        .form-control:focus {
            border-color: var(--primary-light);
            outline: 0;
            box-shadow: 0 0 0 2px rgba(67, 97, 238, 0.25);
        }

        select.form-control {
            appearance: none;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
            background-position: right 0.5rem center;
            background-repeat: no-repeat;
            background-size: 1.5em 1.5em;
            padding-right: 2.5rem;
        }

        textarea.form-control {
            min-height: 100px;
            resize: vertical;
        }

        .form-hint {
            margin-top: 0.375rem;
            font-size: 0.75rem;
            color: var(--gray);
        }

        .form-error {
            margin-top: 0.375rem;
            font-size: 0.75rem;
            color: var(--danger);
        }

        .form-check {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 1rem;
            border: 1px solid var(--gray-light);
            border-radius: var(--border-radius);
        }

        .form-check-content {
            flex-grow: 1;
        }

        .form-check-label {
            font-weight: 500;
        }

        .form-check-hint {
            font-size: 0.875rem;
            color: var(--gray);
        }

        /* Switch */
        .switch {
            position: relative;
            display: inline-block;
            width: 44px;
            height: 24px;
        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: var(--gray-light);
            transition: var(--transition-fast);
            border-radius: 34px;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 18px;
            width: 18px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: var(--transition-fast);
            border-radius: 50%;
        }

        input:checked + .slider {
            background-color: var(--primary);
        }

        input:focus + .slider {
            box-shadow: 0 0 1px var(--primary);
        }

        input:checked + .slider:before {
            transform: translateX(20px);
        }

        /* Grid Layout */
        .grid {
            display: grid;
            gap: 1.5rem;
        }

        .grid-2 {
            grid-template-columns: 1fr;
        }

        @media (min-width: 768px) {
            .grid-2 {
                grid-template-columns: 1fr 1fr;
            }
        }

        /* Image Upload */
        .upload-label {
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 0.75rem;
            background-color: var(--gray-light);
            border-radius: var(--border-radius);
            font-size: 0.875rem;
            transition: var(--transition-fast);
        }

        .upload-label:hover {
            background-color: var(--gray);
            color: var(--white);
        }

        .upload-input {
            display: none;
        }

        .image-preview-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 1rem;
        }

        .image-preview-item {
            position: relative;
            border-radius: var(--border-radius);
            overflow: hidden;
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

        .image-preview-caption {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 0.5rem;
            background-color: rgba(0, 0, 0, 0.7);
            color: var(--white);
            font-size: 0.75rem;
            opacity: 0;
            transition: var(--transition);
        }

        .image-preview-item:hover .image-preview-caption {
            opacity: 1;
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

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
        }

        .empty-icon {
            width: 4rem;
            height: 4rem;
            background-color: var(--gray-light);
            color: var(--gray);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin: 0 auto 1rem;
        }

        .empty-title {
            margin-bottom: 0.5rem;
            font-weight: 500;
        }

        .empty-text {
            color: var(--gray);
            margin-bottom: 1.5rem;
        }

        /* Preview Card */
        .preview-card {
            padding: 1rem;
            background-color: rgba(67, 97, 238, 0.05);
            border-radius: var(--border-radius);
            border: 1px solid rgba(67, 97, 238, 0.1);
            margin-bottom: 1rem;
        }

        .preview-header {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 0.75rem;
        }

        .preview-title {
            font-weight: 500;
        }

        .preview-item {
            display: flex;
            align-items: flex-start;
            gap: 0.5rem;
            margin-bottom: 0.5rem;
            font-size: 0.875rem;
            color: var(--gray-dark);
        }

        .preview-item i {
            margin-top: 0.25rem;
        }

        .preview-hours {
            display: flex;
            gap: 1rem;
            margin-top: 1rem;
        }

        .preview-hour {
            flex: 1;
            padding: 0.75rem;
            border-radius: var(--border-radius);
            text-align: center;
        }

        .preview-hour.opening {
            background-color: rgba(74, 222, 128, 0.1);
            border: 1px solid rgba(74, 222, 128, 0.2);
        }

        .preview-hour.closing {
            background-color: rgba(244, 63, 94, 0.1);
            border: 1px solid rgba(244, 63, 94, 0.2);
        }

        .preview-hour-label {
            font-size: 0.75rem;
            color: var(--gray);
            margin-bottom: 0.25rem;
        }

        .preview-hour-value.opening {
            color: var(--success);
            font-weight: 500;
        }

        .preview-hour-value.closing {
            color: var(--danger);
            font-weight: 500;
        }

        .preview-status {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.75rem;
            border-radius: var(--border-radius);
            border: 1px solid var(--gray-light);
            margin-top: 1rem;
        }

        .preview-status-label {
            font-weight: 500;
        }

        .preview-status-value {
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 500;
        }

        .preview-status-value.active {
            background-color: rgba(74, 222, 128, 0.1);
            color: var(--success);
        }

        .preview-status-value.inactive {
            background-color: rgba(244, 63, 94, 0.1);
            color: var(--danger);
        }

        /* Map */
        #map {
            height: 300px;
            width: 100%;
            border-radius: var(--border-radius);
            margin-bottom: 1rem;
        }

        .map-coordinates {
            display: flex;
            gap: 1rem;
        }

        .map-hint {
            margin-top: 0.5rem;
            font-size: 0.75rem;
            color: var(--gray);
        }

        /* Caption Modal */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);
        }

        .modal-content {
            background-color: var(--white);
            margin: 15% auto;
            padding: 1.5rem;
            border-radius: var(--border-radius-lg);
            box-shadow: var(--shadow-lg);
            width: 90%;
            max-width: 500px;
        }

        .modal-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1rem;
        }

        .modal-title {
            font-size: 1.25rem;
            font-weight: 600;
        }

        .modal-close {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: var(--gray);
        }

        .modal-body {
            margin-bottom: 1.5rem;
        }

        .modal-footer {
            display: flex;
            justify-content: flex-end;
            gap: 0.75rem;
        }

        /* Utilities */
        .mb-6 {
            margin-bottom: 1.5rem;
        }

        .space-y-6 > * + * {
            margin-top: 1.5rem;
        }

        .flex {
            display: flex;
        }

        .items-center {
            align-items: center;
        }

        .justify-between {
            justify-content: space-between;
        }

        .gap-2 {
            gap: 0.5rem;
        }

        .gap-3 {
            gap: 0.75rem;
        }

        .gap-4 {
            gap: 1rem;
        }

        .flex-wrap {
            flex-wrap: wrap;
        }

        .hidden {
            display: none;
        }

        .text-center {
            text-align: center;
        }

        .text-sm {
            font-size: 0.875rem;
        }

        .text-xs {
            font-size: 0.75rem;
        }

        .text-gray {
            color: var(--gray);
        }

        .text-primary {
            color: var(--primary);
        }

        .text-success {
            color: var(--success);
        }

        .text-danger {
            color: var(--danger);
        }

        .font-medium {
            font-weight: 500;
        }

        .font-semibold {
            font-weight: 600;
        }

        .w-full {
            width: 100%;
        }

        .badge {
            display: inline-block;
            padding: 0.25rem 0.5rem;
            border-radius: var(--border-radius-full);
            font-size: 0.75rem;
            font-weight: 500;
        }

        .badge-info {
            background-color: rgba(59, 130, 246, 0.1);
            color: var(--info);
        }
    </style>
</head>
<body>
<div class="branch-form-container">
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex justify-between items-center flex-wrap gap-4">
            <div class="flex items-center gap-4">
                <div class="header-icon">
                    <i class="fas fa-building"></i>
                </div>
                <div class="header-text">
                    <h1>Thêm chi nhánh mới</h1>
                    <p>Nhập thông tin chi nhánh mới</p>
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

    @if(session('error'))
        <div class="alert alert-danger mb-4">
            {{ session('error') }}
        </div>
    @endif

    <form id="branchForm" action="{{ route('admin.branches.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <div class="form-grid">
            <!-- Main Column -->
            <div class="space-y-6">
                <!-- Basic Information -->
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
                                placeholder="Nhập tên chi nhánh" value="{{ old('name') }}" required>
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
                                placeholder="Nhập địa chỉ chi nhánh" required>{{ old('address') }}</textarea>
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
                                    placeholder="Nhập số điện thoại" value="{{ old('phone') }}" required>
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
                                    placeholder="Nhập email (không bắt buộc)" value="{{ old('email') }}">
                                @error('email')
                                    <div class="form-error">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="description" class="form-label">
                                Mô tả
                            </label>
                            <textarea id="description" name="description" class="form-control" 
                                placeholder="Nhập mô tả chi nhánh (không bắt buộc)">{{ old('description') }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Operating Hours -->
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
                                    value="{{ old('opening_hour', '08:00') }}" required>
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
                                    value="{{ old('closing_hour', '22:00') }}" required>
                                @error('closing_hour')
                                    <div class="form-error">{{ $message }}</div>
                                @enderror
                                <div class="form-hint">Giờ đóng cửa phải sau giờ mở cửa</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Location -->
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
                                    value="{{ old('latitude') }}" readonly>
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
                                    value="{{ old('longitude') }}" readonly>
                                @error('longitude')
                                    <div class="form-error">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Branch Images -->
                <div class="card">
                    <div class="card-header">
                        <div class="card-icon">
                            <i class="fas fa-images"></i>
                        </div>
                        <h3>Hình ảnh chi nhánh</h3>
                        <div class="card-actions">
                            <label for="image-upload" class="upload-label">
                                <i class="fas fa-upload"></i>
                                <span>Tải lên</span>
                            </label>
                            <input type="file" id="image-upload" name="images[]" class="upload-input" accept="image/*" multiple>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="imagePreviewContainer" class="image-preview-grid hidden">
                            <!-- Image previews will be added here -->
                        </div>
                        <div id="emptyImageState" class="empty-state">
                            <div class="empty-icon">
                                <i class="fas fa-upload"></i>
                            </div>
                            <h4 class="empty-title">Chưa có hình ảnh</h4>
                            <p class="empty-text">Tải lên hình ảnh chi nhánh</p>
                            <label for="empty-image-upload" class="btn btn-primary">
                                <i class="fas fa-upload"></i>
                                <span>Tải lên hình ảnh</span>
                            </label>
                            <input type="file" id="empty-image-upload" name="images[]" class="upload-input" accept="image/*" multiple>
                        </div>
                        <input type="hidden" id="is_primary" name="primary-image-input" value="0">
                        <div class="form-hint">Chọn nhiều hình ảnh. Định dạng: JPG, PNG, GIF. Tối đa 2MB mỗi ảnh.</div>
                        @error('images')
                            <div class="form-error">{{ $message }}</div>
                         @enderror
                        @error('images.*')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Side Column -->
            <div class="space-y-6">
                <!-- Branch Code Preview -->
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
                                <span>Mã chi nhánh sẽ được tạo tự động</span>
                            </div>
                            <p class="text-sm text-gray mt-2">
                                Ví dụ: BR0001, BR0002, ...
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Manager Assignment -->
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
                                    <option value="{{ $manager->id }}" {{ old('manager_user_id') == $manager->id ? 'selected' : '' }}>
                                        {{ $manager->full_name }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-hint">
                                Chỉ hiển thị những quản lý chưa được phân công cho chi nhánh nào
                            </div>
                            @error('manager_user_id')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Status Card -->
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
                                <div class="form-check-hint" id="statusHint">Chi nhánh đang hoạt động</div>
                            </div>
                            <label class="switch">
                                <input type="checkbox" id="active" name="active" checked>
                                <span class="slider"></span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Submit Card -->
                <div class="card">
                    <div class="card-body">
                        <button type="submit" id="submitButton" class="btn btn-primary w-full">
                            <i class="fas fa-save"></i>
                            <span>Lưu chi nhánh</span>
                        </button>
                    </div>
                </div>

                <!-- Preview Card -->
                <div class="card">
                    <div class="card-header">
                        <h3>Xem trước</h3>
                    </div>
                    <div class="card-body">
                        <div class="preview-card">
                            <div class="preview-header">
                                <i class="fas fa-building text-primary"></i>
                                <h4 class="preview-title" id="previewName">Tên chi nhánh</h4>
                            </div>
                            <div class="preview-item">
                                <i class="fas fa-map-marker-alt text-danger"></i>
                                <span id="previewAddress">Địa chỉ chi nhánh</span>
                            </div>
                            <div class="preview-item">
                                <i class="fas fa-phone text-primary"></i>
                                <span id="previewPhone">Số điện thoại</span>
                            </div>
                            <div class="preview-item" id="previewEmailContainer">
                                <i class="fas fa-envelope text-primary"></i>
                                <span id="previewEmail">Email</span>
                            </div>
                            <div class="preview-item" id="previewManagerContainer">
                                <i class="fas fa-user-tie text-primary"></i>
                                <span id="previewManager">Chưa chọn quản lý</span>
                            </div>
                        </div>

                        <div class="preview-hours">
                            <div class="preview-hour opening">
                                <i class="fas fa-sun text-success"></i>
                                <div class="preview-hour-label">Mở cửa</div>
                                <div class="preview-hour-value opening" id="previewOpeningHour">08:00</div>
                            </div>
                            <div class="preview-hour closing">
                                <i class="fas fa-moon text-danger"></i>
                                <div class="preview-hour-label">Đóng cửa</div>
                                <div class="preview-hour-value closing" id="previewClosingHour">22:00</div>
                            </div>
                        </div>

                        <div class="preview-status">
                            <div class="preview-status-label">Trạng thái</div>
                            <div class="preview-status-value active" id="previewStatus">Đang hoạt động</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Caption Modal -->
<div id="captionModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">Thêm chú thích cho hình ảnh</h3>
            <button type="button" class="modal-close" id="closeCaptionModal">&times;</button>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <label for="imageCaption" class="form-label">Chú thích</label>
                <input type="text" id="imageCaption" class="form-control" placeholder="Nhập chú thích cho hình ảnh">
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-outline" id="cancelCaptionBtn">Hủy</button>
            <button type="button" class="btn btn-primary" id="saveCaptionBtn">Lưu</button>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Form elements
        const form = document.getElementById('branchForm');
        const nameInput = document.getElementById('name');
        const addressInput = document.getElementById('address');
        const phoneInput = document.getElementById('phone');
        const emailInput = document.getElementById('email');
        const openingHourInput = document.getElementById('opening_hour');
        const closingHourInput = document.getElementById('closing_hour');
        const activeInput = document.getElementById('active');
        const descriptionInput = document.getElementById('description');
        const managerSelect = document.getElementById('manager_user_id');
        const latitudeInput = document.getElementById('latitude');
        const longitudeInput = document.getElementById('longitude');
        const primaryImageInput = document.getElementById('primary_image');
        const submitButton = document.getElementById('submitButton');
        
        // Preview elements
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
        
        // Image upload elements
        const imageUpload = document.getElementById('image-upload');
        const emptyImageUpload = document.getElementById('empty-image-upload');
        const imagePreviewContainer = document.getElementById('imagePreviewContainer');
        const emptyImageState = document.getElementById('emptyImageState');
        
        // Caption modal elements
        const captionModal = document.getElementById('captionModal');
        const imageCaption = document.getElementById('imageCaption');
        const closeCaptionModal = document.getElementById('closeCaptionModal');
        const cancelCaptionBtn = document.getElementById('cancelCaptionBtn');
        const saveCaptionBtn = document.getElementById('saveCaptionBtn');
        
        // Store uploaded images and captions
        let uploadedImages = [];
        let imageCaptions = [];
        let currentEditingImageIndex = -1;
        let primaryImageIndex = 0;
        
        // Initialize map
        let map;
        let marker;
        
        function initMap() {
            // Default coordinates (Hanoi, Vietnam)
            const defaultLat = 21.0285;
            const defaultLng = 105.8542;
            
            // Use old values if available
            const oldLat = {{ old('latitude', 'null') }};
            const oldLng = {{ old('longitude', 'null') }};
            
            const initialLat = oldLat !== null ? oldLat : defaultLat;
            const initialLng = oldLng !== null ? oldLng : defaultLng;
            
            map = L.map('map').setView([initialLat, initialLng], 13);
            
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);
            
            // Add click event to map
            map.on('click', function(e) {
                setMarker(e.latlng.lat, e.latlng.lng);
            });
            
            // Set initial marker if coordinates exist
            if (oldLat !== null && oldLng !== null) {
                setMarker(oldLat, oldLng);
            }
        }
        
        function setMarker(lat, lng) {
            // Remove existing marker if any
            if (marker) {
                map.removeLayer(marker);
            }
            
            // Add new marker
            marker = L.marker([lat, lng]).addTo(map);
            
            // Update form inputs
            latitudeInput.value = lat.toFixed(6);
            longitudeInput.value = lng.toFixed(6);
        }
        
        // Initialize form
        function initForm() {
            // Update preview with default values
            updatePreview();
            
            // Add event listeners for real-time preview updates
            nameInput.addEventListener('input', updatePreview);
            addressInput.addEventListener('input', updatePreview);
            phoneInput.addEventListener('input', updatePreview);
            emailInput.addEventListener('input', updatePreview);
            managerSelect.addEventListener('change', updatePreview);
            openingHourInput.addEventListener('input', updatePreview);
            closingHourInput.addEventListener('input', updatePreview);
            activeInput.addEventListener('change', updatePreview);
            
            // Image upload handlers
            imageUpload.addEventListener('change', handleImageUpload);
            emptyImageUpload.addEventListener('change', handleImageUpload);
            
            // Caption modal handlers
            closeCaptionModal.addEventListener('click', closeCaptionModalHandler);
            cancelCaptionBtn.addEventListener('click', closeCaptionModalHandler);
            saveCaptionBtn.addEventListener('click', saveCaptionHandler);
            
            // Initialize map
            initMap();
        }
        
        // Update preview in real-time
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
            
            previewOpeningHour.textContent = openingHourInput.value;
            previewClosingHour.textContent = closingHourInput.value;
            
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
        
        // Handle image upload
        function handleImageUpload(event) {
            const files = event.target.files;
            
            if (files.length > 0) {
                // Show preview container and hide empty state
                imagePreviewContainer.classList.remove('hidden');
                emptyImageState.classList.add('hidden');
                
                // Process each file
                for (let i = 0; i < files.length; i++) {
                    const file = files[i];
                    
                    // Only process image files
                    if (!file.type.match('image.*')) continue;
                    
                    // Add to uploaded images array
                    uploadedImages.push(file);
                    imageCaptions.push('');
                    
                    // Create preview
                    const reader = new FileReader();
                    
                    reader.onload = function(e) {
                        const previewIndex = uploadedImages.length - 1;
                        
                        // Create preview item
                        createImagePreview(previewIndex, e.target.result);
                    };
                    
                    reader.readAsDataURL(file);
                }
            }
            
            // Reset file input
            event.target.value = '';
        }
        
        // Create image preview
        function createImagePreview(index, src) {
            // Create preview item
            const previewItem = document.createElement('div');
            previewItem.className = 'image-preview-item';
            previewItem.dataset.index = index;
            
            // Create image
            const img = document.createElement('img');
            img.src = src;
            img.className = 'image-preview-img';
            img.alt = `Preview ${index + 1}`;
            
            // Create overlay
            const overlay = document.createElement('div');
            overlay.className = 'image-preview-overlay';
            
            // Create actions container
            const actions = document.createElement('div');
            actions.className = 'image-preview-actions';
            
            // Create remove button
            const removeBtn = document.createElement('button');
            removeBtn.className = 'image-preview-btn remove-btn';
            removeBtn.innerHTML = '<i class="fas fa-trash-alt"></i>';
            removeBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                removeImage(index);
            });
            
            // Create caption button
            const captionBtn = document.createElement('button');
            captionBtn.className = 'image-preview-btn';
            captionBtn.innerHTML = '<i class="fas fa-comment"></i>';
            captionBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                openCaptionModal(index);
            });
            
            // Create primary button or set primary button
            if (index === primaryImageIndex) {
                const primaryBtn = document.createElement('button');
                primaryBtn.className = 'image-preview-btn primary-btn';
                primaryBtn.innerHTML = '<i class="fas fa-star"></i>';
                actions.appendChild(primaryBtn);
                
                // Add primary badge
                const primaryBadge = document.createElement('div');
                primaryBadge.className = 'image-preview-badge';
                primaryBadge.textContent = 'Ảnh chính';
                previewItem.appendChild(primaryBadge);
            } else {
                const setPrimaryBtn = document.createElement('button');
                setPrimaryBtn.className = 'image-preview-btn set-primary-btn';
                setPrimaryBtn.innerHTML = '<i class="far fa-star"></i>';
                setPrimaryBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    setPrimaryImage(index);
                });
                actions.appendChild(setPrimaryBtn);
            }
            
            // Add caption display if exists
            if (imageCaptions[index]) {
                const captionDisplay = document.createElement('div');
                captionDisplay.className = 'image-preview-caption';
                captionDisplay.textContent = imageCaptions[index];
                previewItem.appendChild(captionDisplay);
            }
            
            // Assemble preview item
            actions.appendChild(captionBtn);
            actions.appendChild(removeBtn);
            overlay.appendChild(actions);
            previewItem.appendChild(img);
            previewItem.appendChild(overlay);
            
            // Add to container
            imagePreviewContainer.appendChild(previewItem);
            
            // Create hidden input for caption
            const captionInput = document.createElement('input');
            captionInput.type = 'hidden';
            captionInput.name = `captions[${index}]`;
            captionInput.value = imageCaptions[index] || '';
            captionInput.id = `caption-${index}`;
            imagePreviewContainer.appendChild(captionInput);
        }
        
        // Remove image from preview
        function removeImage(index) {
            // Remove from arrays
            uploadedImages.splice(index, 1);
            imageCaptions.splice(index, 1);
            
            // Update primary image index if needed
            if (primaryImageIndex === index) {
                primaryImageIndex = 0;
            } else if (primaryImageIndex > index) {
                primaryImageIndex--;
            }
            
            // Update hidden input
            primaryImageInput.value = primaryImageIndex;
            
            // Rebuild preview
            rebuildImagePreviews();
        }
        
        // Set primary image
        function setPrimaryImage(index) {
            primaryImageIndex = index;
            primaryImageInput.value = index;
            rebuildImagePreviews();
        }
        
        // Rebuild image previews
        function rebuildImagePreviews() {
            // Clear container
            imagePreviewContainer.innerHTML = '';
            
            if (uploadedImages.length === 0) {
                // Show empty state if no images
                imagePreviewContainer.classList.add('hidden');
                emptyImageState.classList.remove('hidden');
            } else {
                // Recreate previews
                uploadedImages.forEach((file, idx) => {
                    const reader = new FileReader();
                    
                    reader.onload = function(e) {
                        createImagePreview(idx, e.target.result);
                    };
                    
                    reader.readAsDataURL(file);
                });
            }
        }
        
        // Open caption modal
        function openCaptionModal(index) {
            currentEditingImageIndex = index;
            imageCaption.value = imageCaptions[index] || '';
            captionModal.style.display = 'block';
        }
        
        // Close caption modal
        function closeCaptionModalHandler() {
            captionModal.style.display = 'none';
            currentEditingImageIndex = -1;
        }
        
        // Save caption
        function saveCaptionHandler() {
            if (currentEditingImageIndex >= 0) {
                imageCaptions[currentEditingImageIndex] = imageCaption.value;
                
                // Update hidden input
                const captionInput = document.getElementById(`caption-${currentEditingImageIndex}`);
                if (captionInput) {
                    captionInput.value = imageCaption.value;
                }
                
                // Rebuild previews to show caption
                rebuildImagePreviews();
            }
            
            closeCaptionModalHandler();
        }
        
        // Initialize the form
        initForm();
    });
</script>
</body>
</html>