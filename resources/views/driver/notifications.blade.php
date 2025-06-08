@extends('layouts.driver.masterLayout')

@section('title', 'Thông báo')

@section('content')
    <div class="p-4 md:p-6 space-y-6">
        <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
            <div class="flex flex-row justify-between items-center p-4">
                <div>
                    <h3 class="text-2xl font-semibold leading-none tracking-tight flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2 h-5 w-5 text-primary"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/></svg> Thông báo
                    </h3>
                    <p id="unread-count-display" class="text-sm text-muted-foreground">
                        @if($unreadCount > 0)
                            Bạn có {{ $unreadCount }} thông báo chưa đọc.
                        @else
                            Không có thông báo mới.
                        @endif
                    </p>
                </div>
                @if($unreadCount > 0)
                    <button id="mark-all-read" class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-9 px-4 py-2">
                        Đọc tất cả
                    </button>
                @endif
            </div>
            <div id="notifications-list-container" class="p-4 pt-0 space-y-3 max-h-[400px] overflow-y-auto">
                @if(count($notifications) > 0)
                    @foreach($notifications as $notif)
                        @php
                            $iconClass = '';
                            $iconSvg = '';
                            switch ($notif['type']) {
                                case 'new_order': $iconSvg = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5 text-blue-500"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/></svg>'; break;
                                case 'status_update': $iconSvg = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5 text-green-500"><path d="M12 22c-5.523 0-10-4.477-10-10S6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z"/><path d="m9 12 2 2 4-4"/></svg>'; break;
                                case 'system_message': $iconSvg = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5 text-yellow-500"><path d="m21.73 18.73-1.41 1.41a2 2 0 0 1-2.83 0L12 13.83l-5.49 5.49a2 2 0 0 1-2.83 0L2.27 18.73A2 2 0 0 1 2.27 15.89L7.76 10.4l-5.49-5.49a2 2 0 0 1 0-2.83L5.27 2.27a2 2 0 0 1 2.83 0L12 7.17l5.49-5.49a2 2 0 0 1 2.83 0L21.73 5.11a2 2 0 0 1 0 2.83L16.24 13.3l5.49 5.49a2 2 0 0 1 0 2.83Z"/></svg>'; break;
                                case 'earning_report': $iconSvg = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5 text-purple-500"><line x1="12" x2="12" y1="2" y2="22"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>'; break;
                                default: $iconSvg = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5 text-gray-500"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/></svg>'; break;
                            }
                        @endphp
                        <div id="notification-{{ $notif['id'] }}" class="p-3 rounded-lg border flex items-start gap-3 transition-colors hover:bg-muted/50 {{ $notif['read'] ? 'bg-muted/30 border-transparent' : 'bg-background font-medium' }}">
                            <div class="flex-shrink-0 mt-1">{!! $iconSvg !!}</div>
                            <div class="flex-grow">
                                <p class="text-sm font-semibold">{{ $notif['title'] }}</p>
                                <p class="text-xs {{ $notif['read'] ? 'text-muted-foreground' : 'text-foreground/80' }}">
                                    {{ $notif['message'] }}
                                </p>
                                <p class="text-xs text-muted-foreground mt-0.5">
                                    {{ \Carbon\Carbon::parse($notif['timestamp'])->locale('vi')->isoFormat('HH:mm DD/MM/YYYY') }}
                                </p>
                                <div class="mt-1.5 space-x-2">
                                    @if(isset($notif['link']) && !empty($notif['link']))
                                        <a href="{{ $notif['link'] }}" class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 text-primary underline-offset-4 hover:underline h-auto p-0 text-xs">
                                            Xem chi tiết
                                        </a>
                                    @endif
                                    @if(!$notif['read'])
                                        <button type="button" onclick="markAsRead('{{ $notif['id'] }}')" class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 text-primary underline-offset-4 hover:underline h-auto p-0 text-xs">
                                            Đánh dấu đã đọc
                                        </button>
                                    @endif
                                    <button type="button" onclick="deleteNotification('{{ $notif['id'] }}')" class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 text-destructive underline-offset-4 hover:underline h-auto p-0 text-xs">
                                        Xóa
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <p class="text-muted-foreground text-center py-8">Không có thông báo nào.</p>
                @endif
            </div>
        </div>

        <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
            <div class="flex flex-col space-y-1.5 p-4">
                <h3 class="text-2xl font-semibold leading-none tracking-tight flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2 h-5 w-5 text-primary"><path d="M7.9 20A9 9 0 1 0 4 16.1L2 22Z"/></svg> Gửi phản hồi & Báo cáo sự cố
                </h3>
                <p class="text-sm text-muted-foreground">Chúng tôi luôn lắng nghe ý kiến của bạn để cải thiện hệ thống.</p>
            </div>
            <div class="p-4 pt-0 space-y-4">
                <div>
                    <label for="feedback" class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">Nội dung phản hồi/sự cố</label>
                    <textarea id="feedback" placeholder="Mô tả chi tiết vấn đề bạn gặp phải hoặc ý kiến đóng góp..." rows="5" class="flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 mt-1"></textarea>
                </div>
                <button id="submit-feedback" class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-primary text-primary-foreground hover:bg-primary/90 h-10 px-4 py-2 w-full">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2 h-4 w-4"><path d="m22 2-7 20-4-9-9-4 20-7Z"/><path d="M22 2 11 13"/></svg> Gửi đi
                </button>
            </div>
        </div>

        <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
            <div class="flex flex-col space-y-1.5 p-4">
                <h3 class="text-2xl font-semibold leading-none tracking-tight flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2 h-5 w-5 text-primary"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><path d="M12 17h.01"/></svg> Hỗ trợ
                </h3>
                <p class="text-sm text-muted-foreground">Liên hệ với chúng tôi nếu bạn cần giúp đỡ.</p>
            </div>
            <div class="p-4 pt-0 space-y-3">
                <button class="inline-flex items-center justify-start whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-10 px-4 py-2 w-full">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2 h-4 w-4"><path d="M22 16.92v3a2 2 0 0 1-2.18 2.02 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.63A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-1.18 2.19l-.7.69a12 12 0 0 0 6.06 6.06l.69-.7a2 2 0 0 1 2.19-1.18 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg> Gọi tổng đài hỗ trợ (1900 xxxx)
                </button>
                <button class="inline-flex items-center justify-start whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-10 px-4 py-2 w-full">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2 h-4 w-4"><path d="M7.9 20A9 9 0 1 0 4 16.1L2 22Z"/></svg> Chat với nhân viên hỗ trợ
                </button>
                <button class="inline-flex items-center justify-start whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-10 px-4 py-2 w-full">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2 h-4 w-4"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><path d="M12 17h.01"/></svg> Câu hỏi thường gặp (FAQ)
                </button>
            </div>
        </div>
    </div>
@endsection

@section('page_scripts')
    <script>
        // JavaScript functions for notifications (mark as read, delete, submit feedback)
        // These will now interact with the DOM directly and potentially send AJAX requests
        // to update the backend (MockDriverData in this case).

        function markAsRead(id) {
            // Simulate API call to update status
            fetch('/driver/notifications/' + id + '/mark-read', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const notifElement = document.getElementById('notification-' + id);
                    if (notifElement) {
                        notifElement.classList.remove('bg-background', 'font-medium');
                        notifElement.classList.add('bg-muted/30', 'border-transparent');
                        const textElements = notifElement.querySelectorAll('.text-foreground\\/80');
                        textElements.forEach(el => {
                            el.classList.remove('text-foreground/80');
                            el.classList.add('text-muted-foreground');
                        });
                        // Remove "Đánh dấu đã đọc" button
                        const markReadButton = notifElement.querySelector('button[onclick*="markAsRead"]');
                        if (markReadButton) markReadButton.remove();
                        updateUnreadCount();
                    }
                } else {
                    alert('Lỗi: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Đã xảy ra lỗi khi đánh dấu đã đọc.');
            });
        }

        function deleteNotification(id) {
            if (!confirm('Bạn có chắc chắn muốn xóa thông báo này không?')) {
                return;
            }
            fetch('/driver/notifications/' + id, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const notifElement = document.getElementById('notification-' + id);
                    if (notifElement) {
                        notifElement.remove();
                        updateUnreadCount();
                    }
                } else {
                    alert('Lỗi: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Đã xảy ra lỗi khi xóa thông báo.');
            });
        }

        function markAllAsRead() {
            fetch('/driver/notifications/mark-all-read', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.querySelectorAll('#notifications-list-container > div').forEach(notifElement => {
                        notifElement.classList.remove('bg-background', 'font-medium');
                        notifElement.classList.add('bg-muted/30', 'border-transparent');
                        const textElements = notifElement.querySelectorAll('.text-foreground\\/80');
                        textElements.forEach(el => {
                            el.classList.remove('text-foreground/80');
                            el.classList.add('text-muted-foreground');
                        });
                        const markReadButton = notifElement.querySelector('button[onclick*="markAsRead"]');
                        if (markReadButton) markReadButton.remove();
                    });
                    updateUnreadCount();
                } else {
                    alert('Lỗi: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Đã xảy ra lỗi khi đánh dấu tất cả đã đọc.');
            });
        }

        function handleSubmitFeedback() {
            const feedbackTextarea = document.getElementById('feedback');
            const feedbackContent = feedbackTextarea.value.trim();

            if (feedbackContent === "") {
                alert('Vui lòng nhập nội dung phản hồi.');
                return;
            }

            fetch('/driver/feedback', { // You'll need to define this route
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ feedback: feedbackContent })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Gửi phản hồi thành công! Cảm ơn bạn đã đóng góp ý kiến.');
                    feedbackTextarea.value = ''; // Clear textarea
                } else {
                    alert('Lỗi: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Đã xảy ra lỗi khi gửi phản hồi.');
            });
        }

        function updateUnreadCount() {
            const unreadNotifications = document.querySelectorAll('#notifications-list-container > div:not(.bg-muted\\/30)');
            const unreadCount = unreadNotifications.length;
            const unreadCountDisplay = document.getElementById('unread-count-display');
            const markAllReadButton = document.getElementById('mark-all-read');

            if (unreadCountDisplay) {
                if (unreadCount > 0) {
                    unreadCountDisplay.textContent = `Bạn có ${unreadCount} thông báo chưa đọc.`;
                    if (markAllReadButton) markAllReadButton.style.display = 'inline-flex';
                } else {
                    unreadCountDisplay.textContent = 'Không có thông báo mới.';
                    if (markAllReadButton) markAllReadButton.style.display = 'none';
                }
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const markAllReadButton = document.getElementById('mark-all-read');
            if (markAllReadButton) {
                markAllReadButton.addEventListener('click', markAllAsRead);
            }

            const submitFeedbackButton = document.getElementById('submit-feedback');
            if (submitFeedbackButton) {
                submitFeedbackButton.addEventListener('click', handleSubmitFeedback);
            }

            updateUnreadCount(); // Initial count update on load
        });
    </script>
@endsection