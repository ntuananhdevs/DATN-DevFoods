@extends('layouts.driver.masterLayout')

@section('title', 'Thông báo - Ứng dụng Tài xế')

@section('content')
<div class="p-4 md:p-6">
    <h1 class="text-2xl font-bold text-gray-900 mb-6">Thông báo</h1>

    <!-- Notifications -->
    <div class="bg-white rounded-lg shadow-md mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM4 19h10a2 2 0 002-2V7a2 2 0 00-2-2H4a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        Thông báo
                    </h2>
                    <p class="text-sm text-gray-600">
                        @if($unreadCount > 0)
                            Bạn có {{ $unreadCount }} thông báo chưa đọc.
                        @else
                            Không có thông báo mới.
                        @endif
                    </p>
                </div>
                @if($unreadCount > 0)
                <button onclick="markAllAsRead()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors text-sm">
                    Đọc tất cả
                </button>
                @endif
            </div>
        </div>
        
        <div class="max-h-96 overflow-y-auto">
            @if(count($notifications) > 0)
                @foreach($notifications as $notification)
                <div class="notification-item p-4 border-b border-gray-100 last:border-b-0 {{ !$notification['read'] ? 'notification-unread' : '' }}" data-notification="{{ $notification['id'] }}">
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0 mt-1">
                            @if($notification['type'] === 'new_order')
                                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            @elseif($notification['type'] === 'status_update')
                                <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            @elseif($notification['type'] === 'system_message')
                                <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                    </svg>
                                </div>
                            @elseif($notification['type'] === 'earning_report')
                                <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                    </svg>
                                </div>
                            @else
                                <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM4 19h10a2 2 0 002-2V7a2 2 0 00-2-2H4a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                            @endif
                        </div>
                        
                        <div class="flex-1 min-w-0">
                            <h4 class="text-sm font-semibold text-gray-900 {{ !$notification['read'] ? 'font-bold' : '' }}">
                                {{ $notification['title'] }}
                            </h4>
                            <p class="text-sm text-gray-600 mt-1">{{ $notification['message'] }}</p>
                            <p class="text-xs text-gray-500 mt-2">{{ $notification['timestamp'] }}</p>
                            
                            <div class="flex items-center space-x-3 mt-3">
                                @if(isset($notification['link']))
                                    <a href="{{ $notification['link'] }}" class="text-xs text-blue-600 hover:text-blue-800 font-medium">
                                        Xem chi tiết
                                    </a>
                                @endif
                                @if(!$notification['read'])
                                    <button onclick="markNotificationAsRead('{{ $notification['id'] }}')" class="text-xs text-gray-600 hover:text-gray-800">
                                        Đánh dấu đã đọc
                                    </button>
                                @endif
                                <button onclick="deleteNotification('{{ $notification['id'] }}')" class="text-xs text-red-600 hover:text-red-800">
                                    Xóa
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            @else
                <div class="p-12 text-center">
                    <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM4 19h10a2 2 0 002-2V7a2 2 0 00-2-2H4a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                    <p class="text-gray-500">Không có thông báo nào.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Feedback Form -->
    <div class="bg-white rounded-lg shadow-md mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                </svg>
                Gửi phản hồi & Báo cáo sự cố
            </h3>
            <p class="text-sm text-gray-600">Chúng tôi luôn lắng nghe ý kiến của bạn để cải thiện hệ thống.</p>
        </div>
        <div class="p-6">
            <form id="feedbackForm" data-ajax-form action="/driver/feedback" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="feedbackContent" class="block text-sm font-medium text-gray-700 mb-2">Nội dung phản hồi/sự cố</label>
                    <textarea name="content" id="feedbackContent" rows="5" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 form-input" placeholder="Mô tả chi tiết vấn đề bạn gặp phải hoặc ý kiến đóng góp..."></textarea>
                </div>
                <button type="submit" class="w-full bg-blue-600 text-white py-3 px-4 rounded-lg hover:bg-blue-700 transition-colors flex items-center justify-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                    </svg>
                    Gửi đi
                </button>
            </form>
        </div>
    </div>

    <!-- Support -->
    <div class="bg-white rounded-lg shadow-md">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192L5.636 18.364M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
                Hỗ trợ
            </h3>
            <p class="text-sm text-gray-600">Liên hệ với chúng tôi nếu bạn cần giúp đỡ.</p>
        </div>
        <div class="p-6">
            <div class="space-y-3">
                <a href="tel:1900xxxx" class="w-full bg-gray-100 text-gray-700 py-3 px-4 rounded-lg hover:bg-gray-200 transition-colors flex items-center justify-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                    </svg>
                    Gọi tổng đài hỗ trợ (1900 xxxx)
                </a>
                <button onclick="openChat()" class="w-full bg-gray-100 text-gray-700 py-3 px-4 rounded-lg hover:bg-gray-200 transition-colors flex items-center justify-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                    Chat với nhân viên hỗ trợ
                </button>
                <a href="#" class="w-full bg-gray-100 text-gray-700 py-3 px-4 rounded-lg hover:bg-gray-200 transition-colors flex items-center justify-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Câu hỏi thường gặp (FAQ)
                </a>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function markAllAsRead() {
    if (confirm('Đánh dấu tất cả thông báo là đã đọc?')) {
        fetch('/driver/notifications/mark-all-read', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            window.driverApp.showToast('Lỗi!', 'Không thể cập nhật thông báo.', 'error');
        });
    }
}

function openChat() {
    window.driverApp.showToast('Thông báo', 'Tính năng chat sẽ được triển khai sớm!', 'info');
}
</script>
@endpush
@endsection
