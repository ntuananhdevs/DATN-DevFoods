// Cart functionality
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Pusher
    const pusher = new Pusher(window.pusherKey, {
        cluster: window.pusherCluster,
        encrypted: true
    });
    
    // Subscribe to cart channel
    const cartChannel = pusher.subscribe('user-cart-channel');
    
    // Subscribe to branch stock channel for price updates
    const branchStockChannel = pusher.subscribe('branch-stock-channel');
    
    // Listen for cart updates
    cartChannel.bind('cart-updated', function(data) {
        // Reload the page when cart is updated from elsewhere
        window.location.reload();
    });
    
    // Listen for product price updates
    branchStockChannel.bind('product-price-updated', function(data) {
        console.log('Product price updated:', data);
        updateCartItemPrices(data.productId, null, null, data.basePrice);
        showPriceUpdateNotification('Giá sản phẩm đã được cập nhật');
    });
    
    // Listen for variant price updates
    branchStockChannel.bind('variant-price-updated', function(data) {
        console.log('Variant price updated:', data);
        updateCartItemPrices(data.productId, data.variantValueId, null, null, data.newPriceAdjustment);
        showPriceUpdateNotification('Giá biến thể đã được cập nhật');
    });
    
    // Listen for topping price updates
    branchStockChannel.bind('topping-price-updated', function(data) {
        console.log('Topping price updated:', data);
        updateCartItemPrices(null, null, data.toppingId, null, null, data.newPrice);
        showPriceUpdateNotification('Giá topping đã được cập nhật');
    });
    
    // Function to update cart item prices
    function updateCartItemPrices(productId, variantValueId, toppingId, newBasePrice, newVariantAdjustment, newToppingPrice) {
        // Update main cart items
        const cartItems = document.querySelectorAll('.cart-item');
        
        // Update mini cart items
        const miniCartItems = document.querySelectorAll('#mini-cart .flex.gap-3');
        
        // Combine both sets of items
        const allItems = [...cartItems, ...miniCartItems];
        
        // If no items found, return early
        if (allItems.length === 0) {
            return;
        }
        
        allItems.forEach(item => {
            const itemProductId = item.getAttribute('data-product-id');
            const itemVariantValueIds = JSON.parse(item.getAttribute('data-variant-value-ids') || '[]');
            const itemToppingIds = JSON.parse(item.getAttribute('data-topping-ids') || '[]');
            
            let shouldUpdate = false;
            let priceChange = 0;
            
            // Check if this item needs price update
            if (productId && itemProductId == productId) {
                if (newBasePrice !== null) {
                    // Base price update
                    const oldBasePrice = parseFloat(item.getAttribute('data-base-price') || 0);
                    priceChange = newBasePrice - oldBasePrice;
                    item.setAttribute('data-base-price', newBasePrice);
                    shouldUpdate = true;
                }
                
                if (newVariantAdjustment !== null && variantValueId && itemVariantValueIds.includes(parseInt(variantValueId))) {
                    // Variant price adjustment update
                    const oldAdjustment = parseFloat(item.getAttribute('data-variant-adjustment') || 0);
                    priceChange = newVariantAdjustment - oldAdjustment;
                    item.setAttribute('data-variant-adjustment', newVariantAdjustment);
                    shouldUpdate = true;
                }
            }
            
            if (newToppingPrice !== null && toppingId && itemToppingIds.includes(parseInt(toppingId))) {
                // Topping price update
                const oldToppingPrice = parseFloat(item.getAttribute('data-topping-price') || 0);
                priceChange = newToppingPrice - oldToppingPrice;
                item.setAttribute('data-topping-price', newToppingPrice);
                shouldUpdate = true;
            }
            
            if (shouldUpdate) {
                // Update item price display
                const itemPriceElement = item.querySelector('.item-price');
                const itemTotalElement = item.querySelector('.item-total');
                const quantityElement = item.querySelector('.item-quantity');
                
                if (itemPriceElement && itemTotalElement && quantityElement) {
                    const quantity = parseInt(quantityElement.textContent);
                    const currentItemPrice = parseFloat(itemPriceElement.textContent.replace(/\D/g, ''));
                    const newItemPrice = currentItemPrice + priceChange;
                    const newItemTotal = newItemPrice * quantity;
                    
                    // Animate price update
                    itemPriceElement.classList.add('price-updated');
                    itemTotalElement.classList.add('price-updated');
                    
                    // Update price displays
                    itemPriceElement.textContent = newItemPrice.toLocaleString() + 'đ';
                    itemTotalElement.textContent = newItemTotal.toLocaleString() + 'đ';
                    
                    // Remove animation class after animation completes
                    setTimeout(() => {
                        itemPriceElement.classList.remove('price-updated');
                        itemTotalElement.classList.remove('price-updated');
                    }, 2000);
                    
                    // Update cart totals (only for main cart, not mini cart)
                    if (item.classList.contains('cart-item')) {
                        updateCartTotals();
                    }
                }
            }
        });
    }
    
    // Function to show price update notification
    function showPriceUpdateNotification(message) {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = 'fixed top-4 right-4 bg-orange-500 text-white px-4 py-2 rounded-lg shadow-lg z-50 animate-fade-in';
        notification.innerHTML = `
            <div class="flex items-center gap-2">
                <i class="fas fa-tags"></i>
                <span>${message}</span>
            </div>
        `;
        
        // Add to DOM
        document.body.appendChild(notification);
        
        // Remove notification after 3 seconds
        setTimeout(() => {
            if (document.body.contains(notification)) {
                notification.classList.add('animate-fade-out');
                setTimeout(() => {
                    if (document.body.contains(notification)) {
                        document.body.removeChild(notification);
                    }
                }, 300);
            }
        }, 3000);
    }
    
    // Cart functionality
    const decreaseButtons = document.querySelectorAll('.decrease-quantity');
    const increaseButtons = document.querySelectorAll('.increase-quantity');
    const removeButtons = document.querySelectorAll('.remove-item');
    const addSuggestedButtons = document.querySelectorAll('.add-suggested');
    const applyButton = document.getElementById('apply-coupon');
    const couponInput = document.getElementById('coupon-code');
    
    // Update cart totals
    function updateCartTotals() {
        let subtotal = 0;
        const cartItems = document.querySelectorAll('.cart-item');
        
        cartItems.forEach(item => {
            const quantityElement = item.querySelector('.item-quantity');
            const itemPriceElement = item.querySelector('.item-price');
            
            if (quantityElement && itemPriceElement) {
                const quantity = parseInt(quantityElement.textContent);
                const itemPriceText = itemPriceElement.textContent;
                const itemPrice = parseInt(itemPriceText.replace(/\D/g, ''));
                
                // Calculate item total based on current quantity and price
                const itemTotal = itemPrice * quantity;
                subtotal += itemTotal;
                
                // Update item total display
                const itemTotalElement = item.querySelector('.item-total');
                if (itemTotalElement) {
                    itemTotalElement.textContent = itemTotal.toLocaleString() + 'đ';
                }
            }
        });
        
        document.getElementById('subtotal').textContent = subtotal.toLocaleString() + 'đ';
        
        // Check if discount is applied
        const discountContainer = document.getElementById('discount-container');
        const discountText = document.getElementById('discount').textContent;
        let discount = 0;
        
        if (!discountContainer.classList.contains('hidden')) {
            discount = parseInt(discountText.replace(/\D/g, ''));
        }
        
        // Free shipping for orders over 100,000đ
        const shipping = subtotal > 100000 ? 0 : 15000;
        document.getElementById('shipping').textContent = shipping === 0 ? 'Miễn phí' : shipping.toLocaleString() + 'đ';
        
        // Calculate total
        const total = subtotal + shipping - discount;
        document.getElementById('total').textContent = total.toLocaleString() + 'đ';
        
        // Update checkout button state
        const checkoutButton = document.querySelector('a[href*="checkout"]');
        if (cartItems.length === 0) {
            checkoutButton.classList.add('opacity-50', 'pointer-events-none');
        } else {
            checkoutButton.classList.remove('opacity-50', 'pointer-events-none');
        }
    }
    
    // Decrease quantity
    decreaseButtons.forEach(button => {
        button.addEventListener('click', function() {
            const itemId = this.getAttribute('data-id');
            const quantityElement = this.parentElement.querySelector('.item-quantity');
            let quantity = parseInt(quantityElement.textContent);
            
            if (quantity > 1) {
                quantity--;
                quantityElement.textContent = quantity;
                
                // Update cart totals immediately with new quantity
                updateCartTotals();
                
                // Update via controller
                fetch('/cart/update', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': window.csrfToken
                    },
                    body: JSON.stringify({
                        cart_item_id: itemId,
                        quantity: quantity
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update cart counter in header
                        if (window.updateCartCount) {
                            window.updateCartCount(data.cart_count);
                        }
                    } else {
                        showToast(data.message || 'Đã xảy ra lỗi. Vui lòng thử lại.');
                        // Revert quantity if update failed
                        quantityElement.textContent = quantity + 1;
                        updateCartTotals();
                    }
                })
                .catch(error => {
                    console.error('Error updating cart:', error);
                    showToast('Đã xảy ra lỗi. Vui lòng thử lại.');
                    // Revert quantity if update failed
                    quantityElement.textContent = quantity + 1;
                    updateCartTotals();
                });
            }
        });
    });
    
    // Increase quantity
    increaseButtons.forEach(button => {
        button.addEventListener('click', function() {
            const itemId = this.getAttribute('data-id');
            const quantityElement = this.parentElement.querySelector('.item-quantity');
            let quantity = parseInt(quantityElement.textContent);
            
            quantity++;
            quantityElement.textContent = quantity;
            
            // Update cart totals immediately with new quantity
            updateCartTotals();
            
            // Update via controller
            fetch('/cart/update', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': window.csrfToken
                },
                body: JSON.stringify({
                    cart_item_id: itemId,
                    quantity: quantity
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update cart counter in header
                    if (window.updateCartCount) {
                        window.updateCartCount(data.cart_count);
                    }
                } else {
                    showToast(data.message || 'Đã xảy ra lỗi. Vui lòng thử lại.');
                    // Revert quantity if update failed
                    quantityElement.textContent = quantity - 1;
                    updateCartTotals();
                }
            })
            .catch(error => {
                console.error('Error updating cart:', error);
                showToast('Đã xảy ra lỗi. Vui lòng thử lại.');
                // Revert quantity if update failed
                quantityElement.textContent = quantity - 1;
                updateCartTotals();
            });
        });
    });
    
    // Remove item
    removeButtons.forEach(button => {
        button.addEventListener('click', function() {
            const itemId = this.getAttribute('data-id');
            const cartItem = this.closest('.cart-item');
            
            // Remove via controller
            fetch('/cart/remove', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': window.csrfToken
                },
                body: JSON.stringify({
                    cart_item_id: itemId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    cartItem.remove();
                    updateCartTotals();
                    
                    // Update cart counter in header
                    if (window.updateCartCount) {
                        window.updateCartCount(data.cart_count);
                    }
                    
                    // Show toast notification
                    showToast('Sản phẩm đã được xóa khỏi giỏ hàng');
                    
                    // Check if cart is empty
                    const remainingItems = document.querySelectorAll('.cart-item');
                    if (remainingItems.length === 0) {
                        // Reload to show empty cart message
                        window.location.reload();
                    }
                } else {
                    showToast(data.message || 'Đã xảy ra lỗi. Vui lòng thử lại.');
                }
            })
            .catch(error => {
                console.error('Error removing item:', error);
                showToast('Đã xảy ra lỗi. Vui lòng thử lại.');
            });
        });
    });
    
    // Add suggested product
    addSuggestedButtons.forEach(button => {
        button.addEventListener('click', function() {
            const productName = this.closest('.bg-white').querySelector('h3').textContent.trim();
            
            // Show toast notification
            showToast(`Đã thêm ${productName} vào giỏ hàng`);
            
            // In a real application, you would add the product to the cart
            // and update the cart UI
        });
    });
    
    // Apply coupon
    if (applyButton && couponInput) {
        applyButton.addEventListener('click', function() {
            const couponCode = couponInput.value.trim();
            
            if (couponCode === '') {
                showToast('Vui lòng nhập mã giảm giá');
                return;
            }
            
            // Simulate coupon validation
            if (couponCode.toUpperCase() === 'FASTFOOD10') {
                // Calculate 10% discount
                const subtotalText = document.getElementById('subtotal').textContent;
                const subtotal = parseInt(subtotalText.replace(/\D/g, ''));
                const discount = Math.round(subtotal * 0.1);
                
                // Show discount in summary
                document.getElementById('discount-container').classList.remove('hidden');
                document.getElementById('discount').textContent = '-' + discount.toLocaleString() + 'đ';
                
                // Update total
                updateCartTotals();
                
                // Save discount to session via controller
                fetch('/coupon/apply', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': window.csrfToken
                    },
                    body: JSON.stringify({
                        coupon_code: couponCode,
                        discount: discount
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showToast('Áp dụng mã giảm giá thành công');
                        // Update checkout button to include discount
                        const checkoutButton = document.querySelector('a[href*="checkout"]');
                        const total = parseInt(document.getElementById('total').textContent.replace(/\D/g, ''));
                        checkoutButton.href = checkoutButton.href + "?discount=" + discount;
                    } else {
                        showToast(data.message || 'Đã xảy ra lỗi khi áp dụng mã giảm giá');
                    }
                })
                .catch(error => {
                    console.error('Error applying coupon:', error);
                    showToast('Đã xảy ra lỗi khi áp dụng mã giảm giá');
                });
            } else {
                showToast('Mã giảm giá không hợp lệ');
            }
        });
    }
    
    // Simple toast notification function
    function showToast(message) {
        // Create toast element
        const toast = document.createElement('div');
        toast.className = 'fixed bottom-4 right-4 bg-gray-800 text-white px-4 py-2 rounded-lg shadow-lg z-50 transition-opacity duration-300 opacity-0';
        toast.textContent = message;
        
        // Add to DOM
        document.body.appendChild(toast);
        
        // Show toast
        setTimeout(() => {
            toast.classList.remove('opacity-0');
            toast.classList.add('opacity-100');
        }, 10);
        
        // Hide and remove toast after 3 seconds
        setTimeout(() => {
            toast.classList.remove('opacity-100');
            toast.classList.add('opacity-0');
            
            setTimeout(() => {
                if (document.body.contains(toast)) {
                    document.body.removeChild(toast);
                }
            }, 300);
        }, 3000);
    }
    
    // Mini cart functionality
    const floatingCartButton = document.getElementById('floating-cart-button');
    const miniCart = document.getElementById('mini-cart');
    const miniCartOverlay = document.getElementById('mini-cart-overlay');
    const closeMiniCartButton = document.getElementById('close-mini-cart');
    
    if (floatingCartButton && miniCart && miniCartOverlay && closeMiniCartButton) {
        // Open mini cart
        floatingCartButton.addEventListener('click', function() {
            miniCart.classList.remove('translate-x-full');
            miniCartOverlay.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        });
        
        // Close mini cart
        function closeMiniCart() {
            miniCart.classList.add('translate-x-full');
            miniCartOverlay.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }
        
        closeMiniCartButton.addEventListener('click', closeMiniCart);
        miniCartOverlay.addEventListener('click', closeMiniCart);
        
        // Close on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeMiniCart();
            }
        });
    }
});
