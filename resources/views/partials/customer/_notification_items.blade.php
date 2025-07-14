@foreach ($customerNotifications as $notification)
    @php
        $conversationId = $notification->data['conversation_id'] ?? null;
        $isRead = $notification->read_at ? 'read' : '';
    @endphp
    <div class="notification-item {{ $isRead }}" id="notification-item-{{ $notification->id }}"
        style="cursor:pointer;" data-notification-id="{{ $notification->id }}"
        onclick="openCustomerChatWithConversation('{{ $conversationId }}', '{{ $notification->id }}')">
        <div class="noti-icon">
            <ion-icon name="chatbubble-ellipses-outline"></ion-icon>
        </div>
        <div class="flex-1 min-w-0">
            <div class="noti-title">
                {{ $notification->data['title'] ?? 'Tin nhắn mới từ ' . ($notification->data['sender_name'] ?? 'Hệ thống') }}
            </div>
            <div class="noti-body">
                Nội dung: {{ $notification->data['content'] ?? '' }}
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

//aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa
