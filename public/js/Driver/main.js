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

            loading.classList.remove("hidden");
            text.classList.add("hidden");

            fetch(form.action, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector(
                        'meta[name="csrf-token"]'
                    ).content,
                },
                body: JSON.stringify({
                    phone_number: form.phone_number.value,
                    password: form.password.value,
                }),
            })
                .then((res) => res.json())
                .then((data) => {
                    loading.classList.add("hidden");
                    text.classList.remove("hidden");

                    if (data.success) {
                        if (data.first_login) {
                            document
                                .getElementById("passwordChangeDialog")
                                .classList.remove("hidden");
                        } else {
                            window.location.href = "/driver"; // trang chủ tài xế
                        }
                    } else {
                        showToast("Lỗi", "Đăng nhập thất bại", "error");
                    }
                })
                .catch(() => {
                    loading.classList.add("hidden");
                    text.classList.remove("hidden");
                    showToast("Lỗi", "Không thể kết nối máy chủ", "error");
                });
        });

    // Xử lý form đổi mật khẩu
    document.getElementById("passwordChangeForm").addEventListener("submit", function (e) {
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
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                phone_number: phoneNumber,
                password: newPassword,
                password_confirmation: confirmPassword
            })
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

        // Tự động ẩn toast sau 3 giây
        setTimeout(function () {
            toast.classList.add("hidden");
        }, 3000);
    }
});
