@extends('layouts.admin.contentLayoutMaster')

@section('title', 'Quản Lý Chat')
<link rel="stylesheet" href="/css/admin/chat.css">

@php
    $statusLabels = [
        'new' => ['label' => 'Chờ phản hồi', 'class' => 'badge badge-waiting', 'icon' => '⏰'],
        'distributed' => ['label' => 'Đã phân phối', 'class' => 'badge badge-distributed', 'icon' => '📤'],
        'active' => ['label' => 'Đang xử lý', 'class' => 'badge badge-active', 'icon' => '🟠'],
        'resolved' => ['label' => 'Đã giải quyết', 'class' => 'badge badge-resolved', 'icon' => '✅'],
        'closed' => ['label' => 'Đã đóng', 'class' => 'badge badge-closed', 'icon' => '🔒'],
    ];
@endphp

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div id="chat-container" class="chat-container"
        @if (isset($conversation) && $conversation) data-conversation-id="{{ $conversation->id }}" @endif
        data-user-id="{{ auth()->id() }}" data-user-type="admin">
        <!-- Sidebar: Danh sách cuộc trò chuyện -->
        <div class="chat-sidebar" style="width:26%">
            <div class="chat-sidebar-header">
                <div class="relative mb-2">
                    <span class="absolute left-3 top-2.5 text-gray-400">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 104.5 4.5a7.5 7.5 0 0012.15 12.15z" />
                        </svg>
                    </span>
                    <input id="chat-search" type="text"
                        class="form-control w-100 w-full pl-10 pr-4 py-2 rounded-lg border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition placeholder-gray-400"
                        style="width:100%" placeholder="Tìm kiếm theo tên, email khách hàng...">
                </div>
                <div class="flex items-center gap-2">
                    <select id="chat-status-filter" class="form-select flex-1">
                        <option value="all">Tất cả trạng thái</option>
                        <option value="new">Chờ phản hồi</option>
                        <option value="distributed">Đã phân phối</option>
                        <option value="active">Đang xử lý</option>
                        <option value="closed">Đã đóng</option>
                    </select>
                    <button id="refresh-chat-list" class="btn btn-light px-2"><i class="fas fa-sync-alt"></i></button>
                </div>
            </div>
            <div id="chat-list" class="chat-list">
                @forelse ($conversations as $conv)
                    <div class="chat-item {{ $conv->id == optional($conversation)->id ? 'active' : '' }}  relative"
                        data-conversation-id="{{ $conv->id }}" data-status="{{ $conv->status }}"
                        data-customer-name="{{ $conv->customer->full_name ?? ($conv->customer->name ?? 'Khách hàng') }}"
                        data-customer-email="{{ $conv->customer->email }}"
                        data-branch-name="{{ $conv->branch ? $conv->branch->name : '' }}"
                        data-customer-phone="{{ $conv->customer->phone ?? '' }}">
                        <div class="flex items-center gap-3 w-full min-w-0">
                            <div class="flex flex-col items-center justify-center relative">
                                <div
                                    class="chat-item-avatar mb-5 w-12 h-12 rounded-full flex items-center justify-center font-bold text-white text-lg {{ $conv->id == optional($conversation)->id ? 'bg-blue-500' : 'bg-orange-500' }}">
                                    {{ strtoupper(substr($conv->customer->full_name ?? ($conv->customer->name ?? 'K'), 0, 1)) }}
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2">
                                    <span
                                        class="chat-item-name truncate font-semibold text-base">{{ $conv->customer->full_name ?? ($conv->customer->name ?? 'Khách hàng') }}</span>
                                </div>
                                <div class="flex items-center gap-2 mt-1">
                                    <span
                                        class="chat-item-preview truncate text-sm text-gray-500 flex-1">{{ $conv->messages->last()->message ?? '...' }}</span>
                                </div>
                                <div class="flex items-center gap-2 mt-1">
                                    <span
                                        class="chat-item-time">{{ $conv->messages->last()?->created_at ? $conv->messages->last()->created_at->format('H:i') : '' }}</span>
                                </div>
                            </div>
                            @if ($conv->messages->where('is_read', false)->where('sender_id', '!=', auth()->id())->count() > 0)
                                <span
                                    class="unread-badge ml-2 absolute right-2 bottom-2">{{ $conv->messages->where('is_read', false)->where('sender_id', '!=', auth()->id())->count() }}</span>
                            @endif
                        </div>
                        <div class="chat-item-badges mt-2 flex flex-row flex-wrap gap-2">
                            <span class="{{ $statusLabels[$conv->status]['class'] ?? 'badge' }}">
                                {{ $statusLabels[$conv->status]['icon'] ?? '' }}
                                {{ $statusLabels[$conv->status]['label'] ?? $conv->status }}
                            </span>
                            <span class="badge badge-branch">{{ $conv->branch?->name }}</span>
                        </div>

                    </div>
                @empty
                    <div class="p-4 text-center ">Không có cuộc trò chuyện nào.</div>
                @endforelse
            </div>
        </div>
        <!-- Main Chat -->
        <div class="chat-main" style="width:55%">
            @php
                $hasConversation = isset($conversation) && $conversation;
            @endphp
            @if ($hasConversation)
                <div class="chat-header">
                    <div class="chat-header-user">
                        <div class="chat-avatar" id="chat-avatar">
                            {{ strtoupper(substr($conversation->customer->full_name ?? ($conversation->customer->name ?? 'K'), 0, 1)) }}
                        </div>
                        <div class="chat-header-info">
                            <h3 id="chat-customer-name">
                                {{ $conversation->customer->full_name ?? ($conversation->customer->name ?? 'Khách hàng') }}
                            </h3>
                            <p id="chat-customer-email">{{ $conversation->customer->email }}</p>
                        </div>
                    </div>
                    <div class="chat-header-actions" id="chat-header-actions">
                        <span
                            class="badge status-badge status-{{ $conversation->status }}">{{ ucfirst($conversation->status) }}</span>
                        @if ($conversation->branch)
                            <span class="badge badge-xs branch-badge ml-2"
                                id="main-branch-badge">{{ $conversation->branch->name }}</span>
                        @endif
                        <button class="chat-tools-btn"><i class="fas fa-phone"></i></button>
                        <button class="chat-tools-btn"><i class="fas fa-video"></i></button>
                    </div>
                </div>
                <div class="chat-messages" id="chat-messages">
                    <!-- Tin nhắn sẽ được load bằng JS ChatCommon -->
                </div>
                <div class="chat-input-container">
                    <form id="chat-form" enctype="multipart/form-data" class="flex w-full gap-2">
                        <textarea id="chat-input-message" class="chat-input" placeholder="Nhập tin nhắn..."></textarea>
                        <input type="file" id="imageInput" class="hidden" name="image" accept="image/*">
                        <input type="file" id="fileInput" class="hidden" name="file"
                            accept="application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/zip,application/x-rar-compressed,application/octet-stream">
                        <button type="button" id="attachImageBtn" class="chat-tools-btn" title="Gửi ảnh"><i
                                class="fas fa-image"></i></button>
                        <button type="button" id="attachFileBtn" class="chat-tools-btn" title="Gửi file"><i
                                class="fas fa-paperclip"></i></button>

                        <button type="submit" id="chat-send-btn" class="chat-send-btn"><i
                                class="fas fa-paper-plane"></i></button>
                    </form>
                </div>
            @else
                <div class="flex flex-col items-center justify-center h-full p-8 text-center ">
                    <img src="https://cdn-icons-png.flaticon.com/512/4076/4076549.png" alt="No chat"
                        style="width:80px;height:80px;opacity:0.5;">
                    <h3 class="mt-4 mb-2 text-lg font-semibold">Chưa có cuộc trò chuyện nào</h3>
                    <p>Bạn sẽ thấy các cuộc trò chuyện với khách hàng tại đây khi có tin nhắn mới.</p>
                </div>
            @endif
        </div>
        <!-- Customer Info -->
        <div class="chat-sidebar border-l" style="width:20%">
            <div class="flex flex-col items-center gap-2 p-4">
                <div class="w-16 h-16 bg-gray-200 rounded-full flex items-center justify-center font-bold text-xl mb-2"
                    id="customer-info-avatar">
                    @if ($hasConversation)
                        {{ strtoupper(substr($conversation->customer->full_name ?? ($conversation->customer->name ?? 'K'), 0, 1)) }}
                    @else
                        ?
                    @endif
                </div>
                <div class="font-bold" id="customer-info-name">
                    @if ($hasConversation)
                        {{ $conversation->customer->full_name ?? ($conversation->customer->name ?? 'Khách hàng') }}
                    @else
                        Khách hàng
                    @endif
                </div>
                <div class="text-xs text-gray-500" id="customer-info-email">
                    @if ($hasConversation)
                        {{ $conversation->customer->email }}
                    @endif
                </div>
                <div class="text-xs text-gray-500" id="customer-info-phone">SĐT:
                    @if ($hasConversation)
                        {{ $conversation->customer->phone ?? '---' }}
                    @else
                        ---
                    @endif
                </div>
                <div class="text-xs text-gray-500">Trạng thái: <span class="font-semibold" id="customer-info-status">
                        @if ($hasConversation)
                            {{ ucfirst($conversation->status) }}
                        @endif
                    </span></div>
                <div class="text-xs text-gray-500">Lần cuối hoạt động: @if ($hasConversation)
                        {{ $conversation->updated_at->diffForHumans() }}
                    @endif
                </div>
                @if ($hasConversation && $conversation->branch)
                    <div class="mt-2"><span class="badge badge-xs branch-badge ml-2"
                            id="customer-info-branch-badge">{{ $conversation->branch->name }}</span></div>
                @endif
            </div>
            <div class="p-4 flex justify-end">
                <div class="w-full flex flex-col items-end">
                    @if ($hasConversation)
                        @if (!$conversation->branch_id && $conversation->status === 'new')
                            <select class="distribution-select form-select w-full max-w-xs" id="distribution-select"
                                data-conversation-id="{{ $conversation->id }}">
                                <option value="" disabled selected>Chọn chi nhánh</option>
                                @foreach ($branches as $branch)
                                    <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                @endforeach
                            </select>
                        @endif
                    @endif
                </div>
            </div>
            <div class="p-4">
                <a href="#" style="background-color: #111827" class="btn w-full text-white">Xem lịch sử đơn
                    hàng</a>
            </div>
        </div>
    </div>

    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <script src="{{ asset('js/chat-realtime.js') }}" defer></script>

    <script>
        window.PUSHER_APP_KEY = "{{ env('PUSHER_APP_KEY') }}";
        window.PUSHER_APP_CLUSTER = "{{ env('PUSHER_APP_CLUSTER') }}";
    </script>

    <style>
        .typing-indicator {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            gap: 10px;
            margin: 8px 0;
            height: 32px;
            min-width: 120px;
        }

        .typing-indicator .typing-flex {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .typing-indicator .dot {
            display: inline-block;
            width: 8px;
            height: 8px;
            background: #f59e42;
            border-radius: 50%;
            opacity: 0.6;
            animation: typing-bounce 1s infinite alternate;
        }

        .typing-indicator .dot:nth-child(2) {
            animation-delay: 0.2s;
        }

        .typing-indicator .dot:nth-child(3) {
            animation-delay: 0.4s;
        }

        .typing-indicator .typing-text {
            margin-left: 8px;
            font-size: 14px;
            color: #888;
            white-space: nowrap;
            font-weight: 500;
            letter-spacing: 0.2px;
        }

        @keyframes typing-bounce {
            0% {
                transform: translateY(0);
                opacity: 0.6;
            }

            100% {
                transform: translateY(-8px);
                opacity: 1;
            }
        }

        .dark .typing-indicator .dot {
            background: #ccc;
        }

        #chat-search {
            background: #fff;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
            padding-left: 40px;
            padding-right: 16px;
            height: 40px;
            font-size: 15px;
            transition: border 0.2s, box-shadow 0.2s;
        }

        #chat-search:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 2px #dbeafe;
        }
    </style>
@endsection
