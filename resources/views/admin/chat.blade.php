@extends('layouts.admin.contentLayoutMaster')

@section('title', 'Super Admin Dashboard')

@section('content')
    <style>
        :root {
            --primary-color: #3b82f6;
            --primary-hover: #2563eb;
            --text-primary: #111827;
            --text-secondary: #4b5563;
            --text-muted: #6b7280;
            --bg-hover: #f3f4f6;
            --border-color: #e5e7eb;
            --badge-waiting: #fef3c7;
            --badge-waiting-text: #92400e;
            --badge-high: #fee2e2;
            --badge-high-text: #991b1b;
            --badge-distributed: #dbeafe;
            --badge-distributed-text: #1e40af;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
            color: var(--text-primary);
            background-color: #f9fafb;
        }

        .chat-container {
            display: flex;
            height: calc(100vh - 80px);
            background-color: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            border: 1px solid var(--border-color);
        }

        .chat-sidebar {
            width: 380px;
            border-right: 1px solid var(--border-color);
            display: flex;
            flex-direction: column;
            background-color: #f9fafb;
        }

        .chat-sidebar-header {
            padding: 20px;
            border-bottom: 1px solid var(--border-color);
            background-color: white;
        }

        .chat-sidebar-header h2 {
            margin: 0;
            font-size: 1.25rem;
            font-weight: 600;
        }

        .chat-sidebar-header p {
            margin: 4px 0 0;
            color: var(--text-secondary);
            font-size: 0.875rem;
        }

        .chat-list {
            flex: 1;
            overflow-y: auto;
        }

        .chat-item {
            padding: 16px 20px;
            border-bottom: 1px solid var(--border-color);
            cursor: pointer;
            transition: background-color 0.2s;
            position: relative;
            background-color: white;
        }

        .chat-item:hover {
            background-color: var(--bg-hover);
        }

        .chat-item.active {
            background-color: #f0f9ff;
            border-left: 3px solid var(--primary-color);
        }

        .chat-item-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 4px;
        }

        .chat-item-name {
            font-weight: 600;
            font-size: 0.9375rem;
            margin: 0;
        }

        .chat-item-time {
            font-size: 0.75rem;
            color: var(--text-muted);
        }

        .chat-item-email {
            font-size: 0.8125rem;
            color: var(--text-secondary);
            margin: 0 0 8px;
        }

        .chat-item-preview {
            font-size: 0.875rem;
            color: var(--text-secondary);
            margin: 0 0 8px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .chat-item-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .chat-item-badges {
            display: flex;
            gap: 8px;
        }

        .badge {
            font-size: 0.75rem;
            padding: 4px 10px;
            border-radius: 9999px;
            font-weight: 500;
        }

        .badge-waiting {
            background-color: var(--badge-waiting);
            color: var(--badge-waiting-text);
        }

        .badge-high {
            background-color: var(--badge-high);
            color: var(--badge-high-text);
        }

        .badge-distributed {
            background-color: var(--badge-distributed);
            color: var(--badge-distributed-text);
        }

        .unread-badge {
            background-color: #ef4444;
            color: white;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            font-weight: 600;
            position: absolute;
            top: 16px;
            right: 20px;
        }

        .chat-main {
            flex: 1;
            display: flex;
            flex-direction: column;
            background: #f9fafb;
        }

        .chat-header {
            padding: 16px 24px;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: white;
        }

        .chat-header-user {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .chat-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #e5e7eb;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 1rem;
        }

        .chat-header-info h3 {
            margin: 0;
            font-size: 1rem;
            font-weight: 600;
        }

        .chat-header-info p {
            margin: 2px 0 0;
            font-size: 0.875rem;
            color: var(--text-secondary);
        }

        .chat-header-actions {
            display: flex;
            gap: 8px;
        }

        .chat-messages {
            flex: 1;
            overflow-y: auto;
            padding: 16px;
            background: #f9fafb;
            display: flex;
            flex-direction: column;
            gap: 8px;
            height: calc(100vh - 200px);
        }

        .message-group {
            margin-bottom: 16px;
            max-width: 80%;
            display: flex;
            flex-direction: column;
        }

        .message-group-admin {
            margin-left: auto;
        }

        .message-group-customer {
            margin-right: auto;
        }

        .message-sender {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 4px;
            padding: 0 8px;
        }

        .message-content {
            display: flex;
            align-items: flex-end;
            gap: 8px;
            padding: 0 8px;
        }

        .message-group-admin .message-content {
            flex-direction: row-reverse;
        }

        .message-bubble {
            padding: 12px 16px;
            border-radius: 16px;
            max-width: 100%;
            word-wrap: break-word;
            font-size: 0.95rem;
            line-height: 1.4;
        }

        .message-admin {
            background-color: #3b82f6;
            color: white;
            border-top-right-radius: 4px;
        }

        .message-customer {
            background-color: #f3f4f6;
            color: #1f2937;
            border-top-left-radius: 4px;
        }

        .message-time {
            font-size: 0.75rem;
            color: #6b7280;
            margin: 0 4px;
            white-space: nowrap;
        }

        .message-system {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin: 16px 0;
        }

        .system-message {
            background-color: #f3f4f6;
            color: #6b7280;
            padding: 8px 16px;
            border-radius: 16px;
            font-size: 0.875rem;
            text-align: center;
        }

        .message-sender-name {
            font-weight: 500;
            color: #1f2937;
            font-size: 0.95rem;
        }

        .message-sender-type {
            font-size: 0.75rem;
            color: #6b7280;
            background-color: #f3f4f6;
            padding: 2px 6px;
            border-radius: 4px;
        }

        .typing-indicator {
            font-size: 0.9rem;
            color: #6b7280;
            margin-top: 4px;
            margin-left: 8px;
            font-style: italic;
        }

        .chat-input-container {
            padding: 16px 24px;
            background-color: white;
            border-top: 1px solid var(--border-color);
        }

        .chat-input-wrapper {
            display: flex;
            gap: 12px;
            align-items: flex-end;
        }

        .chat-input {
            flex: 1;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 12px 16px;
            font-size: 0.9375rem;
            resize: none;
            min-height: 24px;
            max-height: 120px;
            background-color: white;
            transition: border-color 0.2s;
        }

        .chat-input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.1);
        }

        .chat-input-actions {
            display: flex;
            gap: 8px;
        }

        .chat-input-action {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            border: 1px solid var(--border-color);
            background-color: white;
            color: var(--text-secondary);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
        }

        .chat-input-action:hover {
            background-color: var(--bg-hover);
            color: var(--text-primary);
        }

        .chat-input-action i {
            font-size: 1.25rem;
        }

        .send-button {
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: 8px;
            padding: 0 16px;
            height: 40px;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.2s;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .send-button:hover {
            background-color: var(--primary-hover);
        }

        .send-button:disabled {
            background-color: var(--border-color);
            cursor: not-allowed;
        }

        .send-button i {
            font-size: 1rem;
        }

        .chat-empty {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: var(--text-secondary);
            background: #f9fafb;
        }

        .chat-empty-icon {
            font-size: 64px;
            color: #9ca3af;
            margin-bottom: 16px;
        }

        .dropdown-branch {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid var(--border-color);
            border-radius: 6px;
            background: white;
            color: var(--text-primary);
            font-size: 0.95rem;
            margin-top: 10px;
        }

        .stats-row {
            display: flex;
            gap: 24px;
            margin-bottom: 32px;
        }

        .stat-card {
            flex: 1;
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
            padding: 32px 28px 24px 28px;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            min-width: 220px;
            position: relative;
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            margin-bottom: 18px;
        }

        .stat-blue {
            background: #2563eb1a;
            color: #2563eb;
        }

        .stat-green {
            background: #10b9811a;
            color: #10b981;
        }

        .stat-yellow {
            background: #f59e0b1a;
            color: #f59e0b;
        }

        .stat-red {
            background: #ef44441a;
            color: #ef4444;
        }

        .stat-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #22223b;
            margin-bottom: 2px;
        }

        .stat-label {
            color: #6b7280;
            font-size: 1rem;
            font-weight: 500;
            margin-bottom: 8px;
        }

        .stat-desc {
            color: #10b981;
            font-size: 0.98rem;
            font-weight: 500;
        }

        .stat-number {
            font-size: 2.2rem;
            font-weight: 700;
            color: #22223b;
            margin-bottom: 2px;
        }
    </style>

    <!-- Card thống kê -->
    <div class="stats-row">
        <div class="stat-card">
            <div class="stat-icon stat-blue"><i class="fas fa-inbox"></i></div>
            <div class="stat-number">{{ $conversations->where('status', 'waiting')->count() }}</div>
            <div class="stat-label">MỚI NHẬN</div>
            <div class="stat-desc">
                +{{ $conversations->where('status', 'waiting')->where('created_at', '>=', today())->count() }} hôm nay</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon stat-green"><i class="fas fa-comments"></i></div>
            <div class="stat-number">{{ $conversations->where('status', 'processing')->count() }}</div>
            <div class="stat-label">ĐANG XỬ LÝ</div>
            <div class="stat-desc">
                {{ $conversations->where('status', 'processing')->where('updated_at', '>=', now()->subDay())->count() }}
                hoạt động gần đây</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon stat-yellow"><i class="fas fa-exclamation-triangle"></i></div>
            <div class="stat-number">{{ $conversations->where('priority', 'high')->count() }}</div>
            <div class="stat-label">ƯU TIÊN CAO</div>
            <div class="stat-desc">Cần xử lý ngay</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon stat-red"><i class="fas fa-chart-line"></i></div>
            <div class="stat-number">{{ $conversations->count() }}</div>
            <div class="stat-label">TỔNG CỘNG</div>
            <div class="stat-desc">{{ $conversations->where('status', 'solved')->count() }} đã giải quyết</div>
        </div>
    </div>

    <div class="chat-container">
        <!-- Sidebar Danh sách chat động -->
        <div class="chat-sidebar">
            <div class="chat-sidebar-header">
                <h2>Super Admin Dashboard</h2>
                <p>Quản lý và phân phối chat</p>
            </div>
            <div class="chat-list">
                @foreach ($conversations as $conversation)
                    <div class="chat-item {{ $selectedConversation && $conversation->id == $selectedConversation->id ? 'active' : '' }}"
                        data-conversation-id="{{ $conversation->id }}">
                        <div class="chat-item-header">
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <div class="chat-avatar" style="width: 38px; height: 38px; font-size: 1.1rem;">
                                    {{ strtoupper(mb_substr($conversation->customer->name ?? 'K', 0, 1)) }}
                                </div>
                                <div>
                                    <div class="chat-item-name">{{ $conversation->customer->name ?? 'Khách hàng' }}</div>
                                    <div class="chat-item-email">{{ $conversation->customer->email ?? '' }}</div>
                                </div>
                            </div>
                            @if ($conversation->unread_count ?? 0 > 0)
                                <div class="unread-badge">{{ $conversation->unread_count }}</div>
                            @endif
                        </div>
                        <div class="chat-item-preview">
                            {{ $conversation->messages->last()?->message ?? 'Chưa có tin nhắn' }}</div>
                        <div class="chat-item-footer">
                            <div class="chat-item-badges">
                                @if ($conversation->status === 'waiting')
                                    <span class="badge badge-waiting">Chờ xử lý</span>
                                @elseif($conversation->status === 'processing')
                                    <span class="badge badge-high">Đang xử lý</span>
                                @elseif($conversation->status === 'distributed')
                                    <span class="badge badge-distributed">Đã phân công</span>
                                @endif
                                @if ($conversation->priority === 'high')
                                    <span class="badge badge-high">Cao</span>
                                @elseif($conversation->priority === 'normal')
                                    <span class="badge" style="background:#e0e7ff; color:#3730a3;">Bình thường</span>
                                @endif
                                @if ($conversation->branch)
                                    <span class="badge badge-distributed">{{ $conversation->branch->name }}</span>
                                @endif
                            </div>
                            @if (!$conversation->branch)
                                <select class="dropdown-branch distribution-select"
                                    data-conversation-id="{{ $conversation->id }}">
                                    <option>Phân công cho chi nhánh</option>
                                    @foreach ($branches as $branch)
                                        <option value="{{ $branch->id }}"
                                            {{ $conversation->branch_id == $branch->id ? 'selected' : '' }}>
                                            {{ $branch->name }}
                                        </option>
                                    @endforeach
                                </select>
                            @endif
                            <span class="chat-item-time">{{ $conversation->updated_at->format('H:i') }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        <!-- Phần chính: Nội dung chat hoặc trạng thái chọn chat -->
        <div class="chat-main">
            @if ($selectedConversation)
                <!-- Header -->
                <div class="chat-header">
                    <div class="chat-header-user">
                        <div class="chat-avatar">
                            {{ strtoupper(mb_substr($selectedConversation->customer->name ?? 'K', 0, 1)) }}</div>
                        <div class="chat-header-info">
                            <h3>{{ $selectedConversation->customer->name ?? 'Khách hàng' }}</h3>
                            <div class="chat-header-meta">{{ $selectedConversation->customer->email ?? '' }}</div>
                        </div>
                    </div>
                    <div class="chat-header-actions">
                        @if ($selectedConversation->status === 'waiting')
                            <span class="badge badge-waiting">Chờ xử lý</span>
                        @elseif($selectedConversation->status === 'processing')
                            <span class="badge badge-processing">Đang xử lý</span>
                        @elseif($selectedConversation->status === 'distributed')
                            <span class="badge badge-distributed">Đã phân công</span>
                        @endif
                        @if ($selectedConversation->priority === 'high')
                            <span class="badge badge-high">Cao</span>
                        @elseif($selectedConversation->priority === 'normal')
                            <span class="badge" style="background:#e0e7ff; color:#3730a3;">Bình thường</span>
                        @endif
                        @if ($selectedConversation->branch)
                            <span class="badge badge-distributed">{{ $selectedConversation->branch->name }}</span>
                        @endif
                    </div>
                </div>
                <!-- Danh sách tin nhắn -->
                <div class="chat-messages" id="chat-messages">
                    @foreach ($selectedConversation->messages as $message)
                        @if ($message->type === 'system' || $message->is_system_message)
                            <div class="message-system">
                                <div class="system-message">{{ $message->message }}</div>
                                <span class="message-time">{{ $message->created_at->format('H:i') }}</span>
                            </div>
                        @else
                            <div
                                class="message-group {{ $message->sender_id === auth()->id() ? 'message-group-admin' : 'message-group-customer' }}">
                                <div class="message-sender">
                                    <div class="chat-avatar"
                                        style="{{ $message->sender_id === auth()->id() ? 'background-color: #3b82f6; color: white;' : '' }}">
                                        {{ strtoupper(mb_substr($message->sender->name ?? 'K', 0, 1)) }}
                                    </div>
                                    <span class="message-sender-name">{{ $message->sender->name ?? 'Khách hàng' }}</span>
                                    @if ($message->sender_id !== auth()->id())
                                        <span class="message-sender-type">Khách hàng</span>
                                    @endif
                                </div>
                                <div class="message-content">
                                    <div
                                        class="message-bubble {{ $message->sender_id === auth()->id() ? 'message-admin' : 'message-customer' }}">
                                        {{ $message->message }}
                                    </div>
                                    <span class="message-time">{{ $message->created_at->format('H:i') }}</span>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
                <!-- Form gửi tin nhắn -->
                <div class="chat-input-container">
                    <form id="chat-form" class="chat-input-wrapper">
                        <textarea class="chat-input" id="message-input" placeholder="Nhập tin nhắn..." rows="1"></textarea>
                        <div class="chat-input-actions">
                            <label class="chat-input-action" title="Gửi file">
                                <i class="fas fa-paperclip"></i>
                                <input type="file" id="file-input" style="display: none;">
                            </label>
                            <button type="submit" class="send-button" id="send-button" disabled>
                                <i class="fas fa-paper-plane"></i>
                                <span>Gửi</span>
                            </button>
                        </div>
                    </form>
                </div>
            @else
                <div class="chat-empty">
                    <div class="chat-empty-icon">
                        <svg width="64" height="64" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect width="64" height="64" rx="32" fill="#F3F4F6" />
                            <path d="M20 32c0-5.523 5.373-10 12-10s12 4.477 12 10-5.373 10-12 10-12-4.477-12-10Z"
                                fill="#9CA3AF" />
                            <rect x="28" y="28" width="8" height="8" rx="4" fill="#F3F4F6" />
                        </svg>
                    </div>
                    <h2 style="font-size:1.3rem; font-weight:600; color:#22223b; margin-bottom:8px;">Chọn cuộc trò chuyện
                    </h2>
                    <div style="color:var(--text-secondary); font-size:1rem;">Chọn một cuộc trò chuyện để bắt đầu xem và
                        phản hồi</div>
                </div>
            @endif
        </div>
    </div>

    <script>
        window.pusherKey = "{{ config('broadcasting.connections.pusher.key') }}";
        window.pusherCluster = "{{ config('broadcasting.connections.pusher.options.cluster') }}";
    </script>
    <script src="https://js.pusher.com/7.2/pusher.min.js"></script>
    <script src="{{ asset('js/chat.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const messageInput = document.getElementById('message-input');
            const sendBtn = document.getElementById('send-button');
            const chatForm = document.getElementById('chat-form');
            const fileInput = document.getElementById('file-input');
            const chatMessages = document.getElementById('chat-messages');

            // Thêm biến selectedConversationId
            window.selectedConversationId = {{ $selectedConversation ? $selectedConversation->id : 'null' }};

            // Thêm xử lý typing indicator
            let typingTimeout;
            messageInput.addEventListener('input', function() {
                updateSendButtonState();
                autoResizeTextarea();

                // Gửi typing indicator
                if (window.selectedConversationId) {
                    clearTimeout(typingTimeout);
                    sendTypingIndicator(true);

                    // Reset typing status sau 3 giây
                    typingTimeout = setTimeout(() => {
                        sendTypingIndicator(false);
                    }, 3000);
                }
            });

            // Hàm gửi typing indicator
            function sendTypingIndicator(isTyping) {
                if (!window.selectedConversationId) return;

                fetch('{{ route('admin.chat.typing') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content')
                    },
                    body: JSON.stringify({
                        conversation_id: window.selectedConversationId,
                        is_typing: isTyping
                    })
                }).catch(error => console.error('Typing indicator error:', error));
            }

            // Lắng nghe sự kiện typing status
            @foreach ($conversations as $conv)
                pusher.subscribe('chat.{{ $conv->id }}')
                    .bind('typing-status', function(data) {
                        handleTypingStatus(data);
                    });
            @endforeach

            function handleTypingStatus(data) {
                if (data.user_id === {{ auth()->id() }}) return; // Bỏ qua nếu là chính mình

                const typingIndicator = document.querySelector('.typing-indicator');
                if (typingIndicator) {
                    typingIndicator.style.display = data.is_typing ? 'block' : 'none';
                }
            }

            if (messageInput) {
                messageInput.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter' && !e.shiftKey) {
                        e.preventDefault();
                        if (!sendBtn.disabled) {
                            chatForm.dispatchEvent(new Event('submit'));
                        }
                    }
                });
            }

            if (chatForm) {
                chatForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const message = messageInput.value.trim();
                    if (!message) return;

                    const formData = new FormData();
                    formData.append('conversation_id', selectedConversationId);
                    formData.append('message', message);

                    sendBtn.disabled = true;
                    sendBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

                    fetch('{{ route('admin.chat.send') }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                    .getAttribute('content')
                            },
                            body: formData
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                messageInput.value = '';
                                messageInput.style.height = 'auto';
                                updateSendButtonState();
                                // Append luôn tin nhắn vào khung chat
                                if (typeof appendMessageToChat === 'function') {
                                    appendMessageToChat(data.data);
                                    if (typeof scrollToBottom === 'function') scrollToBottom();
                                }
                                // Cập nhật preview, badge, thời gian sidebar
                                updateSidebarAfterSend(data.data);
                            } else {
                                alert('Gửi tin nhắn thất bại: ' + (data.message ||
                                    'Lỗi không xác định'));
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Lỗi gửi tin nhắn: ' + error.message);
                        })
                        .finally(() => {
                            sendBtn.disabled = false;
                            sendBtn.innerHTML = '<i class="fas fa-paper-plane"></i><span>Gửi</span>';
                        });
                });
            }

            if (fileInput) {
                fileInput.addEventListener('change', function(e) {
                    console.log('📎 Handling file upload...');

                    const file = e.target.files[0];
                    if (!file || !selectedConversationId) return;

                    console.log('📎 File selected:', file.name, file.size, 'bytes');

                    const formData = new FormData();
                    formData.append('conversation_id', selectedConversationId);
                    formData.append('attachment', file);

                    sendBtn.disabled = true;
                    sendBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

                    fetch('{{ route('admin.chat.send') }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                    .getAttribute('content')
                            },
                            body: formData
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                e.target.value = '';
                                // Append luôn tin nhắn vào khung chat
                                if (typeof appendMessageToChat === 'function') {
                                    appendMessageToChat(data.data);
                                    if (typeof scrollToBottom === 'function') scrollToBottom();
                                }
                                // Cập nhật preview, badge, thời gian sidebar
                                updateSidebarAfterSend(data.data);
                            } else {
                                alert('Gửi tệp đính kèm thất bại: ' + (data.message ||
                                    'Lỗi không xác định'));
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Lỗi gửi tệp đính kèm: ' + error.message);
                        })
                        .finally(() => {
                            sendBtn.disabled = false;
                            sendBtn.innerHTML = '<i class="fas fa-paper-plane"></i><span>Gửi</span>';
                        });
                });
            }

            function updateSendButtonState() {
                if (messageInput && sendBtn) {
                    const hasText = messageInput.value.trim().length > 0;
                    sendBtn.disabled = !hasText;
                }
            }

            function autoResizeTextarea() {
                if (messageInput) {
                    messageInput.style.height = 'auto';
                    messageInput.style.height = Math.min(messageInput.scrollHeight, 120) + 'px';
                }
            }

            function scrollToBottom() {
                if (chatMessages) {
                    setTimeout(() => {
                        chatMessages.scrollTop = chatMessages.scrollHeight;
                    }, 100);
                }
            }

            const firstConversation = document.querySelector('.chat-item[data-conversation-id]');
            const conversationId = firstConversation ? firstConversation.getAttribute('data-conversation-id') :
                null;
            const userId = document.querySelector('meta[name="user-id"]')?.getAttribute('content') || null;
            const userType = 'admin';

            window.adminChat = new Chat(conversationId, userId, userType);

            document.querySelectorAll('.chat-item').forEach(item => {
                item.addEventListener('click', function() {
                    const conversationId = this.getAttribute('data-conversation-id');
                    if (conversationId) {
                        window.adminChat.conversationId = conversationId;
                        window.adminChat.loadMessages();
                    }
                });
            });

            document.querySelectorAll('.distribution-select').forEach(select => {
                select.addEventListener('change', function() {
                    const conversationId = this.getAttribute('data-conversation-id');
                    const branchId = this.value;

                    if (!confirm('Bạn có chắc muốn phân phối cuộc trò chuyện này?')) {
                        this.selectedIndex = 0;
                        return;
                    }

                    fetch('{{ route('admin.chat.distribute') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector(
                                    'meta[name="csrf-token"]').getAttribute('content'),
                            },
                            body: JSON.stringify({
                                conversation_id: conversationId,
                                branch_id: branchId
                            }),
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                alert('✅ Phân phối thành công!');
                                location.reload();
                            } else {
                                alert('❌ Phân phối thất bại: ' + (data.message ||
                                    'Lỗi không xác định'));
                                this.selectedIndex = 0;
                            }
                        })
                        .catch(error => {
                            console.error('❌ Assignment error:', error);
                            alert('❌ Lỗi phân phối: ' + error.message);
                            this.selectedIndex = 0;
                        });
                });
            });
        });

        const adminId = {{ auth()->id() }};
        const pusher = new Pusher('{{ config('broadcasting.connections.pusher.key') }}', {
            cluster: '{{ config('broadcasting.connections.pusher.options.cluster') }}',
            encrypted: true
        });

        // Lắng nghe tất cả các conversation mà admin có thể thấy
        @foreach ($conversations as $conv)
            pusher.subscribe('chat.{{ $conv->id }}')
                .bind('new-message', function(data) {
                    console.log('Received new message:', data);
                    handleNewMessage(data.message);
                });
        @endforeach

        function handleNewMessage(message) {
            console.log('Handling new message:', message);
            const convId = message.conversation_id;
            const convItem = document.querySelector(`.chat-item[data-conversation-id='${convId}']`);
            const isCurrent = (window.selectedConversationId == convId);

            // Nếu đang mở đúng cuộc trò chuyện
            if (isCurrent && typeof appendMessageToChat === 'function') {
                appendMessageToChat(message);
                if (typeof scrollToBottom === 'function') scrollToBottom();
            }

            // Luôn cập nhật preview ở sidebar
            if (convItem) {
                const preview = convItem.querySelector('.chat-item-preview');
                if (preview) preview.textContent = message.message;
                const time = convItem.querySelector('.chat-item-time');
                if (time) time.textContent = formatTime(message.created_at);

                // Tăng badge số chưa đọc nếu chưa mở
                if (!isCurrent) {
                    let badge = convItem.querySelector('.unread-badge');
                    if (badge) {
                        badge.textContent = parseInt(badge.textContent || 0) + 1;
                        badge.style.display = 'flex';
                    } else {
                        // Nếu chưa có badge, tạo mới
                        const newBadge = document.createElement('div');
                        newBadge.className = 'unread-badge';
                        newBadge.textContent = 1;
                        convItem.querySelector('.chat-item-header').appendChild(newBadge);
                    }
                }
                // Đưa lên đầu danh sách
                if (convItem.parentNode.firstChild !== convItem) {
                    convItem.parentNode.insertBefore(convItem, convItem.parentNode.firstChild);
                }
            }
        }

        function formatTime(timeStr) {
            const d = new Date(timeStr);
            return d.getHours().toString().padStart(2, '0') + ':' + d.getMinutes().toString().padStart(2, '0');
        }

        function updateSidebarAfterSend(message) {
            console.log('Updating sidebar after send:', message);
            const convId = message.conversation_id;
            const convItem = document.querySelector(`.chat-item[data-conversation-id='${convId}']`);
            if (convItem) {
                const preview = convItem.querySelector('.chat-item-preview');
                if (preview) preview.textContent = message.message;
                const time = convItem.querySelector('.chat-item-time');
                if (time) time.textContent = formatTime(message.created_at);
                // Reset badge số chưa đọc về 0 (vì admin vừa gửi)
                let badge = convItem.querySelector('.unread-badge');
                if (badge) {
                    badge.textContent = 0;
                    badge.style.display = 'none';
                }
                // Đưa lên đầu danh sách
                if (convItem.parentNode.firstChild !== convItem) {
                    convItem.parentNode.insertBefore(convItem, convItem.parentNode.firstChild);
                }
            }
        }
    </script>
@endsection
