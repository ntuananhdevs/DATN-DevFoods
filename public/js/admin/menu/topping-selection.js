/**
 * Topping Selection JavaScript for Product Creation
 * Handles AJAX requests to load and manage toppings
 */

class ToppingSelection {
    constructor() {
        this.toppings = [];
        this.selectedToppings = [];
        this.init();
    }

    init() {
        this.loadToppings();
        this.bindEvents();
    }

    /**
     * Load active toppings from server
     */
    async loadToppings() {
        try {
            const response = await fetch('/admin/products/get-toppings', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            const data = await response.json();
            
            if (data.success) {
                this.toppings = data.data;
                this.renderToppings();
            } else {
                this.showError('Không thể tải danh sách topping');
            }
        } catch (error) {
            console.error('Error loading toppings:', error);
            this.showError('Lỗi kết nối khi tải topping');
        }
    }

    /**
     * Render toppings list
     */
    renderToppings() {
        const container = document.getElementById('toppings-list');
        if (!container) return;

        container.innerHTML = '';

        if (this.toppings.length === 0) {
            container.innerHTML = `
                <div class="col-12">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Chưa có topping nào được tạo hoặc kích hoạt.
                    </div>
                </div>
            `;
            return;
        }

        this.toppings.forEach(topping => {
            const toppingCard = this.createToppingCard(topping);
            container.appendChild(toppingCard);
        });
    }

    /**
     * Create topping card element
     */
    createToppingCard(topping) {
        const col = document.createElement('div');
        col.className = 'col-md-6 col-lg-4 mb-3';

        const isSelected = this.selectedToppings.includes(topping.id);
        
        col.innerHTML = `
            <div class="card topping-card ${isSelected ? 'selected' : ''}" data-topping-id="${topping.id}">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center">
                        <div class="form-check me-3">
                            <input class="form-check-input topping-checkbox" 
                                   type="checkbox" 
                                   id="topping_${topping.id}" 
                                   value="${topping.id}"
                                   ${isSelected ? 'checked' : ''}>
                        </div>
                        
                        <div class="topping-image me-3">
                            ${topping.image_url ? 
                                `<img src="${topping.image_url}" alt="${topping.name}" class="rounded" width="50" height="50">` :
                                `<div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                    <i class="fas fa-image text-muted"></i>
                                </div>`
                            }
                        </div>
                        
                        <div class="topping-info flex-grow-1">
                            <h6 class="mb-1">${topping.name}</h6>
                            <small class="text-primary fw-bold">${topping.formatted_price}</small>
                        </div>
                    </div>
                </div>
            </div>
        `;

        return col;
    }

    /**
     * Bind events
     */
    bindEvents() {
        // Handle topping selection
        document.addEventListener('change', (e) => {
            if (e.target.classList.contains('topping-checkbox')) {
                this.handleToppingSelection(e.target);
            }
        });

        // Handle card click
        document.addEventListener('click', (e) => {
            const card = e.target.closest('.topping-card');
            if (card) {
                const checkbox = card.querySelector('.topping-checkbox');
                if (e.target !== checkbox) {
                    checkbox.checked = !checkbox.checked;
                    this.handleToppingSelection(checkbox);
                }
            }
        });

        // Handle select all button
        const selectAllBtn = document.getElementById('select-all-toppings');
        if (selectAllBtn) {
            selectAllBtn.addEventListener('click', () => this.selectAllToppings());
        }

        // Handle clear all button
        const clearAllBtn = document.getElementById('clear-all-toppings');
        if (clearAllBtn) {
            clearAllBtn.addEventListener('click', () => this.clearAllToppings());
        }
    }

    /**
     * Handle topping selection
     */
    handleToppingSelection(checkbox) {
        const toppingId = parseInt(checkbox.value);
        const card = checkbox.closest('.topping-card');

        if (checkbox.checked) {
            if (!this.selectedToppings.includes(toppingId)) {
                this.selectedToppings.push(toppingId);
            }
            card.classList.add('selected');
        } else {
            this.selectedToppings = this.selectedToppings.filter(id => id !== toppingId);
            card.classList.remove('selected');
        }

        this.updateSelectedCount();
        this.updateHiddenInput();
    }

    /**
     * Select all toppings
     */
    selectAllToppings() {
        const checkboxes = document.querySelectorAll('.topping-checkbox');
        checkboxes.forEach(checkbox => {
            if (!checkbox.checked) {
                checkbox.checked = true;
                this.handleToppingSelection(checkbox);
            }
        });
    }

    /**
     * Clear all selections
     */
    clearAllToppings() {
        const checkboxes = document.querySelectorAll('.topping-checkbox');
        checkboxes.forEach(checkbox => {
            if (checkbox.checked) {
                checkbox.checked = false;
                this.handleToppingSelection(checkbox);
            }
        });
    }

    /**
     * Update selected count display
     */
    updateSelectedCount() {
        const countElement = document.getElementById('selected-toppings-count');
        if (countElement) {
            countElement.textContent = this.selectedToppings.length;
        }
    }

    /**
     * Update hidden input for form submission
     */
    updateHiddenInput() {
        let hiddenInput = document.getElementById('selected-toppings-input');
        
        if (!hiddenInput) {
            hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.id = 'selected-toppings-input';
            hiddenInput.name = 'toppings';
            document.querySelector('form').appendChild(hiddenInput);
        }

        hiddenInput.value = JSON.stringify(this.selectedToppings);
    }

    /**
     * Show error message
     */
    showError(message) {
        const container = document.getElementById('toppings-list');
        if (container) {
            container.innerHTML = `
                <div class="col-12">
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        ${message}
                    </div>
                </div>
            `;
        }
    }

    /**
     * Get selected toppings
     */
    getSelectedToppings() {
        return this.selectedToppings;
    }

    /**
     * Set selected toppings (for editing)
     */
    setSelectedToppings(toppingIds) {
        this.selectedToppings = toppingIds || [];
        this.updateSelectedCount();
        this.updateHiddenInput();
        
        // Update checkboxes
        const checkboxes = document.querySelectorAll('.topping-checkbox');
        checkboxes.forEach(checkbox => {
            const toppingId = parseInt(checkbox.value);
            const isSelected = this.selectedToppings.includes(toppingId);
            checkbox.checked = isSelected;
            
            const card = checkbox.closest('.topping-card');
            if (isSelected) {
                card.classList.add('selected');
            } else {
                card.classList.remove('selected');
            }
        });
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Only initialize if we're on a page with topping selection
    if (document.getElementById('toppings-section')) {
        window.toppingSelection = new ToppingSelection();
    }
});

// Export for use in other scripts
if (typeof module !== 'undefined' && module.exports) {
    module.exports = ToppingSelection;
}