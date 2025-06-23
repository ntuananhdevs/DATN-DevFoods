/**
 * Products management module for discount codes
 * Handles product search, selection and loading variants
 */

/**
 * Initialize product search functionality
 * @param {Object} options - Configuration options
 * @param {string} options.searchSelector - Selector for the product search input
 * @param {string} options.containerSelector - Selector for the product container
 */
export function initProductSearch(options = {}) {
    const searchSelector = options.searchSelector || '#product_search';
    const containerSelector = options.containerSelector || '.product-container';
    
    const $searchInput = $(searchSelector);
    const $container = $(containerSelector);
    
    if (!$searchInput.length || !$container.length) {
        console.warn('Product search elements not found');
        return;
    }
    
    // Debounce function to limit API calls
    function debounce(func, wait) {
        let timeout;
        return function(...args) {
            const context = this;
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(context, args), wait);
        };
    }
    
    // Handle product search with debounce
    $searchInput.on('input', debounce(function() {
        const searchTerm = $(this).val().toLowerCase();
        
        // Show loading indicator
        $container.html(`
            <div class="col-span-full p-4 text-center">
                <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div>
                <p class="mt-2 text-gray-500 dark:text-muted-foreground">Đang tìm kiếm sản phẩm...</p>
            </div>
        `);
        
        // Make AJAX request
        $.ajax({
            url: "/admin/discount_codes/get-items-by-type",
            type: 'POST',
            dataType: 'json',
            data: {
                type: 'products',
                search: searchTerm,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(data) {
                if (data.success) {
                    updateProductsList(data.items, $container);
                } else {
                    console.error('Error searching products:', data.message);
                    $container.html(`
                        <div class="col-span-full p-4 text-center bg-red-50 dark:bg-red-950/20 rounded-lg">
                            <p class="text-red-500">Lỗi tìm kiếm: ${data.message || 'Không thể tìm kiếm sản phẩm'}</p>
                        </div>
                    `);
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX error:', error);
                $container.html(`
                    <div class="col-span-full p-4 text-center bg-red-50 dark:bg-red-950/20 rounded-lg">
                        <p class="text-red-500">Lỗi kết nối: Không thể tìm kiếm sản phẩm.</p>
                    </div>
                `);
            }
        });
    }, 500));
}

/**
 * Load and update products list
 * @param {Array} selectedProductIds - Array of already selected product IDs
 * @param {string} containerSelector - Selector for the product container
 */
export function loadProducts(selectedProductIds = [], containerSelector = '.product-container') {
    const $container = $(containerSelector);
    
    if (!$container.length) {
        console.warn('Product container not found');
        return;
    }
    
    // Show loading indicator
    $container.html(`
        <div class="col-span-full p-4 text-center">
            <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div>
            <p class="mt-2 text-gray-500 dark:text-muted-foreground">Đang tải danh sách sản phẩm...</p>
        </div>
    `);
    
    // Make AJAX request
    $.ajax({
        url: "/admin/discount_codes/get-items-by-type",
        type: 'POST',
        dataType: 'json',
        data: {
            type: 'products',
            search: '',
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        success: function(data) {
            if (data.success) {
                updateProductsList(data.items, $container, selectedProductIds);
            } else {
                console.error('Error loading products:', data.message);
                $container.html(`
                    <div class="col-span-full p-4 text-center bg-red-50 dark:bg-red-950/20 rounded-lg">
                        <p class="text-red-500">Lỗi: Không thể tải danh sách sản phẩm.</p>
                    </div>
                `);
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX error:', error);
            $container.html(`
                <div class="col-span-full p-4 text-center bg-red-50 dark:bg-red-950/20 rounded-lg">
                    <p class="text-red-500">Lỗi kết nối: Không thể tải danh sách sản phẩm.</p>
                </div>
            `);
        }
    });
}

/**
 * Update products list in the UI
 * @param {Array} items - Array of product items
 * @param {jQuery} $container - jQuery container element
 * @param {Array} selectedProductIds - Array of already selected product IDs
 */
function updateProductsList(items, $container, selectedProductIds = []) {
    if (!items || items.length === 0) {
        $container.html(`
            <div class="col-span-full p-4 text-center bg-gray-50 dark:bg-card rounded-lg">
                <p class="text-gray-500 dark:text-muted-foreground">Không tìm thấy sản phẩm.</p>
            </div>
        `);
        return;
    }
    
    let itemsHtml = '';
    
    items.forEach(item => {
        const badgeClass = 'bg-green-100 text-green-800 dark:bg-green-950 dark:text-green-200';
        const badgeIcon = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>';
        const badgeText = 'SP';
        
        // Check if item is already selected
        const itemId = parseInt(item.id);
        const isChecked = selectedProductIds.includes(itemId) ? 'checked' : '';
        
        itemsHtml += `
            <div class="checkbox-group hover:border-blue-500 hover:bg-blue-50 dark:hover:border-primary dark:hover:bg-primary/10 transition-colors relative">
                <span class="absolute top-0 right-0 inline-flex items-center px-2 py-1 rounded-bl text-xs font-medium ${badgeClass}">
                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        ${badgeIcon}
                    </svg>
                    ${badgeText}
                </span>
                <input type="checkbox" name="product_ids[]" id="product_${item.id}" value="${item.id}" ${isChecked}>
                <label for="product_${item.id}">
                    ${item.name}
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-gray-500 block">${parseFloat(item.price || 0).toLocaleString()} đ</span>
                        ${item.variant_count !== undefined ? 
                            `<span class="text-xs bg-blue-100 text-blue-800 dark:bg-blue-950 dark:text-blue-200 px-2 py-0.5 rounded-full">
                                ${item.variant_count} biến thể
                            </span>` : ''
                        }
                    </div>
                    ${item.short_description ? `<span class="text-xs text-gray-500 block mt-1 italic">${item.short_description}</span>` : ''}
                </label>
            </div>
        `;
    });
    
    $container.html(itemsHtml);
    
    // Add change event listeners
    $container.find('input[name="product_ids[]"]').on('change', function() {
        const productId = parseInt(this.value);
        const event = new CustomEvent('productSelectionChanged', { 
            detail: { 
                productId: productId, 
                isChecked: this.checked 
            } 
        });
        document.dispatchEvent(event);
    });
} 