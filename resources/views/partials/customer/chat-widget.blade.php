<!-- Floating Chat Button -->
<button id="chatToggleBtn"
    class="fixed bottom-6 right-6 w-16 h-16 bg-orange-500 hover:bg-orange-600 text-white rounded-full shadow-lg hover:shadow-xl transition-all duration-300 flex items-center justify-center z-50 group">
    <svg class="w-8 h-8 group-hover:scale-110 transition-transform" fill="currentColor" viewBox="0 0 24 24">
        <path d="M12 0C5.373 0 0 4.975 0 11.111c0 3.497 1.745 6.616 4.472 8.652V24l4.086-2.242c1.09.301 2.246.464 3.442.464 6.627 0 12-4.974 12-11.111C24 4.975 18.627 0 12 0zm1.193 14.963l-3.056-3.259-5.963 3.259L10.732 8.1l3.13 3.259L19.825 8.1l-6.632 6.863z"/>
    </svg>

    <!-- Notification badge -->
    <div id="chatBadge"
        class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white text-xs rounded-full flex items-center justify-center">
        1
    </div>

    <!-- Pulse animation -->
    <div class="absolute inset-0 rounded-full bg-orange-500 animate-ping opacity-20"></div>
</button>

<!-- Login Required Modal -->
<div id="loginRequiredModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg p-6 w-full max-w-md mx-4 relative">
        <button id="closeLoginModalBtn"
            class="absolute top-2 right-2 text-gray-400 hover:text-gray-700 text-xl font-bold focus:outline-none">&times;</button>
        <h3 class="text-lg font-semibold mb-4">Yêu cầu đăng nhập</h3>
        <p class="text-gray-600 mb-6">Vui lòng đăng nhập để sử dụng tính năng chat với chúng tôi.</p>
        <div class="flex justify-end gap-2">
            <a href="{{ route('customer.login') }}"
                class="px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white rounded-lg transition-colors">
                Đăng nhập
            </a>
        </div>
    </div>
</div>

<!-- Chat Popup -->
<div id="chatPopup"
    class="fixed bottom-24 right-6 w-96 max-w-[calc(100vw-2rem)] shadow-2xl rounded-lg overflow-hidden z-50 border border-gray-200 chat-popup"
    style="height:540px; max-height:80vh;">
    <div id="chatContent" class="bg-white flex flex-col h-full">
        <!-- Header -->
        <div class="bg-gradient-to-r from-orange-500 to-red-500 text-white px-4 py-3 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div
                    class="w-10 h-10 bg-orange-600 rounded-full flex items-center justify-center border-2 border-white">
                    <span class="text-sm font-bold">FS</span>
                </div>
                <div>
                    <h3 class="font-semibold">FastFood Support</h3>
                    <div class="flex items-center gap-2 text-sm">
                        <div id="adminStatus" class="w-2 h-2 rounded-full bg-green-400"></div>
                        <span id="adminStatusText">Đang hoạt động</span>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <button
                    class="text-white hover:bg-white/20 h-8 w-8 rounded flex items-center justify-center transition-colors">
                    <i class="fas fa-phone text-sm"></i>
                </button>
                <button
                    class="text-white hover:bg-white/20 h-8 w-8 rounded flex items-center justify-center transition-colors">
                    <i class="fas fa-video text-sm"></i>
                </button>
                <button id="fullscreenBtn"
                    class="text-white hover:bg-white/20 h-8 w-8 rounded flex items-center justify-center transition-colors">
                    <i class="fas fa-expand text-sm"></i>
                </button>
                <button id="closeChatBtn"
                    class="text-white hover:bg-white/20 h-8 w-8 rounded flex items-center justify-center transition-colors">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>
        </div>

        <!-- Messages -->
        <div id="messagesContainer" class="flex-1 overflow-y-auto p-4 space-y-4 bg-gray-50 custom-scrollbar">
            <!-- Initial message -->
            <div class="flex justify-start">
                <div class="flex gap-2 max-w-[80%]">
                    <div class="w-8 h-8 bg-orange-500 rounded-full flex items-center justify-center flex-shrink-0">
                        <span class="text-white text-xs font-bold">FS</span>
                    </div>
                    <div class="flex flex-col items-start">
                        <div
                            class="rounded-2xl px-4 py-2 max-w-full shadow-sm bg-white text-gray-900 border border-gray-200 rounded-bl-md">
                            <p class="text-sm whitespace-pre-wrap">Xin chào! Chúng tôi có thể giúp gì cho bạn?</p>
                        </div>
                        <span class="text-xs text-gray-500 mt-1 px-2" id="initialTime"></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Input Area -->
        <div class="border-t border-gray-200 bg-white p-4">
            <div class="flex gap-2 items-end">


                <!-- Message input -->
                <div class="flex-1 relative flex items-end">
                    <textarea id="messageInput" placeholder="Nhập tin nhắn..."
                        class="flex-1 min-h-[44px] max-h-[120px] resize-none border border-gray-300 focus:border-orange-500 focus:ring-1 focus:ring-orange-500 rounded-lg px-3 py-2 text-sm"
                        rows="1"></textarea>
                    <!-- Emoji picker -->
                    <div id="emojiPicker"
                        class="absolute bottom-full left-0 mb-2 bg-white border border-gray-200 rounded-lg p-3 shadow-lg z-10 emoji-picker hidden">
                        <div class="grid grid-cols-6 gap-2" id="emojiGrid">
                            <!-- Emojis will be populated by JavaScript -->
                        </div>
                    </div>
                </div>
                <!-- Attachment buttons -->
                <button id="imageBtn"
                    class="h-10 w-10 text-gray-500 hover:text-orange-500 hover:bg-orange-50 rounded flex items-center justify-center transition-colors">
                    <i class="fas fa-image text-lg"></i>
                </button>
                <button id="fileBtn"
                    class="h-10 w-10 text-gray-500 hover:text-orange-500 hover:bg-orange-50 rounded flex items-center justify-center transition-colors">
                    <i class="fas fa-paperclip text-lg"></i>
                </button>
                <button id="sendBtn"
                    class="bg-orange-500 hover:bg-orange-600 text-white h-12 w-12 rounded-lg flex items-center justify-center flex-shrink-0 transition-colors disabled:opacity-50 disabled:cursor-not-allowed ml-2"
                    disabled>
                    <i class="fas fa-paper-plane text-lg"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Fullscreen Chat -->
<div id="fullscreenChat" class="fixed inset-0 z-50 bg-white hidden">
    <!-- Content will be moved here when fullscreen -->
</div>



<!-- Hidden file inputs -->
<input type="file" id="fileInput" class="hidden" accept=".pdf,.doc,.docx,.txt,.zip,.rar">
<input type="file" id="imageInput" class="hidden" accept="image/*">
<script>
    window.PUSHER_APP_KEY = "{{ env('PUSHER_APP_KEY') }}";
    window.PUSHER_APP_CLUSTER = "{{ env('PUSHER_APP_CLUSTER') }}";
</script>

<!-- Thêm biến Pusher và biến user cho JS -->
<script>
    window.pusherKey = @json(config('broadcasting.connections.pusher.key'));
    window.pusherCluster = @json(config('broadcasting.connections.pusher.options.cluster'));
    window.customerUserId = @json(auth()->id());
    window.isAuthenticated = @json(auth()->check());
</script>
<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
<script src="/js/chat-realtime.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Chat Widget JavaScript
        const chatToggleBtn = document.getElementById('chatToggleBtn');
        const chatPopup = document.getElementById('chatPopup');
        const chatContent = document.getElementById('chatContent');
        const fullscreenChat = document.getElementById('fullscreenChat');
        const fullscreenBtn = document.getElementById('fullscreenBtn');
        const closeChatBtn = document.getElementById('closeChatBtn');
        const messagesContainer = document.getElementById('messagesContainer');
        const messageInput = document.getElementById('messageInput');
        const sendBtn = document.getElementById('sendBtn');
        const imageBtn = document.getElementById('imageBtn');
        const fileBtn = document.getElementById('fileBtn');
        const emojiBtn = document.getElementById('emojiBtn');
        const emojiPicker = document.getElementById('emojiPicker');
        const emojiGrid = document.getElementById('emojiGrid');
        const endChatBtn = document.getElementById('endChatBtn');
        const ratingModal = document.getElementById('ratingModal');
        const starRating = document.getElementById('starRating');
        const feedbackText = document.getElementById('feedbackText');
        const submitRatingBtn = document.getElementById('submitRatingBtn');
        const skipRatingBtn = document.getElementById('skipRatingBtn');
        const successNotification = document.getElementById('successNotification');
        const closeNotificationBtn = document.getElementById('closeNotificationBtn');
        const fileInput = document.getElementById('fileInput');
        const imageInput = document.getElementById('imageInput');
        const adminStatus = document.getElementById('adminStatus');
        const adminStatusText = document.getElementById('adminStatusText');
        const initialTime = document.getElementById('initialTime');
        const loginRequiredModal = document.getElementById('loginRequiredModal');
        const closeLoginModalBtn = document.getElementById('closeLoginModalBtn');
        // Đảm bảo sự kiện đóng modal chỉ gắn 1 lần và đúng selector
        if (loginRequiredModal && closeLoginModalBtn) {
            closeLoginModalBtn.addEventListener('click', function(e) {
                e.preventDefault();
                loginRequiredModal.classList.add('hidden');
            });
            loginRequiredModal.addEventListener('mousedown', function(e) {
                if (e.target === loginRequiredModal) {
                    loginRequiredModal.classList.add('hidden');
                }
            });
        }

        // State
        let isChatOpen = false;
        let isFullscreen = false;
        let isAdminOnline = true;
        let isChatEnded = false;
        let currentRating = 0;
        let messages = [];
        let conversationId = null;
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        let pendingFile = null;
        let pendingImage = null;

        // Emojis
        const emojis = ['😊', '😂', '❤️', '👍', '👎', '😢', '😮', '😡', '🙏', '👏', '🎉', '🔥'];

        // Initialize
        init();

        function init() {
            setupEventListeners();
            populateEmojis();
            setupStarRating();
            setInitialTime();
        }

        function setupEventListeners() {
            chatToggleBtn.addEventListener('click', toggleChat);
            closeChatBtn.addEventListener('click', closeChat);
            fullscreenBtn.addEventListener('click', toggleFullscreen);
            sendBtn.addEventListener('click', sendMessage);
            messageInput.addEventListener('input', handleInputChange);
            messageInput.addEventListener('keypress', handleKeyPress);
            imageBtn.addEventListener('click', () => imageInput.click());
            fileBtn.addEventListener('click', () => fileInput.click());
            emojiBtn.addEventListener('click', toggleEmojiPicker);
            endChatBtn.addEventListener('click', endChat);
            skipRatingBtn.addEventListener('click', closeRatingModal);
            submitRatingBtn.addEventListener('click', submitRating);
            closeNotificationBtn.addEventListener('click', closeSuccessNotification);
            imageInput.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    pendingImage = this.files[0];
                    pendingFile = null;
                    sendMessage();
                }
            });
            fileInput.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    pendingFile = this.files[0];
                    pendingImage = null;
                    sendMessage();
                }
            });
            document.addEventListener('click', (e) => {
                if (!emojiPicker.contains(e.target) && !emojiBtn.contains(e.target)) {
                    hideEmojiPicker();
                }
                // Đóng modal đăng nhập khi click ra ngoài
                if (loginRequiredModal && !loginRequiredModal.classList.contains('hidden')) {
                    const modalContent = loginRequiredModal.querySelector('.bg-white');
                    if (modalContent && !modalContent.contains(e.target)) {
                        loginRequiredModal.classList.add('hidden');
                    }
                }
            });
            // Đóng modal khi bấm nút X
            const closeLoginModalBtn = document.getElementById('closeLoginModalBtn');
            if (closeLoginModalBtn) {
                closeLoginModalBtn.addEventListener('click', function() {
                    loginRequiredModal.classList.add('hidden');
                });
            }
            messageInput.addEventListener('input', autoResizeTextarea);
            messageInput.addEventListener('input', () => {
                sendTypingIndicator(true);
                if (window.typingTimeout) clearTimeout(window.typingTimeout);
                window.typingTimeout = setTimeout(() => sendTypingIndicator(false), 2000);
            });
            messageInput.addEventListener('blur', () => sendTypingIndicator(false));
            loginRequiredModal.addEventListener('click', function(e) {
                if (e.target === loginRequiredModal) {
                    loginRequiredModal.classList.add('hidden');
                }
            });
        }

        function populateEmojis() {
            emojiGrid.innerHTML = '';
            emojis.forEach(emoji => {
                const button = document.createElement('button');
                button.textContent = emoji;
                button.className = 'p-2 hover:bg-gray-100 rounded text-lg transition-colors';
                button.addEventListener('click', () => addEmoji(emoji));
                emojiGrid.appendChild(button);
            });
        }

        function setupStarRating() {
            starRating.innerHTML = '';
            for (let i = 1; i <= 5; i++) {
                const star = document.createElement('button');
                star.innerHTML = '<i class="fas fa-star text-2xl"></i>';
                star.className = 'p-1 transition-colors text-gray-300 hover:text-yellow-300';
                star.addEventListener('click', () => setRating(i));
                starRating.appendChild(star);
            }
        }

        function setInitialTime() {
            const now = new Date();
            const timeString = now.toLocaleTimeString('vi-VN', {
                hour: '2-digit',
                minute: '2-digit'
            });
            initialTime.textContent = timeString;
        }

        function toggleChat() {
            // Check if user is authenticated
            const isAuthenticated = window.isAuthenticated;

            if (!isAuthenticated) {
                loginRequiredModal.classList.remove('hidden');
                return;
            }

            if (isChatOpen) {
                closeChat();
            } else {
                openChat();
            }
        }

        function openChat() {
            isChatOpen = true;
            chatPopup.classList.add('show');
            chatBadge.style.display = 'none';
            // Kiểm tra đã có conversation chưa
            fetch('/customer/chat/conversations')
                .then(res => res.json())
                .then(list => {
                    if (list.conversations && list.conversations.length > 0) {
                        conversationId = list.conversations[0].id;
                        window.conversationId = conversationId;

                        loadMessages();
                        initCustomerChatRealtime(conversationId);
                    } else {
                        createConversation();
                    }
                });
        }

        function closeChat() {
            isChatOpen = false;
            chatPopup.classList.remove('show');
            if (isFullscreen) {
                toggleFullscreen();
            }
        }

        function toggleFullscreen() {
            isFullscreen = !isFullscreen;
            const icon = fullscreenBtn.querySelector('i');

            if (isFullscreen) {
                // Move to fullscreen
                fullscreenChat.appendChild(chatContent);
                fullscreenChat.classList.remove('hidden');
                chatPopup.style.display = 'none'; // Ẩn hoàn toàn popup khi fullscreen
                chatContent.style.height = '100vh';
                icon.className = 'fas fa-compress text-sm';
            } else {
                // Move back to popup
                chatPopup.appendChild(chatContent);
                fullscreenChat.classList.add('hidden');
                chatPopup.style.display = 'block'; // Hiện lại popup khi thoát fullscreen
                chatContent.style.height = '600px';
                icon.className = 'fas fa-expand text-sm';
            }

            setTimeout(scrollToBottom, 100);
        }

        function handleInputChange() {
            const hasText = messageInput.value.trim().length > 0;
            const hasFile = !!pendingFile || !!pendingImage;
            sendBtn.disabled = (!hasText && !hasFile) || isChatEnded;
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

        function sendMessage() {
            const content = messageInput.value.trim();
            if ((!content && !pendingFile && !pendingImage) || isChatEnded) return;
            const formData = new FormData();
            messageInput.value = '';
            formData.append('conversation_id', conversationId);
            formData.append('message', content);
            if (pendingFile) {
                formData.append('attachment', pendingFile);
                formData.append('attachment_type', 'file');
            }
            if (pendingImage) {
                formData.append('attachment', pendingImage);
                formData.append('attachment_type', 'image');
            }
            sendBtn.disabled = true;

            // Hiển thị tin nhắn ngay lập tức nếu là text (không file/image)
            if (content && !pendingFile && !pendingImage) {
                const tempId = 'temp-' + Date.now();
                addMessage({
                    id: tempId,
                    content: content,
                    sender: 'user',
                    timestamp: new Date(),
                    type: 'text',
                    isTemp: true
                });
                handleInputChange();
                autoResizeTextarea();
            }

            fetch('/customer/chat/send', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: formData,
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success && (pendingFile || pendingImage)) {
                        // Hiển thị ngay tin nhắn file/ảnh vừa gửi
                        addMessage({
                            id: data.data.id,
                            content: data.data.message,
                            sender: 'user',
                            timestamp: new Date(data.data.sent_at || data.data.created_at),
                            type: data.data.attachment ? (data.data.attachment_type === 'image' ?
                                'image' : 'file') : 'text',
                            imageUrl: data.data.attachment_type === 'image' && data.data
                                .attachment ? '/storage/' + data.data.attachment : undefined,
                            fileName: data.data.attachment_type !== 'image' && data.data
                                .attachment ? data.data.attachment.split('/').pop() : undefined,
                            fileSize: data.data.attachment_type !== 'image' && data.data
                                .attachment ? '' : undefined,
                            fileUrl: data.data.attachment_type !== 'image' && data.data.attachment ?
                                '/storage/' + data.data.attachment : undefined,
                        });
                    }
                    pendingFile = null;
                    pendingImage = null;
                    fileInput.value = '';
                    imageInput.value = '';
                    handleInputChange();
                    autoResizeTextarea();
                })
                .finally(() => {
                    sendBtn.disabled = false;
                });
        }

        function toggleEmojiPicker() {
            if (emojiPicker.classList.contains('show')) {
                hideEmojiPicker();
            } else {
                showEmojiPicker();
            }
        }

        function showEmojiPicker() {
            emojiPicker.classList.remove('hidden');
            setTimeout(() => emojiPicker.classList.add('show'), 10);
        }

        function hideEmojiPicker() {
            emojiPicker.classList.remove('show');
            setTimeout(() => emojiPicker.classList.add('hidden'), 200);
        }

        function addEmoji(emoji) {
            messageInput.value += emoji;
            handleInputChange();
            hideEmojiPicker();
            messageInput.focus();
        }

        function endChat() {
            isChatEnded = true;
            showRatingModal();
        }

        function showRatingModal() {
            ratingModal.classList.remove('hidden');
        }

        function closeRatingModal() {
            ratingModal.classList.add('hidden');
            showSuccessNotification();
        }

        function setRating(rating) {
            currentRating = rating;
            updateStarDisplay();
            submitRatingBtn.disabled = false;
        }

        function updateStarDisplay() {
            const stars = starRating.querySelectorAll('button');
            stars.forEach((star, index) => {
                if (index < currentRating) {
                    star.className = 'p-1 transition-colors text-yellow-400';
                } else {
                    star.className = 'p-1 transition-colors text-gray-300 hover:text-yellow-300';
                }
            });
        }

        function submitRating() {
            const feedback = feedbackText.value.trim();

            // Here you would send the rating to your backend
            console.log('Rating submitted:', {
                rating: currentRating,
                feedback: feedback
            });

            // You can make an AJAX call here
            // axios.post('/api/chat/rating', {
            //     rating: currentRating,
            //     feedback: feedback
            // });

            closeRatingModal();
        }

        function showSuccessNotification() {
            successNotification.classList.remove('hidden');
            setTimeout(() => {
                closeSuccessNotification();
            }, 5000);
        }

        function closeSuccessNotification() {
            successNotification.classList.add('hidden');
        }

        function scrollToBottom() {
            setTimeout(() => {
                messagesContainer.scrollTop = messagesContainer.scrollHeight;
            }, 100);
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        function createConversation() {
            // Gửi message mặc định đầu tiên để tránh lỗi 422
            const formData = new FormData();
            formData.append('message', 'Xin chào!');
            fetch('/customer/chat/create', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: formData,
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success && data.data && data.data.conversation) {
                        conversationId = data.data.conversation.id;
                        window.conversationId = conversationId;
                        loadMessages();
                        initCustomerChatRealtime(conversationId);
                    } else if (data.message && data.message.includes('một cuộc trò chuyện')) {
                        // Nếu đã có conversation, lấy lại id cũ (cần API getConversations)
                        fetch('/customer/chat/conversations')
                            .then(res => res.json())
                            .then(list => {
                                if (list.conversations && list.conversations.length > 0) {
                                    conversationId = list.conversations[0].id;
                                    window.conversationId = conversationId;
                                    loadMessages();
                                    initCustomerChatRealtime(conversationId);
                                }
                            });
                    }
                });
        }

        function loadMessages() {
            fetch('/customer/chat/messages?conversation_id=' + conversationId)
                .then(res => res.json())
                .then(data => {
                    if (data.success && data.messages) {
                        // Xóa tất cả trừ typing indicator
                        [...messagesContainer.children].forEach(child => {
                            if (child.id !== 'customer-typing-indicator') {
                                child.remove();
                            }
                        });
                        data.messages.forEach(msg => {
                            addMessage({
                                id: msg.id,
                                content: msg.message,
                                sender: msg.sender_id == data.conversation.customer_id ?
                                    'user' : 'admin',
                                timestamp: new Date(msg.sent_at),
                                type: msg.attachment ? (msg.attachment_type === 'image' ?
                                    'image' : 'file') : 'text',
                                imageUrl: msg.attachment_type === 'image' ? '/storage/' +
                                    msg.attachment : undefined,
                                fileName: msg.attachment_type !== 'image' && msg
                                    .attachment ? msg.attachment.split('/').pop() :
                                    undefined,
                                fileSize: msg.attachment_type !== 'image' && msg
                                    .attachment ? '' : undefined,
                                fileUrl: msg.attachment_type !== 'image' && msg.attachment ?
                                    '/storage/' + msg.attachment : undefined,
                            });
                        });
                        scrollToBottom();
                    }
                });
        }

        function addMessage(message) {
            // Nếu là message tạm, gắn data-temp="1"
            if (message.id && String(message.id).startsWith('temp-')) {
                const messageElement = createMessageElement(message);
                messageElement.setAttribute('data-temp', '1');
                messagesContainer.appendChild(messageElement);
                scrollToBottom();
                return;
            }

            // Nếu là message thật (id không phải temp-)
            // Ưu tiên cập nhật node tạm thành node thật nếu có
            const tempMsg = messagesContainer.querySelector('[data-temp="1"]');
            if (tempMsg) {
                tempMsg.setAttribute('data-message-id', message.id);
                tempMsg.removeAttribute('data-temp');
                const textNode = tempMsg.querySelector('.text-sm.whitespace-pre-wrap');
                if (textNode) textNode.textContent = message.content;
                const timeNode = tempMsg.querySelector('.text-xs.text-gray-500.mt-1.px-2');
                if (timeNode && message.timestamp) {
                    const timeString = (message.timestamp instanceof Date ? message.timestamp : new Date(message
                        .timestamp)).toLocaleTimeString('vi-VN', {
                        hour: '2-digit',
                        minute: '2-digit'
                    });
                    timeNode.textContent = timeString;
                }
                // Xóa các node tạm khác nếu còn
                Array.from(messagesContainer.querySelectorAll('[data-temp="1"]')).forEach(node => {
                    if (node !== tempMsg) node.remove();
                });
                return; // ĐÃ cập nhật node tạm thành node thật, không thêm node mới!
            }

            // Nếu đã có node với id này, không thêm nữa
            if (message.id && messagesContainer.querySelector(`[data-message-id="${message.id}"]`)) {
                return;
            }

            // Nếu là tin nhắn của chính mình, cũng không thêm node mới (phòng trường hợp Pusher gửi lại)
            const currentUserId = window.customerUserId || {{ auth()->id() ?? 'null' }};
            if (message.sender_id && String(message.sender_id) === String(currentUserId)) {
                return;
            }

            // Nếu không có node tạm, không có node thật, thì tạo node mới như cũ
            messages.push(message);
            const messageElement = createMessageElement(message);
            messagesContainer.appendChild(messageElement);
            scrollToBottom();
        }

        function createMessageElement(message) {
            const messageDiv = document.createElement('div');
            messageDiv.className =
                `flex ${message.sender === 'user' ? 'justify-end' : 'justify-start'} message-enter`;
            messageDiv.setAttribute('data-message-id', message.id);

            const isUser = message.sender === 'user';
            const timeString = message.timestamp.toLocaleTimeString('vi-VN', {
                hour: '2-digit',
                minute: '2-digit'
            });

            // Xác định tên người gửi
            const senderName = isUser ? 'Bạn' : 'FastFood Support';

            let messageContent = '';

            if (message.type === 'text') {
                messageContent = `<p class="text-sm whitespace-pre-wrap">${escapeHtml(message.content)}</p>`;
            } else if (message.type === 'image') {
                messageContent = `
                <div class="space-y-2">
                    <div class="relative group">
                        <img src="${message.imageUrl}" alt="Uploaded image" 
                             class="max-w-full h-auto rounded-lg max-h-64 object-cover cursor-pointer hover:opacity-90 transition-opacity"
                             onclick="window.open('${message.imageUrl}', '_blank')">
                        <div class="absolute inset-0 bg-black/0 group-hover:bg-black/10 rounded-lg transition-colors flex items-center justify-center">
                            <div class="opacity-0 group-hover:opacity-100 transition-opacity bg-black/50 text-white px-2 py-1 rounded text-xs">
                                Click để xem full
                            </div>
                        </div>
                    </div>
                    ${message.content ? `<p class="text-sm">${escapeHtml(message.content)}</p>` : ''}
                </div>
            `;
            } else if (message.type === 'file') {
                messageContent = `
                <div class="flex items-center gap-3 p-3 bg-white/10 rounded-lg min-w-[200px]">
                    <div class="flex-shrink-0">
                                        <i class="fas fa-paperclip"></i>
                                </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium truncate">${escapeHtml(message.fileName)}</p>
                        <p class="text-xs opacity-75">${message.fileSize}</p>
                    </div>
                    <button class="h-8 w-8 rounded hover:bg-white/20 flex items-center justify-center transition-colors">
                        <i class="fas fa-download text-sm"></i>
                    </button>
                </div>
            `;
            }

            messageDiv.innerHTML = `
            <div class="flex gap-2 max-w-[80%] ${isUser ? 'flex-row-reverse' : 'flex-row'}">
                <div class="w-8 h-8 ${isUser ? 'bg-blue-500' : 'bg-orange-500'} rounded-full flex items-center justify-center flex-shrink-0">
                    <span class="text-white text-xs font-bold">${isUser ? 'U' : 'FS'}</span>
                                            </div>
                <div class="flex flex-col ${isUser ? 'items-end' : 'items-start'}">
                    <div class="text-xs text-gray-500 mb-1">${senderName}</div>
                    <div class="rounded-2xl px-4 py-2 max-w-full shadow-sm ${
                        isUser 
                            ? 'bg-orange-500 text-white rounded-br-md' 
                            : 'bg-white text-gray-900 border border-gray-200 rounded-bl-md'
                    }">
                        ${messageContent}
                                        </div>
                    <span class="text-xs text-gray-500 mt-1 px-2">${timeString}</span>
                                        </div>
                                    </div>
                                `;

            return messageDiv;
        }

        // Hàm khởi tạo CustomerChatRealtime sau khi đã có conversationId
        function initCustomerChatRealtime(conversationId) {
            const customerUserId = window.customerUserId;

            if (!conversationId || !customerUserId) return;
            if (window.customerChatInstance) return; // Không khởi tạo lại nếu đã có

            window.customerChatInstance = new CustomerChatRealtime({
                conversationId: conversationId,
                userId: customerUserId,
                api: {
                    send: '/customer/chat/send',
                    getMessages: '/customer/chat/messages?conversation_id=' + conversationId,
                },
                appendMessage: function(message) {
                    addMessage({
                        id: message.id,
                        content: message.message,
                        sender: message.sender_id == customerUserId ? 'user' : 'admin',
                        timestamp: new Date(message.sent_at || message.created_at),
                        type: message.attachment ? (message.attachment_type === 'image' ?
                            'image' : 'file') : 'text',
                        imageUrl: message.attachment_type === 'image' && message
                            .attachment ? '/storage/' + message.attachment : undefined,
                        fileName: message.attachment_type !== 'image' && message
                            .attachment ? message.attachment.split('/').pop() : undefined,
                        fileSize: message.attachment_type !== 'image' && message
                            .attachment ? '' : undefined,
                        fileUrl: message.attachment_type !== 'image' && message.attachment ?
                            '/storage/' + message.attachment : undefined,
                    });
                },
                // Ghi đè hàm setupPusherChannel để thêm log
                setupPusherChannel: function() {
                    try {

                        const channel = this.pusher.subscribe(`chat.${this.conversationId}`);
                        channel.bind("pusher:subscription_succeeded", () => {

                        });
                        channel.bind("pusher:subscription_error", (err) => {

                        });
                        channel.bind("new-message", (data) => {

                            if (data.message) {
                                this.appendMessage(data.message);
                            }
                        });
                        channel.bind("user.typing", (data) => {

                            if (
                                data.user_id !== this.userId &&
                                String(data.conversation_id) === String(this.conversationId)
                            ) {
                                if (data.is_typing) {
                                    this.showTypingIndicator(data.user_name);
                                } else {
                                    this.hideTypingIndicator();
                                }
                            }
                        });
                    } catch (e) {
                        console.error("[CustomerChatRealtime] Lỗi khi setup channel:", e);
                    }
                },
                showTypingIndicator: function(userName) {

                    let typingDiv = document.getElementById("customer-typing-indicator");
                    const msgContainer = document.getElementById("messagesContainer");
                    const typingHTML = `
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-orange-100 text-orange-500 mr-1">
                                <i class="fas fa-pencil-alt"></i>
                            </span>
                            <span class="text-sm font-medium text-gray-700">${userName} đang nhập</span>
                            <span class="typing-indicator">
                                <span class="dot"></span><span class="dot"></span><span class="dot"></span>
                            </span>
                        </div>
                    `;
                    if (!typingDiv) {
                        typingDiv = document.createElement("div");
                        typingDiv.id = "customer-typing-indicator";
                        typingDiv.className =
                            "px-4 py-2 bg-white rounded-2xl shadow border border-orange-100 mb-1 w-fit animate-fade-in";
                        typingDiv.innerHTML = typingHTML;
                        if (msgContainer) {
                            msgContainer.appendChild(typingDiv);
                            console.log('[DEBUG][DOM] Appended typingDiv to messagesContainer',
                                typingDiv, msgContainer.innerHTML);
                        }
                    } else {
                        typingDiv.innerHTML = typingHTML;
                        if (msgContainer && msgContainer.lastChild !== typingDiv) {
                            msgContainer.appendChild(typingDiv);
                        }
                        console.log('[DEBUG][DOM] Updated typingDiv in messagesContainer',
                            typingDiv, msgContainer.innerHTML);
                    }
                },
                hideTypingIndicator: function() {
                    console.log("[DEBUG][CustomerChatRealtime] hideTypingIndicator");
                    const typingDiv = document.getElementById("customer-typing-indicator");
                    if (typingDiv) typingDiv.remove();
                }
            });
            // Gọi hàm setupPusherChannel có log
            window.customerChatInstance.setupPusherChannel();
        }

        function sendTypingIndicator(isTyping) {
            fetch('/customer/chat/typing', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({
                    conversation_id: conversationId,
                    is_typing: isTyping,
                }),
            });
        }

        function createConversationAndSend(type, file) {
            const formData = new FormData();
            formData.append('message', 'Xin chào!');
            fetch('/customer/chat/create', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: formData,
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success && data.data && data.data.conversation) {
                        conversationId = data.data.conversation.id;
                        window.conversationId = conversationId;
                        if (type === 'image') {
                            pendingImage = file;
                            sendMessage();
                        } else if (type === 'file') {
                            pendingFile = file;
                            sendMessage();
                        }
                    }
                });
        }

        // Hàm gửi file/ảnh giống branch/admin chat
        function sendAttachment(type, file) {
            if (!conversationId) {
                // Nếu chưa có conversation, tạo trước rồi gửi file/ảnh
                createConversationAndSend(type, file);
                return;
            }
            const formData = new FormData();
            formData.append('conversation_id', conversationId);
            formData.append('message', '');
            formData.append('attachment', file);
            formData.append('attachment_type', type);
            fetch('/customer/chat/send', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: formData,
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        addMessage({
                            id: data.data.id,
                            content: data.data.message,
                            sender: 'user',
                            timestamp: new Date(data.data.sent_at || data.data.created_at),
                            type: data.data.attachment_type === 'image' ? 'image' : 'file',
                            imageUrl: data.data.attachment_type === 'image' ? '/storage/' + data
                                .data.attachment : undefined,
                            fileName: data.data.attachment_type !== 'image' ? data.data.attachment
                                .split('/').pop() : undefined,
                            fileUrl: data.data.attachment_type !== 'image' ? '/storage/' + data.data
                                .attachment : undefined,
                        });
                    }
                    fileInput.value = '';
                    imageInput.value = '';
                    pendingFile = null;
                    pendingImage = null;
                    handleInputChange();
                    autoResizeTextarea();
                });
        }

        // Hàm mở chat widget từ notification
        window.openCustomerChatWidget = function(conversationId) {
            // Mở popup chat nếu chưa mở
            const chatToggleBtn = document.getElementById('chatToggleBtn');
            const chatPopup = document.getElementById('chatPopup');
            if (!chatPopup.classList.contains('show')) {
                chatToggleBtn.click();
            }
            // Nếu đã có conversationId, load đúng cuộc trò chuyện
            if (conversationId) {
                window.conversationId = conversationId;
                // Nếu đã có hàm loadMessages thì gọi lại
                if (typeof loadMessages === 'function') {
                    loadMessages();
                }
                // Nếu đã có hàm initCustomerChatRealtime thì gọi lại
                if (typeof initCustomerChatRealtime === 'function') {
                    initCustomerChatRealtime(conversationId);
                }
            }
        };
    });
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

    .animate-fade-in {
        animation: fadeIn 0.3s;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }

        to {
            opacity: 1;
            transform: none;
        }
    }

    @media (max-width: 640px) {
        #chatPopup {
            width: 100vw !important;
            right: 0 !important;
            left: 0 !important;
            bottom: 0 !important;
            border-radius: 0 !important;
            height: 80vh !important;
            max-height: 90vh !important;
        }

        #chatContent {
            height: 100% !important;
        }
    }
</style>
