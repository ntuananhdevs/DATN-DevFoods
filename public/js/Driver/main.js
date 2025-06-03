document.addEventListener("DOMContentLoaded", function () {
    const loginForm = document.getElementById("loginForm");
    const loginBtn = document.getElementById("loginButton");

    if (loginForm && loginBtn) {
        loginForm.addEventListener("submit", function (e) {
            e.preventDefault();

            const loading = loginBtn.querySelector(".btn-loading");
            const text = loginBtn.querySelector(".btn-text");

            if (!loading || !text) {
                console.error("Không tìm thấy .btn-loading hoặc .btn-text trong loginButton");
                return;
            }

            loading.classList.remove("hidden");
            text.classList.add("hidden");

            const formData = new FormData(loginForm);
            console.log("Form action:", loginForm.action);

            fetch(loginForm.action, {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')?.content || '',
                    Accept: "application/json",
                    "X-Requested-With": "XMLHttpRequest",
                },
                body: formData,
            })
                .then(async (res) => {
                    const contentType = res.headers.get("content-type") || "";
                    if (res.status === 429) {
                        const data = contentType.includes("application/json") ? await res.json() : {};
                        showToast(
                            "Đã bị khóa tạm thời",
                            data?.message || "Bạn đăng nhập quá nhiều lần, vui lòng thử lại sau.",
                            "error"
                        );
                        return null;
                    }
                    if (!contentType.includes("application/json")) {
                        const text = await res.text();
                        console.error("Non-JSON response:", text);
                        showToast("Lỗi", `Server trả về không phải JSON: ${text.substring(0, 50)}...`, "error");
                        return null;
                    }
                    const data = await res.json();
                    if (!res.ok) {
                        loginForm.querySelectorAll(".error-message").forEach(el => el.remove());
                        if (res.status === 422 && data.errors) {
                            Object.entries(data.errors).forEach(([field, messages]) => {
                                const input = loginForm.querySelector(`[name="${field}"]`);
                                if (input) {
                                    const errorDiv = document.createElement("div");
                                    errorDiv.className = "error-message text-red-500 text-sm mt-1";
                                    errorDiv.textContent = messages.join(" ");
                                    input.parentNode.appendChild(errorDiv);
                                }
                            });
                        }
                        showToast("Lỗi xác thực", data.message || "Vui lòng kiểm tra lại dữ liệu.", "error");
                        return null;
                    }
                    return data;
                })
                .then((data) => {
                    if (!data) return;
                    loading.classList.add("hidden");
                    text.classList.remove("hidden");
                    if (data.first_login) {
                        showToast("Thông báo", "Đăng nhập thành công, vui lòng đổi mật khẩu lần đầu.", "success");
                        const passwordDialog = document.getElementById("passwordChangeDialog");
                        const driverPhoneInput = document.getElementById("driverPhoneInput");
                        if (passwordDialog && driverPhoneInput) {
                            passwordDialog.classList.remove("hidden");
                            driverPhoneInput.value = loginForm.phone_number.value;
                        }
                    } else if (data.success) {
                        window.location.href = data.redirect || "/driver";
                    } else {
                        showToast("Lỗi", data.message || "Đăng nhập thất bại", "error");
                    }
                })
                .catch((error) => {
                    console.error("Fetch error:", error);
                    showToast("Lỗi", "Không thể kết nối máy chủ: " + error.message, "error");
                    loading.classList.add("hidden");
                    text.classList.remove("hidden");
                });
        });
    } else {
        console.error("Không tìm thấy loginForm hoặc loginButton trong DOM");
    }

    // Xử lý form đổi mật khẩu
    document
        .getElementById("passwordChangeForm")
        .addEventListener("submit", function (e) {
            e.preventDefault();

            const newPassword = document.getElementById("newPassword").value;
            const confirmPassword =
                document.getElementById("confirmPassword").value;
            const phoneNumber =
                document.getElementById("driverPhoneInput").value;

            const btn = document.getElementById("changePasswordButton");
            const loading = btn.querySelector(".btn-loading");
            const text = btn.querySelector(".btn-text");

            loading.classList.remove("hidden");
            text.classList.add("hidden");

            fetch("/driver/change-password", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector(
                        'meta[name="csrf-token"]'
                    ).content,
                },
                body: JSON.stringify({
                    phone_number: phoneNumber,
                    password: newPassword,
                    password_confirmation: confirmPassword,
                }),
            })
                .then((res) => {
                    if (!res.ok) {
                        return res.json().then((data) => {
                            throw new Error(
                                data.message ||
                                    "Lỗi khi thay đổi mật khẩu, vui lòng thử lại."
                            );
                        });
                    }
                    return res.json();
                })
                .then((data) => {
                    loading.classList.add("hidden");
                    text.classList.remove("hidden");

                    if (data.success) {
                        showToast(
                            "Thành công",
                            "Mật khẩu đã được thay đổi",
                            "success"
                        );
                        setTimeout(() => {
                            window.location.href = "/driver";
                        }, 1500);
                    } else {
                        showToast(
                            "Lỗi",
                            data.message || "Thay đổi mật khẩu thất bại",
                            "error"
                        );
                    }
                })
                .catch((error) => {
                    loading.classList.add("hidden");
                    text.classList.remove("hidden");
                    console.error("Error:", error);
                    showToast(
                        "Lỗi",
                        "Không thể kết nối máy chủ: " + error.message,
                        "error"
                    );
                });
        });

    // Hàm hiển thị thông báo toast
    function showToast(title, description, type = "success") {
        const toast = document.getElementById("toast");
        if (!toast) return;
    
        // Xoá toast khỏi DOM để reset animation
        toast.classList.add("hidden");
        toast.offsetHeight; // Force reflow để reset lại animation
    
        // Clear timeout cũ nếu có
        if (window.toastTimeout) {
            clearTimeout(window.toastTimeout);
        }
    
        const toastTitle = document.getElementById("toastTitle");
        const toastDescription = document.getElementById("toastDescription");
        const successIcon = toast.querySelector(".toast-icon.success");
        const errorIcon = toast.querySelector(".toast-icon.error");
    
        toastTitle.textContent = title;
        toastDescription.textContent = description;
    
        if (type === "error") {
            successIcon.classList.add("hidden");
            errorIcon.classList.remove("hidden");
            toast.classList.add("error");
            toast.classList.remove("success");
        } else {
            successIcon.classList.remove("hidden");
            errorIcon.classList.add("hidden");
            toast.classList.add("success");
            toast.classList.remove("error");
        }
    
        // Hiển thị lại
        toast.classList.remove("hidden");
    
        // Tự động ẩn sau vài giây
        window.toastTimeout = setTimeout(() => {
            toast.classList.add("hidden");
        }, type === "error" ? 8000 : 4000);
    }    
});