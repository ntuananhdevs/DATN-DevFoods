@foreach ($branchNotifications ?? [] as $notification)
    @php
        $orderId = $notification->data['order_id'] ?? null;
        $orderCode = $notification->data['order_code'] ?? null;
        $conversationId = $notification->data['conversation_id'] ?? null;
        
        // Determine redirect URL based on notification type
        if ($orderId) {
            $redirectUrl = route('branch.orders.index', ['order' => $orderId]);
        } elseif ($orderCode) {
            $redirectUrl = route('branch.orders.index');
        } elseif ($conversationId) {
            $redirectUrl = route('branch.chat.index', ['chat' => $conversationId]);
        } elseif (($notification->data['type'] ?? null) === 'branch_new_review' || ($notification->data['type'] ?? null) === 'branch_review_reported') {
            $redirectUrl = $notification->data['url'] ?? '#';
        } else {
            $redirectUrl = '#';
        }
        
        // Determine notification type for display
        $notificationType = '';
        if (str_contains($notification->type, 'NewOrderNotification') || $orderId) {
            $notificationType = 'order';
        } elseif (str_contains($notification->type, 'OrderStatusNotification') || $orderCode) {
            $notificationType = 'order_status';
        } elseif (str_contains($notification->type, 'NewChatMessageNotification') || $conversationId) {
            $notificationType = 'chat';
        } elseif (($notification->data['type'] ?? null) === 'branch_new_review') {
            $notificationType = 'review';
        } elseif (($notification->data['type'] ?? null) === 'branch_review_reported') {
            $notificationType = 'review_report';
        } else {
            $notificationType = 'general';
        }
    @endphp
    <div class="flex items-start gap-3 px-2 py-2 hover:bg-accent rounded-md {{ $notification->read_at ? '' : 'bg-primary/10 text-primary font-semibold' }}"
        id="notification-item-{{ $notification->id }}" style="cursor:pointer;"
        data-notification-id="{{ $notification->id }}"
        onclick="markNotificationAsRead('{{ $notification->id }}', '{{ $redirectUrl }}')">
        <div class="h-8 w-8 rounded-full bg-primary/10 flex items-center justify-center text-primary">
            @if ($notificationType === 'order')
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="lucide lucide-shopping-cart">
                    <circle cx="8" cy="21" r="1" />
                    <circle cx="19" cy="21" r="1" />
                    <path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12" />
                </svg>
            @elseif ($notificationType === 'order_status')
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="lucide lucide-truck">
                    <path d="M14 18V6a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v11a1 1 0 0 0 1 1h2" />
                    <path d="M15 18H9" />
                    <path d="M19 18h2a1 1 0 0 0 1-1v-3.65a1 1 0 0 0-.22-.624l-3.48-4.35A1 1 0 0 0 17.52 8H14" />
                    <circle cx="17" cy="18" r="2" />
                    <circle cx="7" cy="18" r="2" />
                </svg>
            @elseif ($notificationType === 'chat')
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="lucide lucide-message-circle">
                    <path
                        d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z" />
                </svg>
            @elseif ($notificationType === 'review' || $notificationType === 'review_report')
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="lucide lucide-star">
                    <polygon points="12,2 15.09,8.26 22,9.27 17,14.14 18.18,21.02 12,17.77 5.82,21.02 7,14.14 2,9.27 8.91,8.26" />
                </svg>
            @else
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="lucide lucide-bell">
                    <path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"></path>
                    <path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"></path>
                </svg>
            @endif
        </div>
        <div class="flex-1">
            @if ($notificationType === 'order')
                <p class="text-sm font-medium">{{ $notification->data['message'] ?? 'Đơn hàng mới' }}</p>
                <p class="text-xs text-muted-foreground">Khách hàng: {{ $notification->data['customer_name'] ?? '' }}</p>
            @elseif ($notificationType === 'order_status')
                <p class="text-sm font-medium">{{ $notification->data['title'] ?? $notification->data['message'] ?? 'Cập nhật đơn hàng' }}</p>
                <p class="text-xs text-muted-foreground">
                    @if ($notification->data['order_code'] ?? null)
                        Đơn hàng: {{ $notification->data['order_code'] }}
                    @endif
                    @if ($notification->data['customer_name'] ?? null)
                        - {{ $notification->data['customer_name'] }}
                    @endif
                </p>
            @elseif ($notificationType === 'chat')
                <p class="text-sm font-medium">{{ $notification->data['message'] ?? 'Tin nhắn mới' }}</p>
                <p class="text-xs text-muted-foreground">Nội dung: {{ $notification->data['content'] ?? '' }}</p>
            @elseif ($notificationType === 'review')
                <p class="text-sm font-medium">{{ $notification->data['message'] ?? 'Có bình luận mới tại chi nhánh' }}</p>
                <p class="text-xs text-muted-foreground">ID bình luận: {{ $notification->data['review_id'] ?? '' }}</p>
            @elseif ($notificationType === 'review_report')
                <p class="text-sm font-medium">{{ $notification->data['message'] ?? 'Bình luận bị báo cáo tại chi nhánh' }}</p>
                <p class="text-xs text-muted-foreground">ID bình luận: {{ $notification->data['review_id'] ?? '' }}</p>
            @else
                <p class="text-sm font-medium">{{ $notification->data['message'] ?? $notification->data['title'] ?? 'Thông báo mới' }}</p>
                @if ($notification->data['customer_name'] ?? null)
                    <p class="text-xs text-muted-foreground">{{ $notification->data['customer_name'] }}</p>
                @endif
            @endif
            <p class="text-xs text-muted-foreground">
                {{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}</p>
        </div>
        @if (!$notification->read_at)
            <div class="w-2 h-2 rounded-full bg-primary mt-2"></div>
        @endif
    </div>
@endforeach
@if (($branchNotifications ?? collect())->isEmpty())
    <div class="text-center text-xs text-muted-foreground py-4">Không có thông báo nào</div>
@endif
