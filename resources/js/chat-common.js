import Echo from "laravel-echo";
import Pusher from "pusher-js";

class ChatCommon {
    constructor(options) {
        this.conversationId = options.conversationId;
        this.userId = options.userId;
        this.userType = options.userType || "customer";
        this.messageContainer = document.getElementById(
            options.messageContainerId || "chat-messages"
        );
        this.inputId = options.inputId || "message-input";
        this.fileInputId = options.fileInputId || "fileInput";
        this.imageInputId = options.imageInputId || "imageInput";
        this.sendBtnId = options.sendBtnId || "sendBtn";
        this.attachmentPreviewId =
            options.attachmentPreviewId || "attachment-preview";
        this.api = options.api || {}; // { send, getMessages, typing, ... }
        this.echo = null;
        this.channel = null;
        this.init();
    }

    init() {
        this.setupEventListeners();
        this.loadMessages();
        this.setupRealtime();
    }

    setupEventListeners() {
        const messageInput = document.getElementById(this.inputId);
        const sendBtn = document.getElementById(this.sendBtnId);
        const fileInput = document.getElementById(this.fileInputId);
        const imageInput = document.getElementById(this.imageInputId);

        if (messageInput && sendBtn) {
            sendBtn.addEventListener("click", () => this.sendMessage());
            messageInput.addEventListener("keypress", (e) => {
                if (e.key === "Enter" && !e.shiftKey) {
                    e.preventDefault();
                    this.sendMessage();
                }
                this.sendTyping(true);
            });
            messageInput.addEventListener("input", () => this.sendTyping(true));
            messageInput.addEventListener("blur", () => this.sendTyping(false));
        }

        if (fileInput) {
            fileInput.addEventListener("change", (e) =>
                this.handleFileUpload(e, "file")
            );
        }
        if (imageInput) {
            imageInput.addEventListener("change", (e) =>
                this.handleFileUpload(e, "image")
            );
        }
    }

    async loadMessages() {
        if (!this.api.getMessages) return;
        try {
            const response = await fetch(
                this.api.getMessages.replace(":id", this.conversationId)
            );
            const data = await response.json();
            if (
                data.success &&
                (data.messages || data.conversation?.messages)
            ) {
                this.renderMessages(
                    data.messages || data.conversation.messages
                );
            }
        } catch (e) {
            this.showError("Không thể tải tin nhắn");
        }
    }

    renderMessages(messages) {
        if (!this.messageContainer) return;
        this.messageContainer.innerHTML = "";
        messages.forEach((msg) => this.displayMessage(msg));
    }

    displayMessage(message) {
        if (!this.messageContainer) return;
        const div = document.createElement("div");
        div.className =
            String(message.sender_id) === String(this.userId)
                ? "message sent"
                : "message received";
        div.innerHTML = `
            <div class="message-bubble">${this.escapeHtml(
                message.message || ""
            )}</div>
            <div class="message-info">${
                message.sender?.name || "Khách"
            } • ${this.formatTime(message.created_at)}</div>
        `;
        this.messageContainer.appendChild(div);
        this.scrollToBottom();
    }

    async sendMessage() {
        const messageInput = document.getElementById(this.inputId);
        if (!messageInput || !this.api.send) return;
        const message = messageInput.value.trim();
        if (!message) return;
        const formData = new FormData();
        formData.append("conversation_id", this.conversationId);
        formData.append("message", message);

        try {
            const response = await fetch(this.api.send, {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": document
                        .querySelector('meta[name="csrf-token"]')
                        .getAttribute("content"),
                },
                body: formData,
            });
            const data = await response.json();
            if (data.success && data.data) {
                this.displayMessage(data.data);
                messageInput.value = "";
            }
        } catch (e) {
            this.showError("Không thể gửi tin nhắn");
        }
    }

    async handleFileUpload(e, type) {
        if (!this.api.send) return;
        const file = e.target.files[0];
        if (!file) return;
        const formData = new FormData();
        formData.append("conversation_id", this.conversationId);
        formData.append("attachment", file);

        try {
            const response = await fetch(this.api.send, {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": document
                        .querySelector('meta[name="csrf-token"]')
                        .getAttribute("content"),
                },
                body: formData,
            });
            const data = await response.json();
            if (data.success && data.data) {
                this.displayMessage(data.data);
            }
        } catch (e) {
            this.showError("Không thể gửi tệp");
        }
    }

    sendTyping(isTyping) {
        if (!this.api.typing) return;
        fetch(this.api.typing, {
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
    }

    setupRealtime() {
        if (!window.Echo && window.Pusher) {
            window.Echo = new Echo({
                broadcaster: "pusher",
                key: window.pusherKey || "your-pusher-key",
                cluster: window.pusherCluster || "ap1",
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
        }
        if (window.Echo) {
            this.channel = window.Echo.private("chat." + this.conversationId)
                .listen(".message.sent", (e) => {
                    if (e && e.message) {
                        this.displayMessage(e.message);
                    }
                })
                .listen(".user.typing", (e) => {
                    // Hiển thị trạng thái đang nhập nếu muốn
                });
        }
    }

    escapeHtml(text) {
        const div = document.createElement("div");
        div.textContent = text;
        return div.innerHTML;
    }

    formatTime(timeStr) {
        const d = new Date(timeStr);
        return (
            d.getHours().toString().padStart(2, "0") +
            ":" +
            d.getMinutes().toString().padStart(2, "0")
        );
    }

    scrollToBottom() {
        if (this.messageContainer) {
            setTimeout(() => {
                this.messageContainer.scrollTop =
                    this.messageContainer.scrollHeight;
            }, 100);
        }
    }

    showError(msg) {
        alert(msg);
    }

    distributeConversation(convId, branchId) {
        if (!convId || !branchId) return;
        fetch(this.api.distribute, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document
                    .querySelector('meta[name="csrf-token"]')
                    .getAttribute("content"),
            },
            body: JSON.stringify({
                conversation_id: convId,
                branch_id: branchId,
            }),
        })
            .then((res) => res.json())
            .then((data) => {
                if (data.success) {
                    // Cập nhật giao diện: xóa select phân công, thêm badge "Đã phân công"
                    var select = document.querySelector(
                        '.distribution-select[data-conversation-id="' +
                            convId +
                            '"]'
                    );
                    if (select) {
                        var chatItem = select.closest(".chat-item");
                        var footer =
                            chatItem?.querySelector(".chat-item-footer");
                        if (footer) {
                            var badges =
                                footer.querySelector(".chat-item-badges");
                            if (badges) {
                                badges.innerHTML =
                                    '<span class="badge badge-distributed">Đã phân công</span>';
                            }
                        }
                        select.remove();
                    }
                } else {
                    alert(data.message || "Lỗi phân phối");
                }
            })
            .catch((e) => alert("Lỗi phân phối: " + e));
    }
}

window.ChatCommon = ChatCommon;
