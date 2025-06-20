/**
 * Combos management module for discount codes
 * Handles combo search and selection
 */

/**
 * Initialize combo search functionality
 * @param {Object} options - Configuration options
 * @param {string} options.searchSelector - Selector for the combo search input
 * @param {string} options.containerSelector - Selector for the combo container
 */
export function initComboSearch(options = {}) {
    const searchSelector = options.searchSelector || '#combo_search';
    const containerSelector = options.containerSelector || '.combo-container';
    
    const $searchInput = $(searchSelector);
    const $container = $(containerSelector);
    
    if (!$searchInput.length || !$container.length) {
        console.warn('Combo search elements not found');
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
    
    // Handle combo search with debounce
    $searchInput.on('input', debounce(function() {
        const searchTerm = $(this).val().toLowerCase();
        
        // Show loading indicator
        $container.html(`
            <div class="col-span-full p-4 text-center">
                <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div>
                <p class="mt-2 text-gray-500 dark:text-muted-foreground">Đang tìm kiếm combo...</p>
            </div>
        `);
        
        // Make AJAX request
        $.ajax({
            url: "/admin/discount_codes/get-items-by-type",
            type: 'POST',
            dataType: 'json',
            data: {
                type: 'combos',
                search: searchTerm,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(data) {
                if (data.success) {
                    loadCombos(data.items, $container);
                } else {
                    console.error('Error searching combos:', data.message);
                    $container.html(`
                        <div class="col-span-full p-4 text-center bg-red-50 dark:bg-red-950/20 rounded-lg">
                            <p class="text-red-500">Lỗi tìm kiếm: ${data.message || 'Không thể tìm kiếm combo'}</p>
                        </div>
                    `);
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX error:', error);
                $container.html(`
                    <div class="col-span-full p-4 text-center bg-red-50 dark:bg-red-950/20 rounded-lg">
                        <p class="text-red-500">Lỗi kết nối: Không thể tìm kiếm combo.</p>
                    </div>
                `);
            }
        });
    }, 500));
}

/**
 * Load and update combos list
 * @param {Array} selectedComboIds - Array of already selected combo IDs
 * @param {string|jQuery} containerSelector - Selector or jQuery element for the combo container
 */
export function loadCombos(selectedComboIds = [], containerSelector = '.combo-container') {
    const $container = typeof containerSelector === 'string' ? $(containerSelector) : containerSelector;
    
    if (!$container.length) {
        console.warn('Combo container not found');
        return;
    }
    
    // If combos are already provided, just render them
    if (Array.isArray(selectedComboIds) && !selectedComboIds.every(item => typeof item === 'number')) {
        updateCombosList(selectedComboIds, $container);
        return;
    }
    
    // Show loading indicator
    $container.html(`
        <div class="col-span-full p-4 text-center">
            <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div>
            <p class="mt-2 text-gray-500 dark:text-muted-foreground">Đang tải danh sách combo...</p>
        </div>
    `);
    
    // Make AJAX request
    $.ajax({
        url: "/admin/discount_codes/get-items-by-type",
        type: 'POST',
        dataType: 'json',
        data: {
            type: 'combos',
            search: '',
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        success: function(data) {
            if (data.success) {
                updateCombosList(data.items, $container, selectedComboIds);
            } else {
                console.error('Error loading combos:', data.message);
                $container.html(`
                    <div class="col-span-full p-4 text-center bg-red-50 dark:bg-red-950/20 rounded-lg">
                        <p class="text-red-500">Lỗi: Không thể tải danh sách combo.</p>
                    </div>
                `);
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX error:', error);
            $container.html(`
                <div class="col-span-full p-4 text-center bg-red-50 dark:bg-red-950/20 rounded-lg">
                    <p class="text-red-500">Lỗi kết nối: Không thể tải danh sách combo.</p>
                </div>
            `);
        }
    });
}

/**
 * Update combos list in the UI
 * @param {Array} items - Array of combo items
 * @param {jQuery} $container - jQuery container element
 * @param {Array} selectedComboIds - Array of already selected combo IDs
 */
function updateCombosList(items, $container, selectedComboIds = []) {
    if (!items || items.length === 0) {
        $container.html(`
            <div class="col-span-full p-4 text-center bg-gray-50 dark:bg-card rounded-lg">
                <p class="text-gray-500 dark:text-muted-foreground">Không tìm thấy combo.</p>
            </div>
        `);
        return;
    }
    
    let itemsHtml = '';
    
    items.forEach(item => {
        const badgeClass = 'bg-purple-100 text-purple-800 dark:bg-purple-950 dark:text-purple-200';
        const badgeIcon = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>';
        const badgeText = 'Combo';
        
        // Check if item is already selected
        const itemId = parseInt(item.id);
        const isChecked = selectedComboIds.includes(itemId) ? 'checked' : '';
        
        itemsHtml += `
            <div class="checkbox-group hover:border-blue-500 hover:bg-blue-50 dark:hover:border-primary dark:hover:bg-primary/10 transition-colors relative">
                <span class="absolute top-0 right-0 inline-flex items-center px-2 py-1 rounded-bl text-xs font-medium ${badgeClass}">
                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        ${badgeIcon}
                    </svg>
                    ${badgeText}
                </span>
                <input type="checkbox" name="combo_ids[]" id="combo_${item.id}" value="${item.id}" ${isChecked}>
                <label for="combo_${item.id}">
                    ${item.name}
                    ${item.price ? `<span class="text-xs text-gray-500 block">${parseFloat(item.price).toLocaleString()} đ</span>` : ''}
                </label>
            </div>
        `;
    });
    
    $container.html(itemsHtml);
    
    // Add change event listeners
    $container.find('input[name="combo_ids[]"]').on('change', function() {
        const comboId = parseInt(this.value);
        const event = new CustomEvent('comboSelectionChanged', { 
            detail: { 
                comboId: comboId, 
                isChecked: this.checked 
            } 
        });
        document.dispatchEvent(event);
    });
} 