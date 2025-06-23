/**
 * Product Management JavaScript
 * Handles simple AJAX search functionality for products (similar to topping search)
 */

class ProductManager {
    constructor() {
        this.searchInput = document.getElementById('searchInput');
        this.productTableBody = document.getElementById('productTableBody');
        this.searchTimeout = null;
        this.isLoading = false;

        this.init();
    }

    init() {
        if (this.searchInput) {
            this.searchInput.addEventListener('input', (e) => {
                this.handleSearch(e.target.value);
            });
        }
    }

    handleSearch(searchTerm) {
        // Clear previous timeout
        if (this.searchTimeout) {
            clearTimeout(this.searchTimeout);
        }

        // Set new timeout for debouncing (similar to topping search)
        this.searchTimeout = setTimeout(() => {
            this.performSearch(searchTerm);
        }, 300);
    }

    async performSearch(searchTerm) {
        if (this.isLoading) {
            return;
        }

        this.isLoading = true;
        this.showLoading();

        try {
            // Simple AJAX request like topping search
            const response = await fetch(`${window.location.pathname}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    search: searchTerm
                })
            });

            if (!response.ok) {
                throw new Error('Network response was not ok');
            }

            const data = await response.json();

            if (data.success) {
                this.updateTable(data.html);
            } else {
                this.showError(data.message || 'Có lỗi xảy ra khi tìm kiếm.');
            }

        } catch (error) {
            console.error('Search error:', error);
            this.showError('Có lỗi xảy ra khi tìm kiếm. Vui lòng thử lại.');
        } finally {
            this.isLoading = false;
            this.hideLoading();
        }
    }



    updateTable(html) {
        if (this.productTableBody) {
            this.productTableBody.innerHTML = html;
        }
    }

    showLoading() {
        if (this.productTableBody) {
            const loadingHtml = `
                <tr>
                    <td colspan="8" class="text-center py-8">
                        <div class="flex items-center justify-center space-x-2">
                            <div class="animate-spin rounded-full h-5 w-5 border-b-2 border-blue-600"></div>
                        </div>
                    </td>
                </tr>

            `;
            this.productTableBody.innerHTML = loadingHtml;
        }
    }

    hideLoading() {
        // Loading will be hidden when updateTable is called
    }

    showError(message) {
        if (this.productTableBody) {
            const errorHtml = `
                <tr>
                    <td colspan="8" class="text-center py-8">
                        <div class="text-red-600">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            ${message}
                        </div>
                    </td>
                </tr>
            `;
            this.productTableBody.innerHTML = errorHtml;
        }
    }

}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    window.productManager = new ProductManager();
});