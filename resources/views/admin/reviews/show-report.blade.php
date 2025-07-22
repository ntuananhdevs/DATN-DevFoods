@extends('layouts.admin.contentLayoutMaster')
@section('title', 'Chi tiết báo cáo bình luận')
@section('content')
<main class="container mx-auto px-4">
  <div class="bg-white rounded-lg shadow p-6 space-y-6">
    <div class="flex items-center justify-between">
      <h1 class="text-2xl font-semibold">Chi tiết báo cáo bình luận</h1>
      <a href="{{ route('admin.reviews.reports') }}" class="btn btn-sm btn-secondary ml-2">Quay lại danh sách báo cáo</a>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <!-- Thông tin chi tiết bình luận -->
      <div class="space-y-4">
        <div>
          <p class="text-sm text-gray-600">ID bình luận</p>
          <p class="text-lg font-medium">{{ $review->id }}</p>
        </div>
        <div class="mb-4">
            <p class="text-sm text-gray-600">Sản phẩm/Combo</p>
            @if($review->product)
                <p class="font-semibold">{{ $review->product->name }}</p>
            @elseif($review->combo)
                <p class="font-semibold">{{ $review->combo->name }}</p>
            @else
                <p class="font-semibold">N/A</p>
            @endif
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
        <div class="mb-4">
            <p class="text-sm text-gray-600">Nội dung review</p>
            <p class="font-semibold">{{ $review->review }}</p>
        </div>
        @if($review->review_image)
        <div>
          <p class="text-sm text-gray-600">Ảnh bình luận</p>
          <img src="{{ Storage::disk('s3')->url($review->review_image) }}" alt="Ảnh bình luận" class="w-32 h-32 object-cover rounded border">
        </div>
        @endif
        <div>
          <p class="text-sm text-gray-600">Ngày</p>
          <p class="text-gray-700">{{ $review->review_date ? $review->review_date->format('d/m/Y H:i') : '' }}</p>
        </div>
        @if(isset($review->report_count) && $review->report_count >= 10)
        <div class="p-3 mb-2 rounded bg-red-100 border border-red-400 text-red-700 flex items-center gap-2">
          <i class="fas fa-exclamation-triangle text-xl"></i>
          <span>Cảnh báo: Bình luận này đã bị báo cáo {{ $review->report_count }} lần. Hãy cân nhắc xóa bình luận này!</span>
          <button type="button" class="btn btn-sm btn-danger ml-auto" onclick="showDeleteModal()">Xóa bình luận</button>
        </div>
        <!-- Modal xác nhận xóa -->
        <div id="deleteModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40 hidden">
          <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
            <h2 class="text-xl font-bold mb-4 text-red-600 flex items-center gap-2"><i class="fas fa-exclamation-triangle"></i> Xác nhận xóa bình luận</h2>
            <p>Bạn có chắc chắn muốn xóa bình luận này không? Hành động này không thể hoàn tác.</p>
            <div class="mt-6 flex justify-end gap-2">
              <button type="button" class="btn btn-secondary" onclick="hideDeleteModal()">Huỷ</button>
              <form id="deleteReviewForm" action="{{ route('admin.reviews.destroy', $review->id) }}" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">Xóa</button>
              </form>
            </div>
          </div>
        </div>
        <script>
        function showDeleteModal() {
          document.getElementById('deleteModal').classList.remove('hidden');
        }
        function hideDeleteModal() {
          document.getElementById('deleteModal').classList.add('hidden');
        }
        </script>
        @endif
      </div>
      <!-- Thông tin báo cáo -->
      <div class="space-y-4">
        <div>
          <p class="text-sm text-gray-600">Tổng số báo cáo</p>
          <p class="text-lg font-bold text-red-600">{{ $review->report_count }}</p>
        </div>
        @foreach($reports as $report)
        <div class="border rounded p-2 mb-2">
          <div class="flex items-center gap-2 mb-1">
            <span class="font-semibold">{{ $report->user->name ?? 'Ẩn danh' }}</span>
            <span class="text-xs text-gray-500">{{ $report->created_at ? $report->created_at->format('d/m/Y H:i') : '' }}</span>
          </div>
          <div><b>Lý do:</b> {{ $report->reason_type }}</div>
          <div><b>Nội dung:</b> {{ $report->reason_detail }}</div>
        </div>
        @endforeach
      </div>
    </div>
  </div>
</main>
@endsection 