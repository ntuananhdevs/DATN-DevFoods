// Cart functionality

// Helper functions moved to the outer scope to be accessible

async function updateCartTotals() {
    let subtotal = 0;
    const cartItems = document.querySelectorAll('.cart-item');
    
    cartItems.forEach(item => {
        const quantityElement = item.querySelector('.item-quantity');
        const itemPriceElement = item.querySelector('.item-price');
        
        if (quantityElement && itemPriceElement) {
            const quantity = parseInt(quantityElement.textContent);
            const itemPriceText = itemPriceElement.textContent;
            let itemPrice = parseInt(itemPriceText.replace(/\D/g, ''));
            // Không cộng toppingTotal nữa, vì giá đã bao gồm topping
            const itemTotal = itemPrice * quantity;
            subtotal += itemTotal;
            
            const itemTotalElement = item.querySelector('.item-total');
            if (itemTotalElement) {
                itemTotalElement.textContent = itemTotal.toLocaleString() + 'đ';
            }
        }
    });
    
    const subtotalEl = document.getElementById('subtotal-js');
    if (subtotalEl) {
        subtotalEl.textContent = subtotal.toLocaleString() + 'đ';
    }
    
    const discountContainer = document.getElementById('discount-container-js');
    let discount = 0;
    if (discountContainer && !discountContainer.classList.contains('hidden')) {
        const discountText = document.getElementById('discount-js').textContent;
        discount = parseInt(discountText.replace(/\D/g, '')) || 0;
    }
    
    // No total calculation needed - only subtotal will be displayed
    
    // Update mini cart totals
    updateMiniCartTotals(subtotal);
    
    // Update mini cart count
    updateMiniCartCount();
    
    const checkoutButton = document.querySelector('a[href*="checkout"]');
    if (checkoutButton) {
        if (cartItems.length === 0) {
            checkoutButton.classList.add('opacity-50', 'pointer-events-none');
        } else {
            checkoutButton.classList.remove('opacity-50', 'pointer-events-none');
        }
    }
}

function updateMiniCartTotals(subtotal) {
    const miniCartSubtotal = document.getElementById('mini-cart-subtotal');
    
    if (miniCartSubtotal) {
        miniCartSubtotal.textContent = subtotal.toLocaleString() + 'đ';
    }
}

function updateMiniCartCount(cartCountFromServer = null) {
    const miniCartCount = document.getElementById('mini-cart-count');
    if (miniCartCount) {
        if (cartCountFromServer !== null) {
            miniCartCount.textContent = cartCountFromServer;
        } else {
            // Fallback: đếm số cart-item trên giao diện nếu không có dữ liệu server
            const cartItems = document.querySelectorAll('.cart-item');
            miniCartCount.textContent = cartItems.length;
        }
    }
    // Nếu có icon cart trên header
    const cartCounter = document.getElementById('cart-counter');
    if (cartCounter) {
        if (cartCountFromServer !== null) {
            cartCounter.textContent = cartCountFromServer;
        } else {
            const cartItems = document.querySelectorAll('.cart-item');
            cartCounter.textContent = cartItems.length;
        }
    }
}

function updateSelectAllState() {
    const selectAll = document.getElementById('select-all-cart');
    if (selectAll) {
        const allCheckboxes = document.querySelectorAll('.cart-item-checkbox');
        const checkedCheckboxes = document.querySelectorAll('.cart-item-checkbox:checked');
        
        // Nếu tất cả checkbox đều được chọn thì check "Chọn tất cả"
        if (allCheckboxes.length > 0 && allCheckboxes.length === checkedCheckboxes.length) {
            selectAll.checked = true;
        } else {
            selectAll.checked = false;
        }
    }
}

function updateQuantityButtonStates(cartItem) {
    const decreaseBtn = cartItem.querySelector('.decrease-quantity');
    const increaseBtn = cartItem.querySelector('.increase-quantity');
    const quantityElement = cartItem.querySelector('.item-quantity');
    
    if (!decreaseBtn || !increaseBtn || !quantityElement) return;

    const stockQuantity = parseInt(cartItem.getAttribute('data-stock-quantity') || 0);
    const currentQuantity = parseInt(quantityElement.textContent);
    
    decreaseBtn.disabled = currentQuantity <= 1;
    decreaseBtn.classList.toggle('opacity-50', currentQuantity <= 1);
    decreaseBtn.classList.toggle('cursor-not-allowed', currentQuantity <= 1);
    
    increaseBtn.disabled = currentQuantity >= stockQuantity;
    increaseBtn.classList.toggle('opacity-50', currentQuantity >= stockQuantity);
    increaseBtn.classList.toggle('cursor-not-allowed', currentQuantity >= stockQuantity);
}

function initializeButtonStates() {
    const cartItems = document.querySelectorAll('.cart-item');
    cartItems.forEach(item => {
        updateQuantityButtonStates(item);
    });
}

async function refreshCartView() {
    try {
        const response = await fetch(window.location.href);
        if (!response.ok) {
            throw new Error('Network response was not ok.');
        }
        const htmlText = await response.text();
        const parser = new DOMParser();
        const newDoc = parser.parseFromString(htmlText, 'text/html');

        const newCartContent = newDoc.querySelector('.lg\\:col-span-2');
        const currentCartContainer = document.querySelector('.lg\\:col-span-2');

        const newSummaryContent = newDoc.querySelector('.bg-white.rounded-lg.shadow-sm.p-6.sticky.top-4');
        const currentSummaryContainer = document.querySelector('.bg-white.rounded-lg.shadow-sm.p-6.sticky.top-4');

        if (newCartContent && currentCartContainer) {
            currentCartContainer.innerHTML = newCartContent.innerHTML;
        }

        if (newSummaryContent && currentSummaryContainer) {
            currentSummaryContainer.innerHTML = newSummaryContent.innerHTML;
        }

        // Re-attach event listeners and initialize states
        attachDynamicEventListeners();
        initializeButtonStates();
        updateSelectAllState();
        
        // Update all UI components after refresh
        updateCartTotals();
        updateSummaryBySelected();
        renderSelectedItemsSummary();
        updateCheckoutButtonState();
        
        // Update mini cart
        updateMiniCartCount();
        if (typeof updateMiniCartTotals === 'function') {
            let currentSubtotal = 0;
            const cartItems = document.querySelectorAll('.cart-item');
            cartItems.forEach(item => {
                const itemTotalText = item.querySelector('.item-total')?.textContent || '0';
                const itemTotal = parseInt(itemTotalText.replace(/\D/g, '')) || 0;
                currentSubtotal += itemTotal;
            });
            updateMiniCartTotals(currentSubtotal);
        }

    } catch (error) {
        console.error('Failed to refresh cart view:', error);
        dtmodalShowToast('error', {
            title: 'Lỗi',
            message: 'Không thể cập nhật giỏ hàng. Vui lòng tải lại trang.'
        });
        throw error; // Re-throw to handle in calling function
    }
}


function attachDynamicEventListeners() {
    const decreaseButtons = document.querySelectorAll('.decrease-quantity');
    const increaseButtons = document.querySelectorAll('.increase-quantity');
    const removeButtons = document.querySelectorAll('.remove-item');
    const addSuggestedButtons = document.querySelectorAll('.add-suggested');

    decreaseButtons.forEach(button => {
        button.addEventListener('click', function() {
            const itemId = this.getAttribute('data-id');
            const cartItem = this.closest('.cart-item');
            const quantityElement = this.parentElement.querySelector('.item-quantity');
            let quantity = parseInt(quantityElement.textContent);
            
            if (quantity > 1) {
                quantity--;
                quantityElement.textContent = quantity;
                updateQuantityButtonStates(cartItem);
                
                // Update all UI components immediately for better UX
                updateCartTotals();
                updateSummaryBySelected();
                renderSelectedItemsSummary();
                updateCheckoutButtonState();
                
                fetch('/cart/update', {
                    method: 'POST',
                    credentials: 'include',
                    headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': window.csrfToken},
                    body: JSON.stringify({cart_item_id: itemId, quantity: quantity})
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        if (window.updateCartCount) window.updateCartCount(data.cart_count);
                    } else {
                        // Revert changes if failed
                        quantityElement.textContent = quantity + 1;
                        updateQuantityButtonStates(cartItem);
                        updateCartTotals();
                        updateSummaryBySelected();
                        renderSelectedItemsSummary();
                        updateCheckoutButtonState();
                        dtmodalShowToast('error', {title: 'Lỗi', message: data.message || 'Đã xảy ra lỗi. Vui lòng thử lại.'});
                    }
                })
                .catch(error => {
                    console.error('Error updating cart:', error);
                    // Revert changes if failed
                    quantityElement.textContent = quantity + 1;
                    updateQuantityButtonStates(cartItem);
                    updateCartTotals();
                    updateSummaryBySelected();
                    renderSelectedItemsSummary();
                    updateCheckoutButtonState();
                    dtmodalShowToast('error', {title: 'Lỗi', message: 'Đã xảy ra lỗi. Vui lòng thử lại.'});
                });
            }
        });
    });

    increaseButtons.forEach(button => {
        button.addEventListener('click', function() {
            const itemId = this.getAttribute('data-id');
            const cartItem = this.closest('.cart-item');
            const quantityElement = this.parentElement.querySelector('.item-quantity');
            let quantity = parseInt(quantityElement.textContent);
            const stockQuantity = parseInt(cartItem.getAttribute('data-stock-quantity') || 0);
            
            if (quantity < stockQuantity) {
                quantity++;
                quantityElement.textContent = quantity;
                updateQuantityButtonStates(cartItem);
                
                // Update all UI components immediately for better UX
                updateCartTotals();
                updateSummaryBySelected();
                renderSelectedItemsSummary();
                updateCheckoutButtonState();
                
                fetch('/cart/update', {
                    method: 'POST',
                    credentials: 'include',
                    headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': window.csrfToken},
                    body: JSON.stringify({cart_item_id: itemId, quantity: quantity})
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        if (window.updateCartCount) window.updateCartCount(data.cart_count);
                    } else {
                        // Revert changes if failed
                        quantityElement.textContent = quantity - 1;
                        updateQuantityButtonStates(cartItem);
                        updateCartTotals();
                        updateSummaryBySelected();
                        renderSelectedItemsSummary();
                        updateCheckoutButtonState();
                        dtmodalShowToast('error', {title: 'Lỗi', message: data.message || 'Đã xảy ra lỗi. Vui lòng thử lại.'});
                    }
                })
                .catch(error => {
                    console.error('Error updating cart:', error);
                    // Revert changes if failed
                    quantityElement.textContent = quantity - 1;
                    updateQuantityButtonStates(cartItem);
                    updateCartTotals();
                    updateSummaryBySelected();
                    renderSelectedItemsSummary();
                    updateCheckoutButtonState();
                    dtmodalShowToast('error', {title: 'Lỗi', message: 'Đã xảy ra lỗi. Vui lòng thử lại.'});
                });
            } else {
                dtmodalShowToast('warning', {title: 'Cảnh báo', message: 'Đã đạt giới hạn số lượng có sẵn'});
            }
        });
    });

    removeButtons.forEach(button => {
        button.addEventListener('click', function() {
            const itemId = this.getAttribute('data-id');
            const cartItem = this.closest('.cart-item');
            
            fetch('/cart/remove', {
                method: 'POST',
                credentials: 'include',
                headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': window.csrfToken},
                body: JSON.stringify({cart_item_id: itemId})
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    cartItem.remove();
                    
                    // Update all UI components after removing item
                    updateCartTotals();
                    updateSummaryBySelected();
                    renderSelectedItemsSummary();
                    updateCheckoutButtonState();
                    updateSelectAllState();
                    
                    if (window.updateCartCount) window.updateCartCount(data.cart_count);
                    dtmodalShowToast('success', {title: 'Thành công', message: 'Sản phẩm đã được xóa khỏi giỏ hàng'});
                    
                    const remainingItems = document.querySelectorAll('.cart-item');
                    if (remainingItems.length === 0) {
                        showEmptyCartMessage();
                    }
                } else {
                    dtmodalShowToast('error', {title: 'Lỗi', message: data.message || 'Đã xảy ra lỗi. Vui lòng thử lại.'});
                }
            })
            .catch(error => {
                console.error('Error removing item:', error);
                dtmodalShowToast('error', {title: 'Lỗi', message: 'Đã xảy ra lỗi. Vui lòng thử lại.'});
            });
        });
    });

    addSuggestedButtons.forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.getAttribute('data-product-id');
            const variantId = this.getAttribute('data-variant-id');
            const branchId = this.getAttribute('data-branch-id');
            const variantValuesRaw = this.getAttribute('data-variant-values');
            let variantValues = [];
            try {
                variantValues = JSON.parse(variantValuesRaw);
            } catch (e) {
                variantValues = [];
            }
            const btn = this;

            btn.disabled = true;
            btn.classList.add('opacity-50');

            fetch('/cart/add', {
                method: 'POST',
                credentials: 'include',
                headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': window.csrfToken},
                body: JSON.stringify({
                    product_id: productId,
                    variant_values: variantValues,
                    branch_id: branchId,
                    quantity: 1,
                    toppings: []
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    dtmodalShowToast('success', {
                        title: 'Thành công',
                        message: 'Đã thêm sản phẩm vào giỏ hàng'
                    });
                    
                    // Update cart count
                    if (window.updateCartCount && data.cart_count) {
                        window.updateCartCount(data.cart_count);
                    }
                    
                    // Use refreshCartView but then properly restore UI state
                    refreshCartView().then(() => {
                        // refreshCartView already handles all UI updates
                        console.log('Cart refreshed successfully');
                    }).catch(error => {
                        console.error('Failed to refresh cart:', error);
                    });
                    
                } else {
                    dtmodalShowToast('error', {
                        title: 'Lỗi',
                        message: data.message || 'Có lỗi khi thêm sản phẩm vào giỏ hàng'
                    });
                }
            })
            .catch(error => {
                dtmodalShowToast('error', {
                    title: 'Lỗi',
                    message: 'Có lỗi xảy ra khi thêm sản phẩm vào giỏ hàng'
                });
            })
            .finally(() => {
                btn.disabled = false;
                btn.classList.remove('opacity-50');
            });
        });
    });
}

function showEmptyCartMessage() {
    // Find the container that holds the cart items grid
    const cartGridContainer = document.querySelector('.lg\\:col-span-2 .bg-white.rounded-lg.shadow-sm');
    
    if (cartGridContainer) {
        // Create the empty cart message HTML
        const emptyCartHTML = `
            <div class="p-8 text-center">
                <div class="flex justify-center mb-4">
                    <i class="fas fa-shopping-cart text-gray-300 text-5xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-700 mb-2">Giỏ hàng của bạn đang trống</h3>
                <p class="text-gray-500 mb-6">Hãy thêm sản phẩm vào giỏ hàng để tiếp tục</p>
                <a href="/products" class="bg-orange-500 hover:bg-orange-600 text-white px-6 py-2 rounded-md transition-colors inline-block">
                    Tiếp tục mua sắm
                </a>
            </div>
        `;
        // Replace only the content of the grid container, not the entire column
        cartGridContainer.innerHTML = emptyCartHTML;
    }

    // Clear suggested products
    const suggestedContainer = document.getElementById('suggested-products-container');
    if (suggestedContainer) {
        suggestedContainer.innerHTML = '<div class="col-span-4 text-center text-gray-400">Không có sản phẩm gợi ý</div>';
    }

    // Hide the "select all" checkbox
    const selectAllContainer = document.querySelector('input#select-all-cart')?.parentElement;
    if (selectAllContainer) {
        selectAllContainer.style.display = 'none';
    }
}

function renderSelectedItemsSummary() {
    const container = document.getElementById('selected-items-summary');
    if (!container) return;
    
    const checked = document.querySelectorAll('.cart-item-checkbox:checked');
    
    // Clear container if no items are selected
    if (checked.length === 0) {
        container.innerHTML = '';
        return;
    }
    
    let html = '<div class="font-bold mb-2">Đơn Hàng Của Bạn</div>';
    let validItems = 0;
    
    checked.forEach(cb => {
        const item = cb.closest('.cart-item');
        if (!item) return; // Skip if item doesn't exist (was removed)
        
        const img = item.querySelector('img, .fa-image');
        const name = item.querySelector('h3')?.textContent || '';
        const desc = item.querySelector('.text-sm.text-gray-500')?.textContent || '';
        const qty = item.querySelector('.item-quantity')?.textContent || '1';
        const price = item.querySelector('.item-price')?.textContent || '';
        const toppings = Array.from(item.querySelectorAll('.text-xs.text-gray-600 li')).map(li => li.textContent.trim());
        
        html += `<div class='flex items-center gap-3 mb-3'>
            <div class='w-14 h-14 bg-gray-100 rounded flex items-center justify-center overflow-hidden'>`;
        if (img && img.tagName === 'IMG') {
            html += `<img src='${img.src}' alt='' class='object-cover w-full h-full'>`;
        } else {
            html += `<i class='fas fa-image text-gray-400 text-2xl'></i>`;
        }
        html += `</div>
            <div class='flex-1 min-w-0'>
                <div class='font-semibold'>${name}</div>
                <div class='text-xs text-gray-500 line-clamp-1'>${desc}</div>
                ${toppings.length > 0 ? `<div class='text-xs text-orange-600 mt-1'>+${toppings.length} topping</div>` : ''}
            </div>
            <div class='text-right'>
                <div class='font-bold'>${price}</div>
                <div class='text-xs text-gray-500'>SL: ${qty}</div>
            </div>
        </div>`;
        validItems++;
    });
    
    // Only update if we have valid items
    if (validItems > 0) {
        container.innerHTML = html;
    } else {
        container.innerHTML = '';
    }
}

function updateSummaryBySelected() {
    let subtotal = 0;
    let discount = 0;
    
    // Lấy các item được chọn
    const checked = document.querySelectorAll('.cart-item-checkbox:checked');
    let validItems = 0;
    
    checked.forEach(cb => {
        const item = cb.closest('.cart-item');
        if (!item) return; // Skip if item doesn't exist (was removed)
        
        // Lấy giá từng sản phẩm (tổng đã nhân số lượng)
        const itemTotalText = item.querySelector('.item-total')?.textContent || '0';
        const itemTotal = parseInt(itemTotalText.replace(/\D/g, '')) || 0;
        subtotal += itemTotal;
        validItems++;
    });
    
    // Giảm giá (nếu có, có thể lấy từ session hoặc tính lại)
    if (typeof sessionDiscount !== 'undefined') {
        discount = sessionDiscount;
    } else {
        discount = 0;
    }
    
    // Update summary elements - only subtotal needed
    const subtotalEl = document.getElementById('subtotal-js');
    const discountEl = document.getElementById('discount-js');
    
    if (subtotalEl) subtotalEl.textContent = subtotal.toLocaleString() + 'đ';
    if (discountEl) discountEl.textContent = discount > 0 ? '-' + discount.toLocaleString() + 'đ' : '-0đ';
    
    // Update mini cart totals
    if (typeof updateMiniCartTotals === 'function') {
        updateMiniCartTotals(subtotal);
    }
}

function updateCheckoutButtonState() {
    const checkoutButton = document.getElementById('checkout-btn');
    if (!checkoutButton) return;
    
    const checked = document.querySelectorAll('.cart-item-checkbox:checked');
    let validCheckedItems = 0;
    
    checked.forEach(cb => {
        const item = cb.closest('.cart-item');
        if (item) validCheckedItems++; // Only count if item exists
    });
    
    const anyChecked = validCheckedItems > 0;
    checkoutButton.disabled = !anyChecked;
    
    if (anyChecked) {
        checkoutButton.classList.remove('opacity-50', 'pointer-events-none');
    } else {
        checkoutButton.classList.add('opacity-50', 'pointer-events-none');
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // Initialize cart summary and states
    updateSummaryBySelected();
    renderSelectedItemsSummary();
    updateCheckoutButtonState();
    updateSelectAllState();
    
    // Initialize mini cart totals
    if (typeof updateMiniCartTotals === 'function') {
        let initialSubtotal = 0;
        const cartItems = document.querySelectorAll('.cart-item');
        cartItems.forEach(item => {
            const itemTotalText = item.querySelector('.item-total')?.textContent || '0';
            const itemTotal = parseInt(itemTotalText.replace(/\D/g, '')) || 0;
            initialSubtotal += itemTotal;
        });
        updateMiniCartTotals(initialSubtotal);
    }
    
    // Initialize mini cart count
    if (typeof updateMiniCartCount === 'function') {
        updateMiniCartCount();
    }
    
    // Add event listeners for individual checkboxes
    document.querySelectorAll('.cart-item-checkbox').forEach(cb => {
        cb.addEventListener('change', function() {
            updateSummaryBySelected();
            renderSelectedItemsSummary();
            updateCheckoutButtonState();
            updateSelectAllState();
        });
    });
    
    // Add event listener for select all checkbox
    const selectAll = document.getElementById('select-all-cart');
    if (selectAll) {
        selectAll.addEventListener('change', function() {
            document.querySelectorAll('.cart-item-checkbox').forEach(cb => cb.checked = selectAll.checked);
            updateSummaryBySelected();
            renderSelectedItemsSummary();
            updateCheckoutButtonState();
        });
    }

    // Initialize Pusher, etc.
    const pusher = new Pusher(window.pusherKey, {
        cluster: window.pusherCluster,
        encrypted: true
    });
    const cartChannel = pusher.subscribe('user-cart-channel');
    const branchStockChannel = pusher.subscribe('branch-stock-channel');

    cartChannel.bind('cart-updated', function(data) {
        window.location.reload();
    });
    branchStockChannel.bind('product-price-updated', function(data) {
        console.log('Product price updated:', data);
        updateCartItemPrices(data.productId, null, null, data.basePrice);
        showPriceUpdateNotification('Giá sản phẩm đã được cập nhật');
    });
    branchStockChannel.bind('variant-price-updated', function(data) {
        console.log('Variant price updated:', data);
        updateCartItemPrices(data.productId, data.variantValueId, null, null, data.newPriceAdjustment);
        showPriceUpdateNotification('Giá biến thể đã được cập nhật');
    });
    branchStockChannel.bind('topping-price-updated', function(data) {
        console.log('Topping price updated:', data);
        updateCartItemPrices(null, null, data.toppingId, null, null, data.newPrice);
        showPriceUpdateNotification('Giá topping đã được cập nhật');
    });

    // Initial event listener attachments
    attachDynamicEventListeners();
    initializeButtonStates();

    // Mini cart functionality
    const floatingCartButton = document.getElementById('floating-cart-button');
    const miniCart = document.getElementById('mini-cart');
    const miniCartOverlay = document.getElementById('mini-cart-overlay');
    const closeMiniCartButton = document.getElementById('close-mini-cart');
    
    if (floatingCartButton && miniCart && miniCartOverlay && closeMiniCartButton) {
        floatingCartButton.addEventListener('click', function() {
            miniCart.classList.remove('translate-x-full');
            miniCartOverlay.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        });
        
        function closeMiniCart() {
            miniCart.classList.add('translate-x-full');
            miniCartOverlay.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }
        
        closeMiniCartButton.addEventListener('click', closeMiniCart);
        miniCartOverlay.addEventListener('click', closeMiniCart);
        
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeMiniCart();
            }
        });
    }

    // Khi reload trang, nếu có biến cart_count từ server (render blade), truyền vào updateMiniCartCount
    if (typeof window.cartCountFromServer !== 'undefined') {
        updateMiniCartCount(window.cartCountFromServer);
    } else {
        updateMiniCartCount();
    }
});
