@extends('layouts/admin/contentLayoutMaster')

@section('title', 'Promotion Program Details')

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
    }
</style>

<div class="fade-in">
    <!-- Breadcrumb -->
    <div class="breadcrumb">
        <a href="{{ route('admin.promotions.index') }}">Promotion Programs</a>
        <span>/</span>
        <span>{{ $program->name }}</span>
    </div>

    <!-- Back Button -->
    <a href="{{ route('admin.promotions.index') }}" class="back-btn">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="m12 19-7-7 7-7" />
            <path d="M19 12H5" />
        </svg>
        Back to Promotion Programs
    </a>

    @if (session('success'))
    <div style="background: #dcfce7; color: #166534; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; border: 1px solid #bbf7d0;">
        {{ session('success') }}
    </div>
    @endif

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
                <p style="margin: 0; opacity: 0.9; font-size: 0.875rem;">Promotion Program Details</p>
            </div>
        </div>

        <div class="detail-content">
            <div class="info-grid">
                <div class="info-item">
                    <span class="info-label">Program Name</span>
                    <span class="info-value">{{ $program->name }}</span>
                </div>

                <div class="info-item">
                    <span class="info-label">Status</span>
                    <span class="status-badge {{ $program->is_active ? 'status-active' : 'status-inactive' }}">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10" />
                            @if($program->is_active)
                            <path d="m9 12 2 2 4-4" />
                            @else
                            <path d="m15 9-6 6" />
                            <path d="m9 9 6 6" />
                            @endif
                        </svg>
                        {{ $program->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>

                <div class="info-item">
                    <span class="info-label">Description</span>
                    <span class="info-value">{{ $program->description ?? 'N/A' }}</span>
                </div>

                <div class="info-item">
                    <span class="info-label">Featured Program</span>
                    <span class="status-badge {{ $program->is_featured ? 'status-featured' : 'status-inactive' }}">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polygon points="12,2 15.09,8.26 22,9.27 17,14.14 18.18,21.02 12,17.77 5.82,21.02 7,14.14 2,9.27 8.91,8.26" />
                        </svg>
                        {{ $program->is_featured ? 'Yes' : 'No' }}
                    </span>
                </div>

                <div class="info-item">
                    <span class="info-label">Applicable Scope</span>
                    <span class="info-value">{{ $program->applicable_scope === 'all_branches' ? 'All Branches' : 'Specific Branches' }}</span>
                </div>

                <div class="info-item">
                    <span class="info-label">Display Order</span>
                    <span class="info-value">{{ $program->display_order }}</span>
                </div>

                <div class="info-item">
                    <span class="info-label">Start Date</span>
                    <span class="info-value">{{ $program->start_date->format('d/m/Y H:i') }}</span>
                </div>

                <div class="info-item">
                    <span class="info-label">End Date</span>
                    <span class="info-value">{{ $program->end_date->format('d/m/Y H:i') }}</span>
                </div>

                <div class="info-item">
                    <span class="info-label">Created By</span>
                    <span class="info-value">{{ $program->createdBy->name ?? $program->createdBy->email ?? 'N/A' }}</span>
                </div>

                <div class="info-item">
                    <span class="info-label">Created At</span>
                    <span class="info-value">{{ $program->created_at->format('d/m/Y H:i') }}</span>
                </div>

                <div class="info-item">
                    <span class="info-label">Last Updated</span>
                    <span class="info-value">{{ $program->updated_at->format('d/m/Y H:i') }}</span>
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
                <h2 style="margin: 0; font-size: 1.25rem; font-weight: 600;">Linked Discount Codes</h2>
                <p style="margin: 0; opacity: 0.9; font-size: 0.875rem;">Manage discount codes for this promotion</p>
            </div>
        </div>

        <div class="detail-content">
            @if ($program->discountCodes->isEmpty())
            <div class="empty-state">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" style="margin: 0 auto 1rem; color: #9ca3af;">
                    <circle cx="12" cy="12" r="10" />
                    <path d="m9 12 2 2 4-4" />
                </svg>
                <p style="margin: 0; font-size: 1rem; font-weight: 500;">No discount codes linked</p>
                <p style="margin: 0.5rem 0 0; font-size: 0.875rem;">Link discount codes to this promotion program below.</p>
            </div>
            @else
            <div style="overflow-x: auto;">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Name</th>
                            <th>Type</th>
                            <th>Value</th>
                            <th>Min Order</th>
                            <th>Max Discount</th>
                            <th>Scope</th>
                            <th>Items</th>
                            <th>Ranks</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($program->discountCodes as $discount)
                        <tr>
                            <td><strong>{{ $discount->code }}</strong></td>
                            <td>{{ $discount->name }}</td>
                            <td>
                                <span class="type-badge {{ $discount->discount_type === 'percentage' ? 'type-percentage' : 'type-fixed' }}">
                                    {{ ucfirst(str_replace('_', ' ', $discount->discount_type)) }}
                                </span>
                            </td>
                            <td>
                                <strong>
                                    {{ $discount->discount_type === 'percentage' ? $discount->discount_value . '%' : '$' . number_format($discount->discount_value, 2) }}
                                </strong>
                            </td>
                            <td>${{ number_format($discount->min_order_amount, 2) }}</td>
                            <td>{{ $discount->max_discount_amount ? '$' . number_format($discount->max_discount_amount, 2) : 'N/A' }}</td>
                            <td>{{ ucfirst(str_replace('_', ' ', $discount->applicable_scope)) }}</td>
                            <td>{{ ucfirst(str_replace('_', ' ', $discount->applicable_items)) }}</td>
                            <td>
                                @if($discount->applicable_ranks)
                                @php
                                // Giải mã chuỗi JSON thành mảng nếu cần
                                $ranks = is_string($discount->applicable_ranks) ? json_decode($discount->applicable_ranks, true) : $discount->applicable_ranks;
                                $ranks = is_array($ranks) ? $ranks : [];
                                @endphp
                                {{ implode(', ', array_map(fn($id) => \App\Models\UserRank::find($id)->name ?? $id, $ranks)) }}
                                @else
                                All Ranks
                                @endif
                            </td>
                            <td>
                                <span class="status-badge {{ $discount->is_active ? 'status-active' : 'status-inactive' }}">
                                    {{ $discount->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td>
                                <form action="{{ route('admin.promotions.unlink-discount', [$program, $discount]) }}" method="POST" onsubmit="return confirm('Are you sure you want to unlink this discount code?');" style="display: inline;">
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

            <!-- Link New Discount Code Form -->
            @if($availableDiscountCodes->isNotEmpty())
            <div style="margin-top: 2rem; padding-top: 2rem; border-top: 1px solid #e5e7eb;">
                <h3 class="section-title">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M5 12h14" />
                        <path d="M12 5v14" />
                    </svg>
                    Link New Discount Code
                </h3>

                <form action="{{ route('admin.promotions.link-discount', $program) }}" method="POST" class="form-group">
                    @csrf
                    <select name="discount_code_id" class="form-select" required>
                        <option value="">Select a discount code...</option>
                        @foreach ($availableDiscountCodes as $discount)
                        <option value="{{ $discount->id }}">{{ $discount->code }} - {{ $discount->name }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="action-btn btn-primary">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M9 12l2 2 4-4" />
                            <path d="M21 12c.552 0 1.005-.449.95-.998a10 10 0 0 0-8.953-8.951c-.55-.055-.998.398-.998.95v8a1 1 0 0 0 1 1z" />
                        </svg>
                        Link Discount Code
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
                <h2 style="margin: 0; font-size: 1.25rem; font-weight: 600;">Applicable Branches</h2>
                <p style="margin: 0; opacity: 0.9; font-size: 0.875rem;">Manage branches for this promotion</p>
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
                    {{ $program->applicable_scope === 'all_branches' ? 'Applies to all branches' : 'No specific branches linked' }}
                </p>
                @if($program->applicable_scope === 'all_branches')
                <p style="margin: 0.5rem 0 0; font-size: 0.875rem;">This promotion is available at all branch locations.</p>
                @else
                <p style="margin: 0.5rem 0 0; font-size: 0.875rem;">Link specific branches to this promotion program below.</p>
                @endif
            </div>
            @else
            <div style="overflow-x: auto;">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Branch Name</th>
                            <th>Actions</th>
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
                    Link New Branch
                </h3>

                <form action="{{ route('admin.promotions.link-branch', $program) }}" method="POST" class="form-group">
                    @csrf
                    <select name="branch_id" class="form-select" required>
                        <option value="">Select a branch...</option>
                        @foreach ($availableBranches as $branch)
                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="action-btn btn-primary">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M9 12l2 2 4-4" />
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z" />
                        </svg>
                        Link Branch
                    </button>
                </form>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection