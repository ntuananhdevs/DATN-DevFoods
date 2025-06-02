@extends('layouts.admin.contentLayoutMaster')

@section('title', 'Quản lý Chat - Admin')

@section('content')
    <style>
        /* Dark mode variables */
        :root {
            --background: 0 0% 100%;
            --foreground: 222.2 84% 4.9%;
            --card: 0 0% 100%;
            --card-foreground: 222.2 84% 4.9%;
            --popover: 0 0% 100%;
            --popover-foreground: 222.2 84% 4.9%;
            --primary: 221.2 83.2% 53.3%;
            --primary-foreground: 210 40% 98%;
            --secondary: 210 40% 96%;
            --secondary-foreground: 222.2 84% 4.9%;
            --muted: 210 40% 96%;
            --muted-foreground: 215.4 16.3% 46.9%;
            --accent: 210 40% 96%;
            --accent-foreground: 222.2 84% 4.9%;
            --destructive: 0 84.2% 60.2%;
            --destructive-foreground: 210 40% 98%;
            --border: 214.3 31.8% 91.4%;
            --input: 214.3 31.8% 91.4%;
            --ring: 221.2 83.2% 53.3%;
            --radius: 0.5rem;
        }

        .dark {
            --background: 222.2 84% 4.9%;
            --foreground: 210 40% 98%;
            --card: 222.2 84% 4.9%;
            --card-foreground: 210 40% 98%;
            --popover: 222.2 84% 4.9%;
            --popover-foreground: 210 40% 98%;
            --primary: 217.2 91.2% 59.8%;
            --primary-foreground: 222.2 84% 4.9%;
            --secondary: 217.2 32.6% 17.5%;
            --secondary-foreground: 210 40% 98%;
            --muted: 217.2 32.6% 17.5%;
            --muted-foreground: 215 20.2% 65.1%;
            --accent: 217.2 32.6% 17.5%;
            --accent-foreground: 210 40% 98%;
            --destructive: 0 62.8% 30.6%;
            --destructive-foreground: 210 40% 98%;
            --border: 217.2 32.6% 17.5%;
            --input: 217.2 32.6% 17.5%;
            --ring: 224.3 76.3% 94.1%;
        }

        * {
            border-color: hsl(var(--border));
        }

        body {
            background-color: hsl(var(--background));
            color: hsl(var(--foreground));
        }

        .bg-background {
            background-color: hsl(var(--background));
        }

        .bg-foreground {
            background-color: hsl(var(--foreground));
        }

        .bg-card {
            background-color: hsl(var(--card));
        }

        .bg-card-foreground {
            background-color: hsl(var(--card-foreground));
        }

        .bg-popover {
            background-color: hsl(var(--popover));
        }

        .bg-popover-foreground {
            background-color: hsl(var(--popover-foreground));
        }

        .bg-primary {
            background-color: hsl(var(--primary));
        }

        .bg-primary-foreground {
            background-color: hsl(var(--primary-foreground));
        }

        .bg-secondary {
            background-color: hsl(var(--secondary));
        }

        .bg-secondary-foreground {
            background-color: hsl(var(--secondary-foreground));
        }

        .bg-muted {
            background-color: hsl(var(--muted));
        }

        .bg-muted-foreground {
            background-color: hsl(var(--muted-foreground));
        }

        .bg-accent {
            background-color: hsl(var(--accent));
        }

        .bg-accent-foreground {
            background-color: hsl(var(--accent-foreground));
        }

        .bg-destructive {
            background-color: hsl(var(--destructive));
        }

        .bg-destructive-foreground {
            background-color: hsl(var(--destructive-foreground));
        }

        .text-background {
            color: hsl(var(--background));
        }

        .text-foreground {
            color: hsl(var(--foreground));
        }

        .text-card {
            color: hsl(var(--card));
        }

        .text-card-foreground {
            color: hsl(var(--card-foreground));
        }

        .text-popover {
            color: hsl(var(--popover));
        }

        .text-popover-foreground {
            color: hsl(var(--popover-foreground));
        }

        .text-primary {
            color: hsl(var(--primary));
        }

        .text-primary-foreground {
            color: hsl(var(--primary-foreground));
        }

        .text-secondary {
            color: hsl(var(--secondary));
        }

        .text-secondary-foreground {
            color: hsl(var(--secondary-foreground));
        }

        .text-muted {
            color: hsl(var(--muted));
        }

        .text-muted-foreground {
            color: hsl(var(--muted-foreground));
        }

        .text-accent {
            color: hsl(var(--accent));
        }

        .text-accent-foreground {
            color: hsl(var(--accent-foreground));
        }

        .text-destructive {
            color: hsl(var(--destructive));
        }

        .text-destructive-foreground {
            color: hsl(var(--destructive-foreground));
        }

        .border {
            border-color: hsl(var(--border));
        }

        .border-border {
            border-color: hsl(var(--border));
        }

        .border-input {
            border-color: hsl(var(--input));
        }

        /* Custom scrollbar */
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: hsl(var(--muted));
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: hsl(var(--muted-foreground));
            border-radius: 3px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: hsl(var(--accent-foreground));
        }

        /* Chat specific styles */
        .chat-item:hover {
            background-color: hsl(var(--accent));
        }

        .chat-item.active {
            background-color: hsl(var(--primary) / 0.1);
            border-left: 4px solid hsl(var(--primary));
        }

        /* Message bubbles */
        .message-admin {
            background-color: hsl(var(--primary));
            color: hsl(var(--primary-foreground));
        }

        .message-customer {
            background-color: hsl(var(--card));
            color: hsl(var(--card-foreground));
            border: 1px solid hsl(var(--border));
        }

        /* Quick reply buttons */
        .quick-reply-btn {
            background-color: hsl(var(--muted));
            color: hsl(var(--muted-foreground));
            transition: all 0.2s ease;
        }

        .quick-reply-btn:hover {
            background-color: hsl(var(--accent));
            color: hsl(var(--accent-foreground));
        }

        /* Status indicators */
        .status-online {
            background-color: #22c55e;
        }

        .status-offline {
            background-color: #6b7280;
        }

        /* Theme toggle button */
        .theme-toggle {
            position: relative;
            width: 44px;
            height: 24px;
            background-color: hsl(var(--muted));
            border-radius: 12px;
            transition: background-color 0.3s ease;
            cursor: pointer;
            border: 1px solid hsl(var(--border));
        }

        .theme-toggle.dark {
            background-color: hsl(var(--primary));
        }

        .theme-toggle-handle {
            position: absolute;
            top: 2px;
            left: 2px;
            width: 18px;
            height: 18px;
            background-color: hsl(var(--background));
            border-radius: 50%;
            transition: transform 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
        }

        .theme-toggle.dark .theme-toggle-handle {
            transform: translateX(20px);
        }

        /* Input focus styles */
        input:focus,
        textarea:focus,
        select:focus {
            outline: none;
            border-color: hsl(var(--primary));
            box-shadow: 0 0 0 2px hsl(var(--primary) / 0.2);
        }

        /* Button styles */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            font-size: 0.875rem;
            font-weight: 500;
            transition: all 0.2s ease;
            cursor: pointer;
            border: 1px solid transparent;
        }

        .btn-primary {
            background-color: hsl(var(--primary));
            color: hsl(var(--primary-foreground));
        }

        .btn-primary:hover {
            background-color: hsl(var(--primary) / 0.9);
        }

        .btn-secondary {
            background-color: hsl(var(--secondary));
            color: hsl(var(--secondary-foreground));
        }

        .btn-secondary:hover {
            background-color: hsl(var(--secondary) / 0.8);
        }

        .btn-outline {
            border: 1px solid hsl(var(--border));
            background-color: hsl(var(--background));
            color: hsl(var(--foreground));
        }

        .btn-outline:hover {
            background-color: hsl(var(--accent));
            color: hsl(var(--accent-foreground));
        }

        .btn-destructive {
            background-color: hsl(var(--destructive));
            color: hsl(var(--destructive-foreground));
        }

        .btn-destructive:hover {
            background-color: hsl(var(--destructive) / 0.9);
        }

        /* Card styles */
        .card {
            background-color: hsl(var(--card));
            color: hsl(var(--card-foreground));
            border: 1px solid hsl(var(--border));
            border-radius: 0.5rem;
        }

        /* Modal styles */
        .modal-overlay {
            background-color: rgba(0, 0, 0, 0.5);
        }

        .modal-content {
            background-color: hsl(var(--card));
            color: hsl(var(--card-foreground));
            border: 1px solid hsl(var(--border));
        }
    </style>

    <div class="min-h-screen bg-background">
        <!-- Header -->
        <div class="bg-card shadow-sm border-b border-border">
            <div class="px-6 py-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-foreground">Quản lý Chat</h1>
                        <p class="text-muted-foreground">Hỗ trợ khách hàng trực tuyến</p>
                    </div>
                    <div class="flex items-center gap-4">
                        <!-- Theme Toggle -->
                        <div class="flex items-center gap-2">
                            <span class="text-sm text-muted-foreground">Theme:</span>
                            <button id="themeToggle" class="theme-toggle">
                                <div class="theme-toggle-handle">
                                    <span id="themeIcon">🌙</span>
                                </div>
                            </button>
                        </div>

                        <!-- Admin Status Toggle -->
                        <div class="flex items-center gap-2">
                            <span class="text-sm text-muted-foreground">Trạng thái:</span>
                            <button id="statusToggle"
                                class="relative inline-flex h-6 w-11 items-center rounded-full bg-green-500 transition-colors">
                                <span
                                    class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform translate-x-6"></span>
                            </button>
                            <span id="statusText" class="text-sm font-medium text-green-600">Online</span>
                        </div>

                        <!-- Notifications -->
                        <button class="relative p-2 text-muted-foreground hover:text-foreground hover:bg-accent rounded-lg">
                            <i class="fas fa-bell text-lg"></i>
                            <span id="notificationBadge"
                                class="absolute -top-1 -right-1 h-5 w-5 bg-red-500 text-white text-xs rounded-full flex items-center justify-center hidden">0</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex h-[calc(100vh-120px)]">
            <!-- Chat List Sidebar -->
            <div class="w-80 bg-card border-r border-border flex flex-col">
                <!-- Search & Filter -->
                <div class="p-4 border-b border-border">
                    <div class="relative mb-3">
                        <input type="text" id="searchChats" placeholder="Tìm kiếm cuộc trò chuyện..."
                            class="w-full pl-10 pr-4 py-2 border border-input rounded-lg bg-background text-foreground focus:ring-2 focus:ring-primary focus:border-primary">
                        <i
                            class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-muted-foreground"></i>
                    </div>

                    <div class="flex gap-2">
                        <select id="filterStatus"
                            class="flex-1 text-sm border border-input rounded-lg px-3 py-1 bg-background text-foreground focus:ring-2 focus:ring-primary">
                            <option value="all">Tất cả</option>
                            <option value="active">Đang hoạt động</option>
                            <option value="waiting">Chờ phản hồi</option>
                            <option value="closed">Đã đóng</option>
                        </select>
                        <button id="refreshChats"
                            class="px-3 py-1 text-muted-foreground hover:text-foreground hover:bg-accent rounded-lg">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                    </div>
                </div>

                <!-- Chat List -->
                <div id="chatList" class="flex-1 overflow-y-auto custom-scrollbar">
                    <!-- Chat items will be populated here -->
                </div>
            </div>

            <!-- Main Chat Area -->
            <div class="flex-1 flex flex-col">
                <!-- No Chat Selected State -->
                <div id="noChatSelected" class="flex-1 flex items-center justify-center bg-muted/30">
                    <div class="text-center">
                        <div class="w-24 h-24 bg-muted rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-comments text-3xl text-muted-foreground"></i>
                        </div>
                        <h3 class="text-lg font-medium text-foreground mb-2">Chọn cuộc trò chuyện</h3>
                        <p class="text-muted-foreground">Chọn một cuộc trò chuyện từ danh sách để bắt đầu hỗ trợ khách hàng
                        </p>
                    </div>
                </div>

                <!-- Active Chat -->
                <div id="activeChat" class="flex-1 flex-col hidden">
                    <!-- Chat Header -->
                    <div id="chatHeader" class="bg-card border-b border-border px-6 py-4">
                        <!-- Will be populated when chat is selected -->
                    </div>

                    <!-- Messages Area -->
                    <div id="messagesArea" class="flex-1 overflow-y-auto p-6 bg-muted/30 custom-scrollbar">
                        <!-- Messages will be populated here -->
                    </div>

                    <!-- Quick Replies -->
                    <div class="bg-card border-t border-border px-6 py-3">
                        <div class="flex gap-2 flex-wrap">
                            <button class="quick-reply-btn px-3 py-1 text-sm rounded-full transition-colors">
                                Xin chào! Tôi có thể giúp gì cho bạn?
                            </button>
                            <button class="quick-reply-btn px-3 py-1 text-sm rounded-full transition-colors">
                                Để tôi kiểm tra thông tin cho bạn
                            </button>
                            <button class="quick-reply-btn px-3 py-1 text-sm rounded-full transition-colors">
                                Cảm ơn bạn đã liên hệ với FastFood
                            </button>
                            <button class="quick-reply-btn px-3 py-1 text-sm rounded-full transition-colors">
                                Bạn có cần hỗ trợ thêm không?
                            </button>
                        </div>
                    </div>

                    <!-- Message Input -->
                    <div class="bg-card border-t border-border px-6 py-4">
                        <div class="flex items-end gap-4">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-3">
                                    <button id="attachFileBtn"
                                        class="p-2 text-muted-foreground hover:text-primary hover:bg-primary/10 rounded-lg transition-colors">
                                        <i class="fas fa-paperclip"></i>
                                    </button>
                                    <button id="attachImageBtn"
                                        class="p-2 text-muted-foreground hover:text-primary hover:bg-primary/10 rounded-lg transition-colors">
                                        <i class="fas fa-image"></i>
                                    </button>
                                    <button id="emojiBtn"
                                        class="p-2 text-muted-foreground hover:text-primary hover:bg-primary/10 rounded-lg transition-colors">
                                        <i class="fas fa-smile"></i>
                                    </button>
                                    <button id="templateBtn"
                                        class="p-2 text-muted-foreground hover:text-primary hover:bg-primary/10 rounded-lg transition-colors">
                                        <i class="fas fa-file-alt"></i>
                                    </button>
                                </div>

                                <div class="relative">
                                    <textarea id="messageInput" placeholder="Nhập tin nhắn..."
                                        class="w-full min-h-[60px] max-h-[120px] resize-none border border-input rounded-lg px-4 py-3 pr-12 bg-background text-foreground focus:ring-2 focus:ring-primary focus:border-primary"
                                        rows="2"></textarea>

                                    <!-- Emoji Picker -->
                                    <div id="emojiPicker"
                                        class="absolute bottom-full right-0 mb-2 bg-popover border border-border rounded-lg p-3 shadow-lg z-10 hidden">
                                        <div class="grid grid-cols-8 gap-1" id="emojiGrid">
                                            <!-- Emojis will be populated here -->
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="flex flex-col gap-2">
                                <button id="sendBtn"
                                    class="btn btn-primary px-6 py-3 rounded-lg disabled:opacity-50 disabled:cursor-not-allowed"
                                    disabled>
                                    <i class="fas fa-paper-plane"></i>
                                </button>
                                <button id="closeChatBtn" class="btn btn-destructive px-6 py-3 rounded-lg">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Customer Info Sidebar -->
            <div id="customerInfo" class="w-80 bg-card border-l border-border hidden">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4 text-foreground">Thông tin khách hàng</h3>
                    <div id="customerDetails">
                        <!-- Customer details will be populated here -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Templates Modal -->
    <div id="templatesModal" class="fixed inset-0 modal-overlay flex items-center justify-center z-50 hidden">
        <div class="modal-content rounded-lg w-full max-w-2xl mx-4 max-h-[80vh] overflow-hidden">
            <div class="flex items-center justify-between p-6 border-b border-border">
                <h3 class="text-lg font-semibold text-foreground">Mẫu tin nhắn</h3>
                <button id="closeTemplatesBtn" class="text-muted-foreground hover:text-foreground">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="p-6 overflow-y-auto max-h-[60vh]">
                <div class="grid gap-4">
                    <div
                        class="template-item p-4 border border-border rounded-lg hover:border-primary cursor-pointer transition-colors">
                        <h4 class="font-medium mb-2 text-foreground">Chào mừng khách hàng</h4>
                        <p class="text-sm text-muted-foreground">Xin chào! Cảm ơn bạn đã liên hệ với FastFood. Tôi có thể
                            giúp gì cho bạn hôm nay?</p>
                    </div>

                    <div
                        class="template-item p-4 border border-border rounded-lg hover:border-primary cursor-pointer transition-colors">
                        <h4 class="font-medium mb-2 text-foreground">Hỏi thông tin đơn hàng</h4>
                        <p class="text-sm text-muted-foreground">Bạn có thể cung cấp mã đơn hàng để tôi kiểm tra thông tin
                            cho bạn không?</p>
                    </div>

                    <div
                        class="template-item p-4 border border-border rounded-lg hover:border-primary cursor-pointer transition-colors">
                        <h4 class="font-medium mb-2 text-foreground">Hướng dẫn đặt hàng</h4>
                        <p class="text-sm text-muted-foreground">Để đặt hàng, bạn có thể truy cập website của chúng tôi
                            hoặc gọi hotline 1900-xxxx. Chúng tôi sẽ giao hàng trong vòng 30 phút.</p>
                    </div>

                    <div
                        class="template-item p-4 border border-border rounded-lg hover:border-primary cursor-pointer transition-colors">
                        <h4 class="font-medium mb-2 text-foreground">Thông tin khuyến mãi</h4>
                        <p class="text-sm text-muted-foreground">Hiện tại chúng tôi có chương trình giảm 20% cho đơn hàng
                            từ 200k và miễn phí giao hàng trong bán kính 5km.</p>
                    </div>

                    <div
                        class="template-item p-4 border border-border rounded-lg hover:border-primary cursor-pointer transition-colors">
                        <h4 class="font-medium mb-2 text-foreground">Hỗ trợ kỹ thuật</h4>
                        <p class="text-sm text-muted-foreground">Nếu bạn gặp vấn đề với ứng dụng, vui lòng thử tắt và mở
                            lại app. Nếu vẫn không được, tôi sẽ chuyển cho bộ phận kỹ thuật.</p>
                    </div>

                    <div
                        class="template-item p-4 border border-border rounded-lg hover:border-primary cursor-pointer transition-colors">
                        <h4 class="font-medium mb-2 text-foreground">Kết thúc hỗ trợ</h4>
                        <p class="text-sm text-muted-foreground">Cảm ơn bạn đã liên hệ với chúng tôi. Nếu có thắc mắc gì
                            khác, đừng ngần ngại liên hệ nhé! Chúc bạn một ngày tốt lành!</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Hidden file inputs -->
    <input type="file" id="fileInput" class="hidden" accept=".pdf,.doc,.docx,.txt,.zip,.rar">
    <input type="file" id="imageInput" class="hidden" accept="image/*">

@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Theme Management
            const themeToggle = document.getElementById('themeToggle');
            const themeIcon = document.getElementById('themeIcon');
            const html = document.documentElement;

            // Load saved theme or default to light
            const savedTheme = localStorage.getItem('theme') || 'light';
            setTheme(savedTheme);

            function setTheme(theme) {
                if (theme === 'dark') {
                    html.classList.add('dark');
                    themeToggle.classList.add('dark');
                    themeIcon.textContent = '☀️';
                } else {
                    html.classList.remove('dark');
                    themeToggle.classList.remove('dark');
                    themeIcon.textContent = '🌙';
                }
                localStorage.setItem('theme', theme);
            }

            themeToggle.addEventListener('click', function() {
                const currentTheme = html.classList.contains('dark') ? 'dark' : 'light';
                const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
                setTheme(newTheme);
            });

            // Admin Chat Management System
            const chatList = document.getElementById('chatList');
            const noChatSelected = document.getElementById('noChatSelected');
            const activeChat = document.getElementById('activeChat');
            const chatHeader = document.getElementById('chatHeader');
            const messagesArea = document.getElementById('messagesArea');
            const messageInput = document.getElementById('messageInput');
            const sendBtn = document.getElementById('sendBtn');
            const customerInfo = document.getElementById('customerInfo');
            const customerDetails = document.getElementById('customerDetails');
            const statusToggle = document.getElementById('statusToggle');
            const statusText = document.getElementById('statusText');
            const notificationBadge = document.getElementById('notificationBadge');
            const searchChats = document.getElementById('searchChats');
            const filterStatus = document.getElementById('filterStatus');
            const refreshChats = document.getElementById('refreshChats');
            const templatesModal = document.getElementById('templatesModal');
            const templateBtn = document.getElementById('templateBtn');
            const closeTemplatesBtn = document.getElementById('closeTemplatesBtn');
            const emojiBtn = document.getElementById('emojiBtn');
            const emojiPicker = document.getElementById('emojiPicker');
            const emojiGrid = document.getElementById('emojiGrid');
            const attachFileBtn = document.getElementById('attachFileBtn');
            const attachImageBtn = document.getElementById('attachImageBtn');
            const fileInput = document.getElementById('fileInput');
            const imageInput = document.getElementById('imageInput');
            const closeChatBtn = document.getElementById('closeChatBtn');

            // State
            let isAdminOnline = true;
            let currentChatId = null;
            let chats = [];
            let unreadCount = 0;

            // Emojis
            const emojis = ['😊', '😂', '❤️', '👍', '👎', '😢', '😮', '😡', '🙏', '👏', '🎉', '🔥', '💯', '✅', '❌',
                '⚡', '🍕', '🍔', '🍟', '🥤'
            ];

            // Initialize
            init();

            function init() {
                setupEventListeners();
                populateEmojis();
                loadChats();
                startNotificationCheck();
            }

            function setupEventListeners() {
                statusToggle.addEventListener('click', toggleAdminStatus);
                sendBtn.addEventListener('click', sendMessage);
                messageInput.addEventListener('input', handleInputChange);
                messageInput.addEventListener('keypress', handleKeyPress);
                searchChats.addEventListener('input', filterChats);
                filterStatus.addEventListener('change', filterChats);
                refreshChats.addEventListener('click', loadChats);
                templateBtn.addEventListener('click', showTemplatesModal);
                closeTemplatesBtn.addEventListener('click', hideTemplatesModal);
                emojiBtn.addEventListener('click', toggleEmojiPicker);
                attachFileBtn.addEventListener('click', () => fileInput.click());
                attachImageBtn.addEventListener('click', () => imageInput.click());
                fileInput.addEventListener('change', handleFileUpload);
                imageInput.addEventListener('change', handleImageUpload);
                closeChatBtn.addEventListener('click', closeCurrentChat);

                // Quick reply buttons
                document.addEventListener('click', function(e) {
                    if (e.target.classList.contains('quick-reply-btn')) {
                        messageInput.value = e.target.textContent;
                        handleInputChange();
                        messageInput.focus();
                    }
                });

                // Template selection
                document.addEventListener('click', function(e) {
                    if (e.target.closest('.template-item')) {
                        const template = e.target.closest('.template-item');
                        const text = template.querySelector('p').textContent;
                        messageInput.value = text;
                        handleInputChange();
                        hideTemplatesModal();
                        messageInput.focus();
                    }
                });

                // Close emoji picker when clicking outside
                document.addEventListener('click', function(e) {
                    if (!emojiPicker.contains(e.target) && !emojiBtn.contains(e.target)) {
                        hideEmojiPicker();
                    }
                });

                // Auto-resize textarea
                messageInput.addEventListener('input', autoResizeTextarea);
            }

            function populateEmojis() {
                emojiGrid.innerHTML = '';
                emojis.forEach(emoji => {
                    const button = document.createElement('button');
                    button.textContent = emoji;
                    button.className = 'p-2 hover:bg-accent rounded text-lg transition-colors';
                    button.addEventListener('click', () => addEmoji(emoji));
                    emojiGrid.appendChild(button);
                });
            }

            function toggleAdminStatus() {
                isAdminOnline = !isAdminOnline;
                updateStatusDisplay();

                // Send status update to server
                // axios.post('/api/admin/chat/status', {
                //     online: isAdminOnline
                // }).catch(error => {
                //     console.error('Failed to update status:', error);
                // });
            }

            function updateStatusDisplay() {
                const toggle = statusToggle.querySelector('span');

                if (isAdminOnline) {
                    statusToggle.className =
                        'relative inline-flex h-6 w-11 items-center rounded-full bg-green-500 transition-colors';
                    toggle.className =
                        'inline-block h-4 w-4 transform rounded-full bg-white transition-transform translate-x-6';
                    statusText.textContent = 'Online';
                    statusText.className = 'text-sm font-medium text-green-600';
                } else {
                    statusToggle.className =
                        'relative inline-flex h-6 w-11 items-center rounded-full bg-gray-300 transition-colors';
                    toggle.className =
                        'inline-block h-4 w-4 transform rounded-full bg-white transition-transform translate-x-1';
                    statusText.textContent = 'Offline';
                    statusText.className = 'text-sm font-medium text-gray-600';
                }
            }

            function loadChats() {
                // Enhanced sample data with more realistic conversations
                chats = [{
                        id: '1',
                        customer: {
                            name: 'Nguyễn Văn Minh',
                            email: 'nguyenvanminh@gmail.com',
                            phone: '0123456789',
                            avatar: '/placeholder.svg?height=40&width=40'
                        },
                        lastMessage: 'Tôi muốn hỏi về combo gia đình mới',
                        lastMessageTime: new Date(Date.now() - 120000), // 2 minutes ago
                        status: 'waiting',
                        unread: 3,
                        messages: [{
                                id: '1',
                                content: 'Xin chào! Tôi muốn hỏi về combo gia đình mới mà FastFood vừa ra mắt',
                                sender: 'customer',
                                timestamp: new Date(Date.now() - 300000),
                                type: 'text'
                            },
                            {
                                id: '2',
                                content: 'Combo này có những món gì và giá bao nhiêu ạ?',
                                sender: 'customer',
                                timestamp: new Date(Date.now() - 240000),
                                type: 'text'
                            },
                            {
                                id: '3',
                                content: 'Tôi muốn hỏi về combo gia đình mới',
                                sender: 'customer',
                                timestamp: new Date(Date.now() - 120000),
                                type: 'text'
                            }
                        ]
                    },
                    {
                        id: '2',
                        customer: {
                            name: 'Trần Thị Hương',
                            email: 'tranhuong2024@email.com',
                            phone: '0987654321',
                            avatar: '/placeholder.svg?height=40&width=40'
                        },
                        lastMessage: 'Cảm ơn bạn đã hỗ trợ nhiệt tình!',
                        lastMessageTime: new Date(Date.now() - 480000), // 8 minutes ago
                        status: 'active',
                        unread: 0,
                        messages: [{
                                id: '1',
                                content: 'Chào bạn! Tôi vừa đặt đơn hàng nhưng quên không chọn thêm nước uống',
                                sender: 'customer',
                                timestamp: new Date(Date.now() - 1200000),
                                type: 'text'
                            },
                            {
                                id: '2',
                                content: 'Xin chào! Để tôi kiểm tra đơn hàng của bạn. Bạn có thể cung cấp mã đơn hàng không?',
                                sender: 'admin',
                                timestamp: new Date(Date.now() - 1080000),
                                type: 'text'
                            },
                            {
                                id: '3',
                                content: 'Mã đơn hàng là FF2024002 ạ',
                                sender: 'customer',
                                timestamp: new Date(Date.now() - 1020000),
                                type: 'text'
                            },
                            {
                                id: '4',
                                content: 'Tôi đã cập nhật đơn hàng và thêm 2 ly Coca cho bạn. Không tính thêm phí giao hàng nhé!',
                                sender: 'admin',
                                timestamp: new Date(Date.now() - 900000),
                                type: 'text'
                            },
                            {
                                id: '5',
                                content: 'Cảm ơn bạn đã hỗ trợ nhiệt tình!',
                                sender: 'customer',
                                timestamp: new Date(Date.now() - 480000),
                                type: 'text'
                            }
                        ]
                    },
                    {
                        id: '3',
                        customer: {
                            name: 'Lê Hoàng Nam',
                            email: 'hoangnam.dev@outlook.com',
                            phone: '0369852147',
                            avatar: '/placeholder.svg?height=40&width=40'
                        },
                        lastMessage: 'Đơn hàng #FF2024001 của tôi bị delay không ạ?',
                        lastMessageTime: new Date(Date.now() - 900000), // 15 minutes ago
                        status: 'waiting',
                        unread: 1,
                        messages: [{
                                id: '1',
                                content: 'Chào admin! Tôi đặt đơn hàng từ 45 phút trước rồi mà chưa thấy shipper liên hệ',
                                sender: 'customer',
                                timestamp: new Date(Date.now() - 1200000),
                                type: 'text'
                            },
                            {
                                id: '2',
                                content: 'Đơn hàng #FF2024001 của tôi bị delay không ạ?',
                                sender: 'customer',
                                timestamp: new Date(Date.now() - 900000),
                                type: 'text'
                            }
                        ]
                    },
                    {
                        id: '4',
                        customer: {
                            name: 'Phạm Thị Lan',
                            email: 'phamlan.work@gmail.com',
                            phone: '0912345678',
                            avatar: '/placeholder.svg?height=40&width=40'
                        },
                        lastMessage: 'Tôi có thể đổi địa chỉ giao hàng được không?',
                        lastMessageTime: new Date(Date.now() - 1500000), // 25 minutes ago
                        status: 'active',
                        unread: 0,
                        messages: [{
                                id: '1',
                                content: 'Xin chào! Tôi vừa đặt đơn hàng nhưng cần đổi địa chỉ giao hàng',
                                sender: 'customer',
                                timestamp: new Date(Date.now() - 1800000),
                                type: 'text'
                            },
                            {
                                id: '2',
                                content: 'Chào bạn! Tôi có thể hỗ trợ bạn thay đổi địa chỉ. Bạn cho tôi mã đơn hàng nhé',
                                sender: 'admin',
                                timestamp: new Date(Date.now() - 1680000),
                                type: 'text'
                            },
                            {
                                id: '3',
                                content: 'Mã đơn hàng FF2024003. Địa chỉ mới là: 123 Nguyễn Văn Linh, Quận 7, TP.HCM',
                                sender: 'customer',
                                timestamp: new Date(Date.now() - 1620000),
                                type: 'text'
                            },
                            {
                                id: '4',
                                content: 'Đã cập nhật địa chỉ mới cho bạn. Shipper sẽ giao đến địa chỉ mới trong 20 phút nữa',
                                sender: 'admin',
                                timestamp: new Date(Date.now() - 1560000),
                                type: 'text'
                            },
                            {
                                id: '5',
                                content: 'Tôi có thể đổi địa chỉ giao hàng được không?',
                                sender: 'customer',
                                timestamp: new Date(Date.now() - 1500000),
                                type: 'text'
                            }
                        ]
                    },
                    {
                        id: '5',
                        customer: {
                            name: 'Võ Minh Tuấn',
                            email: 'vominhtuan88@yahoo.com',
                            phone: '0834567890',
                            avatar: '/placeholder.svg?height=40&width=40'
                        },
                        lastMessage: 'Món ăn rất ngon, cảm ơn FastFood!',
                        lastMessageTime: new Date(Date.now() - 3600000), // 1 hour ago
                        status: 'closed',
                        unread: 0,
                        messages: [{
                                id: '1',
                                content: 'Tôi vừa nhận được đơn hàng. Món ăn rất ngon!',
                                sender: 'customer',
                                timestamp: new Date(Date.now() - 3720000),
                                type: 'text'
                            },
                            {
                                id: '2',
                                content: 'Cảm ơn bạn đã đánh giá! Chúng tôi rất vui khi bạn hài lòng với dịch vụ',
                                sender: 'admin',
                                timestamp: new Date(Date.now() - 3660000),
                                type: 'text'
                            },
                            {
                                id: '3',
                                content: 'Món ăn rất ngon, cảm ơn FastFood!',
                                sender: 'customer',
                                timestamp: new Date(Date.now() - 3600000),
                                type: 'text'
                            }
                        ]
                    },
                    {
                        id: '6',
                        customer: {
                            name: 'Đặng Thị Mai',
                            email: 'dangmai.student@edu.vn',
                            phone: '0756789012',
                            avatar: '/placeholder.svg?height=40&width=40'
                        },
                        lastMessage: 'Có chương trình khuyến mãi nào cho sinh viên không ạ?',
                        lastMessageTime: new Date(Date.now() - 7200000), // 2 hours ago
                        status: 'waiting',
                        unread: 2,
                        messages: [{
                                id: '1',
                                content: 'Chào FastFood! Mình là sinh viên, có chương trình ưu đãi gì không ạ?',
                                sender: 'customer',
                                timestamp: new Date(Date.now() - 7500000),
                                type: 'text'
                            },
                            {
                                id: '2',
                                content: 'Có chương trình khuyến mãi nào cho sinh viên không ạ?',
                                sender: 'customer',
                                timestamp: new Date(Date.now() - 7200000),
                                type: 'text'
                            }
                        ]
                    },
                    {
                        id: '7',
                        customer: {
                            name: 'Bùi Văn Đức',
                            email: 'buivanduc.biz@company.com',
                            phone: '0678901234',
                            avatar: '/placeholder.svg?height=40&width=40'
                        },
                        lastMessage: 'Tôi muốn đặt tiệc cho 50 người',
                        lastMessageTime: new Date(Date.now() - 10800000), // 3 hours ago
                        status: 'active',
                        unread: 0,
                        messages: [{
                                id: '1',
                                content: 'Xin chào! Công ty tôi muốn đặt tiệc cho sự kiện 50 người',
                                sender: 'customer',
                                timestamp: new Date(Date.now() - 11400000),
                                type: 'text'
                            },
                            {
                                id: '2',
                                content: 'Chào anh! Chúng tôi có gói tiệc doanh nghiệp rất phù hợp. Tôi sẽ gửi bảng giá cho anh',
                                sender: 'admin',
                                timestamp: new Date(Date.now() - 11280000),
                                type: 'text'
                            },
                            {
                                id: '3',
                                content: 'Tôi muốn đặt tiệc cho 50 người',
                                sender: 'customer',
                                timestamp: new Date(Date.now() - 10800000),
                                type: 'text'
                            }
                        ]
                    },
                    {
                        id: '8',
                        customer: {
                            name: 'Hoàng Thị Linh',
                            email: 'hoanglinh.designer@creative.vn',
                            phone: '0590123456',
                            avatar: '/placeholder.svg?height=40&width=40'
                        },
                        lastMessage: 'Cảm ơn đã giải quyết vấn đề!',
                        lastMessageTime: new Date(Date.now() - 18000000), // 5 hours ago
                        status: 'closed',
                        unread: 0,
                        messages: [{
                                id: '1',
                                content: 'Tôi gặp lỗi khi thanh toán qua ví điện tử',
                                sender: 'customer',
                                timestamp: new Date(Date.now() - 18600000),
                                type: 'text'
                            },
                            {
                                id: '2',
                                content: 'Tôi sẽ kiểm tra và hỗ trợ bạn ngay. Bạn đang sử dụng ví nào?',
                                sender: 'admin',
                                timestamp: new Date(Date.now() - 18480000),
                                type: 'text'
                            },
                            {
                                id: '3',
                                content: 'Đã khắc phục xong lỗi thanh toán. Bạn thử lại nhé!',
                                sender: 'admin',
                                timestamp: new Date(Date.now() - 18120000),
                                type: 'text'
                            },
                            {
                                id: '4',
                                content: 'Cảm ơn đã giải quyết vấn đề!',
                                sender: 'customer',
                                timestamp: new Date(Date.now() - 18000000),
                                type: 'text'
                            }
                        ]
                    }
                ];

                renderChatList();
                updateNotificationBadge();
            }

            function renderChatList() {
                chatList.innerHTML = '';

                const filteredChats = getFilteredChats();

                filteredChats.forEach(chat => {
                    const chatItem = createChatListItem(chat);
                    chatList.appendChild(chatItem);
                });
            }

            function getFilteredChats() {
                let filtered = chats;

                // Filter by search
                const searchTerm = searchChats.value.toLowerCase();
                if (searchTerm) {
                    filtered = filtered.filter(chat =>
                        chat.customer.name.toLowerCase().includes(searchTerm) ||
                        chat.lastMessage.toLowerCase().includes(searchTerm)
                    );
                }

                // Filter by status
                const statusFilter = filterStatus.value;
                if (statusFilter !== 'all') {
                    filtered = filtered.filter(chat => chat.status === statusFilter);
                }

                return filtered.sort((a, b) => b.lastMessageTime - a.lastMessageTime);
            }

            function createChatListItem(chat) {
                const div = document.createElement('div');
                div.className =
                    `chat-item p-4 border-b border-border hover:bg-accent cursor-pointer transition-colors ${currentChatId === chat.id ? 'active' : ''}`;
                div.dataset.chatId = chat.id;

                const timeString = formatTime(chat.lastMessageTime);
                const statusColor = getStatusColor(chat.status);

                div.innerHTML = `
            <div class="flex items-start gap-3">
                <div class="relative">
                    <img src="${chat.customer.avatar}" alt="${chat.customer.name}" class="w-12 h-12 rounded-full object-cover">
                    <div class="absolute -bottom-1 -right-1 w-4 h-4 ${statusColor} rounded-full border-2 border-card"></div>
                </div>
                
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between mb-1">
                        <h4 class="font-medium text-foreground truncate">${chat.customer.name}</h4>
                        <span class="text-xs text-muted-foreground">${timeString}</span>
                    </div>
                    
                    <p class="text-sm text-muted-foreground truncate mb-1">${chat.lastMessage}</p>
                    
                    <div class="flex items-center justify-between">
                        <span class="text-xs px-2 py-1 rounded-full ${getStatusBadgeClass(chat.status)}">${getStatusText(chat.status)}</span>
                        ${chat.unread > 0 ? `<span class="bg-red-500 text-white text-xs px-2 py-1 rounded-full">${chat.unread}</span>` : ''}
                    </div>
                </div>
            </div>
        `;

                div.addEventListener('click', () => selectChat(chat.id));

                return div;
            }

            function selectChat(chatId) {
                currentChatId = chatId;
                const chat = chats.find(c => c.id === chatId);

                if (chat) {
                    // Mark as read
                    chat.unread = 0;

                    // Update UI
                    renderChatList();
                    showActiveChat(chat);
                    updateNotificationBadge();
                }
            }

            function showActiveChat(chat) {
                noChatSelected.classList.add('hidden');
                activeChat.classList.remove('hidden');
                activeChat.classList.add('flex');
                customerInfo.classList.remove('hidden');

                // Update chat header
                chatHeader.innerHTML = `
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <img src="${chat.customer.avatar}" alt="${chat.customer.name}" class="w-10 h-10 rounded-full object-cover">
                    <div>
                        <h3 class="font-semibold text-foreground">${chat.customer.name}</h3>
                        <p class="text-sm text-muted-foreground">${chat.customer.email}</p>
                    </div>
                </div>
                
                <div class="flex items-center gap-2">
                    <span class="text-xs px-3 py-1 rounded-full ${getStatusBadgeClass(chat.status)}">${getStatusText(chat.status)}</span>
                    <button class="p-2 text-muted-foreground hover:text-foreground hover:bg-accent rounded-lg">
                        <i class="fas fa-phone"></i>
                    </button>
                    <button class="p-2 text-muted-foreground hover:text-foreground hover:bg-accent rounded-lg">
                        <i class="fas fa-video"></i>
                    </button>
                    <button class="p-2 text-muted-foreground hover:text-foreground hover:bg-accent rounded-lg">
                        <i class="fas fa-ellipsis-v"></i>
                    </button>
                </div>
            </div>
        `;

                // Update customer info
                customerDetails.innerHTML = `
            <div class="space-y-4">
                <div class="text-center">
                    <img src="${chat.customer.avatar}" alt="${chat.customer.name}" class="w-20 h-20 rounded-full object-cover mx-auto mb-3">
                    <h4 class="font-semibold text-foreground">${chat.customer.name}</h4>
                    <p class="text-sm text-muted-foreground">${chat.customer.email}</p>
                </div>
                
                <div class="space-y-3">
                    <div>
                        <label class="text-sm font-medium text-foreground">Số điện thoại</label>
                        <p class="text-sm text-muted-foreground">${chat.customer.phone}</p>
                    </div>
                    
                    <div>
                        <label class="text-sm font-medium text-foreground">Trạng thái</label>
                        <p class="text-sm"><span class="px-2 py-1 rounded-full ${getStatusBadgeClass(chat.status)}">${getStatusText(chat.status)}</span></p>
                    </div>
                    
                    <div>
                        <label class="text-sm font-medium text-foreground">Lần cuối hoạt động</label>
                        <p class="text-sm text-muted-foreground">${formatTime(chat.lastMessageTime)}</p>
                    </div>
                    
                    <div>
                        <label class="text-sm font-medium text-foreground">Tổng đơn hàng</label>
                        <p class="text-sm text-muted-foreground">${Math.floor(Math.random() * 20) + 5} đơn</p>
                    </div>
                </div>
                
                <div class="pt-4 border-t border-border space-y-2">
                    <button class="w-full btn btn-primary py-2 px-4 rounded-lg">
                        Xem lịch sử đơn hàng
                    </button>
                    <button class="w-full btn btn-secondary py-2 px-4 rounded-lg">
                        Ghi chú khách hàng
                    </button>
                </div>
            </div>
        `;

                // Render messages
                renderMessages(chat.messages);
            }

            function renderMessages(messages) {
                messagesArea.innerHTML = '';

                messages.forEach(message => {
                    const messageElement = createMessageElement(message);
                    messagesArea.appendChild(messageElement);
                });

                scrollToBottom();
            }

            function createMessageElement(message) {
                const div = document.createElement('div');
                div.className = `flex ${message.sender === 'admin' ? 'justify-end' : 'justify-start'} mb-4`;

                const timeString = formatTime(message.timestamp);
                const isAdmin = message.sender === 'admin';

                let messageContent = '';

                if (message.type === 'text') {
                    messageContent = `<p class="text-sm whitespace-pre-wrap">${escapeHtml(message.content)}</p>`;
                } else if (message.type === 'image') {
                    messageContent = `
                <div class="space-y-2">
                    <img src="${message.imageUrl}" alt="Image" class="max-w-full h-auto rounded-lg max-h-64 object-cover">
                    ${message.content ? `<p class="text-sm">${escapeHtml(message.content)}</p>` : ''}
                </div>
            `;
                } else if (message.type === 'file') {
                    messageContent = `
                <div class="flex items-center gap-3 p-3 bg-white/10 rounded-lg min-w-[200px]">
                    <i class="fas fa-file text-lg"></i>
                    <div class="flex-1">
                        <p class="text-sm font-medium">${escapeHtml(message.fileName)}</p>
                        <p class="text-xs opacity-75">${message.fileSize}</p>
                    </div>
                    <button class="p-1 hover:bg-white/20 rounded">
                        <i class="fas fa-download"></i>
                    </button>
                </div>
            `;
                }

                div.innerHTML = `
            <div class="flex gap-3 max-w-[70%] ${isAdmin ? 'flex-row-reverse' : 'flex-row'}">
                <div class="w-8 h-8 ${isAdmin ? 'bg-primary' : 'bg-blue-500'} rounded-full flex items-center justify-center flex-shrink-0">
                    <span class="text-white text-xs font-bold">${isAdmin ? 'A' : 'C'}</span>
                </div>
                
                <div class="flex flex-col ${isAdmin ? 'items-end' : 'items-start'}">
                    <div class="rounded-2xl px-4 py-3 shadow-sm ${
                        isAdmin 
                            ? 'message-admin rounded-br-md' 
                            : 'message-customer rounded-bl-md'
                    }">
                        ${messageContent}
                    </div>
                    <span class="text-xs text-muted-foreground mt-1 px-2">${timeString}</span>
                </div>
            </div>
        `;

                return div;
            }

            function sendMessage() {
                const content = messageInput.value.trim();
                if (!content || !currentChatId) return;

                const message = {
                    id: Date.now().toString(),
                    content: content,
                    sender: 'admin',
                    timestamp: new Date(),
                    type: 'text'
                };

                // Add to current chat
                const chat = chats.find(c => c.id === currentChatId);
                if (chat) {
                    chat.messages.push(message);
                    chat.lastMessage = content;
                    chat.lastMessageTime = new Date();
                    chat.status = 'active';

                    // Update UI
                    renderMessages(chat.messages);
                    renderChatList();
                }

                // Clear input
                messageInput.value = '';
                handleInputChange();
                autoResizeTextarea();

                // Send to server
                // axios.post('/api/admin/chat/send', {
                //     chatId: currentChatId,
                //     message: content,
                //     type: 'text'
                // }).catch(error => {
                //     console.error('Failed to send message:', error);
                // });
            }

            function handleInputChange() {
                const hasText = messageInput.value.trim().length > 0;
                sendBtn.disabled = !hasText;
            }

            function handleKeyPress(e) {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    sendMessage();
                }
            }

            function autoResizeTextarea() {
                messageInput.style.height = 'auto';
                messageInput.style.height = Math.min(messageInput.scrollHeight, 120) + 'px';
            }

            function handleFileUpload(e) {
                const file = e.target.files[0];
                if (!file || !currentChatId) return;

                const message = {
                    id: Date.now().toString(),
                    content: `Đã gửi file: ${file.name}`,
                    sender: 'admin',
                    timestamp: new Date(),
                    type: 'file',
                    fileName: file.name,
                    fileSize: (file.size / 1024 / 1024).toFixed(2) + ' MB',
                    fileUrl: URL.createObjectURL(file)
                };

                addMessageToCurrentChat(message);
                e.target.value = '';
            }

            function handleImageUpload(e) {
                const file = e.target.files[0];
                if (!file || !currentChatId) return;

                const reader = new FileReader();
                reader.onload = function(e) {
                    const message = {
                        id: Date.now().toString(),
                        content: '',
                        sender: 'admin',
                        timestamp: new Date(),
                        type: 'image',
                        imageUrl: e.target.result
                    };

                    addMessageToCurrentChat(message);
                };
                reader.readAsDataURL(file);

                e.target.value = '';
            }

            function addMessageToCurrentChat(message) {
                const chat = chats.find(c => c.id === currentChatId);
                if (chat) {
                    chat.messages.push(message);
                    chat.lastMessage = message.content || 'Đã gửi file';
                    chat.lastMessageTime = new Date();

                    renderMessages(chat.messages);
                    renderChatList();
                }
            }

            function closeCurrentChat() {
                if (!currentChatId) return;

                const chat = chats.find(c => c.id === currentChatId);
                if (chat) {
                    chat.status = 'closed';
                    renderChatList();
                }

                // Hide active chat
                activeChat.classList.add('hidden');
                customerInfo.classList.add('hidden');
                noChatSelected.classList.remove('hidden');
                currentChatId = null;
            }

            function showTemplatesModal() {
                templatesModal.classList.remove('hidden');
            }

            function hideTemplatesModal() {
                templatesModal.classList.add('hidden');
            }

            function toggleEmojiPicker() {
                emojiPicker.classList.toggle('hidden');
            }

            function hideEmojiPicker() {
                emojiPicker.classList.add('hidden');
            }

            function addEmoji(emoji) {
                messageInput.value += emoji;
                handleInputChange();
                hideEmojiPicker();
                messageInput.focus();
            }

            function filterChats() {
                renderChatList();
            }

            function updateNotificationBadge() {
                unreadCount = chats.reduce((total, chat) => total + chat.unread, 0);

                if (unreadCount > 0) {
                    notificationBadge.textContent = unreadCount;
                    notificationBadge.classList.remove('hidden');
                } else {
                    notificationBadge.classList.add('hidden');
                }
            }

            function startNotificationCheck() {
                // Check for new messages every 30 seconds
                setInterval(() => {
                    // In real app, this would fetch from server
                    // checkForNewMessages();
                }, 30000);
            }

            function scrollToBottom() {
                setTimeout(() => {
                    messagesArea.scrollTop = messagesArea.scrollHeight;
                }, 100);
            }

            // Utility functions
            function formatTime(date) {
                const now = new Date();
                const diff = now - date;

                if (diff < 60000) { // Less than 1 minute
                    return 'Vừa xong';
                } else if (diff < 3600000) { // Less than 1 hour
                    return Math.floor(diff / 60000) + ' phút trước';
                } else if (diff < 86400000) { // Less than 1 day
                    return date.toLocaleTimeString('vi-VN', {
                        hour: '2-digit',
                        minute: '2-digit'
                    });
                } else {
                    return date.toLocaleDateString('vi-VN');
                }
            }

            function getStatusColor(status) {
                switch (status) {
                    case 'active':
                        return 'bg-green-500';
                    case 'waiting':
                        return 'bg-yellow-500';
                    case 'closed':
                        return 'bg-gray-400';
                    default:
                        return 'bg-gray-400';
                }
            }

            function getStatusBadgeClass(status) {
                switch (status) {
                    case 'active':
                        return 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300';
                    case 'waiting':
                        return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300';
                    case 'closed':
                        return 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-300';
                    default:
                        return 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-300';
                }
            }

            function getStatusText(status) {
                switch (status) {
                    case 'active':
                        return 'Đang hoạt động';
                    case 'waiting':
                        return 'Chờ phản hồi';
                    case 'closed':
                        return 'Đã đóng';
                    default:
                        return 'Không xác định';
                }
            }

            function escapeHtml(text) {
                const div = document.createElement('div');
                div.textContent = text;
                return div.innerHTML;
            }
        });
    </script>
@endsection
