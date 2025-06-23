@extends('layouts/admin/contentLayoutMaster')

@section('title', 'Chi tiết mã giảm giá')
@section('description', 'Xem chi tiết thông tin mã giảm giá')

@section('content')
<style>
    .detail-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        border: 1px solid #e5e7eb;
        transition: all 0.3s ease;
    }
    
    .dark .detail-card {
        background: hsl(var(--card));
        border-color: hsl(var(--border));
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.3);
    }
    
    .detail-card:hover {
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        transform: translateY(-2px);
    }
    
    .dark .detail-card:hover {
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.4);
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
    
    .dark .detail-header {
        background: linear-gradient(135deg, hsl(var(--primary)) 0%, hsl(var(--primary) / 0.8) 100%);
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
    
    .dark .info-label {
        color: hsl(var(--muted-foreground));
    }
    
    .info-value {
        color: #1f2937;
        font-size: 1rem;
        line-height: 1.5;
    }
    
    .dark .info-value {
        color: hsl(var(--foreground));
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
    
    .dark .status-active {
        background: #166534;
        color: #dcfce7;
    }
    
    .status-inactive {
        background: #fee2e2;
        color: #991b1b;
    }
    
    .dark .status-inactive {
        background: #991b1b;
        color: #fee2e2;
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
    
    .dark .type-percentage {
        background: #1e40af;
        color: #dbeafe;
    }
    
    .type-fixed {
        background: #d1fae5;
        color: #065f46;
    }
    
    .dark .type-fixed {
        background: #065f46;
        color: #d1fae5;
    }
    
    .type-shipping {
        background: #fef3c7;
        color: #d97706;
    }
    
    .dark .type-shipping {
        background: #d97706;
        color: #fef3c7;
    }
    
    .value-display {
        display: inline-block;
        padding: 4px 12px;
        background-color: #dcfce7;
        color: #15803d;
        border-radius: 9999px;
        font-size: 14px;
        font-weight: 500;
    }
    
    .dark .value-display {
        background-color: #15803d;
        color: #dcfce7;
    }

    .value-display.percentage {
        background-color: #dbeafe;
        color: #1e40af;
    }
    
    .dark .value-display.percentage {
        background-color: #1e40af;
        color: #dbeafe;
    }

    .value-display.amount {
        background-color: #fef3c7;
        color: #d97706;
    }
    
    .dark .value-display.amount {
        background-color: #d97706;
        color: #fef3c7;
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
    
    .dark .section-title {
        color: hsl(var(--foreground));
    }
    
    .empty-state {
        text-align: center;
        padding: 2rem;
        color: #6b7280;
        background: #f9fafb;
        border-radius: 8px;
        border: 2px dashed #d1d5db;
    }
    
    .dark .empty-state {
        color: hsl(var(--muted-foreground));
        background: hsl(var(--card) / 0.6);
        border-color: hsl(var(--border));
    }
    
    .dark .empty-state svg {
        color: hsl(var(--muted-foreground));
    }

    .code-display {
        font-family: 'Courier New', monospace;
        background: #f3f4f6;
        padding: 0.5rem 1rem;
        border-radius: 0.5rem;
        border: 1px solid #d1d5db;
        font-weight: 600;
        color: #374151;
    }
    
    .dark .code-display {
        background: hsl(var(--muted) / 0.3);
        border-color: hsl(var(--border));
        color: hsl(var(--foreground));
    }
    
    .dark .text-gray-500,
    .dark .text-gray-900 {
        color: hsl(var(--foreground));
    }
    
    .dark .bg-white {
        background: hsl(var(--card));
    }
    
    .dark .border {
        border-color: hsl(var(--border));
    }
    
    .dark .shadow-sm {
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.3);
    }
    
    .dark .bg-gray-200 {
        background: hsl(var(--muted));
    }
    
    .dark .bg-blue-50 {
        background: hsl(var(--primary) / 0.2);
    }
    
    .dark .text-blue-700 {
        color: hsl(var(--primary));
    }
    
    .dark .bg-green-50 {
        background: rgba(16, 185, 129, 0.2);
    }
    
    .dark .text-green-700 {
        color: rgb(16, 185, 129);
    }
    
    .dark .bg-red-50 {
        background: hsl(var(--destructive) / 0.2);
    }
    
    .dark .text-red-700 {
        color: hsl(var(--destructive));
    }
    
    .dark .text-blue-800,
    .dark .text-blue-600 {
        color: hsl(var(--primary));
    }
    
    .dark .bg-blue-100 {
        background: hsl(var(--primary) / 0.2);
    }
</style>

<div class="fade-in flex flex-col gap-4 pb-4 p-4">
    <!-- Main Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="flex aspect-square w-10 h-10 items-center justify-center rounded-lg bg-primary text-primary-foreground">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-ticket">
                    <path d="M2 9a3 3 0 0 1 0 6v2a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-2a3 3 0 0 1 0-6V7a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v2Z"></path>
                    <path d="M13 5v2"></path>
                    <path d="M13 17v2"></path>
                    <path d="M13 11v2"></path>
                </svg>
            </div>
            <div>
                <h2 class="text-3xl font-bold tracking-tight">{{ $discountCode->code }}</h2>
                <p class="text-muted-foreground">Chi tiết mã giảm giá</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.discount_codes.usage-history', $discountCode->id) }}" class="btn btn-outline flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2">
                    <path d="M3 3v5h5"></path>
                    <path d="M3 8a9 9 0 0 1 9-9 9.75 9.75 0 0 1 6.74 2.74L21 4"></path>
                    <path d="M21 21v-5h-5"></path>
                    <path d="M21 16a9 9 0 0 1-9 9 9.75 9.75 0 0 1-6.74-2.74L3 20"></path>
                </svg>
                Lịch sử sử dụng
            </a>
            <a href="{{ route('admin.discount_codes.edit', $discountCode->id) }}" class="btn btn-outline flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2">
                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                </svg>
                Chỉnh sửa
            </a>
            <a href="{{ route('admin.discount_codes.index') }}" class="btn btn-outline flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2">
                    <path d="m12 19-7-7 7-7"></path>
                    <path d="M19 12H5"></path>
                </svg>
                Quay lại
            </a>
        </div>
    </div>

    <!-- Discount Code Details Card -->
    <div class="detail-card">
        <div class="detail-header">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M2 9a3 3 0 0 1 0 6v2a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-2a3 3 0 0 1 0-6V7a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v2Z"></path>
                <path d="M13 5v2"></path>
                <path d="M13 17v2"></path>
                <path d="M13 11v2"></path>
            </svg>
            <div>
                <h1 style="margin: 0; font-size: 1.5rem; font-weight: 600;">{{ $discountCode->name }}</h1>
                <p style="margin: 0; opacity: 0.9; font-size: 0.875rem;">Thông tin chi tiết mã giảm giá</p>
            </div>
        </div>
        
        <div class="detail-content">
            <div class="info-grid">
                <div class="info-item">
                    <span class="info-label">Mã giảm giá</span>
                    <span class="code-display">{{ $discountCode->code }}</span>
                </div>
                
                <div class="info-item">
                    <span class="info-label">Tên mã giảm giá</span>
                    <span class="info-value">{{ $discountCode->name }}</span>
                </div>
                
                <div class="info-item">
                    <span class="info-label">Trạng thái</span>
                    @php
                        $now = now();
                        if (!$discountCode->is_active) {
                            $status = 'inactive';
                            $statusText = 'Không hoạt động';
                        } elseif ($now->gt($discountCode->end_date)) {
                            $status = 'inactive';
                            $statusText = 'Đã hết hạn';
                        } else {
                            $status = 'active';
                            $statusText = 'Hoạt động';
                        }
                    @endphp
                    <span class="status-badge status-{{ $status }}">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"/>
                            @if($status == 'active')
                                <path d="m9 12 2 2 4-4"/>
                            @else
                                <path d="m15 9-6 6"/>
                                <path d="m9 9 6 6"/>
                            @endif
                        </svg>
                        {{ $statusText }}
                    </span>
                </div>
                
                <div class="info-item">
                    <span class="info-label">Loại giảm giá</span>
                    @php
                        $typeClass = 'percentage';
                        $typeText = 'Phần trăm';
                        switch($discountCode->discount_type) {
                            case 'fixed_amount':
                                $typeClass = 'fixed';
                                $typeText = 'Số tiền cố định';
                                break;
                            case 'free_shipping':
                                $typeClass = 'shipping';
                                $typeText = 'Miễn phí vận chuyển';
                                break;
                        }
                    @endphp
                    <span class="type-badge type-{{ $typeClass }}">{{ $typeText }}</span>
                </div>
                
                <div class="info-item">
                    <span class="info-label">Giá trị</span>
                    @if($discountCode->discount_type == 'percentage')
                        <span class="value-display percentage">{{ $discountCode->discount_value }}%</span>
                    @elseif($discountCode->discount_type == 'fixed_amount')
                        <span class="value-display amount">{{ number_format($discountCode->discount_value) }} đ</span>
                    @else
                        <span class="value-display">Miễn phí vận chuyển</span>
                    @endif
                </div>
                
                <div class="info-item">
                    @php
                        $minType = $discountCode->min_requirement_type;
                        $minValue = $discountCode->min_requirement_value;
                    @endphp
                    <span class="info-label">
                        @if($minType === 'order_amount')
                            Số tiền đơn hàng tối thiểu
                        @elseif($minType === 'product_price')
                            Giá tiền sản phẩm tối thiểu
                        @else
                            Điều kiện tối thiểu
                        @endif
                    </span>
                    <span class="info-value">
                        @if($minType && $minValue > 0)
                            {{ number_format($minValue) }} đ
                        @else
                            Không có điều kiện tối thiểu
                        @endif
                    </span>
                </div>
                
                <div class="info-item">
                    <span class="info-label">Số tiền giảm tối đa</span>
                    <span class="info-value">
                        @if($discountCode->max_discount_amount)
                            {{ number_format($discountCode->max_discount_amount) }} đ
                        @else
                            Không giới hạn
                        @endif
                    </span>
                </div>
                
                <div class="info-item">
                    <span class="info-label">Thời gian hiệu lực</span>
                    <span class="info-value">{{ $discountCode->start_date->format('d/m/Y') }} - {{ $discountCode->end_date->format('d/m/Y') }}</span>
                </div>
                
                <div class="info-item">
                    <span class="info-label">Loại sử dụng</span>
                    <span class="info-value">{{ $discountCode->usage_type == 'public' ? 'Công khai' : 'Riêng tư' }}</span>
                </div>
                
                <div class="info-item">
                    <span class="info-label">Số lần sử dụng tối đa</span>
                    <span class="info-value">
                        @if($discountCode->max_total_usage)
                            {{ number_format($discountCode->max_total_usage) }} lần
                        @else
                            Không giới hạn
                        @endif
                    </span>
                </div>
                
                <div class="info-item">
                    <span class="info-label">Số lần đã sử dụng</span>
                    <span class="info-value">{{ number_format($discountCode->current_usage_count ?? 0) }} lần</span>
                </div>
                
                <div class="info-item">
                    <span class="info-label">Số lần sử dụng tối đa mỗi người</span>
                    <span class="info-value">{{ number_format($discountCode->max_usage_per_user) }} lần</span>
                </div>
            </div>
            
            <!-- Description Section -->
            @if($discountCode->description)
                <div style="margin-top: 2rem; padding-top: 2rem; border-top: 1px solid #e5e7eb;">
                    <h3 class="section-title">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            <polyline points="14,2 14,8 20,8"></polyline>
                            <line x1="16" y1="13" x2="8" y2="13"></line>
                            <line x1="16" y1="17" x2="8" y2="17"></line>
                            <polyline points="10,9 9,9 8,9"></polyline>
                        </svg>
                        Mô tả
                    </h3>
                    
                    <div class="info-value">{{ $discountCode->description }}</div>
                </div>
            @endif

            <!-- Member Ranks Section -->
            @if($discountCode->applicable_ranks)
                <div style="margin-top: 2rem; padding-top: 2rem; border-top: 1px solid #e5e7eb;">
                    <h3 class="section-title">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 2L2 7l10 5 10-5-10-5z"/>
                            <path d="M2 17l10 5 10-5"/>
                            <path d="M2 12l10 5 10-5"/>
                        </svg>
                        Hạng thành viên áp dụng
                    </h3>
                    
                    <div class="info-value">
                        @php
                            $ranks = [
                                1 => 'Đồng',
                                2 => 'Bạc',
                                3 => 'Vàng',
                                4 => 'Bạch Kim',
                                5 => 'Kim Cương'
                            ];
                            
                            $selectedRanks = is_string($discountCode->applicable_ranks) 
                                ? json_decode($discountCode->applicable_ranks, true) 
                                : (array) $discountCode->applicable_ranks;
                            
                            $rankNames = [];
                            foreach ($selectedRanks as $rankId) {
                                if (isset($ranks[$rankId])) {
                                    $rankNames[] = $ranks[$rankId];
                                }
                            }
                        @endphp
                        
                        @if(count($rankNames) > 0)
                            <div class="flex flex-wrap gap-2 mt-2">
                                @foreach($rankNames as $rank)
                                    <span class="px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-950 dark:text-blue-200">
                                        {{ $rank }}
                                    </span>
                                @endforeach
                            </div>
                            
                            @if($discountCode->rank_exclusive)
                                <p class="mt-2 text-sm text-gray-500 dark:text-muted-foreground">Chỉ áp dụng cho các hạng đã chọn</p>
                            @else
                                <p class="mt-2 text-sm text-gray-500 dark:text-muted-foreground">Ưu tiên cho các hạng đã chọn</p>
                            @endif
                        @else
                            <p>Áp dụng cho tất cả hạng thành viên</p>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Applicable Branches -->
    <div class="detail-card">
        <div class="detail-header">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                <circle cx="12" cy="10" r="3"/>
            </svg>
            <div>
                <h2 style="margin: 0; font-size: 1.25rem; font-weight: 600;">Chi nhánh áp dụng</h2>
                <p style="margin: 0; opacity: 0.9; font-size: 0.875rem;">Danh sách chi nhánh có thể sử dụng mã giảm giá</p>
            </div>
        </div>
        
        <div class="detail-content">
            @if ($discountCode->branches && $discountCode->branches->isNotEmpty())
                <div class="info-value">
                    {{ $discountCode->branches->pluck('name')->implode(', ') }}
                </div>
            @else
                <div class="empty-state">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" style="margin: 0 auto 1rem; color: #9ca3af;">
                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                        <circle cx="12" cy="10" r="3"/>
                    </svg>
                    <p style="margin: 0; font-size: 1rem; font-weight: 500;">Áp dụng cho tất cả chi nhánh</p>
                    <p style="margin: 0.5rem 0 0; font-size: 0.875rem;">Mã giảm giá này có thể được sử dụng tại tất cả các chi nhánh.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Products/Categories/Combos -->
    <div class="detail-card">
        <div class="detail-header">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/>
                <line x1="3" y1="6" x2="21" y2="6"/>
                <path d="M16 10a4 4 0 0 1-8 0"/>
            </svg>
            <div>
                <h2 style="margin: 0; font-size: 1.25rem; font-weight: 600;">Sản phẩm/Danh mục/Combo áp dụng</h2>
                <p style="margin: 0; opacity: 0.9; font-size: 0.875rem;">Phạm vi áp dụng của mã giảm giá</p>
            </div>
        </div>
        
        <div class="detail-content">
            @php
                $applicableItems = $discountCode->applicable_items ?? 'all_items';
            @endphp
            
            @if ($applicableItems === 'all_items')
                <div class="empty-state">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" style="margin: 0 auto 1rem; color: #9ca3af;">
                        <path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/>
                        <line x1="3" y1="6" x2="21" y2="6"/>
                        <path d="M16 10a4 4 0 0 1-8 0"/>
                    </svg>
                    <p style="margin: 0; font-size: 1rem; font-weight: 500;">Áp dụng cho tất cả sản phẩm</p>
                    <p style="margin: 0.5rem 0 0; font-size: 0.875rem;">Mã giảm giá này có thể được sử dụng cho tất cả sản phẩm, danh mục và combo.</p>
                </div>
            @elseif ($applicableItems === 'all_categories')
                <div class="empty-state bg-indigo-50 border-indigo-200 dark:bg-indigo-950/20 dark:border-indigo-900">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" style="margin: 0 auto 1rem; color: #6366f1;">
                        <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/>
                    </svg>
                    <p style="margin: 0; font-size: 1rem; font-weight: 500; color: #4f46e5;" class="dark:text-indigo-300">Áp dụng cho tất cả danh mục</p>
                    <p style="margin: 0.5rem 0 0; font-size: 0.875rem; color: #6366f1;" class="dark:text-indigo-400">Mã giảm giá này có thể được sử dụng cho tất cả sản phẩm trong tất cả danh mục.</p>
                </div>
            @elseif ($applicableItems === 'all_combos')
                <div class="empty-state bg-amber-50 border-amber-200 dark:bg-amber-950/20 dark:border-amber-900">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" style="margin: 0 auto 1rem; color: #d97706;" class="dark:text-amber-400">
                        <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
                        <polyline points="3.29 7 12 12 20.71 7"/>
                        <line x1="12" y1="22" x2="12" y2="12"/>
                    </svg>
                    <p style="margin: 0; font-size: 1rem; font-weight: 500; color: #b45309;" class="dark:text-amber-300">Áp dụng cho tất cả combo</p>
                    <p style="margin: 0.5rem 0 0; font-size: 0.875rem; color: #d97706;" class="dark:text-amber-400">Mã giảm giá này chỉ có thể được sử dụng cho các combo sản phẩm.</p>
                </div>
            @elseif ($discountCode->products && $discountCode->products->isNotEmpty())
                <div class="info-value">
                    @php
                        $products = $discountCode->products->whereNotNull('product_id')->pluck('product.name')->filter();
                        $categories = $discountCode->products->whereNotNull('category_id')->pluck('category.name')->filter();
                        $combos = $discountCode->products->whereNotNull('combo_id')->pluck('combo.name')->filter();
                        $variants = $discountCode->products->whereNotNull('product_variant_id');
                    @endphp
                    
                    @if($products->isNotEmpty())
                        <div class="mb-4">
                            <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2">
                                    <path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/>
                                    <path d="M3 6h18"/>
                                    <path d="M16 10a4 4 0 0 1-8 0"/>
                                </svg>
                                Sản phẩm cụ thể:
                            </h4>
                            <div class="flex flex-wrap gap-2">
                                @foreach($products as $product)
                                    <span class="px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-950 dark:text-green-200">
                                        {{ $product }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                    
                    @if($variants->isNotEmpty())
                        <div class="mb-4">
                            <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2">
                                    <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                                    <path d="M3.29 7 12 12l8.71-5"></path>
                                    <path d="M12 22V12"></path>
                                </svg>
                                Biến thể sản phẩm cụ thể:
                            </h4>
                            <div class="flex flex-wrap gap-2">
                                @foreach($variants as $variantItem)
                                    @php
                                        $variant = $variantItem->productVariant;
                                        if(!$variant || !$variant->product) continue;
                                        
                                        $variantValues = $variant->variantValues ?? collect([]);
                                        $variantDescription = $variantValues->pluck('value')->implode(', ');
                                    @endphp
                                    <span class="px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-950 dark:text-blue-200">
                                        {{ $variant->product->name }} ({{ $variantDescription }})
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                    
                    @if($categories->isNotEmpty())
                        <div class="mb-4">
                            <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2">
                                    <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/>
                                </svg>
                                Danh mục cụ thể:
                            </h4>
                            <div class="flex flex-wrap gap-2">
                                @foreach($categories as $category)
                                    <span class="px-3 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-950 dark:text-indigo-200">
                                        {{ $category }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                    
                    @if($combos->isNotEmpty())
                        <div>
                            <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2">
                                    <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
                                    <polyline points="3.29 7 12 12 20.71 7"/>
                                    <line x1="12" y1="22" x2="12" y2="12"/>
                                </svg>
                                Combo cụ thể:
                            </h4>
                            <div class="flex flex-wrap gap-2">
                                @foreach($combos as $combo)
                                    <span class="px-3 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-200">
                                        {{ $combo }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            @elseif ($applicableItems === 'specific_products')
                <div class="empty-state bg-green-50 border-green-200 dark:bg-green-950/20 dark:border-green-900">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" style="margin: 0 auto 1rem; color: #10b981;" class="dark:text-green-400">
                        <path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/>
                        <line x1="3" y1="6" x2="21" y2="6"/>
                        <path d="M16 10a4 4 0 0 1-8 0"/>
                    </svg>
                    <p style="margin: 0; font-size: 1rem; font-weight: 500; color: #047857;" class="dark:text-green-300">Áp dụng cho sản phẩm cụ thể</p>
                    <p style="margin: 0.5rem 0 0; font-size: 0.875rem; color: #10b981;" class="dark:text-green-400">Mã giảm giá này chỉ có thể được sử dụng cho các sản phẩm cụ thể đã chọn.</p>
                </div>
            @elseif ($applicableItems === 'specific_categories')
                <div class="empty-state bg-indigo-50 border-indigo-200 dark:bg-indigo-950/20 dark:border-indigo-900">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" style="margin: 0 auto 1rem; color: #6366f1;" class="dark:text-indigo-400">
                        <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/>
                    </svg>
                    <p style="margin: 0; font-size: 1rem; font-weight: 500; color: #4f46e5;" class="dark:text-indigo-300">Áp dụng cho danh mục cụ thể</p>
                    <p style="margin: 0.5rem 0 0; font-size: 0.875rem; color: #6366f1;" class="dark:text-indigo-400">Mã giảm giá này chỉ có thể được sử dụng cho các danh mục cụ thể đã chọn.</p>
                </div>
            @elseif ($applicableItems === 'specific_combos')
                <div class="info-value">
                    @php
                        $combos = $discountCode->products->whereNotNull('combo_id')->pluck('combo.name')->filter();
                    @endphp
                    
                    @if($combos->isNotEmpty())
                        <div>
                            <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2">
                                    <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
                                    <polyline points="3.29 7 12 12 20.71 7"/>
                                    <line x1="12" y1="22" x2="12" y2="12"/>
                                </svg>
                                Combo cụ thể:
                            </h4>
                            <div class="flex flex-wrap gap-2">
                                @foreach($combos as $combo)
                                    <span class="px-3 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-200">
                                        {{ $combo }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <div class="empty-state bg-amber-50 border-amber-200 dark:bg-amber-950/20 dark:border-amber-900">
                            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" style="margin: 0 auto 1rem; color: #d97706;" class="dark:text-amber-400">
                                <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
                                <polyline points="3.29 7 12 12 20.71 7"/>
                                <line x1="12" y1="22" x2="12" y2="12"/>
                            </svg>
                            <p style="margin: 0; font-size: 1rem; font-weight: 500; color: #b45309;" class="dark:text-amber-300">Áp dụng cho combo cụ thể</p>
                            <p style="margin: 0.5rem 0 0; font-size: 0.875rem; color: #d97706;" class="dark:text-amber-400">Chưa có combo nào được chọn.</p>
                        </div>
                    @endif
                </div>
            @elseif ($applicableItems === 'specific_variants')
                <div class="info-value">
                    @php
                        $variants = $discountCode->products->whereNotNull('product_variant_id');
                    @endphp
                    
                    @if($variants->isNotEmpty())
                        <div class="mb-4">
                            <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2">
                                    <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                                    <path d="M3.29 7 12 12l8.71-5"></path>
                                    <path d="M12 22V12"></path>
                                </svg>
                                Biến thể sản phẩm cụ thể:
                            </h4>
                            <div class="flex flex-wrap gap-2">
                                @foreach($variants as $variantItem)
                                    @php
                                        $variant = $variantItem->productVariant;
                                        if(!$variant || !$variant->product) continue;
                                        
                                        $variantValues = $variant->variantValues ?? collect([]);
                                        $variantDescription = $variantValues->pluck('value')->implode(', ');
                                    @endphp
                                    <span class="px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-950 dark:text-blue-200">
                                        {{ $variant->product->name }} ({{ $variantDescription }})
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <div class="empty-state bg-blue-50 border-blue-200 dark:bg-blue-950/20 dark:border-blue-900">
                            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" style="margin: 0 auto 1rem; color: #3b82f6;" class="dark:text-blue-400">
                                <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                                <path d="M3.29 7 12 12l8.71-5"></path>
                                <path d="M12 22V12"></path>
                            </svg>
                            <p style="margin: 0; font-size: 1rem; font-weight: 500; color: #2563eb;" class="dark:text-blue-300">Áp dụng cho các biến thể sản phẩm</p>
                            <p style="margin: 0.5rem 0 0; font-size: 0.875rem; color: #3b82f6;" class="dark:text-blue-400">Chưa có biến thể sản phẩm nào được chọn.</p>
                        </div>
                    @endif
                </div>
            @else
                <div class="empty-state">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" style="margin: 0 auto 1rem; color: #9ca3af;">
                        <path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/>
                        <line x1="3" y1="6" x2="21" y2="6"/>
                        <path d="M16 10a4 4 0 0 1-8 0"/>
                    </svg>
                    <p style="margin: 0; font-size: 1rem; font-weight: 500;">Áp dụng cho tất cả sản phẩm</p>
                    <p style="margin: 0.5rem 0 0; font-size: 0.875rem;">Mã giảm giá này có thể được sử dụng cho tất cả sản phẩm, danh mục và combo.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Assigned Users -->
    @if($discountCode->usage_type == 'personal')
    <div class="detail-card">
        <div class="detail-header">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                <circle cx="9" cy="7" r="4"/>
                <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
            </svg>
            <div>
                <h2 style="margin: 0; font-size: 1.25rem; font-weight: 600;">Người dùng được gán</h2>
                <p style="margin: 0; opacity: 0.9; font-size: 0.875rem;">Danh sách người dùng có thể sử dụng mã giảm giá riêng tư này</p>
            </div>
        </div>
        
        <div class="detail-content">
            @if ($discountCode->users && $discountCode->users->isNotEmpty())
                <div class="info-value">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($discountCode->users as $userAssignment)
                            @php
                                $user = $userAssignment->user;
                                if (!$user) continue;
                                
                                $userRankId = $user->user_rank_id ?? 0;
                                $rankName = 'Chưa xếp hạng';
                                $rankClass = 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-200';
                                
                                if ($userRankId == 1) {
                                    $rankName = 'Đồng';
                                    $rankClass = 'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-200';
                                } elseif ($userRankId == 2) {
                                    $rankName = 'Bạc';
                                    $rankClass = 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-200';
                                } elseif ($userRankId == 3) {
                                    $rankName = 'Vàng';
                                    $rankClass = 'bg-yellow-100 text-yellow-800 dark:bg-yellow-950 dark:text-yellow-200';
                                } elseif ($userRankId == 4) {
                                    $rankName = 'Bạch Kim';
                                    $rankClass = 'bg-indigo-100 text-indigo-800 dark:bg-indigo-950 dark:text-indigo-200';
                                } elseif ($userRankId == 5) {
                                    $rankName = 'Kim Cương';
                                    $rankClass = 'bg-blue-100 text-blue-800 dark:bg-blue-950 dark:text-blue-200';
                                }
                            @endphp
                            <div class="p-4 border rounded-lg bg-white shadow-sm flex items-start space-x-3">
                                <div class="flex-shrink-0">
                                    @if(!empty($user->avatar_url))
                                        <img src="{{ $user->avatar_url }}" alt="{{ $user->full_name }}" class="w-10 h-10 rounded-full">
                                    @else
                                        <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center text-gray-500">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                                <circle cx="12" cy="7" r="4"></circle>
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-center justify-between">
                                        <h4 class="text-sm font-medium text-gray-900">{{ $user->full_name }}</h4>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $rankClass }}">
                                            {{ $rankName }}
                                        </span>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">{{ $user->email }}</p>
                                    <p class="text-xs text-gray-500">{{ $user->phone ?? 'Không có SĐT' }}</p>
                                    <div class="mt-2 text-xs">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs bg-blue-50 text-blue-700">
                                            Sử dụng: {{ $userAssignment->usage_count ?? 0 }}/{{ $discountCode->max_usage_per_user ?? 'Không giới hạn' }}
                                        </span>
                                        @if($userAssignment->status == 'available')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs bg-green-50 text-green-700 ml-2">
                                                Khả dụng
                                            </span>
                                        @elseif($userAssignment->status == 'used')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs bg-red-50 text-red-700 ml-2">
                                                Đã sử dụng
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="empty-state">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" style="margin: 0 auto 1rem; color: #9ca3af;">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                    <p style="margin: 0; font-size: 1rem; font-weight: 500;">Chưa có người dùng nào được gán</p>
                    <p style="margin: 0.5rem 0 0; font-size: 0.875rem;">Hãy gán người dùng để họ có thể sử dụng mã giảm giá riêng tư này.</p>
                </div>
            @endif
        </div>
    </div>
    @endif
</div>
@endsection
