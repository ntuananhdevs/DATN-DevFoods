// Product page functionality
document.addEventListener("DOMContentLoaded", function() {
    // Debug log for initial state
    console.log('Initial branch state:', {
        selectedBranchId: window.selectedBranchId || null,
        productId: window.productId
    });

    // Auto-show branch selector if no branch is selected
    const selectedBranchId = window.selectedBranchId;
    if (!selectedBranchId) {
        const branchModal = document.getElementById('branch-selector-modal');
        if (branchModal) {
            branchModal.style.display = 'flex';
            document.body.classList.add('overflow-hidden');
        }
    }
    
    // Show toast function
    window.showToast = function(message, type = 'info') {
        if (typeof showNotification === 'function') {
            const titles = {
                success: 'Thành công!',
                error: 'Lỗi!',
                warning: 'Cảnh báo!', 
                info: 'Thông báo!'
            };
            showNotification(type, titles[type], message);
        } else {
            alert(message);
        }
    };
    
    // Product image gallery
    const mainImage = document.getElementById('main-product-image');
    const thumbnails = document.querySelectorAll('.product-thumbnail');
    
    thumbnails.forEach(thumb => {
        thumb.addEventListener('click', function() {
            // Update main image
            mainImage.src = this.querySelector('img').src;
            
            // Update active state
            thumbnails.forEach(t => t.classList.remove('border-orange-500'));
            this.classList.add('border-orange-500');
        });
    });
    
    // Price calculation
    const basePrice = window.basePrice;
    const basePriceDisplay = document.getElementById('base-price');
    const currentPriceDisplay = document.getElementById('current-price');
    const variantInputs = document.querySelectorAll('.variant-input');
    const toppingInputs = document.querySelectorAll('.topping-input');
    const quantityDisplay = document.getElementById('quantity');
    let quantity = 1;
    let totalPrice = basePrice;
    
    function updatePrice() {
        // Calculate variant price adjustments
        let variantAdjustment = 0;
        document.querySelectorAll('.variant-input:checked').forEach(input => {
            variantAdjustment += parseFloat(input.dataset.priceAdjustment || 0);
        });
        
        // Calculate topping price
        let toppingPrice = 0;
        document.querySelectorAll('.topping-input:checked').forEach(input => {
            toppingPrice += parseFloat(input.dataset.price || 0);
        });
        
        // Single item price (variant price + toppings)
        const singleItemPrice = basePrice + variantAdjustment;
        const totalItemPrice = singleItemPrice + toppingPrice;
        
        // Apply quantity
        totalPrice = totalItemPrice * quantity;
        
        // Update display
        if (variantAdjustment !== 0 || toppingPrice !== 0) {
            basePriceDisplay.textContent = `${basePrice.toLocaleString('vi-VN')}đ`;
            basePriceDisplay.classList.remove('hidden');
        } else {
            basePriceDisplay.classList.add('hidden');
        }
        
        currentPriceDisplay.textContent = `${totalPrice.toLocaleString('vi-VN')}đ`;
        
        // Highlight current price if different from base
        if (totalPrice !== basePrice) {
            currentPriceDisplay.classList.add('text-green-500');
            currentPriceDisplay.classList.remove('text-orange-500');
        } else {
            currentPriceDisplay.classList.add('text-orange-500');
            currentPriceDisplay.classList.remove('text-green-500');
        }
    }
    
    // Variant selection
    variantInputs.forEach(input => {
        input.addEventListener('change', function() {
            // Update visual state of labels
            const attributeId = this.dataset.attributeId;
            document.querySelectorAll(`[data-attribute-id="${attributeId}"] + .variant-label`).forEach(label => {
                label.classList.remove('bg-orange-100', 'border-orange-500', 'text-orange-600');
            });
            
            this.nextElementSibling.classList.add('bg-orange-100', 'border-orange-500', 'text-orange-600');
            updatePrice();
        });
    });
    
    // Topping selection
    toppingInputs.forEach(input => {
        input.addEventListener('change', function() {
            const toppingContainer = this.closest('label');
            if (this.checked) {
                toppingContainer.classList.add('topping-checked');
                toppingContainer.querySelector('.w-full').classList.add('scale-110');
                toppingContainer.querySelector('.bg-orange-500').classList.add('scale-100');
            } else {
                toppingContainer.classList.remove('topping-checked');
                toppingContainer.querySelector('.w-full').classList.remove('scale-110');
                toppingContainer.querySelector('.bg-orange-500').classList.remove('scale-100');
            }
            updatePrice();
        });
    });
    
    // Quantity controls
    const decreaseBtn = document.getElementById('decrease-quantity');
    const increaseBtn = document.getElementById('increase-quantity');
    
    decreaseBtn.addEventListener('click', function() {
        if (quantity > 1) {
            quantity--;
            quantityDisplay.textContent = quantity;
            updatePrice();
        }
    });
    
    increaseBtn.addEventListener('click', function() {
        quantity++;
        quantityDisplay.textContent = quantity;
        updatePrice();
    });
    
    // Add to cart functionality
    const addToCartBtn = document.getElementById('add-to-cart');
    const buyNowBtn = document.getElementById('buy-now');
    
    // Function to get selected product data
    function getSelectedProductData() {
        // Get selected branch
        const branchId = document.getElementById('branch-select').value;
        if (!branchId) {
            showToast('Vui lòng chọn chi nhánh trước khi thêm vào giỏ hàng', 'warning');
            return null;
        }
        
        // Get all selected variant values
        const selectedVariantValueIds = [];
        const variantGroups = document.querySelectorAll('#variants-container > div');
        
        variantGroups.forEach(group => {
            const checkedInput = group.querySelector('input:checked');
            if (checkedInput) {
                selectedVariantValueIds.push(parseInt(checkedInput.value));
            }
        });
        
        // Get selected toppings
        const selectedToppings = Array.from(document.querySelectorAll('.topping-input:checked'))
            .map(input => parseInt(input.value));
        
        // Get quantity
        const quantity = parseInt(document.getElementById('quantity').textContent);
        
        return {
            product_id: window.productId,
            variant_values: selectedVariantValueIds,
            branch_id: branchId,
            quantity: quantity,
            toppings: selectedToppings
        };
    }
    
    // Add to cart button click handler
    addToCartBtn.addEventListener('click', function() {
        const productData = getSelectedProductData();
        if (!productData) return;
        
        // Send request using Fetch API
        fetch('/cart/add', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.csrfToken
            },
            body: JSON.stringify(productData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast(data.message, 'success');
                
                // Update cart counter if needed
                if (typeof window.updateCartCount === 'function' && data.count) {
                    window.updateCartCount(data.count);
                }
            } else {
                showToast(data.message || 'Có lỗi khi thêm sản phẩm vào giỏ hàng', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Có lỗi xảy ra khi thêm sản phẩm vào giỏ hàng', 'error');
        });
    });

    // Buy now button click handler
    buyNowBtn.addEventListener('click', function() {
        const productData = getSelectedProductData();
        if (!productData) return;
        
        // Add buy_now flag to the data
        productData.buy_now = true;
        
        // Send request using Fetch API
        fetch('/cart/add', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.csrfToken
            },
            body: JSON.stringify(productData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Redirect to checkout page
                window.location.href = '/checkout';
            } else {
                showToast(data.message || 'Có lỗi khi thêm sản phẩm vào giỏ hàng', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Có lỗi xảy ra khi thêm sản phẩm vào giỏ hàng', 'error');
        });
    });

    // Initialize
    updatePrice();
    
    // Handle discount code copy functionality
    const copyButtons = document.querySelectorAll('.copy-code');
    copyButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.stopPropagation();
            const code = this.dataset.code;
            
            // Create a temporary input element
            const tempInput = document.createElement('input');
            tempInput.value = code;
            document.body.appendChild(tempInput);
            
            // Select and copy the text
            tempInput.select();
            document.execCommand('copy');
            
            // Remove the temporary element
            document.body.removeChild(tempInput);
            
            // Update button text temporarily
            const originalText = this.textContent;
            this.textContent = 'Đã sao chép!';
            this.classList.remove('bg-orange-500', 'hover:bg-orange-600');
            this.classList.add('bg-green-500', 'hover:bg-green-600');
            
            // Reset button text after a delay
            setTimeout(() => {
                this.textContent = originalText;
                this.classList.remove('bg-green-500', 'hover:bg-green-600');
                this.classList.add('bg-orange-500', 'hover:bg-orange-600');
            }, 2000);
            
            // Show toast notification
            showToast(`Đã sao chép mã "${code}" vào clipboard`, 'success');
        });
    });
});

// Tab switching functionality
document.addEventListener('DOMContentLoaded', function() {
    const tabButtons = document.querySelectorAll('[data-tab]');
    const tabContents = document.querySelectorAll('.tab-content');
    
    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            const tabName = this.getAttribute('data-tab');
            
            // Reset all tab buttons
            tabButtons.forEach(btn => {
                btn.classList.remove('border-orange-500', 'text-orange-500');
                btn.classList.add('border-transparent');
            });
            
            // Hide all tab contents
            tabContents.forEach(content => {
                content.classList.add('hidden');
            });
            
            // Activate clicked tab
            this.classList.add('border-orange-500', 'text-orange-500');
            this.classList.remove('border-transparent');
            
            // Show corresponding content
            document.getElementById('content-' + tabName).classList.remove('hidden');
        });
    });
});

// Function to update product availability UI
function updateProductAvailability() {
    const addToCartBtn = document.getElementById('add-to-cart');
    const buyNowBtn = document.getElementById('buy-now');
    const quantityControls = document.querySelectorAll('#decrease-quantity, #increase-quantity');
    const toppingInputs = document.querySelectorAll('.topping-input');
    const outOfStockMessage = document.getElementById('out-of-stock-message');
    
    // Check if any variant is available
    const hasAvailableVariant = Array.from(document.querySelectorAll('.variant-input'))
        .some(input => parseInt(input.dataset.stockQuantity || 0) > 0);
        
    // Check if currently selected variant is available
    const selectedVariant = document.querySelector('.variant-input:checked');
    const isSelectedVariantAvailable = selectedVariant && parseInt(selectedVariant.dataset.stockQuantity || 0) > 0;
    
    console.log('Availability check:', {
        hasAvailableVariant,
        isSelectedVariantAvailable,
        selectedVariantStock: selectedVariant ? selectedVariant.dataset.stockQuantity : null
    });
    
    // Update out of stock message
    if (outOfStockMessage) {
        if (!hasAvailableVariant) {
            outOfStockMessage.style.display = 'block';
            outOfStockMessage.innerHTML = '<p>Sản phẩm hiện đang hết hàng tại chi nhánh của bạn. Vui lòng chọn chi nhánh khác.</p>';
        } else if (!isSelectedVariantAvailable) {
            outOfStockMessage.style.display = 'block';
            outOfStockMessage.innerHTML = '<p>Biến thể đã chọn hiện đang hết hàng. Vui lòng chọn biến thể khác.</p>';
        } else {
            outOfStockMessage.style.display = 'none';
        }
    }
    
    // Update add to cart and buy now buttons
    if (addToCartBtn) {
        if (!hasAvailableVariant) {
            addToCartBtn.disabled = true;
            addToCartBtn.classList.add('bg-gray-400');
            addToCartBtn.classList.remove('bg-orange-500', 'hover:bg-orange-600');
            const span = addToCartBtn.querySelector('span');
            if (span) span.textContent = 'Hết hàng';
        } else if (!isSelectedVariantAvailable) {
            addToCartBtn.disabled = true;
            addToCartBtn.classList.add('bg-gray-400');
            addToCartBtn.classList.remove('bg-orange-500', 'hover:bg-orange-600');
            const span = addToCartBtn.querySelector('span');
            if (span) span.textContent = 'Chọn biến thể khác';
        } else {
            addToCartBtn.disabled = false;
            addToCartBtn.classList.remove('bg-gray-400');
            addToCartBtn.classList.add('bg-orange-500', 'hover:bg-orange-600');
            const span = addToCartBtn.querySelector('span');
            if (span) span.textContent = 'Thêm vào giỏ hàng';
        }
    }
    
    if (buyNowBtn) {
        buyNowBtn.disabled = !isSelectedVariantAvailable;
        buyNowBtn.classList.toggle('opacity-50', !isSelectedVariantAvailable);
        buyNowBtn.classList.toggle('cursor-not-allowed', !isSelectedVariantAvailable);
    }
    
    // Update quantity controls and toppings based on selected variant
    const isControlsEnabled = isSelectedVariantAvailable;
    quantityControls.forEach(control => {
        control.disabled = !isControlsEnabled;
        control.classList.toggle('opacity-50', !isControlsEnabled);
        control.classList.toggle('cursor-not-allowed', !isControlsEnabled);
    });
    
    toppingInputs.forEach(input => {
        input.disabled = !isControlsEnabled;
        const label = input.closest('label');
        if (label) {
            label.classList.toggle('opacity-50', !isControlsEnabled);
            label.classList.toggle('cursor-not-allowed', !isControlsEnabled);
        }
    });
}

// Initialize Pusher
const pusher = new Pusher(window.pusherKey, {
    cluster: window.pusherCluster
});

// Subscribe to the stock update channel
const channel = pusher.subscribe('branch-stock-channel');

// Track last update to prevent duplicate alerts
let lastUpdate = {
    variantId: null,
    quantity: null,
    timestamp: 0
};

// Listen for stock updates
channel.bind('stock-updated', function(data) {
    console.log('Stock update received:', data);
    
    // Check for duplicate updates within 1 second
    const now = Date.now();
    if (lastUpdate.variantId === data.productVariantId && 
        lastUpdate.quantity === data.stockQuantity && 
        now - lastUpdate.timestamp < 1000) {
        console.log('Duplicate update detected, ignoring');
        return;
    }
    
    // Update last update info
    lastUpdate = {
        variantId: data.productVariantId,
        quantity: data.stockQuantity,
        timestamp: now
    };
    
    // Only process if branch ID matches
    const currentBranchId = document.querySelector('.variant-input')?.dataset.branchId;
    if (currentBranchId && parseInt(currentBranchId) !== data.branchId) {
        console.log('Branch ID mismatch, ignoring update');
        return;
    }
    
    // Find all variant inputs that match the updated stock
    const variantInputs = document.querySelectorAll(`.variant-input[data-variant-id="${data.productVariantId}"]`);
    
    if (variantInputs.length === 0) {
        console.log('No matching variants found for productVariantId:', data.productVariantId);
        return;
    }
    
    variantInputs.forEach(input => {
        // Update the stock quantity data attribute
        input.dataset.stockQuantity = data.stockQuantity;
        
        // Get the label element
        const label = input.nextElementSibling;
        
        // Update stock display
        let stockDisplay = label.querySelector('.stock-display');
        if (stockDisplay) {
            if (data.stockQuantity > 0) {
                stockDisplay.textContent = `(Còn ${data.stockQuantity})`;
                stockDisplay.className = `text-xs ml-1 ${data.stockQuantity <= 5 ? 'text-orange-500' : 'text-gray-500'} stock-display`;
            } else {
                stockDisplay.textContent = '(Hết hàng)';
                stockDisplay.className = 'text-xs ml-1 text-red-500 stock-display';
            }
        }
        
        // Update disabled state of variant input
        if (data.stockQuantity <= 0) {
            input.disabled = true;
            label.classList.add('opacity-50', 'cursor-not-allowed');
            label.classList.remove('hover:bg-gray-50');
            
            // If this is the currently selected variant, uncheck it
            if (input.checked) {
                // Find first available variant in the same attribute group
                const attributeId = input.dataset.attributeId;
                const firstAvailableVariant = document.querySelector(`.variant-input[data-attribute-id="${attributeId}"]:not([disabled])`);
                if (firstAvailableVariant) {
                    firstAvailableVariant.checked = true;
                    firstAvailableVariant.dispatchEvent(new Event('change'));
                }
            }
        } else {
            input.disabled = false;
            label.classList.remove('opacity-50', 'cursor-not-allowed');
            label.classList.add('hover:bg-gray-50');
        }
    });
    
    // Force update product availability
    setTimeout(updateProductAvailability, 100);
});

// Listen for topping stock updates
channel.bind('topping-stock-updated', function(data) {
    console.log('Topping stock update received:', data);
    
    // Only process if branch ID matches
    const currentBranchId = document.querySelector('.topping-input')?.dataset.branchId;
    if (currentBranchId && parseInt(currentBranchId) !== data.branchId) {
        console.log('Branch ID mismatch, ignoring update');
        return;
    }
    
    // Find all topping inputs that match the updated stock
    const toppingInputs = document.querySelectorAll(`.topping-input[data-topping-id="${data.toppingId}"]`);
    
    if (toppingInputs.length === 0) {
        console.log('No matching toppings found for toppingId:', data.toppingId);
        return;
    }
    
    toppingInputs.forEach(input => {
        // Update the stock quantity data attribute
        input.dataset.stockQuantity = data.stockQuantity;
        
        // Get the label element (parent of input)
        const label = input.closest('label');
        
        // Get the stock display element
        let stockDisplay = label.querySelector('.stock-display');
        
        // If stock is 0, hide the entire topping
        if (data.stockQuantity <= 0) {
            // If this topping is currently checked, uncheck it
            if (input.checked) {
                input.checked = false;
                input.dispatchEvent(new Event('change'));
            }
            
            // Hide the entire topping
            label.style.display = 'none';
        } else {
            // Show the topping
            label.style.display = 'block';
            
            // Update or create stock display
            if (!stockDisplay) {
                stockDisplay = document.createElement('div');
                stockDisplay.className = 'absolute bottom-0 left-0 right-0 bg-orange-500 bg-opacity-80 text-white text-xs text-center py-1 stock-display';
                label.querySelector('.relative').appendChild(stockDisplay);
            }
            
            // Only show stock display if quantity is less than 5
            if (data.stockQuantity < 5) {
                stockDisplay.style.display = 'block';
                stockDisplay.textContent = `Còn ${data.stockQuantity}`;
                stockDisplay.className = 'absolute bottom-0 left-0 right-0 bg-orange-500 bg-opacity-80 text-white text-xs text-center py-1 stock-display';
            } else {
                stockDisplay.style.display = 'none';
            }
            
            // Enable the input
            input.disabled = false;
            label.classList.remove('opacity-50', 'cursor-not-allowed');
            label.classList.add('hover:bg-gray-50');
        }
    });
    
    // Update overall product availability
    updateProductAvailability();
});