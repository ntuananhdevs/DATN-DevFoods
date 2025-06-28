/**
 * Simple Topping Modal - Optimized version
 * Prevents continuous AJAX calls and simplifies UI
 */
class ToppingModal {
    constructor() {
        this.selectedToppings = new Map();
        this.allToppings = [];
        this.filteredToppings = [];
        this.isLoaded = false; // Prevent multiple loads
        this.init();
    }

    init() {
        this.bindEvents();
    }

    bindEvents() {
        // Open modal button
        document.getElementById('open-toppings-modal')?.addEventListener('click', () => {
            this.openModal();
        });

        // Close modal buttons
        document.getElementById('close-modal')?.addEventListener('click', () => {
            this.closeModal();
        });

        document.getElementById('cancel-modal')?.addEventListener('click', () => {
            this.closeModal();
        });

        // Confirm button
        document.getElementById('confirm-toppings')?.addEventListener('click', () => {
            this.confirmSelection();
        });

        // Search functionality - debounced
        const searchInput = document.getElementById('topping-search');
        let searchTimeout;
        searchInput?.addEventListener('input', (e) => {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                this.filterToppings(e.target.value);
            }, 300); // Debounce 300ms
        });

        // Select/Clear all buttons
        document.getElementById('modal-select-all')?.addEventListener('click', () => {
            this.selectAllToppings();
        });

        document.getElementById('modal-clear-all')?.addEventListener('click', () => {
            this.clearAllToppings();
        });

        // Close modal when clicking outside
        document.getElementById('toppings-modal')?.addEventListener('click', (e) => {
            if (e.target.id === 'toppings-modal') {
                this.closeModal();
            }
        });
    }

    async loadToppings() {
        // Only load once
        if (this.isLoaded) {
            this.renderToppings();
            return;
        }

        try {
            const response = await fetch('/admin/products/get-toppings');
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const result = await response.json();
            
            if (result.success && result.data) {
                this.allToppings = result.data;
                this.filteredToppings = [...this.allToppings];
                this.isLoaded = true;
                this.renderToppings();
            } else {
                throw new Error(result.message || 'Failed to load toppings');
            }
        } catch (error) {
            console.error('Error loading toppings:', error);
            this.displayError('Không thể tải danh sách toppings. Vui lòng thử lại.');
        }
    }

    renderToppings() {
        const container = document.getElementById('modal-toppings-list');
        if (!container) return;

        if (this.filteredToppings.length === 0) {
            container.innerHTML = `
                <div class="text-center py-8 text-gray-500">
                    <p>Không tìm thấy topping nào</p>
                </div>
            `;
            return;
        }

        container.innerHTML = this.filteredToppings.map(topping => 
            this.createToppingItem(topping)
        ).join('');

        // Bind click events for topping items
        container.querySelectorAll('.topping-item').forEach(item => {
            item.addEventListener('click', () => {
                const toppingId = parseInt(item.dataset.toppingId);
                this.toggleTopping(toppingId);
            });
        });
    }

    createToppingItem(topping) {
        const formattedPrice = topping.formatted_price || `${parseInt(topping.price).toLocaleString()}đ`;
        const isSelected = this.selectedToppings.has(topping.id);
        
        return `
            <div class="topping-item ${isSelected ? 'selected' : ''}" data-topping-id="${topping.id}">
                <div class="aspect-square mb-2 overflow-hidden rounded-lg border border-gray-200">
                    ${topping.image_url ? 
                        `<img src="${topping.image_url}" alt="${topping.name}" 
                             class="w-full h-full object-cover"
                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                             onload="this.style.display='block'; this.nextElementSibling.style.display='none';">
                         <div class="no-image-placeholder h-full" style="display: none;"><i class="far fa-image"></i></div>` :
                        `<div class="no-image-placeholder h-full"><i class="far fa-image"></i></div>`
                    }
                </div>
                <div class="text-center">
                    <h4 class="font-medium text-sm mb-1 line-clamp-1">${topping.name}</h4>
                    <p class="text-blue-600 font-semibold text-sm mb-2">${formattedPrice}</p>
                    <p class="text-gray-600 text-xs line-clamp-2">${topping.description || ''}</p>
                </div>
                <div class="selection-indicator">
                    <i class="fas fa-check"></i>
                </div>
            </div>
        `;
    }
 
     toggleTopping(toppingId) {
        const topping = this.allToppings.find(t => t.id === toppingId);
        if (!topping) return;

        if (this.selectedToppings.has(toppingId)) {
            this.selectedToppings.delete(toppingId);
        } else {
            this.selectedToppings.set(toppingId, topping);
        }

        this.updateModalUI();
    }

    selectAllToppings() {
        this.filteredToppings.forEach(topping => {
            this.selectedToppings.set(topping.id, topping);
        });
        this.updateModalUI();
    }

    clearAllToppings() {
        this.selectedToppings.clear();
        this.updateModalUI();
    }

    updateModalUI() {
        // Update selected count in modal
        const countElement = document.getElementById('modal-selected-count');
        if (countElement) {
            countElement.textContent = this.selectedToppings.size;
        }

        // Re-render toppings to update selection state
        this.renderToppings();
    }

    filterToppings(searchTerm) {
        const term = searchTerm.toLowerCase().trim();
        
        if (term === '') {
            this.filteredToppings = [...this.allToppings];
        } else {
            this.filteredToppings = this.allToppings.filter(topping => {
                return topping.name.toLowerCase().includes(term);
            });
        }
        
        this.renderToppings();
    }

    openModal() {
        const modal = document.getElementById('toppings-modal');
        if (modal) {
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            
            // Load toppings only when modal opens
            this.loadToppings();
            
            // Reset search
            const searchInput = document.getElementById('topping-search');
            if (searchInput) {
                searchInput.value = '';
                this.filterToppings('');
            }
        }
    }

    closeModal() {
        const modal = document.getElementById('toppings-modal');
        if (modal) {
            modal.classList.add('hidden');
            document.body.style.overflow = '';
        }
    }

    confirmSelection() {
        this.updateSelectedToppingsDisplay();
        this.updateHiddenInput();
        this.closeModal();
    }

    updateSelectedToppingsDisplay() {
        const container = document.getElementById('selected-toppings-tags');
        const noToppingsMessage = document.getElementById('no-toppings-message');
        
        if (!container) return;

        if (this.selectedToppings.size === 0) {
            container.innerHTML = '<div class="text-gray-500 text-sm italic" id="no-toppings-message">Chưa có topping nào được chọn</div>';
            return;
        }

        // Hide no toppings message
        if (noToppingsMessage) {
            noToppingsMessage.style.display = 'none';
        }

        const tags = Array.from(this.selectedToppings.values()).map(topping => 
            this.createToppingTag(topping)
        ).join('');

        container.innerHTML = tags;

        // Bind remove events
        container.querySelectorAll('.remove-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.stopPropagation();
                const toppingId = parseInt(btn.dataset.toppingId);
                this.removeTopping(toppingId);
            });
        });
    }

    createToppingTag(topping) {
         const formattedPrice = topping.formatted_price || `${parseInt(topping.price).toLocaleString()}đ`;
         
         return `
             <div class="inline-flex items-center gap-2 bg-blue-100 text-blue-800 text-sm px-4 py-2 rounded-lg border border-blue-200">
                <span>${topping.name}</span>
                <span class="text-blue-600 font-medium">${formattedPrice}</span>
                <button type="button" class="remove-btn text-red-500 hover:text-red-700" data-topping-id="${topping.id}">
                    <i class="fas fa-times text-xs"></i>
                </button>
            </div>
        `;
    }

    removeTopping(toppingId) {
        this.selectedToppings.delete(toppingId);
        this.updateSelectedToppingsDisplay();
        this.updateHiddenInput();
    }

    updateHiddenInput() {
        const hiddenInput = document.getElementById('selected_toppings');
        if (hiddenInput) {
            const selectedIds = Array.from(this.selectedToppings.keys());
            hiddenInput.value = JSON.stringify(selectedIds);
        }
    }

    displayError(message) {
        const container = document.getElementById('modal-toppings-list');
        if (container) {
            container.innerHTML = `
                <div class="text-center py-8 text-red-500">
                    <p>${message}</p>
                    <button onclick="location.reload()" class="mt-4 px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                        Thử lại
                    </button>
                </div>
            `;
        }
    }

    // Method to set selected toppings (for editing)
    setSelectedToppings(toppings) {
        this.selectedToppings.clear();
        toppings.forEach(topping => {
            this.selectedToppings.set(topping.id, topping);
        });
        this.updateSelectedToppingsDisplay();
        this.updateHiddenInput();
    }

    // Method to get selected toppings
    getSelectedToppings() {
        return Array.from(this.selectedToppings.values());
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    window.toppingModal = new ToppingModal();
});