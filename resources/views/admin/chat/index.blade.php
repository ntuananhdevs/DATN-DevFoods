@extends('layouts.admin.contentLayoutMaster')

@section('hide_footer', true)

@section('title', 'Quản Lý Chat')
<link rel="stylesheet" href="/css/admin/chat.css">

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        body, html {
            overflow: hidden !important;
            height: 100vh;
        }
        #main-content {
            overflow: hidden !important;
        }
        /* Sidebar chat item nhỏ gọn hơn, sát nhau */
        #chat-list .chat-item {
            padding-top: 2px !important;
            padding-bottom: 2px !important;
            min-height: 40px;
            border-bottom: none !important;
            margin-bottom: 0 !important;
        }
        #chat-list .chat-item .w-12, #chat-list .chat-item .h-12 {
            width: 32px !important;
            height: 32px !important;
            font-size: 0.95rem !important;
        }
        #chat-list .chat-item .font-semibold {
            font-size: 0.98rem !important;
        }
        #chat-list .chat-item .chat-item-preview {
            font-size: 0.9rem !important;
        }
        #chat-list .chat-item .chat-item-time {
            font-size: 0.8rem !important;
        }
    </style>

    <div id="chat-container" class="flex h-[92vh] rounded-lg overflow-hidden"
        @if (isset($conversation) && $conversation) data-conversation-id="{{ $conversation->id }}" @endif
        data-user-id="{{ auth()->id() }}" data-user-type="admin">
        <!-- Sidebar: Danh sách cuộc trò chuyện -->
        <aside class="w-1/4 min-w-[320px] border-r bg-gray-50 flex flex-col">
            <div class="p-4 border-b bg-white flex flex-col gap-3">
                <div class="relative">
                    <input id="chat-search" type="text" class="w-full rounded-full px-4 py-2 border border-gray-200 focus:ring-2 focus:ring-orange-400 focus:border-orange-400 transition placeholder-gray-400 pl-10" placeholder="Tìm kiếm cuộc trò chuyện...">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"><i class="fas fa-search"></i></span>
                </div>
                <div class="flex items-center gap-2">
                    <select id="chat-status-filter" class="flex-1 rounded-full px-3 py-2 border border-gray-200 bg-white focus:ring-2 focus:ring-orange-400 focus:border-orange-400 transition text-sm">
                        <option value="all">Tất cả trạng thái</option>
                        <option value="new">Chờ phản hồi</option>
                        <option value="distributed">Đã phân phối</option>
                        <option value="closed">Đã đóng</option>
                    </select>
                    <button id="refresh-chat-list" class="rounded-full bg-gray-100 hover:bg-orange-100 text-gray-500 w-9 h-9 flex items-center justify-center transition"><i class="fas fa-sync-alt"></i></button>
                </div>
            </div>
            <div id="chat-list" class="flex-1 overflow-y-auto custom-scrollbar">
                @forelse ($conversations as $conv)
                    <div class="chat-item group cursor-pointer px-4 py-3 border-b flex gap-3 items-center transition bg-white hover:bg-orange-50 {{ $conv->id == optional($conversation)->id ? 'bg-orange-50' : '' }}"
                        data-conversation-id="{{ $conv->id }}" data-status="{{ $conv->status }}"
                        data-customer-name="{{ $conv->customer->full_name ?? ($conv->customer->name ?? 'Khách hàng') }}"
                        data-customer-email="{{ $conv->customer->email }}"
                        data-branch-name="{{ $conv->branch ? $conv->branch->name : '' }}"
                        data-customer-phone="{{ $conv->customer->phone ?? '' }}">
                        <div class="relative">
                            <div class="w-12 h-12 rounded-full flex items-center justify-center font-bold text-white text-lg {{ $conv->id == optional($conversation)->id ? 'bg-blue-500' : 'bg-orange-500' }}">
                                {{ strtoupper(substr($conv->customer->full_name ?? ($conv->customer->name ?? 'K'), 0, 1)) }}
                            </div>
                            @if ($conv->messages->where('is_read', false)->where('sender_id', '!=', auth()->id())->count() > 0)
                                <span class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white text-xs rounded-full flex items-center justify-center">
                                    {{ $conv->messages->where('is_read', false)->where('sender_id', '!=', auth()->id())->count() }}
                                </span>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2">
                                <span class="font-semibold text-base truncate">{{ $conv->customer->full_name ?? ($conv->customer->name ?? 'Khách hàng') }}</span>
                                @if ($conv->branch)
                                    <span class="ml-2 px-2 py-0.5 text-xs rounded-full bg-gray-800 text-white">{{ $conv->branch->name }}</span>
                                @endif
                            </div>
                            @php
                                $lastMsg = $conv->messages->last();
                                $isAdminMsg = $lastMsg && ($lastMsg->sender_type === 'super_admin' || $lastMsg->sender_type === 'admin');
                                $isCustomerMsg = $lastMsg && $lastMsg->sender_type === 'customer';
                            @endphp
                            <div class="flex items-center gap-2 mt-1">
                                <span class="chat-item-preview truncate text-sm text-gray-500 flex-1">
                                    @if ($lastMsg)
                                        @if ($isAdminMsg)
                                            Bạn: {{ $lastMsg->message }}
                                        @elseif ($isCustomerMsg)
                                            Khách: {{ $lastMsg->message }}
                                        @else
                                            {{ $lastMsg->message }}
                                        @endif
                                    @else
                                        ...
                                    @endif
                                </span>
                            </div>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="chat-item-time text-xs text-gray-400">{{ $conv->messages->last()?->created_at ? $conv->messages->last()->created_at->format('H:i') : '' }}</span>
                                @if ($conv->status == 'new')
                                    <span class="ml-2 px-2 py-0.5 text-xs rounded-full bg-yellow-100 text-yellow-800">Chờ phản hồi</span>
                                @elseif ($conv->status == 'active' || $conv->status == 'distributed')
                                    <span class="ml-2 px-2 py-0.5 text-xs rounded-full bg-blue-100 text-blue-800">Đang hoạt động</span>
                                @elseif ($conv->status == 'closed')
                                    <span class="ml-2 px-2 py-0.5 text-xs rounded-full bg-gray-200 text-gray-800">Đã đóng</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-4 text-center text-gray-500">Không có cuộc trò chuyện nào.</div>
                @endforelse
            </div>
        </aside>
        <!-- Main Chat -->
        <main class="flex-1 flex flex-col bg-white">
            @php $hasConversation = isset($conversation) && $conversation; @endphp
            @if ($hasConversation)
                <!-- Header -->
                <div class="flex items-center justify-between border-b px-6 py-4 bg-white">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-full bg-orange-500 flex items-center justify-center text-white text-xl font-bold">
                            {{ strtoupper(substr($conversation->customer->full_name ?? ($conversation->customer->name ?? 'K'), 0, 1)) }}
                        </div>
                        <div>
                            <div class="font-semibold text-lg" id="chat-customer-name">
                                {{ $conversation->customer->full_name ?? ($conversation->customer->name ?? 'Khách hàng') }}
                            </div>
                            <div class="text-xs text-gray-500" id="chat-customer-email">{{ $conversation->customer->email }}</div>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="px-3 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800 capitalize">{{ __($conversation->status == 'new' ? 'Chờ phản hồi' : ($conversation->status == 'active' || $conversation->status == 'distributed' ? 'Đang hoạt động' : 'Đã đóng')) }}</span>
                        @if ($conversation->branch)
                            <span class="px-3 py-1 text-xs rounded-full bg-gray-800 text-white">{{ $conversation->branch->name }}</span>
                        @endif
                    </div>
                </div>
                <!-- Messages -->
                <div class="flex-1 overflow-y-auto p-6 space-y-4 custom-scrollbar bg-gray-50" id="chat-messages">
                    <!-- Tin nhắn sẽ được load bằng JS ChatCommon, nhưng sẽ render dạng:
                    <div class='flex items-end gap-2'>
                        <div class='w-8 h-8 rounded-full bg-orange-500 flex items-center justify-center text-white text-xs font-bold'>A</div>
                        <div>
                            <div class='bg-white px-4 py-2 rounded-2xl shadow text-gray-900'>Nội dung tin nhắn</div>
                            <div class='text-xs text-gray-500 mt-1'>Administrator • 23:20</div>
                        </div>
                    </div>
                    hoặc
                    <div class='flex items-end gap-2 flex-row-reverse'>
                        <div class='w-8 h-8 rounded-full bg-blue-500 flex items-center justify-center text-white text-xs font-bold'>T</div>
                        <div>
                            <div class='bg-orange-500 text-white px-4 py-2 rounded-2xl shadow'>Nội dung tin nhắn</div>
                            <div class='text-xs text-gray-500 mt-1'>Tuấn Anh Nguyễn • 23:20</div>
                        </div>
                    </div>
                    -->
                </div>
                <!-- Quick Reply -->
                <div class="px-6 py-2 flex flex-wrap gap-2 border-t bg-white">
                    <button class="px-4 py-2 rounded-full bg-orange-100 text-orange-700 text-sm hover:bg-orange-200 quick-reply-btn">Xin chào! Tôi có thể giúp gì cho bạn?</button>
                    <button class="px-4 py-2 rounded-full bg-orange-100 text-orange-700 text-sm hover:bg-orange-200 quick-reply-btn">Cảm ơn bạn đã liên hệ</button>
                    <button class="px-4 py-2 rounded-full bg-orange-100 text-orange-700 text-sm hover:bg-orange-200 quick-reply-btn">Để tôi kiểm tra thông tin cho bạn</button>
                    <button class="px-4 py-2 rounded-full bg-orange-100 text-orange-700 text-sm hover:bg-orange-200 quick-reply-btn">Bạn có cần hỗ trợ thêm không?</button>
                </div>
                <!-- Preview ảnh trước khi gửi -->
                <div id="chat-image-preview" class="mb-2 flex gap-2 hidden ml-12"></div>
                <form id="chat-form" enctype="multipart/form-data" class="flex items-center gap-2 p-4 border-t bg-white">
                    <button type="button" id="attachImageBtn" class="chat-tools-btn" title="Gửi ảnh"><i class="fas fa-image"></i></button>
                    <input type="file" id="imageInput" class="hidden" name="image" accept="image/*">
                    <button type="button" id="attachFileBtn" class="chat-tools-btn" title="Gửi file"><i class="fas fa-paperclip"></i></button>
                    <input type="file" id="fileInput" class="hidden" name="file"
                        accept="application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/zip,application/x-rar-compressed,application/octet-stream">
                    <textarea id="chat-input-message" class="flex-1 border rounded-full px-4 py-2 resize-none focus:outline-none focus:ring-2 focus:ring-orange-500" placeholder="Nhập tin nhắn..." rows="1"></textarea>
                    <button type="submit" id="chat-send-btn" class="chat-send-btn bg-orange-500 hover:bg-orange-600 text-white rounded-full w-10 h-10 flex items-center justify-center"><i class="fas fa-paper-plane"></i></button>
                </form>
            @else
                <div class="flex flex-1 flex-col items-center justify-center h-full p-8 text-center bg-white">
                    <img src="https://cdn-icons-png.flaticon.com/512/4076/4076549.png" alt="No chat"
                        style="width:80px;height:80px;opacity:0.5;">
                    <h3 class="mt-4 mb-2 text-lg font-semibold">Chưa có cuộc trò chuyện nào</h3>
                    <p>Bạn sẽ thấy các cuộc trò chuyện với khách hàng tại đây khi có tin nhắn mới.</p>
                </div>
            @endif
        </main>
        <!-- Sidebar phải: Thông tin khách hàng -->
        <aside class="w-1/5 min-w-[260px] border-l bg-white flex flex-col">
            <div class="flex flex-col items-center gap-2 p-6">
                <div class="w-16 h-16 bg-gray-200 rounded-full flex items-center justify-center font-bold text-xl mb-2"
                    id="customer-info-avatar">
                    @if ($hasConversation)
                        {{ strtoupper(substr($conversation->customer->full_name ?? ($conversation->customer->name ?? 'K'), 0, 1)) }}
                    @else
                        ?
                    @endif
                </div>
                <div class="font-bold text-lg" id="customer-info-name">
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
                            {{ __($conversation->status == 'new' ? 'Chờ phản hồi' : ($conversation->status == 'active' || $conversation->status == 'distributed' ? 'Đang hoạt động' : 'Đã đóng')) }}
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
                <a href="#" class="btn w-full text-white bg-gray-900 hover:bg-gray-800">Xem lịch sử đơn hàng</a>
            </div>
        </aside>
    </div>

    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <script src="{{ asset('js/test-pusher.js') }}"></script>
    <script src="{{ asset('js/chat-realtime.js') }}" defer></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @if($hasConversation)
                const chatCommon = new ChatCommon({
                    conversationId: '{{ $conversation->id }}',
                    userId: {{ auth()->id() }},
                    userType: 'admin',
                    api: {
                        send: '/admin/chat/send',
                        getMessages: '/admin/chat/messages/{{ $conversation->id }}',
                        distribute: '/admin/chat/distribute'
                    }
                });
            @endif
        });

        window.pusherKey = "{{ config('broadcasting.connections.pusher.key') }}";
        window.pusherCluster = "{{ config('broadcasting.connections.pusher.options.cluster') }}";

        const imageInput = document.getElementById('imageInput');
        const imagePreview = document.getElementById('chat-image-preview');
        let pendingImage = null;
        if (imageInput && imagePreview) {
            imageInput.addEventListener('change', function(e) {
                imagePreview.innerHTML = '';
                const file = this.files[0];
                if (file && file.type.startsWith('image/')) {
                    pendingImage = file;
                    const reader = new FileReader();
                    reader.onload = function(ev) {
                        imagePreview.innerHTML = `<div class='relative inline-block'><img src='${ev.target.result}' class='w-24 h-24 object-cover rounded-lg border'><button type='button' class='absolute -top-2 -right-2 bg-white border border-gray-300 rounded-full p-1 shadow remove-preview-btn' title='Xóa'><i class='fas fa-times text-red-500'></i></button></div>`;
                        imagePreview.classList.remove('hidden');
                        imagePreview.querySelector('.remove-preview-btn').onclick = function() {
                            imageInput.value = '';
                            imagePreview.innerHTML = '';
                            imagePreview.classList.add('hidden');
                            pendingImage = null;
                        };
                    };
                    reader.readAsDataURL(file);
                } else {
                    imagePreview.classList.add('hidden');
                    pendingImage = null;
                }
            });
        }
    </script>
@endsection
