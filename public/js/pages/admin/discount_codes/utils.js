/**
 * Utility functions for discount codes management
 */

/**
 * Create a debounced function to limit API calls
 * @param {Function} func - Function to debounce
 * @param {number} wait - Wait time in milliseconds
 * @returns {Function} - Debounced function
 */
export function debounce(func, wait) {
    let timeout;
    return function(...args) {
        const context = this;
        clearTimeout(timeout);
        timeout = setTimeout(() => func.apply(context, args), wait);
    };
}

/**
 * Log selected items (for debugging)
 * @param {string} type - Type of item (products, categories, combos, variants)
 * @param {Array} selectedIds - Array of selected IDs
 */
export function logSelectedItems(type, selectedIds) {
    // No logging needed
}

/**
 * Toggle visibility of a DOM element
 * @param {string} selector - Element selector
 * @param {boolean} show - Whether to show the element
 */
export function toggleVisibility(selector, show) {
    const element = document.querySelector(selector);
    if (element) {
        element.style.display = show ? 'block' : 'none';
    }
}

/**
 * Add "Select All" functionality to checkboxes
 * @param {string} triggerSelector - Selector for the "Select All" button
 * @param {string} checkboxSelector - Selector for checkboxes
 */
export function setupSelectAll(triggerSelector, checkboxSelector) {
    const triggerElement = document.querySelector(triggerSelector);
    if (!triggerElement) return;
    
    triggerElement.addEventListener('click', function() {
        const checkboxes = document.querySelectorAll(checkboxSelector);
        checkboxes.forEach(checkbox => checkbox.checked = true);
        
        // Dispatch change events to update selected items arrays
        checkboxes.forEach(checkbox => {
            const changeEvent = new Event('change', { bubbles: true });
            checkbox.dispatchEvent(changeEvent);
        });
    });
}

/**
 * Add "Unselect All" functionality to checkboxes
 * @param {string} triggerSelector - Selector for the "Unselect All" button
 * @param {string} checkboxSelector - Selector for checkboxes
 */
export function setupUnselectAll(triggerSelector, checkboxSelector) {
    const triggerElement = document.querySelector(triggerSelector);
    if (!triggerElement) return;
    
    triggerElement.addEventListener('click', function() {
        const checkboxes = document.querySelectorAll(checkboxSelector);
        checkboxes.forEach(checkbox => checkbox.checked = false);
        
        // Dispatch change events to update selected items arrays
        checkboxes.forEach(checkbox => {
            const changeEvent = new Event('change', { bubbles: true });
            checkbox.dispatchEvent(changeEvent);
        });
    });
}

/**
 * Setup radio button group to toggle visibility of related content
 * @param {string} radioGroupName - Name attribute of the radio button group
 * @param {Object} contentMap - Map of radio values to content selectors
 * @returns {Object} - The radio buttons and update function
 */
export function setupRadioToggle(radioGroupName, contentMap) {
    const radios = document.querySelectorAll(`input[name="${radioGroupName}"]`);
    
    if (!radios.length) {
        return { radios: [], update: () => {} };
    }
    
    function updateVisibility() {
        const checkedRadio = document.querySelector(`input[name="${radioGroupName}"]:checked`);
        const selectedValue = checkedRadio?.value;
        
        // Hide all content first
        Object.entries(contentMap).forEach(([value, selector]) => {
            // Skip empty selectors
            if (!selector) return;
            
            const element = document.querySelector(selector);
            if (element) {
                toggleVisibility(selector, false);
            }
        });
        
        // Show content for selected value
        if (selectedValue && contentMap[selectedValue]) {
            // Skip empty selectors
            if (contentMap[selectedValue]) {
                toggleVisibility(contentMap[selectedValue], true);
            }
            
            // Trigger an event for this specific toggle
            const event = new CustomEvent(`${radioGroupName}Changed`, { 
                detail: { value: selectedValue, selector: contentMap[selectedValue] } 
            });
            document.dispatchEvent(event);
        }
    }
    
    // Add change event listeners
    radios.forEach(radio => {
        radio.addEventListener('change', updateVisibility);
    });
    
    // Initial update
    updateVisibility();
    
    // Return the radios and update function for external use
    return { radios, updateVisibility };
} 