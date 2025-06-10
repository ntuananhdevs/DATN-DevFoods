class Chat {
    constructor(conversationId, userId, userType) {
        this.conversationId = conversationId;
        this.userId = userId;
        this.userType = userType;
        this.typingTimeout = null;
        this.pusher = new Pusher(window.pusherKey, {
            cluster: window.pusherCluster,
            encrypted: true,
        });
        this.messageContainer = document.getElementById("chat-messages");
        this.typingIndicator = null;
        this.isTyping = false;

        this.initializeEventListeners();
        this.subscribeToChannels();
        this.setupTypingIndicator();
    }

    initializeEventListeners() {
        const messageInput = document.getElementById("message-input");
        if (messageInput) {
            messageInput.addEventListener("input", () => {
                this.handleTyping();
            });
            messageInput.addEventListener("keypress", (e) => {
                if (e.key === "Enter" && !e.shiftKey) {
                    e.preventDefault();
                    this.sendMessage();
                } else {
                    this.handleTyping();
                }
            });
            messageInput.addEventListener("blur", () => {
                this.stopTyping();
            });
        }
        const sendForm =
            document.getElementById("send-message-form") ||
            document.getElementById("chat-form");
        if (sendForm) {
            sendForm.addEventListener("submit", (e) => {
                e.preventDefault();
                this.sendMessage();
            });
        }
        const fileInput = document.getElementById("attachment");
        if (fileInput) {
            fileInput.addEventListener("change", (e) => {
                this.handleFileSelect(e);
            });
        }
    }

    subscribeToChannels() {
        if (this.conversationId) {
            this.pusher
                .subscribe(`chat.${this.conversationId}`)
                .bind("new-message", (data) => {
                    this.handleNewMessage(data.message);
                })
                .bind("typing-status", (data) => {
                    this.handleTypingStatus(data);
                });
        }
    }

    async sendTypingIndicator(isTyping) {
        if (!this.conversationId) return;
        try {
            const response = await fetch("/admin/chat/typing", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document
                        .querySelector('meta[name="csrf-token"]')
                        .getAttribute("content"),
                },
                body: JSON.stringify({
                    conversation_id: this.conversationId,
                    is_typing: isTyping,
                }),
            });
            if (!response.ok) {
                throw new Error("Failed to send typing indicator");
            }
        } catch (error) {
            console.error("Error sending typing indicator:", error);
        }
    }

    handleTyping() {
        clearTimeout(this.typingTimeout);
        this.sendTypingIndicator(true);
        this.isTyping = true;
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

    handleTypingStatus(data) {
        if (data.user_id === this.userId) return; // Bỏ qua nếu là chính mình
        if (data.is_typing) {
            this.showTypingIndicator(data.user_name || "Đối phương");
        } else {
            this.hideTypingIndicator();
        }
    }

    handleNewMessage(message) {
        console.log("Received new message:", message);
        this.displayMessage(message);
        this.scrollToBottom();
        this.updateSidebar(message);
    }

    updateSidebar(message) {
        const convItem = document.querySelector(
            `.chat-item[data-conversation-id='${message.conversation_id}']`
        );
        if (convItem) {
            const preview = convItem.querySelector(".chat-item-preview");
            if (preview) preview.textContent = message.message;
            const time = convItem.querySelector(".chat-item-time");
            if (time) time.textContent = this.formatTime(message.created_at);
            if (convItem.parentNode.firstChild !== convItem) {
                convItem.parentNode.insertBefore(
                    convItem,
                    convItem.parentNode.firstChild
                );
            }
        }
    }

    formatTime(timeStr) {
        const d = new Date(timeStr);
        return (
            d.getHours().toString().padStart(2, "0") +
            ":" +
            d.getMinutes().toString().padStart(2, "0")
        );
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

    async loadMessages() {
        try {
            let endpoint = `/admin/chat/messages/${this.conversationId}`;
            if (this.userType === "branch") {
                endpoint = `/branch/chat/messages/${this.conversationId}`;
            } else if (this.userType === "customer") {
                endpoint = `/api/chat/messages/${this.conversationId}`;
            }
            const response = await fetch(endpoint, {
                method: "GET",
                headers: {
                    "X-Requested-With": "XMLHttpRequest",
                    "X-CSRF-TOKEN": document
                        .querySelector('meta[name="csrf-token"]')
                        .getAttribute("content"),
                },
            });
            const result = await response.json();
            if (result.success) {
                if (this.messageContainer) {
                    this.messageContainer.innerHTML = "";
                }
                result.messages.forEach((message) => {
                    this.displayMessage(message);
                });
                this.scrollToBottom();
            } else {
                this.showError(
                    "Không thể tải tin nhắn: " +
                        (result.message || "Lỗi không xác định")
                );
            }
        } catch (error) {
            this.showError("Lỗi khi tải tin nhắn: " + error.message);
        }
    }

    async sendMessage() {
        const messageInput = document.getElementById("message-input");
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
        formData.append(
            "_token",
            document
                .querySelector('meta[name="csrf-token"]')
                .getAttribute("content")
        );
        try {
            this.stopTyping();
            let endpoint = "/admin/chat/send-message";
            if (this.userType === "branch") {
                endpoint = "/branch/chat/send-message";
            } else if (this.userType === "customer") {
                endpoint = "/api/customer/send-message";
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
                if (messageInput) messageInput.value = "";
                if (fileInput) fileInput.value = "";
                this.showNotification("Tin nhắn đã được gửi", "success");
            } else {
                throw new Error(result.message || "Failed to send message");
            }
        } catch (error) {
            this.showError("Không thể gửi tin nhắn. Vui lòng thử lại.");
        }
    }

    displayMessage(message) {
        if (!this.messageContainer) return;
        const currentUserId = this.userId;
        const isAdmin = String(message.sender_id) === String(currentUserId);
        const senderName = isAdmin
            ? "Admin"
            : message.sender?.name || "Khách hàng";
        const firstLetter = senderName.charAt(0).toUpperCase();
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

    scrollToBottom() {
        if (this.messageContainer) {
            setTimeout(() => {
                this.messageContainer.scrollTop =
                    this.messageContainer.scrollHeight;
            }, 100);
        }
    }

    showNotification(message, type = "success") {
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
        setTimeout(() => (notification.style.transform = "translateX(0)"), 100);
        setTimeout(() => {
            notification.style.transform = "translateX(100%)";
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }

    showError(message) {
        this.showNotification(message, "error");
    }

    escapeHtml(text) {
        const div = document.createElement("div");
        div.textContent = text;
        return div.innerHTML;
    }
}
