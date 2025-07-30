@foreach ($customerNotifications as $notification)
    @php
        $orderId = $notification->data['order_id'] ?? null;
        $orderCode = $notification->data['order_code'] ?? null;
        $conversationId = $notification->data['conversation_id'] ?? null;
        $isRead = $notification->read_at ? 'read' : '';
        
        // Determine notification type for display
        $notificationType = '';
        if (str_contains($notification->type, 'CustomerOrderSuccessNotification') || ($notification->data['type'] ?? null) === 'order_success') {
            $notificationType = 'order_success';
        } elseif (str_contains($notification->type, 'NewOrderNotification') || $orderId) {
            $notificationType = 'order';
        } elseif (str_contains($notification->type, 'OrderStatusNotification') || $orderCode) {
            $notificationType = 'order_status';
        } elseif (str_contains($notification->type, 'NewChatMessageNotification') || $conversationId) {
            $notificationType = 'chat';
        } elseif (($notification->data['type'] ?? null) === 'customer_new_review') {
            $notificationType = 'review';
        } elseif (($notification->data['type'] ?? null) === 'customer_review_report') {
            $notificationType = 'review_report';
        } else {
            $notificationType = 'general';
        }
    @endphp
    <div class="notification-item {{ $isRead }}" id="notification-item-{{ $notification->id }}"
        style="cursor:pointer;" data-notification-id="{{ $notification->id }}"
        @if($notificationType === 'chat')
            onclick="openCustomerChatWithConversation('{{ $conversationId }}', '{{ $notification->id }}')"
        @else
            onclick="markNotificationAsRead('{{ $notification->id }}')"
        @endif>
        <div class="noti-icon">
            @if ($notificationType === 'order_success')
                <ion-icon name="checkmark-circle" style="color: #10b981;"></ion-icon>
            @elseif ($notificationType === 'order')
                <ion-icon name="car" style="color: #10b981;"></ion-icon>
            @elseif ($notificationType === 'order_status')
                <ion-icon name="car" style="color: #f59e0b;"></ion-icon>
            @elseif ($notificationType === 'chat')
                <ion-icon name="chatbubble-ellipses" style="color: #3b82f6;"></ion-icon>
            @elseif ($notificationType === 'review')
                <ion-icon name="star" style="color: #8b5cf6;"></ion-icon>
            @elseif ($notificationType === 'review_report')
                <ion-icon name="flag" style="color: #ef4444;"></ion-icon>
            @else
                <ion-icon name="notifications" style="color: #6b7280;"></ion-icon>
            @endif
        </div>
        <div class="flex-1 min-w-0">
            <div class="noti-title">
                @if ($notificationType === 'order_success')
                    {{ $notification->data['message'] ?? 'Bạn đã đặt hàng thành công' }}
                @elseif ($notificationType === 'order')
                    {{ $notification->data['message'] ?? 'Đơn hàng mới' }}
                @elseif ($notificationType === 'order_status')
                    {{ $notification->data['title'] ?? $notification->data['message'] ?? 'Cập nhật đơn hàng' }}
                @elseif ($notificationType === 'chat')
                    {{ $notification->data['title'] ?? 'Tin nhắn mới từ ' . ($notification->data['sender_name'] ?? 'Hệ thống') }}
                @elseif ($notificationType === 'review')
                    {{ $notification->data['message'] ?? 'Có phản hồi bình luận mới' }}
                @elseif ($notificationType === 'review_report')
                    {{ $notification->data['message'] ?? 'Bình luận của bạn bị báo cáo' }}
                @else
                    {{ $notification->data['message'] ?? $notification->data['title'] ?? 'Thông báo mới' }}
                @endif
            </div>
            <div class="noti-body">
                @if ($notificationType === 'order_success')
                    @if ($notification->data['order_code'] ?? null)
                        Mã đơn hàng: {{ $notification->data['order_code'] }}
                    @endif
                    @if ($notification->data['branch_name'] ?? null)
                        - Chi nhánh: {{ $notification->data['branch_name'] }}
                    @endif
                    @if ($notification->data['total_amount'] ?? null)
                        - Tổng tiền: {{ number_format($notification->data['total_amount']) }}đ
                    @endif
                @elseif ($notificationType === 'order')
                    @if ($notification->data['branch_name'] ?? null)
                        Chi nhánh: {{ $notification->data['branch_name'] }}
                    @endif
                    @if ($notification->data['total_amount'] ?? null)
                        - Tổng tiền: {{ number_format($notification->data['total_amount']) }}đ
                    @endif
                @elseif ($notificationType === 'order_status')
                    @if ($notification->data['order_code'] ?? null)
                        Đơn hàng: {{ $notification->data['order_code'] }}
                    @endif
                    @if ($notification->data['branch_name'] ?? null)
                        - Chi nhánh: {{ $notification->data['branch_name'] }}
                    @endif
                @elseif ($notificationType === 'chat')
                    Nội dung: {{ $notification->data['content'] ?? '' }}
                @elseif ($notificationType === 'review')
                    ID bình luận: {{ $notification->data['review_id'] ?? '' }}
                @elseif ($notificationType === 'review_report')
                    ID bình luận: {{ $notification->data['review_id'] ?? '' }}
                @else
                    {{ $notification->data['content'] ?? $notification->data['body'] ?? '' }}
                @endif
            </div>
            <div class="noti-time">{{ $notification->created_at->diffForHumans() }}</div>
        </div>
        @if (!$notification->read_at)
            <span class="noti-dot"></span>
        @endif
    </div>
@endforeach
@if ($customerNotifications->isEmpty())
    <div class="text-center text-gray-400 py-8">Không có thông báo nào</div>
@endif
<script>
    function openCustomerChatWithConversation(conversationId, notificationId) {
        // Đánh dấu đã đọc notification bằng hàm từ header
        if (notificationId && typeof markNotificationAsRead === 'function') {
            markNotificationAsRead(notificationId);
        }
        // Mở chat widget và focus vào đúng conversation
        if (typeof window.openCustomerChatWidget === 'function') {
            window.openCustomerChatWidget(conversationId);
        } else {
            // fallback: click nút chat
            var btn = document.getElementById('chatToggleBtn');
            if (btn) btn.click();
            window.conversationId = conversationId;
        }
    }

    // Ngăn tạo div mới nếu đã có notification với id đó
    document.addEventListener('DOMContentLoaded', function() {
        const notiList = document.getElementById('customer-notification-list');
        if (!notiList) return;
        const items = notiList.querySelectorAll('.notification-item');
        const seen = new Set();
        items.forEach(item => {
            const id = item.getAttribute('data-notification-id');
            if (seen.has(id)) {
                item.remove(); // Xóa trùng
            } else {
                seen.add(id);
            }
        });
    });
</script>
