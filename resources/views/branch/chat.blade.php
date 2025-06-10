@extends('layouts.admin.contentLayoutMaster')

@section('title', 'Chat Chi nhánh - ' . $branch->name)

@section('content')
    <style>
        :root {
            --primary-color: #3b82f6;
            --primary-hover: #2563eb;
            --primary-light: #dbeafe;
            --success-color: #10b981;
            --success-light: #d1fae5;
            --warning-color: #f59e0b;
            --warning-light: #fef3c7;
            --danger-color: #ef4444;
            --danger-light: #fee2e2;
            --text-primary: #111827;
            --text-secondary: #4b5563;
            --text-muted: #6b7280;
            --border-color: #e5e7eb;
            --bg-light: #f9fafb;
            --bg-white: #ffffff;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background-color: var(--bg-light);
            color: var(--text-primary);
            line-height: 1.6;
        }

        .branch-chat-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 1.5rem;
        }

        /* Enhanced Header */
        .branch-header {
            background: linear-gradient(135deg, var(--bg-white) 0%, var(--bg-light) 100%);
            border-radius: 16px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: var(--shadow-md);
            border: 1px solid var(--border-color);
            position: relative;
            overflow: hidden;
        }

        .branch-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-color), var(--success-color), var(--warning-color));
        }

        .branch-header h1 {
            font-size: 1.875rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 1rem;
            background: linear-gradient(135deg, var(--primary-color), var(--success-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .branch-header .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .branch-header .user-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .branch-header .user-avatar {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-color), var(--primary-hover));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 1.25rem;
            box-shadow: var(--shadow-md);
        }

        .branch-header .user-details h3 {
            margin: 0;
            font-size: 1.125rem;
            font-weight: 600;
            color: var(--text-primary);
        }

        .branch-header .user-details .role-badge {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-hover));
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 0.25rem;
            display: inline-block;
        }

        .branch-header .status-info {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            background: var(--success-light);
            padding: 0.75rem 1rem;
            border-radius: 12px;
            border: 1px solid var(--success-color);
        }

        .branch-header .status-dot {
            width: 10px;
            height: 10px;
            background-color: var(--success-color);
            border-radius: 50%;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.5;
            }
        }

        .branch-header .status-text {
            font-weight: 600;
            color: var(--success-color);
            font-size: 0.875rem;
        }

        /* Enhanced Stats Cards */
        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: linear-gradient(135deg, var(--bg-white) 0%, var(--bg-light) 100%);
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: var(--shadow-md);
            border: 1px solid var(--border-color);
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-lg);
        }

        .stat-card::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 4px;
            transition: width 0.3s ease;
        }

        .stat-card:hover::before {
            width: 8px;
        }

        .stat-card.new::before {
            background: linear-gradient(180deg, var(--primary-color), var(--primary-hover));
        }

        .stat-card.active::before {
            background: linear-gradient(180deg, var(--success-color), #059669);
        }

        .stat-card.priority::before {
            background: linear-gradient(180deg, var(--warning-color), #d97706);
        }

        .stat-card.total::before {
            background: linear-gradient(180deg, var(--danger-color), #dc2626);
        }

        .stat-card .stat-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1rem;
        }

        .stat-card .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            color: white;
        }

        .stat-card.new .stat-icon {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-hover));
        }

        .stat-card.active .stat-icon {
            background: linear-gradient(135deg, var(--success-color), #059669);
        }

        .stat-card.priority .stat-icon {
            background: linear-gradient(135deg, var(--warning-color), #d97706);
        }

        .stat-card.total .stat-icon {
            background: linear-gradient(135deg, var(--danger-color), #dc2626);
        }

        .stat-card .stat-number {
            font-size: 2rem;
            font-weight: 800;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
            line-height: 1;
        }

        .stat-card .stat-label {
            color: var(--text-secondary);
            font-size: 0.875rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .stat-card .stat-trend {
            font-size: 0.75rem;
            color: var(--success-color);
            font-weight: 600;
            margin-top: 0.5rem;
        }

        /* Enhanced Chat Interface */
        .chat-interface {
            background: var(--bg-white);
            border-radius: 20px;
            box-shadow: var(--shadow-lg);
            overflow: hidden;
            height: calc(100vh - 400px);
            min-height: 600px;
            display: flex;
            border: 1px solid var(--border-color);
        }

        /* Enhanced Sidebar */
        .chat-sidebar {
            width: 320px;
            border-right: 1px solid var(--border-color);
            display: flex;
            flex-direction: column;
            background: linear-gradient(180deg, var(--bg-white) 0%, var(--bg-light) 100%);
        }

        .chat-sidebar-header {
            padding: 1.5rem;
            border-bottom: 1px solid var(--border-color);
            background: var(--bg-white);
        }

        .chat-sidebar-header h3 {
            margin: 0 0 1rem 0;
            font-size: 1.125rem;
            font-weight: 700;
            color: var(--text-primary);
        }

        .search-container {
            position: relative;
        }

        .search-container input {
            width: 100%;
            padding: 0.75rem 1rem 0.75rem 2.5rem;
            border-radius: 12px;
            border: 2px solid var(--border-color);
            font-size: 0.875rem;
            background: var(--bg-light);
            transition: all 0.3s ease;
        }

        .search-container input:focus {
            outline: none;
            border-color: var(--primary-color);
            background: var(--bg-white);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .search-container i {
            position: absolute;
            left: 0.875rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 0.875rem;
        }

        .conversations-list {
            flex: 1;
            overflow-y: auto;
            padding: 0.5rem 0;
        }

        .conversation-item {
            padding: 1rem 1.5rem;
            margin: 0.25rem 0.75rem;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            border: 1px solid transparent;
        }

        .conversation-item:hover {
            background: linear-gradient(135deg, var(--primary-light), rgba(59, 130, 246, 0.05));
            border-color: var(--primary-color);
            transform: translateX(4px);
        }

        .conversation-item.active {
            background: linear-gradient(135deg, var(--primary-light), var(--primary-color));
            border-color: var(--primary-color);
            color: var(--primary-color);
            box-shadow: var(--shadow-md);
        }

        .conversation-item .conversation-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 0.5rem;
        }

        .conversation-item .customer-name {
            font-weight: 700;
            font-size: 0.9375rem;
            color: var(--text-primary);
            margin: 0;
        }

        .conversation-item.active .customer-name {
            color: var(--primary-color);
        }

        .conversation-item .time {
            font-size: 0.75rem;
            color: var(--text-muted);
            font-weight: 600;
        }

        .conversation-item .message-preview {
            font-size: 0.8125rem;
            color: var(--text-secondary);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            margin-bottom: 0.5rem;
            line-height: 1.4;
        }

        .conversation-item .conversation-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .conversation-item .status-badge {
            font-size: 0.6875rem;
            padding: 0.25rem 0.5rem;
            border-radius: 6px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .conversation-item .status-badge.distributed {
            background: var(--primary-light);
            color: var(--primary-color);
        }

        .conversation-item .status-badge.active {
            background: var(--success-light);
            color: var(--success-color);
        }

        .conversation-item .status-badge.resolved {
            background: var(--warning-light);
            color: var(--warning-color);
        }

        .conversation-item .unread-badge {
            background: linear-gradient(135deg, var(--danger-color), #dc2626);
            color: white;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.6875rem;
            font-weight: 700;
            box-shadow: var(--shadow-sm);
        }

        /* Enhanced Chat Main */
        .chat-main {
            flex: 1;
            display: flex;
            flex-direction: column;
            background: var(--bg-white);
        }

        .chat-header {
            padding: 1.5rem;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: linear-gradient(135deg, var(--bg-white) 0%, var(--bg-light) 100%);
        }

        .chat-header .customer-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .chat-header .customer-avatar {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--text-muted), var(--text-secondary));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 1.125rem;
        }

        .chat-header .customer-details h2 {
            font-size: 1.125rem;
            font-weight: 700;
            margin: 0;
            color: var(--text-primary);
        }

        .chat-header .customer-details .email {
            font-size: 0.8125rem;
            color: var(--text-secondary);
            margin-top: 0.125rem;
        }

        .chat-header .chat-actions .dropdown-toggle {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-hover));
            border: none;
            border-radius: 10px;
            padding: 0.5rem 1rem;
            font-weight: 600;
            box-shadow: var(--shadow-sm);
            transition: all 0.3s ease;
        }

        .chat-header .chat-actions .dropdown-toggle:hover {
            transform: translateY(-1px);
            box-shadow: var(--shadow-md);
        }

        /* Enhanced Messages */
        .chat-messages {
            flex: 1;
            padding: 1.5rem;
            overflow-y: auto;
            background: linear-gradient(180deg, var(--bg-light) 0%, var(--bg-white) 100%);
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .message {
            max-width: 75%;
            display: flex;
            flex-direction: column;
            animation: messageSlideIn 0.3s ease;
        }

        @keyframes messageSlideIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .message.sent {
            align-self: flex-end;
            align-items: flex-end;
        }

        .message.received {
            align-self: flex-start;
            align-items: flex-start;
        }

        .message-bubble {
            padding: 1rem 1.25rem;
            border-radius: 18px;
            position: relative;
            box-shadow: var(--shadow-sm);
            word-wrap: break-word;
            line-height: 1.5;
        }

        .message.sent .message-bubble {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-hover));
            color: white;
            border-bottom-right-radius: 6px;
        }

        .message.received .message-bubble {
            background: var(--bg-white);
            color: var(--text-primary);
            border: 1px solid var(--border-color);
            border-bottom-left-radius: 6px;
        }

        .message-info {
            font-size: 0.75rem;
            color: var(--text-muted);
            margin-top: 0.5rem;
            font-weight: 600;
        }

        .system-message {
            align-self: center;
            text-align: center;
            padding: 0.75rem 1.25rem;
            background: linear-gradient(135deg, rgba(107, 114, 128, 0.1), rgba(107, 114, 128, 0.05));
            border-radius: 20px;
            color: var(--text-secondary);
            font-size: 0.8125rem;
            font-weight: 600;
            max-width: 80%;
            border: 1px solid rgba(107, 114, 128, 0.2);
        }

        /* Enhanced Chat Input */
        .chat-input {
            padding: 1.5rem;
            border-top: 1px solid var(--border-color);
            background: var(--bg-white);
        }

        .chat-input .input-group {
            position: relative;
            background: var(--bg-light);
            border-radius: 16px;
            border: 2px solid var(--border-color);
            transition: all 0.3s ease;
        }

        .chat-input .input-group:focus-within {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
            background: var(--bg-white);
        }

        .chat-input textarea {
            width: 100%;
            padding: 1rem 5rem 1rem 1rem;
            border: none;
            background: transparent;
            resize: none;
            font-size: 0.9375rem;
            line-height: 1.5;
            max-height: 120px;
            overflow-y: auto;
            border-radius: 14px;
        }

        .chat-input textarea:focus {
            outline: none;
        }

        .chat-input textarea::placeholder {
            color: var(--text-muted);
        }

        .chat-input .actions {
            position: absolute;
            right: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            display: flex;
            gap: 0.5rem;
        }

        .chat-input .btn-icon {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: transparent;
            border: none;
            color: var(--text-muted);
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 0.875rem;
        }

        .chat-input .btn-icon:hover {
            background: rgba(59, 130, 246, 0.1);
            color: var(--primary-color);
            transform: scale(1.1);
        }

        .chat-input .btn-send {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-hover));
            color: white;
            box-shadow: var(--shadow-sm);
        }

        .chat-input .btn-send:hover {
            background: linear-gradient(135deg, var(--primary-hover), #1d4ed8);
            transform: scale(1.1);
            box-shadow: var(--shadow-md);
        }

        .chat-input .btn-send:disabled {
            background: var(--text-muted);
            cursor: not-allowed;
            transform: none;
        }

        /* Enhanced Empty State */
        .empty-state {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100%;
            color: var(--text-muted);
            text-align: center;
            padding: 3rem;
        }

        .empty-state i {
            font-size: 4rem;
            margin-bottom: 1.5rem;
            color: var(--text-muted);
            opacity: 0.5;
        }

        .empty-state h5 {
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 0.75rem;
            color: var(--text-primary);
        }

        .empty-state p {
            font-size: 0.9375rem;
            color: var(--text-secondary);
            max-width: 300px;
        }

        /* Enhanced Attachment Preview */
        .attachment-preview {
            margin-top: 1rem;
            padding: 1rem;
            background: rgba(59, 130, 246, 0.05);
            border-radius: 12px;
            border: 1px solid rgba(59, 130, 246, 0.2);
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .attachment-preview img {
            height: 60px;
            width: auto;
            border-radius: 8px;
            box-shadow: var(--shadow-sm);
        }

        .attachment-preview .file-info {
            flex: 1;
            font-size: 0.8125rem;
        }

        .attachment-preview .file-info strong {
            display: block;
            color: var(--text-primary);
            margin-bottom: 0.25rem;
        }

        .attachment-preview .btn-close {
            background: var(--danger-color);
            color: white;
            border: none;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .attachment-preview .btn-close:hover {
            background: #dc2626;
            transform: scale(1.1);
        }

        /* Enhanced Scrollbars */
        .chat-messages::-webkit-scrollbar,
        .conversations-list::-webkit-scrollbar {
            width: 6px;
        }

        .chat-messages::-webkit-scrollbar-track,
        .conversations-list::-webkit-scrollbar-track {
            background: transparent;
        }

        .chat-messages::-webkit-scrollbar-thumb,
        .conversations-list::-webkit-scrollbar-thumb {
            background: linear-gradient(180deg, var(--text-muted), var(--text-secondary));
            border-radius: 3px;
        }

        .chat-messages::-webkit-scrollbar-thumb:hover,
        .conversations-list::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(180deg, var(--text-secondary), var(--text-primary));
        }

        /* Loading States */
        .loading-spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 2px solid transparent;
            border-top: 2px solid currentColor;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        /* Responsive Design */
        @media (max-width: 1024px) {
            .stats-container {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .branch-chat-container {
                padding: 1rem;
            }

            .branch-header {
                padding: 1.5rem;
            }

            .branch-header .header-content {
                flex-direction: column;
                align-items: flex-start;
            }

            .stats-container {
                grid-template-columns: 1fr;
                gap: 1rem;
            }

            .chat-interface {
                flex-direction: column;
                height: auto;
            }

            .chat-sidebar {
                width: 100%;
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

        @media (max-width: 480px) {
            .branch-header h1 {
                font-size: 1.5rem;
            }

            .chat-input textarea {
                padding-right: 4rem;
            }

            .chat-input .actions {
                right: 0.5rem;
            }

            .chat-input .btn-icon {
                width: 32px;
                height: 32px;
            }
        }
    </style>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="branch-chat-container">
        <!-- Enhanced Branch Header -->
        <div class="branch-header">
            <h1>💬 Chat Chi nhánh {{ $branch->name }}</h1>
            <div class="header-content">
                <div class="user-info">
                    <div class="user-avatar">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    <div class="user-details">
                        <h3>{{ $user->name }}</h3>
                        <div class="role-badge">{{ ucfirst(str_replace('_', ' ', $user->role)) }}</div>
                    </div>
                </div>
                <div class="status-info">
                    <div class="status-dot"></div>
                    <div class="status-text">
                        <strong>Online</strong> • {{ now()->format('H:i') }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Enhanced Stats Cards -->
        <div class="stats-container">
            <div class="stat-card new" onclick="filterConversations('distributed')">
                <div class="stat-header">
                    <div class="stat-icon">
                        <i class="fas fa-inbox"></i>
                    </div>
                </div>
                <div class="stat-number">{{ $conversations->where('status', 'distributed')->count() }}</div>
                <div class="stat-label">Mới nhận</div>
                <div class="stat-trend">
                    +{{ $conversations->where('status', 'distributed')->where('created_at', '>=', now()->subDay())->count() }}
                    hôm nay</div>
            </div>

            <div class="stat-card active" onclick="filterConversations('active')">
                <div class="stat-header">
                    <div class="stat-icon">
                        <i class="fas fa-comments"></i>
                    </div>
                </div>
                <div class="stat-number">{{ $conversations->whereIn('status', ['active', 'open'])->count() }}</div>
                <div class="stat-label">Đang xử lý</div>
                <div class="stat-trend">
                    {{ $conversations->whereIn('status', ['active', 'open'])->where('updated_at', '>=', now()->subHour())->count() }}
                    hoạt động gần đây</div>
            </div>

            <div class="stat-card priority" onclick="filterConversations('priority')">
                <div class="stat-header">
                    <div class="stat-icon">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                </div>
                <div class="stat-number">
                    {{ $conversations->whereIn('priority', ['high', 'urgent'])->count() }}
                </div>
                <div class="stat-label">Ưu tiên cao</div>
                <div class="stat-trend">Cần xử lý ngay</div>
            </div>

            <div class="stat-card total" onclick="filterConversations('all')">
                <div class="stat-header">
                    <div class="stat-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                </div>
                <div class="stat-number">{{ $conversations->count() }}</div>
                <div class="stat-label">Tổng cộng</div>
                <div class="stat-trend">{{ $conversations->where('status', 'resolved')->count() }} đã giải quyết</div>
            </div>
        </div>

        <!-- Enhanced Chat Interface -->
        <div class="chat-interface">
            <!-- Enhanced Sidebar -->
            <div class="chat-sidebar">
                <div class="chat-sidebar-header">
                    <h3>📋 Danh sách cuộc trò chuyện</h3>
                    <div class="search-container">
                        <i class="fas fa-search"></i>
                        <input type="text" id="search-conversations" placeholder="Tìm kiếm khách hàng...">
                    </div>
                </div>
                <div class="conversations-list" id="conversations-list">
                    @forelse($conversations as $conversation)
                        @php
                            $unreadCount = $conversation->messages
                                ->where('is_read', false)
                                ->where('sender_id', '!=', $user->id)
                                ->count();
                            $customer = $conversation->customer;
                            $lastMessage = $conversation->messages->last();
                        @endphp
                        <div class="conversation-item {{ $loop->first ? 'active' : '' }}"
                            data-conversation-id="{{ $conversation->id }}" data-status="{{ $conversation->status }}"
                            data-priority="{{ $conversation->priority ?? 'normal' }}"
                            data-customer-name="{{ $customer->name ?? 'Khách hàng' }}">

                            <div class="conversation-header">
                                <div class="customer-name">
                                    {{ $customer->name ?? 'Khách hàng' }}
                                </div>
                                <div class="time">{{ $conversation->updated_at->format('H:i') }}</div>
                            </div>

                            <div class="message-preview">
                                @if ($lastMessage)
                                    @if ($lastMessage->message)
                                        {{ Str::limit($lastMessage->message, 45) }}
                                    @elseif ($lastMessage->attachment)
                                        📎 {{ $lastMessage->attachment_type === 'image' ? 'Hình ảnh' : 'Tệp đính kèm' }}
                                    @else
                                        Tin nhắn mới
                                    @endif
                                @else
                                    💬 Cuộc trò chuyện mới
                                @endif
                            </div>

                            <div class="conversation-meta">
                                <span class="status-badge {{ $conversation->status }}">
                                    @switch($conversation->status)
                                        @case('distributed')
                                            📥 Mới nhận
                                        @break

                                        @case('active')
                                        @case('open')
                                            🟢 Đang xử lý
                                        @break

                                        @case('resolved')
                                            ✅ Đã giải quyết
                                        @break

                                        @case('closed')
                                            🔒 Đã đóng
                                        @break

                                        @default
                                            📋 Chờ xử lý
                                    @endswitch
                                </span>

                                @if ($unreadCount > 0)
                                    <div class="unread-badge">{{ $unreadCount }}</div>
                                @endif
                            </div>
                        </div>
                        @empty
                            <div class="empty-state">
                                <i class="fas fa-inbox"></i>
                                <h5>Không có cuộc trò chuyện</h5>
                                <p>Chưa có cuộc trò chuyện nào được phân phối cho chi nhánh này</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Enhanced Main Chat Area -->
                <div class="chat-main">
                    @if ($conversations->count() > 0)
                        @php $firstConversation = $conversations->first(); @endphp
                        <!-- Enhanced Chat Header -->
                        <div class="chat-header">
                            <div class="customer-info">
                                <div class="customer-avatar">
                                    {{ strtoupper(substr($firstConversation->customer->name ?? 'K', 0, 1)) }}
                                </div>
                                <div class="customer-details">
                                    <h2>{{ $firstConversation->customer->name ?? 'Khách hàng' }}</h2>
                                    <div class="email">{{ $firstConversation->customer->email ?? 'Chưa có email' }}</div>
                                </div>
                            </div>
                            <div class="chat-actions">
                                <div class="dropdown">
                                    <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown"
                                        aria-expanded="false">
                                        <i class="fas fa-cog me-2"></i>Hành động
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li><a class="dropdown-item" href="#" onclick="updateStatus('active')">
                                                <i class="fas fa-play me-2 text-success"></i>Kích hoạt cuộc trò chuyện
                                            </a></li>
                                        <li><a class="dropdown-item" href="#" onclick="updateStatus('resolved')">
                                                <i class="fas fa-check me-2 text-warning"></i>Đánh dấu đã giải quyết
                                            </a></li>
                                        <li><a class="dropdown-item" href="#" onclick="updateStatus('closed')">
                                                <i class="fas fa-times me-2 text-danger"></i>Đóng cuộc trò chuyện
                                            </a></li>
                                        {{-- 
                                    <!-- COMMENTED OUT: Staff Assignment Feature -->
                                    @if ($user->role === 'branch_manager')
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item" href="#" data-bs-toggle="modal"
                                                data-bs-target="#assignModal">
                                                <i class="fas fa-user-plus me-2 text-primary"></i>Phân công nhân viên
                                            </a></li>
                                    @endif
                                    --}}
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Enhanced Chat Messages -->
                        <div class="chat-messages" id="chat-messages">
                            @if ($firstConversation && $firstConversation->messages->count() > 0)
                                @foreach ($firstConversation->messages as $message)
                                    @php
                                        $isSent = $message->sender_id === $user->id;
                                        $sender = $message->sender;
                                        $isSystem = $message->is_system_message ?? false;
                                    @endphp

                                    @if ($isSystem)
                                        <div class="system-message">
                                            <i class="fas fa-info-circle me-2"></i>{{ $message->message }}
                                        </div>
                                    @else
                                        <div class="message {{ $isSent ? 'sent' : 'received' }}">
                                            <div class="message-info">
                                                <strong>{{ $isSent ? 'Bạn' : $sender->name ?? 'Khách hàng' }}</strong> •
                                                {{ $message->created_at->format('H:i') }}
                                                @if ($isSent)
                                                    <i class="fas fa-check-double ms-1 text-primary"></i>
                                                @endif
                                            </div>

                                            <div class="message-bubble">
                                                @if ($message->message)
                                                    <div>{!! nl2br(e($message->message)) !!}</div>
                                                @endif

                                                @if ($message->attachment)
                                                    <div class="attachment-preview">
                                                        @if ($message->attachment_type === 'image')
                                                            <img src="{{ asset('storage/' . $message->attachment) }}"
                                                                alt="attachment" class="img-fluid rounded">
                                                        @else
                                                            <div class="d-flex align-items-center gap-2">
                                                                <i class="fas fa-file text-primary"></i>
                                                                <a href="{{ asset('storage/' . $message->attachment) }}"
                                                                    class="text-decoration-none" target="_blank">
                                                                    📎 {{ basename($message->attachment) }}
                                                                </a>
                                                            </div>
                                                        @endif
                                                    </div>
                                                @endif
                                            </div>


                                        </div>
                                    @endif
                                @endforeach
                            @else
                                <div class="empty-state">
                                    <i class="fas fa-comment-dots"></i>
                                    <h5>Chưa có tin nhắn</h5>
                                    <p>Hãy bắt đầu cuộc trò chuyện với khách hàng để hỗ trợ họ tốt nhất</p>
                                </div>
                            @endif
                        </div>

                        <!-- Enhanced Chat Input -->
                        <div class="chat-input">
                            <form id="chat-form">
                                <div class="input-group">
                                    <textarea id="message" placeholder="Nhập tin nhắn của bạn..." rows="1"></textarea>
                                    <div class="actions">
                                        <button type="button" class="btn-icon" id="attachment-btn" title="Đính kèm tệp">
                                            <i class="fas fa-paperclip"></i>
                                        </button>
                                        <button type="submit" class="btn-icon btn-send" id="send-btn"
                                            title="Gửi tin nhắn">
                                            <i class="fas fa-paper-plane"></i>
                                        </button>
                                    </div>
                                </div>
                            </form>
                            <input type="file" id="attachment" class="d-none"
                                accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.txt,.zip,.rar">
                            <div id="attachment-preview"></div>
                        </div>
                    @else
                        <div class="empty-state">
                            <i class="fas fa-comments"></i>
                            <h5>Chưa có cuộc trò chuyện</h5>
                            <p>Chưa có cuộc trò chuyện nào được phân phối cho chi nhánh này. Hãy chờ admin phân phối hoặc liên
                                hệ để được hỗ trợ.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- 
    <!-- COMMENTED OUT: Assign Staff Modal -->
    <!-- 
    @if ($user->role === 'branch_manager')
        <div class="modal fade" id="assignModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fas fa-user-plus me-2"></i>Phân công nhân viên
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form id="assign-form">
                            <div class="mb-3">
                                <label for="staff-select" class="form-label">Chọn nhân viên</label>
                                <select class="form-select" id="staff-select">
                                    <option value="">Chọn nhân viên...</option>
                                    @foreach (\App\Models\User::where('branch_id', $user->branch_id)->where('role', 'branch_staff')->get() as $staff)
                                        <option value="{{ $staff->id }}">{{ $staff->name }} - {{ $staff->email }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="assign-note" class="form-label">Ghi chú (tùy chọn)</label>
                                <textarea class="form-control" id="assign-note" rows="3" 
                                    placeholder="Thêm ghi chú về việc phân công..."></textarea>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-2"></i>Hủy
                        </button>
                        <button type="button" class="btn btn-primary" id="assign-btn">
                            <i class="fas fa-check me-2"></i>Phân công
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
    -->
    --}}

        <script src="{{ asset('js/chat-realtime.js') }}"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                console.log('🚀 Branch Chat Interface Loading...');

                // Global variables
                const currentUserId = {{ $user->id }};
                let selectedConversationId = {{ $conversations->first()->id ?? 'null' }};

                // DOM elements
                const chatMessages = document.getElementById('chat-messages');
                const messageInput = document.getElementById('message');
                const sendBtn = document.getElementById('send-btn');
                const attachmentBtn = document.getElementById('attachment-btn');
                const attachmentInput = document.getElementById('attachment');
                const attachmentPreview = document.getElementById('attachment-preview');
                const conversationItems = document.querySelectorAll('.conversation-item');
                const searchInput = document.getElementById('search-conversations');
                // const assignBtn = document.getElementById('assign-btn'); // COMMENTED OUT

                console.log('✅ Branch chat initialized with conversation ID:', selectedConversationId);

                // Initialize
                init();

                function init() {
                    scrollToBottom();
                    setupEventListeners();
                    updateSendButtonState();

                    if (messageInput) {
                        messageInput.addEventListener('input', autoResizeTextarea);
                    }

                    console.log('✅ Branch chat interface ready');
                }

                function setupEventListeners() {
                    console.log('🔧 Setting up event listeners...');

                    // Conversation selection
                    conversationItems.forEach(item => {
                        item.addEventListener('click', function() {
                            const conversationId = this.getAttribute('data-conversation-id');
                            selectConversation(conversationId);
                        });
                    });

                    // Message form submission
                    const chatForm = document.getElementById('chat-form');
                    if (chatForm) {
                        chatForm.addEventListener('submit', function(e) {
                            e.preventDefault();
                            sendMessage();
                        });
                    }

                    // Enter key to send
                    if (messageInput) {
                        messageInput.addEventListener('keypress', function(e) {
                            if (e.key === 'Enter' && !e.shiftKey) {
                                e.preventDefault();
                                sendMessage();
                            }
                        });

                        messageInput.addEventListener('input', updateSendButtonState);
                    }

                    // Attachment button
                    if (attachmentBtn) {
                        attachmentBtn.addEventListener('click', function() {
                            attachmentInput.click();
                        });
                    }

                    // File selection
                    if (attachmentInput) {
                        attachmentInput.addEventListener('change', handleFileSelection);
                    }

                    // Search conversations
                    if (searchInput) {
                        searchInput.addEventListener('input', function() {
                            const searchTerm = this.value.toLowerCase();
                            conversationItems.forEach(item => {
                                const customerName = item.getAttribute('data-customer-name')
                                    .toLowerCase();
                                const messagePreview = item.querySelector('.message-preview')
                                    .textContent.toLowerCase();
                                const isVisible = customerName.includes(searchTerm) || messagePreview
                                    .includes(searchTerm);
                                item.style.display = isVisible ? '' : 'none';
                            });
                        });
                    }

                    // COMMENTED OUT: Assign staff functionality
                    /*
                    if (assignBtn) {
                        assignBtn.addEventListener('click', assignToStaff);
                    }
                    */

                    console.log('✅ Event listeners set up successfully');
                }

                function scrollToBottom() {
                    if (chatMessages) {
                        setTimeout(() => {
                            chatMessages.scrollTop = chatMessages.scrollHeight;
                        }, 100);
                    }
                }

                function autoResizeTextarea() {
                    if (messageInput) {
                        messageInput.style.height = 'auto';
                        messageInput.style.height = Math.min(messageInput.scrollHeight, 120) + 'px';
                    }
                }

                function updateSendButtonState() {
                    if (messageInput && sendBtn) {
                        const hasText = messageInput.value.trim().length > 0;
                        sendBtn.disabled = !hasText;
                        sendBtn.style.opacity = hasText ? '1' : '0.5';
                    }
                }

                function selectConversation(conversationId) {
                    console.log('🎯 Selecting conversation:', conversationId);

                    selectedConversationId = conversationId;

                    // Update active state with animation
                    conversationItems.forEach(item => {
                        item.classList.remove('active');
                        if (item.getAttribute('data-conversation-id') === conversationId) {
                            item.classList.add('active');
                        }
                    });

                    // Load conversation details
                    loadConversationDetails(conversationId);
                }

                async function loadConversationDetails(conversationId) {
                    try {
                        // Show loading state
                        if (chatMessages) {
                            chatMessages.innerHTML = `
                            <div class="empty-state">
                                <div class="loading-spinner"></div>
                                <h5>Đang tải...</h5>
                                <p>Vui lòng chờ trong giây lát</p>
                            </div>
                        `;
                        }

                        const response = await fetch(`/branch/chat/api/conversation/${conversationId}`, {
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                    .getAttribute('content'),
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });

                        const data = await response.json();

                        if (data.success) {
                            updateChatHeader(data.conversation);
                            renderMessages(data.conversation.messages || []);
                        } else {
                            throw new Error(data.message || 'Failed to load conversation');
                        }
                    } catch (error) {
                        console.error('❌ Error loading conversation:', error);
                        if (chatMessages) {
                            chatMessages.innerHTML = `
                            <div class="empty-state">
                                <i class="fas fa-exclamation-triangle text-danger"></i>
                                <h5>Lỗi tải cuộc trò chuyện</h5>
                                <p>Không thể tải chi tiết cuộc trò chuyện. Vui lòng thử lại.</p>
                            </div>
                        `;
                        }
                    }
                }

                function updateChatHeader(conversation) {
                    const chatHeader = document.querySelector('.chat-header');
                    if (chatHeader && conversation.customer) {
                        const customerName = chatHeader.querySelector('.customer-details h2');
                        const customerEmail = chatHeader.querySelector('.customer-details .email');
                        const customerAvatar = chatHeader.querySelector('.customer-avatar');

                        if (customerName) customerName.textContent = conversation.customer.name || 'Khách hàng';
                        if (customerEmail) customerEmail.textContent = conversation.customer.email || 'Chưa có email';
                        if (customerAvatar) {
                            customerAvatar.textContent = (conversation.customer.name || 'K').charAt(0).toUpperCase();
                        }
                    }
                }

                function renderMessages(messages) {
                    if (!chatMessages) return;

                    if (messages.length === 0) {
                        chatMessages.innerHTML = `
                        <div class="empty-state">
                            <i class="fas fa-comment-dots"></i>
                            <h5>Chưa có tin nhắn</h5>
                            <p>Hãy bắt đầu cuộc trò chuyện với khách hàng để hỗ trợ họ tốt nhất</p>
                        </div>
                    `;
                        return;
                    }

                    chatMessages.innerHTML = '';
                    messages.forEach(message => {
                        addMessageToChat(message, false);
                    });
                    scrollToBottom();
                }

                async function sendMessage() {
                    if (!selectedConversationId) {
                        showNotification('Không có cuộc trò chuyện để gửi tin nhắn', 'error');
                        return;
                    }

                    const message = messageInput.value.trim();
                    const file = attachmentInput.files[0];

                    if (!message && !file) {
                        showNotification('Vui lòng nhập tin nhắn hoặc chọn tệp', 'warning');
                        return;
                    }

                    try {
                        // Show loading state
                        sendBtn.disabled = true;
                        sendBtn.innerHTML = '<div class="loading-spinner"></div>';

                        const formData = new FormData();
                        formData.append('conversation_id', selectedConversationId);
                        formData.append('message', message);
                        if (file) formData.append('attachment', file);

                        const response = await fetch('/branch/chat/send-message', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                    .getAttribute('content'),
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: formData
                        });

                        const data = await response.json();

                        if (data.success) {
                            // Clear form
                            messageInput.value = '';
                            attachmentInput.value = '';
                            attachmentPreview.innerHTML = '';
                            messageInput.style.height = 'auto';

                            // Add message to chat immediately
                            addMessageToChat({
                                id: data.data.id,
                                sender_id: currentUserId,
                                message: data.data.message,
                                attachment: data.data.attachment,
                                attachment_type: data.data.attachment_type,
                                created_at: data.data.created_at,
                                sender: {
                                    name: '{{ $user->name }}'
                                }
                            });

                            // Update conversation preview
                            updateConversationPreview(selectedConversationId, message || '📎 Tệp đính kèm');

                            showNotification('Tin nhắn đã được gửi thành công', 'success');
                        } else {
                            throw new Error(data.message || 'Failed to send message');
                        }
                    } catch (error) {
                        console.error('❌ Send message error:', error);
                        showNotification('Lỗi gửi tin nhắn: ' + error.message, 'error');
                    } finally {
                        sendBtn.innerHTML = '<i class="fas fa-paper-plane"></i>';
                        updateSendButtonState();
                    }
                }

                function addMessageToChat(messageData, animate = true) {
                    if (!chatMessages) return;

                    // Remove empty state if exists
                    const emptyState = chatMessages.querySelector('.empty-state');
                    if (emptyState) {
                        emptyState.remove();
                    }

                    const isCurrentUser = messageData.sender_id === currentUserId;
                    const isSystem = messageData.is_system_message ?? false;

                    if (isSystem) {
                        const systemDiv = document.createElement('div');
                        systemDiv.className = 'system-message';
                        if (animate) systemDiv.style.opacity = '0';

                        systemDiv.innerHTML =
                            `<i class="fas fa-info-circle me-2"></i>${escapeHtml(messageData.message)}`;
                        chatMessages.appendChild(systemDiv);

                        if (animate) {
                            setTimeout(() => systemDiv.style.opacity = '1', 100);
                        }
                    } else {
                        const messageDiv = document.createElement('div');
                        messageDiv.className = `message ${isCurrentUser ? 'sent' : 'received'}`;
                        if (animate) messageDiv.style.opacity = '0';

                        let messageContent = `<div class="message-bubble">`;

                        if (messageData.message) {
                            messageContent += `<div>${escapeHtml(messageData.message).replace(/\n/g, '<br>')}</div>`;
                        }

                        if (messageData.attachment) {
                            messageContent += `<div class="attachment-preview">`;
                            if (messageData.attachment_type === 'image') {
                                messageContent +=
                                    `<img src="/storage/${messageData.attachment}" alt="attachment" class="img-fluid rounded">`;
                            } else {
                                messageContent += `
                                <div class="d-flex align-items-center gap-2">
                                    <i class="fas fa-file text-primary"></i>
                                    <a href="/storage/${messageData.attachment}" class="text-decoration-none" target="_blank">
                                        📎 ${messageData.attachment.split('/').pop()}
                                    </a>
                                </div>
                            `;
                            }
                            messageContent += `</div>`;
                        }

                        messageContent += `</div>
                        <div class="message-info">
                            <strong>${isCurrentUser ? 'Bạn' : (messageData.sender?.name || 'Khách hàng')}</strong> • 
                            ${new Date(messageData.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}
                            ${isCurrentUser ? '<i class="fas fa-check-double ms-1 text-primary"></i>' : ''}
                        </div>`;

                        messageDiv.innerHTML = messageContent;
                        chatMessages.appendChild(messageDiv);

                        if (animate) {
                            setTimeout(() => messageDiv.style.opacity = '1', 100);
                        }
                    }

                    scrollToBottom();
                }

                function updateConversationPreview(conversationId, message) {
                    const conversationElement = document.querySelector(`[data-conversation-id="${conversationId}"]`);
                    if (conversationElement) {
                        const lastMessageElement = conversationElement.querySelector('.message-preview');
                        if (lastMessageElement) {
                            lastMessageElement.textContent = message;
                        }
                    }
                }

                function handleFileSelection(e) {
                    const file = e.target.files[0];
                    if (!file) {
                        attachmentPreview.innerHTML = '';
                        return;
                    }

                    // Validate file size (10MB max)
                    if (file.size > 10 * 1024 * 1024) {
                        showNotification('Tệp quá lớn. Vui lòng chọn tệp nhỏ hơn 10MB', 'error');
                        e.target.value = '';
                        return;
                    }

                    if (file.type.startsWith('image/')) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            attachmentPreview.innerHTML = `
                            <div class="attachment-preview">
                                <img src="${e.target.result}" alt="Preview">
                                <div class="file-info">
                                    <strong>${escapeHtml(file.name)}</strong>
                                    <div>${(file.size / 1024).toFixed(2)} KB</div>
                                </div>
                                <button type="button" class="btn-close" onclick="clearAttachment()">×</button>
                            </div>
                        `;
                        };
                        reader.readAsDataURL(file);
                    } else {
                        attachmentPreview.innerHTML = `
                        <div class="attachment-preview">
                            <i class="fas fa-file fa-2x text-primary"></i>
                            <div class="file-info">
                                <strong>${escapeHtml(file.name)}</strong>
                                <div>${(file.size / 1024).toFixed(2)} KB</div>
                            </div>
                            <button type="button" class="btn-close" onclick="clearAttachment()">×</button>
                        </div>
                    `;
                    }
                }

                // Global functions
                window.clearAttachment = function() {
                    attachmentInput.value = '';
                    attachmentPreview.innerHTML = '';
                };

                window.updateStatus = async function(status) {
                    if (!selectedConversationId) {
                        showNotification('Không có cuộc trò chuyện để cập nhật', 'error');
                        return;
                    }

                    const statusNames = {
                        'active': 'Kích hoạt',
                        'resolved': 'Đã giải quyết',
                        'closed': 'Đóng'
                    };

                    if (!confirm(
                            `Bạn có chắc muốn ${statusNames[status].toLowerCase()} cuộc trò chuyện này?`)) {
                        return;
                    }

                    try {
                        const response = await fetch('/branch/chat/update-status', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                    .getAttribute('content'),
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify({
                                conversation_id: selectedConversationId,
                                status: status
                            })
                        });

                        const data = await response.json();

                        if (data.success) {
                            showNotification(`Trạng thái đã được cập nhật thành "${statusNames[status]}"`,
                                'success');
                            setTimeout(() => location.reload(), 1500);
                        } else {
                            throw new Error(data.message || 'Failed to update status');
                        }
                    } catch (error) {
                        console.error('❌ Update status error:', error);
                        showNotification('Lỗi cập nhật trạng thái: ' + error.message, 'error');
                    }
                };

                // COMMENTED OUT: Staff assignment functionality
                /*
                window.assignToStaff = async function() {
                    const staffSelect = document.getElementById('staff-select');
                    const assignNote = document.getElementById('assign-note');
                    
                    if (!staffSelect || !staffSelect.value) {
                        showNotification('Vui lòng chọn nhân viên', 'warning');
                        return;
                    }

                    try {
                        const response = await fetch('/branch/chat/assign-staff', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify({
                                conversation_id: selectedConversationId,
                                staff_id: staffSelect.value,
                                note: assignNote ? assignNote.value : ''
                            })
                        });

                        const data = await response.json();

                        if (data.success) {
                            showNotification('Phân công nhân viên thành công', 'success');
                            const modal = bootstrap.Modal.getInstance(document.getElementById('assignModal'));
                            if (modal) modal.hide();
                            setTimeout(() => location.reload(), 1500);
                        } else {
                            throw new Error(data.message || 'Failed to assign staff');
                        }
                    } catch (error) {
                        console.error('❌ Assign staff error:', error);
                        showNotification('Lỗi phân công nhân viên: ' + error.message, 'error');
                    }
                };
                */

                window.filterConversations = function(filter) {
                    console.log('🔍 Filtering conversations by:', filter);

                    conversationItems.forEach(item => {
                        const status = item.getAttribute('data-status');
                        const priority = item.getAttribute('data-priority');
                        let shouldShow = false;

                        switch (filter) {
                            case 'distributed':
                                shouldShow = status === 'distributed';
                                break;
                            case 'active':
                                shouldShow = ['active', 'open'].includes(status);
                                break;
                            case 'priority':
                                shouldShow = ['high', 'urgent'].includes(priority);
                                break;
                            case 'all':
                            default:
                                shouldShow = true;
                                break;
                        }

                        item.style.display = shouldShow ? '' : 'none';
                    });

                    // Update active stat card
                    document.querySelectorAll('.stat-card').forEach(card => {
                        card.style.transform = '';
                        card.style.boxShadow = '';
                    });

                    const activeCard = document.querySelector(
                        `.stat-card.${filter === 'active' ? 'active' : filter === 'distributed' ? 'new' : filter === 'priority' ? 'priority' : 'total'}`
                    );
                    if (activeCard) {
                        activeCard.style.transform = 'translateY(-4px)';
                        activeCard.style.boxShadow = 'var(--shadow-lg)';
                    }
                };

                function showNotification(message, type = 'success') {
                    // Remove existing notifications
                    document.querySelectorAll('.notification').forEach(n => n.remove());

                    const notification = document.createElement('div');
                    notification.className = `notification ${type}`;
                    notification.style.cssText = `
                    position: fixed;
                    top: 24px;
                    right: 24px;
                    padding: 16px 24px;
                    border-radius: 12px;
                    color: white;
                    font-weight: 600;
                    z-index: 1000;
                    transform: translateX(100%);
                    transition: transform 0.4s ease;
                    box-shadow: var(--shadow-lg);
                    min-width: 300px;
                    max-width: 400px;
                `;

                    // Set background color based on type
                    switch (type) {
                        case 'success':
                            notification.style.background = 'linear-gradient(135deg, var(--success-color), #059669)';
                            break;
                        case 'error':
                            notification.style.background = 'linear-gradient(135deg, var(--danger-color), #dc2626)';
                            break;
                        case 'warning':
                            notification.style.background = 'linear-gradient(135deg, var (--warning-color), #d97706)';
                            break;
                        default:
                            notification.style.background =
                                'linear-gradient(135deg, var(--primary-color), var(--primary-hover))';
                    }

                    notification.textContent = message;
                    document.body.appendChild(notification);

                    // Show notification
                    setTimeout(() => notification.style.transform = 'translateX(0)', 100);

                    // Hide notification after 4 seconds
                    setTimeout(() => {
                        notification.style.transform = 'translateX(100%)';
                        setTimeout(() => notification.remove(), 400);
                    }, 4000);
                }

                function escapeHtml(text) {
                    const div = document.createElement('div');
                    div.textContent = text;
                    return div.innerHTML;
                }

                // Khởi tạo Echo
                window.Echo = new Echo({
                    broadcaster: 'pusher',
                    key: '{{ env('PUSHER_APP_KEY') }}',
                    cluster: '{{ env('PUSHER_APP_CLUSTER') }}',
                    forceTLS: true
                });

                // Lắng nghe sự kiện tin nhắn mới
                window.Echo.channel('chat')
                    .listen('.message.sent', (e) => {
                        if (e.conversation_id == selectedConversationId) {
                            const messageHtml = createMessageElement(e);
                            chatMessages.appendChild(messageHtml);
                            scrollToBottom();
                            playNotificationSound();
                        }
                        updateConversationPreview(e.conversation_id, e.message || '📎 Tệp đính kèm');
                    });

                // Hàm tạo element tin nhắn
                function createMessageElement(message) {
                    const messageDiv = document.createElement('div');
                    messageDiv.className = `message ${message.sender_id == {{ Auth::id() }} ? 'sent' : 'received'}`;

                    let content = `
                        <div class="message-content">
                            <div class="message-header">
                                <span class="sender-name">${message.sender_name}</span>
                                <span class="message-time">${formatTime(message.created_at)}</span>
                            </div>
                            <div class="message-text">${message.message || ''}</div>
                    `;

                    if (message.attachments && message.attachments.length > 0) {
                        content += '<div class="attachments">';
                        message.attachments.forEach(attachment => {
                            if (attachment.type.startsWith('image/')) {
                                content += `
                                    <div class="attachment">
                                        <img src="${attachment.url}" alt="Image" class="img-fluid">
                                    </div>
                                `;
                            } else {
                                content += `
                                    <div class="attachment">
                                        <a href="${attachment.url}" target="_blank" class="file-link">
                                            <i class="fas fa-file"></i> ${attachment.original_name}
                                        </a>
                                    </div>
                                `;
                            }
                        });
                        content += '</div>';
                    }

                    content += '</div>';
                    messageDiv.innerHTML = content;
                    return messageDiv;
                }

                // Hàm cập nhật preview tin nhắn
                function updateConversationPreview(conversationId, lastMessage) {
                    const conversationElement = document.querySelector(`[data-conversation-id="${conversationId}"]`);
                    if (conversationElement) {
                        const lastMessageElement = conversationElement.querySelector('.message-preview');
                        if (lastMessageElement) {
                            lastMessageElement.textContent = lastMessage;
                        }
                    }
                }

                // Hàm phát âm thanh thông báo
                function playNotificationSound() {
                    const audio = new Audio('/sounds/notification.mp3');
                    audio.play();
                }

                // Hàm format thời gian
                function formatTime(timestamp) {
                    const date = new Date(timestamp);
                    return date.toLocaleTimeString('vi-VN', {
                        hour: '2-digit',
                        minute: '2-digit'
                    });
                }

                console.log('🎉 Branch Chat Interface fully loaded and ready!');

            });
        </script>
    @endsection
