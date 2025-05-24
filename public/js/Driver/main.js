document.addEventListener("DOMContentLoaded", function () {
    // Xử lý hiện/ẩn mật khẩu
    const togglePasswordButtons = document.querySelectorAll(".toggle-password");
    togglePasswordButtons.forEach((button) => {
        button.addEventListener("click", function () {
            const input = this.parentElement.querySelector("input");
            const eyeIcon = this.querySelector(".eye-icon");
            const eyeOffIcon = this.querySelector(".eye-off-icon");

            if (input.type === "password") {
                input.type = "text";
                eyeIcon.classList.add("hidden");
                eyeOffIcon.classList.remove("hidden");
            } else {
                input.type = "password";
                eyeIcon.classList.remove("hidden");
                eyeOffIcon.classList.add("hidden");
            }
        });
    });

    // Xử lý form đăng nhập
    const loginForm = document.getElementById("loginForm");
    const loginBtn = document.getElementById("loginButton");
    const passwordDialog = document.getElementById("passwordChangeDialog");
    const driverPhoneInput = document.getElementById("driverPhoneInput");

    loginForm.addEventListener("submit", function (e) {
        e.preventDefault();

        // Hiển thị loading button
        const loading = loginBtn.querySelector(".btn-loading");
        const text = loginBtn.querySelector(".btn-text");
        loading.classList.remove("hidden");
        text.classList.add("hidden");

        // Xóa các thông báo lỗi cũ
        const errorElements = loginForm.querySelectorAll('.error-message');
        errorElements.forEach(el => el.remove());

        // Lấy dữ liệu form
        const formData = new FormData(loginForm);

        fetch(loginForm.action, {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
                "Accept": "application/json",
                "X-Requested-With": "XMLHttpRequest"
            },
            body: formData,
        })
        .then(async res => {
            const contentType = res.headers.get("content-type");
            if (!contentType || !contentType.includes("application/json")) {
                const text = await res.text();
                throw new Error(`Server returned HTML instead of JSON: ${text.substring(0, 50)}...`);
            }
            
            const data = await res.json();
            
            if (!res.ok) {
                if (res.status === 422) {
                    // Hiển thị validate lỗi trên form
                    if (data.errors) {
                        Object.entries(data.errors).forEach(([field, messages]) => {
                            const input = loginForm.querySelector(`[name="${field}"]`);
                            if (input) {
                                const errorDiv = document.createElement('div');
                                errorDiv.className = 'error-message text-red-500 text-sm mt-1';
                                errorDiv.textContent = messages.join(' ');
                                input.parentNode.appendChild(errorDiv);
                            }
                        });
                    }
                    throw new Error(data.message || "Lỗi xác thực");
                }
                throw new Error(`HTTP error! status: ${res.status}`);
            }
            
            return data;
        })
        .then(data => {
            loading.classList.add("hidden");
            text.classList.remove("hidden");
            if (data.first_login) {
                showToast("Thông báo", data.message, "success");  // Hiển thị toast
                passwordDialog.classList.remove("hidden");  // Mở popup
                driverPhoneInput.value = loginForm.phone_number.value; // truyền phone cho dialog
            } else if (data.success) {
                window.location.href = "/driver";  // Đăng nhập thành công bình thường
            } else {
                showToast("Lỗi", data.message || "Đăng nhập thất bại", "error");
            }
        })
        .catch((error) => {
            loading.classList.add("hidden");
            text.classList.remove("hidden");
            console.error('Fetch error:', error);
            showToast("Lỗi", error.message || "Không thể kết nối máy chủ", "error");
        });
    });

    // Xử lý form đổi mật khẩu
    document
        .getElementById("passwordChangeForm")
        .addEventListener("submit", function (e) {
            e.preventDefault();

            const newPassword = document.getElementById("newPassword").value;
            const confirmPassword = document.getElementById("confirmPassword").value;
            const phoneNumber = document.getElementById("driverPhoneInput").value;

            const btn = document.getElementById("changePasswordButton");
            const loading = btn.querySelector(".btn-loading");
            const text = btn.querySelector(".btn-text");

            loading.classList.remove("hidden");
            text.classList.add("hidden");

            fetch("/driver/change-password", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({
                    phone_number: phoneNumber,
                    password: newPassword,
                    password_confirmation: confirmPassword,
                }),
            })
            .then(res => res.json())
            .then(data => {
                loading.classList.add("hidden");
                text.classList.remove("hidden");

                if (data.success) {
                    showToast("Thành công", "Mật khẩu đã được thay đổi", "success");
                    setTimeout(() => {
                        window.location.href = "/driver";
                    }, 1500);
                } else {
                    showToast("Lỗi", data.message || "Thay đổi mật khẩu thất bại", "error");
                }
            })
            // Enhance error handling in fetch requests
            .catch((error) => {
                console.error('Error:', error);
                showToast("Lỗi", "Không thể kết nối máy chủ: " + error.message, "error");
            });
        });

    // Hàm hiển thị thông báo
    function showToast(title, description, type = "success") {
        const toast = document.getElementById("toast");
        if (!toast) return;

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

        toast.classList.remove("hidden");

        // Enhance toast notification to persist longer for errors
        window.toastTimeout = setTimeout(() => {
            toast.classList.add("hidden");
        }, type === "error" ? 8000 : 4000); // Longer display for errors
    }
});
