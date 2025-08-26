@extends('layouts.customer.fullLayoutMaster')

@section('content')
    <div class="container mx-auto py-4">
        <h2 class="text-2xl font-bold mb-6 text-primary">Tất cả thông báo</h2>
        <div class="space-y-4">
            @forelse($customerNotifications as $notification)
                @php
                    $orderId = $notification->data['order_id'] ?? null;
                    $orderCode = $notification->data['order_code'] ?? null;
                    $conversationId = $notification->data['conversation_id'] ?? null;
                    $isUnread = !$notification->read_at;
                    
                    // Determine notification type for display
                    $notificationType = '';
                    if (str_contains($notification->type, 'NewOrderNotification') || $orderId) {
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
                <div class="bg-white rounded-lg shadow-sm border p-4 hover:shadow-md transition-shadow duration-200 {{ $isUnread ? 'bg-primary/10' : '' }}">
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0">
                            @if ($notificationType === 'order')
                                <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center">
                                    <ion-icon name="bag-outline" class="text-green-600 text-lg"></ion-icon>
                                </div>
                            @elseif ($notificationType === 'order_status')
                                <div class="w-10 h-10 rounded-full bg-yellow-100 flex items-center justify-center">
                                    <ion-icon name="refresh-outline" class="text-yellow-600 text-lg"></ion-icon>
                                </div>
                            @elseif ($notificationType === 'chat')
                                <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center">
                                    <ion-icon name="chatbubble-ellipses-outline" class="text-blue-600 text-lg"></ion-icon>
                                </div>
                            @elseif ($notificationType === 'review')
                                <div class="w-10 h-10 rounded-full bg-purple-100 flex items-center justify-center">
                                    <ion-icon name="star-outline" class="text-purple-600 text-lg"></ion-icon>
                                </div>
                            @elseif ($notificationType === 'review_report')
                                <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center">
                                    <ion-icon name="flag-outline" class="text-red-600 text-lg"></ion-icon>
                                </div>
                            @else
                                <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center">
                                    <ion-icon name="notifications-outline" class="text-gray-600 text-lg"></ion-icon>
                                </div>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            @if ($notificationType === 'order')
                                <div class="font-semibold text-base text-primary">
                                    {{ $notification->data['message'] ?? 'Đơn hàng mới' }}</div>
                                <div class="text-xs text-muted-foreground mt-1">
                                    @if ($notification->data['branch_name'] ?? null)
                                        Chi nhánh: {{ $notification->data['branch_name'] }}
                                    @endif
                                    @if ($notification->data['total_amount'] ?? null)
                                        - Tổng tiền: {{ number_format($notification->data['total_amount']) }}đ
                                    @endif
                                </div>
                            @elseif ($notificationType === 'order_status')
                                <div class="font-semibold text-base text-primary">
                                    {{ $notification->data['title'] ?? $notification->data['message'] ?? 'Cập nhật đơn hàng' }}</div>
                                <div class="text-xs text-muted-foreground mt-1">
                                    @if ($notification->data['order_code'] ?? null)
                                        Đơn hàng: {{ $notification->data['order_code'] }}
                                    @endif
                                    @if ($notification->data['branch_name'] ?? null)
                                        - Chi nhánh: {{ $notification->data['branch_name'] }}
                                    @endif
                                </div>
                            @elseif ($notificationType === 'chat')
                                <div class="font-semibold text-base text-primary">
                                    {{ $notification->data['title'] ?? 'Tin nhắn mới từ ' . ($notification->data['sender_name'] ?? 'Hệ thống') }}</div>
                                <div class="text-xs text-muted-foreground mt-1">Nội dung:
                                    {{ $notification->data['content'] ?? '' }}</div>
                            @elseif ($notificationType === 'review')
                                <div class="font-semibold text-base text-primary">
                                    {{ $notification->data['message'] ?? 'Có phản hồi bình luận mới' }}</div>
                                <div class="text-xs text-muted-foreground mt-1">ID bình luận: {{ $notification->data['review_id'] ?? '' }}</div>
                            @elseif ($notificationType === 'review_report')
                                <div class="font-semibold text-base text-primary">
                                    {{ $notification->data['message'] ?? 'Bình luận của bạn bị báo cáo' }}</div>
                                <div class="text-xs text-muted-foreground mt-1">ID bình luận: {{ $notification->data['review_id'] ?? '' }}</div>
                            @else
                                <div class="font-semibold text-base text-primary">
                                    {{ $notification->data['message'] ?? $notification->data['title'] ?? 'Thông báo mới' }}</div>
                                <div class="text-xs text-muted-foreground mt-1">
                                    {{ $notification->data['content'] ?? $notification->data['body'] ?? '' }}</div>
                            @endif
                            <div class="text-xs text-muted-foreground mt-2">
                                {{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}</div>
                        </div>
                        @if ($isUnread)
                            <div class="w-2 h-2 rounded-full bg-primary mt-2"></div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="text-center text-gray-500 py-8">
                    <p>Không có thông báo nào</p>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if(method_exists($customerNotifications, 'links'))
            <div class="mt-6">
                {{ $customerNotifications->links() }}
            </div>
        @endif
    </div>
@endsection
