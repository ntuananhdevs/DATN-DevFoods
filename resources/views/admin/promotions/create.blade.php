@extends('layouts/admin/contentLayoutMaster')

@section('title', 'Tạo chương trình khuyến mãi')

@section('styles')
<link href="https://cdnjs.cloudflare.com/ajax/libs/feather-icons/4.28.0/feather.min.css" rel="stylesheet">
<style>
    /* Dark mode variables */
    :root {
        --background: 0 0% 100%;
        --foreground: 222.2 84% 4.9%;
        --card: 0 0% 100%;
        --card-foreground: 222.2 84% 4.9%;
        --border: 214.3 31.8% 91.4%;
        --primary: 221.2 83.2% 53.3%;
        --primary-gradient-start: #667eea;
        --primary-gradient-end: #764ba2;
    }

    .dark {
        --background: 222.2 84% 4.9%;
        --foreground: 210 40% 98%;
        --card: 222.2 84% 4.9%;
        --card-foreground: 210 40% 98%;
        --border: 217.2 32.6% 17.5%;
        --primary: 217.2 91.2% 59.8%;
        --primary-gradient-start: #4f6ce7;
        --primary-gradient-end: #8b5dc7;
    }

    /* Theme toggle button */
    .theme-toggle {
        position: relative;
        width: 44px;
        height: 24px;
        background-color: #f3f4f6;
        border-radius: 12px;
        transition: background-color 0.3s ease;
        cursor: pointer;
        border: 1px solid #e5e7eb;
    }

    .dark .theme-toggle {
        background-color: hsl(var(--primary));
        border-color: hsl(var(--border));
    }

    .theme-toggle-handle {
        position: absolute;
        top: 2px;
        left: 2px;
        width: 18px;
        height: 18px;
        background-color: white;
        border-radius: 50%;
        transition: transform 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 10px;
    }

    .dark .theme-toggle-handle {
        transform: translateX(20px);
        background-color: hsl(var(--background));
    }

    .container {
        max-width: 1200px;
        margin: 0 auto;
        animation: fadeIn 0.6s ease-out;
        margin: 0;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .header {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-radius: 15px;
        padding: 25px 30px;
        margin-bottom: 25px;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .dark .header {
        background: rgba(36, 36, 40, 0.95);
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
        border: 1px solid rgba(255, 255, 255, 0.05);
    }

    .header h1 {
        color: #2d3748;
        font-size: 28px;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 12px;
        margin: 0;
    }

    .dark .header h1 {
        color: #e2e8f0;
    }

    .header .icon {
        width: 32px;
        height: 32px;
        background: linear-gradient(135deg, var(--primary-gradient-start), var(--primary-gradient-end));
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
    }

    .breadcrumb {
        margin-top: 10px;
        display: flex;
        align-items: center;
        gap: 8px;
        color: #718096;
        font-size: 14px;
    }

    .dark .breadcrumb {
        color: #94a3b8;
    }

    .breadcrumb a {
        color: #667eea;
        text-decoration: none;
        transition: color 0.2s;
    }

    .dark .breadcrumb a {
        color: #60a5fa;
    }

    .breadcrumb a:hover {
        color: #764ba2;
    }

    .dark .breadcrumb a:hover {
        color: #8b5dc7;
    }

    .form-container {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-radius: 15px;
        padding: 30px;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .dark .form-container {
        background: rgba(36, 36, 40, 0.95);
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
        border: 1px solid rgba(255, 255, 255, 0.05);
    }

    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 25px;
    }

    .form-section {
        background: #f8fafc;
        border-radius: 12px;
        padding: 25px;
        border: 1px solid #e2e8f0;
    }

    .dark .form-section {
        background: #1e293b;
        border: 1px solid #334155;
    }

    .form-section h3 {
        color: #2d3748;
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .dark .form-section h3 {
        color: #e2e8f0;
    }

    .form-section h3 i {
        color: #667eea;
    }

    .dark .form-section h3 i {
        color: #60a5fa;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group.full-width {
        grid-column: 1 / -1;
    }

    label {
        display: block;
        margin-bottom: 8px;
        color: #374151;
        font-weight: 500;
        font-size: 14px;
    }

    .dark label {
        color: #e2e8f0;
    }

    .required {
        color: #ef4444;
    }

    .form-control,
    input[type="text"],
    input[type="number"],
    input[type="datetime-local"],
    input[type="file"],
    textarea,
    select {
        width: 100%;
        padding: 12px 16px;
        border: 2px solid #e5e7eb;
        border-radius: 8px;
        font-size: 14px;
        transition: all 0.2s;
        background: white;
        color: #374151;
    }

    .dark .form-control,
    .dark input[type="text"],
    .dark input[type="number"],
    .dark input[type="datetime-local"],
    .dark input[type="file"],
    .dark textarea,
    .dark select {
        background: #1f2937;
        border-color: #374151;
        color: #e5e7eb;
    }

    .form-control:focus,
    input[type="text"]:focus,
    input[type="number"]:focus,
    input[type="datetime-local"]:focus,
    textarea:focus,
    select:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .dark .form-control:focus,
    .dark input[type="text"]:focus,
    .dark input[type="number"]:focus,
    .dark input[type="datetime-local"]:focus,
    .dark textarea:focus,
    .dark select:focus {
        border-color: #60a5fa;
        box-shadow: 0 0 0 3px rgba(96, 165, 250, 0.1);
    }

    textarea {
        resize: vertical;
        min-height: 100px;
    }

    .checkbox-group {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 12px 16px;
        background: white;
        border: 2px solid #e5e7eb;
        border-radius: 8px;
        transition: all 0.2s;
        cursor: pointer;
    }

    .dark .checkbox-group {
        background: #1f2937;
        border-color: #374151;
    }

    .checkbox-group:hover {
        border-color: #667eea;
    }

    .dark .checkbox-group:hover {
        border-color: #60a5fa;
    }

    .checkbox-group input[type="checkbox"] {
        width: auto;
        margin: 0;
    }

    .checkbox-group label {
        margin: 0;
        cursor: pointer;
        flex: 1;
    }

    .file-input-wrapper {
        position: relative;
        overflow: hidden;
        display: inline-block;
        width: 100%;
    }

    .file-input-wrapper input[type="file"] {
        position: absolute;
        left: -9999px;
    }

    .file-input-label {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 12px 16px;
        background: white;
        border: 2px dashed #d1d5db;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.2s;
        color: #6b7280;
    }

    .dark .file-input-label {
        background: #1f2937;
        border-color: #4b5563;
        color: #9ca3af;
    }

    .file-input-label:hover {
        border-color: #667eea;
        background: #f8fafc;
    }

    .dark .file-input-label:hover {
        border-color: #60a5fa;
        background: #1e293b;
    }

    .file-input-label i {
        color: #667eea;
    }

    .dark .file-input-label i {
        color: #60a5fa;
    }

    .current-file {
        margin-top: 8px;
        padding: 8px 12px;
        background: #f0f9ff;
        border: 1px solid #bae6fd;
        border-radius: 6px;
        font-size: 12px;
        color: #0369a1;
    }

    .dark .current-file {
        background: #0c4a6e;
        border-color: #075985;
        color: #bae6fd;
    }

    .branch-selection {
        display: none;
    }

    .branch-selection.show {
        display: block;
    }

    .branch-checkboxes {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 10px;
        max-height: 200px;
        overflow-y: auto;
        padding: 10px;
        border: 2px solid #e5e7eb;
        border-radius: 8px;
        background: white;
    }

    .dark .branch-checkboxes {
        background: #1f2937;
        border-color: #374151;
    }

    .form-actions {
        margin-top: 30px;
        padding-top: 25px;
        border-top: 1px solid #e5e7eb;
        display: flex;
        gap: 15px;
        justify-content: flex-end;
    }

    .dark .form-actions {
        border-top-color: #374151;
    }

    .btn {
        padding: 12px 24px;
        border-radius: 8px;
        font-weight: 500;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s;
        border: none;
        cursor: pointer;
        font-size: 14px;
    }

    .btn-primary {
        background: linear-gradient(135deg, var(--primary-gradient-start), var(--primary-gradient-end));
        color: white;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
    }

    .dark .btn-primary:hover {
        box-shadow: 0 8px 25px rgba(96, 165, 250, 0.3);
    }

    .btn-secondary {
        background: #f8fafc;
        color: #374151;
        border: 1px solid #d1d5db;
    }

    .dark .btn-secondary {
        background: #1e293b;
        color: #e5e7eb;
        border-color: #4b5563;
    }

    .btn-secondary:hover {
        background: #e5e7eb;
    }

    .dark .btn-secondary:hover {
        background: #2d3748;
    }

    .alert {
        padding: 16px 20px;
        border-radius: 8px;
        margin-bottom: 20px;
        border-left: 4px solid;
    }

    .alert-danger {
        background: #fef2f2;
        border-color: #ef4444;
        color: #dc2626;
    }

    .dark .alert-danger {
        background: #450a0a;
        border-color: #b91c1c;
        color: #fca5a5;
    }

    .alert ul {
        margin: 0;
        padding-left: 20px;
    }

    .alert li {
        margin-bottom: 4px;
    }

    @media (max-width: 768px) {
        .form-grid {
            grid-template-columns: 1fr;
            gap: 20px;
        }
        
        .form-actions {
            flex-direction: column;
        }
        
        .btn {
            justify-content: center;
        }
        
        .branch-checkboxes {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection

@section('content')
<div class="container">
    <!-- Header -->
    <div class="header">
        <div class="flex justify-between items-center">
            <h1>
                <div class="icon">
                    <i data-feather="plus-circle"></i>
                </div>
                Tạo chương trình khuyến mãi
            </h1>
            <div class="flex items-center gap-2">
                <span class="text-sm text-muted-foreground">Theme:</span>
                <button id="themeToggle" class="theme-toggle">
                    <div class="theme-toggle-handle">
                        <span id="themeIcon">🌙</span>
                    </div>
                </button>
            </div>
        </div>
        <div class="breadcrumb">
            <a href="{{ route('admin.promotions.index') }}">Chương trình khuyến mãi</a>
            <i data-feather="chevron-right"></i>
            <span>Tạo mới</span>
        </div>
    </div>

    <!-- Error Messages -->
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Form -->
    <div class="form-container">
        <form action="{{ route('admin.promotions.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="form-grid">
                <!-- Basic Information -->
                <div class="form-section">
                    <h3>
                        <i data-feather="info"></i>
                        Thông tin cơ bản
                    </h3>
                    
                    <div class="form-group">
                        <label for="name">Tên chương trình <span class="required">*</span></label>
                        <input type="text" id="name" name="name" class="form-control" value="{{ old('name') }}" required>
                    </div>

                    <div class="form-group">
                        <label for="description">Mô tả</label>
                        <textarea id="description" name="description" class="form-control" placeholder="Nhập mô tả chương trình...">{{ old('description') }}</textarea>
                    </div>

                    <div class="form-group">
                        <label for="display_order">Thứ tự hiển thị <span class="required">*</span></label>
                        <input type="number" id="display_order" name="display_order" class="form-control" value="{{ old('display_order', 0) }}" min="0" required>
                    </div>
                </div>

                <!-- Schedule & Status -->
                <div class="form-section">
                    <h3>
                        <i data-feather="calendar"></i>
                        Thời gian & Trạng thái
                    </h3>
                    
                    <div class="form-group">
                        <label for="start_date">Ngày bắt đầu <span class="required">*</span></label>
                        <input type="datetime-local" id="start_date" name="start_date" class="form-control" value="{{ old('start_date') }}" required>
                    </div>

                    <div class="form-group">
                        <label for="end_date">Ngày kết thúc <span class="required">*</span></label>
                        <input type="datetime-local" id="end_date" name="end_date" class="form-control" value="{{ old('end_date') }}" required>
                    </div>

                    <div class="form-group">
                        <div class="checkbox-group">
                            <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                            <label for="is_active">Chương trình hoạt động</label>
                            <i data-feather="toggle-right"></i>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="checkbox-group">
                            <input type="checkbox" id="is_featured" name="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }}>
                            <label for="is_featured">Chương trình nổi bật</label>
                            <i data-feather="star"></i>
                        </div>
                    </div>
                </div>

                <!-- Images -->
                <div class="form-section">
                    <h3>
                        <i data-feather="image"></i>
                        Hình ảnh
                    </h3>
                    
                    <div class="form-group">
                        <label for="banner_image">Hình banner</label>
                        <div class="file-input-wrapper">
                            <input type="file" id="banner_image" name="banner_image" accept="image/*">
                            <label for="banner_image" class="file-input-label">
                                <i data-feather="upload"></i>
                                <span>Chọn hình banner...</span>
                            </label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="thumbnail_image">Hình thu nhỏ</label>
                        <div class="file-input-wrapper">
                            <input type="file" id="thumbnail_image" name="thumbnail_image" accept="image/*">
                            <label for="thumbnail_image" class="file-input-label">
                                <i data-feather="upload"></i>
                                <span>Chọn hình thu nhỏ...</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Scope & Branches -->
                <div class="form-section">
                    <h3>
                        <i data-feather="map-pin"></i>
                        Phạm vi áp dụng
                    </h3>
                    
                    <div class="form-group">
                        <label for="applicable_scope">Phạm vi <span class="required">*</span></label>
                        <select id="applicable_scope" name="applicable_scope" class="form-control" required>
                            <option value="all_branches" {{ old('applicable_scope') == 'all_branches' ? 'selected' : '' }}>Tất cả chi nhánh</option>
                            <option value="specific_branches" {{ old('applicable_scope') == 'specific_branches' ? 'selected' : '' }}>Chi nhánh cụ thể</option>
                        </select>
                    </div>

                    <div class="form-group branch-selection" id="branch_selection">
                        <label>Chọn chi nhánh <span class="required">*</span></label>
                        <div class="branch-checkboxes">
                            @foreach ($branches as $branch)
                                <div class="checkbox-group">
                                    <input type="checkbox" id="branch_{{ $branch->id }}" name="branch_ids[]" value="{{ $branch->id }}" {{ in_array($branch->id, old('branch_ids', [])) ? 'checked' : '' }}>
                                    <label for="branch_{{ $branch->id }}">{{ $branch->name }}</label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="form-actions">
                <a href="{{ route('admin.promotions.index') }}" class="btn btn-secondary">
                    <i data-feather="x"></i>
                    Hủy
                </a>
                <button type="submit" class="btn btn-primary">
                    <i data-feather="save"></i>
                    Tạo chương trình
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://unpkg.com/feather-icons"></script>
<script>
    feather.replace();

    // Theme Management
    function initThemeToggle() {
        const themeToggle = document.getElementById('themeToggle');
        const themeIcon = document.getElementById('themeIcon');
        const html = document.documentElement;
        
        // Load saved theme or default to light
        const savedTheme = localStorage.getItem('theme') || 'light';
        setTheme(savedTheme);
        
        function setTheme(theme) {
            if (theme === 'dark') {
                html.classList.add('dark');
                themeToggle.classList.add('dark');
                themeIcon.textContent = '☀️';
            } else {
                html.classList.remove('dark');
                themeToggle.classList.remove('dark');
                themeIcon.textContent = '🌙';
            }
            localStorage.setItem('theme', theme);
        }
        
        themeToggle.addEventListener('click', function() {
            const currentTheme = html.classList.contains('dark') ? 'dark' : 'light';
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            setTheme(newTheme);
        });
    }

    // Initialize theme
    document.addEventListener('DOMContentLoaded', function() {
        initThemeToggle();
    });

    // Handle scope selection
    document.getElementById('applicable_scope').addEventListener('change', function() {
        const branchSelection = document.getElementById('branch_selection');
        if (this.value === 'specific_branches') {
            branchSelection.classList.add('show');
        } else {
            branchSelection.classList.remove('show');
        }
    });

    // Initialize scope visibility
    if (document.getElementById('applicable_scope').value === 'specific_branches') {
        document.getElementById('branch_selection').classList.add('show');
    }

    // File input labels
    document.querySelectorAll('input[type="file"]').forEach(input => {
        input.addEventListener('change', function() {
            const label = this.nextElementSibling.querySelector('span');
            if (this.files.length > 0) {
                label.textContent = this.files[0].name;
            } else {
                if (this.id === 'banner_image') {
                    label.textContent = 'Chọn hình banner...';
                } else {
                    label.textContent = 'Chọn hình thu nhỏ...';
                }
            }
        });
    });
</script>
@endsection
