// Add authentication class to body
document.addEventListener('DOMContentLoaded', function() {
    if (document.body.classList.contains('user-authenticated')) {
        document.body.classList.add('user-authenticated');
    }

    // Khai báo biến channel
    let productsChannel, favoritesChannel, cartChannel, branchStockChannel, discountsChannel;
    
    // Khởi tạo Pusher với key và cluster từ window object
    const pusher = new Pusher(window.pusherKey, {
        cluster: window.pusherCluster,
        encrypted: true,
        enabledTransports: ['ws', 'wss'] // Force WebSocket transport
    });

    // Expose Pusher instance for other scripts to use
    window.existingPusher = pusher;

    // Enable Pusher logging
    Pusher.logToConsole = true;
    
    // Subscribe to channels
    try {
        // Subscribe to branch stock channel
        branchStockChannel = pusher.subscribe('branch-stock-channel');
        
        // Subscribe to products channel
        productsChannel = pusher.subscribe('products-channel');
        
        // Subscribe to favorites channel if user is authenticated
        if (document.body.classList.contains('user-authenticated')) {
            favoritesChannel = pusher.subscribe('user-wishlist-channel');
        }
        
        // Subscribe to cart channel
        cartChannel = pusher.subscribe('user-cart-channel');
        
        // Subscribe to discounts channel
        discountsChannel = pusher.subscribe('discounts');
        
        // Get current branch ID
        const urlParams = new URLSearchParams(window.location.search);
        const currentBranchId = urlParams.get('branch_id') || document.querySelector('meta[name="selected-branch"]')?.content;
        
        // Listen for stock update events
        branchStockChannel.bind('stock-updated', function(data) {
            // Get current branch ID from multiple possible sources
            const urlParams = new URLSearchParams(window.location.search);
            const branchIdFromUrl = urlParams.get('branch_id');
            const branchIdFromMeta = document.querySelector('meta[name="selected-branch"]')?.content;
            const currentBranchId = branchIdFromUrl || branchIdFromMeta || '1'; // Default to branch 1 if not specified
            
            // Only update if the stock change is for the current branch
            if (data.branchId == currentBranchId) {
                const productCards = document.querySelectorAll('.product-card');
                
                productCards.forEach(card => {
                    try {
                        const variants = JSON.parse(card.dataset.variants);
                        
                        const variant = variants.find(v => v.id == data.productVariantId);
                        
                        if (variant) {
                            // Update stock for the variant
                            variant.stock = parseInt(data.stockQuantity);
                            
                            // Check if any variant has stock
                            const hasStock = variants.some(v => v.stock > 0);
                            
                            card.dataset.hasStock = hasStock.toString();
                            
                            // Update the button
                            const buttonContainer = card.querySelector('.flex.justify-between.items-center');
                            if (buttonContainer) {
                                const productId = card.dataset.productId;
                                const priceContainer = buttonContainer.querySelector('.flex.flex-col');
                                
                                if (hasStock) {
                                    buttonContainer.innerHTML = `
                                        <div class="flex flex-col">
                                            ${priceContainer.innerHTML}
                                        </div>
                                        <a href="/shop/products/${productId}" class="add-to-cart-btn">
                                            <i class="fas fa-shopping-cart"></i>
                                            Mua hàng
                                        </a>
                                    `;
                                } else {
                                    buttonContainer.innerHTML = `
                                        <div class="flex flex-col">
                                            ${priceContainer.innerHTML}
                                        </div>
                                        <button class="add-to-cart-btn disabled" disabled>
                                            <i class="fas fa-ban"></i>
                                            Hết hàng
                                        </button>
                                    `;
                                }
                            }
                            
                            // Update the variants data attribute
                            card.dataset.variants = JSON.stringify(variants);
                            
                            // Add animation to highlight the change
                            card.classList.add('highlight-update');
                            setTimeout(() => {
                                card.classList.remove('highlight-update');
                            }, 1000);
                        }
                    } catch (error) {
                        console.error('Error updating product stock:', error);
                    }
                });
            }
        });

        // Listen for product update events
        productsChannel.bind('product-updated', function(data) {
            // Reload page to show updated product
            window.location.reload();
        });
        
        productsChannel.bind('product-created', function(data) {
            // Reload page to show new product
            window.location.reload();
        });

        productsChannel.bind('product-deleted', function(data) {
            // Remove the deleted product from the grid
            const productCard = document.querySelector(`.product-card[data-product-id="${data.product_id}"]`);
            if (productCard) {
                productCard.remove();
            }
        });
        
        // Listen for favorite updates if user is authenticated
        if (favoritesChannel) {
            favoritesChannel.bind('favorite-updated', function(data) {
                if (data.product_id) {
                    const favoriteButtons = document.querySelectorAll(`.favorite-btn[data-product-id="${data.product_id}"]`);
                    favoriteButtons.forEach(button => {
                        const icon = button.querySelector('i');
                        if (data.is_favorite) {
                            icon.classList.remove('far');
                            icon.classList.add('fas', 'text-red-500');
                        } else {
                            icon.classList.remove('fas', 'text-red-500');
                            icon.classList.add('far');
                        }
                    });
                }
            });
        }
        
        // Listen for cart events
        cartChannel.bind('cart-updated', function(data) {
            updateCartCount(data.count);
        });

        // Listen for discount updates
        discountsChannel.bind('discount-updated', function(data) {
            handleDiscountUpdate(data);
        });
        
        // Listen for subscription success
        discountsChannel.bind('pusher:subscription_succeeded', () => {
            console.log('✅ Successfully subscribed to discounts channel');
        });

        // Listen for subscription error
        discountsChannel.bind('pusher:subscription_error', (error) => {
            console.error('❌ Failed to subscribe to discounts channel:', error);
        });

        console.log('🔧 All channels subscribed successfully');

        // Handle connection state changes
        pusher.connection.bind('state_change', function(states) {
            // Connection state changed
        });

        pusher.connection.bind('error', function(err) {
            console.error('Pusher connection error:', err);
        });

    } catch (error) {
        console.error('Error during channel subscription:', error);
    }
    
    // Function to update cart counter
    function updateCartCount(count) {
        const cartCounter = document.querySelector('#cart-counter');
        if (cartCounter) {
            cartCounter.textContent = count;
            
            // Animation to highlight the change
            cartCounter.classList.add('animate-bounce');
            setTimeout(() => {
                cartCounter.classList.remove('animate-bounce');
            }, 1000);
        }
    }
    
    // Function to handle discount updates
    function handleDiscountUpdate(data) {
        // Check if data has the expected structure
        if (!data) {
            console.error('No data received in discount update');
            return;
        }

        // Handle data structure from DiscountUpdated event
        let action, discountData;
        
        if (data.action && data.discountData) {
            // Event structure: { action: 'created', discountData: {...} }
            action = data.action;
            discountData = data.discountData;
        } else if (data.action) {
            // Direct action structure
            action = data.action;
            discountData = data;
        } else {
            console.error('Invalid data structure:', data);
            return;
        }
        
        switch (action) {
            case 'created':
                handleDiscountCreated(discountData);
                break;
            case 'updated':
                handleDiscountUpdated(discountData);
                break;
            case 'deleted':
                handleDiscountDeleted(discountData);
                break;
            default:
                // Unknown action
        }
    }
    
    function handleDiscountCreated(discountData) {
        refreshDiscountCodes();
        updateDiscountBadges(discountData);
    }

    function handleDiscountUpdated(discountData) {
        refreshDiscountCodes();
        updateDiscountBadges(discountData);
    }

    function handleDiscountDeleted(discountData) {
        showToast(`Xóa mã giảm giá: ${discountData.code}`, 'warning');
        refreshDiscountCodes();
        updateDiscountBadges(discountData);
    }

    function refreshDiscountCodes() {
        // Find all product cards with discount codes
        const productCards = document.querySelectorAll('.product-card');
        
        productCards.forEach((card, index) => {
            const discountTags = card.querySelectorAll('.discount-badge');
            if (discountTags.length > 0) {
                // Add a subtle animation to indicate update
                card.classList.add('animate-pulse');
                setTimeout(() => {
                    card.classList.remove('animate-pulse');
                }, 2000);
            }
        });
    }
    
    function updateDiscountBadges(discountData) {
        const productCards = document.querySelectorAll('.product-card');
        
        productCards.forEach((card, index) => {
            const discountTags = card.querySelectorAll('.discount-badge');
            
            discountTags.forEach(tag => {
                // Try to get discount code from data attribute or text content
                let discountCode = tag.getAttribute('data-discount-code');
                
                // If no data attribute, try to extract from text content
                if (!discountCode) {
                    const text = tag.textContent.trim();
                    // Try to extract code from text like "Giảm 10%" or "Miễn phí vận chuyển"
                    if (text.includes('Giảm') || text.includes('Miễn phí')) {
                        // For now, we'll use a more generic approach
                        discountCode = 'MATCH_ALL'; // This will match all discount badges
                    }
                }
                
                // If we found a matching discount or it's a general update
                if (discountCode === discountData.code || discountCode === 'MATCH_ALL') {
                    if (!discountData.is_active) {
                        // Hide the discount badge if discount is inactive
                        // Add animation for hiding
                        tag.classList.add('fade-out');
                        setTimeout(() => {
                            tag.style.display = 'none';
                            tag.classList.remove('fade-out');
                        }, 500);
                    } else {
                        // Show the discount badge if discount is active
                        tag.style.display = 'inline-flex';
                        
                        // Add animation for showing
                        tag.classList.add('fade-in');
                        setTimeout(() => {
                            tag.classList.remove('fade-in');
                        }, 500);
                    }
                }
            });
        });
    }
    
    // Function to attach event listeners to newly rendered products
    function attachEventListeners() {
        // Favorite button handling
        document.querySelectorAll('.favorite-btn:not(.login-prompt-btn)').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const productCard = this.closest('.product-card');
                const productId = productCard.dataset.productId;
                const icon = this.querySelector('i');
                if (!productId || !icon) return;
                const isFavorite = icon.classList.contains('far');

                // Giao diện ngay lập tức
                if (isFavorite) {
                    icon.classList.remove('far');
                    icon.classList.add('fas', 'text-red-500');
                } else {
                    icon.classList.remove('fas', 'text-red-500');
                    icon.classList.add('far');
                }

                // Gọi API toggle yêu thích
                fetch('/favorite/toggle', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
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
                    // Revert visual nếu lỗi
                    if (isFavorite) {
                        icon.classList.remove('fas', 'text-red-500');
                        icon.classList.add('far');
                    } else {
                        icon.classList.remove('far');
                        icon.classList.add('fas', 'text-red-500');
                    }
                    showToast('Đã xảy ra lỗi. Vui lòng thử lại.', 'error');
                });
            });
        });
        
        // Login prompt button handling
        document.querySelectorAll('.login-prompt-btn').forEach(button => {
            button.addEventListener('click', function() {
                const loginPopup = document.getElementById('login-popup');
                if (loginPopup) {
                    loginPopup.classList.remove('hidden');
                }
            });
        });
    }
    
    // Attach initial event listeners
    attachEventListeners();
    
    // Toast notification function
    function showToast(message, type = 'info') {
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
    }
});