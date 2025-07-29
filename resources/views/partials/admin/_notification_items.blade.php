@php
    // Ưu tiên $adminNotifications, nếu không có thì dùng $notifications
    $notis = collect($adminNotifications ?? ($notifications ?? []));
@endphp

@if ($notis->isEmpty())
    <div class="text-center text-xs text-muted-foreground py-4">Không có thông báo nào</div>
@else
    @foreach ($notis as $notification)
        @php
            $orderId = $notification->data['order_id'] ?? null;
            $conversationId = $notification->data['conversation_id'] ?? null;
            if ($orderId) {
                $redirectUrl = route('admin.orders.index', ['order' => $orderId]);
            } elseif ($conversationId) {
                $redirectUrl = route('admin.chat.index', ['chat' => $conversationId]);
            } else {
                $redirectUrl = '#';
            }
        @endphp
        <div class="flex items-start gap-3 px-2 py-2 hover:bg-accent rounded-md {{ $notification->read_at ? '' : 'bg-primary/10 text-primary font-semibold' }}"
            id="notification-item-{{ $notification->id }}" style="cursor:pointer;"
            data-notification-id="{{ $notification->id }}"
            onclick="markNotificationAsRead('{{ $notification->id }}', '{{ $redirectUrl }}')">
            <div class="h-8 w-8 rounded-full bg-primary/10 flex items-center justify-center text-primary">
                @if ($orderId)
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" class="lucide lucide-shopping-cart">
                        <circle cx="8" cy="21" r="1" />
                        <circle cx="19" cy="21" r="1" />
                        <path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12" />
                    </svg>
                @elseif ($conversationId)
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" class="lucide lucide-message-circle">
                        <path
                            d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z" />
                    </svg>
                @endif
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium truncate">
                    {{ $notification->data['message'] ?? ($orderId ? 'Đơn hàng mới' : 'Tin nhắn mới') }}
                </p>
                @if ($orderId)
                    <p class="text-xs text-muted-foreground truncate">
                        Khách hàng: {{ $notification->data['customer_name'] ?? '' }}
                    </p>
                    <p class="text-xs text-muted-foreground truncate">
                        Chi nhánh: {{ $notification->data['branch_name'] ?? '' }}
                    </p>
                @elseif ($conversationId)
                    <p class="text-xs text-muted-foreground truncate">
                        Nội dung: {{ $notification->data['content'] ?? '' }}
                    </p>
                @endif
                <p class="text-xs text-muted-foreground">
                    {{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}
                </p>
            </div>
            @if (!$notification->read_at)
                <div class="w-2 h-2 rounded-full bg-primary mt-2"></div>
            @endif
        </div>
    @endforeach
@endif
