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

// Chat Realtime với Pusher
class ChatRealtime {
    constructor(options) {
        if (!options || !options.conversationId || !options.userId) {
            throw new Error(
                "Missing required options: conversationId and userId"
            );
        }

        this.conversationId = options.conversationId;
        this.userId = options.userId;
        this.userType = options.userType || "customer";
        this.api = options.api || {};
        if (!this.api.send || !this.api.getMessages || !this.api.distribute) {
            throw new Error("Thiếu endpoint API khi khởi tạo ChatCommon");
        }
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
        if (!window.Echo) {
            console.error("❌ Echo chưa được khởi tạo");
            return;
        }

        // Subscribe to conversation channel
        this.channel = window.Echo.private(`chat.${this.conversationId}`)
            .listen("MessageSent", (e) => {
                console.log("New message received:", e);
                this.handleNewMessage(e);
            })
            .listen("UserTyping", (e) => {
                console.log("User typing:", e);
                this.handleTypingIndicator(e);
            })
            .listen("ConversationUpdated", (e) => {
                console.log("Conversation updated:", e);
                this.handleConversationUpdate(e);
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
        const sendForm = document.querySelector(".chat-input-container form");
        if (sendForm) {
            sendForm.addEventListener("submit", (e) => {
                e.preventDefault();
                this.sendMessage();
            });
        }

        // File attachment

        // Enter key to send
        const messageInput = document.getElementById("message-input");
        if (messageInput) {
            messageInput.addEventListener("keypress", (e) => {
                if (e.key === "Enter" && !e.shiftKey) {
                    e.preventDefault();
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

        // Distribution select
        const distributeSelects = document.querySelectorAll(
            ".distribution-select"
        );
        distributeSelects.forEach((select) => {
            select.addEventListener("change", (e) => {
                const conversationId = e.target.dataset.conversationId;
                const branchId = e.target.value;
                if (conversationId && branchId) {
                    this.distributeConversation(conversationId, branchId);
                }
            });
        });

        // Lọc trạng thái
        const statusFilter = document.getElementById("chat-status-filter");
        if (statusFilter) {
            statusFilter.addEventListener("change", (e) => {
                const value = e.target.value;
                document.querySelectorAll(".chat-item").forEach((item) => {
                    if (value === "all" || item.dataset.status === value) {
                        item.style.display = "";
                    } else {
                        item.style.display = "none";
                    }
                });
            });
        }

        // Tìm kiếm
        const searchInput = document.getElementById("chat-search");
        if (searchInput) {
            searchInput.addEventListener("input", (e) => {
                const q = e.target.value.toLowerCase();
                document.querySelectorAll(".chat-item").forEach((item) => {
                    const name = item.dataset.customerName?.toLowerCase() || "";
                    const email =
                        item.dataset.customerEmail?.toLowerCase() || "";
                    if (name.includes(q) || email.includes(q)) {
                        item.style.display = "";
                    } else {
                        item.style.display = "none";
                    }
                });
            });
        }

        // Phân công chi nhánh chỉ ở cột info
        const branchSelect = document.getElementById("distribution-select");
        if (branchSelect) {
            branchSelect.addEventListener("change", (e) => {
                const branchId = e.target.value;
                const conversationId = e.target.dataset.conversationId;
                if (branchId && conversationId) {
                    this.distributeConversation(conversationId, branchId);
                }
            });
        }

        // Nút refresh danh sách chat
        const refreshBtn = document.getElementById("refresh-chat-list");
        if (refreshBtn) {
            refreshBtn.addEventListener("click", () => {
                location.reload(); // Nếu có API thì thay bằng AJAX lấy lại danh sách
            });
        }

        // Nút gửi ảnh
        const attachImageBtn = document.getElementById("attachImageBtn");
        const imageInput = document.getElementById("imageInput");
        if (attachImageBtn && imageInput) {
            attachImageBtn.addEventListener("click", () => imageInput.click());
            imageInput.addEventListener("change", (e) => {
                if (e.target.files.length) {
                    this.sendAttachment("image", e.target.files[0]);
                }
            });
        }

        // Nút gửi file
        const attachFileBtn = document.getElementById("attachFileBtn");
        const fileInput = document.getElementById("fileInput");
        if (attachFileBtn && fileInput) {
            attachFileBtn.addEventListener("click", () => fileInput.click());
            fileInput.addEventListener("change", (e) => {
                if (e.target.files.length) {
                    this.sendAttachment("file", e.target.files[0]);
                }
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
        if (!this.messageInput || !this.messageInput.value.trim()) return;
        const message = this.messageInput.value.trim();
        this.messageInput.value = "";
        if (this.sendBtn) this.sendBtn.disabled = true;
        try {
            const formData = new FormData();
            formData.append("message", message);
            formData.append("conversation_id", this.conversationId);
            const url = this.api.send;
            if (!url) {
                this.showError("API gửi tin nhắn chưa được cấu hình");
                return;
            }
            const response = await fetch(url, {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": document.querySelector(
                        'meta[name="csrf-token"]'
                    ).content,
                    "X-Requested-With": "XMLHttpRequest",
                },
                body: formData,
            });
            const data = await response.json();
            if (data.success) {
                // Hiển thị tin nhắn vừa gửi ngay lập tức
                this.appendMessage({
                    ...data.message,
                    sender_id: this.userId,
                    sender: { full_name: "Admin", name: "Admin" },
                    created_at: new Date().toISOString(),
                    message: message,
                });
                // Cập nhật preview sidebar
                if (typeof updateSidebarPreview === "function") {
                    updateSidebarPreview({
                        ...data.message,
                        message: message,
                        created_at: new Date().toISOString(),
                        conversation_id: this.conversationId,
                    });
                }
                this.scrollToBottom();
            } else {
                throw new Error(data.message || "Gửi tin nhắn thất bại");
            }
        } catch (error) {
            this.showError("Không thể gửi tin nhắn");
            this.messageInput.value = message;
        } finally {
            if (this.sendBtn) this.sendBtn.disabled = false;
            if (this.messageInput) {
                this.messageInput.focus();
                this.messageInput.style.height = "auto";
            }
        }
    }

    async distributeConversation(conversationId, branchId) {
        // Hiển thị nút xác nhận trước khi phân công
        if (
            !window.confirm(
                "Bạn có chắc chắn muốn phân công cuộc trò chuyện này cho chi nhánh đã chọn?"
            )
        ) {
            return;
        }
        fetch(this.api.distribute, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document.querySelector(
                    'meta[name="csrf-token"]'
                ).content,
                "X-Requested-With": "XMLHttpRequest",
            },
            body: JSON.stringify({
                conversation_id: conversationId,
                branch_id: branchId,
            }),
        })
            .then((response) => response.json())
            .then((data) => {
                if (data.success) {
                    this.showNotification(
                        "Đã phân phối cuộc trò chuyện thành công",
                        "success"
                    );
                    setTimeout(() => {
                        window.location.reload();
                    }, 800);
                } else {
                    this.showError(
                        data.message || "Không thể phân phối cuộc trò chuyện"
                    );
                }
            })
            .catch((error) => {
                this.showError(
                    "Không thể phân phối cuộc trò chuyện. Vui lòng thử lại."
                );
            });
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
                            <button type="button" onclick="this.parentElement.parentElement.innerHTML=''; document.getElementById('fileInput').value='';">✕</button>
                        </div>
                    `;
                };
                reader.readAsDataURL(file);
            } else {
                preview.innerHTML = `
                    <div class="file-preview-item">
                        <i class="fas fa-file"></i>
                        <span>${file.name}</span>
                        <button type="button" onclick="this.parentElement.parentElement.innerHTML=''; document.getElementById('fileInput').value='';">✕</button>
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

    escapeHtml(unsafe) {
        if (!unsafe) return "";
        return unsafe
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    // Cleanup method
    destroy() {
        if (this.channel) {
            this.channel.stopListening("MessageSent");
            this.channel.stopListening("UserTyping");
            this.channel.stopListening("ConversationUpdated");
        }
        if (this.typingTimeout) {
            clearTimeout(this.typingTimeout);
        }
        // Leave online users channel
        window.Echo.leave("online-users");
    }

    async loadMessages() {
        if (!this.conversationId) return;

        try {
            console.log("📥 Đang tải tin nhắn...");
            const url = this.api.getMessages.replace(
                ":id",
                this.conversationId
            );
            const response = await fetch(url);
            const data = await response.json();

            if (this.messageContainer) {
                this.messageContainer.innerHTML = "";
                if (data.messages && Array.isArray(data.messages)) {
                    // Sắp xếp tin nhắn theo thời gian
                    data.messages.sort(
                        (a, b) =>
                            new Date(a.created_at) - new Date(b.created_at)
                    );
                    data.messages.forEach((message) => {
                        this.appendMessage(message);
                    });
                    this.scrollToBottom();
                }
            }
        } catch (error) {
            console.error("❌ Lỗi khi tải tin nhắn:", error);
            this.showError("Không thể tải tin nhắn");
        }
    }

    appendMessage(message) {
        if (!this.messageContainer) return;
        const isAdmin = String(message.sender_id) === String(this.userId);
        const senderName =
            message.sender && (message.sender.full_name || message.sender.name)
                ? message.sender.full_name || message.sender.name
                : isAdmin
                ? "Admin"
                : "Khách hàng";
        const avatarLetter = senderName.charAt(0).toUpperCase();
        let attachmentHtml = "";
        if (message.attachment) {
            if (message.attachment_type === "image") {
                attachmentHtml = `<img src="/storage/${message.attachment}" class="mt-2 rounded-lg max-h-40 cursor-pointer" onclick="window.open('/storage/${message.attachment}','_blank')">`;
            } else {
                attachmentHtml = `<a href="/storage/${
                    message.attachment
                }" target="_blank" class="text-blue-500 underline">📎 ${message.attachment
                    .split("/")
                    .pop()}</a>`;
            }
        }
        const timeString = this.formatTime(
            message.created_at || message.sent_at
        );
        const msgDiv = document.createElement("div");
        msgDiv.className = `flex items-end gap-2 mb-2 ${
            isAdmin ? "justify-end" : "justify-start"
        }`;
        msgDiv.innerHTML = `
            <div class="flex gap-2 max-w-[80%] ${
                isAdmin ? "flex-row-reverse" : "flex-row"
            }">
                <div class="w-8 h-8 ${
                    isAdmin ? "bg-blue-500" : "bg-orange-500"
                } rounded-full flex items-center justify-center flex-shrink-0">
                    <span class="text-white text-xs font-bold">${avatarLetter}</span>
                </div>
                <div class="flex flex-col ${
                    isAdmin ? "items-end" : "items-start"
                }">
                    <div class="rounded-2xl px-4 py-2 max-w-full shadow-sm ${
                        isAdmin
                            ? "bg-orange-500 text-white rounded-br-md"
                            : "bg-white text-gray-900 border border-gray-200 rounded-bl-md"
                    }">
                        <div>${this.escapeHtml(message.message) || ""}</div>
                        ${attachmentHtml}
                    </div>
                    <span class="text-xs text-gray-500 mt-1 px-2">${timeString}</span>
                </div>
            </div>
        `;
        this.messageContainer.appendChild(msgDiv);
        this.scrollToBottom();
    }

    // Thêm hàm để hiển thị phân công chi nhánh
    showDistributionSection(conversationId) {
        const distributionSection = document.getElementById(
            `distribution-${conversationId}`
        );
        if (distributionSection) {
            distributionSection.classList.add("active");
        }
    }

    async sendAttachment(type, file) {
        if (!file) return;
        const formData = new FormData();
        formData.append("conversation_id", this.conversationId);
        formData.append("message", ""); // Gửi message rỗng
        if (type === "image") {
            formData.append("image", file);
        } else {
            formData.append("file", file);
        }
        formData.append(
            "_token",
            document.querySelector('meta[name="csrf-token"]').content
        );
        try {
            const url = this.api.send;
            const response = await fetch(url, {
                method: "POST",
                body: formData,
                headers: { "X-Requested-With": "XMLHttpRequest" },
            });
            const data = await response.json();
            if (data.success) {
                if (
                    String(this.conversationId) ===
                    String(data.data.conversation_id)
                ) {
                    this.appendMessage(data.data);
                    this.scrollToBottom();
                }
                if (typeof updateSidebarPreview === "function") {
                    updateSidebarPreview({
                        ...data.data,
                        conversation_id: this.conversationId,
                    });
                }
            } else {
                this.showError(data.message || "Không thể gửi file");
            }
        } catch (e) {
            this.showError("Không thể gửi file");
        }
    }

    setupPusherChannels() {
        if (this._pusherChannel) {
            this._pusherChannel.unbind_all();
            this.pusher.unsubscribe(`chat.${this.conversationId}`);
        }
        this._pusherChannel = this.pusher.subscribe(
            `chat.${this.conversationId}`
        );
        this._pusherChannel.bind("new-message", (data) => {
            // Chỉ appendMessage nếu conversationId hiện tại trùng với conversation_id của tin nhắn
            if (
                data.message &&
                String(data.message.sender_id) !== String(this.userId) &&
                String(this.conversationId) ===
                    String(data.message.conversation_id)
            ) {
                const lastMsg = this.messageContainer.lastElementChild;
                let isDuplicate = false;
                if (lastMsg && data.message.id) {
                    isDuplicate =
                        lastMsg.dataset &&
                        lastMsg.dataset.messageId == data.message.id;
                }
                if (!isDuplicate) {
                    this.appendMessage(data.message);
                    this.scrollToBottom();
                }
            }
            // Luôn update preview sidebar cho đúng conversation
            if (typeof updateSidebarPreview === "function") {
                updateSidebarPreview({
                    ...data.message,
                    conversation_id: data.message.conversation_id,
                    branch: data.message.branch || null,
                    status: data.message.status || null,
                });
            }
        });
        this._pusherChannel.bind("conversation-updated", (data) => {
            this.updateConversationStatus(data.status);
        });
    }

    switchConversation(conversationId, chatItem) {
        document.querySelectorAll(".chat-item").forEach((item) => {
            item.classList.remove("active");
        });
        chatItem.classList.add("active");
        this.conversationId = conversationId;
        if (this.chatContainer) {
            this.chatContainer.dataset.conversationId = conversationId;
        }
        // Lấy thông tin customer từ chatItem
        const customerName = chatItem.dataset.customerName;
        const customerEmail = chatItem.dataset.customerEmail;
        const branchName = chatItem.dataset.branchName;
        // Cập nhật avatar, tên, email, branch ở customer info
        const firstLetter = customerName.charAt(0).toUpperCase();
        const avatar = document.getElementById("chat-avatar");
        const name = document.getElementById("chat-customer-name");
        const email = document.getElementById("chat-customer-email");
        const infoAvatar = document.getElementById("customer-info-avatar");
        const infoName = document.getElementById("customer-info-name");
        const infoEmail = document.getElementById("customer-info-email");
        const infoBranch = document.getElementById(
            "customer-info-branch-badge"
        );
        if (avatar) avatar.textContent = firstLetter;
        if (name) name.textContent = customerName;
        if (email) email.textContent = customerEmail;
        if (infoAvatar) infoAvatar.textContent = firstLetter;
        if (infoName) infoName.textContent = customerName;
        if (infoEmail) infoEmail.textContent = customerEmail;
        if (infoBranch) {
            if (branchName) {
                infoBranch.textContent = branchName;
                infoBranch.style.display = "";
            } else {
                infoBranch.style.display = "none";
            }
        }
        // Trạng thái
        const status = chatItem.dataset.status;
        const statusBadge = document.querySelector(".status-badge");
        if (statusBadge) {
            statusBadge.textContent =
                status === "distributed" || status === "active"
                    ? "Đã phân phối"
                    : status === "new"
                    ? "Chờ phản hồi"
                    : status === "closed"
                    ? "Đã đóng"
                    : status;
            statusBadge.className = `badge status-badge status-${status}`;
        }
        // Cập nhật branch badge ở chat-main header
        const mainBranchBadge = document.getElementById("main-branch-badge");
        if (mainBranchBadge) {
            if (branchName) {
                mainBranchBadge.textContent = branchName;
                mainBranchBadge.style.display = "";
            } else {
                mainBranchBadge.style.display = "none";
            }
        }
        this.loadMessages();
        this.setupPusherChannels();
    }

    async confirmDistribution(conversationId, branchId) {
        try {
            const response = await fetch(this.api.distribute, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector(
                        'meta[name="csrf-token"]'
                    ).content,
                    "X-Requested-With": "XMLHttpRequest",
                },
                body: JSON.stringify({
                    conversation_id: conversationId,
                    branch_id: branchId,
                }),
            });

            const data = await response.json();
            if (data.success) {
                this.showNotification(
                    "Đã phân phối cuộc trò chuyện thành công"
                );
                // Cập nhật UI branch badge, status, branch_id
                const chatItem = document.querySelector(
                    `.chat-item[data-conversation-id="${conversationId}"]`
                );
                if (chatItem) {
                    chatItem.classList.add("distributed");
                    chatItem.dataset.status = "distributed";
                    chatItem.dataset.branchName = data.branch.name;
                    chatItem.dataset.branchId = data.branch.id;
                    // Cập nhật badges
                    const badges = chatItem.querySelector(".chat-item-badges");
                    if (badges) {
                        badges.innerHTML = `
                            <span class="badge badge-distributed">Đã phân phối</span>
                            <span class="badge badge-xs branch-badge ml-2">${data.branch.name}</span>
                        `;
                    }
                }
                // Cập nhật branch badge ở chat-main header
                const mainBranchBadge =
                    document.getElementById("main-branch-badge");
                if (mainBranchBadge) {
                    mainBranchBadge.textContent = data.branch.name;
                    mainBranchBadge.style.display = "";
                }
                // Cập nhật branch badge ở customer info
                const infoBranchBadge = document.getElementById(
                    "customer-info-branch-badge"
                );
                if (infoBranchBadge) {
                    infoBranchBadge.textContent = data.branch.name;
                    infoBranchBadge.style.display = "";
                }
                // Ẩn select phân phối
                const select = document.getElementById("distribution-select");
                if (select) {
                    select.style.display = "none";
                }
            } else {
                throw new Error(data.message || "Phân công thất bại");
            }
        } catch (error) {
            console.error("❌ Lỗi khi phân công:", error);
            this.showError("Không thể phân công cuộc trò chuyện");
        }
    }
}

// Export cho global use
window.ChatRealtime = ChatRealtime;

// Auto-initialize if conversation data is available
document.addEventListener("DOMContentLoaded", function () {
    const conversationId = document.querySelector(
        'meta[name="conversation-id"]'
    )?.content;
    const userId = document.querySelector('meta[name="user-id"]')?.content;
    const userType = document.querySelector('meta[name="user-type"]')?.content;

    if (conversationId && userId) {
        window.chatInstance = new ChatRealtime({
            conversationId,
            userId,
            userType,
            api: {
                send: document.querySelector('meta[name="api-send"]')?.content,
                getMessages: document.querySelector('meta[name="api-messages"]')
                    ?.content,
                distribute: document.querySelector(
                    'meta[name="api-distribute"]'
                )?.content,
            },
        });
    }
});

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
        // Cập nhật preview tin nhắn
        const preview = convItem.querySelector(".chat-item-preview");
        if (preview) {
            if (message.attachment_type === "image") {
                preview.textContent = "📷 Ảnh";
            } else if (message.attachment_type === "file") {
                preview.textContent = "📎 File đính kèm";
            } else {
                preview.textContent = message.message || "";
            }
        }

        // Cập nhật thời gian
        const time = convItem.querySelector(".chat-item-time");
        if (time) {
            time.textContent = formatTime(
                message.sent_at || message.created_at
            );
        }

        // Cập nhật badge số tin nhắn chưa đọc
        if (
            String(window.selectedConversationId) !==
            String(message.conversation_id)
        ) {
            let badge = convItem.querySelector(".unread-badge");
            if (!badge) {
                badge = document.createElement("span");
                badge.className = "unread-badge ml-2 absolute right-2 bottom-2";
                convItem.appendChild(badge);
            }
            badge.textContent = parseInt(badge.textContent || 0) + 1;
            badge.style.display = "flex";
        }

        // Đưa lên đầu danh sách
        if (convItem.parentNode.firstChild !== convItem) {
            convItem.parentNode.insertBefore(
                convItem,
                convItem.parentNode.firstChild
            );
        }

        // Cập nhật badge branch nếu có
        if (message.branch) {
            const badges = convItem.querySelector(".chat-item-badges");
            if (badges) {
                const branchBadge = badges.querySelector(".branch-badge");
                if (branchBadge) {
                    branchBadge.textContent = message.branch.name;
                } else {
                    badges.innerHTML += `<span class="badge badge-xs branch-badge ml-2">${message.branch.name}</span>`;
                }
            }
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

// Khởi tạo biến global cho chat admin
let adminChatInstance = null;

// Class ChatCommon cho admin chat
class ChatCommon {
    constructor(options) {
        if (!options || !options.conversationId || !options.userId) {
            console.error(
                "Thiếu thông tin cần thiết: conversationId và userId"
            );
            return;
        }

        this.conversationId = options.conversationId;
        this.userId = options.userId;
        this.userType = options.userType || "admin";
        this.api = options.api || {};

        // Khởi tạo các DOM elements
        this.messageContainer = document.getElementById("chat-messages");
        this.messageInput = document.getElementById("message-input");
        this.sendBtn = document.getElementById("sendBtn");
        this.attachFileBtn = document.getElementById("attachFileBtn");
        this.fileInput = document.getElementById("fileInput");
        this.chatContainer = document.getElementById("chat-container");

        // Khởi tạo Pusher
        this.pusher = new Pusher("6ef607214efab0d72419", {
            cluster: "ap1",
            encrypted: true,
        });

        this.init();
    }

    init() {
        console.log("🚀 Khởi tạo Chat Admin...");
        this.setupEventListeners();
        this.setupPusherChannels();
        if (this.conversationId) {
            this.loadMessages();
        }
    }

    setupEventListeners() {
        console.log("🔧 Thiết lập event listeners...");

        // Xử lý input tin nhắn
        if (this.messageInput) {
            this.messageInput.addEventListener("input", () => {
                if (this.sendBtn) {
                    this.sendBtn.disabled = !this.messageInput.value.trim();
                }
            });

            this.messageInput.addEventListener("keypress", (e) => {
                if (e.key === "Enter" && !e.shiftKey) {
                    e.preventDefault();
                    this.sendMessage();
                }
            });
        }

        // Xử lý nút gửi tin nhắn
        if (this.sendBtn) {
            this.sendBtn.addEventListener("click", () => {
                this.sendMessage();
            });
        }

        // Xử lý đính kèm file
        if (this.attachFileBtn && this.fileInput) {
            this.attachFileBtn.addEventListener("click", () => {
                this.fileInput.click();
            });

            this.fileInput.addEventListener("change", (e) => {
                this.handleFileSelect(e);
            });
        }

        // Xử lý click vào cuộc trò chuyện
        document.querySelectorAll(".chat-item").forEach((item) => {
            item.addEventListener("click", () => {
                const conversationId = item.dataset.conversationId;
                if (conversationId) {
                    this.switchConversation(conversationId, item);
                }
            });
        });

        // Xử lý phân công chat
        document.querySelectorAll(".distribution-select").forEach((select) => {
            select.addEventListener("click", (e) => {
                e.stopPropagation();
                const conversationId = select.dataset.conversationId;
                this.showDistributionSection(conversationId);
            });

            select.addEventListener("change", (e) => {
                const conversationId = e.target.dataset.conversationId;
                const branchId = e.target.value;
                if (conversationId && branchId) {
                    this.distributeConversation(conversationId, branchId);
                }
            });
        });

        // Lọc trạng thái
        const statusFilter = document.getElementById("chat-status-filter");
        if (statusFilter) {
            statusFilter.addEventListener("change", (e) => {
                const value = e.target.value;
                document.querySelectorAll(".chat-item").forEach((item) => {
                    if (value === "all" || item.dataset.status === value) {
                        item.style.display = "";
                    } else {
                        item.style.display = "none";
                    }
                });
            });
        }

        // Tìm kiếm
        const searchInput = document.getElementById("chat-search");
        if (searchInput) {
            searchInput.addEventListener("input", (e) => {
                const q = e.target.value.toLowerCase();
                document.querySelectorAll(".chat-item").forEach((item) => {
                    const name = item.dataset.customerName?.toLowerCase() || "";
                    const email =
                        item.dataset.customerEmail?.toLowerCase() || "";
                    if (name.includes(q) || email.includes(q)) {
                        item.style.display = "";
                    } else {
                        item.style.display = "none";
                    }
                });
            });
        }

        // Nút refresh danh sách chat
        const refreshBtn = document.getElementById("refresh-chat-list");
        if (refreshBtn) {
            refreshBtn.addEventListener("click", () => {
                location.reload(); // Nếu có API thì thay bằng AJAX lấy lại danh sách
            });
        }

        // Nút gửi ảnh
        const attachImageBtn = document.getElementById("attachImageBtn");
        const imageInput = document.getElementById("imageInput");
        if (attachImageBtn && imageInput) {
            attachImageBtn.addEventListener("click", () => imageInput.click());
            imageInput.addEventListener("change", (e) => {
                if (e.target.files.length) {
                    this.sendAttachment("image", e.target.files[0]);
                }
            });
        }

        // Nút gửi file
        const attachFileBtn = document.getElementById("attachFileBtn");
        const fileInput = document.getElementById("fileInput");
        if (attachFileBtn && fileInput) {
            attachFileBtn.addEventListener("click", () => fileInput.click());
            fileInput.addEventListener("change", (e) => {
                if (e.target.files.length) {
                    this.sendAttachment("file", e.target.files[0]);
                }
            });
        }
    }

    setupPusherChannels() {
        console.log("📡 Thiết lập kênh Pusher...");

        // Lắng nghe kênh chat
        const channel = this.pusher.subscribe(`chat.${this.conversationId}`);

        channel.bind("new-message", (data) => {
            console.log("📨 Tin nhắn mới:", data);
            if (data.message) {
                // Chỉ appendMessage nếu message chưa có trong DOM (dựa vào id hoặc created_at)
                if (data.message.sender_id !== this.userId) {
                    this.appendMessage(data.message);
                    this.scrollToBottom();
                }
            }
        });

        channel.bind("conversation-updated", (data) => {
            console.log("🔄 Cập nhật cuộc trò chuyện:", data);
            this.updateConversationStatus(data.status);
        });
    }

    async loadMessages() {
        if (!this.conversationId) return;

        try {
            console.log("📥 Đang tải tin nhắn...");
            const url = this.api.getMessages.replace(
                ":id",
                this.conversationId
            );
            const response = await fetch(url);
            const data = await response.json();

            if (this.messageContainer) {
                this.messageContainer.innerHTML = "";
                if (data.messages && Array.isArray(data.messages)) {
                    // Sắp xếp tin nhắn theo thời gian
                    data.messages.sort(
                        (a, b) =>
                            new Date(a.created_at) - new Date(b.created_at)
                    );
                    data.messages.forEach((message) => {
                        this.appendMessage(message);
                    });
                    this.scrollToBottom();
                }
            }
        } catch (error) {
            console.error("❌ Lỗi khi tải tin nhắn:", error);
            this.showError("Không thể tải tin nhắn");
        }
    }

    async sendMessage() {
        if (!this.messageInput || !this.messageInput.value.trim()) return;
        const message = this.messageInput.value.trim();
        this.messageInput.value = "";
        if (this.sendBtn) this.sendBtn.disabled = true;
        try {
            const formData = new FormData();
            formData.append("message", message);
            formData.append("conversation_id", this.conversationId);
            const url = this.api.send;
            if (!url) {
                this.showError("API gửi tin nhắn chưa được cấu hình");
                return;
            }
            const response = await fetch(url, {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": document.querySelector(
                        'meta[name="csrf-token"]'
                    ).content,
                    "X-Requested-With": "XMLHttpRequest",
                },
                body: formData,
            });
            const data = await response.json();
            if (data.success) {
                // Hiển thị tin nhắn vừa gửi ngay lập tức
                this.appendMessage({
                    ...data.message,
                    sender_id: this.userId,
                    sender: { full_name: "Admin", name: "Admin" },
                    created_at: new Date().toISOString(),
                    message: message,
                });
                // Cập nhật preview sidebar
                if (typeof updateSidebarPreview === "function") {
                    updateSidebarPreview({
                        ...data.message,
                        message: message,
                        created_at: new Date().toISOString(),
                        conversation_id: this.conversationId,
                    });
                }
                this.scrollToBottom();
            } else {
                throw new Error(data.message || "Gửi tin nhắn thất bại");
            }
        } catch (error) {
            this.showError("Không thể gửi tin nhắn");
            this.messageInput.value = message;
        } finally {
            if (this.sendBtn) this.sendBtn.disabled = false;
            if (this.messageInput) {
                this.messageInput.focus();
                this.messageInput.style.height = "auto";
            }
        }
    }

    showDistributionSection(conversationId) {
        const distributionSection = document.getElementById(
            `distribution-${conversationId}`
        );
        if (distributionSection) {
            distributionSection.classList.add("active");
        }
    }

    showDistributionConfirm(conversationId, branchId) {
        const confirmSection = document.createElement("div");
        confirmSection.id = `distribution-confirm-${conversationId}`;
        confirmSection.className = "distribution-confirm-section";
        confirmSection.innerHTML = `
            <div class="distribution-confirm-content">
                <h4>Xác nhận phân công</h4>
                <p>Bạn có chắc chắn muốn phân công cuộc trò chuyện này cho chi nhánh đã chọn?</p>
                <div class="distribution-confirm-actions">
                    <button class="distribution-btn confirm" onclick="window.adminChat.confirmDistribution(${conversationId}, ${branchId})">
                        Xác nhận
                    </button>
                    <button class="distribution-btn cancel" onclick="window.adminChat.cancelDistribution(${conversationId})">
                        Hủy
                    </button>
                </div>
            </div>
        `;
        document.body.appendChild(confirmSection);
    }

    async confirmDistribution(conversationId, branchId) {
        try {
            const response = await fetch(this.api.distribute, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector(
                        'meta[name="csrf-token"]'
                    ).content,
                    "X-Requested-With": "XMLHttpRequest",
                },
                body: JSON.stringify({
                    conversation_id: conversationId,
                    branch_id: branchId,
                }),
            });

            const data = await response.json();
            if (data.success) {
                this.showNotification(
                    "Đã phân phối cuộc trò chuyện thành công"
                );
                // Cập nhật UI branch badge, status, branch_id
                const chatItem = document.querySelector(
                    `.chat-item[data-conversation-id="${conversationId}"]`
                );
                if (chatItem) {
                    chatItem.classList.add("distributed");
                    chatItem.dataset.status = "distributed";
                    chatItem.dataset.branchName = data.branch.name;
                    chatItem.dataset.branchId = data.branch.id;
                    // Cập nhật badges
                    const badges = chatItem.querySelector(".chat-item-badges");
                    if (badges) {
                        badges.innerHTML = `
                            <span class="badge badge-distributed">Đã phân phối</span>
                            <span class="badge badge-xs branch-badge ml-2">${data.branch.name}</span>
                        `;
                    }
                }
                // Cập nhật branch badge ở chat-main header
                const mainBranchBadge =
                    document.getElementById("main-branch-badge");
                if (mainBranchBadge) {
                    mainBranchBadge.textContent = data.branch.name;
                    mainBranchBadge.style.display = "";
                }
                // Cập nhật branch badge ở customer info
                const infoBranchBadge = document.getElementById(
                    "customer-info-branch-badge"
                );
                if (infoBranchBadge) {
                    infoBranchBadge.textContent = data.branch.name;
                    infoBranchBadge.style.display = "";
                }
                // Ẩn select phân phối
                const select = document.getElementById("distribution-select");
                if (select) {
                    select.style.display = "none";
                }
            } else {
                throw new Error(data.message || "Phân công thất bại");
            }
        } catch (error) {
            console.error("❌ Lỗi khi phân công:", error);
            this.showError("Không thể phân công cuộc trò chuyện");
        }
    }

    cancelDistribution(conversationId) {
        this.hideDistributionConfirm(conversationId);
        const select = document.querySelector(
            `.distribution-select[data-conversation-id="${conversationId}"]`
        );
        if (select) {
            select.value = "";
        }
    }

    hideDistributionConfirm(conversationId) {
        const confirmSection = document.getElementById(
            `distribution-confirm-${conversationId}`
        );
        if (confirmSection) {
            confirmSection.remove();
        }
    }

    updateConversationUI(conversationId, status) {
        const chatItem = document.querySelector(
            `.chat-item[data-conversation-id="${conversationId}"]`
        );
        if (chatItem) {
            chatItem.dataset.status = status;
            const badges = chatItem.querySelector(".chat-item-badges");
            if (badges) {
                let badgeHtml = "";
                switch (status) {
                    case "new":
                        badgeHtml =
                            '<span class="badge badge-waiting">Chờ phản hồi</span>';
                        break;
                    case "distributed":
                        badgeHtml =
                            '<span class="badge badge-distributed">Đã phân phối</span>';
                        break;
                    case "closed":
                        badgeHtml =
                            '<span class="badge badge-waiting">Đã đóng</span>';
                        break;
                    default:
                        badgeHtml =
                            '<span class="badge badge-waiting">Đang xử lý</span>';
                }
                badges.innerHTML = badgeHtml;
            }
        }
    }

    switchConversation(conversationId, chatItem) {
        console.log("🔄 Chuyển cuộc trò chuyện:", conversationId);

        // Cập nhật trạng thái active
        document.querySelectorAll(".chat-item").forEach((item) => {
            item.classList.remove("active");
        });
        chatItem.classList.add("active");

        // Cập nhật conversation ID
        this.conversationId = conversationId;
        if (this.chatContainer) {
            this.chatContainer.dataset.conversationId = conversationId;
        }

        // Cập nhật thông tin header
        const customerName = chatItem.dataset.customerName;
        const customerEmail = chatItem.dataset.customerEmail;
        const branchName = chatItem.dataset.branchName;
        const firstLetter = customerName.charAt(0).toUpperCase();

        const avatar = document.getElementById("chat-avatar");
        const name = document.getElementById("chat-customer-name");
        const email = document.getElementById("chat-customer-email");
        const infoAvatar = document.getElementById("customer-info-avatar");
        const infoName = document.getElementById("customer-info-name");
        const infoEmail = document.getElementById("customer-info-email");
        const infoBranch = document.getElementById(
            "customer-info-branch-badge"
        );
        if (avatar) avatar.textContent = firstLetter;
        if (name) name.textContent = customerName;
        if (email) email.textContent = customerEmail;
        if (infoAvatar) infoAvatar.textContent = firstLetter;
        if (infoName) infoName.textContent = customerName;
        if (infoEmail) infoEmail.textContent = customerEmail;
        if (infoBranch) {
            if (branchName) {
                infoBranch.textContent = branchName;
                infoBranch.style.display = "";
            } else {
                infoBranch.style.display = "none";
            }
        }
        // Trạng thái
        const status = chatItem.dataset.status;
        const statusBadge = document.querySelector(".status-badge");
        if (statusBadge) {
            statusBadge.textContent =
                status === "distributed" || status === "active"
                    ? "Đã phân phối"
                    : status === "new"
                    ? "Chờ phản hồi"
                    : status === "closed"
                    ? "Đã đóng"
                    : status;
            statusBadge.className = `badge status-badge status-${status}`;
        }
        // Cập nhật branch badge ở chat-main header
        const mainBranchBadge = document.getElementById("main-branch-badge");
        if (mainBranchBadge) {
            if (branchName) {
                mainBranchBadge.textContent = branchName;
                mainBranchBadge.style.display = "";
            } else {
                mainBranchBadge.style.display = "none";
            }
        }
        this.loadMessages();
        this.setupPusherChannels();
    }

    appendMessage(message) {
        if (!this.messageContainer) return;
        const isAdmin = String(message.sender_id) === String(this.userId);
        const senderName =
            message.sender && (message.sender.full_name || message.sender.name)
                ? message.sender.full_name || message.sender.name
                : isAdmin
                ? "Admin"
                : "Khách hàng";
        const avatarLetter = senderName.charAt(0).toUpperCase();
        let attachmentHtml = "";
        if (message.attachment) {
            if (message.attachment_type === "image") {
                attachmentHtml = `<img src="/storage/${message.attachment}" class="mt-2 rounded-lg max-h-40 cursor-pointer" onclick="window.open('/storage/${message.attachment}','_blank')">`;
            } else {
                attachmentHtml = `<a href="/storage/${
                    message.attachment
                }" target="_blank" class="text-blue-500 underline">📎 ${message.attachment
                    .split("/")
                    .pop()}</a>`;
            }
        }
        const timeString = this.formatTime(
            message.created_at || message.sent_at
        );
        const msgDiv = document.createElement("div");
        msgDiv.className = `flex items-end gap-2 mb-2 ${
            isAdmin ? "justify-end" : "justify-start"
        }`;
        msgDiv.innerHTML = `
            <div class="flex gap-2 max-w-[80%] ${
                isAdmin ? "flex-row-reverse" : "flex-row"
            }">
                <div class="w-8 h-8 ${
                    isAdmin ? "bg-blue-500" : "bg-orange-500"
                } rounded-full flex items-center justify-center flex-shrink-0">
                    <span class="text-white text-xs font-bold">${avatarLetter}</span>
                </div>
                <div class="flex flex-col ${
                    isAdmin ? "items-end" : "items-start"
                }">
                    <div class="rounded-2xl px-4 py-2 max-w-full shadow-sm ${
                        isAdmin
                            ? "bg-orange-500 text-white rounded-br-md"
                            : "bg-white text-gray-900 border border-gray-200 rounded-bl-md"
                    }">
                        <div>${this.escapeHtml(message.message) || ""}</div>
                        ${attachmentHtml}
                    </div>
                    <span class="text-xs text-gray-500 mt-1 px-2">${timeString}</span>
                </div>
            </div>
        `;
        this.messageContainer.appendChild(msgDiv);
        this.scrollToBottom();
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
                        <button type="button" onclick="this.parentElement.parentElement.innerHTML=''; document.getElementById('fileInput').value='';">✕</button>
                    </div>
                `;
                };
                reader.readAsDataURL(file);
            } else {
                preview.innerHTML = `
                <div class="file-preview-item">
                    <i class="fas fa-file"></i>
                    <span>${file.name}</span>
                    <button type="button" onclick="this.parentElement.parentElement.innerHTML=''; document.getElementById('fileInput').value='';">✕</button>
                </div>
            `;
            }
            preview.style.display = "block";
        }
    }

    scrollToBottom() {
        if (this.messageContainer) {
            setTimeout(() => {
                this.messageContainer.scrollTop =
                    this.messageContainer.scrollHeight;
            }, 100);
        }
    }

    showError(message) {
        this.showNotification(message, "error");
    }

    showNotification(message, type = "success") {
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

        notification.textContent = message;
        document.body.appendChild(notification);

        setTimeout(() => (notification.style.transform = "translateX(0)"), 100);
        setTimeout(() => {
            notification.style.transform = "translateX(100%)";
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }

    formatTime(dateString) {
        const date = new Date(dateString);
        return date.toLocaleTimeString("vi-VN", {
            hour: "2-digit",
            minute: "2-digit",
        });
    }

    escapeHtml(unsafe) {
        if (!unsafe) return "";
        return unsafe
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    async sendAttachment(type, file) {
        if (!file) return;
        const formData = new FormData();
        formData.append("conversation_id", this.conversationId);
        formData.append("message", ""); // Gửi message rỗng
        if (type === "image") {
            formData.append("image", file);
        } else {
            formData.append("file", file);
        }
        formData.append(
            "_token",
            document.querySelector('meta[name="csrf-token"]').content
        );

        try {
            const url = this.api.send;
            const response = await fetch(url, {
                method: "POST",
                body: formData,
                headers: { "X-Requested-With": "XMLHttpRequest" },
            });
            const data = await response.json();
            if (data.success) {
                if (
                    String(this.conversationId) ===
                    String(data.data.conversation_id)
                ) {
                    this.appendMessage(data.data);
                    this.scrollToBottom();
                }
                if (typeof updateSidebarPreview === "function") {
                    updateSidebarPreview({
                        ...data.data,
                        conversation_id: this.conversationId,
                    });
                }
            } else {
                this.showError(data.message || "Không thể gửi file");
            }
        } catch (e) {
            this.showError("Không thể gửi file");
        }
    }

    distributeConversation(conversationId, branchId) {
        const select = document.querySelector(
            `.distribution-select[data-conversation-id="${conversationId}"]`
        );
        if (!select) return;

        const branchName = select.options[select.selectedIndex].text;
        createDistributionModal(conversationId, branchId, branchName, this);
    }

    async confirmDistribution(conversationId, branchId) {
        try {
            const response = await fetch(this.api.distribute, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector(
                        'meta[name="csrf-token"]'
                    ).content,
                    "X-Requested-With": "XMLHttpRequest",
                },
                body: JSON.stringify({
                    conversation_id: conversationId,
                    branch_id: branchId,
                }),
            });

            const data = await response.json();
            if (data.success) {
                this.showNotification(
                    "Đã phân phối cuộc trò chuyện thành công"
                );

                // Cập nhật UI
                const chatItem = document.querySelector(
                    `.chat-item[data-conversation-id="${conversationId}"]`
                );
                if (chatItem) {
                    chatItem.classList.add("distributed");
                    chatItem.dataset.status = "distributed";
                    chatItem.dataset.branchName = data.branch.name;

                    // Cập nhật badges
                    const badges = chatItem.querySelector(".chat-item-badges");
                    if (badges) {
                        badges.innerHTML = `
                            <span class="badge badge-distributed">Đã phân phối</span>
                            <span class="badge badge-xs branch-badge ml-2">${data.branch.name}</span>
                        `;
                    }

                    // Cập nhật branch badge ở chat-main header
                    const mainBranchBadge =
                        document.getElementById("main-branch-badge");
                    if (mainBranchBadge) {
                        mainBranchBadge.textContent = data.branch.name;
                        mainBranchBadge.style.display = "";
                    }

                    // Cập nhật branch badge ở customer info
                    const infoBranchBadge = document.getElementById(
                        "customer-info-branch-badge"
                    );
                    if (infoBranchBadge) {
                        infoBranchBadge.textContent = data.branch.name;
                        infoBranchBadge.style.display = "";
                    }
                }

                // Reload sau 1 giây
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            } else {
                throw new Error(data.message || "Phân công thất bại");
            }
        } catch (error) {
            console.error("❌ Lỗi khi phân công:", error);
            this.showError("Không thể phân công cuộc trò chuyện");
        }
    }
}

// Export cho global use
window.ChatCommon = ChatCommon;

// Khởi tạo chat admin khi trang đã load
document.addEventListener("DOMContentLoaded", function () {
    const chatContainer = document.getElementById("chat-container");
    if (chatContainer) {
        adminChatInstance = new ChatCommon({
            conversationId: chatContainer.dataset.conversationId,
            userId: chatContainer.dataset.userId,
            userType: chatContainer.dataset.userType,
            api: {
                send: "/admin/chat/send",
                getMessages: "/admin/chat/messages/:id",
                distribute: "/admin/chat/distribute",
            },
        });
    }
});

// Thêm CSS cho section xác nhận phân công

// Thêm hàm tạo modal xác nhận phân phối
function createDistributionModal(
    conversationId,
    branchId,
    branchName,
    instance
) {
    const modal = document.createElement("div");
    modal.id = "distribution-modal";
    modal.className =
        "fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50";
    modal.innerHTML = `
        <div class="bg-white rounded-lg p-6 max-w-sm w-full mx-4">
            <h3 class="text-lg font-semibold mb-4">Xác nhận phân phối</h3>
            <p class="mb-4">Bạn có chắc chắn muốn phân phối cuộc trò chuyện này cho chi nhánh <strong>${branchName}</strong>?</p>
            <div class="flex justify-end gap-2">
                <button id="cancel-distribution" class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">Hủy</button>
                <button id="confirm-distribution" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">Xác nhận</button>
            </div>
        </div>
    `;
    document.body.appendChild(modal);

    // Xử lý sự kiện
    document.getElementById("cancel-distribution").onclick = () => {
        modal.remove();
    };
    document.getElementById("confirm-distribution").onclick = () => {
        if (instance && typeof instance.confirmDistribution === "function") {
            instance.confirmDistribution(conversationId, branchId);
        }
        modal.remove();
    };
}

class BranchChat {
    constructor(options) {
        console.log("[BranchChat] Init with options:", options);
        if (!options || !options.conversationId || !options.userId) {
            throw new Error(
                "Missing required options: conversationId and userId"
            );
        }
        this.conversationId = options.conversationId;
        this.userId = options.userId;
        this.userType = options.userType || "branch";
        this.api = options.api || {};
        this.messageInput = document.querySelector(
            options.messageInputSelector || "#chat-input-message"
        );
        this.sendBtn = document.querySelector(
            options.sendButtonSelector || "#chat-send-btn"
        );
        this.attachmentInput = document.querySelector(
            options.fileInputSelector || "#chat-input-file"
        );
        this.imageInput = document.querySelector(
            options.imageInputSelector || "#chat-input-image"
        );
        this.attachmentPreview = document.getElementById("attachment-preview");
        this.messageContainer = document.getElementById("chat-messages");
        this.chatContainer = document.getElementById("chat-container");
        this.pusher = new Pusher("6ef607214efab0d72419", {
            cluster: "ap1",
            encrypted: true,
        });
        this.init();
    }
    init() {
        console.log("[BranchChat] init()");
        this.setupEventListeners();
        this.setupPusherChannels();
        this.loadMessages();
    }
    setupEventListeners() {
        console.log("[BranchChat] setupEventListeners");
        if (this.messageInput && this.sendBtn) {
            this.sendBtn.addEventListener("click", (e) => {
                e.preventDefault();
                this.sendMessage();
            });
            this.messageInput.addEventListener("keypress", (e) => {
                if (e.key === "Enter" && !e.shiftKey) {
                    e.preventDefault();
                    this.sendMessage();
                }
            });
        } else {
            console.warn(
                "[BranchChat] Không tìm thấy messageInput hoặc sendBtn"
            );
        }
        // Gửi file
        if (this.attachmentInput) {
            this.attachmentInput.addEventListener("change", (e) => {
                if (e.target.files.length) {
                    this.sendAttachment("file", e.target.files[0]);
                }
            });
        } else {
            console.warn("[BranchChat] Không tìm thấy attachmentInput");
        }
        // Gửi ảnh
        if (this.imageInput) {
            this.imageInput.addEventListener("change", (e) => {
                if (e.target.files.length) {
                    this.sendAttachment("image", e.target.files[0]);
                }
            });
        } else {
            console.warn("[BranchChat] Không tìm thấy imageInput");
        }
    }
    setupPusherChannels() {
        console.log("[BranchChat] setupPusherChannels", this.conversationId);
        const channel = this.pusher.subscribe(`chat.${this.conversationId}`);
        channel.bind("new-message", (data) => {
            console.log("[BranchChat] new-message", data);
            if (
                data.message &&
                String(data.message.conversation_id) ===
                    String(this.conversationId)
            ) {
                this.appendMessage(data.message);
                this.scrollToBottom();
            }
        });
        channel.bind("conversation-updated", (data) => {
            console.log("[BranchChat] conversation-updated", data);
            // Có thể cập nhật trạng thái nếu cần
        });
    }
    async loadMessages() {
        if (!this.conversationId) return;
        try {
            const url = this.api.getMessages.replace(
                ":id",
                this.conversationId
            );
            console.log("[BranchChat] loadMessages url:", url);
            const response = await fetch(url);
            const data = await response.json();
            console.log("[BranchChat] loadMessages response:", data);
            if (this.messageContainer) {
                this.messageContainer.innerHTML = "";
                if (data.messages && Array.isArray(data.messages)) {
                    data.messages.forEach((message) => {
                        this.appendMessage(message);
                    });
                    this.scrollToBottom();
                }
            }
        } catch (error) {
            console.error("[BranchChat] loadMessages error:", error);
            this.showError("Không thể tải tin nhắn");
        }
    }
    async sendMessage() {
        if (!this.messageInput || !this.messageInput.value.trim()) {
            console.warn(
                "[BranchChat] sendMessage: Không có messageInput hoặc nội dung rỗng"
            );
            return;
        }

        if (!this.conversationId) {
            console.error("[BranchChat] sendMessage: Không có conversationId");
            this.showError("Không thể gửi tin nhắn: Chưa chọn cuộc trò chuyện");
            return;
        }

        const message = this.messageInput.value.trim();
        this.messageInput.value = "";
        if (this.sendBtn) this.sendBtn.disabled = true;

        try {
            const formData = new FormData();
            formData.append("message", message);
            formData.append("conversation_id", this.conversationId);

            const url = this.api.send;
            if (!url) {
                this.showError("API gửi tin nhắn chưa được cấu hình");
                return;
            }

            console.log("[BranchChat] sendMessage POST", url, {
                conversation_id: this.conversationId,
                message: message,
            });

            const response = await fetch(url, {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": document.querySelector(
                        'meta[name="csrf-token"]'
                    ).content,
                    "X-Requested-With": "XMLHttpRequest",
                },
                body: formData,
            });

            const data = await response.json();
            console.log("[BranchChat] sendMessage response:", data);

            if (data.success) {
                this.appendMessage(data.data);
                this.scrollToBottom();
                if (this.attachmentPreview)
                    this.attachmentPreview.innerHTML = "";
                if (this.attachmentInput) this.attachmentInput.value = "";
            } else {
                throw new Error(data.message || "Gửi tin nhắn thất bại");
            }
        } catch (error) {
            console.error("[BranchChat] sendMessage error:", error);
            this.showError(error.message || "Không thể gửi tin nhắn");
            this.messageInput.value = message;
        } finally {
            if (this.sendBtn) this.sendBtn.disabled = false;
            if (this.messageInput) {
                this.messageInput.focus();
            }
        }
    }
    async sendAttachment(type, file) {
        console.log("[BranchChat] sendAttachment", type, file);
        if (!file) return;
        const formData = new FormData();
        formData.append("conversation_id", this.conversationId);
        formData.append("message", ""); // Gửi message rỗng
        if (type === "image") {
            formData.append("image", file);
        } else {
            formData.append("file", file);
        }
        formData.append(
            "_token",
            document.querySelector('meta[name="csrf-token"]').content
        );
        try {
            const url = this.api.send;
            console.log("[BranchChat] sendAttachment POST", url, formData);
            const response = await fetch(url, {
                method: "POST",
                body: formData,
                headers: { "X-Requested-With": "XMLHttpRequest" },
            });
            const data = await response.json();
            console.log("[BranchChat] sendAttachment response:", data);
            if (data.success) {
                if (
                    String(this.conversationId) ===
                    String(data.data.conversation_id)
                ) {
                    this.appendMessage(data.data);
                    this.scrollToBottom();
                }
            } else {
                this.showError(data.message || "Không thể gửi file");
            }
        } catch (e) {
            console.error("[BranchChat] sendAttachment error:", e);
            this.showError("Không thể gửi file");
        }
    }
    async loadConversation(conversationId) {
        console.log("[BranchChat] loadConversation", conversationId);
        if (!conversationId) {
            console.error("[BranchChat] loadConversation: Không có conversationId");
            return;
        }

        this.conversationId = conversationId;
        if (this.api && this.api.getMessages) {
            this.api.getMessages = `/branch/chat/api/conversation/${conversationId}`;
        }

        try {
            const url = `/branch/chat/api/conversation/${conversationId}`;
            console.log("[BranchChat] loadConversation fetching", url);
            const response = await fetch(url);
            const data = await response.json();
            console.log("[BranchChat] loadConversation response:", data);

            if (data && data.success && data.conversation) {
                const conv = data.conversation;
                // Lưu lại customerId để xác định loại sender khi appendMessage
                this.conversationCustomerId = conv.customer?.id;
                
                // Update UI elements
                const elements = {
                    "chat-header-name": conv.customer?.full_name || conv.customer?.name || "Khách hàng",
                    "chat-header-email": conv.customer?.email || "",
                    "chat-header-avatar": (conv.customer?.full_name || conv.customer?.name || "K").charAt(0).toUpperCase(),
                    "chat-info-name": conv.customer?.full_name || conv.customer?.name || "Khách hàng",
                    "chat-info-email": conv.customer?.email || "",
                    "chat-info-avatar": (conv.customer?.full_name || conv.customer?.name || "K").charAt(0).toUpperCase(),
                    "chat-info-status": conv.status_label || conv.status || "",
                    "chat-info-branch": conv.branch?.name || ""
                };

                Object.entries(elements).forEach(([id, value]) => {
                    const element = document.getElementById(id);
                    if (element) {
                        element.textContent = value;
                    } else {
                        console.warn(`[BranchChat] Element not found: ${id}`);
                    }
                });

                // Load messages after conversation is loaded
                await this.loadMessages();
            } else {
                console.error("[BranchChat] loadConversation: Invalid response", data);
                this.showError("Không thể tải thông tin cuộc trò chuyện");
            }
        } catch (e) {
            console.error("[BranchChat] loadConversation fetch error:", e);
            this.showError("Không thể tải thông tin cuộc trò chuyện");
        }
    }
    appendMessage(message) {
        if (!this.messageContainer) return;
        // Fallback xác định loại sender nếu không có sender_type
        let senderType = message.sender_type;
        if (!senderType) {
            if (message.sender && message.sender.id == this.userId) {
                senderType = "branch_staff";
            } else if (
                this.conversationCustomerId &&
                message.sender &&
                message.sender.id == this.conversationCustomerId
            ) {
                senderType = "customer";
            } else {
                senderType = "customer";
            }
        }
        if (
            senderType !== "branch_staff" &&
            senderType !== "customer" &&
            !message.is_system_message
        )
            return;
        const isBranch = senderType === "branch_staff";
        const senderName = isBranch
            ? "Bạn"
            : message.sender?.full_name || message.sender?.name || "Khách hàng";
        const avatarLetter = senderName.charAt(0).toUpperCase();
        let attachmentHtml = "";
        if (message.attachment) {
            if (message.attachment_type === "image") {
                attachmentHtml = `<img src="/storage/${message.attachment}" class="mt-2 rounded-lg max-h-40 cursor-pointer" onclick="window.open('/storage/${message.attachment}','_blank')">`;
            } else {
                attachmentHtml = `<a href="/storage/${
                    message.attachment
                }" target="_blank" class="text-blue-500 underline">📎 ${message.attachment
                    .split("/")
                    .pop()}</a>`;
            }
        }
        const timeString = this.formatTime(
            message.created_at || message.sent_at
        );
        const msgDiv = document.createElement("div");
        msgDiv.className = `flex items-end gap-2 mb-2 ${
            isBranch ? "justify-end" : "justify-start"
        }`;
        msgDiv.innerHTML = `
            <div class="flex gap-2 max-w-[80%] ${
                isBranch ? "flex-row-reverse" : "flex-row"
            }">
                <div class="w-8 h-8 ${
                    isBranch ? "bg-blue-500" : "bg-orange-500"
                } rounded-full flex items-center justify-center flex-shrink-0">
                    <span class="text-white text-xs font-bold">${avatarLetter}</span>
                </div>
                <div class="flex flex-col ${
                    isBranch ? "items-end" : "items-start"
                }">
                    <div class="rounded-2xl px-4 py-2 max-w-full shadow-sm ${
                        isBranch
                            ? "bg-orange-500 text-white rounded-br-md"
                            : "bg-white text-gray-900 border border-gray-200 rounded-bl-md"
                    }">
                        <div>${this.escapeHtml(message.message) || ""}</div>
                        ${attachmentHtml}
                    </div>
                    <span class="text-xs text-gray-500 mt-1 px-2">${timeString}</span>
                </div>
            </div>
        `;
        this.messageContainer.appendChild(msgDiv);
        this.scrollToBottom();
    }
    showFilePreview(file) {
        if (!this.attachmentPreview) return;
        if (file.type.startsWith("image/")) {
            const reader = new FileReader();
            reader.onload = (e) => {
                this.attachmentPreview.innerHTML = `<img src="${e.target.result}" alt="Preview" style="max-width: 100px; max-height: 100px;"> <span>${file.name}</span>`;
            };
            reader.readAsDataURL(file);
        } else {
            this.attachmentPreview.innerHTML = `<i class="fas fa-file"></i> <span>${file.name}</span>`;
        }
        this.attachmentPreview.style.display = "block";
    }
    scrollToBottom() {
        if (this.messageContainer) {
            setTimeout(() => {
                this.messageContainer.scrollTop =
                    this.messageContainer.scrollHeight;
            }, 100);
        }
    }
    showError(message) {
        this.showNotification(message, "error");
    }
    showNotification(message, type = "success") {
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
        notification.textContent = message;
        document.body.appendChild(notification);
        setTimeout(() => (notification.style.transform = "translateX(0)"), 100);
        setTimeout(() => {
            notification.style.transform = "translateX(100%)";
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }
    formatTime(dateString) {
        const date = new Date(dateString);
        return date.toLocaleTimeString("vi-VN", {
            hour: "2-digit",
            minute: "2-digit",
        });
    }
    escapeHtml(unsafe) {
        if (!unsafe) return "";
        return unsafe
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }
    destroy() {
        // Unsubscribe pusher nếu cần
    }
}

window.BranchChat = BranchChat;
