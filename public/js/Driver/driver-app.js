// Driver App JavaScript Functions

class DriverApp {
    constructor() {
        this.init();
    }

    init() {
        this.setupEventListeners();
        this.initializeComponents();
    }

    setupEventListeners() {
        // Mobile menu toggle
        document.addEventListener('click', (e) => {
            if (e.target.matches('[data-mobile-menu-toggle]')) {
                this.toggleMobileMenu();
            }
        });

        // Search functionality
        const searchInputs = document.querySelectorAll('[data-search]');
        searchInputs.forEach(input => {
            input.addEventListener('input', (e) => {
                this.handleSearch(e.target.value, e.target.dataset.search);
            });
        });

        // Tab switching
        document.addEventListener('click', (e) => {
            if (e.target.matches('[data-tab]')) {
                this.switchTab(e.target.dataset.tab);
            }
        });

        // Form submissions
        document.addEventListener('submit', (e) => {
            if (e.target.matches('[data-ajax-form]')) {
                e.preventDefault();
                this.handleAjaxForm(e.target);
            }
        });

        // Toggle switches
        document.addEventListener('change', (e) => {
            if (e.target.matches('[data-toggle]')) {
                this.handleToggle(e.target);
            }
        });
    }

    initializeComponents() {
        // Initialize tooltips
        this.initTooltips();
        
        // Initialize modals
        this.initModals();
        
        // Auto-refresh data
        this.startAutoRefresh();
    }

    // Mobile menu toggle
    toggleMobileMenu() {
        const menu = document.querySelector('[data-mobile-menu]');
        if (menu) {
            menu.classList.toggle('hidden');
        }
    }

    // Search functionality
    handleSearch(query, target) {
        const items = document.querySelectorAll(`[data-searchable="${target}"]`);
        const searchTerm = query.toLowerCase();

        items.forEach(item => {
            const text = item.textContent.toLowerCase();
            if (text.includes(searchTerm)) {
                item.style.display = '';
                item.classList.add('fade-in');
            } else {
                item.style.display = 'none';
                item.classList.remove('fade-in');
            }
        });
    }

    // Tab switching
    switchTab(tabId) {
        // Hide all tab contents
        document.querySelectorAll('[data-tab-content]').forEach(content => {
            content.classList.add('hidden');
        });

        // Remove active class from all tabs
        document.querySelectorAll('[data-tab]').forEach(tab => {
            tab.classList.remove('tab-active');
            tab.classList.add('tab-inactive');
        });

        // Show selected tab content
        const targetContent = document.querySelector(`[data-tab-content="${tabId}"]`);
        if (targetContent) {
            targetContent.classList.remove('hidden');
            targetContent.classList.add('fade-in');
        }

        // Add active class to selected tab
        const activeTab = document.querySelector(`[data-tab="${tabId}"]`);
        if (activeTab) {
            activeTab.classList.remove('tab-inactive');
            activeTab.classList.add('tab-active');
        }
    }

    // AJAX form handling
    async handleAjaxForm(form) {
        const submitBtn = form.querySelector('[type="submit"]');
        const originalText = submitBtn.textContent;
        
        // Show loading state
        submitBtn.classList.add('btn-loading');
        submitBtn.disabled = true;

        try {
            const formData = new FormData(form);
            const response = await fetch(form.action, {
                method: form.method,
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const data = await response.json();

            if (data.success) {
                this.showToast('Thành công!', data.message || 'Thao tác đã được thực hiện.', 'success');
                if (data.redirect) {
                    window.location.href = data.redirect;
                }
            } else {
                this.showToast('Lỗi!', data.message || 'Có lỗi xảy ra.', 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            this.showToast('Lỗi!', 'Có lỗi xảy ra. Vui lòng thử lại.', 'error');
        } finally {
            // Reset loading state
            submitBtn.classList.remove('btn-loading');
            submitBtn.disabled = false;
            submitBtn.textContent = originalText;
        }
    }

    // Toggle switch handling
    handleToggle(toggle) {
        const action = toggle.dataset.toggle;
        const value = toggle.checked;

        switch (action) {
            case 'driver-status':
                this.updateDriverStatus(value);
                break;
            default:
                console.log(`Toggle ${action}:`, value);
        }
    }

    // Update driver status
    async updateDriverStatus(isActive) {
        try {
            const response = await fetch('/driver/update-status', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ is_active: isActive })
            });

            const data = await response.json();

            if (data.success) {
                const message = isActive ? 'Đã bật nhận đơn hàng' : 'Đã tắt nhận đơn hàng';
                this.showToast('Cập nhật trạng thái', message, 'success');
            }
        } catch (error) {
            console.error('Error updating driver status:', error);
            this.showToast('Lỗi!', 'Không thể cập nhật trạng thái.', 'error');
        }
    }

    // Toast notifications
    showToast(title, message, type = 'info') {
        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        toast.innerHTML = `
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    ${this.getToastIcon(type)}
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium">${title}</p>
                    <p class="text-sm">${message}</p>
                </div>
                <div class="ml-auto pl-3">
                    <button onclick="this.parentElement.parentElement.remove()" class="text-white hover:text-gray-200">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                        </svg>
                    </button>
                </div>
            </div>
        `;

        document.body.appendChild(toast);

        // Auto remove after 5 seconds
        setTimeout(() => {
            if (toast.parentElement) {
                toast.remove();
            }
        }, 5000);
    }

    getToastIcon(type) {
        const icons = {
            success: '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>',
            error: '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 001.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>',
            warning: '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>',
            info: '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>'
        };
        return icons[type] || icons.info;
    }

    // Initialize tooltips
    initTooltips() {
        const tooltips = document.querySelectorAll('[data-tooltip]');
        tooltips.forEach(element => {
            element.addEventListener('mouseenter', (e) => {
                this.showTooltip(e.target, e.target.dataset.tooltip);
            });
            element.addEventListener('mouseleave', () => {
                this.hideTooltip();
            });
        });
    }

    showTooltip(element, text) {
        const tooltip = document.createElement('div');
        tooltip.className = 'absolute z-50 px-2 py-1 text-sm text-white bg-gray-900 rounded shadow-lg';
        tooltip.textContent = text;
        tooltip.id = 'tooltip';

        document.body.appendChild(tooltip);

        const rect = element.getBoundingClientRect();
        tooltip.style.left = rect.left + (rect.width / 2) - (tooltip.offsetWidth / 2) + 'px';
        tooltip.style.top = rect.top - tooltip.offsetHeight - 5 + 'px';
    }

    hideTooltip() {
        const tooltip = document.getElementById('tooltip');
        if (tooltip) {
            tooltip.remove();
        }
    }

    // Initialize modals
    initModals() {
        document.addEventListener('click', (e) => {
            if (e.target.matches('[data-modal-open]')) {
                this.openModal(e.target.dataset.modalOpen);
            }
            if (e.target.matches('[data-modal-close]')) {
                this.closeModal(e.target.dataset.modalClose);
            }
        });
    }

    openModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('fade-in');
        }
    }

    closeModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('fade-in');
        }
    }

    // Auto refresh data
    startAutoRefresh() {
        // Refresh notifications every 30 seconds
        setInterval(() => {
            this.refreshNotifications();
        }, 30000);

        // Refresh order status every 60 seconds
        setInterval(() => {
            this.refreshOrderStatus();
        }, 60000);
    }

    async refreshNotifications() {
        try {
            const response = await fetch('/driver/notifications/count');
            const data = await response.json();
            
            // Update notification badges
            const badges = document.querySelectorAll('[data-notification-count]');
            badges.forEach(badge => {
                if (data.unread_count > 0) {
                    badge.textContent = data.unread_count;
                    badge.classList.remove('hidden');
                } else {
                    badge.classList.add('hidden');
                }
            });
        } catch (error) {
            console.error('Error refreshing notifications:', error);
        }
    }

    async refreshOrderStatus() {
        const currentPage = window.location.pathname;
        if (currentPage.includes('/orders/') && !currentPage.endsWith('/orders')) {
            // Refresh individual order page
            const orderId = currentPage.split('/').pop();
            try {
                const response = await fetch(`/driver/orders/${orderId}/status`);
                const data = await response.json();
                
                // Update order status on page
                const statusElement = document.querySelector('[data-order-status]');
                if (statusElement && data.status !== statusElement.textContent) {
                    location.reload(); // Reload page if status changed
                }
            } catch (error) {
                console.error('Error refreshing order status:', error);
            }
        }
    }

    // Utility functions
    formatCurrency(amount) {
        return new Intl.NumberFormat('vi-VN', {
            style: 'currency',
            currency: 'VND'
        }).format(amount);
    }

    formatDate(date) {
        return new Intl.DateTimeFormat('vi-VN', {
            year: 'numeric',
            month: '2-digit',
            day: '2-digit',
            hour: '2-digit',
            minute: '2-digit'
        }).format(new Date(date));
    }

    // Geolocation functions
    getCurrentLocation() {
        return new Promise((resolve, reject) => {
            if (!navigator.geolocation) {
                reject(new Error('Geolocation is not supported'));
                return;
            }

            navigator.geolocation.getCurrentPosition(
                position => resolve({
                    lat: position.coords.latitude,
                    lng: position.coords.longitude
                }),
                error => reject(error),
                {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 300000
                }
            );
        });
    }

    // Map utilities
    initializeMap(containerId, options = {}) {
        const mapboxgl = window.mapboxgl; // Declare mapboxgl variable
        if (typeof mapboxgl === 'undefined') {
            console.error('Mapbox GL JS is not loaded');
            return null;
        }

        mapboxgl.accessToken = options.accessToken || 'pk.eyJ1IjoibmhhdG5ndXllbnF2IiwiYSI6ImNtYjZydDNnZDAwY24ybm9qcTdxcTNocG8ifQ.u7X_0DfN7d52xZ8cGFbWyQ';

        const map = new mapboxgl.Map({
            container: containerId,
            style: options.style || 'mapbox://styles/mapbox/streets-v12',
            center: options.center || [106.668, 10.774],
            zoom: options.zoom || 13
        });

        // Add navigation controls
        map.addControl(new mapboxgl.NavigationControl(), 'top-right');
        
        // Add geolocate control
        map.addControl(new mapboxgl.GeolocateControl({
            positionOptions: {
                enableHighAccuracy: true
            },
            trackUserLocation: true,
            showUserHeading: true
        }), 'top-right');

        return map;
    }
}

// Initialize app when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    window.driverApp = new DriverApp();
});

// Global utility functions
window.updateOrderStatus = async function(orderId, newStatus) {
    if (!confirm(`Xác nhận cập nhật trạng thái đơn hàng thành "${newStatus}"?`)) {
        return;
    }

    try {
        const response = await fetch(`/driver/orders/${orderId}/update-status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ status: newStatus })
        });

        const data = await response.json();

        if (data.success) {
            window.driverApp.showToast('Thành công!', `Đơn hàng ${orderId} đã được cập nhật.`, 'success');
            setTimeout(() => location.reload(), 1000);
        } else {
            window.driverApp.showToast('Lỗi!', data.message || 'Có lỗi xảy ra.', 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        window.driverApp.showToast('Lỗi!', 'Có lỗi xảy ra. Vui lòng thử lại.', 'error');
    }
};

window.markNotificationAsRead = async function(notificationId) {
    try {
        const response = await fetch(`/driver/notifications/${notificationId}/mark-read`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });

        const data = await response.json();
        if (data.success) {
            const element = document.querySelector(`[data-notification="${notificationId}"]`);
            if (element) {
                element.classList.remove('notification-unread');
            }
        }
    } catch (error) {
        console.error('Error marking notification as read:', error);
    }
};

window.deleteNotification = async function(notificationId) {
    if (!confirm('Bạn có chắc chắn muốn xóa thông báo này?')) {
        return;
    }

    try {
        const response = await fetch(`/driver/notifications/${notificationId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });

        const data = await response.json();
        if (data.success) {
            const element = document.querySelector(`[data-notification="${notificationId}"]`);
            if (element) {
                element.remove();
            }
            window.driverApp.showToast('Thành công!', 'Đã xóa thông báo.', 'success');
        }
    } catch (error) {
        console.error('Error deleting notification:', error);
        window.driverApp.showToast('Lỗi!', 'Không thể xóa thông báo.', 'error');
    }
};
