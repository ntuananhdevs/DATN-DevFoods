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
    
    // Show toast function (custom slide-in right)
    window.showToast = function(message, type = 'info') {
        // Remove old toast if exists
        const oldToast = document.querySelector('.custom-toast');
        if (oldToast) oldToast.remove();

        // Create toast element
        const toast = document.createElement('div');
        toast.className = 'custom-toast fixed top-8 right-4 z-50 px-5 py-3 rounded-lg shadow-lg flex items-center gap-3 animate-toast-in';
        toast.style.minWidth = '220px';
        toast.style.maxWidth = '90vw';
        toast.style.fontSize = '1rem';
        toast.style.transition = 'transform 0.4s cubic-bezier(.4,2,.3,1), opacity 0.3s';
        toast.style.transform = 'translateX(120%)';
        toast.style.opacity = '0';

        // Icon
        const icon = document.createElement('i');
        icon.className = 'fas';
        switch(type) {
            case 'success':
                toast.classList.add('bg-green-500', 'text-white');
                icon.classList.add('fa-check-circle');
                break;
            case 'error':
                toast.classList.add('bg-red-500', 'text-white');
                icon.classList.add('fa-times-circle');
                break;
            case 'warning':
                toast.classList.add('bg-yellow-400', 'text-gray-900');
                icon.classList.add('fa-exclamation-triangle');
                break;
            default:
                toast.classList.add('bg-gray-800', 'text-white');
                icon.classList.add('fa-info-circle');
        }
        icon.style.fontSize = '1.3em';
        toast.appendChild(icon);

        // Message
        const msg = document.createElement('span');
        msg.textContent = message;
        toast.appendChild(msg);

        // Add to DOM
        document.body.appendChild(toast);

        // Force reflow for animation
        setTimeout(() => {
            toast.style.transform = 'translateX(0)';
            toast.style.opacity = '1';
        }, 10);

        // Hide and remove after 2.5s
        setTimeout(() => {
            toast.style.transform = 'translateX(120%)';
            toast.style.opacity = '0';
            setTimeout(() => {
                if (toast.parentNode) toast.parentNode.removeChild(toast);
            }, 400);
        }, 2500);
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
    
    // Discount code info from server (window)
    const bestDiscount = window.bestDiscountCode || null;
    const bestDiscountAmount = window.bestDiscountAmount || 0;
    
    // Helper: tính số tiền giảm giá tốt nhất cho 1 sản phẩm
    function calcBestDiscountAmount(price) {
        if (!bestDiscount) return 0;
        let discountAmount = 0;
        if (bestDiscount.discount_type === 'percentage') {
            discountAmount = price * (bestDiscount.discount_value / 100);
            if (bestDiscount.max_discount_amount && bestDiscount.max_discount_amount > 0) {
                discountAmount = Math.min(discountAmount, bestDiscount.max_discount_amount);
            }
        } else if (bestDiscount.discount_type === 'fixed_amount') {
            discountAmount = bestDiscount.discount_value;
        }
        if (bestDiscount.min_order_amount > 0 && price < bestDiscount.min_order_amount) {
            discountAmount = 0;
        }
        return discountAmount;
    }

    // Define updatePrice function in global scope
    window.updatePrice = function() {
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
        // Tổng giá gốc (base + variant + topping)
        const originalPrice = window.basePrice + variantAdjustment + toppingPrice;
        // Tính số tiền giảm giá tốt nhất
        const discountAmount = calcBestDiscountAmount(originalPrice);
        // Giá sau giảm
        const finalPrice = Math.max(0, originalPrice - discountAmount);
        // Hiển thị
        if (discountAmount > 0) {
            basePriceDisplay.textContent = `${originalPrice.toLocaleString('vi-VN')}đ`;
            basePriceDisplay.classList.remove('hidden');
            currentPriceDisplay.textContent = `${finalPrice.toLocaleString('vi-VN')}đ`;
        } else {
            basePriceDisplay.classList.add('hidden');
            currentPriceDisplay.textContent = `${originalPrice.toLocaleString('vi-VN')}đ`;
        }
        // Highlight
        if (discountAmount > 0) {
            currentPriceDisplay.classList.add('text-orange-500');
            currentPriceDisplay.classList.remove('text-green-500');
        } else if (originalPrice !== window.basePrice) {
            currentPriceDisplay.classList.add('text-green-500');
            currentPriceDisplay.classList.remove('text-orange-500');
        } else {
            currentPriceDisplay.classList.add('text-orange-500');
            currentPriceDisplay.classList.remove('text-green-500');
        }
    };
    
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
            // Không cần updatePrice() vì số lượng không ảnh hưởng đến giá hiển thị trên trang chi tiết
        }
    });
    
    increaseBtn.addEventListener('click', function() {
        quantity++;
        quantityDisplay.textContent = quantity;
        // Không cần updatePrice() vì số lượng không ảnh hưởng đến giá hiển thị trên trang chi tiết
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

    // Initialize
    updatePrice();

    // Favorite button handling
    const favoriteBtn = document.querySelector('.favorite-btn');
    if (favoriteBtn) {
        favoriteBtn.addEventListener('click', function(e) {
            e.preventDefault();
            const productId = this.getAttribute('data-product-id');
            const icon = this.querySelector('i');
            if (!productId || !icon) return;

            fetch('/favorite/toggle', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': window.csrfToken
                },
                body: JSON.stringify({ product_id: productId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (data.is_favorite) {
                        icon.classList.remove('far');
                        icon.classList.add('fas', 'text-red-500');
                        showToast('Đã thêm vào yêu thích', 'success');
                    } else {
                        icon.classList.remove('fas', 'text-red-500');
                        icon.classList.add('far');
                        showToast('Đã xóa khỏi yêu thích', 'info');
                    }
                } else {
                    showToast(data.message || 'Có lỗi xảy ra', 'error');
                }
            })
            .catch(error => {
                showToast('Có lỗi xảy ra khi cập nhật yêu thích', 'error');
            });
        });
    }
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

// Listen for product price updates
channel.bind('product-price-updated', function(data) {
    console.log('Product price update received:', data);
    
    // Get current branch ID from the hidden input
    const currentBranchId = document.getElementById('branch-select')?.value;
    if (currentBranchId && parseInt(currentBranchId) !== data.branchId) {
        console.log('Branch ID mismatch, ignoring update');
        return;
    }
    
    // Update base price
    window.basePrice = parseFloat(data.basePrice);
    
    // Update price displays
    const basePriceDisplay = document.getElementById('base-price');
    const currentPriceDisplay = document.getElementById('current-price');
    const priceUpdateNotification = document.getElementById('price-update-notification');
    
    // Add animation class to current price
    currentPriceDisplay.classList.add('animate-price-update');
    
    // Update base price display
    basePriceDisplay.textContent = `${window.basePrice.toLocaleString('vi-VN')}đ`;
    basePriceDisplay.classList.remove('hidden');
    
    // Update current price display
    currentPriceDisplay.textContent = `${window.basePrice.toLocaleString('vi-VN')}đ`;
    currentPriceDisplay.classList.add('text-orange-500');
    currentPriceDisplay.classList.remove('text-green-500');
    
    // Show price update notification
    priceUpdateNotification.classList.remove('hidden');
    
    // Force price recalculation
    window.updatePrice();
    
    // Remove animation class after animation completes
    setTimeout(() => {
        currentPriceDisplay.classList.remove('animate-price-update');
    }, 500);
    
    // Hide notification after 5 seconds
    setTimeout(() => {
        priceUpdateNotification.classList.add('hidden');
    }, 5000);
    
    // Show toast notification    
    // Update related products prices if they exist
    const relatedProducts = document.querySelectorAll('.related-product');
    relatedProducts.forEach(product => {
        const productId = product.dataset.productId;
        if (productId == data.productId) {
            const priceElement = product.querySelector('.product-price');
            if (priceElement) {
                priceElement.textContent = `${window.basePrice.toLocaleString('vi-VN')}đ`;
                priceElement.classList.add('animate-price-update');
                setTimeout(() => {
                    priceElement.classList.remove('animate-price-update');
                }, 500);
            }
        }
    });
});

// Listen for variant price updates
channel.bind('variant-price-updated', function(data) {
    console.log('Variant price update received:', data);
    
    // Get current branch ID from the hidden input
    const currentBranchId = document.getElementById('branch-select')?.value;
    if (currentBranchId && parseInt(currentBranchId) !== data.branchId) {
        console.log('Branch ID mismatch, ignoring update');
        return;
    }
    
    // Find the variant input that matches the updated variant value
    const variantInput = document.querySelector(`.variant-input[value="${data.variantValueId}"]`);
    
    if (variantInput) {
        // Update the price adjustment data attribute
        variantInput.dataset.priceAdjustment = data.newPriceAdjustment;
        
        // Get the label element
        const label = variantInput.nextElementSibling;
        
        // Update the price display in the label
        const priceSpan = label.querySelector('span[class*="text-red-600"], span[class*="text-green-600"]');
        if (priceSpan) {
            if (data.newPriceAdjustment > 0) {
                priceSpan.textContent = `+${parseFloat(data.newPriceAdjustment).toLocaleString('vi-VN')}đ`;
                priceSpan.className = 'text-sm ml-1 text-red-600';
            } else if (data.newPriceAdjustment < 0) {
                priceSpan.textContent = `${parseFloat(data.newPriceAdjustment).toLocaleString('vi-VN')}đ`;
                priceSpan.className = 'text-sm ml-1 text-green-600';
            } else {
                // Remove price span if adjustment is 0
                priceSpan.remove();
            }
        } else if (data.newPriceAdjustment !== 0) {
            // Create new price span if it doesn't exist
            const newPriceSpan = document.createElement('span');
            newPriceSpan.className = `text-sm ml-1 ${data.newPriceAdjustment > 0 ? 'text-red-600' : 'text-green-600'}`;
            newPriceSpan.textContent = `${data.newPriceAdjustment > 0 ? '+' : ''}${parseFloat(data.newPriceAdjustment).toLocaleString('vi-VN')}đ`;
            label.appendChild(newPriceSpan);
        }
        
        // Add animation to the label
        label.classList.add('animate-price-update');
        setTimeout(() => {
            label.classList.remove('animate-price-update');
        }, 500);
        
        // Force price recalculation if this variant is currently selected
        if (variantInput.checked) {
            window.updatePrice();
            
            // Show variant price update notification
            const variantPriceNotification = document.getElementById('variant-price-update-notification');
            if (variantPriceNotification) {
                variantPriceNotification.classList.remove('hidden');
                setTimeout(() => {
                    variantPriceNotification.classList.add('hidden');
                }, 5000);
            }
        }
        
        // Simple fade animation for variant price change (no toast)
        const variantLabel = variantInput.nextElementSibling;
        variantLabel.classList.add('variant-price-updated');
        setTimeout(() => {
            variantLabel.classList.remove('variant-price-updated');
        }, 2000);
    }
    
    // Update related products variant prices if they exist
    const relatedProducts = document.querySelectorAll('.related-product');
    relatedProducts.forEach(product => {
        const productId = product.dataset.productId;
        if (productId == data.productId) {
            // Update variant prices in related products if needed
            const variantInputs = product.querySelectorAll('.variant-input');
            variantInputs.forEach(input => {
                if (input.value == data.variantValueId) {
                    input.dataset.priceAdjustment = data.newPriceAdjustment;
                    const label = input.nextElementSibling;
                    const priceSpan = label.querySelector('span[class*="text-red-600"], span[class*="text-green-600"]');
                    if (priceSpan && data.newPriceAdjustment !== 0) {
                        priceSpan.textContent = `${data.newPriceAdjustment > 0 ? '+' : ''}${parseFloat(data.newPriceAdjustment).toLocaleString('vi-VN')}đ`;
                        priceSpan.className = `text-sm ml-1 ${data.newPriceAdjustment > 0 ? 'text-red-600' : 'text-green-600'}`;
                    }
                }
            });
        }
    });
});

// Listen for topping price updates
channel.bind('topping-price-updated', function(data) {
    console.log('Topping price update received:', data);
    
    // Get current branch ID from the hidden input
    const currentBranchId = document.getElementById('branch-select')?.value;
    if (currentBranchId && parseInt(currentBranchId) !== data.branchId) {
        console.log('Branch ID mismatch, ignoring update');
        return;
    }
    
    // Find all topping inputs that match the updated topping
    const toppingInputs = document.querySelectorAll(`.topping-input[data-topping-id="${data.toppingId}"]`);
    
    if (toppingInputs.length > 0) {
        toppingInputs.forEach(input => {
            // Update the price data attribute
            input.dataset.price = data.newPrice;
            
            // Get the label element (parent of input)
            const label = input.closest('label');
            
            // Update the price display
            const priceElement = label.querySelector('.text-xs.text-orange-500.font-medium');
            if (priceElement) {
                priceElement.textContent = `+${parseFloat(data.newPrice).toLocaleString('vi-VN')}đ`;
                
                // Add animation to the price element
                priceElement.classList.add('topping-price-updated');
                setTimeout(() => {
                    priceElement.classList.remove('topping-price-updated');
                }, 2000);
            }
            
            // Add animation to the entire topping label
            label.classList.add('topping-price-updated');
            setTimeout(() => {
                label.classList.remove('topping-price-updated');
            }, 2000);
        });
        
        // Force price recalculation if any topping is currently selected
        const selectedToppings = document.querySelectorAll('.topping-input:checked');
        if (selectedToppings.length > 0) {
            window.updatePrice();
        }
        
        // Show topping price update notification
        const toppingPriceNotification = document.getElementById('topping-price-update-notification');
        if (toppingPriceNotification) {
            toppingPriceNotification.classList.remove('hidden');
            setTimeout(() => {
                toppingPriceNotification.classList.add('hidden');
            }, 5000);
        }
    }
    
    // Update related products topping prices if they exist
    const relatedProducts = document.querySelectorAll('.related-product');
    relatedProducts.forEach(product => {
        const toppingInputs = product.querySelectorAll('.topping-input');
        toppingInputs.forEach(input => {
            if (input.dataset.toppingId == data.toppingId) {
                input.dataset.price = data.newPrice;
                const label = input.closest('label');
                const priceElement = label.querySelector('.text-xs.text-orange-500.font-medium');
                if (priceElement) {
                    priceElement.textContent = `+${parseFloat(data.newPrice).toLocaleString('vi-VN')}đ`;
                }
            }
        });
    });
});

// Listen for product variant updates (create, update, delete)
channel.bind('product-variant-updated', function(data) {
    console.log('Product variant update received:', data);
    console.log('Data structure:', {
        productId: data.productId,
        action: data.action,
        variantData: data.variantData,
        branchId: data.branchId
    });
    
    // Get current branch ID from the hidden input
    const currentBranchId = document.getElementById('branch-select')?.value;
    console.log('Current branch ID:', currentBranchId, 'Event branch ID:', data.branchId);
    
    if (currentBranchId && parseInt(currentBranchId) !== data.branchId) {
        console.log('Branch ID mismatch, ignoring update');
        return;
    }
    
    const variantsContainer = document.getElementById('variants-container');
    console.log('Variants container found:', variantsContainer);
    
    if (!variantsContainer) {
        console.log('Variants container not found!');
        return;
    }
    
    console.log('Processing action:', data.action);
    
    switch (data.action) {
        case 'created':
            handleVariantCreated(data, variantsContainer);
            break;
        case 'updated':
            handleVariantUpdated(data, variantsContainer);
            break;
        case 'deleted':
            handleVariantDeleted(data, variantsContainer);
            break;
        default:
            console.log('Unknown action:', data.action);
    }
    
    // Force price recalculation
    window.updatePrice();
    
    // Update product availability
    updateProductAvailability();
});

// Handle variant created
function handleVariantCreated(data, container) {
    console.log('Handling variant created:', data);
    console.log('Container:', container);
    console.log('Variant data:', data.variantData);
    
    // Check if variant data has values
    if (!data.variantData.variant_values || data.variantData.variant_values.length === 0) {
        console.log('No variant values found, reloading page...');
        showVariantNotification('Biến thể mới đã được thêm - Đang tải lại trang...', 'info');
        setTimeout(() => {
            window.location.reload();
        }, 2000);
        return;
    }
    
    // Add new variant options to the UI
    data.variantData.variant_values.forEach(variantValue => {
        console.log('Processing variant value:', variantValue);
        
        const attributeContainer = findOrCreateAttributeContainer(container, variantValue.attribute_name, variantValue.attribute_id);
        console.log('Attribute container found/created:', attributeContainer);
        
        // Check if variant value already exists
        const existingInput = attributeContainer.querySelector(`input[value="${variantValue.id}"]`);
        console.log('Existing input:', existingInput);
        
        if (!existingInput) {
            // Create new variant option
            const variantOption = createVariantOption(variantValue, data.variantData.id);
            console.log('Created variant option:', variantOption);
            
            if (attributeContainer && variantOption) {
                attributeContainer.appendChild(variantOption);
                console.log('Variant option appended to container');
                
                // Add animation
                variantOption.classList.add('variant-created');
                setTimeout(() => {
                    variantOption.classList.remove('variant-created');
                }, 2000);
            } else {
                console.error('Failed to append variant option - container or option is null');
            }
        }
    });
    
    // Show notification
    showVariantNotification('Biến thể mới đã được thêm', 'success');
}

// Handle variant updated
function handleVariantUpdated(data, container) {
    console.log('Handling variant updated:', data);
    
    // Update existing variant options
    data.variantData.variant_values.forEach(variantValue => {
        const existingInput = container.querySelector(`input[value="${variantValue.id}"]`);
        if (existingInput) {
            const label = existingInput.nextElementSibling;
            
            // Update variant value text
            const valueText = label.childNodes[0];
            if (valueText && valueText.nodeType === Node.TEXT_NODE) {
                valueText.textContent = variantValue.value;
            }
            
            // Update price adjustment
            existingInput.dataset.priceAdjustment = variantValue.price_adjustment;
            
            // Update price display
            updateVariantPriceDisplay(label, variantValue.price_adjustment);
            
            // Add animation
            label.classList.add('variant-updated');
            setTimeout(() => {
                label.classList.remove('variant-updated');
            }, 2000);
        }
    });
    
    // Show notification
    showVariantNotification('Biến thể đã được cập nhật', 'info');
}

// Handle variant deleted
function handleVariantDeleted(data, container) {
    console.log('Handling variant deleted:', data);
    
    // If variant_values is empty, we need to find the variant by its ID
    if (!data.variantData.variant_values || data.variantData.variant_values.length === 0) {
        console.log('No variant values found, trying to find variant by ID:', data.variantData.id);
        
        // Find all variant inputs that belong to this variant ID
        const variantInputs = container.querySelectorAll(`input[data-variant-id="${data.variantData.id}"]`);
        console.log('Found variant inputs by ID:', variantInputs.length);
        console.log('All variant inputs in container:', container.querySelectorAll('input[data-variant-id]'));
        
        if (variantInputs.length === 0) {
            console.log('No variant inputs found, reloading page...');
            showVariantNotification('Biến thể đã được xóa - Đang tải lại trang...', 'warning');
            setTimeout(() => {
                window.location.reload();
            }, 2000);
            return;
        }
        
        variantInputs.forEach((input, index) => {
            console.log(`Processing variant input ${index}:`, input);
            const label = input.nextElementSibling;
            console.log('Label found:', label);
            
            if (label) {
                // Add fade out animation
                label.classList.add('variant-deleted');
                console.log('Added variant-deleted class to label');
                setTimeout(() => {
                    if (label.parentNode) {
                        label.parentNode.removeChild(label);
                        console.log('Removed label from DOM');
                    }
                }, 500);
            }
        });
        
        // Show notification
        showVariantNotification('Biến thể đã được xóa', 'warning');
        return;
    }
    
    // Remove variant options from UI using variant values
    data.variantData.variant_values.forEach(variantValue => {
        const existingInput = container.querySelector(`input[value="${variantValue.id}"]`);
        if (existingInput) {
            const label = existingInput.nextElementSibling;
            
            // Add fade out animation
            label.classList.add('variant-deleted');
            setTimeout(() => {
                if (label.parentNode) {
                    label.parentNode.removeChild(label);
                }
            }, 500);
        }
    });
    
    // Show notification
    showVariantNotification('Biến thể đã được xóa', 'warning');
}

// Helper function to find or create attribute container
function findOrCreateAttributeContainer(container, attributeName, attributeId) {
    console.log('Looking for attribute container:', attributeName, attributeId);
    console.log('Container children:', container.children);
    
    // First, try to find existing attribute container by looking for h3 with the attribute name
    let attributeContainer = null;
    const h3Elements = container.querySelectorAll('h3');
    console.log('Found h3 elements:', h3Elements.length);
    
    for (let h3 of h3Elements) {
        console.log('Checking h3:', h3.textContent.trim(), 'vs', attributeName);
        if (h3.textContent.trim() === attributeName) {
            attributeContainer = h3.parentElement;
            console.log('Found existing attribute container:', attributeContainer);
            break;
        }
    }
    
    if (!attributeContainer) {
        console.log('Creating new attribute container for:', attributeName);
        
        // Create new attribute container
        attributeContainer = document.createElement('div');
        attributeContainer.setAttribute('data-attribute-name', attributeName);
        attributeContainer.setAttribute('data-attribute-id', attributeId);
        
        const title = document.createElement('h3');
        title.className = 'font-medium mb-2';
        title.textContent = attributeName;
        
        const optionsContainer = document.createElement('div');
        optionsContainer.className = 'flex flex-wrap gap-2';
        
        attributeContainer.appendChild(title);
        attributeContainer.appendChild(optionsContainer);
        container.appendChild(attributeContainer);
        
        console.log('Created new attribute container:', attributeContainer);
    }
    
    // Try to find the options container
    let optionsContainer = attributeContainer.querySelector('.flex.flex-wrap.gap-2');
    
    // If not found, try alternative selectors
    if (!optionsContainer) {
        optionsContainer = attributeContainer.querySelector('div:last-child');
        console.log('Using alternative selector for options container:', optionsContainer);
    }
    
    // If still not found, create one
    if (!optionsContainer) {
        console.log('Creating new options container');
        optionsContainer = document.createElement('div');
        optionsContainer.className = 'flex flex-wrap gap-2';
        attributeContainer.appendChild(optionsContainer);
    }
    
    console.log('Final options container:', optionsContainer);
    return optionsContainer;
}

// Helper function to create variant option
function createVariantOption(variantValue, variantId) {
    console.log('Creating variant option for:', variantValue, 'variantId:', variantId);
    
    const label = document.createElement('label');
    label.className = 'relative flex items-center';
    
    const input = document.createElement('input');
    input.type = 'radio';
    input.name = `attribute_${variantValue.attribute_id}`;
    input.value = variantValue.id;
    input.dataset.attributeId = variantValue.attribute_id;
    input.dataset.priceAdjustment = variantValue.price_adjustment;
    input.dataset.variantId = variantId;
    input.dataset.stockQuantity = '0'; // Default stock
    input.dataset.branchId = window.selectedBranchId || '';
    input.className = 'sr-only variant-input';
    
    console.log('Created input:', input);
    
    const span = document.createElement('span');
    span.className = 'px-4 py-2 rounded-md border cursor-pointer variant-label hover:bg-gray-50';
    span.textContent = variantValue.value;
    
    console.log('Created span with text:', variantValue.value);
    
    // Add price adjustment if not zero
    if (variantValue.price_adjustment !== 0) {
        const priceSpan = document.createElement('span');
        priceSpan.className = `text-sm ml-1 ${variantValue.price_adjustment > 0 ? 'text-red-600' : 'text-green-600'}`;
        priceSpan.textContent = `${variantValue.price_adjustment > 0 ? '+' : ''}${parseFloat(variantValue.price_adjustment).toLocaleString('vi-VN')}đ`;
        span.appendChild(priceSpan);
        console.log('Added price span:', priceSpan.textContent);
    }
    
    // Add stock display
    const stockSpan = document.createElement('span');
    stockSpan.className = 'text-xs ml-1 text-gray-500 stock-display';
    stockSpan.textContent = '(Còn 0)';
    span.appendChild(stockSpan);
    console.log('Added stock span');
    
    label.appendChild(input);
    label.appendChild(span);
    
    console.log('Final label created:', label);
    
    // Add event listener
    input.addEventListener('change', function() {
        console.log('Variant input changed:', this.value);
        // Update visual state of labels
        const attributeId = this.dataset.attributeId;
        document.querySelectorAll(`[data-attribute-id="${attributeId}"] + .variant-label`).forEach(label => {
            label.classList.remove('bg-orange-100', 'border-orange-500', 'text-orange-600');
        });
        
        this.nextElementSibling.classList.add('bg-orange-100', 'border-orange-500', 'text-orange-600');
        window.updatePrice();
    });
    
    return label;
}

// Helper function to update variant price display
function updateVariantPriceDisplay(label, priceAdjustment) {
    let priceSpan = label.querySelector('span[class*="text-red-600"], span[class*="text-green-600"]');
    
    if (priceAdjustment !== 0) {
        if (priceSpan) {
            priceSpan.textContent = `${priceAdjustment > 0 ? '+' : ''}${parseFloat(priceAdjustment).toLocaleString('vi-VN')}đ`;
            priceSpan.className = `text-sm ml-1 ${priceAdjustment > 0 ? 'text-red-600' : 'text-green-600'}`;
        } else {
            priceSpan = document.createElement('span');
            priceSpan.className = `text-sm ml-1 ${priceAdjustment > 0 ? 'text-red-600' : 'text-green-600'}`;
            priceSpan.textContent = `${priceAdjustment > 0 ? '+' : ''}${parseFloat(priceAdjustment).toLocaleString('vi-VN')}đ`;
            label.appendChild(priceSpan);
        }
    } else if (priceSpan) {
        priceSpan.remove();
    }
}

// Helper function to show variant notification
function showVariantNotification(message, type) {
    const notification = document.getElementById('variant-update-notification');
    if (notification) {
        const messageElement = notification.querySelector('span');
        if (messageElement) {
            messageElement.textContent = message;
        }
        
        // Update notification style based on type
        const notificationDiv = notification.querySelector('div');
        notificationDiv.className = `flex items-center gap-2 text-sm px-3 py-2 rounded-md border animate-fade-in`;
        
        switch (type) {
            case 'success':
                notificationDiv.classList.add('text-green-600', 'bg-green-50', 'border-green-200');
                break;
            case 'warning':
                notificationDiv.classList.add('text-yellow-600', 'bg-yellow-50', 'border-yellow-200');
                break;
            default:
                notificationDiv.classList.add('text-blue-600', 'bg-blue-50', 'border-blue-200');
        }
        
        notification.classList.remove('hidden');
        setTimeout(() => {
            notification.classList.add('hidden');
        }, 5000);
    }
}

// Initialize Pusher for discount realtime
const discountsPusher = new Pusher(window.pusherKey, {
    cluster: window.pusherCluster,
    encrypted: true,
    enabledTransports: ['ws', 'wss']
});
const discountsChannel = discountsPusher.subscribe('discounts');
discountsChannel.bind('discount-updated', function(data) {
    console.log('--- Pusher event "discount-updated" received ---');
    console.log('Data received:', data);
    setTimeout(() => {
        window.location.reload();
    }, 1000);
});