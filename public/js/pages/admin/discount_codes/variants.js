/**
 * Product Variants management module for discount codes
 * Handles loading and searching product variants
 */

/**
 * Initialize variant search functionality
 * @param {Object} options - Configuration options
 * @param {string} options.searchSelector - Selector for the variant search input
 * @param {string} options.containerSelector - Selector for the variant container
 */
export function initVariantSearch(options = {}) {
    const searchSelector = options.searchSelector || '#variant_search';
    const containerSelector = options.containerSelector || '#variants_container';
    
    const $searchInput = $(searchSelector);
    const $container = $(containerSelector);
    
    if (!$searchInput.length || !$container.length) {
        console.warn('Variant search elements not found');
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
    
    // Handle variant search with debounce
    $searchInput.on('input', debounce(function() {
        const searchTerm = $(this).val().toLowerCase();
        
        // Show loading indicator
        $container.html(`
            <div class="col-span-full p-4 text-center">
                <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div>
                <p class="mt-2 text-gray-500 dark:text-muted-foreground">Đang tìm kiếm biến thể sản phẩm...</p>
            </div>
        `);
        
        // Make AJAX request
        $.ajax({
            url: "/admin/discount_codes/get-items-by-type",
            type: 'POST',
            dataType: 'json',
            data: {
                type: 'variants',
                search: searchTerm,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(data) {
                if (data.success) {
                    loadVariants(data.items, $container);
                } else {
                    console.error('Error searching variants:', data.message);
                    $container.html(`
                        <div class="col-span-full p-4 text-center bg-red-50 dark:bg-red-950/20 rounded-lg">
                            <p class="text-red-500">Lỗi tìm kiếm: ${data.message || 'Không thể tìm kiếm biến thể sản phẩm'}</p>
                        </div>
                    `);
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX error:', error);
                $container.html(`
                    <div class="col-span-full p-4 text-center bg-red-50 dark:bg-red-950/20 rounded-lg">
                        <p class="text-red-500">Lỗi kết nối: Không thể tìm kiếm biến thể sản phẩm.</p>
                    </div>
                `);
            }
        });
    }, 500));
}

/**
 * Load and update product variants list
 * @param {Array} selectedVariantIds - Array of already selected variant IDs
 * @param {string|jQuery} containerSelector - Selector or jQuery element for the variants container
 */
export function loadVariants(selectedVariantIds = [], containerSelector = '#variants_container') {
    const $container = typeof containerSelector === 'string' ? $(containerSelector) : containerSelector;
    
    if (!$container.length) {
        console.warn('Variants container not found');
        return;
    }
    
    // If variants are already provided, just render them
    if (Array.isArray(selectedVariantIds) && !selectedVariantIds.every(item => typeof item === 'number')) {
        updateVariantsList(selectedVariantIds, $container);
        return;
    }
    
    // Show loading indicator
    $container.html(`
        <div class="col-span-full p-4 text-center">
            <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div>
            <p class="mt-2 text-gray-500 dark:text-muted-foreground">Đang tải danh sách biến thể sản phẩm...</p>
        </div>
    `);
    
    // Make AJAX request
    $.ajax({
        url: "/admin/discount_codes/get-items-by-type",
        type: 'POST',
        dataType: 'json',
        data: {
            type: 'variants',
            search: '',
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(data) {
            if (data.success) {
                updateVariantsList(data.items, $container, selectedVariantIds);
            } else {
                console.error('Error loading variants:', data.message);
                $container.html(`
                    <div class="col-span-full p-4 text-center bg-red-50 dark:bg-red-950/20 rounded-lg">
                        <p class="text-red-500">Lỗi: Không thể tải danh sách biến thể sản phẩm.</p>
                    </div>
                `);
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX error:', error);
            $container.html(`
                <div class="col-span-full p-4 text-center bg-red-50 dark:bg-red-950/20 rounded-lg">
                    <p class="text-red-500">Lỗi kết nối: Không thể tải danh sách biến thể sản phẩm.</p>
                    <button class="mt-2 px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600" onclick="window.discountCodeModule.variants.loadVariants()">
                        Thử lại
                    </button>
                </div>
            `);
        }
    });
}

/**
 * Update product variants list in the UI
 * @param {Array} items - Array of variant items
 * @param {jQuery} $container - jQuery container element
 * @param {Array} selectedVariantIds - Array of already selected variant IDs
 */
function updateVariantsList(items, $container, selectedVariantIds = []) {
    if (!items || items.length === 0) {
        $container.html(`
            <div class="col-span-full p-4 text-center bg-gray-50 dark:bg-card rounded-lg">
                <p class="text-gray-500 dark:text-muted-foreground">Không tìm thấy biến thể sản phẩm nào.</p>
            </div>
        `);
        return;
    }
    
    let variantsHtml = '';
    
    items.forEach(variant => {
        const itemId = parseInt(variant.id);
        const isChecked = selectedVariantIds.includes(itemId) ? 'checked' : '';
        const variantPrice = parseFloat(variant.price || 0).toLocaleString();
        
        variantsHtml += `
            <div class="checkbox-group hover:border-blue-500 hover:bg-blue-50 dark:hover:border-primary dark:hover:bg-primary/10 transition-colors relative">
                <span class="absolute top-0 right-0 inline-flex items-center px-2 py-1 rounded-bl text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-950 dark:text-blue-200">
                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path>
                    </svg>
                    BT
                </span>
                <input type="checkbox" name="variant_ids[]" id="variant_${variant.id}" value="${variant.id}" ${isChecked}>
                <label for="variant_${variant.id}" class="flex flex-col">
                    <span class="font-medium">${variant.product_name || 'Sản phẩm không xác định'}</span>
                    <span class="text-xs text-blue-600">${variant.variant_description || 'Không có mô tả'}</span>
                    <div class="flex items-center justify-between mt-1">
                        <span class="text-xs text-gray-500">${variantPrice} đ</span>
                        ${variant.sku ? `<span class="text-xs text-gray-500">SKU: ${variant.sku}</span>` : ''}
                    </div>
                </label>
            </div>
        `;
    });
    
    $container.html(variantsHtml);
    
    // Add change event listeners
    $container.find('input[name="variant_ids[]"]').on('change', function() {
        const variantId = parseInt(this.value);
        const event = new CustomEvent('variantSelectionChanged', { 
            detail: { 
                variantId: variantId, 
                isChecked: this.checked 
            } 
        });
        document.dispatchEvent(event);
    });
} 