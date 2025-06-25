// Cart functionality

// Helper functions moved to the outer scope to be accessible
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
            
            const itemTotal = itemPrice * quantity;
            subtotal += itemTotal;
            
            const itemTotalElement = item.querySelector('.item-total');
            if (itemTotalElement) {
                itemTotalElement.textContent = itemTotal.toLocaleString() + 'đ';
            }
        }
    });
    
    const subtotalEl = document.getElementById('subtotal');
    if (subtotalEl) {
        subtotalEl.textContent = subtotal.toLocaleString() + 'đ';
    }
    
    const discountContainer = document.getElementById('discount-container');
    let discount = 0;
    if (discountContainer && !discountContainer.classList.contains('hidden')) {
        const discountText = document.getElementById('discount').textContent;
        discount = parseInt(discountText.replace(/\D/g, '')) || 0;
    }
    
    const shippingEl = document.getElementById('shipping');
    if (shippingEl) {
        const shipping = subtotal > 100000 ? 0 : 15000;
        shippingEl.textContent = shipping === 0 ? 'Miễn phí' : shipping.toLocaleString() + 'đ';
        
        const total = subtotal + shipping - discount;
        const totalEl = document.getElementById('total');
        if (totalEl) {
            totalEl.textContent = total.toLocaleString() + 'đ';
        }
    }
    
    const checkoutButton = document.querySelector('a[href*="checkout"]');
    if (checkoutButton) {
        if (cartItems.length === 0) {
            checkoutButton.classList.add('opacity-50', 'pointer-events-none');
        } else {
            checkoutButton.classList.remove('opacity-50', 'pointer-events-none');
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

        attachDynamicEventListeners();
        initializeButtonStates();

    } catch (error) {
        console.error('Failed to refresh cart view:', error);
        dtmodalShowToast('error', {
            title: 'Lỗi',
            message: 'Không thể cập nhật giỏ hàng. Vui lòng tải lại trang.'
        });
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
                updateCartTotals();
                
                fetch('/cart/update', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': window.csrfToken},
                    body: JSON.stringify({cart_item_id: itemId, quantity: quantity})
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        if (window.updateCartCount) window.updateCartCount(data.cart_count);
                    } else {
                        quantityElement.textContent = quantity + 1;
                        updateQuantityButtonStates(cartItem);
                        updateCartTotals();
                        dtmodalShowToast('error', {title: 'Lỗi', message: data.message || 'Đã xảy ra lỗi. Vui lòng thử lại.'});
                    }
                })
                .catch(error => {
                    console.error('Error updating cart:', error);
                    quantityElement.textContent = quantity + 1;
                    updateQuantityButtonStates(cartItem);
                    updateCartTotals();
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
                updateCartTotals();
                
                fetch('/cart/update', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': window.csrfToken},
                    body: JSON.stringify({cart_item_id: itemId, quantity: quantity})
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        if (window.updateCartCount) window.updateCartCount(data.cart_count);
                    } else {
                        quantityElement.textContent = quantity - 1;
                        updateQuantityButtonStates(cartItem);
                        updateCartTotals();
                        dtmodalShowToast('error', {title: 'Lỗi', message: data.message || 'Đã xảy ra lỗi. Vui lòng thử lại.'});
                    }
                })
                .catch(error => {
                    console.error('Error updating cart:', error);
                    quantityElement.textContent = quantity - 1;
                    updateQuantityButtonStates(cartItem);
                    updateCartTotals();
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
                headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': window.csrfToken},
                body: JSON.stringify({cart_item_id: itemId})
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    cartItem.remove();
                    updateCartTotals();
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
                    if (window.updateCartCount && data.cart_count) {
                        window.updateCartCount(data.cart_count);
                    }
                    refreshCartView();
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
    const cartContainer = document.querySelector('.lg\\:col-span-2');
    if (cartContainer) {
        cartContainer.innerHTML = `
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <div class="p-8 text-center">
                    <div class="flex justify-center mb-4"><i class="fas fa-shopping-cart text-gray-300 text-5xl"></i></div>
                    <h3 class="text-xl font-bold text-gray-700 mb-2">Giỏ hàng của bạn đang trống</h3>
                    <p class="text-gray-500 mb-6">Hãy thêm sản phẩm vào giỏ hàng để tiếp tục</p>
                    <a href="/products" class="bg-orange-500 hover:bg-orange-600 text-white px-6 py-2 rounded-md transition-colors inline-block">Tiếp tục mua sắm</a>
                </div>
            </div>`;
    }
    const checkoutButton = document.querySelector('a[href*="checkout"]');
    if (checkoutButton) checkoutButton.classList.add('opacity-50', 'pointer-events-none');
    
    const miniCartItems = document.querySelectorAll('#mini-cart .flex.gap-3');
    miniCartItems.forEach(item => item.remove());
    const miniCartContent = document.querySelector('#mini-cart .flex-1');
    if (miniCartContent) {
        miniCartContent.innerHTML = `
            <div class="text-center py-8">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4"><i class="fas fa-shopping-cart text-3xl text-gray-400"></i></div>
                <p class="text-gray-500 font-medium">Giỏ hàng của bạn đang trống</p>
            </div>`;
    }
}


document.addEventListener('DOMContentLoaded', function() {
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

    // Listeners for static elements
    const applyButton = document.getElementById('apply-coupon');
    const couponInput = document.getElementById('coupon-code');
    if (applyButton && couponInput) {
        applyButton.addEventListener('click', function() {
            const couponCode = couponInput.value.trim();
            
            if (couponCode === '') {
                dtmodalShowToast('warning', {
                    title: 'Cảnh báo',
                    message: 'Vui lòng nhập mã giảm giá'
                });
                return;
            }
            
            if (couponCode.toUpperCase() === 'FASTFOOD10') {
                const subtotalText = document.getElementById('subtotal').textContent;
                const subtotal = parseInt(subtotalText.replace(/\D/g, ''));
                const discount = Math.round(subtotal * 0.1);
                
                document.getElementById('discount-container').classList.remove('hidden');
                document.getElementById('discount').textContent = '-' + discount.toLocaleString() + 'đ';
                
                updateCartTotals();
                
                fetch('/coupon/apply', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': window.csrfToken},
                    body: JSON.stringify({
                        coupon_code: couponCode,
                        discount: discount
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        dtmodalShowToast('success', {
                            title: 'Thành công',
                            message: 'Áp dụng mã giảm giá thành công'
                        });
                        const checkoutButton = document.querySelector('a[href*="checkout"]');
                        const total = parseInt(document.getElementById('total').textContent.replace(/\D/g, ''));
                        checkoutButton.href = checkoutButton.href + "?discount=" + discount;
                    } else {
                        dtmodalShowToast('error', {
                            title: 'Lỗi',
                            message: data.message || 'Đã xảy ra lỗi khi áp dụng mã giảm giá'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error applying coupon:', error);
                    dtmodalShowToast('error', {
                        title: 'Lỗi',
                        message: 'Đã xảy ra lỗi khi áp dụng mã giảm giá'
                    });
                });
            } else {
                dtmodalShowToast('warning', {
                    title: 'Cảnh báo',
                    message: 'Mã giảm giá không hợp lệ'
                });
            }
        });
    }

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
});
