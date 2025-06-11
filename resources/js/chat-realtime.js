// Chat Realtime với Pusher
import Echo from "laravel-echo";
import Pusher from "pusher-js";

window.Pusher = Pusher;

// Khởi tạo Laravel Echo
window.Echo = new Echo({
    broadcaster: "pusher",
    key: "6ef607214efab0d72419",
    cluster: "ap1",
    forceTLS: true,
    authEndpoint: "/broadcasting/auth",
    auth: {
        headers: {
            "X-CSRF-TOKEN": document
                .querySelector('meta[name="csrf-token"]')
                .getAttribute("content"),
        },
    },
});

// Lắng nghe kênh chat theo conversation ID
const conversationId = document.querySelector(
    'meta[name="conversation-id"]'
)?.content;
if (conversationId) {
    window.Echo.private(`chat.${conversationId}`).listen("MessageSent", (e) => {
        console.log("New message received:", e);
        // Xử lý tin nhắn mới ở đây
    });
}

class ChatRealtime {
    constructor(conversationId, userId, userType = "customer") {
        this.conversationId = conversationId;
        this.userId = userId;
        this.userType = userType;
        this.channel = null;
        this.messageContainer = document.getElementById("chat-messages");
        this.typingIndicator = null;
        this.typingTimeout = null;
        this.isTyping = false;
        this.onlineUsers = new Set();

        this.init();
        this.loadMessages();
    }

    init() {
        this.setupChannels();
        this.setupEventListeners();
        this.setupTypingIndicator();
    }

    setupChannels() {
        // Subscribe to conversation channel
        this.channel = window.Echo.private(`chat.${this.conversationId}`)
            .listen(".message.sent", (e) => {
                console.log("New message received:", e);
                this.handleNewMessage(e);
            })
            .listen(".user.typing", (e) => {
                console.log("User typing:", e);
                this.handleTypingIndicator(e);
            })
            .listen(".conversation.updated", (e) => {
                console.log("Conversation updated:", e);
                this.handleConversationUpdate(e);
            })
            .error((error) => {
                console.error("Channel error:", error);
            });

        // Subscribe to online users presence channel
        window.Echo.join("online-users")
            .here((users) => {
                console.log("Users currently online:", users);
                this.updateOnlineUsers(users);
            })
            .joining((user) => {
                console.log("User joined:", user);
                this.onlineUsers.add(user.id);
                this.updateUserStatus(user.id, true);
            })
            .leaving((user) => {
                console.log("User left:", user);
                this.onlineUsers.delete(user.id);
                this.updateUserStatus(user.id, false);
            });
    }

    setupEventListeners() {
        // Send message form
        const sendForm =
            document.getElementById("send-message-form") ||
            document.getElementById("chat-form");
        if (sendForm) {
            sendForm.addEventListener("submit", (e) => {
                e.preventDefault();
                const conversationId = window.selectedConversationId;
                this.sendMessage();
            });
        }

        // File attachment
        const fileInput = document.getElementById("attachment");
        if (fileInput) {
            fileInput.addEventListener("change", (e) => {
                this.handleFileSelect(e);
            });
        }

        // Enter key to send
        const messageInput =
            document.getElementById("message-input") ||
            document.getElementById("message");
        if (messageInput) {
            messageInput.addEventListener("keypress", (e) => {
                if (e.key === "Enter" && !e.shiftKey) {
                    e.preventDefault();
                    const conversationId = window.selectedConversationId;
                    this.sendMessage();
                } else {
                    this.handleTyping();
                }
            });

            messageInput.addEventListener("input", () => {
                this.handleTyping();
            });

            messageInput.addEventListener("blur", () => {
                this.stopTyping();
            });
        }
    }

    setupTypingIndicator() {
        this.typingIndicator = document.createElement("div");
        this.typingIndicator.className = "typing-indicator";
        this.typingIndicator.style.display = "none";
        this.typingIndicator.innerHTML = `
            <div class="typing-bubble">
                <span></span>
                <span></span>
                <span></span>
            </div>
            <div class="typing-text">đang nhập...</div>
        `;

        if (this.messageContainer) {
            this.messageContainer.appendChild(this.typingIndicator);
        }
    }

    async sendMessage() {
        const messageInput =
            document.getElementById("message-input") ||
            document.getElementById("message");
        const fileInput = document.getElementById("attachment");
        const message = messageInput?.value.trim();

        if (!message && !fileInput?.files.length) {
            return;
        }

        const formData = new FormData();
        formData.append("conversation_id", this.conversationId);
        formData.append("message", message || "");

        if (fileInput?.files.length) {
            formData.append("attachment", fileInput.files[0]);
        }

        // Add CSRF token
        formData.append(
            "_token",
            document
                .querySelector('meta[name="csrf-token"]')
                .getAttribute("content")
        );

        try {
            // Stop typing indicator
            this.stopTyping();

            // Show sending indicator
            this.showSendingIndicator();

            // Determine endpoint based on user type
            let endpoint = "/api/customer/send-message";
            if (this.userType === "admin") {
                endpoint = "/admin/chat/send-message";
            } else if (this.userType === "branch") {
                endpoint = "/branch/chat/send-message";
            }

            const response = await fetch(endpoint, {
                method: "POST",
                body: formData,
                headers: {
                    "X-Requested-With": "XMLHttpRequest",
                },
            });

            const result = await response.json();

            if (result.success) {
                // Clear form
                if (messageInput) messageInput.value = "";
                if (fileInput) fileInput.value = "";
                this.hideSendingIndicator();

                // Message will be displayed via Pusher broadcast
                console.log("Message sent successfully");

                // Show success notification
                this.showNotification("Tin nhắn đã được gửi", "success");

                // Update selected conversation
                window.selectedConversationId = this.conversationId;
            } else {
                throw new Error(result.message || "Failed to send message");
            }
        } catch (error) {
            console.error("Error sending message:", error);
            this.hideSendingIndicator();
            this.showError("Không thể gửi tin nhắn. Vui lòng thử lại.");
        }
    }

    handleNewMessage(messageData) {
        // Don't display own messages (they're already displayed)
        if (messageData.sender_id == this.userId) {
            return;
        }

        this.displayMessage(messageData);
        this.scrollToBottom();
        this.playNotificationSound();
        this.showNotification(
            `Tin nhắn mới từ ${messageData.sender?.name || "Người dùng"}`,
            "info"
        );
    }

    handleTypingIndicator(data) {
        if (data.user_id == this.userId) {
            return; // Don't show typing indicator for own typing
        }

        if (data.is_typing) {
            this.showTypingIndicator(data.user_name);
        } else {
            this.hideTypingIndicator();
        }
    }

    handleConversationUpdate(data) {
        console.log("Conversation updated:", data);

        // Update conversation status in UI
        this.updateConversationStatus(data.status);

        // Show notification about status change
        const statusMessages = {
            distributed: "Cuộc trò chuyện đã được phân phối",
            active: "Cuộc trò chuyện đã được kích hoạt",
            resolved: "Cuộc trò chuyện đã được giải quyết",
            closed: "Cuộc trò chuyện đã được đóng",
        };

        if (statusMessages[data.status]) {
            this.showNotification(statusMessages[data.status], "info");
        }
    }

    handleTyping() {
        if (!this.isTyping) {
            this.isTyping = true;
            this.sendTypingIndicator(true);
        }

        // Clear existing timeout
        if (this.typingTimeout) {
            clearTimeout(this.typingTimeout);
        }

        // Set new timeout to stop typing after 3 seconds
        this.typingTimeout = setTimeout(() => {
            this.stopTyping();
        }, 3000);
    }

    stopTyping() {
        if (this.isTyping) {
            this.isTyping = false;
            this.sendTypingIndicator(false);
        }

        if (this.typingTimeout) {
            clearTimeout(this.typingTimeout);
            this.typingTimeout = null;
        }
    }

    async sendTypingIndicator(isTyping) {
        try {
            let endpoint = "/api/customer/typing";
            if (this.userType === "admin") {
                endpoint = "/admin/chat/typing";
            } else if (this.userType === "branch") {
                endpoint = "/branch/chat/typing";
            }

            await fetch(endpoint, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document
                        .querySelector('meta[name="csrf-token"]')
                        .getAttribute("content"),
                    "X-Requested-With": "XMLHttpRequest",
                },
                body: JSON.stringify({
                    conversation_id: this.conversationId,
                    is_typing: isTyping,
                }),
            });
        } catch (error) {
            console.error("Error sending typing indicator:", error);
        }
    }

    showTypingIndicator(userName) {
        if (this.typingIndicator) {
            this.typingIndicator.querySelector(
                ".typing-text"
            ).textContent = `${userName} đang nhập...`;
            this.typingIndicator.style.display = "flex";
            this.scrollToBottom();
        }
    }

    hideTypingIndicator() {
        if (this.typingIndicator) {
            this.typingIndicator.style.display = "none";
        }
    }

    displayMessage(message) {
        if (!this.messageContainer) return;

        // Xử lý tin nhắn hệ thống
        if (message.type === "system") {
            const systemMessage = document.createElement("div");
            systemMessage.className = "message-system";
            systemMessage.innerHTML = `
                <div class="system-message">
                    ${this.escapeHtml(message.message)}
                </div>
                <span class="message-time">${new Date(
                    message.created_at
                ).toLocaleTimeString([], {
                    hour: "2-digit",
                    minute: "2-digit",
                })}</span>
            `;
            this.messageContainer.appendChild(systemMessage);
            return;
        }

        const currentUserId = document
            .querySelector('meta[name="user-id"]')
            .getAttribute("content");
        const isAdmin = String(message.sender_id) === String(currentUserId);
        const senderName = isAdmin
            ? "Admin"
            : message.sender?.name || "Khách hàng";
        const firstLetter = senderName.charAt(0).toUpperCase();

        // Check if we should create a new message group
        let lastGroup = this.messageContainer.lastElementChild;
        let createNewGroup = true;

        if (lastGroup && lastGroup.classList.contains("message-group")) {
            const lastSenderName = lastGroup.querySelector(
                ".message-sender-name"
            );
            if (lastSenderName && lastSenderName.textContent === senderName) {
                createNewGroup = false;
            }
        }

        if (createNewGroup) {
            // Create new message group
            const messageGroup = document.createElement("div");
            messageGroup.className = "message-group";

            messageGroup.innerHTML = `
                <div class="message-sender">
                    <div class="chat-avatar" style="${
                        isAdmin
                            ? "background-color: #3b82f6; color: white;"
                            : ""
                    }">
                        ${firstLetter}
                    </div>
                    <span class="message-sender-name">${this.escapeHtml(
                        senderName
                    )}</span>
                    ${
                        !isAdmin
                            ? '<span class="message-sender-type">Khách hàng</span>'
                            : ""
                    }
                </div>
            `;

            this.messageContainer.appendChild(messageGroup);
            lastGroup = messageGroup;
        }

        // Add message to group
        const messageContainer = document.createElement("div");
        messageContainer.style.display = "flex";
        messageContainer.style.marginBottom = "8px";

        messageContainer.innerHTML = `
            <div class="message-bubble ${
                isAdmin ? "message-admin" : "message-customer"
            }">
                ${this.escapeHtml(message.message)}
            </div>
            <span class="message-time">${new Date(
                message.created_at
            ).toLocaleTimeString([], {
                hour: "2-digit",
                minute: "2-digit",
            })}</span>
        `;

        lastGroup.appendChild(messageContainer);
    }

    updateOnlineUsers(users) {
        this.onlineUsers.clear();
        users.forEach((user) => {
            this.onlineUsers.add(user.id);
        });

        // Update UI to show online status
        this.updateAllUserStatuses();
    }

    updateUserStatus(userId, isOnline) {
        // Update user status indicators in the UI
        const userElements = document.querySelectorAll(
            `[data-user-id="${userId}"]`
        );
        userElements.forEach((element) => {
            const statusIndicator = element.querySelector(".status-indicator");
            if (statusIndicator) {
                statusIndicator.className = `status-indicator ${
                    isOnline ? "online" : "offline"
                }`;
            }
        });
    }

    updateAllUserStatuses() {
        // Update all user status indicators
        document.querySelectorAll("[data-user-id]").forEach((element) => {
            const userId = Number.parseInt(
                element.getAttribute("data-user-id")
            );
            const isOnline = this.onlineUsers.has(userId);
            this.updateUserStatus(userId, isOnline);
        });
    }

    updateConversationStatus(status) {
        // Update status badges in the UI
        const statusBadges = document.querySelectorAll(".status-badge");
        statusBadges.forEach((badge) => {
            badge.className = `status-badge status-${status}`;
            badge.textContent = this.getStatusText(status);
        });
    }

    getStatusText(status) {
        const statusTexts = {
            new: "Mới",
            distributed: "Đã phân phối",
            active: "Đang xử lý",
            resolved: "Đã giải quyết",
            closed: "Đã đóng",
        };
        return statusTexts[status] || status;
    }

    handleFileSelect(event) {
        const file = event.target.files[0];
        if (file) {
            const fileSize = file.size / 1024 / 1024; // MB
            if (fileSize > 10) {
                this.showError(
                    "File quá lớn. Vui lòng chọn file nhỏ hơn 10MB."
                );
                event.target.value = "";
                return;
            }

            // Show file preview
            this.showFilePreview(file);
        }
    }

    showFilePreview(file) {
        const preview =
            document.getElementById("file-preview") ||
            document.getElementById("attachment-preview");
        if (preview) {
            if (file.type.startsWith("image/")) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    preview.innerHTML = `
                        <div class="file-preview-item">
                            <img src="${e.target.result}" alt="Preview" style="max-width: 100px; max-height: 100px;">
                            <span>${file.name}</span>
                            <button type="button" onclick="this.parentElement.parentElement.innerHTML=''; document.getElementById('attachment').value='';">✕</button>
                        </div>
                    `;
                };
                reader.readAsDataURL(file);
            } else {
                preview.innerHTML = `
                    <div class="file-preview-item">
                        <i class="fas fa-file"></i>
                        <span>${file.name}</span>
                        <button type="button" onclick="this.parentElement.parentElement.innerHTML=''; document.getElementById('attachment').value='';">✕</button>
                    </div>
                `;
            }
            preview.style.display = "block";
        }
    }

    showSendingIndicator() {
        const indicator = document.getElementById("sending-indicator");
        if (indicator) {
            indicator.style.display = "block";
        }

        // Disable send button
        const sendBtn = document.getElementById("send-btn");
        if (sendBtn) {
            sendBtn.disabled = true;
            sendBtn.innerHTML =
                '<i class="fas fa-spinner fa-spin"></i> Đang gửi...';
        }
    }

    hideSendingIndicator() {
        const indicator = document.getElementById("sending-indicator");
        if (indicator) {
            indicator.style.display = "none";
        }

        // Re-enable send button
        const sendBtn = document.getElementById("send-btn");
        if (sendBtn) {
            sendBtn.disabled = false;
            sendBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Gửi';
        }
    }

    showError(message) {
        this.showNotification(message, "error");
    }

    showNotification(message, type = "success") {
        // Remove existing notifications
        document
            .querySelectorAll(".chat-notification")
            .forEach((n) => n.remove());

        const notification = document.createElement("div");
        notification.className = `chat-notification ${type}`;
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 12px 20px;
            border-radius: 8px;
            color: white;
            font-weight: 500;
            z-index: 1000;
            transform: translateX(100%);
            transition: transform 0.3s ease;
            max-width: 300px;
        `;

        // Set background color based on type
        switch (type) {
            case "success":
                notification.style.backgroundColor = "#10b981";
                break;
            case "error":
                notification.style.backgroundColor = "#ef4444";
                break;
            case "warning":
                notification.style.backgroundColor = "#f59e0b";
                break;
            case "info":
                notification.style.backgroundColor = "#3b82f6";
                break;
            default:
                notification.style.backgroundColor = "#6b7280";
        }

        notification.textContent = message;
        document.body.appendChild(notification);

        // Show notification
        setTimeout(() => (notification.style.transform = "translateX(0)"), 100);

        // Hide notification after 3 seconds
        setTimeout(() => {
            notification.style.transform = "translateX(100%)";
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }

    scrollToBottom() {
        if (this.messageContainer) {
            setTimeout(() => {
                this.messageContainer.scrollTop =
                    this.messageContainer.scrollHeight;
            }, 100);
        }
    }

    playNotificationSound() {
        // Create audio element for notification
        try {
            const audio = new Audio("/sounds/notification.mp3");
            audio.volume = 0.3;
            audio
                .play()
                .catch((e) => console.log("Could not play notification sound"));
        } catch (e) {
            console.log("Notification sound not available");
        }
    }

    formatTime(timestamp) {
        const date = new Date(timestamp);
        return date.toLocaleTimeString("vi-VN", {
            hour: "2-digit",
            minute: "2-digit",
        });
    }

    escapeHtml(text) {
        const div = document.createElement("div");
        div.textContent = text;
        return div.innerHTML;
    }

    // Cleanup method
    destroy() {
        if (this.channel) {
            window.Echo.leave(`chat.${this.conversationId}`);
        }

        if (this.typingTimeout) {
            clearTimeout(this.typingTimeout);
        }

        // Leave online users channel
        window.Echo.leave("online-users");
    }

    async loadMessages() {
        try {
            console.log("Bắt đầu tải tin nhắn...");
            console.log("Conversation ID:", this.conversationId);
            console.log("User Type:", this.userType);

            // Determine endpoint based on user type
            let endpoint = `/api/chat/messages/${this.conversationId}`;
            if (this.userType === "admin") {
                endpoint = `/admin/chat/messages/${this.conversationId}`;
            } else if (this.userType === "branch") {
                endpoint = `/branch/chat/messages/${this.conversationId}`;
            }

            console.log("Endpoint:", endpoint);

            const response = await fetch(endpoint, {
                method: "GET",
                headers: {
                    "X-Requested-With": "XMLHttpRequest",
                    "X-CSRF-TOKEN": document
                        .querySelector('meta[name="csrf-token"]')
                        .getAttribute("content"),
                },
            });

            console.log("Response status:", response.status);
            const result = await response.json();
            console.log("Response data:", result);

            if (result.success) {
                console.log("Số tin nhắn nhận được:", result.messages.length);
                // Clear existing messages
                if (this.messageContainer) {
                    this.messageContainer.innerHTML = "";
                }

                // Display messages
                result.messages.forEach((message) => {
                    this.displayMessage(message);
                });

                // Scroll to bottom
                this.scrollToBottom();
            } else {
                console.error("Lỗi từ server:", result.message);
                this.showError(
                    "Không thể tải tin nhắn: " +
                        (result.message || "Lỗi không xác định")
                );
            }
        } catch (error) {
            console.error("Chi tiết lỗi:", error);
            console.error("Stack trace:", error.stack);
            this.showError("Lỗi khi tải tin nhắn: " + error.message);
        }
    }
}

// Export for global use
window.ChatRealtime = ChatRealtime;

// Auto-initialize if conversation data is available
document.addEventListener("DOMContentLoaded", () => {
    const chatContainer = document.getElementById("chat-container");
    if (chatContainer) {
        const conversationId = chatContainer.dataset.conversationId;
        const userId = chatContainer.dataset.userId;
        const userType = chatContainer.dataset.userType || "customer";

        if (conversationId && userId) {
            window.chatInstance = new ChatRealtime(
                conversationId,
                userId,
                userType
            );
        }
    }
});

window.adminChat = new ChatRealtime(conversationId, userId, userType);

function appendMessageToChat(message) {
    const chatMessages = document.querySelector(".chat-messages");
    if (!chatMessages) return;
    const isAdmin = String(message.sender_id) === String(currentUserId);
    let html = `
        <div class="message-group ${
            isAdmin ? "message-group-admin" : "message-group-customer"
        }">
            <div class="message-sender">
                <div class="chat-avatar" style="${
                    isAdmin ? "background-color: #3b82f6; color: white;" : ""
                }">
                    ${
                        message.sender &&
                        (message.sender.full_name || message.sender.name)
                            ? (message.sender.full_name || message.sender.name)
                                  .charAt(0)
                                  .toUpperCase()
                            : "A"
                    }
                </div>
                <span class="message-sender-name">${
                    message.sender &&
                    (message.sender.full_name || message.sender.name)
                        ? message.sender.full_name || message.sender.name
                        : "Khách hàng"
                }</span>
                ${
                    !isAdmin
                        ? '<span class="message-sender-type">Khách hàng</span>'
                        : ""
                }
            </div>
            <div class="message-content">
                <div class="message-bubble ${
                    isAdmin ? "message-admin" : "message-customer"
                }">
                    ${message.message || ""}
                    ${
                        message.attachment
                            ? `<br><a href="/storage/${message.attachment}" target="_blank">📎 File đính kèm</a>`
                            : ""
                    }
                </div>
                <span class="message-time">${formatTime(
                    message.sent_at || message.created_at
                )}</span>
            </div>
        </div>
    `;
    chatMessages.insertAdjacentHTML("beforeend", html);
}

function updateSidebarPreview(message) {
    const convItem = document.querySelector(
        `[data-conversation-id='${message.conversation_id}']`
    );
    if (convItem) {
        // Cập nhật preview
        const preview = convItem.querySelector(
            ".chat-item-preview, .message-preview, .text-truncate"
        );
        if (preview) preview.textContent = message.message;
        // Cập nhật thời gian
        const time = convItem.querySelector(
            ".chat-item-time, .time, .text-muted.small"
        );
        if (time)
            time.textContent = formatTime(
                message.sent_at || message.created_at
            );
        // Badge số chưa đọc
        if (
            String(window.selectedConversationId) !==
            String(message.conversation_id)
        ) {
            let badge = convItem.querySelector(
                ".unread-badge, .badge.bg-danger"
            );
            if (badge) {
                badge.textContent = parseInt(badge.textContent || 0) + 1;
                badge.style.display = "inline-block";
            }
        }
        // Đưa lên đầu danh sách
        if (convItem.parentNode.firstChild !== convItem) {
            convItem.parentNode.insertBefore(
                convItem,
                convItem.parentNode.firstChild
            );
        }
    }
}

function formatTime(timeStr) {
    const d = new Date(timeStr);
    return (
        d.getHours().toString().padStart(2, "0") +
        ":" +
        d.getMinutes().toString().padStart(2, "0")
    );
}

// Lắng nghe tất cả các conversation mà user có thể thấy (giả sử bạn có biến conversationsList là mảng id)
if (window.conversationsList && Array.isArray(window.conversationsList)) {
    window.conversationsList.forEach(function (convId) {
        const channel = pusher.subscribe("chat." + convId);
        channel.bind("new-message", function (data) {
            if (
                String(window.selectedConversationId) ===
                String(data.message.conversation_id)
            ) {
                appendMessageToChat(data.message);
                if (typeof scrollToBottom === "function") scrollToBottom();
            }
            updateSidebarPreview(data.message);
        });
    });
}
// Khi click vào một cuộc trò chuyện, hãy set window.selectedConversationId = conversationId;

// Ví dụ: lấy từ cuộc trò chuyện đầu tiên đang active
const firstActive = document.querySelector(
    ".chat-item.active, .conversation-item.active"
);
if (firstActive) {
    window.selectedConversationId = firstActive.getAttribute(
        "data-conversation-id"
    );
}

const pusher = new Pusher("6ef607214efab0d72419", {
    cluster: "ap1",
    encrypted: true,
});
