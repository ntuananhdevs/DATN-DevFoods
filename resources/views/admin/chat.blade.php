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
        }

        .chat-header {
            padding: 16px 24px;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
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
            padding: 24px;
            overflow-y: auto;
            background-color: #f9fafb;
        }

        .message-group {
            margin-bottom: 24px;
        }

        .message-sender {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 4px;
        }

        .message-sender-name {
            font-weight: 500;
            font-size: 0.875rem;
        }

        .message-sender-type {
            font-size: 0.75rem;
            color: var(--text-muted);
        }

        .message-time {
            font-size: 0.75rem;
            color: var(--text-muted);
            margin-left: 8px;
        }

        .message-bubble {
            padding: 12px 16px;
            border-radius: 8px;
            max-width: 80%;
            font-size: 0.9375rem;
            line-height: 1.5;
        }

        .message-customer {
            background-color: #f3f4f6;
            color: var(--text-primary);
            align-self: flex-start;
            border-bottom-left-radius: 2px;
        }

        .message-admin {
            background-color: var(--primary-color);
            color: white;
            align-self: flex-end;
            border-bottom-right-radius: 2px;
            margin-left: auto;
        }

        .chat-input-container {
            padding: 16px 24px;
            border-top: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .chat-input {
            flex: 1;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 12px 16px;
            font-size: 0.9375rem;
            resize: none;
            height: 48px;
            max-height: 120px;
            overflow-y: auto;
        }

        .chat-input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.1);
        }

        .chat-send-btn {
            width: 48px;
            height: 48px;
            border-radius: 8px;
            background-color: var(--primary-color);
            color: white;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background-color 0.2s;
        }

        .chat-send-btn:hover {
            background-color: var(--primary-hover);
        }

        .chat-send-btn:disabled {
            background-color: #e5e7eb;
            cursor: not-allowed;
        }

        .chat-tools-btn {
            width: 48px;
            height: 48px;
            border-radius: 8px;
            background-color: white;
            color: var(--text-secondary);
            border: 1px solid var(--border-color);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
        }

        .chat-tools-btn:hover {
            background-color: var(--bg-hover);
            color: var(--text-primary);
        }

        .distribution-select {
            width: 100%;
            padding: 10px 12px;
            border-radius: 6px;
            border: 1px solid var(--border-color);
            font-size: 0.875rem;
            color: var(--text-primary);
            background-color: white;
            margin-top: 16px;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%236b7280'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 12px center;
            background-size: 16px;
            padding-right: 36px;
        }

        .distribution-select:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.1);
        }

        .empty-state {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100%;
            color: var(--text-muted);
            text-align: center;
            padding: 2rem;
        }

        .empty-state i {
            font-size: 3rem;
            margin-bottom: 1rem;
            color: #d1d5db;
        }

        .empty-state h3 {
            font-size: 1.25rem;
            font-weight: 500;
            margin-bottom: 0.5rem;
            color: var(--text-primary);
        }

        .empty-state p {
            font-size: 0.875rem;
        }

        .loading-spinner {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .loading-spinner i {
            font-size: 2rem;
            color: var(--primary-color);
        }

        /* Custom scrollbar */
        .chat-list::-webkit-scrollbar,
        .chat-messages::-webkit-scrollbar,
        .chat-input::-webkit-scrollbar {
            width: 6px;
        }

        .chat-list::-webkit-scrollbar-track,
        .chat-messages::-webkit-scrollbar-track,
        .chat-input::-webkit-scrollbar-track {
            background: transparent;
        }

        .chat-list::-webkit-scrollbar-thumb,
        .chat-messages::-webkit-scrollbar-thumb,
        .chat-input::-webkit-scrollbar-thumb {
            background-color: #d1d5db;
            border-radius: 20px;
        }

        .chat-list::-webkit-scrollbar-thumb:hover,
        .chat-messages::-webkit-scrollbar-thumb:hover,
        .chat-input::-webkit-scrollbar-thumb:hover {
            background-color: #9ca3af;
        }
    </style>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="chat-container">
        <!-- Chat Sidebar -->
        <div class="chat-sidebar">
            <div class="chat-sidebar-header">
                <h2>Super Admin Dashboard</h2>
                <p>Quản lý và phân phối chat</p>
            </div>
            <div class="chat-list">
                @forelse($conversations as $conversation)
                    @php
                        $unreadCount = $conversation->messages->where('is_read', false)->count();
                        $firstLetter = strtoupper(
                            substr($conversation->customer->full_name ?? ($conversation->customer->name ?? 'K'), 0, 1),
                        );
                    @endphp
                    <div class="chat-item {{ $loop->first ? 'active' : '' }}" data-conversation-id="{{ $conversation->id }}"
                        data-customer-name="{{ $conversation->customer->full_name ?? ($conversation->customer->name ?? 'Khách hàng') }}"
                        data-customer-email="{{ $conversation->customer->email ?? 'test@customer.com' }}"
                        data-status="{{ $conversation->status }}">
                        @if ($unreadCount > 0)
                            <div class="unread-badge">{{ $unreadCount }}</div>
                        @endif
                        <div class="chat-item-header">
                            <h3 class="chat-item-name">
                                {{ $conversation->customer->full_name ?? ($conversation->customer->name ?? 'Khách hàng') }}
                            </h3>
                            <span class="chat-item-time">{{ $conversation->updated_at->format('H:i') }}</span>
                        </div>
                        <p class="chat-item-email">{{ $conversation->customer->email ?? 'test@customer.com' }}</p>
                        <p class="chat-item-preview">
                            @if ($conversation->messages->last())
                                {{ Str::limit($conversation->messages->last()->content ?? ($conversation->messages->last()->message ?? 'Tệp đính kèm'), 60) }}
                            @else
                                {{ $conversation->subject ?? 'Cuộc trò chuyện mới' }}
                            @endif
                        </p>
                        <div class="chat-item-footer">
                            <div class="chat-item-badges">
                                @switch($conversation->status)
                                    @case('new')
                                        <span class="badge badge-waiting">Chờ xử lý</span>
                                        <span class="badge badge-high">Cao</span>
                                    @break

                                    @case('distributed')
                                        <span class="badge badge-distributed">Đã phân công</span>
                                    @break

                                    @case('closed')
                                        <span class="badge badge-waiting">Đã đóng</span>
                                    @break

                                    @default
                                        <span class="badge badge-waiting">Đang xử lý</span>
                                @endswitch

                            </div>
                        </div>

                        @if ($conversation->status === 'new')
                            <select class="distribution-select" data-conversation-id="{{ $conversation->id }}">
                                <option value="" disabled selected>Phân công cho chi nhánh</option>
                                @foreach ($branches as $branch)
                                    <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                @endforeach
                            </select>
                        @endif
                    </div>
                    @empty
                        <div class="empty-state">
                            <i class="fas fa-inbox"></i>
                            <h3>Không có cuộc trò chuyện</h3>
                            <p>Chưa có cuộc trò chuyện nào được tạo</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Chat Main Area -->
            <div class="chat-main" id="chat-main">
                @if ($conversations->count() > 0)
                    @php $firstConversation = $conversations->first(); @endphp
                    <!-- Chat Header -->
                    <div class="chat-header" id="chat-header">
                        <div class="chat-header-user">
                            <div class="chat-avatar" id="chat-avatar">
                                {{ strtoupper(substr($firstConversation->customer->full_name ?? ($firstConversation->customer->name ?? 'K'), 0, 1)) }}
                            </div>
                            <div class="chat-header-info">
                                <h3 id="chat-customer-name">
                                    {{ $firstConversation->customer->full_name ?? ($firstConversation->customer->name ?? 'Khách hàng') }}
                                </h3>
                                <p id="chat-customer-email">{{ $firstConversation->customer->email ?? 'test@customer.com' }}
                                </p>
                            </div>
                        </div>
                        <div class="chat-header-actions" id="chat-header-actions">
                            @switch($firstConversation->status)
                                @case('new')
                                    <span class="badge badge-waiting">Chờ xử lý</span>
                                    <span class="badge badge-high">Cao</span>
                                @break

                                @case('distributed')
                                    <span class="badge badge-distributed">Đã phân công</span>
                                @break

                                @case('closed')
                                    <span class="badge badge-waiting">Đã đóng</span>
                                @break

                                @default
                                    <span class="badge badge-waiting">Đang xử lý</span>
                            @endswitch
                        </div>
                    </div>

                    <!-- Chat Messages -->
                    <div class="chat-messages" id="chat-messages">
                        <!-- Messages will be loaded here via AJAX -->
                    </div>

                    <!-- Chat Input -->
                    <div class="chat-input-container">
                        <button class="chat-tools-btn" id="attachFileBtn">
                            <i class="fas fa-paperclip"></i>
                        </button>
                        <textarea id="messageInput" class="chat-input" placeholder="Nhập tin nhắn..." rows="1"></textarea>
                        <button id="sendBtn" class="chat-send-btn" disabled>
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>
                @else
                    <div class="empty-state">
                        <i class="fas fa-comments"></i>
                        <h3>Chọn cuộc trò chuyện</h3>
                        <p>Chọn một cuộc trò chuyện từ danh sách để bắt đầu nhắn tin</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Hidden file input -->
        <input type="file" id="fileInput" style="display: none;" accept="image/*,.pdf,.doc,.docx,.txt,.zip,.rar">

        <script src="{{ asset('js/chat.js') }}" defer></script>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                console.log('🚀 Admin Chat Dashboard Loading...');

                // Global variables
                const currentUserId = 1;
                let selectedConversationId = {{ $conversations->first()->id ?? 'null' }};
                let currentConversationData = null;

                // DOM elements
                const chatMessages = document.getElementById('chat-messages');
                const messageInput = document.getElementById('messageInput');
                const sendBtn = document.getElementById('sendBtn');
                const attachFileBtn = document.getElementById('attachFileBtn');
                const fileInput = document.getElementById('fileInput');
                const chatHeader = document.getElementById('chat-header');
                const chatAvatar = document.getElementById('chat-avatar');
                const chatCustomerName = document.getElementById('chat-customer-name');
                const chatCustomerEmail = document.getElementById('chat-customer-email');
                const chatHeaderActions = document.getElementById('chat-header-actions');

                console.log('✅ Initialized with conversation ID:', selectedConversationId);

                // Initialize
                init();

                function init() {
                    setupEventListeners();
                    if (selectedConversationId) {
                        loadConversation(selectedConversationId);
                    }
                    updateSendButtonState();
                }

                function setupEventListeners() {
                    console.log('🔧 Setting up event listeners...');

                    // Chat item clicks
                    document.querySelectorAll('.chat-item').forEach(item => {
                        item.addEventListener('click', function(e) {
                            // Don't trigger if clicking on select dropdown
                            if (e.target.tagName === 'SELECT' || e.target.tagName === 'OPTION') {
                                return;
                            }

                            const conversationId = this.getAttribute('data-conversation-id');
                            if (conversationId && conversationId !== selectedConversationId) {
                                selectConversation(conversationId, this);
                            }
                        });
                    });

                    // Distribution select changes
                    document.querySelectorAll('.distribution-select').forEach(select => {
                        select.addEventListener('change', function(e) {
                            e.stopPropagation(); // Prevent triggering chat item click
                            const conversationId = this.getAttribute('data-conversation-id');
                            const branchId = this.value;
                            if (conversationId && branchId) {
                                assignToBranch(conversationId, branchId);
                            }
                        });
                    });

                    // Message input events
                    if (messageInput) {
                        messageInput.addEventListener('input', function() {
                            updateSendButtonState();
                            autoResizeTextarea();
                        });

                        messageInput.addEventListener('keypress', function(e) {
                            if (e.key === 'Enter' && !e.shiftKey) {
                                e.preventDefault();
                                sendMessage();
                            }
                        });
                    }

                    // Send button
                    if (sendBtn) {
                        sendBtn.addEventListener('click', sendMessage);
                    }

                    // File attachment
                    if (attachFileBtn) {
                        attachFileBtn.addEventListener('click', function() {
                            fileInput.click();
                        });
                    }

                    if (fileInput) {
                        fileInput.addEventListener('change', handleFileUpload);
                    }

                    console.log('✅ Event listeners set up successfully');
                }

                function selectConversation(conversationId, itemElement) {
                    console.log('🎯 Selecting conversation:', conversationId);

                    // Update active state
                    document.querySelectorAll('.chat-item').forEach(item => {
                        item.classList.remove('active');
                    });
                    itemElement.classList.add('active');

                    // Update selected conversation ID
                    selectedConversationId = conversationId;

                    // Update chat header with conversation data
                    const customerName = itemElement.getAttribute('data-customer-name');
                    const customerEmail = itemElement.getAttribute('data-customer-email');
                    const status = itemElement.getAttribute('data-status');

                    updateChatHeader(customerName, customerEmail, status);

                    // Load conversation messages
                    loadConversation(conversationId);
                }

                function updateChatHeader(customerName, customerEmail, status) {
                    console.log('📋 Updating chat header:', customerName, customerEmail, status);

                    // Update avatar
                    const firstLetter = customerName.charAt(0).toUpperCase();
                    chatAvatar.textContent = firstLetter;

                    // Update customer info
                    chatCustomerName.textContent = customerName;
                    chatCustomerEmail.textContent = customerEmail;

                    // Update status badges
                    let badgesHtml = '';
                    switch (status) {
                        case 'new':
                            badgesHtml =
                                '<span class="badge badge-waiting">Chờ xử lý</span><span class="badge badge-high">Cao</span>';
                            break;
                        case 'distributed':
                            badgesHtml = '<span class="badge badge-distributed">Đã phân công</span>';
                            break;
                        case 'closed':
                            badgesHtml = '<span class="badge badge-waiting">Đã đóng</span>';
                            break;
                        default:
                            badgesHtml = '<span class="badge badge-waiting">Đang xử lý</span>';
                    }
                    chatHeaderActions.innerHTML = badgesHtml;
                }

                function loadConversation(conversationId) {
                    console.log('📥 Loading conversation:', conversationId);

                    // Show loading spinner
                    chatMessages.innerHTML =
                        '<div class="loading-spinner"><i class="fas fa-spinner fa-spin"></i></div>';

                    // Fetch conversation messages
                    fetch(`/api/conversations/${conversationId}/messages`, {
                            method: 'GET',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                    'content'),
                                'Accept': 'application/json'
                            }
                        })
                        .then(response => {
                            console.log('📡 Load conversation response status:', response.status);
                            return response.json();
                        })
                        .then(data => {
                            console.log('📡 Load conversation response:', data);
                            if (data.success) {
                                currentConversationData = data.data;
                                renderMessages(data.data.messages);
                            } else {

                                chatMessages.innerHTML = `
                    <div class="empty-state">
                        <i class="fas fa-exclamation-triangle"></i>
                        <h3>Lỗi tải tin nhắn</h3>
                        <p>${data.message || 'Không thể tải tin nhắn'}</p>
                    </div>
                `;
                            }
                        })
                        .catch(error => {
                            console.error('❌ Load conversation error:', error);
                            chatMessages.innerHTML = `
                <div class="empty-state">
                    <i class="fas fa-exclamation-triangle"></i>
                    <h3>Lỗi kết nối</h3>
                    <p>Không thể tải tin nhắn. Vui lòng thử lại.</p>
                </div>
            `;
                        });
                }

                function renderMessages(messages) {
                    console.log('🎨 Rendering messages:', messages.length);

                    if (!messages || messages.length === 0) {
                        chatMessages.innerHTML = `
                <div class="empty-state">
                    <i class="fas fa-comment"></i>
                    <h3>Chưa có tin nhắn</h3>
                    <p>Hãy bắt đầu cuộc trò chuyện</p>
                </div>
            `;
                        return;
                    }

                    // Group messages by sender
                    const messageGroups = [];
                    let currentGroup = [];
                    let currentSenderId = null;

                    messages.forEach(message => {
                        if (currentSenderId !== message.sender_id) {
                            if (currentGroup.length > 0) {
                                messageGroups.push(currentGroup);
                                currentGroup = [];
                            }
                            currentSenderId = message.sender_id;
                        }
                        currentGroup.push(message);
                    });

                    if (currentGroup.length > 0) {
                        messageGroups.push(currentGroup);
                    }

                    // Render message groups
                    let messagesHtml = '';
                    messageGroups.forEach(group => {
                        const firstMessage = group[0];
                        const isAdmin = firstMessage.sender_id === currentUserId;
                        const senderName = isAdmin ? 'Admin' : (firstMessage.sender?.full_name || firstMessage
                            .sender?.name || 'Khách hàng');
                        const firstLetter = senderName.charAt(0).toUpperCase();

                        messagesHtml += `
                <div class="message-group">
                    <div class="message-sender">
                        <div class="chat-avatar" style="${isAdmin ? 'background-color: #3b82f6; color: white;' : ''}">
                            ${firstLetter}
                        </div>
                        <span class="message-sender-name">${escapeHtml(senderName)}</span>
                        ${!isAdmin ? '<span class="message-sender-type">Khách hàng</span>' : ''}
                    </div>
            `;

                        group.forEach(message => {
                            const messageTime = new Date(message.created_at).toLocaleTimeString([], {
                                hour: '2-digit',
                                minute: '2-digit'
                            });
                            messagesHtml += `
                    <div style="display: flex; margin-bottom: 8px;">
                        <div class="message-bubble ${isAdmin ? 'message-admin' : 'message-customer'}">
                            ${message.content || message.message ? escapeHtml(message.content || message.message) : ''}
                            ${message.attachment || message.file_url ? renderAttachment(message) : ''}
                        </div>
                        <span class="message-time">${messageTime}</span>
                    </div>
                `;
                        });

                        messagesHtml += '</div>';
                    });

                    chatMessages.innerHTML = messagesHtml;
                    scrollToBottom();
                }

                function renderAttachment(message) {
                    const attachment = message.attachment || message.file_url;
                    const attachmentType = message.attachment_type || message.message_type;

                    if (attachmentType === 'image') {
                        return `
                <div style="margin-top: 8px;">
                    <img src="/storage/${attachment}" alt="attachment" 
                         style="max-width: 100%; border-radius: 4px; max-height: 200px;">
                </div>
            `;
                    } else {
                        const fileName = attachment.split('/').pop();
                        return `
                <div style="margin-top: 8px;">
                    <div style="display: flex; align-items: center; gap: 8px; padding: 8px; background-color: rgba(255,255,255,0.1); border-radius: 4px;">
                        <i class="fas fa-file"></i>
                        <span>${escapeHtml(fileName)}</span>
                        <a href="/storage/${attachment}" target="_blank" style="margin-left: auto;">
                            <i class="fas fa-download"></i>
                        </a>
                    </div>
                </div>
            `;
                    }
                }

                function assignToBranch(conversationId, branchId) {
                    console.log('🏢 Assigning conversation', conversationId, 'to branch', branchId);

                    if (!branchId) return;

                    if (!confirm('Bạn có chắc muốn phân phối cuộc trò chuyện này?')) {
                        const select = document.querySelector(`[data-conversation-id="${conversationId}"]`);
                        if (select) select.selectedIndex = 0;
                        return;
                    }

                    fetch('{{ route('admin.chat.distribute') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                    'content')
                            },
                            body: JSON.stringify({
                                conversation_id: conversationId,
                                branch_id: branchId
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                alert('✅ Phân phối thành công!');
                                location.reload(); // Reload to update the sidebar
                            } else {
                                alert('❌ Phân phối thất bại: ' + (data.message || 'Lỗi không xác định'));
                                const select = document.querySelector(`[data-conversation-id="${conversationId}"]`);
                                if (select) select.selectedIndex = 0;
                            }
                        })
                        .catch(error => {
                            console.error('❌ Assignment error:', error);
                            alert('❌ Lỗi phân phối: ' + error.message);
                            const select = document.querySelector(`[data-conversation-id="${conversationId}"]`);
                            if (select) select.selectedIndex = 0;
                        });
                }

                function sendMessage() {
                    console.log('💬 Sending message...');

                    if (!selectedConversationId) {
                        alert('❌ Không có cuộc trò chuyện để gửi tin nhắn.');
                        return;
                    }

                    const message = messageInput.value.trim();
                    if (!message) {
                        alert('❌ Vui lòng nhập tin nhắn.');
                        return;
                    }

                    const formData = new FormData();
                    formData.append('conversation_id', selectedConversationId);
                    formData.append('message', message);

                    // Show loading
                    sendBtn.disabled = true;
                    sendBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

                    fetch('{{ route('admin.chat.send') }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                    'content')
                            },
                            body: formData
                        })
                        .then(response => {
                            console.log('📡 Send message response status:', response.status);
                            return response.json();
                        })
                        .then(data => {
                            console.log('📡 Send message response:', data);
                            if (data.success) {
                                console.log('✅ Message sent successfully');
                                messageInput.value = '';
                                updateSendButtonState();
                                autoResizeTextarea();

                                // Add message to chat immediately
                                addMessageToChat({
                                    id: data.data.id,
                                    sender_id: currentUserId,
                                    message: data.data.message,
                                    created_at: data.data.created_at,
                                    sender: {
                                        name: 'Admin'
                                    }
                                });

                                // Update conversation preview
                                updateConversationPreview(selectedConversationId, message);
                            } else {
                                alert('❌ Gửi tin nhắn thất bại: ' + (data.message || 'Lỗi không xác định'));
                            }
                        })
                        .catch(error => {
                            console.error('❌ Send message error:', error);
                            alert('❌ Lỗi gửi tin nhắn: ' + error.message);
                        })
                        .finally(() => {
                            sendBtn.disabled = false;
                            sendBtn.innerHTML = '<i class="fas fa-paper-plane"></i>';
                        });
                }

                function handleFileUpload(e) {
                    console.log('📎 Handling file upload...');

                    const file = e.target.files[0];
                    if (!file || !selectedConversationId) return;

                    console.log('📎 File selected:', file.name, file.size, 'bytes');

                    const formData = new FormData();
                    formData.append('conversation_id', selectedConversationId);
                    formData.append('attachment', file);

                    // Show loading
                    sendBtn.disabled = true;
                    sendBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

                    fetch('{{ route('admin.chat.send') }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                    'content')
                            },
                            body: formData
                        })
                        .then(response => {
                            console.log('📡 File upload response status:', response.status);
                            return response.json();
                        })
                        .then(data => {
                            console.log('📡 File upload response:', data);
                            if (data.success) {
                                console.log('✅ File uploaded successfully');
                                // Reset file input
                                e.target.value = '';
                                // Reload conversation to show attachment
                                loadConversation(selectedConversationId);
                            } else {
                                alert('❌ Gửi tệp đính kèm thất bại: ' + (data.message || 'Lỗi không xác định'));
                            }
                        })
                        .catch(error => {
                            console.error('❌ File upload error:', error);
                            alert('❌ Lỗi gửi tệp đính kèm: ' + error.message);
                        })
                        .finally(() => {
                            sendBtn.disabled = false;
                            sendBtn.innerHTML = '<i class="fas fa-paper-plane"></i>';
                        });
                }

                function addMessageToChat(data) {
                    console.log('💬 Adding message to chat:', data);

                    if (!chatMessages) return;

                    const isAdmin = data.sender_id === currentUserId;
                    const senderName = isAdmin ? 'Admin' : (data.sender?.name || 'Khách hàng');
                    const firstLetter = senderName.charAt(0).toUpperCase();

                    // Check if we should create a new message group
                    let lastGroup = chatMessages.lastElementChild;
                    let createNewGroup = true;

                    if (lastGroup && lastGroup.classList.contains('message-group')) {
                        const lastSenderName = lastGroup.querySelector('.message-sender-name');
                        if (lastSenderName && lastSenderName.textContent === senderName) {
                            createNewGroup = false;
                        }
                    }

                    if (createNewGroup) {
                        // Create new message group
                        const messageGroup = document.createElement('div');
                        messageGroup.className = 'message-group';

                        messageGroup.innerHTML = `
                <div class="message-sender">
                    <div class="chat-avatar" style="${isAdmin ? 'background-color: #3b82f6; color: white;' : ''}">
                        ${firstLetter}
                    </div>
                    <span class="message-sender-name">${escapeHtml(senderName)}</span>
                    ${!isAdmin ? '<span class="message-sender-type">Khách hàng</span>' : ''}
                </div>
            `;

                        chatMessages.appendChild(messageGroup);
                        lastGroup = messageGroup;
                    }

                    // Add message to group
                    const messageContainer = document.createElement('div');
                    messageContainer.style.display = 'flex';
                    messageContainer.style.marginBottom = '8px';

                    messageContainer.innerHTML = `
            <div class="message-bubble ${isAdmin ? 'message-admin' : 'message-customer'}">
                ${escapeHtml(data.message)}
            </div>
            <span class="message-time">${new Date(data.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}</span>
        `;

                    lastGroup.appendChild(messageContainer);
                    scrollToBottom();
                }

                function updateConversationPreview(conversationId, message) {
                    const conversationItem = document.querySelector(`[data-conversation-id="${conversationId}"]`);
                    if (conversationItem) {
                        const previewElement = conversationItem.querySelector('.chat-item-preview');
                        if (previewElement) {
                            previewElement.textContent = message.length > 60 ? message.substring(0, 60) + '...' :
                                message;
                        }

                        const timeElement = conversationItem.querySelector('.chat-item-time');
                        if (timeElement) {
                            timeElement.textContent = new Date().toLocaleTimeString([], {
                                hour: '2-digit',
                                minute: '2-digit'
                            });
                        }
                    }
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

                function escapeHtml(text) {
                    const div = document.createElement('div');
                    div.textContent = text;
                    return div.innerHTML;
                }

                console.log('✅ Admin Chat Dashboard loaded successfully');

                // Lấy các biến cần thiết từ blade
                const conversationId = ...; // ID hội thoại đang mở
                const userId = ...; // ID user hiện tại (branch)
                const userType = 'branch';

                // Khởi tạo realtime chat
                window.branchChat = new ChatRealtime(conversationId, userId, userType);
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
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                    .getAttribute('content'),
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
                                alert('❌ Phân phối thất bại: ' + (data.message || 'Lỗi không xác định'));
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
        </script>
    @endsection
