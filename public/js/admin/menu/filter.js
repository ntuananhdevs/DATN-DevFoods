/**
 * Filter Management JavaScript
 * Handles AJAX filtering functionality for admin pages
 */

class FilterManager {
    constructor(options = {}) {
        this.filterForm = document.getElementById('filterForm');
        this.filterModal = document.getElementById('filterModal');
        this.tableBody = document.getElementById('productTableBody');
        this.paginationContainer = document.querySelector('.pagination-container');
        this.baseUrl = options.baseUrl || window.location.pathname;
        this.isLoading = false;
        this.currentFilters = {};

        this.init();
    }

    init() {
        this.bindEvents();
        this.loadCurrentFilters();
    }

    bindEvents() {
        // Filter form submission
        if (this.filterForm) {
            this.filterForm.addEventListener('submit', (e) => {
                e.preventDefault();
                this.applyFilters();
            });
        }

        // Category filter change
        const categoryFilter = document.getElementById('filter_category');
        if (categoryFilter) {
            categoryFilter.addEventListener('change', () => {
                this.updatePriceRangeByCategory(categoryFilter.value);
                this.applyFilters();
            });
        }

        // Stock status checkboxes
        const stockCheckboxes = document.querySelectorAll('input[name="stock_status[]"]');
        stockCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', () => {
                this.applyFilters();
            });
        });

        // Product status checkboxes
        const statusCheckboxes = document.querySelectorAll('input[name="status[]"]');
        statusCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', () => {
                this.applyFilters();
            });
        });

        // Date filter
        const dateFilter = document.getElementById('filter_date_added');
        if (dateFilter) {
            dateFilter.addEventListener('change', () => {
                this.applyFilters();
            });
        }

        // Price range slider (if exists)
        if (window.priceSlider) {
            // Listen for price slider changes - now only triggers when drag ends
            document.addEventListener('priceRangeChanged', () => {
                this.applyFilters();
            });
        }

        // Reset filters button
        const resetButton = document.getElementById('resetFilters');
        if (resetButton) {
            resetButton.addEventListener('click', () => {
                this.resetFilters();
            });
        }

        // Apply filters button
        const applyButton = document.getElementById('applyFilters');
        if (applyButton) {
            applyButton.addEventListener('click', () => {
                this.applyFilters();
                this.closeModal();
            });
        }
    }

    loadCurrentFilters() {
        // Load filters from URL parameters
        const urlParams = new URLSearchParams(window.location.search);

        // Category filter
        const categoryId = urlParams.get('category_id');
        if (categoryId) {
            const categorySelect = document.getElementById('filter_category');
            if (categorySelect) {
                categorySelect.value = categoryId;
            }
        }

        // Stock status filters
        const stockStatus = urlParams.getAll('stock_status[]');
        stockStatus.forEach(status => {
            const checkbox = document.querySelector(`input[name="stock_status[]"][value="${status}"]`);
            if (checkbox) {
                checkbox.checked = true;
            }
        });

        // Product status filters
        const productStatuses = urlParams.getAll('status[]');
        productStatuses.forEach(status => {
            const checkbox = document.querySelector(`input[name="status[]"][value="${status}"]`);
            if (checkbox) {
                checkbox.checked = true;
            }
        });

        // Date filter
        const dateAdded = urlParams.get('date_added');
        if (dateAdded) {
            const dateInput = document.getElementById('filter_date_added');
            if (dateInput) {
                dateInput.value = dateAdded;
            }
        }

        // Price range
        const priceMin = urlParams.get('price_min');
        const priceMax = urlParams.get('price_max');
        if (priceMin || priceMax) {
            const minInput = document.getElementById('minPriceInput');
            const maxInput = document.getElementById('maxPriceInput');
            if (minInput && priceMin) minInput.value = this.formatPrice(priceMin);
            if (maxInput && priceMax) maxInput.value = this.formatPrice(priceMax);
        }
    }

    async applyFilters() {
        if (this.isLoading || (window.productManager && window.productManager.isLoading)) {
            return;
        }

        this.isLoading = true;
        this.showLoading();

        try {
            const urlParams = this.collectFilters();
            urlParams.set('page', 1); // Reset to first page

            // Keep search parameter if exists
            const currentSearch = new URLSearchParams(window.location.search).get('search');
            if (currentSearch) {
                urlParams.set('search', currentSearch);
            }

            const response = await fetch(`${this.baseUrl}?${urlParams.toString()}`, {
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
            this.updateFilterCounts(data);

            // Update URL without page reload
            const newUrl = `${this.baseUrl}?${urlParams.toString()}`;
            window.history.pushState({}, '', newUrl);

            // Store current filters as object for reference
            this.currentFilters = Object.fromEntries(urlParams.entries());

        } catch (error) {
            console.error('Filter error:', error);
            this.showError('Có lỗi xảy ra khi lọc dữ liệu. Vui lòng thử lại.');
        } finally {
            this.isLoading = false;
            this.hideLoading();
        }
    }

    collectFilters() {
        const urlParams = new URLSearchParams();

        // Category filter
        const categorySelect = document.getElementById('filter_category');
        if (categorySelect && categorySelect.value) {
            urlParams.set('category_id', categorySelect.value);
        }

        // Stock status filters
        const stockCheckboxes = document.querySelectorAll('input[name="stock_status[]"]:checked');
        stockCheckboxes.forEach(checkbox => {
            urlParams.append('stock_status[]', checkbox.value);
        });

        // Product status filters
        const statusCheckboxes = document.querySelectorAll('input[name="status[]"]:checked');
        statusCheckboxes.forEach(checkbox => {
            urlParams.append('status[]', checkbox.value);
        });

        // Date filter
        const dateInput = document.getElementById('filter_date_added');
        if (dateInput && dateInput.value) {
            urlParams.set('date_added', dateInput.value);
        }

        // Price range
        if (window.priceSlider) {
            const priceValues = window.priceSlider.getValues();
            if (priceValues.min > window.priceSlider.min) {
                urlParams.set('price_min', priceValues.min);
            }
            if (priceValues.max < window.priceSlider.max) {
                urlParams.set('price_max', priceValues.max);
            }
        }

        return urlParams;
    }

    resetFilters() {
        // Clear form
        const form = document.getElementById('filterForm');
        if (form) {
            form.reset();
        }

        // Reset category to "Tất cả danh mục" (empty value)
        const categorySelect = document.getElementById('filter_category');
        if (categorySelect) {
            categorySelect.value = '';
        }

        // Clear all checkboxes explicitly
        const allCheckboxes = document.querySelectorAll('input[name="stock_status[]"], input[name="status[]"]');
        allCheckboxes.forEach(checkbox => {
            checkbox.checked = false;
        });

        // Reset price slider to full available range
        if (window.priceSlider) {
            // Get the original min/max from the slider's initial range
            const originalMin = window.priceSlider.min;
            const originalMax = window.priceSlider.max;
            window.priceSlider.reset();
        }

        // Apply empty filters
        this.applyFilters();
    }

    updateTable(html) {
        if (this.tableBody) {
            this.tableBody.innerHTML = html;

            // Reinitialize checkboxes and other components
            this.reinitializeComponents();
        }
    }

    updatePagination(paginationHtml) {
        if (this.paginationContainer && paginationHtml) {
            // Replace the entire pagination container with the new HTML
            this.paginationContainer.outerHTML = paginationHtml;
            // Re-get the pagination container reference after replacement
            this.paginationContainer = document.querySelector('.pagination-container');
            
            // Only override changePage if not already owned by another manager
            if (!window.changePage || window.changePage._owner !== 'product') {
                window.changePage = (page) => {
                    this.applyFiltersWithPage(page);
                };
                window.changePage._owner = 'filter';
            }
        }
    }

    async applyFiltersWithPage(page) {
        if (this.isLoading || (window.productManager && window.productManager.isLoading)) {
            return;
        }

        try {
            this.isLoading = true;
            this.showLoading();

            const urlParams = this.collectFilters();
            urlParams.set('page', page);

            // Keep search parameter if exists
            const currentSearch = new URLSearchParams(window.location.search).get('search');
            if (currentSearch) {
                urlParams.set('search', currentSearch);
            }

            const response = await fetch(`${this.baseUrl}?${urlParams.toString()}`, {
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
            this.updateFilterCounts(data);

            // Update URL without page reload
            const newUrl = `${this.baseUrl}?${urlParams.toString()}`;
            window.history.pushState({}, '', newUrl);

            // Store current filters as object for reference
            this.currentFilters = Object.fromEntries(urlParams.entries());

        } catch (error) {
            console.error('Pagination error:', error);
            this.showError('Có lỗi xảy ra khi chuyển trang. Vui lòng thử lại.');
        } finally {
            this.isLoading = false;
            this.hideLoading();
        }
    }

    updateFilterCounts(data) {
        // Update filter result count if element exists
        const resultCount = document.getElementById('filterResultCount');
        if (resultCount && data.total !== undefined) {
            resultCount.textContent = `${data.total} kết quả`;
        }

        // Only update price slider range if it's not currently being dragged
        // and if min_price and max_price are provided
        if (data.min_price !== undefined && data.max_price !== undefined) {
            if (window.priceSlider && !window.priceSlider.isDragging) {
                this.updatePriceSlider(data.min_price, data.max_price);
            }
        }
    }

    showLoading() {
        if (this.tableBody) {
            this.tableBody.style.opacity = '0.5';
            this.tableBody.style.pointerEvents = 'none';
        }

        // Add loading indicator to filter button
        const filterButton = document.querySelector('[onclick="toggleModal(\'filterModal\')"]');
        if (filterButton) {
            filterButton.classList.add('loading');
            filterButton.disabled = true;
        }
    }

    hideLoading() {
        if (this.tableBody) {
            this.tableBody.style.opacity = '1';
            this.tableBody.style.pointerEvents = 'auto';
        }

        // Remove loading indicator from filter button
        const filterButton = document.querySelector('[onclick="toggleModal(\'filterModal\')"]');
        if (filterButton) {
            filterButton.classList.remove('loading');
            filterButton.disabled = false;
        }
    }

    showError(message) {
        // Create or update error message
        let errorDiv = document.querySelector('.filter-error');
        if (!errorDiv) {
            errorDiv = document.createElement('div');
            errorDiv.className = 'filter-error bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4';
            if (this.tableBody && this.tableBody.parentNode) {
                this.tableBody.parentNode.insertBefore(errorDiv, this.tableBody);
            }
        }
        errorDiv.textContent = message;

        // Auto hide after 5 seconds
        setTimeout(() => {
            if (errorDiv) {
                errorDiv.remove();
            }
        }, 5000);
    }

    reinitializeComponents() {
        // Prevent duplicate initialization
        if (this._reinitializing) return;
        this._reinitializing = true;
        
        try {
            // Reinitialize checkboxes
            if (window.productManager && typeof window.productManager.reinitializeCheckboxes === 'function') {
                window.productManager.reinitializeCheckboxes();
            }

            // Reinitialize any other components that need refresh after AJAX update
            // This can be extended based on specific needs
        } finally {
            this._reinitializing = false;
        }
    }

    closeModal() {
        if (this.filterModal) {
            this.filterModal.classList.add('hidden');
        }
    }

    formatPrice(price) {
        return new Intl.NumberFormat('vi-VN').format(price);
    }

    // Public method to get current filters
    getCurrentFilters() {
        return {
            ...this.currentFilters
        };
    }

    // Update price range based on selected category
    async updatePriceRangeByCategory(categoryId) {
        if (!categoryId || !window.priceSlider) {
            // Reset to default range if no category selected
            if (!categoryId && window.priceSlider && !window.priceSlider.isDragging) {
                window.priceSlider.updateRange(0, 10000000);
            }
            return;
        }

        // Don't update range if user is currently dragging the slider
        if (window.priceSlider.isDragging) {
            return;
        }

        try {
            const response = await fetch(`/admin/products/price-range?category_id=${categoryId}`, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                }
            });

            if (response.ok) {
                const data = await response.json();
                console.log('Price range data:', data); // Debug log
                
                if (data.error) {
                    console.error('API returned error:', data.message);
                    return;
                }
                
                // Validate and use the data
                const minPrice = (data.min_price !== null && data.min_price !== undefined) ? data.min_price : 0;
                const maxPrice = (data.max_price !== null && data.max_price !== undefined) ? data.max_price : 10000000;
                
                window.priceSlider.updateRange(minPrice, maxPrice);
            } else {
                console.error('Failed to fetch price range:', response.status, response.statusText);
            }
        } catch (error) {
            console.error('Error fetching price range:', error);
        }
    }

    // Update price slider range
    updatePriceSlider(minPrice, maxPrice) {
        if (window.priceSlider && typeof window.priceSlider.updateRange === 'function') {
            window.priceSlider.updateRange(minPrice, maxPrice);
            console.log(`Price slider updated: ${minPrice} - ${maxPrice}`);
        } else {
            console.warn('Price slider not found or updateRange method not available');
        }
    }

    // Public method to set filters programmatically
    setFilters(filters) {
        Object.keys(filters).forEach(key => {
            const element = document.getElementById(`filter_${key}`) ||
                document.querySelector(`[name="${key}"]`) ||
                document.querySelector(`[name="${key}[]"]`);

            if (element) {
                if (element.type === 'checkbox') {
                    element.checked = filters[key];
                } else {
                    element.value = filters[key];
                }
            }
        });

        this.applyFilters();
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    window.filterManager = new FilterManager();
});

// Export for use in other scripts if needed
if (typeof module !== 'undefined' && module.exports) {
    module.exports = FilterManager;
}