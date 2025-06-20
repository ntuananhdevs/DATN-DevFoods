/**
 * Discount Code Module Initialization
 * Main entry point for discount code creation page functionality
 */

import { initProductSearch, loadProducts } from './products.js';
import { initCategorySearch, loadCategories } from './categories.js';
import { initComboSearch, loadCombos } from './combos.js';
import { initVariantSearch, loadVariants } from './variants.js';
import { initUserSearch, initRankSelection, toggleRankExclusivity, fetchUsers } from './users.js';
import { 
    setupRadioToggle, 
    setupSelectAll, 
    setupUnselectAll,
    logSelectedItems
} from './utils.js';

// Main initialization function for discount code module
class DiscountCodeModule {
    constructor() {
        this.selectedProductIds = [];
        this.selectedCategoryIds = [];
        this.selectedComboIds = [];
        this.selectedVariantIds = [];
        this.selectedUserIds = [];
        
        // Initialize global window object to make functions accessible for event handlers
        window.discountCodeModule = {
            products: {
                loadProducts: (selectedIds) => loadProducts(selectedIds || this.selectedProductIds, '#products_selection .grid')
            },
            categories: {
                loadCategories: (selectedIds) => loadCategories(selectedIds || this.selectedCategoryIds, '#categories_selection .grid')
            },
            combos: {
                loadCombos: (selectedIds) => loadCombos(selectedIds || this.selectedComboIds, '#combos_selection .grid')
            },
            variants: {
                loadVariants: (selectedIds) => loadVariants(selectedIds || this.selectedVariantIds, '#variants_container')
            },
            users: {
                search: initUserSearch,
                initRankSelection: initRankSelection,
                toggleRankExclusivity: toggleRankExclusivity,
                fetchUsers: fetchUsers
            }
        };
    }
    
    // Initialize all functionality
    init() {
        // Parse initial selection data
        this.parseInitialSelections();
        
        // Initialize search functionality
        this.initSearchFunctions();
        
        // Setup radio toggle functionality
        this.initRadioToggles();
        
        // Setup selection button functionality
        this.initSelectionButtons();
        
        // Add event listeners for selection changes
        this.setupSelectionChangeListeners();
        
        // Initialize date validation
        this.initDateValidation();
        
        // Load initial data based on selected radio buttons
        this.loadInitialData();
    }
    
    // Parse initial selections from JSON data in the page
    parseInitialSelections() {
        try {
            // Products
            const selectedProductsScript = document.getElementById('selected_products_data');
            if (selectedProductsScript) {
                this.selectedProductIds = JSON.parse(selectedProductsScript.textContent || '[]').map(id => parseInt(id));
            } else {
                // Try to parse from inline JSON
                const selectedProductsJson = document.querySelector('[data-selected-products]')?.dataset.selectedProducts;
                if (selectedProductsJson) {
                    this.selectedProductIds = JSON.parse(selectedProductsJson || '[]').map(id => parseInt(id));
                }
            }
            
            // Categories
            const selectedCategoriesScript = document.getElementById('selected_categories_data');
            if (selectedCategoriesScript) {
                this.selectedCategoryIds = JSON.parse(selectedCategoriesScript.textContent || '[]').map(id => parseInt(id));
            } else {
                const selectedCategoriesJson = document.querySelector('[data-selected-categories]')?.dataset.selectedCategories;
                if (selectedCategoriesJson) {
                    this.selectedCategoryIds = JSON.parse(selectedCategoriesJson || '[]').map(id => parseInt(id));
                }
            }
            
            // Combos
            const selectedCombosScript = document.getElementById('selected_combos_data');
            if (selectedCombosScript) {
                this.selectedComboIds = JSON.parse(selectedCombosScript.textContent || '[]').map(id => parseInt(id));
            } else {
                const selectedCombosJson = document.querySelector('[data-selected-combos]')?.dataset.selectedCombos;
                if (selectedCombosJson) {
                    this.selectedComboIds = JSON.parse(selectedCombosJson || '[]').map(id => parseInt(id));
                }
            }
            
            // Variants
            const selectedVariantsScript = document.getElementById('selected_variants_data');
            if (selectedVariantsScript) {
                this.selectedVariantIds = JSON.parse(selectedVariantsScript.textContent || '[]').map(id => parseInt(id));
            } else {
                const selectedVariantsJson = document.querySelector('[data-selected-variants]')?.dataset.selectedVariants;
                if (selectedVariantsJson) {
                    this.selectedVariantIds = JSON.parse(selectedVariantsJson || '[]').map(id => parseInt(id));
                }
            }
            
            // Users
            const selectedUsersScript = document.getElementById('selected_users_data');
            if (selectedUsersScript) {
                this.selectedUserIds = JSON.parse(selectedUsersScript.textContent || '[]').map(id => parseInt(id));
            } else {
                const selectedUsersJson = document.querySelector('[data-selected-users]')?.dataset.selectedUsers;
                if (selectedUsersJson) {
                    this.selectedUserIds = JSON.parse(selectedUsersJson || '[]').map(id => parseInt(id));
                }
            }
            
            // Initial selections loaded
        } catch (e) {
            console.error('Error parsing initial selections:', e);
        }
    }
    
    // Initialize search functionality
    initSearchFunctions() {
        // Initialize product search
        initProductSearch({
            searchSelector: '#product_search',
            containerSelector: '#products_selection .grid'
        });
        
        // Initialize category search
        initCategorySearch({
            searchSelector: '#category_search',
            containerSelector: '#categories_selection .grid'
        });
        
        // Initialize combo search
        initComboSearch({
            searchSelector: '#combo_search',
            containerSelector: '#combos_selection .grid'
        });
        
        // Initialize variant search
        initVariantSearch({
            searchSelector: '#variant_search',
            containerSelector: '#variants_container'
        });
        
        // Initialize user search
        initUserSearch({
            searchSelector: '#user_search',
            containerSelector: '#users_selection .grid'
        });
        
        // Initialize rank selection
        initRankSelection({
            rankCheckboxSelector: 'input[name="applicable_ranks[]"]',
            containerSelector: '#users_selection .grid',
            countDisplaySelector: '#users_selection .text-xs.text-gray-500'
        });
    }
    
    // Set up radio button toggle behavior
    initRadioToggles() {
        // Setup applicable items radio toggle
        setupRadioToggle('applicable_items', {
            'all_items': '#all_items_selection',
            'specific_products': '#products_selection',
            'specific_categories': '#categories_selection',
            'combos_only': '#combos_selection',
            'specific_variants': '#variants_selection'
        });
        
        // Add specific event listener for variants radio button
        $('input[name="applicable_items"]').on('change', function() {
            if (this.value === 'specific_variants' && this.checked) {
                // Load variants when this option is selected
                loadVariants([], '#variants_container');
            }
        });
        
        // Setup applicable scope radio toggle
        setupRadioToggle('applicable_scope', {
            'all_branches': '',
            'specific_branches': '#branch_selection'
        });
        
        // Setup usage type selection
        const $usageTypeSelect = $('#usage_type');
        
        // Initial check
        if ($usageTypeSelect.val() === 'personal') {
            $('#users_selection').show();
        } else {
            $('#users_selection').hide();
        }
        
        // Add change event listener
        $usageTypeSelect.on('change', function() {
            const selectedValue = $(this).val();
            
            if (selectedValue === 'personal') {
                $('#users_selection').show();
                
                // Load user data when showing the section
                setTimeout(() => {
                    if ($('#users_selection').is(':visible')) {
                        // Try both ways to call fetchUsers
                        try {
                            window.discountCodeModule.users.fetchUsers();
                        } catch (e) {
                            try {
                                fetchUsers();
                            } catch (e2) {
                            }
                        }
                    }
                }, 300); // Increased timeout to ensure DOM is ready
            } else {
                $('#users_selection').hide();
            }
        });
        
        // Handle rank exclusivity toggle
        $('#rank_exclusive').on('change', function() {
            toggleRankExclusivity(this.checked);
        });
        
        // Add event listeners for rank checkboxes
        $('input[name="applicable_ranks[]"]').on('change', function() {
            // If usage type is personal and users section is visible, fetch users
            if ($('#usage_type').val() === 'personal' && $('#users_selection').is(':visible')) {
                window.discountCodeModule.users.fetchUsers();
            }
        });
    }
    
    // Set up selection buttons (select all/unselect all)
    initSelectionButtons() {
        // Products
        setupSelectAll('.select-all-products', 'input[name="product_ids[]"]');
        setupUnselectAll('.unselect-all-products', 'input[name="product_ids[]"]');
        
        // Categories
        setupSelectAll('.select-all-categories', 'input[name="category_ids[]"]');
        setupUnselectAll('.unselect-all-categories', 'input[name="category_ids[]"]');
        
        // Combos
        setupSelectAll('.select-all-combos', 'input[name="combo_ids[]"]');
        setupUnselectAll('.unselect-all-combos', 'input[name="combo_ids[]"]');
        
        // Variants
        setupSelectAll('.select-all-variants', 'input[name="variant_ids[]"]');
        setupUnselectAll('.unselect-all-variants', 'input[name="variant_ids[]"]');
        
        // Branches
        setupSelectAll('.select-all-branches', 'input[name="branch_ids[]"]');
        setupUnselectAll('.unselect-all-branches', 'input[name="branch_ids[]"]');
        
        // Users
        setupSelectAll('.select-all-users', 'input[name="assigned_users[]"]');
        setupUnselectAll('.unselect-all-users', 'input[name="assigned_users[]"]');
    }
    
    // Set up event listeners for selection changes
    setupSelectionChangeListeners() {
        // Product selection changes
        document.addEventListener('productSelectionChanged', (event) => {
            const { productId, isChecked } = event.detail;
            
            if (isChecked && !this.selectedProductIds.includes(productId)) {
                this.selectedProductIds.push(productId);
            } else if (!isChecked) {
                this.selectedProductIds = this.selectedProductIds.filter(id => id !== productId);
            }
            
            logSelectedItems('products', this.selectedProductIds);
        });
        
        // Category selection changes
        document.addEventListener('categorySelectionChanged', (event) => {
            const { categoryId, isChecked } = event.detail;
            
            if (isChecked && !this.selectedCategoryIds.includes(categoryId)) {
                this.selectedCategoryIds.push(categoryId);
            } else if (!isChecked) {
                this.selectedCategoryIds = this.selectedCategoryIds.filter(id => id !== categoryId);
            }
            
            logSelectedItems('categories', this.selectedCategoryIds);
        });
        
        // Combo selection changes
        document.addEventListener('comboSelectionChanged', (event) => {
            const { comboId, isChecked } = event.detail;
            
            if (isChecked && !this.selectedComboIds.includes(comboId)) {
                this.selectedComboIds.push(comboId);
            } else if (!isChecked) {
                this.selectedComboIds = this.selectedComboIds.filter(id => id !== comboId);
            }
            
            logSelectedItems('combos', this.selectedComboIds);
        });
        
        // Variant selection changes
        document.addEventListener('variantSelectionChanged', (event) => {
            const { variantId, isChecked } = event.detail;
            
            if (isChecked && !this.selectedVariantIds.includes(variantId)) {
                this.selectedVariantIds.push(variantId);
            } else if (!isChecked) {
                this.selectedVariantIds = this.selectedVariantIds.filter(id => id !== variantId);
            }
            
            logSelectedItems('variants', this.selectedVariantIds);
        });
        
        // User selection changes
        document.addEventListener('userSelectionChanged', (event) => {
            const { userId, isChecked } = event.detail;
            
            if (isChecked && !this.selectedUserIds.includes(userId)) {
                this.selectedUserIds.push(userId);
            } else if (!isChecked) {
                this.selectedUserIds = this.selectedUserIds.filter(id => id !== userId);
            }
            
            logSelectedItems('users', this.selectedUserIds);
        });
    }
    
    // Initialize date validation for start and end dates
    initDateValidation() {
        const startDateField = document.getElementById('start_date');
        const endDateField = document.getElementById('end_date');
        
        if (!startDateField || !endDateField) return;
        
        function validateDates() {
            const startDate = new Date(startDateField.value);
            const endDate = new Date(endDateField.value);
            
            if (endDate < startDate) {
                alert('Ngày kết thúc không thể trước ngày bắt đầu!');
                // Set end date to start date + 30 days
                const newEndDate = new Date(startDate);
                newEndDate.setDate(newEndDate.getDate() + 30);
                
                // Format the date for datetime-local input
                const year = newEndDate.getFullYear();
                const month = String(newEndDate.getMonth() + 1).padStart(2, '0');
                const day = String(newEndDate.getDate()).padStart(2, '0');
                const hours = String(newEndDate.getHours()).padStart(2, '0');
                const minutes = String(newEndDate.getMinutes()).padStart(2, '0');
                
                endDateField.value = `${year}-${month}-${day}T${hours}:${minutes}`;
            }
        }
        
        startDateField.addEventListener('change', validateDates);
        endDateField.addEventListener('change', validateDates);
    }
    
    // Load initial data
    loadInitialData() {
        // Load products
        loadProducts(this.selectedProductIds, '#products_selection .grid');
        
        // Load categories
        loadCategories(this.selectedCategoryIds, '#categories_selection .grid');
        
        // Load combos
        loadCombos(this.selectedComboIds, '#combos_selection .grid');
        
        // Load variants
        loadVariants(this.selectedVariantIds, '#variants_container');
        
        // Initialize users - this is handled by initRankSelection which is called in initSearchFunctions
        // We don't need to call fetchUsers() here as it's automatically called by initRankSelection
        
        // But if we're in personal mode, make sure users are loaded
        if ($('#usage_type').val() === 'personal' && $('#users_selection').is(':visible')) {
            // Make sure to call fetchUsers with a slight delay to ensure DOM is ready
            setTimeout(() => {
                window.discountCodeModule.users.fetchUsers();
            }, 300);
        }
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    const discountCodeModule = new DiscountCodeModule();
    discountCodeModule.init();
}); 