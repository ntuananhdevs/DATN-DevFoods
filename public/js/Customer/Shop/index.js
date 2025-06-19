// Add authentication class to body
document.addEventListener('DOMContentLoaded', function() {
    if (document.body.classList.contains('user-authenticated')) {
        document.body.classList.add('user-authenticated');
    }

    // Khai báo biến channel
    let productsChannel, favoritesChannel, cartChannel, branchStockChannel;
    
    // Khởi tạo Pusher với key và cluster từ window object
    const pusher = new Pusher(window.pusherKey, {
        cluster: window.pusherCluster,
        encrypted: true,
        enabledTransports: ['ws', 'wss'] // Force WebSocket transport
    });

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
        
        // Get current branch ID
        const urlParams = new URLSearchParams(window.location.search);
        const currentBranchId = urlParams.get('branch_id') || document.querySelector('meta[name="selected-branch"]')?.content;
        
        // Listen for stock update events
        branchStockChannel.bind('stock-updated', function(data) {
            console.log('Stock update received:', data);
            
            // Get current branch ID from multiple possible sources
            const urlParams = new URLSearchParams(window.location.search);
            const branchIdFromUrl = urlParams.get('branch_id');
            const branchIdFromMeta = document.querySelector('meta[name="selected-branch"]')?.content;
            const currentBranchId = branchIdFromUrl || branchIdFromMeta || '1'; // Default to branch 1 if not specified
            
            console.log('Current branch ID:', currentBranchId);
            console.log('Event branch ID:', data.branchId);
            
            // Only update if the stock change is for the current branch
            if (data.branchId == currentBranchId) {
                console.log('Updating UI for current branch');
                const productCards = document.querySelectorAll('.product-card');
                console.log('Found product cards:', productCards.length);
                
                productCards.forEach(card => {
                    try {
                        const variants = JSON.parse(card.dataset.variants);
                        console.log('Product variants:', variants);
                        
                        const variant = variants.find(v => v.id == data.productVariantId);
                        console.log('Found variant:', variant);
                        
                        if (variant) {
                            // Update stock for the variant
                            variant.stock = parseInt(data.stockQuantity);
                            
                            // Check if any variant has stock
                            const hasStock = variants.some(v => v.stock > 0);
                            console.log('Has stock:', hasStock);
                            
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
                            
                            console.log('Updated product card:', card.dataset.productId);
                        }
                    } catch (error) {
                        console.error('Error updating product stock:', error);
                    }
                });
            } else {
                console.log('Skipping update - different branch');
            }
        });

        // Listen for product update events
        productsChannel.bind('product-updated', function(data) {
            console.log('Product update received:', data);
            // Reload page to show updated product
            window.location.reload();
        });
        
        productsChannel.bind('product-created', function(data) {
            console.log('Product created:', data);
            // Reload page to show new product
            window.location.reload();
        });

        productsChannel.bind('product-deleted', function(data) {
            console.log('Product deleted:', data);
            // Remove the deleted product from the grid
            const productCard = document.querySelector(`.product-card[data-product-id="${data.product_id}"]`);
            if (productCard) {
                productCard.remove();
            }
        });
        
        // Listen for favorite updates if user is authenticated
        if (favoritesChannel) {
            favoritesChannel.bind('favorite-updated', function(data) {
                console.log('Favorite update received:', data);
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
            console.log('Cart update received:', data);
            updateCartCount(data.count);
        });

        // Handle connection state changes
        pusher.connection.bind('state_change', function(states) {
            console.log('Pusher connection state changed:', states);
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
    
    // Function to attach event listeners to newly rendered products
    function attachEventListeners() {
        // Favorite button handling
        document.querySelectorAll('.favorite-btn:not(.login-prompt-btn)').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const productCard = this.closest('.product-card');
                const productId = productCard.dataset.productId;
                const icon = this.querySelector('i');
                const isFavorite = icon.classList.contains('far');
                
                // Immediate visual effect
                if (isFavorite) {
                    icon.classList.remove('far');
                    icon.classList.add('fas', 'text-red-500');
                } else {
                    icon.classList.remove('fas', 'text-red-500');
                    icon.classList.add('far');
                }
                
                // AJAX call to update favorites
                axios.post('/api/favorites/toggle', {
                    product_id: productId,
                    is_favorite: isFavorite
                })
                .then(response => {
                    if (response.data.success) {
                        // Update wishlist counter if function exists
                        if (typeof window.updateWishlistCount === 'function') {
                            window.updateWishlistCount(response.data.count);
                        }
                        showToast(response.data.message);
                    }
                })
                .catch(error => {
                    // Revert visual change if error
                    if (isFavorite) {
                        icon.classList.remove('fas', 'text-red-500');
                        icon.classList.add('far');
                    } else {
                        icon.classList.remove('far');
                        icon.classList.add('fas', 'text-red-500');
                    }
                    console.error('Error updating favorites:', error);
                    showToast('Đã xảy ra lỗi. Vui lòng thử lại.');
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
                document.body.removeChild(toast);
            }, 300);
        }, 3000);
    }
});