@extends('layouts.branch.contentLayoutMaster')
@section('title', 'Quản lý bình luận chi nhánh')
@section('content')
<div class="min-h-screen bg-gradient-to-br">
    <div class="flex flex-col gap-4 pb-4 delay-200 duration-700 ease-in-out">
        <div class="flex items-center gap-3 mb-4">
            <div class="flex aspect-square w-10 h-10 items-center justify-center rounded-lg bg-blue-600 text-white">
                <i class="fas fa-comments text-xl"></i>
            </div>
            <div>
                <h2 class="text-3xl font-bold tracking-tight">Quản lý bình luận chi nhánh</h2>
                <p class="text-muted-foreground">Xem và quản lý các bình luận của khách hàng tại chi nhánh</p>
            </div>
        </div>

        <!-- Filter Form -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="p-6 border-b border-gray-200 bg-gray-50">
                <form method="GET" action="{{ route('branch.reviews.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Từ khóa</label>
                        <input type="text" name="keyword" value="{{ request('keyword') }}" 
                               placeholder="Tìm theo tên khách hàng, sản phẩm..." 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Đánh giá</label>
                        <select name="rating" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Tất cả</option>
                            <option value="5" {{ request('rating') == '5' ? 'selected' : '' }}>5 sao</option>
                            <option value="4" {{ request('rating') == '4' ? 'selected' : '' }}>4 sao</option>
                            <option value="3" {{ request('rating') == '3' ? 'selected' : '' }}>3 sao</option>
                            <option value="2" {{ request('rating') == '2' ? 'selected' : '' }}>2 sao</option>
                            <option value="1" {{ request('rating') == '1' ? 'selected' : '' }}>1 sao</option>
                        </select>
                    </div>
                    <div>
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                            <i class="fas fa-search mr-2"></i>Tìm kiếm
                        </button>
                    </div>
                </form>
            </div>

            <!-- Reviews Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Khách hàng</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sản phẩm</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Đánh giá</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nội dung</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ngày</th>

                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($reviews as $review)
                            <tr data-review-id="{{ $review->id }}">
                                <td class="px-4 py-3">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                                <i class="fas fa-user text-gray-600"></i>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $review->user->full_name ?? 'Ẩn danh' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="text-sm text-gray-900">
                                        @if($review->product)
                                            {{ $review->product->name }}
                                        @elseif($review->combo)
                                            {{ $review->combo->name }}
                                        @else
                                            N/A
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="fas fa-star {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}"></i>
                                        @endfor
                                        <span class="ml-2 text-sm text-gray-600">({{ $review->rating }})</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 max-w-xs">
                                    <div class="text-sm text-gray-900 truncate" title="{{ $review->review }}">
                                        {{ Str::limit($review->review, 100) }}
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-500">
                                    {{ $formatDate($review->review_date) }}
                                </td>

                                <td class="px-4 py-3 text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('branch.reviews.show', $review->id) }}" 
                                           class="text-blue-600 hover:text-blue-900" title="Xem chi tiết">
                                            <i class="fas fa-eye"></i>
                                        </a>

                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                    Chưa có bình luận nào tại chi nhánh này.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if ($reviews->lastPage() > 1)
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
                        {{ $reviews->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>


@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Realtime updates for branch reviews
    if (typeof Pusher !== 'undefined') {
        try {
            const pusher = new Pusher('{{ env('PUSHER_APP_KEY') }}', {
                cluster: '{{ env('PUSHER_APP_CLUSTER') }}',
                encrypted: true
            });

            const branchId = {{ auth('manager')->user()->branch->id ?? 0 }};
            const channel = pusher.subscribe('branch-reviews.' + branchId);

            // Listen for new reviews
            channel.bind('new-review', function(data) {
                console.log('New review received:', data);
                
                // Show notification
                if (typeof dtmodalShowToast === 'function') {
                    dtmodalShowToast('notification', {
                        title: 'Bình luận mới',
                        message: 'Có bình luận mới từ khách hàng!'
                    });
                } else {
                    alert('Có bình luận mới từ khách hàng!');
                }
                
                // Reload page to show new review
                setTimeout(() => {
                    location.reload();
                }, 1000);
            });

            // Listen for deleted reviews
            channel.bind('review-deleted', function(data) {
                console.log('Review deleted:', data);
                
                // Remove review from table if exists
                const reviewRow = document.querySelector(`tr[data-review-id="${data.review_id}"]`);
                if (reviewRow) {
                    reviewRow.remove();
                }
                
                // Show notification
                if (typeof dtmodalShowToast === 'function') {
                    dtmodalShowToast('info', {
                        title: 'Bình luận đã xóa',
                        message: 'Một bình luận đã bị xóa'
                    });
                } else {
                    alert('Một bình luận đã bị xóa');
                }
            });

        } catch (error) {
            console.error('Pusher setup error:', error);
        }
    }
});
</script>
@endpush