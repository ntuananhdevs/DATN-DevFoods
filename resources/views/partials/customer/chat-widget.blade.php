<!-- Floating Chat Button -->
<button id="chatToggleBtn" class="fixed bottom-6 right-6 w-16 h-16 bg-orange-500 hover:bg-orange-600 text-white rounded-full shadow-lg hover:shadow-xl transition-all duration-300 flex items-center justify-center z-50 group">
    <i class="fas fa-comments text-2xl group-hover:scale-110 transition-transform"></i>
    
    <!-- Notification badge -->
    <div id="chatBadge" class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white text-xs rounded-full flex items-center justify-center">
        1
    </div>
    
    <!-- Pulse animation -->
    <div class="absolute inset-0 rounded-full bg-orange-500 animate-ping opacity-20"></div>
</button>

<!-- Chat Popup -->
<div id="chatPopup" class="fixed bottom-24 right-6 w-96 max-w-[calc(100vw-2rem)] shadow-2xl rounded-lg overflow-hidden z-50 border border-gray-200 chat-popup">
    <div id="chatContent" class="bg-white flex flex-col h-[600px]">
        <!-- Header -->
        <div class="bg-gradient-to-r from-orange-500 to-red-500 text-white px-4 py-3 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-orange-600 rounded-full flex items-center justify-center border-2 border-white">
                    <span class="text-sm font-bold">FS</span>
                </div>
                <div>
                    <h3 class="font-semibold">FastFood Support</h3>
                    <div class="flex items-center gap-2 text-sm">
                        <div id="adminStatus" class="w-2 h-2 rounded-full bg-green-400"></div>
                        <span id="adminStatusText">Đang hoạt động</span>
                        <span id="typingIndicator" class="text-orange-200 hidden">• đang nhập...</span>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <button class="text-white hover:bg-white/20 h-8 w-8 rounded flex items-center justify-center transition-colors">
                    <i class="fas fa-phone text-sm"></i>
                </button>
                <button class="text-white hover:bg-white/20 h-8 w-8 rounded flex items-center justify-center transition-colors">
                    <i class="fas fa-video text-sm"></i>
                </button>
                <button id="fullscreenBtn" class="text-white hover:bg-white/20 h-8 w-8 rounded flex items-center justify-center transition-colors">
                    <i class="fas fa-expand text-sm"></i>
                </button>
                <button id="closeChatBtn" class="text-white hover:bg-white/20 h-8 w-8 rounded flex items-center justify-center transition-colors">
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
                        <div class="bg-white text-gray-900 border border-gray-200 rounded-2xl rounded-bl-md px-4 py-2 shadow-sm">
                            <p class="text-sm">Xin chào! Tôi có thể giúp gì cho bạn hôm nay? 😊</p>
                        </div>
                        <span class="text-xs text-gray-500 mt-1 px-2" id="initialTime"></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Input Area -->
        <div class="border-t border-gray-200 bg-white p-4">
            <div class="flex items-end gap-3">
                <div class="flex-1 relative">
                    <!-- Attachment buttons -->
                    <div class="flex items-center gap-2 mb-3">
                        <button id="imageBtn" class="h-8 w-8 text-gray-500 hover:text-orange-500 hover:bg-orange-50 rounded flex items-center justify-center transition-colors">
                            <i class="fas fa-image text-sm"></i>
                        </button>
                        <button id="fileBtn" class="h-8 w-8 text-gray-500 hover:text-orange-500 hover:bg-orange-50 rounded flex items-center justify-center transition-colors">
                            <i class="fas fa-paperclip text-sm"></i>
                        </button>
                        <button id="emojiBtn" class="h-8 w-8 text-gray-500 hover:text-orange-500 hover:bg-orange-50 rounded flex items-center justify-center transition-colors">
                            <i class="fas fa-smile text-sm"></i>
                        </button>
                        <button id="endChatBtn" class="text-red-600 hover:text-red-700 hover:bg-red-50 border border-red-200 px-3 py-1 rounded text-sm transition-colors">
                            Kết thúc
                        </button>
                    </div>

                    <!-- Emoji picker -->
                    <div id="emojiPicker" class="absolute bottom-full left-0 mb-2 bg-white border border-gray-200 rounded-lg p-3 shadow-lg z-10 emoji-picker hidden">
                        <div class="grid grid-cols-6 gap-2" id="emojiGrid">
                            <!-- Emojis will be populated by JavaScript -->
                        </div>
                    </div>

                    <!-- Message input -->
                    <div class="flex items-end gap-2">
                        <textarea 
                            id="messageInput" 
                            placeholder="Nhập tin nhắn..." 
                            class="flex-1 min-h-[44px] max-h-[120px] resize-none border border-gray-300 focus:border-orange-500 focus:ring-1 focus:ring-orange-500 rounded-lg px-3 py-2 text-sm"
                            rows="1"
                        ></textarea>
                        <button 
                            id="sendBtn" 
                            class="bg-orange-500 hover:bg-orange-600 text-white h-[44px] px-4 rounded-lg flex items-center justify-center flex-shrink-0 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                            disabled
                        >
                            <i class="fas fa-paper-plane text-sm"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Fullscreen Chat -->
<div id="fullscreenChat" class="fixed inset-0 z-50 bg-white hidden">
    <!-- Content will be moved here when fullscreen -->
</div>

<!-- Rating Modal -->
<div id="ratingModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg p-6 w-full max-w-md mx-4">
        <h3 class="text-lg font-semibold mb-4">Đánh giá cuộc trò chuyện</h3>
        
        <div class="space-y-4">
            <div>
                <p class="text-sm text-gray-600 mb-3">Bạn cảm thấy cuộc trò chuyện như thế nào?</p>
                <div class="flex justify-center gap-2" id="starRating">
                    <!-- Stars will be populated by JavaScript -->
                </div>
            </div>

            <div>
                <label class="text-sm font-medium text-gray-700 mb-2 block">Góp ý thêm (tùy chọn)</label>
                <textarea 
                    id="feedbackText" 
                    placeholder="Chia sẻ trải nghiệm của bạn..." 
                    class="w-full min-h-[80px] border border-gray-300 rounded-lg px-3 py-2 text-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-500"
                ></textarea>
            </div>

            <div class="flex gap-2 justify-end">
                <button id="skipRatingBtn" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                    Bỏ qua
                </button>
                <button id="submitRatingBtn" class="px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                    Gửi đánh giá
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Success notification -->
<div id="successNotification" class="fixed bottom-4 left-4 bg-green-500 text-white p-4 rounded-lg shadow-lg z-50 hidden">
    <div class="flex items-center gap-2">
        <span>Cảm ơn bạn đã đánh giá! 🙏</span>
        <button id="closeNotificationBtn" class="text-white hover:bg-green-600 h-6 w-6 rounded flex items-center justify-center transition-colors">
            <i class="fas fa-times text-xs"></i>
        </button>
    </div>
</div>

<!-- Hidden file inputs -->
<input type="file" id="fileInput" class="hidden" accept=".pdf,.doc,.docx,.txt,.zip,.rar">
<input type="file" id="imageInput" class="hidden" accept="image/*">

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
    const typingIndicator = document.getElementById('typingIndicator');
    const chatBadge = document.getElementById('chatBadge');
    const initialTime = document.getElementById('initialTime');

    // State
    let isChatOpen = false;
    let isFullscreen = false;
    let isAdminOnline = true;
    let isTyping = false;
    let isChatEnded = false;
    let currentRating = 0;
    let messages = [];

    // Emojis
    const emojis = ['😊', '😂', '❤️', '👍', '👎', '😢', '😮', '😡', '🙏', '👏', '🎉', '🔥'];

    // Initialize
    init();

    function init() {
        setupEventListeners();
        populateEmojis();
        setupStarRating();
        setInitialTime();
        
        // Simulate admin going offline after 5 minutes
        setTimeout(() => {
            setAdminOffline();
        }, 300000);
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
        imageInput.addEventListener('change', handleImageUpload);
        fileInput.addEventListener('change', handleFileUpload);

        // Close emoji picker when clicking outside
        document.addEventListener('click', (e) => {
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
        console.log('Toggle chat clicked, isChatOpen:', isChatOpen);
        if (isChatOpen) {
            closeChat();
        } else {
            openChat();
        }
    }

    function openChat() {
        console.log('Opening chat');
        isChatOpen = true;
        chatPopup.classList.add('show');
        console.log('Added show class, chatPopup classes:', chatPopup.className);
        chatBadge.style.display = 'none';
        scrollToBottom();
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
            chatPopup.style.display = 'none';
            chatContent.style.height = '100vh';
            icon.className = 'fas fa-compress text-sm';
        } else {
            // Move back to popup
            chatPopup.appendChild(chatContent);
            fullscreenChat.classList.add('hidden');
            chatPopup.style.display = 'block';
            chatContent.style.height = '600px';
            icon.className = 'fas fa-expand text-sm';
        }
        
        setTimeout(scrollToBottom, 100);
    }

    function handleInputChange() {
        const hasText = messageInput.value.trim().length > 0;
        sendBtn.disabled = !hasText || isChatEnded;
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
        if (!content || isChatEnded) return;

        const message = {
            id: Date.now().toString(),
            content: content,
            sender: 'user',
            timestamp: new Date(),
            type: 'text'
        };

        addMessage(message);
        messageInput.value = '';
        handleInputChange();
        autoResizeTextarea();

        // Auto-reply
        if (!isAdminOnline) {
            setTimeout(() => {
                const autoReply = {
                    id: (Date.now() + 1).toString(),
                    content: 'Cảm ơn bạn! Admin hiện đang bận, chúng tôi sẽ phản hồi trong vài phút. Bạn vui lòng chờ một chút nhé! 🙏',
                    sender: 'admin',
                    timestamp: new Date(),
                    type: 'text'
                };
                addMessage(autoReply);
            }, 1000);
        } else {
            // Simulate admin typing
            showTyping();
            setTimeout(() => {
                hideTyping();
                const responses = [
                    'Cảm ơn bạn đã liên hệ! Tôi sẽ hỗ trợ bạn ngay. 😊',
                    'Để tôi kiểm tra thông tin cho bạn nhé. 🔍',
                    'Bạn có thể cho tôi biết thêm chi tiết không? 🤔',
                    'Tôi hiểu rồi, để tôi hỗ trợ bạn. ✅',
                    'Bạn có cần tôi gọi điện tư vấn trực tiếp không? 📞'
                ];
                const randomResponse = responses[Math.floor(Math.random() * responses.length)];

                const adminReply = {
                    id: (Date.now() + 1).toString(),
                    content: randomResponse,
                    sender: 'admin',
                    timestamp: new Date(),
                    type: 'text'
                };
                addMessage(adminReply);
            }, 2000);
        }
    }

    function addMessage(message) {
        messages.push(message);
        const messageElement = createMessageElement(message);
        messagesContainer.appendChild(messageElement);
        scrollToBottom();
    }

    function createMessageElement(message) {
        const messageDiv = document.createElement('div');
        messageDiv.className = `flex ${message.sender === 'user' ? 'justify-end' : 'justify-start'} message-enter`;

        const isUser = message.sender === 'user';
        const timeString = message.timestamp.toLocaleTimeString('vi-VN', {
            hour: '2-digit',
            minute: '2-digit'
        });

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

    function handleImageUpload(e) {
        const file = e.target.files[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = function(e) {
            const message = {
                id: Date.now().toString(),
                content: '',
                sender: 'user',
                timestamp: new Date(),
                type: 'image',
                imageUrl: e.target.result
            };
            addMessage(message);
        };
        reader.readAsDataURL(file);

        // Reset input
        e.target.value = '';
    }

    function handleFileUpload(e) {
        const file = e.target.files[0];
        if (!file) return;

        const message = {
            id: Date.now().toString(),
            content: `Đã gửi file: ${file.name}`,
            sender: 'user',
            timestamp: new Date(),
            type: 'file',
            fileName: file.name,
            fileSize: (file.size / 1024 / 1024).toFixed(2) + ' MB',
            fileUrl: URL.createObjectURL(file)
        };

        addMessage(message);

        // Reset input
        e.target.value = '';
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

    function showTyping() {
        isTyping = true;
        typingIndicator.classList.remove('hidden');
        
        // Add typing animation
        const typingDiv = document.createElement('div');
        typingDiv.id = 'typingAnimation';
        typingDiv.className = 'flex justify-start';
        typingDiv.innerHTML = `
            <div class="flex gap-2 max-w-[80%]">
                <div class="w-8 h-8 bg-orange-500 rounded-full flex items-center justify-center">
                    <span class="text-white text-xs font-bold">FS</span>
                </div>
                <div class="bg-white rounded-2xl rounded-bl-md px-4 py-3 border border-gray-200 shadow-sm">
                    <div class="flex space-x-1">
                        <div class="w-2 h-2 bg-gray-400 rounded-full typing-dot"></div>
                        <div class="w-2 h-2 bg-gray-400 rounded-full typing-dot"></div>
                        <div class="w-2 h-2 bg-gray-400 rounded-full typing-dot"></div>
                    </div>
                </div>
            </div>
        `;
        messagesContainer.appendChild(typingDiv);
        scrollToBottom();
    }

    function hideTyping() {
        isTyping = false;
        typingIndicator.classList.add('hidden');
        
        const typingAnimation = document.getElementById('typingAnimation');
        if (typingAnimation) {
            typingAnimation.remove();
        }
    }

    function setAdminOffline() {
        isAdminOnline = false;
        adminStatus.className = 'w-2 h-2 rounded-full bg-gray-400';
        adminStatusText.textContent = 'Không hoạt động';
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
});
</script>
