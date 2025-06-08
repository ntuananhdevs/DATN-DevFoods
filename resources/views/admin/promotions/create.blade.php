@extends('layouts/admin/contentLayoutMaster')

@section('title', 'Tạo chương trình khuyến mãi')

@section('styles')
<link href="https://cdnjs.cloudflare.com/ajax/libs/feather-icons/4.28.0/feather.min.css" rel="stylesheet">
<style>
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

    .header h1 {
        color: #2d3748;
        font-size: 28px;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 12px;
        margin: 0;
    }

    .header .icon {
        width: 32px;
        height: 32px;
        background: linear-gradient(135deg, #667eea, #764ba2);
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

    .breadcrumb a {
        color: #667eea;
        text-decoration: none;
        transition: color 0.2s;
    }

    .breadcrumb a:hover {
        color: #764ba2;
    }

    .form-container {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-radius: 15px;
        padding: 30px;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
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

    .form-section h3 {
        color: #2d3748;
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .form-section h3 i {
        color: #667eea;
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

    .checkbox-group:hover {
        border-color: #667eea;
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

    .file-input-label:hover {
        border-color: #667eea;
        background: #f8fafc;
    }

    .file-input-label i {
        color: #667eea;
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

    .form-actions {
        margin-top: 30px;
        padding-top: 25px;
        border-top: 1px solid #e5e7eb;
        display: flex;
        gap: 15px;
        justify-content: flex-end;
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
        background: linear-gradient(135deg, #667eea, #764ba2);
        color: white;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
    }

    .btn-secondary {
        background: #f8fafc;
        color: #374151;
        border: 1px solid #d1d5db;
    }

    .btn-secondary:hover {
        background: #e5e7eb;
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
        <h1>
            <div class="icon">
                <i data-feather="plus-circle"></i>
            </div>
            Tạo chương trình khuyến mãi
        </h1>
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
