/**
 * Driver Application Sample Data Filler
 * Điền thông tin mẫu vào form đăng ký tài xế để testing
 */

// Sample data templates
const sampleData = {
    template1: {
        full_name: "Nguyễn Văn Nam",
        date_of_birth: "1990-05-15",
        gender: "male",
        phone_number: "0912345678",
        email: "nguyenvannam@example.com",
        address: "123 Đường Nguyễn Huệ, Phường Bến Nghé",
        city: "Thành phố Hồ Chí Minh",
        district: "Quận 1",
        id_card_number: "079090001234",
        id_card_issue_date: "2015-06-20",
        id_card_issue_place: "Cục Cảnh sát ĐKQL cư trú và DLQG về dân cư",
        vehicle_type: "motorcycle",
        vehicle_model: "Honda Winner X",
        vehicle_color: "Đỏ đen",
        license_plate: "51H1-12345",
        driver_license_number: "B1123456789",
        bank_name: "Ngân hàng Vietcombank",
        bank_account_number: "1234567890123",
        bank_account_name: "NGUYEN VAN NAM",
        emergency_contact_name: "Nguyễn Thị Lan",
        emergency_contact_phone: "0987654321",
        emergency_contact_relationship: "Vợ"
    },
    template2: {
        full_name: "Trần Thị Hoa",
        date_of_birth: "1992-03-10",
        gender: "female",
        phone_number: "0976543210",
        email: "tranthihoa@example.com",
        address: "456 Đường Lê Lợi, Phường Lê Lợi",
        city: "Thành phố Đà Nẵng", 
        district: "Quận Hải Châu",
        id_card_number: "043092001234",
        id_card_issue_date: "2016-08-15",
        id_card_issue_place: "Cục Cảnh sát ĐKQL cư trú và DLQG về dân cư",
        vehicle_type: "car",
        vehicle_model: "Toyota Vios",
        vehicle_color: "Trắng",
        license_plate: "43A-56789",
        driver_license_number: "B2987654321",
        bank_name: "Ngân hàng Techcombank",
        bank_account_number: "9876543210987",
        bank_account_name: "TRAN THI HOA",
        emergency_contact_name: "Trần Văn Minh",
        emergency_contact_phone: "0934567890",
        emergency_contact_relationship: "Anh trai"
    },
    template3: {
        full_name: "Lê Minh Tuan",
        date_of_birth: "1988-12-25",
        gender: "male", 
        phone_number: "0865432109",
        email: "leminhtuan@example.com",
        address: "789 Đường Trần Phú, Phường Phước Vĩnh",
        city: "Tỉnh Thừa Thiên Huế",
        district: "Thành phố Huế",
        id_card_number: "026088001234",
        id_card_issue_date: "2014-04-10",
        id_card_issue_place: "Cục Cảnh sát ĐKQL cư trú và DLQG về dân cư",
        vehicle_type: "bicycle",
        vehicle_model: "Giant ATX 830",
        vehicle_color: "Xanh đen",
        license_plate: "N/A",
        driver_license_number: "N/A", 
        bank_name: "Ngân hàng ACB",
        bank_account_number: "5432109876543",
        bank_account_name: "LE MINH TUAN",
        emergency_contact_name: "Lê Thị Mai",
        emergency_contact_phone: "0923456789",
        emergency_contact_relationship: "Mẹ"
    }
};

/**
 * Fill form with sample data
 */
function fillSampleData(templateName = 'template1') {
    const data = sampleData[templateName];
    
    if (!data) {
        console.error('Template not found:', templateName);
        return;
    }

    // Fill text inputs
    Object.keys(data).forEach(key => {
        const input = document.querySelector(`input[name="${key}"], select[name="${key}"]`);
        if (input) {
            input.value = data[key];
            
            // Trigger change event for validation
            input.dispatchEvent(new Event('change', { bubbles: true }));
            input.dispatchEvent(new Event('input', { bubbles: true }));
        }
    });

    // Check terms checkbox
    const termsCheckbox = document.querySelector('input[name="terms_accepted"]');
    if (termsCheckbox) {
        termsCheckbox.checked = true;
        termsCheckbox.dispatchEvent(new Event('change', { bubbles: true }));
    }

    // Show success message
    showNotification('✅ Đã điền thông tin mẫu thành công!', 'success');
}

/**
 * Clear all form data
 */
function clearFormData() {
    const form = document.querySelector('form');
    if (form) {
        form.reset();
        
        // Reset file input labels
        const fileLabels = document.querySelectorAll('.file-input-label');
        fileLabels.forEach(label => {
            const inputId = label.getAttribute('for');
            if (inputId) {
                if (inputId.includes('profile')) {
                    label.innerHTML = '<i class="fas fa-cloud-upload-alt mr-2"></i>Chọn ảnh chân dung';
                } else if (inputId.includes('id_card_front')) {
                    label.innerHTML = '<i class="fas fa-cloud-upload-alt mr-2"></i>Chọn ảnh mặt trước';
                } else if (inputId.includes('id_card_back')) {
                    label.innerHTML = '<i class="fas fa-cloud-upload-alt mr-2"></i>Chọn ảnh mặt sau';
                } else if (inputId.includes('driver_license')) {
                    label.innerHTML = '<i class="fas fa-cloud-upload-alt mr-2"></i>Chọn ảnh GPLX';
                } else if (inputId.includes('vehicle_registration')) {
                    label.innerHTML = '<i class="fas fa-cloud-upload-alt mr-2"></i>Chọn ảnh đăng ký xe';
                }
                
                // Reset label styles
                label.style.borderColor = '#d1d5db';
                label.style.backgroundColor = '#f7fafc';
                label.style.color = '';
            }
        });
        
        showNotification('🗑️ Đã xóa toàn bộ dữ liệu form!', 'info');
    }
}

/**
 * Create sample files for upload (creates empty files for testing)
 */
function createSampleFiles() {
    const fileInputs = document.querySelectorAll('input[type="file"]');
    
    fileInputs.forEach(input => {
        // Create a dummy file
        const file = new File([''], 'sample-image.jpg', { type: 'image/jpeg' });
        
        // Create a new FileList
        const dataTransfer = new DataTransfer();
        dataTransfer.items.add(file);
        input.files = dataTransfer.files;
        
        // Update label
        const label = document.getElementById(input.id + '_label');
        if (label) {
            label.innerHTML = `<i class="fas fa-check mr-2"></i>sample-image.jpg`;
            label.style.borderColor = '#10b981';
            label.style.backgroundColor = '#ecfdf5';
            label.style.color = '#047857';
        }
        
        // Trigger change event
        input.dispatchEvent(new Event('change', { bubbles: true }));
    });
    
    showNotification('📁 Đã tạo file mẫu cho tất cả trường upload!', 'success');
}

/**
 * Show notification message
 */
function showNotification(message, type = 'info') {
    // Remove existing notification
    const existingNotification = document.querySelector('.sample-notification');
    if (existingNotification) {
        existingNotification.remove();
    }
    
    // Create notification
    const notification = document.createElement('div');
    notification.className = `sample-notification fixed top-4 right-4 px-6 py-3 rounded-lg shadow-lg z-50 transition-all duration-300`;
    
    // Set colors based on type
    switch (type) {
        case 'success':
            notification.classList.add('bg-green-100', 'border', 'border-green-400', 'text-green-700');
            break;
        case 'error':
            notification.classList.add('bg-red-100', 'border', 'border-red-400', 'text-red-700');
            break;
        case 'info':
            notification.classList.add('bg-blue-100', 'border', 'border-blue-400', 'text-blue-700');
            break;
        default:
            notification.classList.add('bg-gray-100', 'border', 'border-gray-400', 'text-gray-700');
    }
    
    notification.textContent = message;
    document.body.appendChild(notification);
    
    // Auto remove after 3 seconds
    setTimeout(() => {
        notification.style.opacity = '0';
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 300);
    }, 3000);
}

/**
 * Create and inject control panel
 */
function createControlPanel() {
    // Check if already exists
    if (document.querySelector('.sample-control-panel')) {
        return;
    }
    
    const controlPanel = document.createElement('div');
    controlPanel.className = 'sample-control-panel fixed bottom-4 right-4 bg-white border border-gray-300 rounded-lg shadow-lg p-4 z-50';
    controlPanel.style.minWidth = '280px';
    
    controlPanel.innerHTML = `
        <div class="mb-3">
            <h6 class="font-semibold text-gray-800 mb-2">🛠️ Sample Data Control</h6>
            <div class="grid grid-cols-1 gap-2">
                <button onclick="fillSampleData('template1')" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-2 rounded text-sm transition-colors">
                    👨 Mẫu Nam (Xe máy)
                </button>
                <button onclick="fillSampleData('template2')" class="bg-pink-500 hover:bg-pink-600 text-white px-3 py-2 rounded text-sm transition-colors">
                    👩 Mẫu Nữ (Ô tô)
                </button>
                <button onclick="fillSampleData('template3')" class="bg-green-500 hover:bg-green-600 text-white px-3 py-2 rounded text-sm transition-colors">
                    🚴 Mẫu Xe đạp
                </button>
            </div>
        </div>
        <div class="grid grid-cols-1 gap-2">
            <button onclick="createSampleFiles()" class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-2 rounded text-sm transition-colors">
                📁 Tạo file mẫu
            </button>
            <button onclick="clearFormData()" class="bg-red-500 hover:bg-red-600 text-white px-3 py-2 rounded text-sm transition-colors">
                🗑️ Xóa toàn bộ
            </button>
            <button onclick="toggleControlPanel()" class="bg-gray-500 hover:bg-gray-600 text-white px-3 py-2 rounded text-sm transition-colors">
                ❌ Ẩn panel
            </button>
        </div>
    `;
    
    document.body.appendChild(controlPanel);
}

/**
 * Toggle control panel visibility
 */
function toggleControlPanel() {
    const panel = document.querySelector('.sample-control-panel');
    if (panel) {
        panel.style.display = panel.style.display === 'none' ? 'block' : 'none';
    }
}

/**
 * Initialize when DOM is loaded
 */
document.addEventListener('DOMContentLoaded', function() {
    // Only show in development or when specifically enabled
    const showSampleControls = 
        window.location.hostname === 'localhost' || 
        window.location.hostname === '127.0.0.1' ||
        localStorage.getItem('enableSampleData') === 'true' ||
        window.location.search.includes('sample=true');
    
    if (showSampleControls) {
        createControlPanel();
        console.log('🚀 Sample Data Control Panel loaded!');
        console.log('Available functions:');
        console.log('- fillSampleData("template1|template2|template3")');
        console.log('- createSampleFiles()');
        console.log('- clearFormData()');
    }
});

// Global functions for console access
window.fillSampleData = fillSampleData;
window.clearFormData = clearFormData;
window.createSampleFiles = createSampleFiles;
window.toggleControlPanel = toggleControlPanel; 