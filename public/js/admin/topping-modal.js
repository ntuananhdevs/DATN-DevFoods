class ToppingModal {
    constructor() {
        this.selectedToppings = new Map(); // Store selected toppings with their data
        this.allToppings = [];
        this.filteredToppings = [];
        this.init();
    }

    init() {
        this.bindEvents();
        this.loadToppings();
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

        // Search functionality
        const searchInput = document.getElementById('topping-search');
        const clearSearchBtn = document.getElementById('clear-search');
        
        searchInput?.addEventListener('input', (e) => {
            const value = e.target.value;
            this.filterToppings(value);
            
            // Show/hide clear button
            if (value.trim()) {
                clearSearchBtn?.classList.remove('hidden');
            } else {
                clearSearchBtn?.classList.add('hidden');
            }
        });
        
        // Clear search functionality
        clearSearchBtn?.addEventListener('click', () => {
            searchInput.value = '';
            clearSearchBtn.classList.add('hidden');
            this.filterToppings('');
            searchInput.focus();
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
        try {
            const response = await fetch('/admin/products/get-toppings');
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const result = await response.json();
            
            if (result.success && result.data) {
                this.allToppings = result.data;
                this.filteredToppings = [...this.allToppings];
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
                <div class="col-span-full text-center py-8 text-gray-500">
                    <i class="fas fa-search text-4xl mb-4"></i>
                    <p>Không tìm thấy topping nào</p>
                </div>
            `;
            return;
        }

        container.innerHTML = this.filteredToppings.map(topping => 
            this.createToppingCard(topping)
        ).join('');

        // Bind click events for topping cards
        container.querySelectorAll('.topping-card').forEach(card => {
            card.addEventListener('click', () => {
                const toppingId = parseInt(card.dataset.toppingId);
                this.toggleTopping(toppingId);
            });
        });
    }

    createToppingCard(topping) {
        const isSelected = this.selectedToppings.has(topping.id);
        
        return `
            <div class="topping-card border-2 rounded-xl p-4 cursor-pointer transition-all duration-300 hover:shadow-lg transform hover:-translate-y-1 ${
                isSelected ? 'border-blue-500 bg-gradient-to-br from-blue-50 to-blue-100 shadow-md' : 'border-gray-200 hover:border-blue-300 bg-white'
            }" data-topping-id="${topping.id}">
                
                <!-- Selection indicator -->
                <div class="flex justify-between items-start mb-3">
                    <div class="flex-1"></div>
                    <div class="relative w-7 h-7 rounded-full border-2 flex items-center justify-center transition-all duration-300 ${
                        isSelected 
                            ? 'bg-gradient-to-r from-blue-500 to-blue-600 border-blue-500 text-white shadow-lg scale-110' 
                            : 'border-gray-300 bg-white hover:border-blue-400 hover:bg-blue-50'
                    }">
                        ${isSelected ? `
                            <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                        ` : ''}
                        ${isSelected ? '<div class="absolute -top-1 -right-1 w-3 h-3 bg-green-400 rounded-full animate-ping"></div>' : ''}
                    </div>
                </div>
                
                <!-- Topping image -->
                 <div class="flex justify-center mb-3">
                     <div class="w-20 h-20 rounded-xl overflow-hidden bg-gray-100 flex items-center justify-center shadow-sm ring-2 ring-gray-100 ${
                         isSelected ? 'ring-blue-200' : ''
                     }">
                         <img src="${topping.image_url}" alt="${topping.name}" 
                              class="w-full h-full object-cover transition-transform duration-300 hover:scale-110" 
                              onerror="this.src='/images/default-topping.png'">
                     </div>
                 </div>
                
                <!-- Topping info -->
                <div class="text-center">
                    <h4 class="text-sm font-semibold text-gray-900 mb-2 line-clamp-2 leading-tight">${topping.name}</h4>
                    <div class="inline-flex items-center px-2 py-1 rounded-full text-xs font-bold ${
                        isSelected 
                            ? 'bg-blue-100 text-blue-700 border border-blue-200' 
                            : 'bg-orange-100 text-orange-700 border border-orange-200'
                    }">
                        ${topping.formatted_price}
                    </div>
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
        const term = this.normalizeText(searchTerm.trim());
        
        if (term === '') {
            this.filteredToppings = [...this.allToppings];
        } else {
            this.filteredToppings = this.allToppings.filter(topping => {
                const normalizedName = this.normalizeText(topping.name);
                // Tìm kiếm tương đối: kiểm tra từng từ trong search term
                const searchWords = term.split(/\s+/).filter(word => word.length > 0);
                return searchWords.every(word => normalizedName.includes(word));
            });
        }
        
        this.renderToppings();
    }
    
    // Hàm chuẩn hóa text để tìm kiếm không phân biệt dấu
    normalizeText(text) {
        return text.toLowerCase()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '') // Loại bỏ dấu
            .replace(/đ/g, 'd')
            .replace(/Đ/g, 'd');
    }

    openModal() {
        const modal = document.getElementById('toppings-modal');
        if (modal) {
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            
            // Reset search
            const searchInput = document.getElementById('topping-search');
            const clearSearchBtn = document.getElementById('clear-search');
            if (searchInput) {
                searchInput.value = '';
                clearSearchBtn?.classList.add('hidden');
                this.filterToppings('');
                // Focus vào search input để người dùng có thể tìm kiếm ngay
                setTimeout(() => searchInput.focus(), 100);
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
        this.updateMainCounter();
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
            <div class="topping-tag inline-flex items-center gap-2 bg-gradient-to-r from-blue-100 to-blue-50 text-blue-800 text-sm font-medium px-3 py-2 rounded-lg border border-blue-200 hover:from-blue-200 hover:to-blue-100 hover:shadow-md transition-all duration-300 shadow-sm">
                <div class="w-2 h-2 bg-blue-500 rounded-full flex-shrink-0 animate-pulse"></div>
                <div class="flex items-center gap-2 flex-1">
                    <span class="font-semibold whitespace-nowrap">${topping.name}</span>
                    <span class="text-blue-600 font-bold text-sm bg-blue-50 px-2 py-1 rounded-full whitespace-nowrap">${formattedPrice}</span>
                </div>
                <button type="button" class="remove-btn flex-shrink-0 text-red-500 hover:text-red-700 hover:scale-125 hover:rotate-90 transition-all duration-300 p-1 rounded-full hover:bg-red-100" data-topping-id="${topping.id}">
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                </button>
            </div>
        `;
    }

    removeTopping(toppingId) {
        this.selectedToppings.delete(toppingId);
        this.updateSelectedToppingsDisplay();
        this.updateHiddenInput();
        this.updateMainCounter();
    }

    updateHiddenInput() {
        const hiddenInput = document.getElementById('selected_toppings');
        if (hiddenInput) {
            const selectedIds = Array.from(this.selectedToppings.keys());
            hiddenInput.value = JSON.stringify(selectedIds);
        }
    }

    updateMainCounter() {
        const countElement = document.getElementById('selected-toppings-count');
        const displayElement = document.getElementById('selected-toppings-display');
        const noToppingsMessage = document.getElementById('no-toppings-message');
        const totalElement = document.getElementById('selected-toppings-total');
        const totalPriceElement = document.getElementById('total-price');
        
        if (countElement) {
            countElement.textContent = this.selectedToppings.size;
            
            // Calculate total price
            let totalPrice = 0;
            this.selectedToppings.forEach(topping => {
                totalPrice += parseInt(topping.price) || 0;
            });
            
            // Add visual feedback based on selection count
            if (this.selectedToppings.size > 0) {
                countElement.classList.add('animate-pulse');
                if (displayElement) {
                    displayElement.classList.add('has-toppings');
                }
                if (noToppingsMessage) {
                    noToppingsMessage.style.display = 'none';
                }
                if (totalElement) {
                    totalElement.classList.remove('hidden');
                }
                if (totalPriceElement) {
                    totalPriceElement.textContent = `${totalPrice.toLocaleString()}đ`;
                }
            } else {
                countElement.classList.remove('animate-pulse');
                if (displayElement) {
                    displayElement.classList.remove('has-toppings');
                }
                if (noToppingsMessage) {
                    noToppingsMessage.style.display = 'flex';
                }
                if (totalElement) {
                    totalElement.classList.add('hidden');
                }
            }
        }
    }

    displayError(message) {
        const container = document.getElementById('modal-toppings-list');
        if (container) {
            container.innerHTML = `
                <div class="col-span-full text-center py-8 text-red-500">
                    <i class="fas fa-exclamation-triangle text-4xl mb-4"></i>
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
        this.updateMainCounter();
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