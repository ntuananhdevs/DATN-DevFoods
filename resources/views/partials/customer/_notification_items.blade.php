@foreach ($customerNotifications as $notification)
    @php
        $type = $notification->data['type'] ?? 'system'; // Mặc định là 'system' nếu không có
        $isRead = $notification->read_at ? 'read' : '';
        $message = $notification->data['message'] ?? 'Bạn có thông báo mới.';
        $url = $notification->data['url'] ?? '#';

        $iconHtml = '';
        switch ($type) {
            case 'review_liked':
                $iconHtml = '<ion-icon name="thumbs-up-outline" class="text-blue-500"></ion-icon>';
                break;
            case 'review_replied':
                $iconHtml = '<ion-icon name="chatbubble-ellipses-outline" class="text-green-500"></ion-icon>';
                break;
            case 'review_reported':
                $iconHtml = '<ion-icon name="alert-circle-outline" class="text-yellow-500"></ion-icon>';
                break;
            default: // system & chat
                $iconHtml = '<ion-icon name="notifications-outline"></ion-icon>';
                break;
        }

        // Tạo hành động onclick
        $onClickAction = "window.location.href = '{$url}'";
        if (!$notification->read_at) {
             // Nếu chưa đọc, thêm hành động markAsRead
            $onClickAction = "markNotificationAsRead('{$notification->id}', '{$url}')";
        }
    @endphp

    <div class="notification-item flex items-center gap-3 p-3 border-b {{ $isRead ? 'opacity-75' : 'font-semibold' }} hover:bg-gray-50 transition-colors duration-150"
        id="notification-item-{{ $notification->id }}" style="cursor:pointer;"
        data-notification-id="{{ $notification->id }}"
        onclick="{{ $onClickAction }}">

        <div class="noti-icon text-2xl">
            {!! $iconHtml !!}
        </div>
        <div class="flex-1 min-w-0">
            <div class="noti-body text-sm text-gray-800">
                {!! $message !!}
            </div>
            <div class="noti-time text-xs text-gray-500 mt-1">{{ $notification->created_at->diffForHumans() }}</div>
        </div>
        @if (!$notification->read_at)
            <span class="w-2 h-2 bg-blue-500 rounded-full noti-dot"></span>
        @endif
    </div>
@endforeach

@if ($customerNotifications->isEmpty())
    <div class="text-center text-gray-400 py-8">Không có thông báo nào</div>
@endif

<script>
    function markNotificationAsRead(notificationId, redirectUrl) {
        // Gửi yêu cầu đánh dấu đã đọc
        fetch(`/notifications/${notificationId}/read`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
        }).then(res => res.json()).then(data => {
            if (data.success) {
                // Xóa style chưa đọc và chuyển hướng
                const item = document.getElementById(`notification-item-${notificationId}`);
                if (item) {
                    item.classList.remove('font-semibold');
                    item.classList.add('opacity-75');
                    const dot = item.querySelector('.noti-dot');
                    if(dot) dot.remove();
                }
            }
        }).finally(() => {
            // Luôn chuyển hướng dù API có lỗi hay không
            if(redirectUrl && redirectUrl !== '#') {
                window.location.href = redirectUrl;
            }
        });
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
