@extends('layouts.branch.contentLayoutMaster')
@section('title', 'Báo cáo vi phạm bình luận')
@section('content')
<div class="min-h-screen bg-gradient-to-br">
    <div class="flex flex-col gap-4 pb-4 delay-200 duration-700 ease-in-out">
        <div class="flex items-center gap-3 mb-4">
            <div class="flex aspect-square w-10 h-10 items-center justify-center rounded-lg bg-red-600 text-white">
                <i class="fas fa-flag text-xl"></i>
            </div>
            <div>
                <h2 class="text-3xl font-bold tracking-tight">Báo cáo vi phạm bình luận</h2>
                <p class="text-muted-foreground">Kiểm tra các báo cáo vi phạm về bình luận tại chi nhánh</p>
            </div>
        </div>

        <!-- Filter Form -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="p-6 border-b border-gray-200 bg-gray-50">
                <form method="GET" action="{{ route('branch.reviews.reports') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Loại báo cáo</label>
                        <select name="reason_type" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                            <option value="">Tất cả</option>
                            <option value="spam" {{ request('reason_type') == 'spam' ? 'selected' : '' }}>Spam</option>
                            <option value="inappropriate" {{ request('reason_type') == 'inappropriate' ? 'selected' : '' }}>Nội dung không phù hợp</option>
                            <option value="fake" {{ request('reason_type') == 'fake' ? 'selected' : '' }}>Đánh giá giả</option>
                            <option value="offensive" {{ request('reason_type') == 'offensive' ? 'selected' : '' }}>Ngôn từ xúc phạm</option>
                            <option value="other" {{ request('reason_type') == 'other' ? 'selected' : '' }}>Khác</option>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="w-full bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors">
                            <i class="fas fa-search mr-2"></i>Lọc báo cáo
                        </button>
                    </div>
                </form>
            </div>

            <!-- Reports Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sản phẩm</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Người dùng</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lý do</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nội dung báo cáo</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ngày</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($reports as $report)
                            <tr>
                                <td class="px-4 py-3 text-sm text-gray-900">{{ $report->id }}</td>
                                <td class="px-4 py-3">
                                    <div class="text-sm text-gray-900">
                                        @if($report->review->product)
                                            {{ $report->review->product->name }}
                                        @elseif($report->review->combo)
                                            {{ $report->review->combo->name }}
                                        @else
                                            N/A
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="text-sm text-gray-900">
                                        {{ $report->review->user->full_name ?? 'Ẩn danh' }}
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                        @switch($report->reason_type)
                                            @case('spam')
                                                bg-yellow-100 text-yellow-800
                                                @break
                                            @case('inappropriate')
                                                bg-red-100 text-red-800
                                                @break
                                            @case('fake')
                                                bg-purple-100 text-purple-800
                                                @break
                                            @case('offensive')
                                                bg-orange-100 text-orange-800
                                                @break
                                            @default
                                                bg-gray-100 text-gray-800
                                        @endswitch
                                    ">
                                        @switch($report->reason_type)
                                            @case('spam')
                                                Spam
                                                @break
                                            @case('inappropriate')
                                                Không phù hợp
                                                @break
                                            @case('fake')
                                                Đánh giá giả
                                                @break
                                            @case('offensive')
                                                Xúc phạm
                                                @break
                                            @default
                                                {{ $report->reason_type }}
                                        @endswitch
                                    </span>
                                </td>
                                <td class="px-4 py-3 max-w-xs">
                                    <div class="text-sm text-gray-900 truncate" title="{{ $report->reason_detail }}">
                                        {{ Str::limit($report->reason_detail, 80) }}
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-500">
                                    {{ $formatDate($report->created_at) }}
                                </td>
                                <td class="px-4 py-3">
                                    <a href="{{ route('branch.reviews.report.show', $report->review_id) }}" 
                                       class="inline-flex items-center px-3 py-1 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        <i class="fas fa-eye mr-1"></i> Xem chi tiết
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                    Không có báo cáo vi phạm nào tại chi nhánh này.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if ($reports->lastPage() > 1)
                <div class="bg-white px-6 py-4 border-t border-gray-200">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-700">
                            Hiển thị
                            <span class="font-medium text-gray-900">{{ ($reports->currentPage() - 1) * $reports->perPage() + 1 }}</span>
                            đến
                            <span class="font-medium text-gray-900">{{ min($reports->currentPage() * $reports->perPage(), $reports->total()) }}</span>
                            của
                            <span class="font-medium text-gray-900">{{ $reports->total() }}</span>
                            báo cáo
                        </div>
                        {{ $reports->links() }}
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
    // Realtime updates for branch review reports
    if (typeof Pusher !== 'undefined') {
        try {
            const pusher = new Pusher('{{ env('PUSHER_APP_KEY') }}', {
                cluster: '{{ env('PUSHER_APP_CLUSTER') }}',
                encrypted: true
            });

            const branchId = {{ auth('manager')->user()->branch->id ?? 0 }};
            const channel = pusher.subscribe('branch-reviews.' + branchId);

            // Listen for new reviews (might have reports)
            channel.bind('new-review', function(data) {
                console.log('New review received:', data);
                // Could potentially reload if this page shows all reviews
            });

            // Listen for deleted reviews
            channel.bind('review-deleted', function(data) {
                console.log('Review deleted:', data);
                
                // Remove any reports related to this review
                const reportRows = document.querySelectorAll(`tr[data-review-id="${data.review_id}"]`);
                reportRows.forEach(row => row.remove());
                
                // Show notification
                if (typeof dtmodalShowToast === 'function') {
                    dtmodalShowToast('info', {
                        title: 'Bình luận đã xóa',
                        message: 'Bình luận liên quan đến báo cáo đã bị xóa'
                    });
                }
            });

        } catch (error) {
            console.error('Pusher setup error:', error);
        }
    }
});
</script>
@endpush