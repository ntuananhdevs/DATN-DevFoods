@extends('layouts.admin.contentLayoutMaster')

@section('content')
    <div class="container mx-auto py-4">
        <h2 class="text-2xl font-bold mb-6 text-primary">Tất cả thông báo</h2>
        <div class="space-y-4">
            @forelse($adminNotifications as $notification)
                @php
                    $orderId = $notification->data['order_id'] ?? null;
                    $conversationId = $notification->data['conversation_id'] ?? null;
                    $isUnread = !$notification->read_at;
                @endphp
                <div
                    class="flex items-start gap-4 rounded-lg border bg-white shadow-sm p-4 transition hover:shadow-md {{ $isUnread ? 'bg-primary/10 border-primary' : '' }}">
                    <div class="flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-primary/20">
                        @if ($orderId)
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-blue-500" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <circle cx="8" cy="21" r="1" />
                                <circle cx="19" cy="21" r="1" />
                                <path
                                    d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12" />
                            </svg>
                        @elseif ($conversationId)
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-blue-500" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path
                                    d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z" />
                            </svg>
                        @else
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-gray-400" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path d="M13.73 21a2 2 0 0 1-3.46 0" />
                                <path d="M18.63 13A17.89 17.89 0 0 1 18 8" />
                                <path d="M6 8a17.89 17.89 0 0 1-.63 5" />
                                <path d="M6.26 21a2 2 0 0 0 3.46 0" />
                                <path d="M18.63 13A17.89 17.89 0 0 0 18 8" />
                                <path d="M6 8a17.89 17.89 0 0 0-.63 5" />
                                <path d="M12 3v1" />
                                <path d="M12 19v2" />
                            </svg>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        @if ($orderId)
                            <div class="font-semibold text-base text-primary">
                                {{ $notification->data['message'] ?? 'Đơn hàng mới' }}</div>
                            <div class="text-xs text-muted-foreground mt-1">Khách hàng:
                                {{ $notification->data['customer_name'] ?? '' }}</div>
                        @elseif ($conversationId)
                            <div class="font-semibold text-base text-primary">
                                {{ $notification->data['message'] ?? 'Tin nhắn mới' }}</div>
                            <div class="text-xs text-muted-foreground mt-1">Nội dung:
                                {{ $notification->data['content'] ?? '' }}</div>
                        @else
                            <div class="font-semibold text-base text-primary">{{ $notification->data['message'] ?? '' }}
                            </div>
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
            {{ $adminNotifications->links() }}
        </div>
    </div>
@endsection
