// Hiển thị modal
function dtmodalShowModal(type, options = {}) {
    const modalId =
        "dtmodal" + type.charAt(0).toUpperCase() + type.slice(1) + "Modal";
    const modal = document.getElementById(modalId);

    if (modal) {
        // Cập nhật nội dung modal nếu có options
        if (options.title) {
            const titleElement = modal.querySelector(".dtmodal-title");
            if (titleElement) titleElement.textContent = options.title;
        }

        if (options.subtitle) {
            const subtitleElement = modal.querySelector(".dtmodal-subtitle");
            if (subtitleElement) subtitleElement.textContent = options.subtitle;
        }

        if (options.message) {
            const messageElement = modal.querySelector(".dtmodal-message");
            if (messageElement) messageElement.textContent = options.message;
        }

        if (options.confirmText) {
            const confirmButton = modal.querySelector(".dtmodal-btn-primary");
            if (confirmButton) confirmButton.textContent = options.confirmText;
        }

        if (options.cancelText) {
            const cancelButton = modal.querySelector(".dtmodal-btn-outline");
            if (cancelButton) cancelButton.textContent = options.cancelText;
        }

        // Hiển thị modal
        modal.classList.add("dtmodal-active");
        document.body.classList.add("dtmodal-open");
    } else if (options.createIfNotExists) {
        // Tạo modal động nếu không tìm thấy modal có sẵn
        dtmodalCreateModal({
            type: type,
            title: options.title || "",
            subtitle: options.subtitle || "",
            message: options.message || "",
            confirmText: options.confirmText || "Xác nhận",
            cancelText: options.cancelText || "Hủy bỏ",
            onConfirm: options.onConfirm || null,
            onCancel: options.onCancel || null,
        });
    }
}

// Đóng modal
function dtmodalCloseModal(modalId) {
    const modal = document.getElementById(modalId);

    if (modal) {
        modal.classList.remove("dtmodal-active");
        document.body.classList.remove("dtmodal-open");

        // Xóa modal khỏi DOM nếu là modal động
        if (modal.classList.contains("dtmodal-dynamic")) {
            setTimeout(() => {
                modal.remove();
            }, 300);
        }
    }
}

// Hiển thị toast
function dtmodalShowToast(type, options = {}) {
    const toastContainer = document.getElementById("dtmodalToastContainer");
    if (!toastContainer) {
        const container = document.createElement("div");
        container.id = "dtmodalToastContainer";
        container.className = "dtmodal-toast-container";
        document.body.appendChild(container);
    }

    const toast = document.createElement("div");
    toast.className = `dtmodal-toast dtmodal-toast-${type}`;

    let icon, title, message;

    switch (type) {
        case "success":
            icon = "check-circle";
            title = options.title || "Thành công!";
            message =
                options.message || "Thao tác đã được thực hiện thành công.";
            break;
        case "error":
            icon = "times-circle";
            title = options.title || "Lỗi!";
            message =
                options.message || "Đã xảy ra lỗi khi thực hiện thao tác.";
            break;
        case "warning":
            icon = "exclamation-triangle";
            title = options.title || "Cảnh báo!";
            message = options.message || "Hãy xác nhận trước khi tiếp tục.";
            break;
        case "info":
            icon = "info-circle";
            title = options.title || "Thông tin";
            message = options.message || "Có thông tin quan trọng cần lưu ý.";
            break;
        case "notification":
            icon = "bell";
            title = options.title || "Thông báo mới";
            message = options.message || "Bạn có thông báo mới cần xem.";
            break;
        default:
            icon = "info-circle";
            title = options.title || "Thông báo";
            message = options.message || "Đây là một thông báo.";
    }

    toast.innerHTML = `
        <div class="dtmodal-toast-icon-wrapper">
            <div class="dtmodal-toast-icon">
                <i class="fas fa-${icon}"></i>
            </div>
        </div>
        <div class="dtmodal-toast-content">
            <h4 class="dtmodal-toast-title">${title}</h4>
            <p class="dtmodal-toast-message">${message}</p>
        </div>
        <button class="dtmodal-toast-close" onclick="dtmodalCloseToast(this.parentNode)">
            <i class="fas fa-times"></i>
        </button>
        <div class="dtmodal-toast-progress">
            <div class="dtmodal-toast-progress-bar"></div>
        </div>
    `;

    const toastContainerEl = document.getElementById("dtmodalToastContainer");
    toastContainerEl.appendChild(toast);

    // Hiển thị toast sau khi thêm vào DOM
    setTimeout(() => {
        toast.classList.add("dtmodal-active");
    }, 10);

    // Tự động đóng toast sau 5 giây
    setTimeout(() => {
        dtmodalCloseToast(toast);
    }, 5000);
}

// Đóng toast
function dtmodalCloseToast(toast) {
    toast.classList.remove("dtmodal-active");

    // Xóa toast khỏi DOM sau khi animation kết thúc
    setTimeout(() => {
        if (toast.parentNode) {
            toast.parentNode.removeChild(toast);
        }
    }, 300);
}

// Tạo modal động
function dtmodalCreateModal(options) {
    const {
        type = "info",
            title = "",
            subtitle = "",
            message = "",
            confirmText = "Xác nhận",
            cancelText = "Hủy bỏ",
            onConfirm = null,
            onCancel = null,
    } = options;

    const modalId = "dtmodal" + Math.random().toString(36).substr(2, 9);
    const modal = document.createElement("div");
    modal.className = "dtmodal-overlay dtmodal-active dtmodal-dynamic";
    modal.id = modalId;

    const icons = {
        success: "check-circle",
        error: "times-circle",
        warning: "exclamation-triangle",
        info: "info-circle",
    };

    modal.innerHTML = `
        <div class="dtmodal-container dtmodal-${type}">
            <div class="dtmodal-header">
                <div class="dtmodal-icon-wrapper">
                    <div class="dtmodal-icon">
                        <i class="fas fa-${icons[type]}"></i>
                    </div>
                </div>
                <div class="dtmodal-title-content">
                    <h3 class="dtmodal-title">${title}</h3>
                    ${
                        subtitle
                            ? `<p class="dtmodal-subtitle">${subtitle}</p>`
                            : ""
                    }
                </div>
                <button class="dtmodal-close" onclick="dtmodalCloseModal('${modalId}')">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="dtmodal-body">
                <p class="dtmodal-message">${message}</p>
            </div>
            <div class="dtmodal-footer">
                <button class="dtmodal-btn dtmodal-btn-outline" onclick="dtmodalHandleAction('${modalId}', false)">${cancelText}</button>
                <button class="dtmodal-btn dtmodal-btn-primary" onclick="dtmodalHandleAction('${modalId}', true)">${confirmText}</button>
            </div>
        </div>
    `;

    document.body.appendChild(modal);

    // Lưu callbacks
    modal.onConfirm = onConfirm;
    modal.onCancel = onCancel;

    // Đóng modal khi click vào overlay
    modal.addEventListener("click", (e) => {
        if (e.target === modal) {
            dtmodalCloseModal(modalId);
        }
    });

    return modalId;
}

// Xử lý action của modal
function dtmodalHandleAction(modalId, isConfirm) {
    const modal = document.getElementById(modalId);
    if (!modal) return;

    if (isConfirm && typeof modal.onConfirm === "function") {
        modal.onConfirm();
    } else if (!isConfirm && typeof modal.onCancel === "function") {
        modal.onCancel();
    }

    dtmodalCloseModal(modalId);
}

// Xác nhận xóa
function dtmodalConfirmDelete(options) {
    const {
        title = "Xác nhận xóa",
            subtitle = "Bạn có chắc chắn muốn xóa?",
            message = "Hành động này không thể hoàn tác.",
            itemName = "",
            onConfirm = null,
    } = options;

    return dtmodalCreateModal({
        type: "warning",
        title: title,
        subtitle: subtitle,
        message: itemName ?
            `Bạn đang xóa: <strong>"${itemName}"</strong><br>${message}` :
            message,
        confirmText: "Xác nhận xóa",
        cancelText: "Hủy bỏ",
        onConfirm: onConfirm,
        onCancel: null,
    });
}

function dtmodalConfirmIndex(options) {
    const {
        title = "Xác nhận xóa",
            subtitle = "Bạn có chắc chắn muốn xóa?",
            message = "Hành động này không thể hoàn tác.",
            itemName = "",
            onConfirm = null,
    } = options;

    return dtmodalCreateModal({
        type: "warning",
        title: title,
        subtitle: subtitle,
        message: itemName ?
            `Bạn đang thay đổi trạng thái của  : <strong>"${itemName}"</strong><br>${message}` :
            message,
        confirmText: "Xác nhận thay đổi",
        cancelText: "Hủy bỏ",
        onConfirm: onConfirm,
        onCancel: null,
    });
}

// Hàm xử lý thay đổi trạng thái cho một mục
function dtmodalHandleStatusToggle(options) {
    const {
        button,
        userName,
        currentStatus,
        formSelector = "form",
        confirmTitle = "Xác nhận thay đổi trạng thái",
        confirmSubtitle = "Bạn có chắc chắn muốn thay đổi trạng thái?",
        confirmMessage = "Hành động này sẽ thay đổi trạng thái hoạt động.",
        successMessage = "Đã thay đổi trạng thái thành công",
        errorMessage = "Có lỗi xảy ra khi thay đổi trạng thái",
    } = options;

    const newStatus = !currentStatus;
    const statusText = newStatus ? "kích hoạt" : "vô hiệu hóa";

    dtmodalConfirmIndex({
        title: confirmTitle,
        subtitle: confirmSubtitle || `Bạn có chắc chắn muốn ${statusText} mục này?`,
        message: confirmMessage,
        itemName: userName,
        onConfirm: () => {
            const form = button.closest(formSelector);
            form.submit();
        },
    });
}

// Hàm cập nhật trạng thái cho nhiều người dùng đã chọn
function updateSelectedStatus(status) {
    const checkboxes = document.getElementsByClassName("row-checkbox");
    const selectedIds = [];

    for (let checkbox of checkboxes) {
        if (checkbox.checked) {
            selectedIds.push(checkbox.value);
        }
    }

    if (selectedIds.length === 0) {
        dtmodalShowToast("warning", {
            title: "Cảnh báo",
            message: "Vui lòng chọn ít nhất một người dùng",
        });
        return;
    }

    const statusText = status === 1 ? "kích hoạt" : "vô hiệu hóa";

    dtmodalCreateModal({
        type: "warning",
        title: `Xác nhận ${statusText} người dùng`,
        message: `Bạn có chắc chắn muốn ${statusText} ${selectedIds.length} người dùng đã chọn không?`,
        confirmText: "Xác nhận",
        cancelText: "Hủy",
        onConfirm: function() {
            document.getElementById("selectedUserIds").value =
                selectedIds.join(",");
            document.getElementById("selectedStatus").value = status;
            document.getElementById("bulkStatusForm").submit();
        },
    });
}

// Hàm cập nhật trạng thái cho nhiều banner đã chọn
function updateSelectedBannerStatus(status) {
    const checkboxes = document.getElementsByClassName("row-checkbox");
    const selectedIds = [];

    for (let checkbox of checkboxes) {
        if (checkbox.checked) {
            selectedIds.push(parseInt(checkbox.value));
        }
    }

    if (selectedIds.length === 0) {
        dtmodalShowToast("warning", {
            title: "Cảnh báo",
            message: "Vui lòng chọn ít nhất một banner",
        });
        return;
    }

    const statusText = status === 1 ? "kích hoạt" : "vô hiệu hóa";

    dtmodalCreateModal({
        type: "warning",
        title: `Xác nhận ${statusText} banner`,
        message: `Bạn có chắc chắn muốn ${statusText} ${selectedIds.length} banner đã chọn không?`,
        confirmText: "Xác nhận",
        cancelText: "Hủy",
        onConfirm: function() {
            document.getElementById("ids").value = JSON.stringify(selectedIds);
            document.getElementById("status").value = status;
            document.getElementById("bulkStatusForm").submit();
        },
    });
}