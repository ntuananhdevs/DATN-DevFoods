// Xử lý hiện/ẩn mật khẩu
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
    document
        .getElementById("loginForm")
        .addEventListener("submit", function (e) {
            e.preventDefault();
            const form = e.target;
            const loginBtn = document.getElementById("loginButton");
            const loading = loginBtn.querySelector(".btn-loading");
            const text = loginBtn.querySelector(".btn-text");

            // Ẩn toast cũ nếu đang hiển thị
            const toast = document.getElementById("toast");
            if (toast && !toast.classList.contains("hidden")) {
                toast.classList.add("hidden");
            }

            loading.classList.remove("hidden");
            text.classList.add("hidden");

            // Sử dụng FormData thay vì JSON để phù hợp với xử lý của Laravel
            const formData = new FormData(form);
            
            fetch(form.action, {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": document.querySelector(
                        'meta[name="csrf-token"]'
                    ).content,
                },
                body: formData,
                // Thêm credentials để đảm bảo cookie được gửi
                credentials: 'same-origin'
            })
                .then((res) => {
                    // Lưu status code để xử lý lỗi
                    const status = res.status;
                    
                    if (res.redirected) {
                        // Nếu server redirect, chuyển hướng theo URL
                        window.location.href = res.url;
                        return { redirected: true };
                    }
                    
                    // Thử parse JSON response
                    return res.json().then(data => {
                        return { data, status };
                    }).catch(err => {
                        // Nếu không parse được JSON, trả về lỗi
                        return { error: 'Invalid JSON response', status };
                    });
                })
                .then((result) => {
                    if (result.redirected) return; // Đã xử lý redirect
                    
                    loading.classList.add("hidden");
                    text.classList.remove("hidden");
                    
                    const { data, status, error } = result;
                    
                    // Nếu có lỗi parse JSON
                    if (error) {
                        showToast("Lỗi", "Phản hồi không hợp lệ từ máy chủ", "error");
                        return;
                    }
                    
                    // Xử lý response dựa trên status code
                    if (status === 422) { // Validation error
                        // Hiển thị lỗi validation đầu tiên
                        const errorMessage = data.message || (data.errors ? Object.values(data.errors)[0][0] : "Dữ liệu không hợp lệ");
                        showToast("Lỗi", errorMessage, "error");
                        return;
                    }
                    
                    if (data.success) {
                        if (data.first_login) {
                            document
                                .getElementById("passwordChangeDialog")
                                .classList.remove("hidden");
                        } else {
                            window.location.href = "/driver"; // trang chủ tài xế
                        }
                    } else {
                        showToast("Lỗi", data.message || "Đăng nhập thất bại", "error");
                    }
                })
                .catch((error) => {
                    console.error("Lỗi:", error);
                    loading.classList.add("hidden");
                    text.classList.remove("hidden");
                    showToast("Lỗi", "Không thể kết nối máy chủ", "error");
                });})
        });

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
                .then((res) => res.json())
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
                .catch(() => {
                    loading.classList.add("hidden");
                    text.classList.remove("hidden");
                    showToast("Lỗi", "Không thể kết nối máy chủ", "error");
                });
        });

    // Hàm hiển thị thông báo
    function showToast(title, description, type = "success") {
        const toast = document.getElementById("toast");
        if (!toast) return;
        
        // Hủy bỏ timeout ẩn toast trước đó nếu có
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

        // Đảm bảo toast hiển thị ngay lập tức
        toast.classList.remove("hidden");

        // Tự động ẩn toast sau 4 giây
        window.toastTimeout = setTimeout(function () {
            toast.classList.add("hidden");
        }, 4000);
    }

