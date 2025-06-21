/**
 * Product Management JavaScript
 * Handles AJAX search functionality for products
 */

class ProductManager {
    constructor() {
        this.searchInput = document.getElementById('searchInput');
        this.productTableBody = document.getElementById('productTableBody');
        this.searchTimeout = null;
        this.currentPage = 1;
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

        // Set new timeout for debouncing
        this.searchTimeout = setTimeout(() => {
            this.performSearch(searchTerm);
        }, 300);
    }

    async performSearch(searchTerm) {
        if (this.isLoading || (window.filterManager && window.filterManager.isLoading)) {
            return;
        }

        this.isLoading = true;
        this.showLoading();

        try {
            // Get current URL parameters
            const urlParams = new URLSearchParams(window.location.search);
            urlParams.set('search', searchTerm);
            urlParams.set('page', 1); // Reset to first page

            // Make AJAX request
            const response = await fetch(`${window.location.pathname}?${urlParams.toString()}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) {
                throw new Error('Network response was not ok');
            }

            const data = await response.json();
            this.updateTable(data.html);
            this.updatePagination(data.pagination);

            // Update URL without page reload
            const newUrl = `${window.location.pathname}?${urlParams.toString()}`;
            window.history.pushState({}, '', newUrl);

        } catch (error) {
            console.error('Search error:', error);
            this.showError('Có lỗi xảy ra khi tìm kiếm. Vui lòng thử lại.');
        } finally {
            this.isLoading = false;
            this.hideLoading();
        }
    }

    async performSearchWithPage(searchTerm, page) {
        if (this.isLoading || (window.filterManager && window.filterManager.isLoading)) {
            return;
        }

        this.isLoading = true;
        this.showLoading();

        try {
            // Get current URL parameters
            const urlParams = new URLSearchParams(window.location.search);
            urlParams.set('search', searchTerm);
            urlParams.set('page', page);

            // Make AJAX request
            const response = await fetch(`${window.location.pathname}?${urlParams.toString()}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) {
                throw new Error('Network response was not ok');
            }

            const data = await response.json();
            this.updateTable(data.html);
            this.updatePagination(data.pagination);

            // Update URL without page reload
            const newUrl = `${window.location.pathname}?${urlParams.toString()}`;
            window.history.pushState({}, '', newUrl);

        } catch (error) {
            console.error('Search pagination error:', error);
            this.showError('Có lỗi xảy ra khi chuyển trang tìm kiếm. Vui lòng thử lại.');
        } finally {
            this.isLoading = false;
            this.hideLoading();
        }
    }

    updateTable(html) {
        if (this.productTableBody) {
            this.productTableBody.innerHTML = html;

            // Reinitialize checkboxes after updating table
            this.reinitializeCheckboxes();
        }
    }

    updatePagination(paginationHtml) {
        const paginationContainer = document.querySelector('.pagination-container');
        if (paginationContainer && paginationHtml) {
            // Replace the entire pagination container with the new HTML
            paginationContainer.outerHTML = paginationHtml;
            
            // Only override changePage if not already owned by another manager
            if (!window.changePage || window.changePage._owner !== 'filter') {
                window.changePage = (page) => {
                    if (this.searchInput.value.trim()) {
                        this.performSearchWithPage(page);
                    } else {
                        // If no search, let filter manager handle it
                        if (window.filterManager) {
                            window.filterManager.applyFiltersWithPage(page);
                        }
                    }
                };
                window.changePage._owner = 'product';
            }
        }
    }

    showLoading() {
        if (this.productTableBody) {
            this.productTableBody.style.opacity = '0.5';
            this.productTableBody.style.pointerEvents = 'none';
        }

        // Add loading indicator to search input
        if (this.searchInput) {
            this.searchInput.classList.add('loading');
        }
    }

    hideLoading() {
        if (this.productTableBody) {
            this.productTableBody.style.opacity = '1';
            this.productTableBody.style.pointerEvents = 'auto';
        }

        // Remove loading indicator from search input
        if (this.searchInput) {
            this.searchInput.classList.remove('loading');
        }
    }

    showError(message) {
        // Create or update error message
        let errorDiv = document.querySelector('.search-error');
        if (!errorDiv) {
            errorDiv = document.createElement('div');
            errorDiv.className = 'search-error bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4';
            this.productTableBody.parentNode.insertBefore(errorDiv, this.productTableBody);
        }
        errorDiv.textContent = message;

        // Auto hide after 5 seconds
        setTimeout(() => {
            if (errorDiv) {
                errorDiv.remove();
            }
        }, 5000);
    }

    reinitializeCheckboxes() {
        // Prevent duplicate initialization
        if (this._reinitializingCheckboxes) return;
        this._reinitializingCheckboxes = true;
        
        try {
            // Reinitialize checkbox functionality after AJAX update
            const selectAllCheckbox = document.getElementById('selectAllCheckbox');
            const productCheckboxes = document.querySelectorAll('.product-checkbox');

            if (selectAllCheckbox) {
                // Remove existing event listeners by cloning
                const newSelectAllCheckbox = selectAllCheckbox.cloneNode(true);
                selectAllCheckbox.parentNode.replaceChild(newSelectAllCheckbox, selectAllCheckbox);

                // Add new event listener
                newSelectAllCheckbox.addEventListener('change', function() {
                    const isChecked = this.checked;
                    const currentCheckboxes = document.querySelectorAll('.product-checkbox');
                    currentCheckboxes.forEach(checkbox => {
                        checkbox.checked = isChecked;
                    });
                });
            }

            // Handle individual checkboxes - remove old listeners first
            productCheckboxes.forEach(checkbox => {
                const newCheckbox = checkbox.cloneNode(true);
                checkbox.parentNode.replaceChild(newCheckbox, checkbox);
                
                newCheckbox.addEventListener('change', function() {
                    const currentCheckboxes = document.querySelectorAll('.product-checkbox');
                    const currentSelectAll = document.getElementById('selectAllCheckbox');
                    const allChecked = Array.from(currentCheckboxes).every(cb => cb.checked);
                    if (currentSelectAll) {
                        currentSelectAll.checked = allChecked;
                    }
                });
            });
        } finally {
            this._reinitializingCheckboxes = false;
        }
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    window.productManager = new ProductManager();
});

// Export for use in other scripts if needed
if (typeof module !== 'undefined' && module.exports) {
    module.exports = ProductManager;
}