@php
use Illuminate\Support\Facades\Storage;
@endphp

@extends('layouts.branch.contentLayoutMaster')
@section('title', 'Chi tiết bình luận')
@section('content')
<div class="min-h-screen bg-gradient-to-br">
    <div class="flex flex-col gap-4 pb-4 delay-200 duration-700 ease-in-out">
        <div class="flex items-center gap-3 mb-4">
            <a href="{{ route('branch.reviews.index') }}" class="text-blue-600 hover:text-blue-800">
                <i class="fas fa-arrow-left text-xl"></i>
            </a>
            <div class="flex aspect-square w-10 h-10 items-center justify-center rounded-lg bg-blue-600 text-white">
                <i class="fas fa-comment text-xl"></i>
            </div>
            <div>
                <h2 class="text-3xl font-bold tracking-tight">Chi tiết bình luận</h2>
                <p class="text-muted-foreground">Xem chi tiết và phản hồi bình luận khách hàng</p>
            </div>
        </div>

        <!-- Review Details -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Thông tin bình luận</h3>
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

                        </div>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Nội dung bình luận</h3>
                        <div class="review-content bg-gradient-to-r from-amber-50 to-orange-50 p-6 rounded-lg border-l-4 border-amber-400">
                            <div class="flex items-start">
                                <div class="flex-shrink-0 h-10 w-10 bg-amber-500 rounded-full flex items-center justify-center mr-4">
                                    <i class="fas fa-star text-white"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="text-gray-900 text-base leading-relaxed">{{ $review->review }}</p>
                                    @if($review->review_image)
                                        <div class="mt-4">
                                            <img src="{{ Storage::disk('s3')->url($review->review_image) }}" alt="Review Image" class="max-w-xs rounded-lg shadow-md border border-gray-200">
                                        </div>
                                    @endif
                                    <div class="mt-4 pt-3 border-t border-amber-200">
                                        <div class="flex items-center justify-between text-sm text-gray-600">
                                            <span class="flex items-center">
                                                <i class="fas fa-reply mr-2 text-amber-600"></i>
                                                Tổng số phản hồi: <strong class="ml-1 text-amber-700">{{ $review->replies->count() }}</strong>
                                            </span>
                                            <span class="flex items-center">
                                                <i class="fas fa-store mr-2 text-blue-600"></i>
                                                Phản hồi từ chi nhánh: <strong class="ml-1 text-blue-700">{{ $review->replies->where('is_official', true)->count() }}</strong>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Replies Section -->
            <div class="p-6">
                @php
                    // Chỉ lấy replies từ manager (is_official = true)
                    $managerReplies = $review->replies->where('is_official', true);
                    $managerRepliesCount = $managerReplies->count();
                @endphp
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center space-x-3">
                        <div class="flex items-center justify-center w-10 h-10 bg-blue-600 rounded-full">
                            <i class="fas fa-comments text-white"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900" id="replyTitle">
                                Phản hồi từ chi nhánh
                            </h3>
                            <p class="text-sm text-gray-600">Quản lý các phản hồi chính thức từ chi nhánh</p>
                        </div>
                    </div>
                </div>
                
                <!-- Existing Manager Replies Only -->
                <div class="space-y-4 mb-6" id="repliesContainer">
                    @if($managerRepliesCount > 0)
                        @foreach($managerReplies as $reply)
                            <div class="reply-official p-4 rounded-lg" data-reply-id="{{ $reply->id }}">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-8 w-8 reply-avatar rounded-full flex items-center justify-center">
                                            <i class="fas fa-store text-white text-sm"></i>
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm font-medium text-gray-900">Chi nhánh</p>
                                            <p class="text-xs text-gray-500">{{ $formatDate($reply->reply_date) }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            Phản hồi chính thức
                                        </span>
                                        <button onclick="deleteReply({{ $reply->id }})" 
                                                class="text-red-600 hover:text-red-800 hover:bg-red-50 p-1 rounded transition-colors delete-btn" 
                                                title="Xóa phản hồi">
                                            <i class="fas fa-trash text-xs"></i>
                                        </button>
                                    </div>
                                </div>
                                <p class="text-gray-900">{{ $reply->reply }}</p>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center text-gray-500 py-8" id="noRepliesMessage">
                            <i class="fas fa-comments text-4xl mb-2 opacity-50"></i>
                            <p>Chưa có phản hồi nào từ chi nhánh</p>
                        </div>
                    @endif
                </div>

                <!-- Reply Form -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h4 class="text-md font-medium text-gray-900 mb-3">Phản hồi bình luận</h4>
                    <form id="replyForm" data-review-id="{{ $review->id }}">
                        @csrf
                        <div class="mb-4">
                            <textarea name="reply" id="replyTextarea" rows="4" 
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                                      placeholder="Nhập phản hồi của bạn..." required></textarea>
                            <div id="replyError" class="mt-1 text-sm text-red-600 hidden"></div>
                        </div>
                        <div class="flex justify-end">
                            <button type="submit" id="replySubmitBtn" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                                <i class="fas fa-reply mr-2"></i><span id="replyBtnText">Gửi phản hồi</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>


    </div>
</div>


@endsection

@push('styles')
<style>
/* Review Content Styling */
.review-content {
    background: linear-gradient(135deg, #fef3c7 0%, #fed7aa 100%);
    border-left: 4px solid #f59e0b;
    transition: all 0.3s ease;
}

.review-content:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 20px rgba(245, 158, 11, 0.15);
}

/* Reply Section Header */
#replyTitle {
    background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

#replyCount {
    display: inline-block;
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    color: white;
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 0.875rem;
    font-weight: bold;
    min-width: 24px;
    text-align: center;
    transition: all 0.3s ease;
}

#replyCount:hover {
    transform: scale(1.1);
    box-shadow: 0 2px 8px rgba(59, 130, 246, 0.3);
}

/* Reply Styling */
.reply-official {
    background: linear-gradient(135deg, #ffffff 0%, #f8fafc 50%, #e2e8f0 100%);
    border: 1px solid #e2e8f0;
    border-left: 5px solid #3b82f6;
    border-radius: 16px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
    backdrop-filter: blur(10px);
}

.reply-official::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, #3b82f6 0%, #1d4ed8 50%, #0ea5e9 100%);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.reply-official:hover::before {
    opacity: 1;
}

.reply-official::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, rgba(59, 130, 246, 0.02) 0%, rgba(29, 78, 216, 0.05) 100%);
    opacity: 0;
    transition: opacity 0.3s ease;
    pointer-events: none;
}

.reply-official:hover::after {
    opacity: 1;
}

.reply-official:hover {
    transform: translateY(-4px) scale(1.01);
    box-shadow: 0 12px 32px rgba(59, 130, 246, 0.15), 0 4px 16px rgba(0, 0, 0, 0.08);
    border-color: #3b82f6;
    background: linear-gradient(135deg, #ffffff 0%, #f0f9ff 50%, #dbeafe 100%);
}

.reply-official .reply-avatar {
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 50%, #0ea5e9 100%);
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.25);
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    border: 2px solid rgba(255, 255, 255, 0.2);
    position: relative;
    overflow: hidden;
}

.reply-official .reply-avatar::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.3), transparent);
    transform: rotate(45deg);
    transition: all 0.6s ease;
    opacity: 0;
}

.reply-official:hover .reply-avatar {
    transform: scale(1.15) rotate(5deg);
    box-shadow: 0 6px 20px rgba(59, 130, 246, 0.4);
}

.reply-official:hover .reply-avatar::before {
    opacity: 1;
    animation: shimmer 1.5s ease-in-out;
}

@keyframes shimmer {
    0% { transform: translateX(-100%) translateY(-100%) rotate(45deg); }
    50% { transform: translateX(0%) translateY(0%) rotate(45deg); }
    100% { transform: translateX(100%) translateY(100%) rotate(45deg); }
}

/* Reply content styling */
.reply-official p {
    color: #1f2937;
    line-height: 1.6;
    font-weight: 500;
    transition: color 0.3s ease;
}

.reply-official:hover p {
    color: #111827;
}

/* Badge styling */
.reply-official .bg-blue-100 {
    background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%) !important;
    border: 1px solid rgba(59, 130, 246, 0.2);
    transition: all 0.3s ease;
}

.reply-official:hover .bg-blue-100 {
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%) !important;
    color: white !important;
    transform: scale(1.05);
    box-shadow: 0 2px 8px rgba(59, 130, 246, 0.3);
}

/* Delete button styling */
.reply-official .delete-btn {
    opacity: 0;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    background: linear-gradient(135deg, rgba(239, 68, 68, 0.1) 0%, rgba(220, 38, 38, 0.1) 100%);
    border-radius: 8px;
    border: 1px solid rgba(239, 68, 68, 0.2);
    position: relative;
    overflow: hidden;
}

.reply-official .delete-btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
    transition: left 0.5s ease;
}

.reply-official:hover .delete-btn {
    opacity: 1;
    transform: scale(1.1) rotate(-2deg);
    box-shadow: 0 4px 12px rgba(239, 68, 68, 0.25);
}

.reply-official .delete-btn:hover {
    background: linear-gradient(135deg, rgba(239, 68, 68, 0.2) 0%, rgba(220, 38, 38, 0.2) 100%);
    transform: scale(1.15) rotate(2deg);
    border-color: rgba(239, 68, 68, 0.4);
    box-shadow: 0 6px 16px rgba(239, 68, 68, 0.35);
}

.reply-official .delete-btn:hover::before {
    left: 100%;
}

.reply-official .delete-btn:active {
    transform: scale(0.95);
    transition: transform 0.1s ease;
}

/* Date styling */
.reply-official .text-xs.text-gray-500 {
    color: #6b7280;
    font-weight: 500;
    transition: color 0.3s ease;
}

.reply-official:hover .text-xs.text-gray-500 {
    color: #4b5563;
}

/* Name styling */
.reply-official .text-sm.font-medium {
    background: linear-gradient(135deg, #1f2937 0%, #374151 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    font-weight: 600;
    transition: all 0.3s ease;
}

.reply-official:hover .text-sm.font-medium {
    background: linear-gradient(135deg, #111827 0%, #1f2937 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

/* Stats styling */
.reply-stats {
    background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
    border: 1px solid #0ea5e9;
    border-radius: 8px;
    padding: 8px 12px;
}

/* No replies message styling */
#noRepliesMessage {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    border: 2px dashed #cbd5e1;
    border-radius: 12px;
    transition: all 0.3s ease;
}

#noRepliesMessage:hover {
    border-color: #94a3b8;
    background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
}

/* Reply form styling */
#replyForm {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    transition: all 0.3s ease;
}

#replyForm:hover {
    border-color: #3b82f6;
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.1);
}

#replyTextarea {
    border-radius: 8px;
    transition: all 0.3s ease;
}

#replyTextarea:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

#replySubmitBtn {
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    transition: all 0.3s ease;
    border-radius: 8px;
}

#replySubmitBtn:hover:not(:disabled) {
    background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
}

/* Animation for new replies */
@keyframes slideInFromRight {
    0% {
        opacity: 0;
        transform: translateX(100px) scale(0.8);
    }
    50% {
        opacity: 0.7;
        transform: translateX(-10px) scale(1.05);
    }
    100% {
        opacity: 1;
        transform: translateX(0) scale(1);
    }
}

@keyframes pulseGlow {
    0%, 100% {
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
    }
    50% {
        box-shadow: 0 8px 25px rgba(59, 130, 246, 0.2);
    }
}

.reply-official.new-reply {
    animation: slideInFromRight 0.6s cubic-bezier(0.4, 0, 0.2, 1), pulseGlow 2s ease-in-out;
}

/* Fade out animation for deleted replies */
@keyframes fadeOutLeft {
    0% {
        opacity: 1;
        transform: translateX(0) scale(1);
    }
    100% {
        opacity: 0;
        transform: translateX(-100px) scale(0.8);
    }
}

.reply-official.deleting {
    animation: fadeOutLeft 0.4s ease-in-out forwards;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Realtime updates for review replies
    if (typeof Pusher !== 'undefined') {
        try {
            const pusher = new Pusher('{{ env('PUSHER_APP_KEY') }}', {
                cluster: '{{ env('PUSHER_APP_CLUSTER') }}',
                encrypted: true
            });

            const reviewId = {{ $review->id }};
            const branchId = {{ auth('manager')->user()->branch->id ?? 0 }};
            
            // Subscribe to both channels
            const replyChannel = pusher.subscribe('review-replies.' + reviewId);
            const branchChannel = pusher.subscribe('branch-reviews.' + branchId);

            // Listen for new replies (from other users/sessions)
            replyChannel.bind('new-reply', function(data) {
                console.log('New reply received from realtime:', data);
                console.log('Reply is_official:', data.reply?.is_official);
                console.log('Reply user_id:', data.reply?.user_id);
                console.log('Current user_id:', {{ auth('manager')->user()->id ?? 0 }});
                
                // Chỉ hiển thị replies từ manager khác (không phải user hiện tại)
                // Và chỉ hiển thị nếu reply chưa tồn tại trong DOM (tránh duplicate)
                if (data.reply && data.reply.is_official) {
                    // Skip if this reply was already added locally
                    if (locallyAddedReplies && locallyAddedReplies.has(data.reply.id)) {
                        console.log('Skipping reply - already added locally');
                        return;
                    }
                    
                    console.log('Processing official reply from realtime...');
                    const existingReply = document.querySelector(`[data-reply-id="${data.reply.id}"]`);
                    console.log('Existing reply found:', existingReply);
                    
                    if (!existingReply) {
                        const repliesContainer = document.getElementById('repliesContainer');
                        console.log('Replies container found:', repliesContainer);
                        
                        if (repliesContainer) {
                            // Remove "no replies" message if exists
                            const noRepliesMessage = document.getElementById('noRepliesMessage');
                            if (noRepliesMessage) {
                                noRepliesMessage.remove();
                            }
                            
                            const replyHtml = `
                                <div class="reply-official p-4 rounded-lg" data-reply-id="${data.reply.id}">
                                    <div class="flex items-center justify-between mb-2">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-8 w-8 reply-avatar rounded-full flex items-center justify-center">
                                                <i class="fas fa-store text-white text-sm"></i>
                                            </div>
                                            <div class="ml-3">
                                                <p class="text-sm font-medium text-gray-900">Chi nhánh</p>
                                                <p class="text-xs text-gray-500">${new Date(data.reply.reply_date).toLocaleString('vi-VN')}</p>
                                            </div>
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                Phản hồi chính thức
                                            </span>
                                            <button onclick="deleteReply(${data.reply.id})" 
                                                    class="text-red-600 hover:text-red-800 hover:bg-red-50 p-1 rounded transition-colors delete-btn" 
                                                    title="Xóa phản hồi">
                                                <i class="fas fa-trash text-xs"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <p class="text-gray-900">${data.reply.reply}</p>
                                </div>
                            `;
                            repliesContainer.insertAdjacentHTML('beforeend', replyHtml);
                            
                            // Update reply count
                            const replyCountElement = document.querySelector('h3');
                            if (replyCountElement) {
                                // Count actual reply elements (exclude no-replies message)
                                const replyElements = repliesContainer.querySelectorAll('[data-reply-id]');
                                const currentCount = replyElements.length;
                                replyCountElement.textContent = `Phản hồi từ chi nhánh (${currentCount})`;
                            }
                            
                            // Update total reply count in review content
                            updateReplyStats();
                            
                            // Show notification for realtime updates from other users
                            if (typeof dtmodalShowToast === 'function') {
                                dtmodalShowToast('notification', {
                                    title: 'Phản hồi mới',
                                    message: 'Có phản hồi mới từ chi nhánh khác!'
                                });
                            }
                        }
                    }
                }
            });

        } catch (error) {
            console.error('Pusher setup error:', error);
        }
    }
});

// Function to delete reply
function deleteReply(replyId) {
    if (typeof dtmodalCreateModal === 'function') {
        dtmodalCreateModal({
            type: 'warning',
            title: 'Xác nhận xóa phản hồi',
            message: 'Bạn có chắc chắn muốn xóa phản hồi này? Hành động này không thể hoàn tác.',
            confirmText: 'Xóa',
            cancelText: 'Hủy',
            onConfirm: function() {
                performDeleteReply(replyId);
            }
        });
    } else {
        // Fallback to confirm dialog
        if (confirm('Bạn có chắc chắn muốn xóa phản hồi này?')) {
            performDeleteReply(replyId);
        }
    }
}

// Function to perform the actual delete
function performDeleteReply(replyId) {
    fetch(`/branch/reviews/reply/${replyId}`, {
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
            // Remove reply from DOM
            const replyElement = document.querySelector(`[data-reply-id="${replyId}"]`);
            if (replyElement) {
                replyElement.remove();
            }
            
            // Update reply count
            const repliesContainer = document.getElementById('repliesContainer');
            const replyCountElement = document.querySelector('h3');
            if (replyCountElement && repliesContainer) {
                // Count actual reply elements (exclude no-replies message)
                const replyElements = repliesContainer.querySelectorAll('[data-reply-id]');
                const currentCount = replyElements.length;
                replyCountElement.textContent = `Phản hồi từ chi nhánh (${currentCount})`;
                
                // Show "no replies" message if no replies left
                if (currentCount === 0) {
                    const noRepliesHtml = `
                        <div class="text-center text-gray-500 py-8" id="noRepliesMessage">
                            <i class="fas fa-comments text-4xl mb-2 opacity-50"></i>
                            <p>Chưa có phản hồi nào từ chi nhánh</p>
                        </div>
                    `;
                    repliesContainer.insertAdjacentHTML('beforeend', noRepliesHtml);
                }
            }
            
            // Update total reply count in review content
            updateReplyStats();
            
            // Show notification
            if (typeof dtmodalShowToast === 'function') {
                dtmodalShowToast('success', {
                    title: 'Thành công',
                    message: 'Xóa phản hồi thành công!'
                });
            }
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
                message: 'Có lỗi xảy ra khi xóa phản hồi!'
            });
        } else {
            alert('Có lỗi xảy ra!');
        }
    });
}

// Function to update reply statistics
function updateReplyStats() {
    // Update the branch reply count in review content stats
    const repliesContainer = document.getElementById('repliesContainer');
    const branchReplyCountElement = document.querySelector('.reply-stats strong:last-child');
    
    if (repliesContainer && branchReplyCountElement) {
        // Count actual reply elements (exclude no-replies message)
        const replyElements = repliesContainer.querySelectorAll('[data-reply-id]');
        const currentBranchCount = replyElements.length;
        branchReplyCountElement.textContent = currentBranchCount;
    }
}

// Handle reply form submission with AJAX
document.addEventListener('DOMContentLoaded', function() {
    const replyForm = document.getElementById('replyForm');
    const replyTextarea = document.getElementById('replyTextarea');
    const replySubmitBtn = document.getElementById('replySubmitBtn');
    const replyBtnText = document.getElementById('replyBtnText');
    const replyError = document.getElementById('replyError');
    
    // Track replies added by current user to avoid duplicates
    let locallyAddedReplies = new Set();
    
    if (replyForm) {
        replyForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const reviewId = this.dataset.reviewId;
            const replyContent = replyTextarea.value.trim();
            
            if (!replyContent) {
                showReplyError('Vui lòng nhập nội dung phản hồi');
                return;
            }
            
            // Disable form
            replySubmitBtn.disabled = true;
            replyBtnText.textContent = 'Đang gửi...';
            hideReplyError();
            
            // Send AJAX request
            fetch(`/branch/reviews/${reviewId}/reply`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    reply: replyContent
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Track this reply as locally added
                    locallyAddedReplies.add(data.reply.id);
                    
                    // Add new reply to DOM immediately (don't wait for Pusher)
                    addReplyToDOM(data.reply);
                    
                    // Clear form
                    replyTextarea.value = '';
                    
                    // Update counts
                    updateReplyCounts();
                    updateReplyStats();
                    
                    // Show success notification
                    if (typeof dtmodalShowToast === 'function') {
                        dtmodalShowToast('success', {
                            title: 'Thành công',
                            message: data.message || 'Phản hồi đã được gửi thành công!'
                        });
                    }
                } else {
                    showReplyError(data.message || 'Có lỗi xảy ra khi gửi phản hồi');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showReplyError('Có lỗi xảy ra khi gửi phản hồi');
            })
            .finally(() => {
                // Re-enable form
                replySubmitBtn.disabled = false;
                replyBtnText.textContent = 'Gửi phản hồi';
            });
        });
    }
    
    function showReplyError(message) {
        replyError.textContent = message;
        replyError.classList.remove('hidden');
    }
    
    function hideReplyError() {
        replyError.classList.add('hidden');
    }
    
    function addReplyToDOM(reply) {
        console.log('Adding reply to DOM:', reply);
        const repliesContainer = document.getElementById('repliesContainer');
        console.log('Local replies container found:', repliesContainer);
        
        if (repliesContainer) {
            // Remove "no replies" message if exists
            const noRepliesMessage = document.getElementById('noRepliesMessage');
            if (noRepliesMessage) {
                noRepliesMessage.remove();
            }
            const replyHtml = `
                <div class="reply-official p-4 rounded-lg" data-reply-id="${reply.id}">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-8 w-8 reply-avatar rounded-full flex items-center justify-center">
                                <i class="fas fa-store text-white text-sm"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900">Chi nhánh</p>
                                <p class="text-xs text-gray-500">${new Date(reply.reply_date).toLocaleString('vi-VN')}</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                Phản hồi chính thức
                            </span>
                            <button onclick="deleteReply(${reply.id})" 
                                    class="text-red-600 hover:text-red-800 hover:bg-red-50 p-1 rounded transition-colors delete-btn" 
                                    title="Xóa phản hồi">
                                <i class="fas fa-trash text-xs"></i>
                            </button>
                        </div>
                    </div>
                    <p class="text-gray-900">${reply.reply}</p>
                </div>
            `;
            repliesContainer.insertAdjacentHTML('beforeend', replyHtml);
        }
    }
    
    function updateReplyCounts() {
        const repliesContainer = document.getElementById('repliesContainer');
        const replyCountElement = document.getElementById('replyCount');
        
        if (replyCountElement && repliesContainer) {
            // Count actual reply elements (exclude no-replies message)
            const replyElements = repliesContainer.querySelectorAll('[data-reply-id]');
            const currentCount = replyElements.length;
            replyCountElement.textContent = currentCount;
            
            // Add animation effect
            replyCountElement.style.transform = 'scale(1.2)';
            setTimeout(() => {
                replyCountElement.style.transform = 'scale(1)';
            }, 200);
        }
    }
});
</script>
@endpush