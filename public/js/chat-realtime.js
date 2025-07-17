// Class ChatCommon cho admin chat

class ChatCommon {
    constructor(options) {
        if (!options || !options.conversationId || !options.userId) {
            return;
        }

        this.conversationId = options.conversationId;
        this.userId = options.userId;
        this.userType = options.userType || "admin";
        this.api = options.api || {};

        // Khởi tạo các DOM elements
        this.messageContainer = document.getElementById("chat-messages");
        this.messageInput = document.getElementById("chat-input-message");
        this.sendBtn = document.getElementById("chat-send-btn");
        this.fileInput = document.getElementById("chat-input-file");
        this.imageInput = document.getElementById("chat-input-image");
        this.chatContainer = document.getElementById("chat-container");
        this.chatList = document.getElementById("chat-list");

        // Khởi tạo Pusher
        if (window.PUSHER_APP_KEY && window.PUSHER_APP_CLUSTER) {
            this.pusher = new Pusher(window.PUSHER_APP_KEY, {
                cluster: window.PUSHER_APP_CLUSTER,
                encrypted: true,
            });
        } else if (window.pusherKey && window.pusherCluster) {
            this.pusher = new Pusher(window.pusherKey, {
                cluster: window.pusherCluster,
                encrypted: true,
            });
        } else {
            this.pusher = null;
        }

        this.init();
        this.setupPusherGlobalListeners(); // Lắng nghe Pusher JS thuần cho sidebar
        this.currentChannel = null; // Lưu channel hiện tại
        this.isSendingAttachment = false; // Thêm flag chống double submit
    }

    setupPusherGlobalListeners() {
        // Khởi tạo Pusher nếu chưa có
        if (window.PUSHER_APP_KEY && window.PUSHER_APP_CLUSTER) {
            if (!window._sidebarPusher) {
                window._sidebarPusher = new Pusher(window.PUSHER_APP_KEY, {
                    cluster: window.PUSHER_APP_CLUSTER,
                    encrypted: true,
                    authEndpoint: "/broadcasting/auth",
                    auth: {
                        headers: {
                            "X-CSRF-TOKEN": document.querySelector(
                                'meta[name="csrf-token"]'
                            ).content,
                        },
                    },
                });
            }
        } else if (window.pusherKey && window.pusherCluster) {
            if (!window._sidebarPusher) {
                window._sidebarPusher = new Pusher(window.pusherKey, {
                    cluster: window.pusherCluster,
                    encrypted: true,
                    authEndpoint: "/broadcasting/auth",
                    auth: {
                        headers: {
                            "X-CSRF-TOKEN": document.querySelector(
                                'meta[name="csrf-token"]'
                            ).content,
                        },
                    },
                });
            }
        } else {
            return;
        }
        const pusher = window._sidebarPusher;
        // Admin subscribe channel tổng (public channel)
        const adminChannel = pusher.subscribe("admin.conversations");
        adminChannel.bind("conversation.updated", (data) => {
            if (data.update_type === "created") {
                const chatList = this.chatList;
                const conversationId = data.conversation.id;
                let chatItem = chatList.querySelector(
                    `[data-conversation-id='${conversationId}']`
                );
                let sidebarMsg = data.last_message
                    ? {
                          ...data.last_message,
                          conversation_id: data.conversation.id,
                          status: data.conversation.status,
                          customer: data.conversation.customer,
                          branch_id: data.conversation.branch_id,
                      }
                    : {
                          conversation_id: data.conversation.id,
                          status: data.conversation.status,
                          customer: data.conversation.customer,
                          branch_id: data.conversation.branch_id,
                          message: "",
                          sender: data.conversation.customer
                              ? {
                                    full_name:
                                        data.conversation.customer.full_name,
                                }
                              : { full_name: "Khách hàng" },
                          sender_id: data.conversation.customer
                              ? data.conversation.customer.id
                              : "",
                          created_at: data.conversation.updated_at,
                      };
                if (!chatItem) {
                    // Tạo chat-item mới và prepend
                    chatItem = this.createSidebarChatItem(sidebarMsg);
                    if (chatList)
                        chatList.insertBefore(chatItem, chatList.firstChild);
                } else {
                    // Đã có, chỉ cập nhật preview/badge và di chuyển lên đầu
                    this.updateSidebarPreview(sidebarMsg);
                    chatItem.remove();
                    chatList.insertBefore(chatItem, chatList.firstChild);
                }
                this.showNotification("Có cuộc trò chuyện mới!", "info");
                let chatItemEl = document.querySelector(
                    `[data-conversation-id='${data.conversation.id}']`
                );
                if (chatItemEl) {
                    this.updateInfoPanel(chatItemEl);
                }
            } else if (data.last_message) {
                this.updateSidebarPreview({
                    ...data.last_message,
                    conversation_id: data.conversation.id,
                    status: data.conversation.status,
                    customer: data.conversation.customer,
                    branch_id: data.conversation.branch_id,
                });
                let chatItemEl = document.querySelector(
                    `[data-conversation-id='${data.conversation.id}']`
                );
                if (chatItemEl) {
                    this.updateInfoPanel(chatItemEl);
                }
            }
            // Cập nhật trạng thái nếu cần
            this.updateConversationStatus(data.conversation.status);
        });
        // LẮNG NGHE TIN NHẮN MỚI Ở CẤP SIDEBAR (ADMIN)
        adminChannel.bind("new-message", (data) => {
            // Đừng appendMessage ở đây!
            // this.appendMessage(data.message); // <-- XÓA hoặc comment dòng này
            this.updateSidebarPreview(data.message); // <-- chỉ update preview
        });
        // Đăng ký thành công
        adminChannel.bind("pusher:subscription_succeeded", () => {});
        // Lỗi subscribe
        adminChannel.bind("pusher:subscription_error", (err) => {});
    }

    init() {
        this.setupEventListeners();
        this.setupPusherChannels();
        if (this.conversationId) {
            this.loadMessages();
        }
    }

    setupEventListeners() {
        // Xử lý input tin nhắn
        if (this.messageInput) {
            this.messageInput.addEventListener("input", () => {
                this.sendTypingIndicator(true);
                this.handleTyping();
            });
            this.messageInput.addEventListener("keypress", (e) => {
                if (e.key === "Enter" && !e.shiftKey) {
                    e.preventDefault();
                    if (this.messageInput.value.trim()) {
                        this.sendMessage();
                    }
                }
            });
            this.messageInput.addEventListener("blur", () => {
                this.sendTypingIndicator(false);
            });
        }

        // Xử lý nút gửi tin nhắn
        if (this.sendBtn) {
            this.sendBtn.addEventListener("click", (e) => {
                e.preventDefault();
                this.sendMessage();
            });
        }

        // Xử lý form submit
        const chatForm = document.getElementById("chat-form");
        if (chatForm) {
            chatForm.addEventListener("submit", (e) => {
                e.preventDefault();
                this.sendMessage();
            });
        }

        // Xử lý đính kèm file
        if (this.fileInput) {
            this.fileInput.addEventListener("change", (e) => {
                this.handleFileSelect(e);
            });
        }

        // Xử lý đính kèm ảnh
        if (this.imageInput) {
            this.imageInput.addEventListener("change", (e) => {
                if (e.target.files.length) {
                    if (this.isSendingAttachment) return;
                    this.isSendingAttachment = true;
                    this.sendAttachment("image", e.target.files[0]).finally(
                        () => {
                            this.isSendingAttachment = false;
                            this.imageInput.value = "";
                        }
                    );
                }
            });
        }

        // Xử lý click vào cuộc trò chuyện
        document.querySelectorAll(".conversation-item").forEach((item) => {
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
                location.reload();
            });
        }

        // Nút gửi ảnh
        const attachImageBtn = document.getElementById("attachImageBtn");
        const imageInput = document.getElementById("chat-input-image");
        if (attachImageBtn && imageInput) {
            attachImageBtn.addEventListener("click", () => imageInput.click());
            imageInput.addEventListener("change", (e) => {
                if (e.target.files.length) {
                    if (this.isSendingAttachment) return;
                    this.isSendingAttachment = true;
                    this.sendAttachment("image", e.target.files[0]).finally(
                        () => {
                            this.isSendingAttachment = false;
                            this.imageInput.value = "";
                        }
                    );
                }
            });
        }

        // Nút gửi file
        const attachFileBtn = document.getElementById("attachFileBtn");
        const fileInput = document.getElementById("chat-input-file");
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
        if (!this.pusher) {
            return;
        }
        // Unsubscribe channel cũ nếu có
        if (this.currentChannel) {
            this.pusher.unsubscribe(`chat.${this.currentChannel}`);
        }
        this.currentChannel = this.conversationId;
        // Lắng nghe kênh chat
        const channel = this.pusher.subscribe(`chat.${this.conversationId}`);

        channel.bind("new-message", (data) => {
            if (data.message) {
                // Xóa message tạm thời đúng sender_id
                const tempMsg = this.messageContainer.querySelector(
                    `[data-message-id^='temp-'][data-sender-id='${data.message.sender_id}']`
                );
                if (tempMsg) tempMsg.remove();
                // Chỉ append nếu chưa có message thật
                const existingMessage = this.messageContainer.querySelector(
                    `[data-message-id='${data.message.id}']`
                );
                if (!existingMessage) {
                    this.appendMessage(data.message);
                    this.scrollToBottom();
                }
                // Cập nhật preview trong sidebar và di chuyển lên đầu
                this.updateSidebarPreview(data.message);
                this.moveConversationToTop(data.message.conversation_id);
            }
        });

        channel.bind("conversation.updated", (data) => {
            // Nếu là cuộc trò chuyện mới hoặc có last_message thì cập nhật sidebar
            if (data.update_type === "created") {
                // Nếu có last_message thì dùng, không thì tạo message giả từ thông tin conversation
                let sidebarMsg = data.last_message
                    ? {
                          ...data.last_message,
                          conversation_id: data.conversation.id,
                          status: data.conversation.status,
                          customer: data.conversation.customer,
                          branch_id: data.conversation.branch_id,
                      }
                    : {
                          conversation_id: data.conversation.id,
                          status: data.conversation.status,
                          customer: data.conversation.customer,
                          branch_id: data.conversation.branch_id,
                          message: "",
                          sender: data.conversation.customer
                              ? {
                                    full_name:
                                        data.conversation.customer.full_name,
                                }
                              : { full_name: "Khách hàng" },
                          sender_id: data.conversation.customer
                              ? data.conversation.customer.id
                              : "",
                          created_at: data.conversation.updated_at,
                      };
                this.updateSidebarPreview(sidebarMsg);
                this.showNotification("Có cuộc trò chuyện mới!", "info");
                let chatItemEl = document.querySelector(
                    `[data-conversation-id='${data.conversation.id}']`
                );
                if (chatItemEl) {
                    this.updateInfoPanel(chatItemEl);
                }
            } else if (data.last_message) {
                this.updateSidebarPreview({
                    ...data.last_message,
                    conversation_id: data.conversation.id,
                    status: data.conversation.status,
                    customer: data.conversation.customer,
                    branch_id: data.conversation.branch_id,
                });
                let chatItemEl = document.querySelector(
                    `[data-conversation-id='${data.conversation.id}']`
                );
                if (chatItemEl) {
                    this.updateInfoPanel(chatItemEl);
                }
            }
            // Cập nhật trạng thái nếu cần
            this.updateConversationStatus(data.conversation.status);
        });

        // LẮNG NGHE SỰ KIỆN TYPING
        channel.bind("user.typing", (data) => {
            if (
                Number(data.user_id) !== Number(this.userId) &&
                String(data.conversation_id) === String(this.conversationId)
            ) {
                if (data.is_typing) {
                    this.showTypingIndicator(data.user_name);
                } else {
                    this.hideTypingIndicator();
                }
            }
        });
    }

    updateSidebarPreview(message) {
        if (!this.chatList) return;
        let chatItem = document.querySelector(
            `.chat-item[data-conversation-id="${message.conversation_id}"]`
        );
        const isNew = !chatItem;

        if (!chatItem) {
            chatItem = this.createSidebarChatItem(message);
            this.chatList.insertBefore(chatItem, this.chatList.firstChild);
        } else {
            // Cập nhật preview tin nhắn (giới hạn 30 ký tự)
            const previewElement = chatItem.querySelector(".chat-item-preview");
            if (previewElement) {
                const preview =
                    (message.message || "...").length > 30
                        ? (message.message || "...").substring(0, 30) + "..."
                        : message.message || "...";
                previewElement.textContent = preview;
            }
            // Cập nhật thời gian
            const timeElement = chatItem.querySelector(".chat-item-time");
            if (timeElement && message.created_at) {
                timeElement.textContent = this.formatTime(message.created_at);
            }
            // Cập nhật badge trạng thái
            const badges = chatItem.querySelector(".chat-item-badges");
            if (badges) {
                let statusLabel = "";
                switch (message.status) {
                    case "distributed":
                        statusLabel = "Đã phân phối";
                        break;
                    case "active":
                        statusLabel = "Đang xử lý";
                        break;
                    case "closed":
                        statusLabel = "Đã đóng";
                        break;
                    case "resolved":
                        statusLabel = "Đã giải quyết";
                        break;
                    default:
                        statusLabel = "Chờ phản hồi";
                }
                badges.innerHTML =
                    `<span class="badge badge-distributed">${this.getStatusText(
                        message.status
                    )}</span>` +
                    (chatItem.dataset.branchName
                        ? `<span class="badge" style="background:#374151;color:#fff;">${chatItem.dataset.branchName}</span>`
                        : "");
            }

            // Luôn di chuyển lên đầu sidebar
            chatItem.remove();
            this.chatList.insertBefore(chatItem, this.chatList.firstChild);
        }
        // Gắn lại event click nếu cần
        chatItem.onclick = () => {
            this.switchConversation(message.conversation_id, chatItem);
        };
        // Thêm hiệu ứng highlight khi có tin nhắn mới hoặc vừa tạo mới
        chatItem.classList.add("highlight-new");
        setTimeout(() => {
            chatItem.classList.remove("highlight-new");
        }, 2000);
    }

    moveConversationToTop(conversationId) {
        const chatItem = document.querySelector(
            `.chat-item[data-conversation-id="${conversationId}"]`
        );
        if (chatItem && this.chatList) {
            // Xóa chat item khỏi vị trí hiện tại
            chatItem.remove();
            // Thêm vào đầu danh sách
            this.chatList.insertBefore(chatItem, this.chatList.firstChild);

            // Thêm hiệu ứng highlight
            chatItem.classList.add("highlight-new");
            setTimeout(() => {
                chatItem.classList.remove("highlight-new");
            }, 2000);
        }
    }

    async loadMessages() {
        if (!this.conversationId) return;

        try {
            const url = this.api.getMessages.replace(
                ":conversation",
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
            this.showError("Không thể tải tin nhắn");
        }
    }

    async sendMessage() {
        if (!this.messageInput) {
            return;
        }
        const message = this.messageInput.value.trim();
        // Ngăn gửi tin nhắn rỗng (không nhập gì và không có file/ảnh)
        if (
            !message &&
            !(this.fileInput?.files?.length || this.imageInput?.files?.length)
        ) {
            this.showError("Bạn phải nhập nội dung hoặc đính kèm file/ảnh!");
            return false; // Không gửi, không reload
        }
        // Hiển thị tin nhắn tạm thời ngay lập tức
        const tempId = "temp-" + Date.now();
        this.appendMessage({
            id: tempId,
            message: message,
            sender_id: this.userId,
            sender: { full_name: "Admin" },
            created_at: new Date().toISOString(),
            isTemp: true,
        });
        this.scrollToBottom();
        // Xóa nội dung input sau khi đã lấy giá trị
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
                // Không appendMessage ở đây nữa để tránh lặp
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
                this.messageInput.value = "";
                this.messageInput.focus();
                this.messageInput.style.height = "48px";
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
                )?.content,
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

    handleTyping() {
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
        if (this.typingTimeout) {
            clearTimeout(this.typingTimeout);
            this.typingTimeout = null;
        }
    }

    async sendTypingIndicator(isTyping) {
        try {
            await fetch("/admin/chat/typing", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector(
                        'meta[name="csrf-token"]'
                    ).content,
                    "X-Requested-With": "XMLHttpRequest",
                },
                body: JSON.stringify({
                    conversation_id: this.conversationId,
                    is_typing: isTyping,
                }),
            });
        } catch (e) {}
    }

    showTypingIndicator(userName) {
        let typingDiv = document.getElementById("admin-typing-indicator");
        if (!typingDiv) {
            typingDiv = document.createElement("div");
            typingDiv.id = "admin-typing-indicator";
            typingDiv.className = "typing-indicator";
            typingDiv.innerHTML = `<span class=\"typing-text\">${userName} đang nhập</span><span class=\"dot\"></span><span class=\"dot\"></span><span class=\"dot\"></span>`;
        }
        if (this.messageContainer) {
            const old = document.getElementById("admin-typing-indicator");
            if (old && old.parentNode) old.parentNode.removeChild(old);
            this.messageContainer.appendChild(typingDiv);
            this.scrollToBottom();
        }
    }

    hideTypingIndicator() {
        const typingDiv = document.getElementById("admin-typing-indicator");
        if (typingDiv && typingDiv.parentNode)
            typingDiv.parentNode.removeChild(typingDiv);
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
        const senderName =
            (message.sender && message.sender.full_name) || "Người dùng";
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
            new: "Chờ phản hồi",
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
            const url = this.api.getMessages.replace(
                ":conversation",
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
            this.showError("Không thể tải tin nhắn");
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

    async sendAttachment(type, file) {
        if (!file) return;
        // Tạo message tạm thời
        const tempId = "temp-" + Date.now();
        let tempMsg = {
            id: tempId,
            message: type === "image" ? "Đã gửi ảnh" : "Đã gửi file",
            attachment: type === "image" ? URL.createObjectURL(file) : null,
            attachment_type: type,
            sender_id: this.userId,
            sender: { full_name: "Admin" },
            created_at: new Date().toISOString(),
            isTemp: true,
        };
        this.appendMessage(tempMsg);
        this.scrollToBottom();
        try {
            const formData = new FormData();
            formData.append("conversation_id", this.conversationId);
            formData.append("message", "");
            formData.append("attachment", file);
            formData.append("attachment_type", type);
            formData.append(
                "_token",
                document.querySelector('meta[name="csrf-token"]').content
            );
            const url = this.api.send;
            await fetch(url, {
                method: "POST",
                body: formData,
                headers: { "X-Requested-With": "XMLHttpRequest" },
            });
            // KHÔNG appendMessage ở đây, chỉ append khi nhận realtime từ Pusher
        } catch (e) {
            this.showError("Không thể gửi file");
        } finally {
            if (this.messageInput) this.messageInput.value = "";
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
                    "Đã phân phối cuộc trò chuyện thành công",
                    "success"
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
                // Cập nhật preview trong sidebar và di chuyển lên đầu
                this.updateSidebarPreview(data.message);
                this.moveConversationToTop(data.message.conversation_id);
                showChatNotification(data.message.conversation_id);
            }
        } catch (error) {
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
        const infoPhone = document.getElementById("customer-info-phone");

        const infoBranch = document.getElementById(
            "customer-info-branch-badge"
        );

        if (avatar) avatar.textContent = firstLetter;
        if (name) name.textContent = customerName;
        if (email) email.textContent = customerEmail;
        if (infoPhone)
            infoPhone.textContent = "SĐT: " + (customerPhone || "---");
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
                status === "distributed"
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

        // Cập nhật API URL
        this.api.getMessages = `/admin/chat/messages/${conversationId}`;

        // Xóa tin nhắn cũ và tải tin nhắn mới
        if (this.messageContainer) {
            this.messageContainer.innerHTML = "";
        }
        this.loadMessages();
        this.setupPusherChannels();

        // Cập nhật trạng thái vào info panel
        const infoStatus = document.getElementById("customer-info-status");
        if (infoStatus) {
            if (status === "distributed" || status === "active") {
                infoStatus.textContent = "Đã phân phối";
            } else if (status === "new") {
                infoStatus.textContent = "Chờ phản hồi";
            } else if (status === "closed") {
                infoStatus.textContent = "Đã đóng";
            } else {
                infoStatus.textContent = status;
            }
        }
        // Hiển thị/ẩn thành phần phân công
        const distributionSection = document.getElementById(
            `distribution-${conversationId}`
        );
        if (distributionSection) {
            if (status === "distributed" || status === "active") {
                distributionSection.classList.add("active");
                distributionSection.style.display = "";
            } else {
                distributionSection.classList.remove("active");
                distributionSection.style.display = "none";
            }
        }
        this.updateInfoPanel(chatItem);
        // Hiển thị/ẩn select phân công chi nhánh
        const distributionContainer = document.getElementById(
            "distribution-select-container"
        );
        if (distributionContainer) {
            if (
                !branchName &&
                status === "new" &&
                window.branches &&
                window.branches.length
            ) {
                // Hiển thị select
                let select = document.createElement("select");
                select.className =
                    "distribution-select form-select w-full max-w-xs";
                select.id = "distribution-select";
                select.dataset.conversationId = conversationId;
                select.innerHTML = `<option value="" disabled selected>Chọn chi nhánh</option>`;
                window.branches.forEach((branch) => {
                    select.innerHTML += `<option value="${branch.id}">${branch.name}</option>`;
                });
                select.addEventListener("change", (e) => {
                    const branchId = e.target.value;
                    if (branchId) {
                        this.distributeConversation(conversationId, branchId);
                    }
                });
                distributionContainer.innerHTML = "";
                distributionContainer.appendChild(select);
            } else {
                // Ẩn select
                distributionContainer.innerHTML = "";
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
                    "Đã phân phối cuộc trò chuyện thành công",
                    "success"
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
        // Cập nhật trạng thái active
        document.querySelectorAll(".chat-item").forEach((item) => {
            item.classList.remove("active");
        });
        chatItem.classList.add("active");

        // Cập nhật conversation ID và API
        this.conversationId = conversationId;
        if (this.api) {
            this.api.getMessages = `/admin/chat/messages/${conversationId}`;
        }

        // Lấy thông tin từ chatItem
        const customerName = chatItem.dataset.customerName;
        const customerEmail = chatItem.dataset.customerEmail;
        const branchName = chatItem.dataset.branchName;
        const status = chatItem.dataset.status;
        const customerPhone = chatItem.dataset.customerPhone || "---";
        const lastActivity = chatItem.dataset.lastActivity || "";
        const firstLetter = customerName.charAt(0).toUpperCase();

        // Header
        const avatar = document.getElementById("chat-header-avatar");
        const name = document.getElementById("chat-header-name");
        const email = document.getElementById("chat-header-email");
        if (avatar) avatar.textContent = firstLetter;
        if (name) name.textContent = customerName;
        if (email) email.textContent = customerEmail;

        // Trạng thái badge header
        const statusBadge = document.querySelector(".status-badge");
        if (statusBadge) {
            statusBadge.textContent = this.getStatusText(status);
            statusBadge.className = `badge status-badge status-${status}`;
        }
        // Branch badge header
        const mainBranchBadge = document.getElementById("main-branch-badge");
        if (mainBranchBadge) {
            if (branchName) {
                mainBranchBadge.textContent = branchName;
                mainBranchBadge.style.display = "";
            } else {
                mainBranchBadge.style.display = "none";
            }
        }

        // Info panel
        const infoAvatar = document.getElementById("chat-info-avatar");
        const infoName = document.getElementById("chat-info-name");
        const infoEmail = document.getElementById("chat-info-email");
        const infoStatus = document.getElementById("chat-info-status");
        const infoBranch = document.getElementById("chat-info-branch");
        const infoLastActivity = document.getElementById(
            "chat-info-last-activity"
        );
        if (infoAvatar) infoAvatar.textContent = firstLetter;
        if (infoName) infoName.textContent = customerName;
        if (infoEmail) infoEmail.textContent = customerEmail;
        if (infoStatus) infoStatus.textContent = this.getStatusText(status);
        if (infoBranch) infoBranch.textContent = branchName ? branchName : "";
        if (infoLastActivity && lastActivity)
            infoLastActivity.textContent = lastActivity;

        // Xóa tin nhắn cũ và load mới
        if (this.messageContainer) {
            this.messageContainer.innerHTML = "";
        }
        this.loadMessages();
        this.setupPusherChannels();

        // Cập nhật info panel bên phải: số điện thoại và trạng thái
        const infoPhone = document.getElementById("chat-info-phone");
        if (infoPhone)
            infoPhone.textContent = "SĐT: " + (customerPhone || "---");
        // Cập nhật info panel bên phải: số điện thoại và trạng thái
        // Đã có biến status ở trên
        const infoStatusElem = document.getElementById("chat-info-status");
        if (infoStatusElem) {
            if (status === "distributed" || status === "active") {
                infoStatusElem.textContent = "Đã phân phối";
            } else if (status === "new") {
                infoStatusElem.textContent = "Chờ phản hồi";
            } else if (status === "resolved") {
                infoStatusElem.textContent = "Đã giải quyết";
            } else if (status === "closed") {
                infoStatusElem.textContent = "Đã đóng";
            } else {
                infoStatusElem.textContent = status;
            }
        }
        this.updateInfoPanel(chatItem);
        // Hiển thị/ẩn select phân công chi nhánh
        const distributionContainer = document.getElementById(
            "distribution-select-container"
        );
        if (distributionContainer) {
            if (
                !branchName &&
                status === "new" &&
                window.branches &&
                window.branches.length
            ) {
                // Hiển thị select
                let select = document.createElement("select");
                select.className =
                    "distribution-select form-select w-full max-w-xs";
                select.id = "distribution-select";
                select.dataset.conversationId = conversationId;
                select.innerHTML = `<option value="" disabled selected>Chọn chi nhánh</option>`;
                window.branches.forEach((branch) => {
                    select.innerHTML += `<option value="${branch.id}">${branch.name}</option>`;
                });
                select.addEventListener("change", (e) => {
                    const branchId = e.target.value;
                    if (branchId) {
                        this.distributeConversation(conversationId, branchId);
                    }
                });
                distributionContainer.innerHTML = "";
                distributionContainer.appendChild(select);
            } else {
                // Ẩn select
                distributionContainer.innerHTML = "";
            }
        }
    }

    appendMessage(message) {
        // Nếu message bị lồng trong key App\Models\ChatMessage thì lấy object bên trong ra
        if (
            message &&
            typeof message === "object" &&
            Object.keys(message).length === 1 &&
            Object.keys(message)[0].includes("App\\Models\\ChatMessage")
        ) {
            message = Object.values(message)[0];
        }
        if (!this.messageContainer) return;
        // Lấy tên người gửi ưu tiên full_name
        let senderName =
            (message.sender && message.sender.full_name) || "Người dùng";
        const isAdmin = String(message.sender_id) === String(this.userId);
        const avatarLetter = senderName.charAt(0).toUpperCase();
        let attachmentHtml = "";
        if (message.attachment) {
            if (message.attachment_type === "image") {
                attachmentHtml = `<img src="${
                    message.attachment.startsWith("blob:")
                        ? message.attachment
                        : "/storage/" + message.attachment
                }" class="mt-2 rounded-lg max-h-40 cursor-pointer" onclick="window.open('${
                    message.attachment.startsWith("blob:")
                        ? message.attachment
                        : "/storage/" + message.attachment
                }','_blank')">`;
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
        msgDiv.dataset.messageId = message.id;
        msgDiv.dataset.senderId = message.sender_id;
        msgDiv.innerHTML = `
            <div class="flex gap-2 max-w-[80%] ${
                isAdmin ? "flex-row-reverse" : "flex-row"
            }">
                <div class="w-8 h-8 ${
                    isAdmin ? "bg-blue-500" : "bg-orange-500"
                } rounded-full mt-5 flex items-center justify-center flex-shrink-0">
                    <span class="text-white text-xs font-bold">${avatarLetter}</span>
                </div>
                <div class="flex flex-col ${
                    isAdmin ? "items-end" : "items-start"
                }">
                    <div class="text-xs text-gray-500 mb-1">${senderName}</div>
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
        // Thêm hiệu ứng fade-in cho tin nhắn mới
        msgDiv.style.opacity = "0";
        msgDiv.style.transition = "opacity 0.3s ease-in-out";
        this.messageContainer.appendChild(msgDiv);
        setTimeout(() => {
            msgDiv.style.opacity = "1";
        }, 50);
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
        // Tạo message tạm thời
        const tempId = "temp-" + Date.now();
        let tempMsg = {
            id: tempId,
            message: type === "image" ? "Đã gửi ảnh" : "Đã gửi file",
            attachment: type === "image" ? URL.createObjectURL(file) : null,
            attachment_type: type,
            sender_id: this.userId,
            sender: { full_name: "Admin" },
            created_at: new Date().toISOString(),
            isTemp: true,
        };
        this.appendMessage(tempMsg);
        this.scrollToBottom();
        try {
            const formData = new FormData();
            formData.append("conversation_id", this.conversationId);
            formData.append("message", "");
            formData.append("attachment", file);
            formData.append("attachment_type", type);
            formData.append(
                "_token",
                document.querySelector('meta[name="csrf-token"]').content
            );
            const url = this.api.send;
            await fetch(url, {
                method: "POST",
                body: formData,
                headers: { "X-Requested-With": "XMLHttpRequest" },
            });
            // KHÔNG appendMessage ở đây, chỉ append khi nhận realtime từ Pusher
        } catch (e) {
            this.showError("Không thể gửi file");
        } finally {
            if (this.messageInput) this.messageInput.value = "";
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
                    "Đã phân phối cuộc trò chuyện thành công",
                    "success"
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
            this.showError("Không thể phân công cuộc trò chuyện");
        }
    }

    // Thêm hàm tạo chat-item cho admin
    createSidebarChatItem(message) {
        const div = document.createElement("div");
        div.className = "chat-item conversation-item";
        div.dataset.conversationId = message.conversation_id;
        div.dataset.status = message.status || "new";
        div.dataset.customerName =
            (message.customer &&
                (message.customer.full_name || message.customer.name)) ||
            (message.sender && message.sender.full_name) ||
            "Khách hàng";
        div.dataset.customerEmail =
            (message.customer && message.customer.email) || "";
        div.dataset.customerPhone =
            (message.customer && message.customer.phone) || "";
        div.dataset.branchName =
            message.branch_name || (message.branch ? message.branch.name : "");
        // Tạo preview tin nhắn giới hạn 30 ký tự
        const preview =
            (message.message || "...").length > 30
                ? (message.message || "...").substring(0, 30) + "..."
                : message.message || "...";
        // Badge trạng thái
        let statusLabel = "";
        switch (message.status) {
            case "distributed":
                statusLabel = "Đã phân phối";
                break;
            case "active":
                statusLabel = "Đang xử lý";
                break;
            case "closed":
                statusLabel = "Đã đóng";
                break;
            case "resolved":
                statusLabel = "Đã giải quyết";
                break;
            default:
                statusLabel = "Chờ phản hồi";
        }
        div.innerHTML = `
            <div class="chat-item-header">
                <span class="chat-item-name">${div.dataset.customerName}</span>
            </div>
            <div class="chat-item-preview">${preview}</div>
            <span class="chat-item-time">${
                message.created_at ? this.formatTime(message.created_at) : ""
            }</span>
            <div class="chat-item-footer mt-2">
                <div class="chat-item-badges">
                    <span class="badge badge-distributed">${this.getStatusText(
                        message.status
                    )}</span>
                    ${
                        div.dataset.branchName
                            ? `<span class="badge" style="background:#374151;color:#fff;">${div.dataset.branchName}</span>`
                            : ""
                    }
                </div>
                
            </div>
        `;
        div.addEventListener("click", () => {
            this.switchConversation(message.conversation_id, div);
        });
        return div;
    }

    updateInfoPanel(chatItem) {
        const customerName = chatItem.dataset.customerName || "Khách hàng";
        const customerEmail = chatItem.dataset.customerEmail || "";
        const customerPhone = chatItem.dataset.customerPhone || "---";
        const branchName = chatItem.dataset.branchName || "";
        const status = chatItem.dataset.status || "new";
        const firstLetter = customerName.charAt(0).toUpperCase();
        const conversationId = chatItem.dataset.conversationId;

        const avatar = document.getElementById("customer-info-avatar");
        const name = document.getElementById("customer-info-name");
        const email = document.getElementById("customer-info-email");
        const phone = document.getElementById("customer-info-phone");
        const statusSpan = document.getElementById("customer-info-status");
        const branchBadge = document.getElementById(
            "customer-info-branch-badge"
        );

        if (avatar) avatar.textContent = firstLetter;
        if (name) name.textContent = customerName;
        if (email) email.textContent = customerEmail;
        if (phone) phone.textContent = "SĐT: " + customerPhone;
        if (statusSpan) {
            if (status === "distributed" || status === "active") {
                statusSpan.textContent = "Đã phân phối";
            } else if (status === "new") {
                statusSpan.textContent = "Chờ phản hồi";
            } else if (status === "closed") {
                statusSpan.textContent = "Đã đóng";
            } else {
                statusSpan.textContent = status;
            }
        }
        if (branchBadge) {
            if (branchName) {
                branchBadge.textContent = branchName;
                branchBadge.style.display = "";
            } else {
                branchBadge.style.display = "none";
            }
        }

        // Xử lý select phân phối
        const selectContainer = document.getElementById(
            "distribution-select-container"
        );
        if (selectContainer) {
            selectContainer.innerHTML = "";
            if (
                !branchName &&
                status === "new" &&
                window.branches &&
                window.branches.length
            ) {
                const select = document.createElement("select");
                select.className =
                    "distribution-select form-select w-full max-w-xs";
                select.id = "distribution-select";
                select.dataset.conversationId = conversationId;
                select.innerHTML = `<option value="" disabled selected>Chọn chi nhánh</option>`;
                window.branches.forEach((branch) => {
                    select.innerHTML += `<option value="${branch.id}">${branch.name}</option>`;
                });
                select.addEventListener("change", (e) => {
                    const branchId = e.target.value;
                    if (branchId) {
                        this.distributeConversation(conversationId, branchId);
                    }
                });
                selectContainer.appendChild(select);
            }
        }
    }
}

// Export cho global use
window.ChatCommon = ChatCommon;

// Khởi tạo chat admin khi trang đã load
document.addEventListener("DOMContentLoaded", function () {
    const chatContainer = document.getElementById("chat-container");
    if (chatContainer) {
        // Đảm bảo chỉ có 1 instance ChatCommon toàn cục
        if (!window.adminChat || !(window.adminChat instanceof ChatCommon)) {
            window.adminChat = new ChatCommon({
                conversationId: chatContainer.dataset.conversationId,
                userId: chatContainer.dataset.userId,
                userType: "admin",
                api: {
                    send: "/admin/chat/send",
                    getMessages: "/admin/chat/messages/:conversation",
                    distribute: "/admin/chat/distribute",
                },
            });
        }
        // Không khởi tạo lại nếu đã có instance
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
        if (!options || !options.conversationId || !options.userId) {
            throw new Error(
                "Thiếu thông tin cần thiết: conversationId và userId"
            );
        }
        this.conversationId = options.conversationId;
        this.userId = options.userId;
        this.userType = options.userType || "branch";
        this.api = options.api || {};
        // Khởi tạo các DOM elements
        this.messageContainer = document.getElementById("chat-messages");
        this.messageInput = document.getElementById("chat-input-message");
        this.sendBtn = document.getElementById("chat-send-btn");
        this.fileInput = document.getElementById("chat-input-file");
        this.imageInput = document.getElementById("chat-input-image");
        this.chatContainer = document.getElementById("chat-container");
        this.chatList = document.getElementById("chat-list");
        // Khởi tạo Pusher đúng key branch
        if (window.PUSHER_APP_KEY && window.PUSHER_APP_CLUSTER) {
            this.pusher = new Pusher(window.PUSHER_APP_KEY, {
                cluster: window.PUSHER_APP_CLUSTER,
                encrypted: true,
            });
        } else {
            alert("Pusher key/cluster missing! Branch chat sẽ không realtime.");
            this.pusher = null;
        }
        this.init();
        this.setupPusherGlobalListeners();
    }

    setupPusherGlobalListeners() {
        if (window.PUSHER_APP_KEY && window.PUSHER_APP_CLUSTER) {
            if (!window._sidebarPusher) {
                window._sidebarPusher = new Pusher(window.PUSHER_APP_KEY, {
                    cluster: window.PUSHER_APP_CLUSTER,
                    encrypted: true,
                    authEndpoint: "/broadcasting/auth",
                    auth: {
                        headers: {
                            "X-CSRF-TOKEN": document.querySelector(
                                'meta[name="csrf-token"]'
                            ).content,
                        },
                    },
                });
            }
        } else if (window.pusherKey && window.pusherCluster) {
            if (!window._sidebarPusher) {
                window._sidebarPusher = new Pusher(window.pusherKey, {
                    cluster: window.pusherCluster,
                    encrypted: true,
                    authEndpoint: "/broadcasting/auth",
                    auth: {
                        headers: {
                            "X-CSRF-TOKEN": document.querySelector(
                                'meta[name="csrf-token"]'
                            ).content,
                        },
                    },
                });
            }
        } else {
            return;
        }
        const pusher = window._sidebarPusher;
        const branchId = this.chatContainer?.dataset.branchId || this.userId;
        const branchChannel = pusher.subscribe(
            `branch.${branchId}.conversations`
        );
        branchChannel.bind("conversation.updated", async (data) => {
            if (data.update_type === "created") {
                // Khi nhận conversation mới, fetch lại chi tiết và cập nhật UI
                await this.loadConversation(data.conversation.id);
            }
        });
        // LẮNG NGHE TIN NHẮN MỚI Ở CẤP SIDEBAR (BRANCH)
        branchChannel.bind("new-message", (data) => {
            if (data.message) {
                this.updateSidebarPreview(data.message);
            }
        });
        // Đăng ký thành công
        branchChannel.bind("pusher:subscription_succeeded", () => {});
        // Lỗi subscribe
        branchChannel.bind("pusher:subscription_error", (err) => {});
    }

    init() {
        try {
            window.chatInstance = this;
            this.setupEventListeners();
            this.setupPusherChannels();
            this.loadMessages();
        } catch (error) {}
    }

    setupEventListeners() {
        // Xử lý input tin nhắn
        if (this.messageInput) {
            this.messageInput.addEventListener("input", () => {
                this.sendTypingIndicator(true);
                if (this.typingTimeout) clearTimeout(this.typingTimeout);
                this.typingTimeout = setTimeout(() => {
                    this.sendTypingIndicator(false);
                }, 2000);
            });
            this.messageInput.addEventListener("keypress", (e) => {
                if (e.key === "Enter" && !e.shiftKey) {
                    e.preventDefault();
                    if (this.messageInput.value.trim()) {
                        this.sendMessage();
                    }
                }
            });
            this.messageInput.addEventListener("blur", () => {
                this.sendTypingIndicator(false);
            });
        }

        // Xử lý nút gửi tin nhắn
        if (this.sendBtn) {
            this.sendBtn.addEventListener("click", () => {
                this.sendMessage();
            });
        }

        // Xử lý đính kèm file
        if (this.fileInput) {
            this.fileInput.addEventListener("change", (e) => {
                if (e.target.files.length) {
                    this.sendAttachment("file", e.target.files[0]);
                }
            });
        }

        // Xử lý đính kèm ảnh
        if (this.imageInput) {
            this.imageInput.addEventListener("change", (e) => {
                if (e.target.files.length) {
                    if (this.isSendingAttachment) return;
                    this.isSendingAttachment = true;
                    this.sendAttachment("image", e.target.files[0]).finally(
                        () => {
                            this.isSendingAttachment = false;
                            this.imageInput.value = "";
                        }
                    );
                }
            });
        }

        // Xử lý click vào cuộc trò chuyện
        document.querySelectorAll(".conversation-item").forEach((item) => {
            item.addEventListener("click", () => {
                const conversationId = item.dataset.conversationId;
                if (conversationId) {
                    this.switchConversation(conversationId, item);
                }
            });
        });

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
        // Lọc trạng thái
        const statusFilter = document.getElementById("chat-status-filter");
        if (statusFilter) {
            statusFilter.addEventListener("change", (e) => {
                const value = e.target.value;
                document.querySelectorAll(".chat-item").forEach((item) => {
                    // Hiển thị tất cả nếu chọn 'all'
                    if (value === "all") {
                        item.style.display = "";
                    } else if (item.dataset.status === value) {
                        item.style.display = "";
                    } else {
                        item.style.display = "none";
                    }
                });
            });
        }
        // Nút refresh
        const refreshBtn = document.getElementById("refresh-chat-list");
        if (refreshBtn) {
            refreshBtn.addEventListener("click", () => {
                location.reload();
            });
        }

        // Nút gửi ảnh
        const attachImageBtn = document.getElementById("attachImageBtn");
        const imageInput = document.getElementById("chat-input-image");
        if (attachImageBtn && imageInput) {
            attachImageBtn.addEventListener("click", () => imageInput.click());
        }

        // Nút gửi file
        const attachFileBtn = document.getElementById("attachFileBtn");
        const fileInput = document.getElementById("chat-input-file");
        if (attachFileBtn && fileInput) {
            attachFileBtn.addEventListener("click", () => fileInput.click());
        }
    }

    setupPusherChannels() {
        if (!this.pusher) {
            return;
        }
        // Unsubscribe channel cũ nếu có
        if (this.currentChannel) {
            this.pusher.unsubscribe(`chat.${this.currentChannel}`);
        }
        this.currentChannel = this.conversationId;

        const channel = this.pusher.subscribe(`chat.${this.conversationId}`);
        channel.bind("new-message", (data) => {
            if (data.message) {
                // Xóa message tạm thời nếu có
                const tempMsg = this.messageContainer.querySelector(
                    '[data-message-id^="temp-"]'
                );
                if (tempMsg) tempMsg.remove();
                // Cập nhật preview trong sidebar và di chuyển lên đầu
                this.updateSidebarPreview(data.message);
                // Nếu đang ở cuộc trò chuyện này thì append message ngay lập tức
                if (
                    String(this.conversationId) ===
                    String(data.message.conversation_id)
                ) {
                    // Kiểm tra xem tin nhắn đã tồn tại chưa
                    const existingMessage = document.querySelector(
                        `[data-message-id="${data.message.id}"]`
                    );
                    if (!existingMessage) {
                        this.appendMessage(data.message);
                        this.scrollToBottom();
                    }
                }
                // Luôn update preview chat-item
                this.updateSidebarPreview(data.message);
            }
        });
        channel.bind("conversation.updated", (data) => {
            if (data.update_type === "created") {
                // Nếu có last_message thì dùng, không thì tạo message giả từ thông tin conversation
                let sidebarMsg = data.last_message
                    ? {
                          ...data.last_message,
                          conversation_id: data.conversation.id,
                          status: data.conversation.status,
                          customer: data.conversation.customer,
                          branch_id: data.conversation.branch_id,
                      }
                    : {
                          conversation_id: data.conversation.id,
                          status: data.conversation.status,
                          customer: data.conversation.customer,
                          branch_id: data.conversation.branch_id,
                          message: "",
                          sender: data.conversation.customer
                              ? {
                                    full_name:
                                        data.conversation.customer.full_name,
                                }
                              : { full_name: "Khách hàng" },
                          sender_id: data.conversation.customer
                              ? data.conversation.customer.id
                              : "",
                          created_at: data.conversation.updated_at,
                      };
                this.updateSidebarPreview(sidebarMsg);
                this.showNotification("Có cuộc trò chuyện mới!", "info");
            } else if (data.last_message) {
                this.updateSidebarPreview({
                    ...data.last_message,
                    conversation_id: data.conversation.id,
                    status: data.conversation.status,
                    customer: data.conversation.customer,
                    branch_id: data.conversation.branch_id,
                });
                this.updateInfoPanel(chatItem);
            }
            // ... giữ nguyên các xử lý khác ...
        });
        // ... giữ nguyên các event khác ...
        // Bổ sung bind typing
        channel.bind("user.typing", (data) => {
            if (
                Number(data.user_id) !== Number(this.userId) &&
                String(data.conversation_id) === String(this.conversationId)
            ) {
                if (data.is_typing) {
                    this.showTypingIndicator(data.user_name);
                } else {
                    this.hideTypingIndicator();
                }
            }
        });
    }

    updateSidebarPreview(message) {
        if (!this.chatList) return;
        let chatItem = document.querySelector(
            `.chat-item[data-conversation-id="${message.conversation_id}"]`
        );
        const isNew = !chatItem;
        if (!chatItem) {
            chatItem = this.createSidebarChatItem(message);
            this.chatList.insertBefore(chatItem, this.chatList.firstChild);
        } else {
            // Cập nhật preview tin nhắn (giới hạn 30 ký tự)
            const previewElement = chatItem.querySelector(".chat-item-preview");
            if (previewElement) {
                const preview =
                    (message.message || "...").length > 30
                        ? (message.message || "...").substring(0, 30) + "..."
                        : message.message || "...";
                previewElement.textContent = preview;
            }
            // Cập nhật thời gian
            const timeElement = chatItem.querySelector(".chat-item-time");
            if (timeElement && message.created_at) {
                timeElement.textContent = this.formatTime(message.created_at);
            }
            // Cập nhật badge trạng thái
            const badges = chatItem.querySelector(".chat-item-badges");
            if (badges) {
                let statusLabel = "";
                switch (message.status) {
                    case "distributed":
                        statusLabel = "Đã phân phối";
                        break;
                    case "active":
                        statusLabel = "Đang xử lý";
                        break;
                    case "closed":
                        statusLabel = "Đã đóng";
                        break;
                    case "resolved":
                        statusLabel = "Đã giải quyết";
                        break;
                    default:
                        statusLabel = "Chờ phản hồi";
                }
                badges.innerHTML =
                    `<span class="badge badge-distributed">${this.getStatusText(
                        message.status
                    )}</span>` +
                    (chatItem.dataset.branchName
                        ? `<span class="badge" style="background:#374151;color:#fff;">${chatItem.dataset.branchName}</span>`
                        : "");
            }

            // Luôn di chuyển lên đầu sidebar
            chatItem.remove();
            this.chatList.insertBefore(chatItem, this.chatList.firstChild);
        }
        // Gắn lại event click nếu cần
        chatItem.onclick = () => {
            this.switchConversation(message.conversation_id, chatItem);
        };
        // Thêm hiệu ứng highlight khi có tin nhắn mới hoặc vừa tạo mới
        chatItem.classList.add("highlight-new");
        setTimeout(() => {
            chatItem.classList.remove("highlight-new");
        }, 2000);
    }

    appendMessage(message) {
        if (!this.messageContainer) return;
        // Xóa message tạm thời nếu có (khi append message thật)
        if (message.id && !String(message.id).startsWith("temp-")) {
            const tempMsg = this.messageContainer.querySelector(
                '[data-message-id^="temp-"]'
            );
            if (tempMsg) tempMsg.remove();
        }
        const isAdmin = String(message.sender_id) === String(this.userId);
        const senderName =
            message.sender && message.sender.full_name
                ? message.sender.full_name
                : isAdmin
                ? "Nhân viên chi nhánh"
                : "Khách hàng";
        const avatarLetter = senderName.charAt(0).toUpperCase();
        let attachmentHtml = "";
        if (message.attachment) {
            if (message.attachment_type === "image") {
                attachmentHtml = `<img src="${
                    message.attachment.startsWith("blob:")
                        ? message.attachment
                        : "/storage/" + message.attachment
                }" class="mt-2 rounded-lg max-h-40 cursor-pointer" onclick="window.open('${
                    message.attachment.startsWith("blob:")
                        ? message.attachment
                        : "/storage/" + message.attachment
                }','_blank')">`;
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
        msgDiv.dataset.messageId = message.id;
        msgDiv.dataset.senderId = message.sender_id; // <-- DÒNG NÀY LÀ BẮT BUỘC
        msgDiv.innerHTML = `
            <div class="flex gap-2 max-w-[80%] ${
                isAdmin ? "flex-row-reverse" : "flex-row"
            }">
                <div class="w-8 h-8 ${
                    isAdmin ? "bg-blue-500" : "bg-orange-500"
                } rounded-full mt-5 flex items-center justify-center flex-shrink-0">
                    <span class="text-white text-xs font-bold">${avatarLetter}</span>
                </div>
                <div class="flex flex-col ${
                    isAdmin ? "items-end" : "items-start"
                }">
                    <div class="text-xs  text-gray-500 mb-1">${senderName}</div>
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

        // Thêm hiệu ứng fade-in cho tin nhắn mới
        msgDiv.style.opacity = "0";
        msgDiv.style.transition = "opacity 0.3s ease-in-out";
        this.messageContainer.appendChild(msgDiv);

        // Trigger hiệu ứng fade-in
        setTimeout(() => {
            msgDiv.style.opacity = "1";
        }, 50);

        this.scrollToBottom();
    }

    async loadMessages() {
        if (!this.conversationId) return;

        try {
            const url = this.api.getMessages;
            const response = await fetch(url);
            const data = await response.json();
            if (data.success) {
                this.messageContainer.innerHTML = "";
                data.messages.forEach((message) => {
                    this.appendMessage(message);
                });
                this.scrollToBottom();
            }
        } catch (error) {
            this.showError("Không thể tải tin nhắn");
        }
    }

    async sendMessage() {
        if (!this.messageInput) {
            return;
        }
        const message = this.messageInput.value.trim();
        if (
            !message &&
            !(this.fileInput?.files?.length || this.imageInput?.files?.length)
        ) {
            this.showError("Bạn phải nhập nội dung hoặc đính kèm file/ảnh!");
            return false; // Không gửi, không reload
        }

        if (!message) {
            return;
        }
        // Xóa nội dung input sau khi đã lấy giá trị
        this.messageInput.value = "";
        if (this.sendBtn) this.sendBtn.disabled = true;
        // Hiển thị tin nhắn tạm thời nếu là text (không file/ảnh)
        if (!this.fileInput?.files?.length && !this.imageInput?.files?.length) {
            const tempId = "temp-" + Date.now();
            this.appendMessage({
                id: tempId,
                message: message,
                sender_id: this.userId,
                sender: { full_name: "Nhân viên chi nhánh" },
                created_at: new Date().toISOString(),
                isTemp: true,
            });
            this.scrollToBottom();
        }
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
                // Nếu là gửi file/ảnh thì appendMessage ở đây
                if (
                    data.data &&
                    (this.fileInput?.files?.length ||
                        this.imageInput?.files?.length)
                ) {
                    this.appendMessage({
                        ...data.data,
                        sender_id: this.userId,
                        sender: { full_name: "Nhân viên chi nhánh" },
                        created_at: new Date().toISOString(),
                    });
                    this.scrollToBottom();
                }
                // Cập nhật preview sidebar
                this.updateSidebarPreview({
                    ...data.message,
                    message: message,
                    created_at: new Date().toISOString(),
                    conversation_id: this.conversationId,
                });
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
                this.messageInput.value = ""; // Luôn xóa nội dung input
                this.messageInput.focus();
                this.messageInput.style.height = "48px";
            }
        }
    }

    async sendAttachment(type, file) {
        if (!file) return;
        // Tạo message tạm thời
        const tempId = "temp-" + Date.now();
        let tempMsg = {
            id: tempId,
            message: type === "image" ? "Đã gửi ảnh" : "Đã gửi file",
            attachment: type === "image" ? URL.createObjectURL(file) : null,
            attachment_type: type,
            sender_id: this.userId,
            sender: { full_name: "Nhân viên chi nhánh" },
            created_at: new Date().toISOString(),
            isTemp: true,
        };
        this.appendMessage(tempMsg);
        this.scrollToBottom();
        try {
            const formData = new FormData();
            formData.append("attachment", file);
            formData.append("conversation_id", this.conversationId);
            formData.append("attachment_type", type);

            const response = await fetch(this.api.send, {
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
                this.appendMessage({
                    ...data.data,
                    sender_id: this.userId,
                    sender: {
                        full_name: "Nhân viên chi nhánh",
                        name: "Nhân viên chi nhánh",
                    },
                    created_at: new Date().toISOString(),
                });
                this.scrollToBottom();
            } else {
                throw new Error(data.message || "Gửi file thất bại");
            }
        } catch (error) {
            this.showError("Không thể gửi file");
        } finally {
            if (this.messageInput) this.messageInput.value = ""; // Xóa input sau khi gửi file/ảnh
        }
    }

    async switchConversation(conversationId, chatItem) {
        // Cập nhật trạng thái active
        document.querySelectorAll(".chat-item").forEach((item) => {
            item.classList.remove("active");
        });
        chatItem.classList.add("active");

        // Unsubscribe channel cũ nếu có
        if (this.currentChannel) {
            this.pusher.unsubscribe(`chat.${this.currentChannel}`);
        }
        this.currentChannel = conversationId;

        // Cập nhật conversation ID
        this.conversationId = conversationId;
        if (this.chatContainer) {
            this.chatContainer.dataset.conversationId = conversationId;
        }

        // Cập nhật thông tin header
        const customerName = chatItem.dataset.customerName;
        const customerEmail = chatItem.dataset.customerEmail;
        const firstLetter = customerName.charAt(0).toUpperCase();

        const avatar = document.getElementById("chat-header-avatar");
        const name = document.getElementById("chat-header-name");
        const email = document.getElementById("chat-header-email");

        if (avatar) avatar.textContent = firstLetter;
        if (name) name.textContent = customerName;
        if (email) email.textContent = customerEmail;

        // Cập nhật API URL
        this.api.getMessages = `/branch/chat/api/conversation/${conversationId}`;

        this.loadMessages();
        this.setupPusherChannels();

        const customerPhone = chatItem.dataset.customerPhone;
        const infoPhone = document.getElementById("customer-info-phone");
        if (infoPhone)
            infoPhone.textContent = "SĐT: " + (customerPhone || "---");
        // Cập nhật info panel bên phải: số điện thoại và trạng thái
        const status = chatItem.dataset.status;
        const infoStatus = document.getElementById("chat-info-status");
        if (infoStatus) {
            if (status === "distributed" || status === "active") {
                infoStatus.textContent = "Đã phân phối";
            } else if (status === "new") {
                infoStatus.textContent = "Chờ phản hồi";
            } else if (status === "resolved") {
                infoStatus.textContent = "Đã giải quyết";
            } else if (status === "closed") {
                infoStatus.textContent = "Đã đóng";
            } else {
                infoStatus.textContent = status;
            }
        }
        this.updateInfoPanel(chatItem);
        // Hiển thị/ẩn select phân công chi nhánh
        const distributionContainer = document.getElementById(
            "distribution-select-container"
        );
        if (distributionContainer) {
            if (
                !branchName &&
                status === "new" &&
                window.branches &&
                window.branches.length
            ) {
                // Hiển thị select
                let select = document.createElement("select");
                select.className =
                    "distribution-select form-select w-full max-w-xs";
                select.id = "distribution-select";
                select.dataset.conversationId = conversationId;
                select.innerHTML = `<option value="" disabled selected>Chọn chi nhánh</option>`;
                window.branches.forEach((branch) => {
                    select.innerHTML += `<option value="${branch.id}">${branch.name}</option>`;
                });
                select.addEventListener("change", (e) => {
                    const branchId = e.target.value;
                    if (branchId) {
                        this.distributeConversation(conversationId, branchId);
                    }
                });
                distributionContainer.innerHTML = "";
                distributionContainer.appendChild(select);
            } else {
                // Ẩn select
                distributionContainer.innerHTML = "";
            }
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

    updateConversationStatus(status) {
        const statusBadge = document.querySelector(".status-badge");
        if (statusBadge) {
            statusBadge.textContent = this.getStatusText(status);
            statusBadge.className = `badge status-badge status-${status}`;
        }
    }

    getStatusText(status) {
        const statusTexts = {
            new: "Chờ phản hồi",
            distributed: "Đã phân phối",
            active: "Đang xử lý",
            resolved: "Đã giải quyết",
            closed: "Đã đóng",
        };
        return statusTexts[status] || status;
    }

    async loadConversation(conversationId) {
        try {
            const response = await fetch(
                `/branch/chat/api/conversation/${conversationId}`
            );
            const data = await response.json();

            if (data.success) {
                // Cập nhật header
                const customer = data.conversation.customer || {};
                const headerName = document.getElementById("chat-header-name");
                const headerEmail =
                    document.getElementById("chat-header-email");
                const headerAvatar =
                    document.getElementById("chat-header-avatar");
                if (headerName)
                    headerName.textContent =
                        customer.full_name || customer.name || "Khách hàng";
                if (headerEmail) headerEmail.textContent = customer.email || "";
                if (headerAvatar) {
                    headerAvatar.textContent = (
                        customer.full_name ||
                        customer.name ||
                        "K"
                    )
                        .charAt(0)
                        .toUpperCase();
                    // Đổi màu avatar: cam cho khách, xanh cho nhân viên
                    headerAvatar.style.backgroundColor =
                        data.conversation.status === "distributed"
                            ? "#f59e42"
                            : "#3b82f6";
                    headerAvatar.style.color = "#fff";
                }

                // Cập nhật info panel
                const infoName = document.getElementById("chat-info-name");
                const infoEmail = document.getElementById("chat-info-email");
                const infoAvatar = document.getElementById("chat-info-avatar");
                const infoBranch = document.getElementById("chat-info-branch");
                const infoStatus = document.getElementById("chat-info-status");
                if (infoName)
                    infoName.textContent =
                        customer.full_name || customer.name || "Khách hàng";
                if (infoEmail) infoEmail.textContent = customer.email || "";
                if (infoAvatar) {
                    infoAvatar.textContent = (
                        customer.full_name ||
                        customer.name ||
                        "K"
                    )
                        .charAt(0)
                        .toUpperCase();
                    infoAvatar.style.backgroundColor =
                        data.conversation.status === "distributed"
                            ? "#f59e42"
                            : "#3b82f6";
                    infoAvatar.style.color = "#fff";
                }
                if (infoBranch)
                    infoBranch.textContent = `Chi nhánh: ${
                        data.conversation.branch
                            ? data.conversation.branch.name
                            : ""
                    }`;
                if (infoStatus)
                    infoStatus.textContent = data.conversation.status;

                // Cập nhật last activity
                const lastActivity = document.getElementById(
                    "chat-info-last-activity"
                );
                if (lastActivity && data.conversation.updated_at_human) {
                    lastActivity.textContent =
                        data.conversation.updated_at_human;
                }

                // Cập nhật branch badge, trạng thái badge
                const mainBranchBadge =
                    document.getElementById("main-branch-badge");
                if (mainBranchBadge) {
                    if (data.conversation.branch) {
                        mainBranchBadge.textContent =
                            data.conversation.branch.name;
                        mainBranchBadge.style.display = "";
                    } else {
                        mainBranchBadge.style.display = "none";
                    }
                }
                const statusBadge = document.querySelector(".status-badge");
                if (statusBadge) {
                    statusBadge.textContent = this.getStatusText(
                        data.conversation.status
                    );
                    statusBadge.className = `badge status-badge status-${data.conversation.status}`;
                }

                // Render lại nút action
                this.renderActionButtons(data.conversation);

                // Cập nhật tin nhắn
                this.messageContainer.innerHTML = "";
                data.messages.forEach((message) => {
                    this.appendMessage(message);
                });
                this.scrollToBottom();
                this.updateConversationStatus(data.conversation.status);

                // Cập nhật info panel bên phải: số điện thoại và trạng thái
                const infoPhone = document.getElementById("chat-info-phone");
                if (infoPhone)
                    infoPhone.textContent =
                        "SĐT: " + (data.conversation.customer?.phone || "---");
            }
        } catch (error) {
            this.showError(
                "Không thể tải cuộc trò chuyện. Vui lòng thử lại sau."
            );
        }
    }

    // Thêm hàm tạo chat-item cho admin
    createSidebarChatItem(message) {
        const div = document.createElement("div");
        div.className = "chat-item conversation-item";
        div.dataset.conversationId = message.conversation_id;
        div.dataset.status = message.status || "new";
        div.dataset.customerName =
            (message.customer &&
                (message.customer.full_name || message.customer.name)) ||
            (message.sender && message.sender.full_name) ||
            "Khách hàng";
        div.dataset.customerEmail =
            (message.customer && message.customer.email) || "";
        div.dataset.customerPhone =
            (message.customer && message.customer.phone) || "";
        div.dataset.branchName =
            message.branch_name || (message.branch ? message.branch.name : "");
        // Tạo preview tin nhắn giới hạn 30 ký tự
        const preview =
            (message.message || "...").length > 30
                ? (message.message || "...").substring(0, 30) + "..."
                : message.message || "...";
        // Badge trạng thái
        let statusLabel = "";
        switch (message.status) {
            case "distributed":
                statusLabel = "Đã phân phối";
                break;
            case "active":
                statusLabel = "Đang xử lý";
                break;
            case "closed":
                statusLabel = "Đã đóng";
                break;
            case "resolved":
                statusLabel = "Đã giải quyết";
                break;
            default:
                statusLabel = "Chờ phản hồi";
        }
        div.innerHTML = `
            <div class="chat-item-header">
                <span class="chat-item-name">${div.dataset.customerName}</span>
            </div>
            <div class="chat-item-preview">${preview}</div>
            <span class="chat-item-time">${
                message.created_at ? this.formatTime(message.created_at) : ""
            }</span>
            <div class="chat-item-footer mt-2">
                <div class="chat-item-badges">
                    <span class="badge badge-distributed">${this.getStatusText(
                        message.status
                    )}</span>
                    ${
                        div.dataset.branchName
                            ? `<span class="badge" style="background:#374151;color:#fff;">${div.dataset.branchName}</span>`
                            : ""
                    }
                </div>
                
            </div>
        `;
        div.addEventListener("click", () => {
            this.switchConversation(message.conversation_id, div);
        });
        return div;
    }

    async sendTypingIndicator(isTyping) {
        try {
            await fetch("/branch/chat/typing", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector(
                        'meta[name="csrf-token"]'
                    ).content,
                    "X-Requested-With": "XMLHttpRequest",
                },
                body: JSON.stringify({
                    conversation_id: this.conversationId,
                    is_typing: isTyping,
                }),
            });
        } catch (e) {
            // silent
        }
    }

    showTypingIndicator(userName) {
        let typingDiv = document.getElementById("branch-typing-indicator");
        if (!typingDiv) {
            typingDiv = document.createElement("div");
            typingDiv.id = "branch-typing-indicator";
            typingDiv.className = "typing-indicator";
            typingDiv.innerHTML = `<span class=\"typing-text\">${userName} đang nhập</span><span class=\"dot\"></span><span class=\"dot\"></span><span class=\"dot\"></span>`;
        }
        const msg = document.getElementById("chat-messages");
        if (msg) {
            const old = document.getElementById("branch-typing-indicator");
            if (old && old.parentNode) old.parentNode.removeChild(old);
            msg.appendChild(typingDiv);
            if (typeof this.scrollToBottom === "function")
                this.scrollToBottom();
        }
    }

    hideTypingIndicator() {
        const typingDiv = document.getElementById("branch-typing-indicator");
        if (typingDiv && typingDiv.parentNode)
            typingDiv.parentNode.removeChild(typingDiv);
    }

    // Thêm 2 method cập nhật UI
    updateHeaderAndInfoPanel(conversation) {
        if (!conversation) return;
        const customer = conversation.customer || {};
        const headerName = document.getElementById("chat-header-name");
        const headerEmail = document.getElementById("chat-header-email");
        const headerAvatar = document.getElementById("chat-header-avatar");
        const infoName = document.getElementById("chat-info-name");
        const infoEmail = document.getElementById("chat-info-email");
        const infoAvatar = document.getElementById("chat-info-avatar");
        const infoBranch = document.getElementById("chat-info-branch");
        const infoStatus = document.getElementById("chat-info-status");
        if (headerName)
            headerName.textContent =
                customer.full_name || customer.name || "Khách hàng";
        if (headerEmail) headerEmail.textContent = customer.email || "";
        if (headerAvatar)
            headerAvatar.textContent = (
                customer.full_name ||
                customer.name ||
                "K"
            )
                .charAt(0)
                .toUpperCase();
        if (infoName)
            infoName.textContent =
                customer.full_name || customer.name || "Khách hàng";
        if (infoEmail) infoEmail.textContent = customer.email || "";
        if (infoAvatar)
            infoAvatar.textContent = (
                customer.full_name ||
                customer.name ||
                "K"
            )
                .charAt(0)
                .toUpperCase();
        if (infoBranch)
            infoBranch.textContent = `Chi nhánh: ${
                conversation.branch ? conversation.branch.name : ""
            }`;
        if (infoStatus) infoStatus.textContent = conversation.status;
    }

    renderActionButtons(conversation) {
        const container = document.querySelector(
            ".chat-info-panel .flex.flex-col.items-end"
        );
        if (!container) return;
        container.innerHTML = "";
        if (conversation.status === "distributed") {
            container.innerHTML = `<button id="btn-activate-conversation" class="flex items-center gap-2 px-4 py-2 text-xs font-medium text-blue-700 bg-blue-100 rounded-full hover:bg-blue-200 transition"><i class="fas fa-play-circle text-blue-600"></i> Kích hoạt</button>`;
        } else if (conversation.status === "active") {
            container.innerHTML = `<button id="btn-resolve-conversation" class="flex items-center gap-2 px-4 py-2 text-xs font-medium text-green-700 bg-green-100 rounded-full hover:bg-green-200 transition"><i class="fas fa-check-circle text-green-600"></i> Đã giải quyết</button><button id="btn-close-conversation" class="flex items-center gap-2 px-4 py-2 text-xs font-medium text-gray-700 bg-gray-200 rounded-full hover:bg-gray-300 transition"><i class="fas fa-times-circle text-gray-600"></i> Đóng</button>`;
        } else if (conversation.status === "resolved") {
            container.innerHTML = `<button id="btn-close-conversation" class="flex items-center gap-2 px-4 py-2 text-xs font-medium text-gray-700 bg-gray-200 rounded-full hover:bg-gray-300 transition"><i class="fas fa-times-circle text-gray-600"></i> Đóng</button>`;
        }
        // Gắn lại event cho các nút nếu cần
    }
}

window.BranchChat = BranchChat;
// ... existing code ...

class CustomerChatRealtime {
    constructor(options) {
        if (!options || !options.conversationId || !options.userId) {
            throw new Error("Thiếu thông tin: conversationId và userId");
        }
        this.conversationId = options.conversationId;
        this.userId = options.userId;
        this.api = options.api || {};
        this.appendMessage = options.appendMessage || function () {};
        if (window.PUSHER_APP_KEY && window.PUSHER_APP_CLUSTER) {
            this.pusher = new Pusher(window.PUSHER_APP_KEY, {
                cluster: window.PUSHER_APP_CLUSTER,
                encrypted: true,
            });
        } else {
            this.pusher = null;
        }
        this.pusher = new Pusher(window.pusherKey, {
            cluster: window.pusherCluster,
            encrypted: true,
        });
        this.init();
    }

    init() {
        this.setupPusherChannel();
    }

    setupPusherChannel() {
        try {
            const channel = this.pusher.subscribe(
                `chat.${this.conversationId}`
            );
            channel.bind("pusher:subscription_succeeded", () => {});
            channel.bind("pusher:subscription_error", (err) => {});
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
        } catch (e) {}
    }

    showTypingIndicator(userName) {
        let typingDiv = document.getElementById("customer-typing-indicator");
        const msgContainer = document.getElementById("messagesContainer");
        if (!typingDiv) {
            typingDiv = document.createElement("div");
            typingDiv.id = "customer-typing-indicator";
            typingDiv.className = "typing-indicator";
            typingDiv.innerHTML = `<span class=\"typing-text\">${userName} đang nhập</span><span class=\"dot\"></span><span class=\"dot\"></span><span class=\"dot\"></span>`;
        }
        if (msgContainer) {
            // Remove old
            const old = document.getElementById("customer-typing-indicator");
            if (old && old.parentNode) old.parentNode.removeChild(old);
            msgContainer.appendChild(typingDiv);
            msgContainer.scrollTop = msgContainer.scrollHeight;
        }
    }

    hideTypingIndicator() {
        const typingDiv = document.getElementById("customer-typing-indicator");
        if (typingDiv && typingDiv.parentNode)
            typingDiv.parentNode.removeChild(typingDiv);
    }

    async sendTypingIndicator(isTyping) {
        try {
            await fetch("/customer/chat/typing", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector(
                        'meta[name="csrf-token"]'
                    ).content,
                    "X-Requested-With": "XMLHttpRequest",
                },
                body: JSON.stringify({
                    conversation_id: this.conversationId,
                    is_typing: isTyping,
                }),
            });
        } catch (e) {}
    }
}

window.CustomerChatRealtime = CustomerChatRealtime;
// ... existing code ...

document.addEventListener("DOMContentLoaded", function () {
    // ... existing code ...
    // BranchChat instance đã được khởi tạo ở trên
    // Thêm event cho các nút trạng thái
    const btnActivate = document.getElementById("btn-activate-conversation");
    const btnResolve = document.getElementById("btn-resolve-conversation");
    const btnClose = document.getElementById("btn-close-conversation");
    function getCurrentConversationId() {
        // Lấy id từ item đang active trong sidebar
        const activeItem = document.querySelector(".conversation-item.active");
        return activeItem
            ? activeItem.getAttribute("data-conversation-id")
            : null;
    }
    function updateStatus(status) {
        const conversationId = getCurrentConversationId();
        if (!conversationId) return;
        fetch("/branch/chat/update-status", {
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
                status: status,
            }),
        })
            .then((res) => res.json())
            .then((data) => {
                if (data.success) {
                    if (status === "active") {
                        // Nếu là kích hoạt thì reload lại trang branch
                        window.location.reload();
                    } else if (window.chatInstance) {
                        window.chatInstance.loadConversation(conversationId);
                    } else {
                        location.reload();
                    }
                } else {
                    alert(data.message || "Có lỗi xảy ra!!");
                }
            });
    }
    if (btnActivate) btnActivate.onclick = () => updateStatus("active");
    if (btnResolve) btnResolve.onclick = () => updateStatus("resolved");
    if (btnClose) btnClose.onclick = () => updateStatus("closed");

    // Quick reply click handler
    document.querySelectorAll(".quick-reply-btn").forEach(function (btn) {
        btn.addEventListener("click", function () {
            var input = document.getElementById("chat-input-message");
            if (input) {
                input.value = btn.textContent;
                input.focus();
            }
        });
    });

    // Xử lý chuyển chi nhánh
    document
        .querySelectorAll(".distribution-select")
        .forEach(function (select) {
            select.addEventListener("change", function (e) {
                var conversationId = select.dataset.conversationId;
                var branchId = select.value;
                if (conversationId && branchId) {
                    fetch("/admin/chat/distribute", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": document.querySelector(
                                'meta[name="csrf-token"]'
                            ).content,
                        },
                        body: JSON.stringify({
                            conversation_id: conversationId,
                            branch_id: branchId,
                        }),
                    })
                        .then((res) => res.json())
                        .then((data) => {
                            if (data.success) {
                                // Reload hoặc cập nhật UI
                                window.location.reload();
                            } else {
                                alert(
                                    data.message ||
                                        "Có lỗi xảy ra khi phân phối!"
                                );
                            }
                        })
                        .catch(() => alert("Có lỗi xảy ra khi phân phối!"));
                }
            });
        });
});
// ... existing code ...

// function showChatNotification(conversationId) {
//     // Hiện badge đỏ ở chuông
//     var bell = document.getElementById("chat-new-msg-badge");
//     if (bell) bell.classList.remove("hidden");
//     // Hiện badge đỏ ở chat-item sidebar
//     var chatItem = document.querySelector(
//         '.chat-item[data-conversation-id="' + conversationId + '"]'
//     );
//     if (chatItem) {
//         var badge = chatItem.querySelector(".unread-badge");
//         if (badge) {
//             badge.classList.remove("hidden");
//             badge.textContent = parseInt(badge.textContent || "0") + 1;
//         } else {
//             var newBadge = document.createElement("span");
//             newBadge.className =
//                 "unread-badge absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white text-xs rounded-full flex items-center justify-center";
//             newBadge.textContent = "1";
//             chatItem.appendChild(newBadge);
//         }
//     }
// }

function updateChatNewMessageNotify(count) {
    // Header dropdown notify
    var notify = document.getElementById("chat-new-message-notify");
    var countSpan = document.getElementById("chat-new-message-count");
    var countSpan2 = document.getElementById("chat-new-message-count-2");
    if (notify && count > 0) {
        notify.classList.remove("hidden");
        if (countSpan) countSpan.textContent = count;
        if (countSpan2) countSpan2.textContent = count;
    } else if (notify) {
        notify.classList.add("hidden");
    }
    // Sidebar badge
    var sidebarBadge = document.getElementById("sidebar-chat-badge");
    if (sidebarBadge) {
        if (count > 0) {
            sidebarBadge.style.display = "inline-flex";
            sidebarBadge.textContent = count;
        } else {
            sidebarBadge.style.display = "none";
            sidebarBadge.textContent = "0";
        }
    }
    // Header badge
    var headerBadge = document.getElementById("header-chat-badge");
    if (headerBadge) {
        if (count > 0) {
            headerBadge.style.display = "flex";
            headerBadge.textContent = count;
        } else {
            headerBadge.style.display = "none";
            headerBadge.textContent = "0";
        }
    }
}
// ... existing code ...
// Giả sử có biến global window.unreadChatCount
// Khi có tin nhắn mới:
window.unreadChatCount = (window.unreadChatCount || 0) + 1;
updateChatNewMessageNotify(window.unreadChatCount);
// Khi đọc hết:
// window.unreadChatCount = 0;
// updateChatNewMessageNotify(0);
// ... existing code ...
