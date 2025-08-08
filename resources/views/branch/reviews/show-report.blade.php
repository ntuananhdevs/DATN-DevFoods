@php
use Illuminate\Support\Facades\Storage;
@endphp

@extends('layouts.branch.contentLayoutMaster')
@section('title', 'Chi tiết báo cáo vi phạm')
@section('content')
<div class="min-h-screen bg-gradient-to-br">
    <div class="flex flex-col gap-4 pb-4 delay-200 duration-700 ease-in-out">
        <div class="flex items-center gap-3 mb-4">
            <a href="{{ route('branch.reviews.reports') }}" class="text-red-600 hover:text-red-800">
                <i class="fas fa-arrow-left text-xl"></i>
            </a>
            <div class="flex aspect-square w-10 h-10 items-center justify-center rounded-lg bg-red-600 text-white">
                <i class="fas fa-flag text-xl"></i>
            </div>
            <div>
                <h2 class="text-3xl font-bold tracking-tight">Chi tiết báo cáo vi phạm</h2>
                <p class="text-muted-foreground">Xem chi tiết báo cáo và bình luận bị báo cáo</p>
            </div>
        </div>

        <!-- Review Details -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Thông tin bình luận bị báo cáo</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-3">
                        <div>
                            <span class="text-sm font-medium text-gray-500">Khách hàng:</span>
                            <span class="ml-2 text-sm text-gray-900">{{ $review->user->full_name ?? 'Ẩn danh' }}</span>
                        </div>
                        <div>
                            <span class="text-sm font-medium text-gray-500">Sản phẩm:</span>
                            <span class="ml-2 text-sm text-gray-900">
                                @if($review->product)
                                    {{ $review->product->name }}
                                @elseif($review->combo)
                                    {{ $review->combo->name }}
                                @else
                                    N/A
                                @endif
                            </span>
                        </div>
                        <div>
                            <span class="text-sm font-medium text-gray-500">Đánh giá:</span>
                            <div class="ml-2 inline-flex items-center">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}"></i>
                                @endfor
                                <span class="ml-2 text-sm text-gray-600">({{ $review->rating }}/5)</span>
                            </div>
                        </div>
                        <div>
                            <span class="text-sm font-medium text-gray-500">Ngày đánh giá:</span>
                            <span class="ml-2 text-sm text-gray-900">{{ $formatDate($review->review_date) }}</span>
                        </div>
                        <div>
                            <span class="text-sm font-medium text-gray-500">Số báo cáo:</span>
                            <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                {{ $reports->count() }} báo cáo
                            </span>
                        </div>
                    </div>
                    <div>
                        <h4 class="text-sm font-medium text-gray-700 mb-2">Nội dung bình luận:</h4>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <p class="text-gray-900">{{ $review->review }}</p>
                        </div>
                        @if($review->review_image)
                            <div class="mt-4">
                                <h4 class="text-sm font-medium text-gray-700 mb-2">Hình ảnh đính kèm:</h4>
                                <img src="{{ Storage::disk('s3')->url($review->review_image) }}" alt="Review Image" class="max-w-xs rounded-lg shadow-md">
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Reports List -->
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Danh sách báo cáo ({{ $reports->count() }})</h3>
                <div class="space-y-4">
                    @foreach($reports as $report)
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-8 w-8 bg-gray-300 rounded-full flex items-center justify-center">
                                        <i class="fas fa-user text-gray-600 text-sm"></i>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-900">{{ $report->user->full_name ?? 'Ẩn danh' }}</p>
                                        <p class="text-xs text-gray-500">{{ $formatDate($report->created_at) }}</p>
                                    </div>
                                </div>
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
                            </div>
                            <div class="bg-red-50 p-3 rounded-lg">
                                <p class="text-sm text-gray-900">{{ $report->reason_detail }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex justify-end space-x-4">
            <a href="{{ route('branch.reviews.show', $review->id) }}" 
               class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                <i class="fas fa-eye mr-2"></i>Xem chi tiết bình luận
            </a>
            @if($reports->count() >= 5)
                <button onclick="deleteReview({{ $review->id }})" 
                        class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors">
                    <i class="fas fa-trash mr-2"></i>Xóa bình luận
                </button>
            @else
                <span class="text-sm text-gray-500 bg-gray-100 px-4 py-2 rounded-lg">
                    <i class="fas fa-info-circle mr-2"></i>
                    Cần {{ 5 - $reports->count() }} báo cáo nữa để có thể xóa
                </span>
            @endif
        </div>
    </div>
</div>

<script>
function deleteReview(reviewId) {
    if (typeof dtmodalCreateModal === 'function') {
        dtmodalCreateModal({
            type: 'warning',
            title: 'Xác nhận xóa bình luận',
            message: 'Bạn có chắc chắn muốn xóa bình luận vi phạm này? Hành động này không thể hoàn tác.',
            confirmText: 'Xóa bình luận',
            cancelText: 'Hủy',
            onConfirm: function() {
                performDeleteReview(reviewId);
            }
        });
    } else {
        if (confirm('Bạn có chắc chắn muốn xóa bình luận vi phạm này? Hành động này không thể hoàn tác.')) {
            performDeleteReview(reviewId);
        }
    }
}

function performDeleteReview(reviewId) {
    fetch(`/branch/reviews/${reviewId}/delete`, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            if (typeof dtmodalShowToast === 'function') {
                dtmodalShowToast('success', {
                    title: 'Thành công',
                    message: data.message || 'Xóa bình luận thành công!'
                });
            }
            
            // Redirect to reports list after successful deletion
            setTimeout(() => {
                window.location.href = '/branch/reviews/reports/list';
            }, 1500);
        } else {
            if (typeof dtmodalShowToast === 'function') {
                dtmodalShowToast('error', {
                    title: 'Lỗi',
                    message: data.message || 'Có lỗi xảy ra!'
                });
            } else {
                alert(data.message || 'Có lỗi xảy ra!');
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        if (typeof dtmodalShowToast === 'function') {
            dtmodalShowToast('error', {
                title: 'Lỗi',
                message: 'Có lỗi xảy ra khi xóa bình luận!'
            });
        } else {
            alert('Có lỗi xảy ra!');
        }
    });
}

// Realtime updates for report count
document.addEventListener('DOMContentLoaded', function() {
    if (typeof Pusher !== 'undefined') {
        try {
            const pusher = new Pusher('{{ env('PUSHER_APP_KEY') }}', {
                cluster: '{{ env('PUSHER_APP_CLUSTER') }}',
                encrypted: true
            });

            const reviewId = {{ $review->id }};
            const reportChannel = pusher.subscribe('review-reports');

            // Listen for report count updates
            reportChannel.bind('review-report-updated', function(data) {
                console.log('Report count updated:', data);
                
                if (data.review_id == reviewId) {
                    // Update report count display
                    const reportCountElements = document.querySelectorAll('.report-count');
                    reportCountElements.forEach(element => {
                        element.textContent = data.report_count + ' báo cáo';
                    });
                    
                    // Update action button based on report count
                    const actionContainer = document.querySelector('.action-buttons');
                    if (actionContainer) {
                        if (data.report_count >= 5) {
                            // Show delete button
                            actionContainer.innerHTML = `
                                <a href="{{ route('branch.reviews.show', $review->id) }}" 
                                   class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                                    <i class="fas fa-eye mr-2"></i>Xem chi tiết bình luận
                                </a>
                                <button onclick="deleteReview(${reviewId})" 
                                        class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors">
                                    <i class="fas fa-trash mr-2"></i>Xóa bình luận
                                </button>
                            `;
                        } else {
                            // Show info message
                            const needed = 5 - data.report_count;
                            actionContainer.innerHTML = `
                                <a href="{{ route('branch.reviews.show', $review->id) }}" 
                                   class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                                    <i class="fas fa-eye mr-2"></i>Xem chi tiết bình luận
                                </a>
                                <span class="text-sm text-gray-500 bg-gray-100 px-4 py-2 rounded-lg">
                                    <i class="fas fa-info-circle mr-2"></i>
                                    Cần ${needed} báo cáo nữa để có thể xóa
                                </span>
                            `;
                        }
                    }
                    
                    // Show notification
                    if (typeof dtmodalShowToast === 'function') {
                        dtmodalShowToast('info', {
                            title: 'Cập nhật báo cáo',
                            message: `Số lượng báo cáo đã được cập nhật: ${data.report_count}`
                        });
                    }
                }
            });

        } catch (error) {
            console.error('Pusher setup error:', error);
        }
    }
});
</script>
@endsection