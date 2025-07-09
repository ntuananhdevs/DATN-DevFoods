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
            const currentBranchId = branchIdFromUrl || branchIdFromMeta || '1';

            // Only update if the stock change is for the current branch
            if (data.branchId == currentBranchId) {
                // Duyệt tất cả card, tìm variant có id trùng productVariantId
                document.querySelectorAll('.product-card').forEach(card => {
                    try {
                        let variants = JSON.parse(card.dataset.variants);
                        let updated = false;
                        variants.forEach(v => {
                            if (v.id == data.productVariantId) {
                                v.stock = parseInt(data.stockQuantity);
                                updated = true;
                            }
                        });
                        if (!updated) return; // Không phải card này
                        // Update lại data-variants
                        card.dataset.variants = JSON.stringify(variants);
                        // Tính tổng tồn kho mới
                        const totalStock = variants.reduce((sum, v) => sum + (v.stock || 0), 0);
                        // Update stock quantity text
                        const stockQtySpan = card.querySelector('.product-stock-qty');
                        if (stockQtySpan) {
                            if (totalStock > 0) {
                                stockQtySpan.textContent = `Còn ${totalStock} sản phẩm`;
                            } else {
                                stockQtySpan.textContent = 'Hết hàng';
                            }
                        }
                        // Update trạng thái button
                        if (totalStock > 0) {
                            // Xóa class và overlay hết hàng nếu có
                            card.classList.remove('out-of-stock');
                            const overlay = card.querySelector('.out-of-stock-overlay');
                            if (overlay) overlay.remove();
                        } else {
                            // Thêm class và overlay hết hàng nếu chưa có
                            card.classList.add('out-of-stock');
                            if (!card.querySelector('.out-of-stock-overlay')) {
                                const overlayDiv = document.createElement('div');
                                overlayDiv.className = 'out-of-stock-overlay';
                                overlayDiv.innerHTML = '<span>hết hàng</span>';
                                card.querySelector('.relative').prepend(overlayDiv);
                            }
                        }
                        // Add animation to highlight the change
                        card.classList.add('highlight-update');
                        setTimeout(() => {
                            card.classList.remove('highlight-update');
                        }, 1000);
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
        S
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
            console.log('--- Pusher event "discount-updated" received ---');
            console.log('Data received:', data);
            
            // Reload page after a short delay
            setTimeout(() => {
                window.location.reload();
            }, 1000);
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

    // Favorite (heart) button logic
    document.querySelectorAll('.favorite-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            // Nếu là nút login-prompt-btn thì show popup đăng nhập
            if (btn.classList.contains('login-prompt-btn')) {
                document.getElementById('login-popup').classList.remove('hidden');
                return;
            }
            // Đã đăng nhập
            const productId = btn.getAttribute('data-product-id');
            const icon = btn.querySelector('i');
            const isFavorite = icon.classList.contains('fas');
            // Optimistic UI
            if (isFavorite) {
                icon.classList.remove('fas', 'text-red-500');
                icon.classList.add('far');
            } else {
                icon.classList.remove('far');
                icon.classList.add('fas', 'text-red-500');
            }
            // Gửi AJAX
            fetch('/wishlist' + (isFavorite ? '/' + productId : ''), {
                method: isFavorite ? 'DELETE' : 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': window.csrfToken
                },
                body: isFavorite ? null : JSON.stringify({ product_id: productId })
            })
            .then(res => res.json())
            .then(data => {
                if (data && data.message) {
                    dtmodalShowToast(isFavorite ? 'info' : 'success', {
                        title: isFavorite ? 'Thông báo' : 'Thành công',
                        message: data.message
                    });
                } else {
                    // Nếu lỗi, revert lại UI
                    if (isFavorite) {
                        icon.classList.remove('far');
                        icon.classList.add('fas', 'text-red-500');
                    } else {
                        icon.classList.remove('fas', 'text-red-500');
                        icon.classList.add('far');
                    }
                    dtmodalShowToast('error', {
                        title: 'Lỗi',
                        message: 'Có lỗi khi cập nhật yêu thích'
                    });
                }
            })
            .catch(() => {
                // Nếu lỗi, revert lại UI
                if (isFavorite) {
                    icon.classList.remove('far');
                    icon.classList.add('fas', 'text-red-500');
                } else {
                    icon.classList.remove('fas', 'text-red-500');
                    icon.classList.add('far');
                }
                dtmodalShowToast('error', {
                    title: 'Lỗi',
                    message: 'Có lỗi khi cập nhật yêu thích'
                });
            });
        });
    });

    // Đóng modal đăng nhập khi bấm nút đóng hoặc click ra ngoài
    const loginPopup = document.getElementById('login-popup');
    const closeLoginBtn = document.getElementById('close-login-popup');
    if (loginPopup && closeLoginBtn) {
        closeLoginBtn.onclick = function() {
            loginPopup.classList.add('hidden');
        };
        loginPopup.onclick = function(e) {
            if (e.target === this) this.classList.add('hidden');
        };
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                loginPopup.classList.add('hidden');
            }
        });
    }
});

// Force reload when coming back from bfcache (back/forward)
window.addEventListener('pageshow', function(event) {
    if (event.persisted || (window.performance && performance.getEntriesByType('navigation')[0]?.type === 'back_forward')) {
        window.location.reload();
    }
});

// Thêm fallback: reload khi tab được hiển thị lại nếu discount đã bị tắt (dùng localStorage flag)
document.addEventListener('visibilitychange', function() {
    if (document.visibilityState === 'visible' && window.needDiscountReload) {
        window.location.reload();
    }
});

// Lazy load section by scroll
window.addEventListener('DOMContentLoaded', function() {
    const sections = Array.from(document.querySelectorAll('.category-section'));
    let currentSection = 0;
    let loading = false;
    function showNextSection() {
        if (currentSection + 1 < sections.length) {
            currentSection++;
            const section = sections[currentSection];
            // 1. Show skeletons
            const skeletons = section.querySelector('.skeletons-container');
            const cardsContainer = section.querySelector('.product-cards-container');
            if (skeletons && cardsContainer) {
                skeletons.style.display = 'grid';
                cardsContainer.style.display = 'none';
                section.style.display = 'block';
                // 2. After delay, hide skeletons, show cards with fade-in
                setTimeout(() => {
                    skeletons.style.display = 'none';
                    cardsContainer.style.display = 'grid';
                    const cards = cardsContainer.querySelectorAll('.product-card');
                    cards.forEach((card, idx) => {
                        setTimeout(() => {
                            card.classList.add('fade-in-card');
                        }, idx * 60); // staggered
                    });
                }, 400);
            } else {
                section.style.display = 'block';
            }
        }
    }
    function onScroll() {
        if (loading) return;
        const lastVisible = sections[currentSection];
        if (!lastVisible) return;
        const rect = lastVisible.getBoundingClientRect();
        if (rect.bottom < window.innerHeight + 200) {
            loading = true;
            setTimeout(() => {
                showNextSection();
                loading = false;
            }, 100);
        }
    }
    window.addEventListener('scroll', onScroll);
    // Hiệu ứng fade-in cho section đầu tiên
    setTimeout(() => {
        const firstSection = sections[0];
        if (firstSection) {
            const cards = firstSection.querySelectorAll('.product-card');
            cards.forEach((card, idx) => {
                setTimeout(() => {
                    card.classList.add('fade-in-card');
                }, idx * 60);
            });
        }
    }, 200);
});