<!-- Floating Chat Button -->
<button id="chatToggleBtn"
    class="fixed bottom-6 right-6 w-16 h-16 bg-orange-500 hover:bg-orange-600 text-white rounded-full shadow-lg hover:shadow-xl transition-all duration-300 flex items-center justify-center z-50 group">
    <i class="fas fa-comments text-2xl group-hover:scale-110 transition-transform"></i>

    <!-- Notification badge -->
    <div id="chatBadge"
        class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white text-xs rounded-full flex items-center justify-center">
        1
    </div>

    <!-- Pulse animation -->
    <div class="absolute inset-0 rounded-full bg-orange-500 animate-ping opacity-20"></div>
</button>

<!-- Chat Popup -->
<div id="chatPopup"
    class="fixed bottom-24 right-6 w-96 max-w-[calc(100vw-2rem)] shadow-2xl rounded-lg overflow-hidden z-50 border border-gray-200 chat-popup">
    <div id="chatContent" class="bg-white flex flex-col h-[600px]">
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
                        <span id="typingIndicator" class="text-orange-200 hidden">• đang nhập...</span>
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
                            class="bg-white text-gray-900 border border-gray-200 rounded-2xl rounded-bl-md px-4 py-2 shadow-sm">
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
                        <button id="imageBtn"
                            class="h-8 w-8 text-gray-500 hover:text-orange-500 hover:bg-orange-50 rounded flex items-center justify-center transition-colors">
                            <i class="fas fa-image text-sm"></i>
                        </button>
                        <button id="fileBtn"
                            class="h-8 w-8 text-gray-500 hover:text-orange-500 hover:bg-orange-50 rounded flex items-center justify-center transition-colors">
                            <i class="fas fa-paperclip text-sm"></i>
                        </button>
                        <button id="emojiBtn"
                            class="h-8 w-8 text-gray-500 hover:text-orange-500 hover:bg-orange-50 rounded flex items-center justify-center transition-colors">
                            <i class="fas fa-smile text-sm"></i>
                        </button>
                        <button id="endChatBtn"
                            class="text-red-600 hover:text-red-700 hover:bg-red-50 border border-red-200 px-3 py-1 rounded text-sm transition-colors">
                            Kết thúc
                        </button>
                    </div>

                    <!-- Emoji picker -->
                    <div id="emojiPicker"
                        class="absolute bottom-full left-0 mb-2 bg-white border border-gray-200 rounded-lg p-3 shadow-lg z-10 emoji-picker hidden">
                        <div class="grid grid-cols-6 gap-2" id="emojiGrid">
                            <!-- Emojis will be populated by JavaScript -->
                        </div>
                    </div>

                    <!-- Message input -->
                    <div class="flex items-end gap-2">
                        <textarea id="messageInput" placeholder="Nhập tin nhắn..."
                            class="flex-1 min-h-[44px] max-h-[120px] resize-none border border-gray-300 focus:border-orange-500 focus:ring-1 focus:ring-orange-500 rounded-lg px-3 py-2 text-sm"
                            rows="1"></textarea>
                        <button id="sendBtn"
                            class="bg-orange-500 hover:bg-orange-600 text-white h-[44px] px-4 rounded-lg flex items-center justify-center flex-shrink-0 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                            disabled>
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
                <textarea id="feedbackText" placeholder="Chia sẻ trải nghiệm của bạn..."
                    class="w-full min-h-[80px] border border-gray-300 rounded-lg px-3 py-2 text-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-500"></textarea>
            </div>

            <div class="flex gap-2 justify-end">
                <button id="skipRatingBtn"
                    class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                    Bỏ qua
                </button>
                <button id="submitRatingBtn"
                    class="px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                    disabled>
                    Gửi đánh giá
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Success notification -->
<div id="successNotification"
    class="fixed bottom-4 left-4 bg-green-500 text-white p-4 rounded-lg shadow-lg z-50 hidden">
    <div class="flex items-center gap-2">
        <span>Cảm ơn bạn đã đánh giá! 🙏</span>
        <button id="closeNotificationBtn"
            class="text-white hover:bg-green-600 h-6 w-6 rounded flex items-center justify-center transition-colors">
            <i class="fas fa-times text-xs"></i>
        </button>
    </div>
</div>

<!-- Hidden file inputs -->
<input type="file" id="fileInput" class="hidden" accept=".pdf,.doc,.docx,.txt,.zip,.rar">
<input type="file" id="imageInput" class="hidden" accept="image/*">

<div id="chat-container" data-conversation-id="{{ $conversation->id ?? '' }}"
    data-user-id="{{ auth()->id() ?? session('customer_id') }}" data-user-type="customer">
    <div id="chat-messages" class="chat-messages"></div>
</div>
<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
<script src="{{ asset('js/echo.js') }}"></script>
<script src="{{ asset('js/chat-common.js') }}" defer></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var chatContainer = document.getElementById('chat-container');
        if (!chatContainer) return;
        var chat = new ChatCommon({
            conversationId: chatContainer.getAttribute('data-conversation-id'),
            userId: chatContainer.getAttribute('data-user-id'),
            userType: chatContainer.getAttribute('data-user-type'),
            api: {
                send: '{{ route('customer.chat.send') }}',
                getMessages: '/api/conversations/' + chatContainer.getAttribute('data-conversation-id'),
                typing: '{{ route('customer.chat.typing', [], false) }}'
            }
        });
    });
</script>
