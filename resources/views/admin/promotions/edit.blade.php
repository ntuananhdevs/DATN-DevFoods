@extends('layouts/admin/contentLayoutMaster')

@section('title', 'Chỉnh sửa chương trình khuyến mãi')

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
        display: flex;
        align-items: center;
        gap: lateral;
    }

    .current-image {
        margin-top: 10px;
        border-radius: 8px;
        overflow: hidden;
        border: 1px solid #e5e7eb;
    }

    .current-image img {
        width: 100%;
        max-width: 200px;
        height: auto;
        display: block;
    }

    .branch-selection {
        display: none;
    }

    .branch-selection.show {
        display: block;
    }

    .branch-checkboxes, .rank-checkboxes, .discount-checkboxes {
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
        border-top: 1px solid #e2e8f0;
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

    .badge {
        font-size: 12px;
        padding: 4px 8px;
        border-radius: 9999px;
        font-weight: 500;
    }

    .badge-percentage {
        background-color: #dcfce7;
        color: #16a34a;
    }

    .badge-fixed {
        background-color: #e0f2fe;
        color: #0284c7;
    }

    .badge-special {
        background-color: #f3e8ff;
        color: #7c3aed;
    }

    .discount-code-item {
        background: #f9fafb;
        padding: 1rem;
        border-radius: 8px;
        border: 1px solid #e5e7eb;
        margin-bottom: 10px;
    }

    .rank-item {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 6px 12px;
        border-radius: 6px;
        margin-bottom: 4px;
    }

    .rank-item img {
        width: 16px;
        height: 16px;
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
        
        .branch-checkboxes, .rank-checkboxes, .discount-checkboxes {
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
                <i data-feather="edit-3"></i>
            </div>
            Chỉnh sửa {{ $program->name }}
        </h1>
        <div class="breadcrumb">
            <a href="{{ route('admin.promotions.index') }}">Chương trình khuyến mãi</a>
            <i data-feather="chevron-right"></i>
            <a href="{{ route('admin.promotions.show', $program) }}">{{ $program->name }}</a>
            <i data-feather="chevron-right"></i>
            <span>Chỉnh sửa</span>
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
        <form action="{{ route('admin.promotions.update', $program) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="form-grid">
                <!-- Basic Information -->
                <div class="form-section">
                    <h3>
                        <i data-feather="info"></i>
                        Thông tin cơ bản
                    </h3>
                    
                    <div class="form-group">
                        <label for="name">Tên chương trình <span class="required">*</span></label>
                        <input type="text" id="name" name="name" class="form-control" value="{{ old('name', $program->name) }}" required>
                    </div>

                    <div class="form-group">
                        <label for="description">Mô tả</label>
                        <textarea id="description" name="description" class="form-control" placeholder="Nhập mô tả chương trình...">{{ old('description', $program->description) }}</textarea>
                    </div>

                    <div class="form-group">
                        <label for="display_order">Thứ tự hiển thị <span class="required">*</span></label>
                        <input type="number" id="display_order" name="display_order" class="form-control" value="{{ old('display_order', $program->display_order) }}" min="0" required>
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
                        <input type="datetime-local" id="start_date" name="start_date" class="form-control" value="{{ old('start_date', $program->start_date->setTimezone('Asia/Ho_Chi_Minh')->format('Y-m-d\TH:i')) }}" required>
                    </div>

                    <div class="form-group">
                        <label for="end_date">Ngày kết thúc <span class="required">*</span></label>
                        <input type="datetime-local" id="end_date" name="end_date" class="form-control" value="{{ old('end_date', $program->end_date->setTimezone('Asia/Ho_Chi_Minh')->format('Y-m-d\TH:i')) }}" required>
                    </div>

                    <div class="form-group">
                        <div class="checkbox-group">
                            <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $program->is_active) ? 'checked' : '' }}>
                            <label for="is_active">Chương trình hoạt động</label>
                            <i data-feather="toggle-right"></i>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="checkbox-group">
                            <input type="checkbox" id="is_featured" name="is_featured" value="0" {{ old('is_featured', $program->is_featured) ? 'checked' : '' }}>
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
                                <span>Chọn hình banner mới...</span>
                            </label>
                        </div>
                        @if ($program->banner_image)
                            <div class="current-file">
                                <i data-feather="file-text"></i>
                                Hiện tại: {{ basename($program->banner_image) }}
                            </div>
                            <div class="current-image">
                                <img src="{{ asset('storage/' . $program->banner_image) }}" alt="Banner hiện tại">
                            </div>
                        @endif
                    </div>

                    <div class="form-group">
                        <label for="thumbnail_image">Hình thu nhỏ</label>
                        <div class="file-input-wrapper">
                            <input type="file" id="thumbnail_image" name="thumbnail_image" accept="image/*">
                            <label for="thumbnail_image" class="file-input-label">
                                <i data-feather="upload"></i>
                                <span>Chọn hình thu nhỏ mới...</span>
                            </label>
                        </div>
                        @if ($program->thumbnail_image)
                            <div class="current-file">
                                <i data-feather="file-text"></i>
                                Hiện tại: {{ basename($program->thumbnail_image) }}
                            </div>
                            <div class="current-image">
                                <img src="{{ asset('storage/' . $program->thumbnail_image) }}" alt="Thumbnail hiện tại">
                            </div>
                        @endif
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
                            <option value="all_branches" {{ old('applicable_scope', $program->applicable_scope) == 'all_branches' ? 'selected' : '' }}>Tất cả chi nhánh</option>
                            <option value="specific_branches" {{ old('applicable_scope', $program->applicable_scope) == 'specific_branches' ? 'selected' : '' }}>Chi nhánh cụ thể</option>
                        </select>
                    </div>

                    <div class="form-group branch-selection {{ $program->applicable_scope == 'specific_branches' ? 'show' : '' }}" id="branch_selection">
                        <label>Chọn chi nhánh <span class="required">*</span></label>
                        <div class="branch-checkboxes">
                            @foreach ($branches as $branch)
                                <div class="checkbox-group">
                                    <input type="checkbox" id="branch_{{ $branch->id }}" name="branch_ids[]" value="{{ $branch->id }}" {{ $program->branches->contains($branch->id) || in_array($branch->id, old('branch_ids', [])) ? 'checked' : '' }}>
                                    <label for="branch_{{ $branch->id }}">{{ $branch->name }}</label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                
                <!-- Discount Codes (Read-only summary) -->
                <div class="form-section">
                    <h3>
                        <i data-feather="tag"></i>
                        Mã giảm giá đã liên kết
                    </h3>
                    
                    <div class="form-group">
                        @if($program->discountCodes->isNotEmpty())
                            <div class="discount-checkboxes">
                                @foreach($program->discountCodes as $discount)
                                    <div class="discount-code-item">
                                        <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                                            <strong>{{ $discount->code }}</strong>
                                            @php
                                                $typeClass = 'badge-percentage';
                                                $typeText = $discount->discount_value . '%';
                                                
                                                if ($discount->discount_type == 'fixed_amount') {
                                                    $typeClass = 'badge-fixed';
                                                    $typeText = number_format($discount->discount_value) . ' đ';
                                                } elseif ($discount->discount_type == 'free_shipping') {
                                                    $typeClass = 'badge-special';
                                                    $typeText = 'Miễn phí vận chuyển';
                                                }
                                            @endphp
                                            <span class="badge {{ $typeClass }}">{{ $typeText }}</span>
                                        </div>
                                        <div style="font-size: 13px; color: #64748b">{{ $discount->name }}</div>
                                        @if($discount->applicable_ranks)
                                            @php
                                                $ranks = is_string($discount->applicable_ranks) ? json_decode($discount->applicable_ranks, true) : $discount->applicable_ranks;
                                                $ranks = is_array($ranks) ? $ranks : [];
                                                $rankCount = count($ranks);
                                            @endphp
                                            <div style="margin-top: 8px; font-size: 12px; color: #475569;">
                                                <i data-feather="users" style="width: 14px; height: 14px;"></i>
                                                {{ $rankCount > 0 ? $rankCount . ' hạng thành viên' : 'Tất cả hạng' }}
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                            <div style="margin-top: 10px;">
                                <p style="font-size: 13px; color: #64748b;">
                                    <i data-feather="info" style="width: 14px; height: 14px;"></i>
                                    Để thêm/xóa mã giảm giá, hãy sử dụng chức năng quản lý trên trang chi tiết chương trình.
                                </p>
                            </div>
                        @else
                            <div style="padding: 20px; text-align: center; background: #f9fafb; border-radius: 8px; border: 1px dashed #d1d5db;">
                                <div style="color: #64748b; margin-bottom: 5px;">
                                    <i data-feather="alert-circle"></i>
                                </div>
                                <p style="margin: 0; color: #475569;">Chưa có mã giảm giá nào được liên kết</p>
                                <p style="margin-top: 5px; font-size: 13px; color: #64748b;">
                                    Quay lại trang chi tiết chương trình để thêm mã giảm giá sau khi cập nhật.
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="form-actions">
                <a href="{{ route('admin.promotions.show', $program) }}" class="btn btn-secondary">
                    <i data-feather="x"></i>
                    Hủy
                </a>
                <button type="submit" class="btn btn-primary">
                    <i data-feather="save"></i>
                    Cập nhật chương trình
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

    // File input labels
    document.querySelectorAll('input[type="file"]').forEach(input => {
        input.addEventListener('change', function() {
            const label = this.nextElementSibling.querySelector('span');
            if (this.files.length > 0) {
                label.textContent = this.files[0].name;
            } else {
                if (this.id === 'banner_image') {
                    label.textContent = 'Chọn hình banner mới...';
                } else {
                    label.textContent = 'Chọn hình thu nhỏ mới...';
                }
            }
        });
    });
</script>
@endsection