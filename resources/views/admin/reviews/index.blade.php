@extends('layouts.admin.contentLayoutMaster')
@section('title', 'Quản lý bình luận sản phẩm')
@section('content')
<div class="min-h-screen bg-gradient-to-br">
    <div class="flex flex-col gap-4 pb-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="flex aspect-square w-10 h-10 items-center justify-center rounded-lg bg-primary text-primary-foreground">
                    <i class="fas fa-comments text-white text-xl"></i>
                </div>
                <div>
                    <h2 class="text-3xl font-bold tracking-tight">Quản lý bình luận sản phẩm</h2>
                    <p class="text-muted-foreground">Kiểm duyệt, trả lời, và quản lý các đánh giá sản phẩm</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-lg overflow-hidden transform transition-all duration-500 hover:shadow-2xl">
            <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-800">Danh sách bình luận</h2>
            </div>
            <div class="p-6 border-b border-gray-200 bg-gray-50">
                <form method="GET" action="{{ route('admin.reviews.filter') }}" class="flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-4 lg:space-y-0 gap-2">
                    <div class="flex flex-1">
                        <div class="relative flex-1 max-w-md">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-search text-gray-400"></i>
                            </div>
                            <input type="text" name="keyword" placeholder="Tìm kiếm theo sản phẩm, người dùng, nội dung..." value="{{ request('keyword') }}" class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg leading-5 bg-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-300">
                        </div>
                    </div>
                    <div class="flex gap-2 items-center">
                        <a href="{{ route('admin.reviews.reports') }}" class="flex items-center gap-1 border rounded px-3 py-2 min-w-[120px] bg-pink-50 hover:bg-pink-100 text-pink-600 font-semibold transition" title="Xem báo cáo vi phạm">
                            <i class="fas fa-flag"></i>
                            <span>Báo cáo</span>
                        </a>
                        <select name="rating" class="border rounded px-2 py-2 min-w-[120px]">
                            <option value="">Tất cả sao</option>
                            @for($i=5;$i>=1;$i--)
                                <option value="{{ $i }}" @if(request('rating')==$i) selected @endif>{{ $i }} sao</option>
                            @endfor
                        </select>
                        <button type="submit" class="bg-primary text-white px-6 py-2 rounded-lg font-semibold hover:bg-primary-dark transition">Lọc</button>
                    </div>
                </form>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200" id="reviewTable">
                    <thead>
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sản phẩm</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Người dùng</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nội dung</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Số sao</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($reviews as $review)
                            <tr>
                                <td class="px-4 py-3">{{ $review->id }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        @if($review->product && $review->product->image)
                                            <img src="{{ Storage::disk('s3')->url($review->product->image) }}" alt="{{ $review->product->name }}" class="w-10 h-10 object-cover rounded">
                                        @elseif($review->combo && $review->combo->image_url)
                                            <img src="{{ $review->combo->image_url }}" alt="{{ $review->combo->name }}" class="w-10 h-10 object-cover rounded">
                                        @endif
                                        <span>
                                            @if($review->product)
                                                {{ $review->product->name }}
                                            @elseif($review->combo)
                                                [Combo] {{ $review->combo->name }}
                                            @else
                                                N/A
                                            @endif
                                        </span>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        @if($review->user && $review->user->avatar)
                                            <img src="{{ $review->user->avatar }}" alt="{{ $review->user->name }}" class="w-8 h-8 object-cover rounded-full">
                                        @endif
                                        <span>{{ $review->user->name ?? 'Ẩn danh' }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 max-w-xs truncate" title="{{ $review->review }}">{{ Str::limit($review->review, 80) }}</td>
                                <td class="px-4 py-3 text-center">{{ $review->rating }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex gap-2">
                                        <a href="{{ route('admin.reviews.show', $review->id) }}" class="btn btn-xs btn-secondary" title="Xem chi tiết">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <form action="{{ route('admin.reviews.destroy', $review->id) }}" method="POST" class="delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn btn-xs btn-danger" title="Xóa"
                                                onclick="dtmodalCreateModal({
                                                    type: 'warning',
                                                    title: 'Xác nhận xóa bình luận',
                                                    subtitle: 'Bạn có chắc chắn muốn xóa bình luận này?',
                                                    message: `Hành động này không thể hoàn tác. Bạn đang xóa bình luận có nội dung: <strong>\'{{ Str::limit($review->review, 40) }}\'</strong>`,
                                                    confirmText: 'Xác nhận xóa',
                                                    cancelText: 'Hủy bỏ',
                                                    onConfirm: () => {
                                                        const form = this.closest('form');
                                                        const url = form.action;
                                                        const token = form.querySelector('input[name=_token]').value;
                                                        const row = form.closest('tr');

                                                        fetch(url, {
                                                            method: 'DELETE',
                                                            headers: {
                                                                'X-CSRF-TOKEN': token,
                                                                'Accept': 'application/json',
                                                                'Content-Type': 'application/json'
                                                            }
                                                        })
                                                        .then(res => res.json())
                                                        .then(data => {
                                                            if(data.success) {
                                                                dtmodalShowToast('success', { title: 'Thành công', message: data.message });
                                                                row.remove();
                                                            } else {
                                                                dtmodalShowToast('error', { title: 'Lỗi', message: data.message });
                                                            }
                                                        })
                                                        .catch(() => {
                                                            dtmodalShowToast('error', { title: 'Lỗi', message: 'Có lỗi xảy ra, vui lòng thử lại.' });
                                                        });
                                                    }
                                                })">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-500">Không có bình luận nào.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="bg-white px-6 py-4 border-t border-gray-200">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-700">
                        Hiển thị
                        <span class="font-medium text-gray-900">{{ ($reviews->currentPage() - 1) * $reviews->perPage() + 1 }}</span>
                        đến
                        <span class="font-medium text-gray-900">{{ min($reviews->currentPage() * $reviews->perPage(), $reviews->total()) }}</span>
                        của
                        <span class="font-medium text-gray-900">{{ $reviews->total() }}</span>
                        bình luận
                    </div>
                    @if ($reviews->lastPage() > 1)
                        <div class="flex items-center space-x-2">
                            @if (!$reviews->onFirstPage())
                                <a href="{{ $reviews->previousPageUrl() }}&{{ http_build_query(request()->except('page')) }}"
                                    class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:text-gray-700 transform transition-all duration-200 hover:scale-105">
                                    <i class="fas fa-chevron-left mr-1"></i>
                                    Trước
                                </a>
                            @endif
                            @php
                                $start = max(1, $reviews->currentPage() - 2);
                                $end = min($reviews->lastPage(), $reviews->currentPage() + 2);
                                if ($start > 1) {
                                    echo '<a href="' . $reviews->url(1) . '&' . http_build_query(request()->except('page')) . '" class="relative inline-flex items-center px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:text-gray-700 transform transition-all duration-200 hover:scale-105">1</a>';
                                    if ($start > 2) {
                                        echo '<span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700">...</span>';
                                    }
                                }
                            @endphp
                            @for ($i = $start; $i <= $end; $i++)
                                <a href="{{ $reviews->url($i) }}&{{ http_build_query(request()->except('page')) }}"
                                    class="relative inline-flex items-center px-3 py-2 text-sm font-medium rounded-lg transform transition-all duration-200 hover:scale-105 {{ $reviews->currentPage() == $i ? 'bg-blue-500 text-white border-blue-500' : 'text-gray-500 bg-white border-gray-300 hover:bg-gray-50 hover:text-gray-700' }}">
                                    {{ $i }}
                                </a>
                            @endfor
                            @php
                                if ($end < $reviews->lastPage()) {
                                    if ($end < $reviews->lastPage() - 1) {
                                        echo '<span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700">...</span>';
                                    }
                                    echo '<a href="' . $reviews->url($reviews->lastPage()) . '&' . http_build_query(request()->except('page')) . '" class="relative inline-flex items-center px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:text-gray-700 transform transition-all duration-200 hover:scale-105">' . $reviews->lastPage() . '</a>';
                                }
                            @endphp
                            @if ($reviews->hasMorePages())
                                <a href="{{ $reviews->nextPageUrl() }}&{{ http_build_query(request()->except('page')) }}"
                                    class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:text-gray-700 transform transition-all duration-200 hover:scale-105">
                                    Tiếp
                                    <i class="fas fa-chevron-right ml-1"></i>
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    @if(session('success'))
        dtmodalShowToast('success', {
            title: 'Thành công',
            message: '{{ session('success') }}'
        });
    @endif
    @if(session('error'))
        dtmodalShowToast('error', {
            title: 'Lỗi',
            message: '{{ session('error') }}'
        });
    @endif
});

function showReviewDetailModal(review) {
    let modalHtml = `
        <div class='dtmodal-overlay dtmodal-active dtmodal-dynamic' id='reviewDetailModal'>
            <div class='dtmodal-container dtmodal-info'>
                <div class='dtmodal-header'>
                    <div class='dtmodal-title-content'>
                        <h3 class='dtmodal-title'>Chi tiết bình luận #${review.id}</h3>
                    </div>
                    <button class='dtmodal-close' onclick='dtmodalCloseModal("reviewDetailModal")'>
                        <i class='fas fa-times'></i>
                    </button>
                </div>
                <div class='dtmodal-body'>
                    <p><b>Sản phẩm:</b> ${review.product?.name ?? 'N/A'}</p>
                    <p><b>Người dùng:</b> ${review.user?.name ?? 'Ẩn danh'}</p>
                    <p><b>Số sao:</b> ${review.rating}</p>
                    <p><b>Nội dung:</b> ${review.review}</p>
                    <p><b>Ngày:</b> ${review.review_date ?? ''}</p>
                </div>
                <div class='dtmodal-footer'>
                    <button class='dtmodal-btn dtmodal-btn-primary' onclick='dtmodalCloseModal("reviewDetailModal")'>Đóng</button>
                </div>
            </div>
        </div>
    `;
    document.body.insertAdjacentHTML('beforeend', modalHtml);
}
</script>
@endpush 