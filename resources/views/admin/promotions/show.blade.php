@extends('layouts/admin/contentLayoutMaster')

@section('title', 'Chi tiết chương trình khuyến mãi')

@section('content')
<style>
    .fade-in {
        animation: fadeIn 0.5s ease-in;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .detail-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        border: 1px solid #e5e7eb;
        transition: all 0.3s ease;
    }

    .detail-card:hover {
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        transform: translateY(-2px);
    }

    .detail-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 1.5rem;
        border-radius: 12px 12px 0 0;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .detail-content {
        padding: 1.5rem;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .info-item {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .info-label {
        font-weight: 600;
        color: #374151;
        font-size: 0.875rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .info-value {
        color: #1f2937;
        font-size: 1rem;
        line-height: 1.5;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.875rem;
        font-weight: 500;
    }

    .status-active {
        background: #dcfce7;
        color: #166534;
    }

    .status-inactive {
        background: #fee2e2;
        color: #991b1b;
    }

    .status-featured {
        background: #fef3c7;
        color: #92400e;
    }

    .image-preview {
        max-width: 200px;
        border-radius: 8px;
        border: 2px solid #e5e7eb;
        transition: transform 0.3s ease;
    }

    .image-preview:hover {
        transform: scale(1.05);
    }

    .section-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .data-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 1rem;
    }

    .data-table th {
        background: #f9fafb;
        padding: 12px;
        text-align: left;
        font-weight: 600;
        color: #374151;
        border-bottom: 2px solid #e5e7eb;
        font-size: 0.875rem;
    }

    .data-table td {
        padding: 12px;
        border-bottom: 1px solid #e5e7eb;
        color: #1f2937;
    }

    .data-table tr:hover {
        background: #f9fafb;
    }

    .type-badge {
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 500;
        text-transform: uppercase;
    }

    .type-percentage {
        background: #dbeafe;
        color: #1e40af;
    }

    .type-fixed {
        background: #d1fae5;
        color: #065f46;
    }

    .action-btn {
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 0.875rem;
        font-weight: 500;
        text-decoration: none;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
    }

    .btn-danger {
        background: #fee2e2;
        color: #991b1b;
    }

    .btn-danger:hover {
        background: #fecaca;
        transform: translateY(-1px);
    }

    .btn-primary {
        background: #dbeafe;
        color: #1e40af;
    }

    .btn-primary:hover {
        background: #bfdbfe;
        transform: translateY(-1px);
    }

    .form-group {
        display: flex;
        gap: 12px;
        align-items: center;
        margin-top: 1rem;
        padding: 1rem;
        background: #f9fafb;
        border-radius: 8px;
        border: 1px solid #e5e7eb;
    }

    .form-select {
        padding: 8px 12px;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        background: white;
        color: #374151;
        min-width: 200px;
    }

    .form-select:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .empty-state {
        text-align: center;
        padding: 2rem;
        color: #6b7280;
        background: #f9fafb;
        border-radius: 8px;
        border: 2px dashed #d1d5db;
    }

    .breadcrumb {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 1.5rem;
        color: #6b7280;
        font-size: 0.875rem;
    }

    .breadcrumb a {
        color: #3b82f6;
        text-decoration: none;
    }

    .breadcrumb a:hover {
        text-decoration: underline;
    }

    .back-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 16px;
        background: #f3f4f6;
        color: #374151;
        text-decoration: none;
        border-radius: 6px;
        font-size: 0.875rem;
        font-weight: 500;
        transition: all 0.3s ease;
        margin-bottom: 1.5rem;
    }

    .back-btn:hover {
        background: #e5e7eb;
        transform: translateY(-1px);
    }

    /* Thêm CSS cho thẻ thống kê */
    .stat-cards {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 1rem;
        margin-bottom: 1.5rem;
    }
    
    .stat-card {
        background: white;
        border-radius: 12px;
        padding: 1.25rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        border: 1px solid #e5e7eb;
        transition: all 0.3s ease;
    }
    
    .stat-card:hover {
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        transform: translateY(-2px);
    }
    
    .stat-icon {
        width: 24px;
        height: 24px;
    }
    
    .text-blue-500 {
        color: #3b82f6;
    }
    
    .text-green-500 {
        color: #10b981;
    }
    
    .text-purple-500 {
        color: #8b5cf6;
    }
    
    .text-amber-500 {
        color: #f59e0b;
    }
    
    .text-muted-foreground {
        color: #6b7280;
    }

    @media (max-width: 768px) {
        .info-grid {
            grid-template-columns: 1fr;
        }

        .data-table {
            font-size: 0.875rem;
        }

        .data-table th,
        .data-table td {
            padding: 8px;
        }

        .form-group {
            flex-direction: column;
            align-items: stretch;
        }
        
        .stat-cards {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="fade-in">
    <!-- Breadcrumb -->
    <div class="breadcrumb">
        <a href="{{ route('admin.promotions.index') }}">Chương trình khuyến mãi</a>
        <span>/</span>
        <span>{{ $program->name }}</span>
    </div>

    <!-- Back Button -->
    <a href="{{ route('admin.promotions.index') }}" class="back-btn">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="m12 19-7-7 7-7" />
            <path d="M19 12H5" />
        </svg>
        Quay lại danh sách
    </a>

    @if (session('success'))
    <div style="background: #dcfce7; color: #166534; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; border: 1px solid #bbf7d0;">
        {{ session('success') }}
    </div>
    @endif

    <!-- Thống kê -->
    <div class="stat-cards">
        <div class="stat-card">
            <div class="flex items-center gap-2 mb-2">
                <svg class="stat-icon text-blue-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M20 7h-9m0 0l3-3m-3 3l3 3m-3 8h9m0 0l-3 3m3-3l-3-3" />
                    <rect x="3" y="5" width="4" height="14" rx="1" />
                </svg>
                <span class="text-sm font-medium text-muted-foreground">Mã giảm giá</span>
            </div>
            <div class="text-2xl font-bold">{{ $program->discountCodes->count() }}</div>
        </div>

        <div class="stat-card">
            <div class="flex items-center gap-2 mb-2">
                <svg class="stat-icon text-green-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 12c.562 0 .998-.428 1-.988.008-3.104-1.457-6.126-3.985-8.02-2.528-1.895-5.903-2.342-8.884-1.158C6.15 3.01 4.193 5.256 3.5 7.99m-.46 3.52c-.235 4.49 2.35 8.68 6.64 10.18 3.903 1.36 8.22.05 10.96-3.18" />
                    <path d="M1 11a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm21.54.54a1.5 1.5 0 1 0 0-2.12 1.5 1.5 0 0 0 0 2.12z" />
                    <circle cx="12" cy="12" r="10" />
                    <path d="M12 7v5l2.5 2.5" />
                </svg>
                <span class="text-sm font-medium text-muted-foreground">Thời gian còn lại</span>
            </div>
            <div class="text-2xl font-bold">
                @php
                    $now = now();
                    $endDate = $program->end_date;
                    $daysLeft = $now->gt($endDate) ? 0 : ceil($now->diffInDays($endDate));
                @endphp
                {{ $daysLeft }} ngày
            </div>
        </div>

        <div class="stat-card">
            <div class="flex items-center gap-2 mb-2">
                <svg class="stat-icon text-purple-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M2 10s3-3 5-3 4 3 6 3 4-3 6-3 5 3 5 3" />
                    <path d="M2 19s3-3 5-3 4 3 6 3 4-3 6-3 5 3 5 3" />
                </svg>
                <span class="text-sm font-medium text-muted-foreground">Lượt sử dụng</span>
            </div>
            <div class="text-2xl font-bold">
                @php
                    $totalUsage = $program->discountCodes->sum('current_usage_count');
                    $maxUsage = $program->discountCodes->sum('max_total_usage');
                @endphp
                {{ number_format($totalUsage) }}
                @if($maxUsage > 0)
                    <span class="text-sm text-muted-foreground">/ {{ number_format($maxUsage) }}</span>
                @endif
            </div>
        </div>

        <div class="stat-card">
            <div class="flex items-center gap-2 mb-2">
                <svg class="stat-icon text-amber-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z" />
                    <circle cx="12" cy="10" r="3" />
                </svg>
                <span class="text-sm font-medium text-muted-foreground">Chi nhánh áp dụng</span>
            </div>
            <div class="text-2xl font-bold">
                @if($program->applicable_scope === 'all_branches')
                    Tất cả
                @else
                    {{ $program->branches->count() }}
                @endif
            </div>
        </div>
    </div>

    <!-- Program Details Card -->
    <div class="detail-card">
        <div class="detail-header">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                <circle cx="9" cy="7" r="4" />
                <path d="M22 21v-2a4 4 0 0 0-3-3.87" />
                <path d="M16 3.13a4 4 0 0 1 0 7.75" />
            </svg>
            <div>
                <h1 style="margin: 0; font-size: 1.5rem; font-weight: 600;">{{ $program->name }}</h1>
                <p style="margin: 0; opacity: 0.9; font-size: 0.875rem;">Chi tiết chương trình khuyến mãi</p>
            </div>
        </div>

        <div class="detail-content">
            <div class="info-grid">
                <div class="info-item">
                    <span class="info-label">Tên chương trình</span>
                    <span class="info-value">{{ $program->name }}</span>
                </div>

                <div class="info-item">
                    <span class="info-label">Trạng thái</span>
                    @php
                    $now = now();
                    if (!$program->is_active) {
                        $status = 'status-inactive';
                        $statusText = 'Không hoạt động';
                        $icon = '<path d="m15 9-6 6" /><path d="m9 9 6 6" />';
                    } elseif ($program->start_date && $now->lt($program->start_date)) {
                        $status = 'status-featured';
                        $statusText = 'Sắp diễn ra';
                        $icon = '<path d="M12.75 13l4.25 4.25" /><path d="M12 2v10" /><circle cx="12" cy="14" r="8" />';
                    } elseif ($program->end_date && $now->gt($program->end_date)) {
                        $status = 'status-inactive';
                        $statusText = 'Đã hết hạn';
                        $icon = '<circle cx="12" cy="12" r="10" /><path d="m14 8-6 8" /><path d="m10 8 4 8" />';
                    } else {
                        $status = 'status-active';
                        $statusText = 'Đang hoạt động';
                        $icon = '<path d="m9 12 2 2 4-4" />';
                    }
                    @endphp
                    <span class="status-badge {{ $status }}">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10" />
                            {!! $icon !!}
                        </svg>
                        {{ $statusText }}
                    </span>
                </div>

                <div class="info-item">
                    <span class="info-label">Mô tả</span>
                    <span class="info-value">{{ $program->description ?? 'Chưa có mô tả' }}</span>
                </div>

                <div class="info-item">
                    <span class="info-label">Chương trình nổi bật</span>
                    <span class="status-badge {{ $program->is_featured ? 'status-featured' : 'status-inactive' }}">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polygon points="12,2 15.09,8.26 22,9.27 17,14.14 18.18,21.02 12,17.77 5.82,21.02 7,14.14 2,9.27 8.91,8.26" />
                        </svg>
                        {{ $program->is_featured ? 'Có' : 'Không' }}
                    </span>
                </div>

                <div class="info-item">
                    <span class="info-label">Phạm vi áp dụng</span>
                    <span class="info-value">{{ $program->applicable_scope === 'all_branches' ? 'Tất cả chi nhánh' : 'Chi nhánh cụ thể' }}</span>
                </div>

                <div class="info-item">
                    <span class="info-label">Thứ tự hiển thị</span>
                    <span class="info-value">{{ $program->display_order }}</span>
                </div>

                <div class="info-item">
                    <span class="info-label">Thời gian bắt đầu</span>
                    <span class="info-value">{{ $program->start_date->format('d/m/Y H:i') }}</span>
                </div>

                <div class="info-item">
                    <span class="info-label">Thời gian kết thúc</span>
                    <span class="info-value">{{ $program->end_date->format('d/m/Y H:i') }}</span>
                </div>

                <div class="info-item">
                    <span class="info-label">Tạo bởi</span>
                    <span class="info-value">{{ $program->createdBy->name ?? ($program->createdBy->email ?? 'N/A') }}</span>
                </div>

                <div class="info-item">
                    <span class="info-label">Ngày tạo</span>
                    <span class="info-value">{{ $program->created_at->format('d/m/Y H:i') }}</span>
                </div>

                <div class="info-item">
                    <span class="info-label">Cập nhật lần cuối</span>
                    <span class="info-value">{{ $program->updated_at->format('d/m/Y H:i') }}</span>
                </div>
                
                <div class="info-item">
                    <span class="info-label">Tổng số mã giảm giá</span>
                    <span class="info-value">{{ $program->discountCodes->count() }}</span>
                </div>
            </div>

            <!-- Images Section -->
            @if($program->banner_image || $program->thumbnail_image)
            <div style="margin-top: 2rem; padding-top: 2rem; border-top: 1px solid #e5e7eb;">
                <h3 class="section-title">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect width="18" height="18" x="3" y="3" rx="2" ry="2" />
                        <circle cx="9" cy="9" r="2" />
                        <path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21" />
                    </svg>
                    Program Images
                </h3>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem;">
                    @if($program->banner_image)
                    <div>
                        <span class="info-label">Banner Image</span>
                        <div style="margin-top: 8px;">
                            <img src="{{ asset('storage/' . $program->banner_image) }}" alt="Banner" class="image-preview">
                        </div>
                    </div>
                    @endif

                    @if($program->thumbnail_image)
                    <div>
                        <span class="info-label">Thumbnail Image</span>
                        <div style="margin-top: 8px;">
                            <img src="{{ asset('storage/' . $program->thumbnail_image) }}" alt="Thumbnail" class="image-preview">
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Linked Discount Codes -->
    <div class="detail-card" style="margin-top: 1.5rem;">
        <div class="detail-header">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M21 12c.552 0 1.005-.449.95-.998a10 10 0 0 0-8.953-8.951c-.55-.055-.998.398-.998.95v8a1 1 0 0 0 1 1z" />
                <path d="M21.21 15.89A10 10 0 1 1 8 2.83" />
            </svg>
            <div>
                <h2 style="margin: 0; font-size: 1.25rem; font-weight: 600;">Mã giảm giá liên kết</h2>
                <p style="margin: 0; opacity: 0.9; font-size: 0.875rem;">Quản lý mã giảm giá cho chương trình khuyến mãi này</p>
            </div>
        </div>

        <div class="detail-content">
            @if ($program->discountCodes->isEmpty())
            <div class="empty-state">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" style="margin: 0 auto 1rem; color: #9ca3af;">
                    <circle cx="12" cy="12" r="10" />
                    <path d="m9 12 2 2 4-4" />
                </svg>
                <p style="margin: 0; font-size: 1rem; font-weight: 500;">Chưa có mã giảm giá được liên kết</p>
                <p style="margin: 0.5rem 0 0; font-size: 0.875rem;">Liên kết mã giảm giá cho chương trình khuyến mãi này phía dưới.</p>
            </div>
            @else
            <div style="overflow-x: auto;">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Mã</th>
                            <th>Tên</th>
                            <th>Loại</th>
                            <th>Giá trị</th>
                            <th>Đơn tối thiểu</th>
                            <th>Giảm tối đa</th>
                            <th>Phạm vi</th>
                            <th>Sản phẩm</th>
                            <th>Hạng</th>
                            <th>Lượt dùng</th>
                            <th>Trạng thái</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($program->discountCodes as $discount)
                        <tr>
                            <td><strong>{{ $discount->code }}</strong></td>
                            <td>{{ $discount->name }}</td>
                            <td>
                                <span class="type-badge {{ $discount->discount_type === 'percentage' ? 'type-percentage' : ($discount->discount_type === 'fixed_amount' ? 'type-fixed' : 'type-special') }}">
                                    @if($discount->discount_type === 'percentage')
                                        Giảm %
                                    @elseif($discount->discount_type === 'fixed_amount')
                                        Giảm tiền
                                    @elseif($discount->discount_type === 'free_shipping')
                                        Miễn phí ship
                                    @endif
                                </span>
                            </td>
                            <td>
                                <strong>
                                    @if($discount->discount_type === 'percentage')
                                        {{ $discount->discount_value }}%
                                    @elseif($discount->discount_type === 'fixed_amount')
                                        {{ number_format($discount->discount_value, 0) }}đ
                                    @else
                                        Miễn phí
                                    @endif
                                </strong>
                            </td>
                            <td>{{ number_format($discount->min_order_amount, 0) }}đ</td>
                            <td>{{ $discount->max_discount_amount ? number_format($discount->max_discount_amount, 0) . 'đ' : 'Không giới hạn' }}</td>
                            <td>
                                @if($discount->applicable_scope === 'all_branches')
                                    <span style="display: inline-block; padding: 4px 8px; background: #dcfce7; color: #166534; border-radius: 10px; font-size: 0.75rem;">
                                    Tất cả chi nhánh
                                    </span>
                                @else
                                    @if($discount->branches->isEmpty())
                                        <span style="color: #ef4444; font-size: 0.75rem;">Chưa có chi nhánh nào</span>
                                    @else
                                        <div style="display: flex; flex-direction: column; gap: 4px;">
                                            @foreach($discount->branches->take(2) as $branch)
                                                <span style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 150px; font-size: 0.875rem;" 
                                                      title="{{ $branch->name }} ({{ $branch->address ?? 'Không có địa chỉ' }})">
                                                    {{ $branch->name }}
                                                </span>
                                            @endforeach
                                            @if($discount->branches->count() > 2)
                                                <span style="color: #3b82f6; font-size: 0.75rem; cursor: pointer;" 
                                                      title="{{ $discount->branches->skip(2)->take(5)->pluck('name')->implode(', ') }}{{ $discount->branches->count() > 7 ? ',...' : '' }}">
                                                    +{{ $discount->branches->count() - 2 }} chi nhánh khác
                                                </span>
                                            @endif
                                        </div>
                                    @endif
                                @endif
                            </td>
                            <td>
                                @if($discount->applicable_items === 'all_items')
                                    Tất cả sản phẩm
                                @elseif($discount->applicable_items === 'specific_products')
                                    Sản phẩm cụ thể
                                @elseif($discount->applicable_items === 'specific_categories')
                                    Danh mục cụ thể
                                @elseif($discount->applicable_items === 'combos_only')
                                    Chỉ combo
                                @endif
                            </td>
                            <td>
                                @if($discount->applicable_ranks)
                                    @php
                                    // Parse rank JSON if needed
                                    $ranks = is_string($discount->applicable_ranks) ? json_decode($discount->applicable_ranks, true) : $discount->applicable_ranks;
                                    $ranks = is_array($ranks) ? $ranks : [];
                                    
                                    // Load ranks from database
                                    $rankNames = [];
                                    if(!empty($ranks)) {
                                        foreach($ranks as $rankId) {
                                            $rank = \App\Models\UserRank::find($rankId);
                                            if($rank) {
                                                $rankNames[] = '<span style="color: ' . $rank->color . ';">' . $rank->name . '</span>';
                                            }
                                        }
                                    }
                                    @endphp
                                    @if(!empty($rankNames))
                                        {!! implode(', ', $rankNames) !!}
                                    @else
                                        Tất cả hạng
                                    @endif
                                @else
                                    Tất cả hạng
                                @endif
                                @if($discount->rank_exclusive)
                                    <span style="margin-left: 5px; font-size: 10px; background: #f3e8ff; color: #7c3aed; padding: 2px 6px; border-radius: 10px;">Độc quyền</span>
                                @endif
                            </td>
                            <td>
                                {{ $discount->current_usage_count }} / {{ $discount->max_total_usage ?? '∞' }}
                                <div style="width: 100%; height: 4px; background: #e5e7eb; border-radius: 2px; margin-top: 3px;">
                                    @if($discount->max_total_usage)
                                        @php
                                            $percentage = min(100, ($discount->current_usage_count / $discount->max_total_usage) * 100);
                                        @endphp
                                        <div style="height: 100%; background: #3b82f6; width: {{ $percentage }}%; border-radius: 2px;"></div>
                                    @endif
                                </div>
                                <div style="font-size: 10px; color: #6b7280; margin-top: 3px;">
                                    {{ $discount->max_usage_per_user }} lần/người
                                </div>
                            </td>
                            <td>
                                <span class="status-badge {{ $discount->is_active ? 'status-active' : 'status-inactive' }}">
                                    {{ $discount->is_active ? 'Đang hoạt động' : 'Không hoạt động' }}
                                </span>
                                <div style="font-size: 10px; margin-top: 3px;">
                                    {{ $discount->start_date->format('d/m/Y') }} → {{ $discount->end_date->format('d/m/Y') }}
                                </div>
                            </td>
                            <td>
                                <form action="{{ route('admin.promotions.unlink-discount', [$program, $discount]) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn gỡ liên kết mã giảm giá này?');" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="action-btn btn-danger">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M3 6h18" />
                                            <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6" />
                                            <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2" />
                                        </svg>
                                        Gỡ liên kết
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif

            <!-- Link New Discount Code Form -->
            @if($availableDiscountCodes->isNotEmpty())
            <div style="margin-top: 2rem; padding-top: 2rem; border-top: 1px solid #e5e7eb;">
                <h3 class="section-title">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M5 12h14" />
                        <path d="M12 5v14" />
                    </svg>
                    Liên kết mã giảm giá mới
                </h3>

                <form action="{{ route('admin.promotions.link-discount', $program) }}" method="POST" class="form-group">
                    @csrf
                    <select name="discount_code_id" class="form-select" required>
                        <option value="">Chọn mã giảm giá...</option>
                        @foreach ($availableDiscountCodes as $discount)
                        <option value="{{ $discount->id }}">{{ $discount->code }} - {{ $discount->name }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="action-btn btn-primary">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M9 12l2 2 4-4" />
                            <path d="M21 12c.552 0 1.005-.449.95-.998a10 10 0 0 0-8.953-8.951c-.55-.055-.998.398-.998.95v8a1 1 0 0 0 1 1z" />
                        </svg>
                        Liên kết mã giảm giá
                    </button>
                </form>
            </div>
            @endif
        </div>
    </div>

    <!-- Applicable Branches -->
    <div class="detail-card" style="margin-top: 1.5rem;">
        <div class="detail-header">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z" />
                <circle cx="12" cy="10" r="3" />
            </svg>
            <div>
                <h2 style="margin: 0; font-size: 1.25rem; font-weight: 600;">Chi nhánh áp dụng</h2>
                <p style="margin: 0; opacity: 0.9; font-size: 0.875rem;">Quản lý chi nhánh cho chương trình khuyến mãi này</p>
            </div>
        </div>

        <div class="detail-content">
            @if ($program->branches->isEmpty())
            <div class="empty-state">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" style="margin: 0 auto 1rem; color: #9ca3af;">
                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z" />
                    <circle cx="12" cy="10" r="3" />
                </svg>
                <p style="margin: 0; font-size: 1rem; font-weight: 500;">
                    {{ $program->applicable_scope === 'all_branches' ? 'Áp dụng cho tất cả chi nhánh' : 'Chưa có chi nhánh cụ thể được liên kết' }}
                </p>
                @if($program->applicable_scope === 'all_branches')
                <p style="margin: 0.5rem 0 0; font-size: 0.875rem;">Chương trình khuyến mãi này áp dụng tại tất cả các chi nhánh.</p>
                @else
                <p style="margin: 0.5rem 0 0; font-size: 0.875rem;">Liên kết chi nhánh cụ thể cho chương trình khuyến mãi này phía dưới.</p>
                @endif
            </div>
            @else
            <div style="overflow-x: auto;">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Tên chi nhánh</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($program->branches as $branch)
                        <tr>
                            <td><strong>{{ $branch->name }}</strong></td>
                            <td>
                                <form action="{{ route('admin.promotions.unlink-branch', [$program, $branch]) }}" method="POST" onsubmit="return confirm('Are you sure you want to unlink this branch?');" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="action-btn btn-danger">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M3 6h18" />
                                            <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6" />
                                            <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2" />
                                        </svg>
                                        Unlink
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif

            <!-- Link New Branch Form -->
            @if ($program->applicable_scope === 'specific_branches' && $availableBranches->isNotEmpty())
            <div style="margin-top: 2rem; padding-top: 2rem; border-top: 1px solid #e5e7eb;">
                <h3 class="section-title">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M5 12h14" />
                        <path d="M12 5v14" />
                    </svg>
                    Liên kết chi nhánh mới
                </h3>

                <form action="{{ route('admin.promotions.link-branch', $program) }}" method="POST" class="form-group">
                    @csrf
                    <select name="branch_id" class="form-select" required>
                        <option value="">Chọn chi nhánh...</option>
                        @foreach ($availableBranches as $branch)
                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="action-btn btn-primary">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M9 12l2 2 4-4" />
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z" />
                        </svg>
                        Liên kết chi nhánh
                    </button>
                </form>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection