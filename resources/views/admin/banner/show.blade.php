@php
use Illuminate\Support\Facades\Storage;
@endphp

@extends('layouts.admin.contentLayoutMaster')

@section('content')
<div class="container">
    <h2 class="mb-4">Chi tiết banner</h2>

    <div class="banner-details-container">
        {{-- Banner Image --}}
        <div class="banner-card">
            @if ($banner->image_path)
                <img src="{{ Storage::disk('s3')->url($banner->image_path) }}" alt="{{ $banner->title }}" class="banner-image">
            @else
                <div class="banner-image bg-light d-flex align-items-center justify-content-center">
                    <span>Không có ảnh</span>
                </div>
            @endif

            <div class="banner-actions mt-4">
                <a href="{{ route('admin.banners.edit', $banner->id) }}" class="btn btn-warning">
                    <i class="fas fa-pen"></i> Sửa
                </a>
                <a href="{{ route('admin.banners.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Quay lại
                </a>
            </div>
        </div>

        {{-- Banner Details --}}
        <div class="banner-info-card">
            <h5 class="mb-3">Thông tin banner</h5>
            <table class="table table-borderless banner-info-table">
                <tr>
                    <td>Tiêu đề</td>
                    <td>{{ $banner->title }}</td>
                </tr>
                <tr>
                    <td>Mô tả</td>
                    <td>{{ $banner->description ?: 'Không có mô tả' }}</td>
                </tr>
                <tr>
                    <td>Liên kết</td>
                    <td>{{ $banner->link ?: 'Không có liên kết' }}</td>
                </tr>
                <tr>
                    <td>Trạng thái</td>
                    <td>
                        <span class="badge {{ $banner->is_active ? 'badge-success' : 'badge-danger' }}">
                            {{ $banner->is_active ? 'Hoạt động' : 'Vô hiệu hóa' }}
                        </span>
                    </td>
                </tr>
                <tr>
                    <td>Ngày bắt đầu</td>
                    <td>{{ $banner->start_at->format('d/m/Y H:i') }}</td>
                </tr>
                <tr>
                    <td>Ngày kết thúc</td>
                    <td>{{ $banner->end_at->format('d/m/Y H:i') }}</td>
                </tr>
                <tr>
                    <td>Ngày tạo</td>
                    <td>{{ $banner->created_at->format('d/m/Y H:i') }}</td>
                </tr>
                <tr>
                    <td>Lần cập nhật</td>
                    <td>{{ $banner->updated_at->format('d/m/Y H:i') }}</td>
                </tr>
                <tr>
                    <td>Vị trí hiển thị</td>
                    <td>
                        @if($banner->order === 0)
                            Đầu tiên
                        @elseif($banner->order === 1)
                            Giữa
                        @else
                            Cuối cùng
                        @endif
                    </td>
                </tr>
            </table>
        </div>
    </div>
</div>
@endsection
