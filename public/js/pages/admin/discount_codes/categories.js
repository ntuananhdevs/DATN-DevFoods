/**
 * Categories management module for discount codes
 * Handles category search and selection
 */

/**
 * Initialize category search functionality
 * @param {Object} options - Configuration options
 * @param {string} options.searchSelector - Selector for the category search input
 * @param {string} options.containerSelector - Selector for the category container
 */
export function initCategorySearch(options = {}) {
    const searchSelector = options.searchSelector || '#category_search';
    const containerSelector = options.containerSelector || '.category-container';
    
    const $searchInput = $(searchSelector);
    const $container = $(containerSelector);
    
    if (!$searchInput.length || !$container.length) {
        console.warn('Category search elements not found');
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
    
    // Handle category search with debounce
    $searchInput.on('input', debounce(function() {
        const searchTerm = $(this).val().toLowerCase();
        
        // Show loading indicator
        $container.html(`
            <div class="col-span-full p-4 text-center">
                <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div>
                <p class="mt-2 text-gray-500 dark:text-muted-foreground">Đang tìm kiếm danh mục...</p>
            </div>
        `);
        
        // Make AJAX request
        $.ajax({
            url: "/admin/discount_codes/get-items-by-type",
            type: 'POST',
            dataType: 'json',
            data: {
                type: 'categories',
                search: searchTerm,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(data) {
                if (data.success) {
                    loadCategories(data.items, $container);
                } else {
                    console.error('Error searching categories:', data.message);
                    $container.html(`
                        <div class="col-span-full p-4 text-center bg-red-50 dark:bg-red-950/20 rounded-lg">
                            <p class="text-red-500">Lỗi tìm kiếm: ${data.message || 'Không thể tìm kiếm danh mục'}</p>
                        </div>
                    `);
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX error:', error);
                $container.html(`
                    <div class="col-span-full p-4 text-center bg-red-50 dark:bg-red-950/20 rounded-lg">
                        <p class="text-red-500">Lỗi kết nối: Không thể tìm kiếm danh mục.</p>
                    </div>
                `);
            }
        });
    }, 500));
}

/**
 * Load and update categories list
 * @param {Array} selectedCategoryIds - Array of already selected category IDs
 * @param {string|jQuery} containerSelector - Selector or jQuery element for the category container
 */
export function loadCategories(selectedCategoryIds = [], containerSelector = '.category-container') {
    const $container = typeof containerSelector === 'string' ? $(containerSelector) : containerSelector;
    
    if (!$container.length) {
        console.warn('Category container not found');
        return;
    }
    
    // If categories are already provided, just render them
    if (Array.isArray(selectedCategoryIds) && !selectedCategoryIds.every(item => typeof item === 'number')) {
        updateCategoriesList(selectedCategoryIds, $container);
        return;
    }
    
    // Show loading indicator
    $container.html(`
        <div class="col-span-full p-4 text-center">
            <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div>
            <p class="mt-2 text-gray-500 dark:text-muted-foreground">Đang tải danh sách danh mục...</p>
        </div>
    `);
    
    // Make AJAX request
    $.ajax({
        url: "/admin/discount_codes/get-items-by-type",
        type: 'POST',
        dataType: 'json',
        data: {
            type: 'categories',
            search: '',
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        success: function(data) {
            if (data.success) {
                updateCategoriesList(data.items, $container, selectedCategoryIds);
            } else {
                console.error('Error loading categories:', data.message);
                $container.html(`
                    <div class="col-span-full p-4 text-center bg-red-50 dark:bg-red-950/20 rounded-lg">
                        <p class="text-red-500">Lỗi: Không thể tải danh sách danh mục.</p>
                    </div>
                `);
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX error:', error);
            $container.html(`
                <div class="col-span-full p-4 text-center bg-red-50 dark:bg-red-950/20 rounded-lg">
                    <p class="text-red-500">Lỗi kết nối: Không thể tải danh sách danh mục.</p>
                </div>
            `);
        }
    });
}

/**
 * Update categories list in the UI
 * @param {Array} items - Array of category items
 * @param {jQuery} $container - jQuery container element
 * @param {Array} selectedCategoryIds - Array of already selected category IDs
 */
function updateCategoriesList(items, $container, selectedCategoryIds = []) {
    if (!items || items.length === 0) {
        $container.html(`
            <div class="col-span-full p-4 text-center bg-gray-50 dark:bg-card rounded-lg">
                <p class="text-gray-500 dark:text-muted-foreground">Không tìm thấy danh mục.</p>
            </div>
        `);
        return;
    }
    
    let itemsHtml = '';
    
    items.forEach(item => {
        const badgeClass = 'bg-indigo-100 text-indigo-800 dark:bg-indigo-950 dark:text-indigo-200';
        const badgeIcon = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>';
        const badgeText = 'DM';
        
        // Check if item is already selected
        const itemId = parseInt(item.id);
        const isChecked = selectedCategoryIds.includes(itemId) ? 'checked' : '';
        
        itemsHtml += `
            <div class="checkbox-group hover:border-blue-500 hover:bg-blue-50 dark:hover:border-primary dark:hover:bg-primary/10 transition-colors relative">
                <span class="absolute top-0 right-0 inline-flex items-center px-2 py-1 rounded-bl text-xs font-medium ${badgeClass}">
                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        ${badgeIcon}
                    </svg>
                    ${badgeText}
                </span>
                <input type="checkbox" name="category_ids[]" id="category_${item.id}" value="${item.id}" ${isChecked}>
                <label for="category_${item.id}">
                    ${item.name}
                </label>
            </div>
        `;
    });
    
    $container.html(itemsHtml);
    
    // Add change event listeners
    $container.find('input[name="category_ids[]"]').on('change', function() {
        const categoryId = parseInt(this.value);
        const event = new CustomEvent('categorySelectionChanged', { 
            detail: { 
                categoryId: categoryId, 
                isChecked: this.checked 
            } 
        });
        document.dispatchEvent(event);
    });
} 