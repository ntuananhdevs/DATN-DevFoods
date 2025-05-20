// Xử lý hiện/ẩn mật khẩu
document.addEventListener('DOMContentLoaded', function() {
    // Xử lý hiện/ẩn mật khẩu
    const togglePasswordButtons = document.querySelectorAll('.toggle-password');
    togglePasswordButtons.forEach(button => {
        button.addEventListener('click', function() {
            const input = this.parentElement.querySelector('input');
            const eyeIcon = this.querySelector('.eye-icon');
            const eyeOffIcon = this.querySelector('.eye-off-icon');
            
            if (input.type === 'password') {
                input.type = 'text';
                eyeIcon.classList.add('hidden');
                eyeOffIcon.classList.remove('hidden');
            } else {
                input.type = 'password';
                eyeIcon.classList.remove('hidden');
                eyeOffIcon.classList.add('hidden');
            }
        });
    });
    
    // Xử lý form đăng nhập
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const phoneInput = document.getElementById('phone');
            const passwordInput = document.getElementById('password');
            const loginButton = document.getElementById('loginButton');
            
            // Hiển thị trạng thái loading
            loginButton.querySelector('.btn-text').classList.add('hidden');
            loginButton.querySelector('.btn-loading').classList.remove('hidden');
            loginButton.disabled = true;
            
            // Giả lập xác thực
            setTimeout(function() {
                // Giả sử đây là lần đăng nhập đầu tiên
                const isFirstLogin = true;
                
                if (isFirstLogin) {
                    // Hiển thị dialog đổi mật khẩu
                    document.getElementById('passwordChangeDialog').classList.remove('hidden');
                } else {
                    // Đăng nhập thành công
                    showToast('Đăng nhập thành công', 'Chào mừng bạn quay trở lại!');
                    
                    // Chuyển hướng đến trang dashboard
                    setTimeout(function() {
                        window.location.href = '/driver/';
                    }, 1000);
                }
                
                // Khôi phục trạng thái nút
                loginButton.querySelector('.btn-text').classList.remove('hidden');
                loginButton.querySelector('.btn-loading').classList.add('hidden');
                loginButton.disabled = false;
            }, 1000);
        });
    }
    
    // Xử lý form đổi mật khẩu
    const passwordChangeForm = document.getElementById('passwordChangeForm');
    if (passwordChangeForm) {
        passwordChangeForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const newPasswordInput = document.getElementById('newPassword');
            const confirmPasswordInput = document.getElementById('confirmPassword');
            const changePasswordButton = document.getElementById('changePasswordButton');
            
            // Kiểm tra mật khẩu khớp nhau
            if (newPasswordInput.value !== confirmPasswordInput.value) {
                showToast('Mật khẩu không khớp', 'Vui lòng kiểm tra lại mật khẩu xác nhận', 'error');
                return;
            }
            
            // Kiểm tra độ dài mật khẩu
            if (newPasswordInput.value.length < 8) {
                showToast('Mật khẩu quá ngắn', 'Mật khẩu phải có ít nhất 8 ký tự', 'error');
                return;
            }
            
            // Hiển thị trạng thái loading
            changePasswordButton.querySelector('.btn-text').classList.add('hidden');
            changePasswordButton.querySelector('.btn-loading').classList.remove('hidden');
            changePasswordButton.disabled = true;
            
            // Giả lập đổi mật khẩu
            setTimeout(function() {
                // Ẩn dialog
                document.getElementById('passwordChangeDialog').classList.add('hidden');
                
                
                showToast('Đổi mật khẩu thành công', 'Mật khẩu của bạn đã được cập nhật');
                
                setTimeout(function() {
                    window.location.href = '/driver/';
                }, 1000);
            }, 1000);
        });
    }
    
    // Xử lý số điện thoại chỉ cho phép nhập số
    const phoneInput = document.getElementById('phone');
    if (phoneInput) {
        phoneInput.addEventListener('input', function(e) {
            e.target.value = e.target.value.replace(/[^\d]/g, '');
        });
    }
});

// Hàm hiển thị thông báo
function showToast(title, description, type = 'success') {
    const toast = document.getElementById('toast');
    if (!toast) return;
    
    const toastTitle = document.getElementById('toastTitle');
    const toastDescription = document.getElementById('toastDescription');
    const successIcon = toast.querySelector('.toast-icon.success');
    const errorIcon = toast.querySelector('.toast-icon.error');
    
    toastTitle.textContent = title;
    toastDescription.textContent = description;
    
    if (type === 'error') {
        successIcon.classList.add('hidden');
        errorIcon.classList.remove('hidden');
        toast.classList.add('error');
        toast.classList.remove('success');
    } else {
        successIcon.classList.remove('hidden');
        errorIcon.classList.add('hidden');
        toast.classList.add('success');
        toast.classList.remove('error');
    }
    
    toast.classList.remove('hidden');
    
    // Tự động ẩn toast sau 3 giây
    setTimeout(function() {
        toast.classList.add('hidden');
    }, 3000);
}