@extends('layouts.customer.layout')

@section('title', 'Chat hỗ trợ')

@section('page-style')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        .chat-container {
            height: 75vh;
            border-radius: 0.75rem;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            background-color: #fff;
        }

        .chat-sidebar {
            height: 100%;
            overflow-y: auto;
            border-right: 1px solid var(--border-color);
            background-color: #f9fafb;
        }

        .chat-main {
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .chat-header {
            padding: 1rem 1.5rem;
            background-color: #fff;
            border-bottom: 1px solid var(--border-color);
        }

        .chat-messages {
            flex: 1;
            overflow-y: auto;
            padding: 1.5rem;
            background-color: #f9fafb;
        }

        .chat-input {
            padding: 1rem 1.5rem;
            background-color: #fff;
            border-top: 1px solid var(--border-color);
        }

        .conversation-item {
            padding: 1rem;
            border-bottom: 1px solid var(--border-color);
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .conversation-item:hover {
            background-color: #f3f4f6;
        }

        .conversation-item.active {
            background-color: #eff6ff;
            border-left: 4px solid var(--primary-color);
        }

        .message {
            margin-bottom: 1.5rem;
            max-width: 80%;
            display: flex;
            flex-direction: column;
        }

        .message.sent {
            margin-left: auto;
            align-items: flex-end;
        }

        .message.received {
            margin-right: auto;
            align-items: flex-start;
        }

        .message-bubble {
            padding: 0.75rem 1rem;
            border-radius: 1rem;
            position: relative;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        }

        .message.sent .message-bubble {
            background-color: var(--primary-color);
            color: white;
            border-bottom-right-radius: 0.25rem;
        }

        .message.received .message-bubble {
            background-color: white;
            color: var(--text-primary);
            border: 1px solid var(--border-color);
            border-bottom-left-radius: 0.25rem;
        }

        .message-info {
            font-size: 0.75rem;
            color: var(--text-secondary);
            margin-top: 0.25rem;
        }

        .status-badge {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
            border-radius: 9999px;
            font-weight: 600;
        }

        .status-new {
            background-color: #fef3c7;
            color: #92400e;
        }

        .status-distributed {
            background-color: #dbeafe;
            color: #1e40af;
        }

        .status-active {
            background-color: #d1fae5;
            color: #065f46;
        }

        .status-closed {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .empty-state {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100%;
            color: var(--text-secondary);
            text-align: center;
            padding: 2rem;
        }

        .attachment-preview {
            margin-top: 0.5rem;
        }

        .attachment-preview img {
            max-width: 200px;
            border-radius: 0.5rem;
        }

        .system-message {
            text-align: center;
            margin: 1rem 0;
            padding: 0.5rem 1rem;
            background-color: #f3f4f6;
            border-radius: 9999px;
            color: var(--text-secondary);
            font-size: 0.875rem;
            display: inline-block;
            margin-left: auto;
            margin-right: auto;
        }

        .new-chat-btn {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            width: 3.5rem;
            height: 3.5rem;
            border-radius: 50%;
            background-color: var(--primary-color);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: all 0.2s;
            z-index: 100;
        }

        .new-chat-btn:hover {
            transform: scale(1.05);
            background-color: #2563eb;
            color: white;
        }

        .typing-indicator {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
        }

        .typing-indicator span {
            height: 8px;
            width: 8px;
            background-color: #bbb;
            border-radius: 50%;
            display: inline-block;
            margin-right: 3px;
            animation: typing 1s infinite;
        }

        .typing-indicator span:nth-child(2) {
            animation-delay: 0.2s;
        }

        .typing-indicator span:nth-child(3) {
            animation-delay: 0.4s;
        }

        @keyframes typing {
            0% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-5px);
            }

            100% {
                transform: translateY(0);
            }
        }

        @media (max-width: 768px) {
            .chat-container {
                height: auto;
            }

            .chat-sidebar {
                height: 300px;
                border-right: none;
                border-bottom: 1px solid var(--border-color);
            }

            .chat-main {
                height: 500px;
            }

            .message {
                max-width: 90%;
            }
        }
    </style>
@endsection

@section('content')
    <div class="row mb-4">
        <div class="col">
            <h2 class="mb-0">Chat hỗ trợ</h2>
            <p class="text-muted">Trò chuyện với đội ngũ hỗ trợ của chúng tôi</p>
        </div>
    </div>

    <div class="chat-container">
        <div class="row g-0 h-100">
            <!-- Sidebar - Conversations List -->
            <div class="col-md-4 chat-sidebar">
                <div class="p-3 bg-white border-bottom">
                    <h5 class="mb-0">Cuộc trò chuyện của bạn</h5>
                    <p class="text-muted small mb-0">{{ $conversations->count() }} cuộc trò chuyện</p>
                </div>
                <div id="conversations-list">
                    @forelse($conversations as $conversation)
                        <div class="conversation-item {{ $loop->first ? 'active' : '' }}"
                            data-conversation-id="{{ $conversation->id }}"
                            onclick="selectConversation({{ $conversation->id }})">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <strong>Cuộc trò chuyện #{{ $conversation->id }}</strong>
                                    <div class="text-muted small">
                                        {{ $conversation->created_at->format('d/m/Y H:i') }}
                                    </div>
                                </div>
                                @if ($conversation->messages->where('is_read', false)->where('sender_id', '!=', auth()->id())->count() > 0)
                                    <span class="badge bg-danger rounded-pill">
                                        {{ $conversation->messages->where('is_read', false)->where('sender_id', '!=', auth()->id())->count() }}
                                    </span>
                                @endif
                            </div>

                            <div class="text-truncate small mb-2">
                                @if ($conversation->messages->last())
                                    {{ Str::limit($conversation->messages->last()->message ?? ($conversation->messages->last()->content ?? 'Tệp đính kèm'), 40) }}
                                @else
                                    Cuộc trò chuyện mới
                                @endif
                            </div>

                            <div class="d-flex justify-content-between align-items-center">
                                <span class="status-badge status-{{ $conversation->status }}">
                                    @switch($conversation->status)
                                        @case('new')
                                            Chờ xử lý
                                        @break

                                        @case('distributed')
                                            Đã phân phối
                                        @break

                                        @case('closed')
                                            Đã đóng
                                        @break

                                        @default
                                            {{ ucfirst($conversation->status) }}
                                    @endswitch
                                </span>

                                @if ($conversation->branch)
                                    <span class="badge bg-light text-dark">
                                        <i class="fas fa-building me-1"></i>{{ $conversation->branch->name }}
                                    </span>
                                @endif
                            </div>
                        </div>
                        @empty
                            <div class="empty-state">
                                <i class="fas fa-comments fa-3x mb-3 text-muted"></i>
                                <h5>Chưa có cuộc trò chuyện</h5>
                                <p>Bắt đầu cuộc trò chuyện mới để được hỗ trợ</p>
                                <button class="btn btn-primary mt-2" data-bs-toggle="modal" data-bs-target="#newChatModal">
                                    <i class="fas fa-plus me-2"></i>Tạo cuộc trò chuyện
                                </button>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Main Chat Area -->
                <div class="col-md-8 chat-main">
                    @if ($conversations->count() > 0 && $conversations->first())
                        <!-- Chat Header -->
                        <div class="chat-header">
                            @php $firstConversation = $conversations->first(); @endphp
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="mb-1">Cuộc trò chuyện #{{ $firstConversation->id }}</h5>
                                    <div class="d-flex align-items-center">
                                        <span class="status-badge status-{{ $firstConversation->status }} me-2">
                                            @switch($firstConversation->status)
                                                @case('new')
                                                    Chờ xử lý
                                                @break

                                                @case('distributed')
                                                    Đã phân phối
                                                @break

                                                @case('closed')
                                                    Đã đóng
                                                @break

                                                @default
                                                    {{ ucfirst($firstConversation->status) }}
                                            @endswitch
                                        </span>
                                        @if ($firstConversation->branch)
                                            <span class="text-muted small">
                                                <i class="fas fa-building me-1"></i>{{ $firstConversation->branch->name }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div>
                                    <span class="badge bg-light text-dark">
                                        <i class="fas fa-calendar-alt me-1"></i>
                                        {{ $firstConversation->created_at->format('d/m/Y') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <!-- Chat Messages -->
                        <div class="chat-messages" id="chat-messages">
                            @if ($firstConversation && $firstConversation->messages->count() > 0)
                                @foreach ($firstConversation->messages as $message)
                                    @php
                                        $isSent = $message->sender_id === auth()->id();
                                        $sender = $message->sender;
                                        $isSystem = $message->is_system_message ?? false;
                                    @endphp
                                    @if ($isSystem)
                                        <div class="system-message">
                                            <i class="fas fa-info-circle me-1"></i>{{ $message->message ?? $message->content }}
                                        </div>
                                    @else
                                        <div class="message {{ $isSent ? 'sent' : 'received' }}">
                                            <div class="message-bubble">
                                                @if ($message->message ?? $message->content)
                                                    <div>{!! nl2br(e($message->message ?? $message->content)) !!}</div>
                                                @endif
                                                @if ($message->attachment)
                                                    <div class="attachment-preview">
                                                        @if ($message->attachment_type === 'image')
                                                            <img src="{{ asset('storage/' . $message->attachment) }}"
                                                                alt="attachment" class="img-fluid rounded">
                                                        @else
                                                            <a href="{{ asset('storage/' . $message->attachment) }}"
                                                                class="btn btn-sm btn-light" target="_blank">
                                                                <i class="fas fa-file me-2"></i>Tải tệp
                                                            </a>
                                                        @endif
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="message-info">
                                                <span>{{ $isSent ? 'Bạn' : $sender->name ?? ($sender->full_name ?? 'Nhân viên hỗ trợ') }}</span>
                                                ·
                                                <span>{{ $message->created_at->format('H:i') }}</span>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            @else
                                <div class="empty-state">
                                    <i class="fas fa-comment fa-3x mb-3 text-muted"></i>
                                    <h5>Chưa có tin nhắn</h5>
                                    <p>Hãy bắt đầu cuộc trò chuyện</p>
                                </div>
                            @endif
                        </div>
                        <!-- Chat Input -->
                        <div class="chat-input">
                            <form id="chat-form" class="mb-2">
                                <div class="input-group">
                                    <textarea class="form-control" id="message" placeholder="Nhập tin nhắn..." rows="2"></textarea>
                                    <input type="file" id="attachment" class="d-none"
                                        accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.txt">
                                    <button type="button" class="btn btn-outline-secondary"
                                        onclick="document.getElementById('attachment').click()">
                                        <i class="fas fa-paperclip"></i>
                                    </button>
                                    <button type="submit" class="btn btn-primary" id="send-btn">
                                        <i class="fas fa-paper-plane"></i> Gửi
                                    </button>
                                </div>
                            </form>
                            <div id="attachment-preview"></div>
                            <div class="text-muted small">
                                <i class="fas fa-info-circle me-1"></i>
                                Thời gian phản hồi thông thường: 5-10 phút trong giờ làm việc
                            </div>
                        </div>
                    @else
                        <div class="empty-state">
                            <i class="fas fa-comments fa-3x mb-3 text-muted"></i>
                            <h5>Chưa có cuộc trò chuyện</h5>
                            <p>Bắt đầu cuộc trò chuyện mới để được hỗ trợ</p>
                            <button class="btn btn-primary mt-2" data-bs-toggle="modal" data-bs-target="#newChatModal">
                                <i class="fas fa-plus me-2"></i>Tạo cuộc trò chuyện
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- New Chat Button (Fixed) -->
        <a href="#" class="new-chat-btn" data-bs-toggle="modal" data-bs-target="#newChatModal">
            <i class="fas fa-plus fa-lg"></i>
        </a>

        <!-- New Chat Modal -->
        <div class="modal fade" id="newChatModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Tạo cuộc trò chuyện mới</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="new-chat-form" action="{{ route('customer.chat.create') }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="new-message" class="form-label">Tin nhắn <span
                                        class="text-danger">*</span></label>
                                <textarea class="form-control" id="new-message" name="message" rows="4" required
                                    placeholder="Nhập nội dung tin nhắn của bạn..."></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="new-attachment" class="form-label">Tệp đính kèm (tùy chọn)</label>
                                <input type="file" class="form-control" id="new-attachment" name="attachment">
                                <div class="form-text">Hỗ trợ: hình ảnh, PDF, Word, Excel (tối đa 10MB)</div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                            <button type="submit" class="btn btn-primary" id="create-chat-btn">
                                <i class="fas fa-paper-plane me-2"></i>Gửi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endsection

    @section('page-script')
        <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
        <script>
            // Error logging function
            function logError(error, context = '') {
                console.error(`[${context}]`, error);
            }

            // Try-catch wrapper
            function tryCatch(fn, context) {
                return function() {
                    try {
                        return fn.apply(this, arguments);
                    } catch (error) {
                        logError(error, context);
                        alert('Đã xảy ra lỗi. Vui lòng thử lại sau.');
                    }
                };
            }

            // Initialize
            try {
                // Global variables
                const currentUserId = {{ auth()->id() }};
                let selectedConversationId = {{ $conversations->first()->id ?? 'null' }};
                const chatMessages = document.getElementById('chat-messages');
                const messageInput = document.getElementById('message');
                const sendBtn = document.getElementById('send-btn');
                const attachmentInput = document.getElementById('attachment');
                const attachmentPreview = document.getElementById('attachment-preview');

                console.log('✅ Initialized with conversation ID:', selectedConversationId);

                // Scroll to bottom
                const scrollToBottom = tryCatch(function() {
                    if (chatMessages) {
                        chatMessages.scrollTop = chatMessages.scrollHeight;
                    }
                }, 'scrollToBottom');

                scrollToBottom();

                // Send message
                const sendMessage = tryCatch(function() {
                    if (!selectedConversationId) {
                        alert('Không có cuộc trò chuyện để gửi tin nhắn.');
                        return;
                    }

                    const message = messageInput.value.trim();
                    const file = attachmentInput.files[0];

                    if (!message && !file) {
                        alert('Vui lòng nhập tin nhắn hoặc chọn tệp.');
                        return;
                    }

                    const formData = new FormData();
                    formData.append('conversation_id', selectedConversationId);
                    formData.append('message', message);
                    if (file) formData.append('attachment', file);

                    // Show loading
                    sendBtn.disabled = true;
                    sendBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang gửi...';

                    fetch('{{ route('customer.chat.send') }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                    'content')
                            },
                            body: formData
                        })
                        .then(res => {
                            if (!res.ok) {
                                throw new Error(`HTTP ${res.status}: ${res.statusText}`);
                            }
                            return res.json();
                        })
                        .then(data => {
                            console.log('Send response:', data);
                            if (data.success) {
                                messageInput.value = '';
                                attachmentInput.value = '';
                                attachmentPreview.innerHTML = '';

                                // Add message to chat immediately
                                addMessageToChat({
                                    id: data.data.id,
                                    sender_id: currentUserId,
                                    message: data.data.message,
                                    attachment: data.data.attachment,
                                    attachment_type: data.data.attachment_type,
                                    created_at: data.data.created_at,
                                    sender_name: '{{ auth()->user()->name ?? auth()->user()->full_name }}'
                                });
                            } else {
                                alert('Gửi tin nhắn thất bại: ' + (data.message || 'Lỗi không xác định'));
                            }
                        })
                        .catch(error => {
                            logError(error, 'sendMessage');
                            alert('Lỗi gửi tin nhắn! Vui lòng thử lại sau.');
                        })
                        .finally(() => {
                            sendBtn.disabled = false;
                            sendBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Gửi';
                        });
                }, 'sendMessage');

                // Add message to chat
                function addMessageToChat(data) {
                    const isCurrentUser = data.sender_id === currentUserId;

                    // Check if it's a system message
                    if (data.is_system_message) {
                        const systemDiv = document.createElement('div');
                        systemDiv.className = 'system-message';
                        systemDiv.innerHTML = `<i class="fas fa-info-circle me-1"></i>${data.message}`;
                        chatMessages.appendChild(systemDiv);
                    } else {
                        const messageDiv = document.createElement('div');
                        messageDiv.classList.add('message', isCurrentUser ? 'sent' : 'received');

                        let messageContent = `<div class="message-bubble">`;

                        if (data.message) {
                            messageContent += `<div>${data.message.replace(/\n/g, '<br>')}</div>`;
                        }

                        if (data.attachment) {
                            messageContent += `<div class="attachment-preview">`;
                            if (data.attachment_type === 'image') {
                                messageContent +=
                                    `<img src="/storage/${data.attachment}" alt="attachment" class="img-fluid rounded">`;
                            } else {
                                messageContent += `<a href="/storage/${data.attachment}" class="btn btn-sm btn-light" target="_blank">
                                <i class="fas fa-file me-2"></i>Tải tệp
                            </a>`;
                            }
                            messageContent += `</div>`;
                        }

                        messageContent += `</div>
                <div class="message-info">
                    <span>${isCurrentUser ? 'Bạn' : (data.sender_name || 'Nhân viên hỗ trợ')}</span> · 
                    <span>${new Date(data.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}</span>
                </div>`;

                        messageDiv.innerHTML = messageContent;
                        chatMessages.appendChild(messageDiv);
                    }

                    scrollToBottom();
                }

                // Form submit
                document.getElementById('chat-form')?.addEventListener('submit', tryCatch(function(e) {
                    e.preventDefault();
                    sendMessage();
                }, 'chat-form submit'));

                // Select conversation
                window.selectConversation = tryCatch(function(conversationId) {
                    if (!conversationId) {
                        console.warn('No conversation ID provided');
                        return;
                    }

                    selectedConversationId = conversationId;
                    console.log('Selected conversation:', conversationId);

                    // Update active conversation
                    const conversationItems = document.querySelectorAll('.conversation-item');
                    if (!conversationItems.length) {
                        console.warn('No conversation items found');
                        return;
                    }

                    conversationItems.forEach(item => {
                        item.classList.remove('active');
                    });

                    const selectedItem = document.querySelector(`[data-conversation-id="${conversationId}"]`);
                    if (!selectedItem) {
                        console.warn(`Conversation item with ID ${conversationId} not found`);
                        return;
                    }

                    selectedItem.classList.add('active');

                    // Load conversation details and messages
                    const url = `{{ route('customer.chat.messages') }}?conversation_id=${conversationId}`;
                    console.log('Fetching messages from:', url);

                    fetch(url)
                        .then(res => {
                            console.log('Response status:', res.status);
                            if (!res.ok) {
                                return res.json().then(err => {
                                    console.error('Error response:', err);
                                    throw new Error(err.message || `HTTP ${res.status}: ${res.statusText}`);
                                });
                            }
                            return res.json();
                        })
                        .then(data => {
                            console.log('Response data:', data);
                            if (data.success && data.conversation) {
                                // Update header
                                const chatHeader = document.querySelector('.chat-header');
                                if (!chatHeader) {
                                    console.warn('Chat header element not found');
                                    return;
                                }

                                chatHeader.innerHTML = `
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h5 class="mb-1">Cuộc trò chuyện #${data.conversation.id}</h5>
                                        <div class="d-flex align-items-center">
                                            <span class="status-badge status-${data.conversation.status} me-2">
                                                ${getStatusLabel(data.conversation.status)}
                                            </span>
                                            ${data.conversation.branch ? 
                                                `<span class="text-muted small">
                                                                                                                                                    <i class="fas fa-building me-1"></i>${data.conversation.branch.name}
                                                                                                                                                </span>` : ''}
                                        </div>
                                    </div>
                                    <div>
                                        <span class="badge bg-light text-dark">
                                            <i class="fas fa-calendar-alt me-1"></i>
                                            ${new Date(data.conversation.created_at).toLocaleDateString()}
                                        </span>
                                    </div>
                                </div>
                            `;

                                // Update messages
                                const chatMessages = document.getElementById('chat-messages');
                                if (!chatMessages) {
                                    console.warn('Chat messages element not found');
                                    return;
                                }

                                chatMessages.innerHTML = '';
                                if (data.messages && data.messages.length) {
                                    data.messages.forEach(message => {
                                        addMessageToChat({
                                            ...message,
                                            sender_name: message.sender.name || message.sender
                                                .full_name
                                        });
                                    });
                                } else {
                                    chatMessages.innerHTML = `
                                    <div class="empty-state">
                                        <i class="fas fa-comment fa-3x mb-3 text-muted"></i>
                                        <h5>Chưa có tin nhắn</h5>
                                        <p>Hãy bắt đầu cuộc trò chuyện</p>
                                    </div>
                                `;
                                }
                            } else {
                                console.error('Invalid response data:', data);
                                alert('Không thể tải cuộc trò chuyện: ' + (data.message || 'Lỗi không xác định'));
                            }
                        })
                        .catch(error => {
                            console.error('Fetch error:', error);
                            logError(error, 'loadConversation');
                            alert('Lỗi tải cuộc trò chuyện: ' + error.message);
                        });
                }, 'selectConversation');

                // Helper function to get status label
                function getStatusLabel(status) {
                    switch (status) {
                        case 'new':
                            return 'Chờ xử lý';
                        case 'distributed':
                            return 'Đã phân phối';
                        case 'closed':
                            return 'Đã đóng';
                        default:
                            return status.charAt(0).toUpperCase() + status.slice(1);
                    }
                }

                // File attachment
                attachmentInput.addEventListener('change', tryCatch(function(e) {
                    const file = e.target.files[0];
                    if (!file) {
                        attachmentPreview.innerHTML = '';
                        return;
                    }

                    if (file.type.startsWith('image/')) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            attachmentPreview.innerHTML = `
                    <div class="alert alert-info d-flex align-items-center">
                        <img src="${e.target.result}" alt="Preview" style="height: 40px; width: auto; margin-right: 10px;">
                        <div>
                            <strong>${file.name}</strong> (${(file.size / 1024).toFixed(2)} KB)
                            <button type="button" class="btn-close ms-3" onclick="clearAttachment()"></button>
                        </div>
                    </div>
                `;
                        };
                        reader.readAsDataURL(file);
                    } else {
                        attachmentPreview.innerHTML = `
                <div class="alert alert-info d-flex align-items-center">
                    <i class="fas fa-file fa-2x me-3"></i>
                    <div>
                        <strong>${file.name}</strong> (${(file.size / 1024).toFixed(2)} KB)
                        <button type="button" class="btn-close ms-3" onclick="clearAttachment()"></button>
                    </div>
                </div>
            `;
                    }
                }, 'attachment'));

                // Clear attachment
                window.clearAttachment = function() {
                    attachmentInput.value = '';
                    attachmentPreview.innerHTML = '';
                };

                // Pusher
                try {
                    if ('{{ config('broadcasting.connections.pusher.key') }}') {
                        const pusher = new Pusher('{{ config('broadcasting.connections.pusher.key') }}', {
                            cluster: '{{ config('broadcasting.connections.pusher.options.cluster') }}',
                            encrypted: true
                        });

                        const channel = pusher.subscribe('chat.{{ $conversation->id ?? '' }}');
                        channel.bind('new-message', function(data) {
                            console.log('New message received:', data);
                            if (data.conversation_id == selectedConversationId && data.sender_id !== currentUserId) {
                                addMessageToChat(data);
                            }
                        });

                        pusher.connection.bind('connected', function() {
                            console.log('✅ Pusher connected');
                        });

                        pusher.connection.bind('error', function(err) {
                            logError(err, 'Pusher');
                        });
                    } else {
                        console.warn('⚠️ Pusher not configured');
                    }
                } catch (error) {
                    logError(error, 'Pusher setup');
                }

                // Handle new chat form submission
                document.getElementById('new-chat-form')?.addEventListener('submit', tryCatch(function(e) {
                    e.preventDefault();

                    const form = e.target;
                    const submitBtn = form.querySelector('#create-chat-btn');
                    const formData = new FormData(form);

                    // Disable submit button and show loading
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Đang tạo...';

                    fetch(form.action, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                    'content')
                            },
                            body: formData
                        })
                        .then(res => {
                            if (!res.ok) {
                                return res.json().then(err => {
                                    const errorMessage = err.message || (err.errors ? Object.values(err
                                            .errors).flat().join(', ') :
                                        'Không thể tạo cuộc trò chuyện mới');
                                    throw new Error(errorMessage);
                                });
                            }
                            return res.json();
                        })
                        .then(data => {
                            if (data.success) {
                                // Show success message
                                const toast = document.createElement('div');
                                toast.className = 'position-fixed top-0 end-0 p-3';
                                toast.style.zIndex = '1050';
                                toast.innerHTML = `
                                <div class="toast show" role="alert" aria-live="assertive" aria-atomic="true">
                                    <div class="toast-header bg-success text-white">
                                        <i class="fas fa-check-circle me-2"></i>
                                        <strong class="me-auto">Thành công</strong>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
                                    </div>
                                    <div class="toast-body">
                                        Cuộc trò chuyện mới đã được tạo thành công!
                                    </div>
                                </div>
                            `;
                                document.body.appendChild(toast);

                                // Close modal
                                const modal = bootstrap.Modal.getInstance(document.getElementById(
                                    'newChatModal'));
                                modal.hide();

                                // Reload page after 1.5 seconds
                                setTimeout(() => {
                                    window.location.reload();
                                }, 1500);
                            } else {
                                throw new Error(data.message || 'Không thể tạo cuộc trò chuyện mới');
                            }
                        })
                        .catch(error => {
                            logError(error, 'createNewChat');
                            alert('Lỗi tạo cuộc trò chuyện: ' + error.message);
                        })
                        .finally(() => {
                            // Re-enable submit button
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = '<i class="fas fa-paper-plane me-2"></i>Gửi';
                        });
                }, 'new-chat-form submit'));

                const customerId = {{ auth()->id() }};
                const pusher = new Pusher('{{ config('broadcasting.connections.pusher.key') }}', {
                    cluster: '{{ config('broadcasting.connections.pusher.options.cluster') }}',
                    encrypted: true
                });

                // Lắng nghe tất cả các conversation mà customer có thể thấy
                @foreach ($conversations as $conv)
                    pusher.subscribe('chat.{{ $conv->id }}')
                        .bind('new-message', function(data) {
                            handleNewMessage(data.message);
                        });
                @endforeach

                function handleNewMessage(message) {
                    const convId = message.conversation_id;
                    const convItem = document.querySelector(`.conversation-item[data-conversation-id='${convId}']`);
                    const isCurrent = (window.selectedConversationId == convId); // Cần set biến này khi click vào chat

                    // Nếu đang mở đúng cuộc trò chuyện
                    if (isCurrent && typeof appendMessageToChat === 'function') {
                        appendMessageToChat(message);
                        if (typeof scrollToBottom === 'function') scrollToBottom();
                    }

                    // Luôn cập nhật preview ở sidebar
                    if (convItem) {
                        // Cập nhật preview tin nhắn
                        const preview = convItem.querySelector('.text-truncate');
                        if (preview) preview.textContent = message.message;
                        // Cập nhật thời gian
                        const time = convItem.querySelector('.text-muted.small');
                        if (time) time.textContent = formatTime(message.sent_at);
                        // Tăng badge số chưa đọc nếu chưa mở
                        if (!isCurrent) {
                            let badge = convItem.querySelector('.badge.bg-danger');
                            if (badge) {
                                badge.textContent = parseInt(badge.textContent || 0) + 1;
                                badge.style.display = 'inline-block';
                            } else {
                                // Nếu chưa có badge, tạo mới
                                const newBadge = document.createElement('span');
                                newBadge.className = 'badge bg-danger rounded-pill';
                                newBadge.textContent = 1;
                                convItem.querySelector('.d-flex.justify-content-between.align-items-start').appendChild(
                                    newBadge);
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

            } catch (error) {
                logError(error, 'Initialization');
            }
        </script>
    @endsection
