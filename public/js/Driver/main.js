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

        fetch(loginForm.action, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                phone_number: phoneInput.value,
                password: passwordInput.value
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('Đăng nhập thành công', 'Chào mừng bạn quay trở lại!');
                
                if (data.first_login) {
                    // Hiển thị form đổi mật khẩu
                    document.getElementById('passwordChangeDialog').classList.remove('hidden');
                } else {
                    // Chuyển hướng đến dashboard
                    setTimeout(() => {
                        window.location.href = '/driver/';
                    }, 1000);
                }
            } else {
                showToast('Lỗi đăng nhập', data.message || 'Số điện thoại hoặc mật khẩu không đúng', 'error');
            }
        })
        .catch(error => {
            console.error('Lỗi:', error);
            showToast('Lỗi hệ thống', 'Không thể kết nối đến máy chủ', 'error');
        })
        .finally(() => {
            loginButton.querySelector('.btn-text').classList.remove('hidden');
            loginButton.querySelector('.btn-loading').classList.add('hidden');
            loginButton.disabled = false;
        });
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

        // Gửi yêu cầu đổi mật khẩu
        fetch('/driver/change-password', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                password: newPasswordInput.value,
                password_confirmation: confirmPasswordInput.value
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('Thành công', 'Mật khẩu đã được thay đổi thành công');
                document.getElementById('passwordChangeDialog').classList.add('hidden');
                setTimeout(function() {
                    window.location.href = '/driver/';
                }, 1000);
            } else {
                showToast('Lỗi', data.message || 'Đã xảy ra lỗi khi đổi mật khẩu', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Lỗi', 'Đã xảy ra lỗi khi kết nối với máy chủ', 'error');
        })
        .finally(() => {
            changePasswordButton.querySelector('.btn-text').classList.remove('hidden');
            changePasswordButton.querySelector('.btn-loading').classList.add('hidden');
            changePasswordButton.disabled = false;
        });
    });
}


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
});