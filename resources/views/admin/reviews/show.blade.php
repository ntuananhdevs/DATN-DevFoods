@extends('layouts.admin.contentLayoutMaster')
@section('title', 'Chi tiết bình luận')
@section('content')
<main class="container mx-auto px-4">
  <div class="bg-white rounded-lg shadow p-6 space-y-6">
    <div class="flex items-center justify-between">
      <h1 class="text-2xl font-semibold">Chi tiết bình luận</h1>
      <div>
        <a href="{{ route('admin.reviews.index') }}" class="btn btn-sm btn-secondary ml-2">Quay lại</a>
      </div>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <!-- Thông tin chi tiết -->
      <div class="space-y-4">
        <div>
          <p class="text-sm text-gray-600">ID</p>
          <p class="text-lg font-medium">{{ $review->id }}</p>
        </div>
        <div>
          <p class="text-sm text-gray-600">Sản phẩm</p>
          <div class="flex items-center gap-2">
            @if($review->product && $review->product->image)
              <img src="{{ Storage::disk('s3')->url($review->product->image) }}" alt="{{ $review->product->name }}" class="w-12 h-12 object-cover rounded">
            @elseif($review->combo && $review->combo->image_url)
              <img src="{{ $review->combo->image_url }}" alt="{{ $review->combo->name }}" class="w-12 h-12 object-cover rounded">
            @endif
            <span class="text-lg font-medium">
              @if($review->product)
                {{ $review->product->name }}
              @elseif($review->combo)
                [Combo] {{ $review->combo->name }}
              @else
                N/A
              @endif
            </span>
          </div>
        </div>
        <div>
          <p class="text-sm text-gray-600">Người dùng</p>
          <div class="flex items-center gap-2">
            @if($review->user && $review->user->avatar)
              <img src="{{ $review->user->avatar }}" alt="{{ $review->user->name }}" class="w-10 h-10 object-cover rounded-full">
            @endif
            <span class="text-lg font-medium">{{ $review->user->name }}</span>
          </div>
        </div>
        <div>
          <p class="text-sm text-gray-600">Số sao</p>
          <p class="text-lg font-medium">{{ $review->rating }}</p>
        </div>
        <div>
          <p class="text-sm text-gray-600">Nội dung</p>
          <p class="text-gray-800">{{ $review->review }}</p>
        </div>
        <div>
          <p class="text-sm text-gray-600">Ảnh bình luận</p>
          @if($review->review_image)
            <img src="{{ Storage::disk('s3')->url($review->review_image) }}" alt="Ảnh bình luận" class="w-32 h-32 object-cover rounded border">
          @else
            <span class="text-gray-500 italic">Không có ảnh bình luận</span>
          @endif
        </div>
        <div>
          <p class="text-sm text-gray-600">Ngày</p>
          <p class="text-gray-700">{{ $review->review_date ? $review->review_date->format('d/m/Y H:i') : '' }}</p>
        </div>
      </div>
      <!-- Phản hồi -->
      <div>
        <p class="text-sm text-gray-600 mb-2">Phản hồi</p>
        @forelse($review->replies as $reply)
          <div class="border rounded p-2 mb-2">
            <div class="flex items-center gap-2 mb-1">
              <span class="font-semibold">{{ $reply->user->name ?? 'Ẩn danh' }}</span>
              <span class="text-xs text-gray-500">{{ $reply->reply_date ? $reply->reply_date->format('d/m/Y H:i') : '' }}</span>
            </div>
            <div>{{ $reply->reply }}</div>
          </div>
        @empty
          <p class="text-gray-500">Chưa có phản hồi nào.</p>
        @endforelse
      </div>
    </div>
  </div>
</main>
@endsection 