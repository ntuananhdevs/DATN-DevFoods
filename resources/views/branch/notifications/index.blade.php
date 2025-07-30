@extends('layouts.branch.contentLayoutMaster')

    @section('content')
        <div class="container mx-auto py-4">
            <h2 class="text-2xl font-bold mb-6 text-primary">Tất cả thông báo</h2>
            <div class="space-y-4">
                @forelse($notifications as $notification)
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
                        } elseif (($notification->data['type'] ?? null) === 'branch_new_review') {
                            $notificationType = 'review';
                        } elseif (($notification->data['type'] ?? null) === 'branch_review_reported') {
                            $notificationType = 'review_report';
                        } else {
                            $notificationType = 'general';
                        }
                    @endphp
                    <div
                        class="flex items-start gap-4 rounded-lg border bg-white shadow-sm p-4 transition hover:shadow-md {{ $isUnread ? 'bg-primary/10 border-primary' : '' }}">
                        <div class="flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-primary/20">
                            @if ($notificationType === 'order')
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-blue-500" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <circle cx="8" cy="21" r="1" />
                                    <circle cx="19" cy="21" r="1" />
                                    <path
                                        d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12" />
                                </svg>
                            @elseif ($notificationType === 'order_status')
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-green-500" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path d="M14 18V6a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v11a1 1 0 0 0 1 1h2" />
                                    <path d="M15 18H9" />
                                    <path d="M19 18h2a1 1 0 0 0 1-1v-3.65a1 1 0 0 0-.22-.624l-3.48-4.35A1 1 0 0 0 17.52 8H14" />
                                    <circle cx="17" cy="18" r="2" />
                                    <circle cx="7" cy="18" r="2" />
                                </svg>
                            @elseif ($notificationType === 'chat')
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-blue-500" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path
                                        d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z" />
                                </svg>
                            @elseif ($notificationType === 'review' || $notificationType === 'review_report')
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-yellow-500" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <polygon points="12,2 15.09,8.26 22,9.27 17,14.14 18.18,21.02 12,17.77 5.82,21.02 7,14.14 2,9.27 8.91,8.26" />
                                </svg>
                            @else
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-gray-400" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"></path>
                                    <path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"></path>
                                </svg>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            @if ($notificationType === 'order')
                                <div class="font-semibold text-base text-primary">
                                    {{ $notification->data['message'] ?? 'Đơn hàng mới' }}</div>
                                <div class="text-xs text-muted-foreground mt-1">Khách hàng:
                                    {{ $notification->data['customer_name'] ?? '' }}</div>
                            @elseif ($notificationType === 'order_status')
                                <div class="font-semibold text-base text-primary">
                                    {{ $notification->data['title'] ?? $notification->data['message'] ?? 'Cập nhật đơn hàng' }}</div>
                                <div class="text-xs text-muted-foreground mt-1">
                                    @if ($notification->data['order_code'] ?? null)
                                        Đơn hàng: {{ $notification->data['order_code'] }}
                                    @endif
                                    @if ($notification->data['customer_name'] ?? null)
                                        - Khách hàng: {{ $notification->data['customer_name'] }}
                                    @endif
                                </div>
                            @elseif ($notificationType === 'chat')
                                <div class="font-semibold text-base text-primary">
                                    {{ $notification->data['message'] ?? 'Tin nhắn mới' }}</div>
                                <div class="text-xs text-muted-foreground mt-1">Nội dung:
                                    {{ $notification->data['content'] ?? '' }}</div>
                            @elseif ($notificationType === 'review')
                                <div class="font-semibold text-base text-primary">
                                    {{ $notification->data['message'] ?? 'Có bình luận mới tại chi nhánh' }}</div>
                                <div class="text-xs text-muted-foreground mt-1">ID bình luận:
                                    {{ $notification->data['review_id'] ?? '' }}</div>
                            @elseif ($notificationType === 'review_report')
                                <div class="font-semibold text-base text-primary">
                                    {{ $notification->data['message'] ?? 'Bình luận bị báo cáo tại chi nhánh' }}</div>
                                <div class="text-xs text-muted-foreground mt-1">ID bình luận:
                                    {{ $notification->data['review_id'] ?? '' }}</div>
                            @else
                                <div class="font-semibold text-base text-primary">
                                    {{ $notification->data['message'] ?? $notification->data['title'] ?? 'Thông báo mới' }}</div>
                                @if ($notification->data['customer_name'] ?? null)
                                    <div class="text-xs text-muted-foreground mt-1">Khách hàng:
                                        {{ $notification->data['customer_name'] }}</div>
                                @endif
                            @endif
                            <div class="text-xs text-muted-foreground mt-2">
                                {{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}</div>
                        </div>
                        @if ($isUnread)
                            <span class="inline-block w-2 h-2 rounded-full bg-primary mt-2 ml-2" title="Chưa đọc"></span>
                        @endif
                    </div>
                @empty
                    <div class="text-center text-muted-foreground py-8">Không có thông báo nào.</div>
                @endforelse
            </div>
            <div class="mt-6 flex justify-center">
                {{ $notifications->links() }}
            </div>
        </div>
    @endsection
