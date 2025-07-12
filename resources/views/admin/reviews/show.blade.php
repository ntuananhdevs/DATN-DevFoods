@extends('layouts.admin.contentLayoutMaster')
@section('title', 'Chi tiết bình luận')
@section('content')
<main class="container mx-auto px-4">
  <div class="bg-white rounded-lg shadow p-6 space-y-6">
    <div class="flex items-center justify-between">
      <h1 class="text-2xl font-semibold">Chi tiết bình luận</h1>
      <div>
        @if(!$review->approved)
        <form action="{{ route('admin.reviews.approve', $review->id) }}" method="POST" class="inline">
          @csrf
          <button type="submit" class="btn btn-sm btn-primary">Duyệt</button>
        </form>
        @endif
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
            @endif
            <span class="text-lg font-medium">{{ $review->product->name ?? 'N/A' }}</span>
          </div>
        </div>
        <div>
          <p class="text-sm text-gray-600">Người dùng</p>
          <div class="flex items-center gap-2">
            @if($review->user && $review->user->avatar)
              <img src="{{ $review->user->avatar }}" alt="{{ $review->user->name }}" class="w-10 h-10 object-cover rounded-full">
            @endif
            <span class="text-lg font-medium">{{ $review->user->name ?? 'Ẩn danh' }}</span>
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
        <div>
          <p class="text-sm text-gray-600">Trạng thái duyệt</p>
          <span class="inline-block px-3 py-1 rounded-full text-sm font-medium {{ $review->approved ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
            {{ $review->approved ? 'Đã duyệt' : 'Chưa duyệt' }}
          </span>
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
@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
  const approveForm = document.querySelector('form[action*="approve"]');
  if (approveForm) {
    const approveBtn = approveForm.querySelector('button[type="submit"]');
    if (approveBtn) {
      approveBtn.addEventListener('click', function(e) {
        e.preventDefault();
        const isApprove = approveBtn.classList.contains('btn-primary');
        dtmodalCreateModal({
          icon: isApprove ? 'fa-solid fa-circle-check text-green-500' : 'fa-solid fa-circle-xmark text-yellow-500',
          title: isApprove ? 'Xác nhận duyệt bình luận?' : 'Xác nhận bỏ duyệt bình luận?',
          subtitle: isApprove ? 'Bình luận này sẽ được hiển thị công khai cho khách hàng.' : 'Bình luận này sẽ bị ẩn khỏi khách hàng.',
          message: isApprove ? 'Bạn có chắc chắn muốn DUYỆT bình luận này? Hành động này sẽ giúp khách hàng khác tham khảo ý kiến.' : 'Bạn có chắc chắn muốn BỎ DUYỆT bình luận này? Bình luận sẽ không còn hiển thị công khai.',
          confirmText: isApprove ? 'Duyệt ngay' : 'Bỏ duyệt',
          cancelText: 'Huỷ',
          onConfirm: function() {
            approveForm.submit();
          },
          onCancel: function() {}
        });
      });
    }
  }
});
</script>
@endsection 