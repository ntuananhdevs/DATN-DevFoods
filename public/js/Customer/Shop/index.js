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
            // Instead of reloading, update the specific product card
            updateProductCard(data.product);
        });
        
        productsChannel.bind('product-created', function(data) {
            console.log('Product created:', data);
            // Instead of reloading, fetch the latest products
            fetchLatestProducts();
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
    
    // Function to fetch latest products
    function fetchLatestProducts() {
        const currentUrl = new URL(window.location.href);
        const category = currentUrl.searchParams.get('category') || '';
        const search = currentUrl.searchParams.get('search') || '';
        const sort = currentUrl.searchParams.get('sort') || 'popular';
        const branchId = currentUrl.searchParams.get('branch_id') || '';
        const page = currentUrl.searchParams.get('page') || 1;

        // Show loading state
        const productsGrid = document.querySelector('.grid.grid-cols-1.sm\\:grid-cols-2.md\\:grid-cols-3.lg\\:grid-cols-4.gap-6');
        if (productsGrid) {
            productsGrid.innerHTML = '<div class="col-span-4 flex justify-center py-12"><i class="fas fa-spinner fa-spin fa-3x text-orange-500"></i></div>';
        }

        // Fetch products via AJAX
        axios.get('/api/products', {
            params: {
                sort: sort,
                category: category,
                search: search,
                branch_id: branchId,
                page: page
            }
        })
        .then(response => {
            if (response.data.success) {
                // Render products
                renderProducts(response.data.products);
                
                // Update pagination
                renderPagination(response.data.pagination);
            }
        })
        .catch(error => {
            console.error('Error fetching products:', error);
            if (productsGrid) {
                productsGrid.innerHTML = `
                    <div class="col-span-4 text-center py-8">
                        <i class="fas fa-exclamation-circle text-red-500 text-4xl mb-4"></i>
                        <h3 class="text-xl font-bold text-gray-700 mb-2">Đã xảy ra lỗi</h3>
                        <p class="text-gray-500">Không thể tải sản phẩm. Vui lòng thử lại sau.</p>
                    </div>
                `;
            }
        });
    }
    
    // Function to update product stock
    function updateProductStock(variantId, stockQuantity, branchId) {
        const productCards = document.querySelectorAll('.product-card');
        
        productCards.forEach(card => {
            try {
                const variants = JSON.parse(card.dataset.variants);
                const currentBranchId = document.querySelector('meta[name="selected-branch"]')?.content;
                
                // Check if this product has the variant that was updated
                const hasUpdatedVariant = variants.some(v => v.id == variantId && v.branch_id == branchId);
                
                if (hasUpdatedVariant) {
                    // Update stock for the specific variant
                    const variantIndex = variants.findIndex(v => v.id == variantId && v.branch_id == branchId);
                    if (variantIndex !== -1) {
                        variants[variantIndex].stock = stockQuantity;
                    }
                    
                    // Check if any variant has stock for current branch
                    const hasStock = variants.some(v => v.branch_id == currentBranchId && v.stock > 0);
                    card.dataset.hasStock = hasStock.toString();
                    
                    // Update the button
                    const buttonContainer = card.querySelector('.flex.justify-between.items-center');
                    if (buttonContainer) {
                        const productId = card.dataset.productId;
                        if (hasStock) {
                            buttonContainer.innerHTML = `
                                <div class="flex flex-col">
                                    ${buttonContainer.querySelector('.flex.flex-col').innerHTML}
                                </div>
                                <a href="/shop/products/${productId}" class="add-to-cart-btn">
                                    <i class="fas fa-shopping-cart"></i>
                                    Mua hàng
                                </a>
                            `;
                        } else {
                            buttonContainer.innerHTML = `
                                <div class="flex flex-col">
                                    ${buttonContainer.querySelector('.flex.flex-col').innerHTML}
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
                }
            } catch (error) {
                console.error('Error updating product stock:', error);
            }
        });
    }
    
    // Function to update product card
    function updateProductCard(product) {
        const productCard = document.querySelector(`.product-card[data-product-id="${product.id}"]`);
        if (!productCard) {
            // If product card doesn't exist, fetch latest products
            fetchLatestProducts();
            return;
        }

        // Update product data attributes
        productCard.dataset.variants = JSON.stringify(product.variants.map(variant => ({
            id: variant.id,
            stock: variant.stock_quantity,
            branch_id: variant.branch_id
        })));
        productCard.dataset.hasStock = product.has_stock ? 'true' : 'false';

        // Update price
        const priceElement = productCard.querySelector('.product-price');
        if (product.discount_price && product.base_price > product.discount_price) {
            priceElement.textContent = `${new Intl.NumberFormat('vi-VN').format(product.discount_price)}đ`;
            
            // Update original price if exists
            let originalPriceElement = productCard.querySelector('.product-original-price');
            if (!originalPriceElement) {
                originalPriceElement = document.createElement('span');
                originalPriceElement.className = 'product-original-price';
                priceElement.parentNode.appendChild(originalPriceElement);
            }
            originalPriceElement.textContent = `${new Intl.NumberFormat('vi-VN').format(product.base_price)}đ`;
            
            // Update discount badge
            const discountPercent = Math.round(((product.base_price - product.discount_price) / product.base_price) * 100);
            let badgeElement = productCard.querySelector('.badge-sale');
            if (!badgeElement) {
                badgeElement = document.createElement('span');
                badgeElement.className = 'custom-badge badge-sale';
                productCard.querySelector('.relative').appendChild(badgeElement);
            }
            badgeElement.textContent = `-${discountPercent}%`;
        } else {
            priceElement.textContent = `${new Intl.NumberFormat('vi-VN').format(product.base_price)}đ`;
            
            // Remove original price if exists
            const originalPriceElement = productCard.querySelector('.product-original-price');
            if (originalPriceElement) {
                originalPriceElement.remove();
            }
            
            // Remove discount badge if exists
            const badgeElement = productCard.querySelector('.badge-sale');
            if (badgeElement) {
                badgeElement.remove();
            }
        }

        // Update buy button
        const buttonContainer = productCard.querySelector('.flex.justify-between.items-center');
        if (buttonContainer) {
            if (product.has_stock) {
                buttonContainer.innerHTML = `
                    <div class="flex flex-col">
                        ${buttonContainer.querySelector('.flex.flex-col').innerHTML}
                    </div>
                    <a href="/shop/products/${product.id}" class="add-to-cart-btn">
                        <i class="fas fa-shopping-cart"></i>
                        Mua hàng
                    </a>
                `;
            } else {
                buttonContainer.innerHTML = `
                    <div class="flex flex-col">
                        ${buttonContainer.querySelector('.flex.flex-col').innerHTML}
                    </div>
                    <button class="add-to-cart-btn disabled" disabled>
                        <i class="fas fa-ban"></i>
                        Hết hàng
                    </button>
                `;
            }
        }
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

    // Sort handling with AJAX
    const sortSelect = document.getElementById('sort-select');
    const productsGrid = document.querySelector('.grid.grid-cols-1.sm\\:grid-cols-2.md\\:grid-cols-3.lg\\:grid-cols-4.gap-6');
    const paginationContainer = document.querySelector('.pagination-container');
    
    if (sortSelect && productsGrid) {
        sortSelect.addEventListener('change', function() {
            // Show loading state
            productsGrid.innerHTML = '<div class="col-span-4 flex justify-center py-12"><i class="fas fa-spinner fa-spin fa-3x text-orange-500"></i></div>';
            
            // Get current filters
            const currentUrl = new URL(window.location.href);
            const category = currentUrl.searchParams.get('category') || '';
            const search = currentUrl.searchParams.get('search') || '';
            const branchId = currentUrl.searchParams.get('branch_id') || '';
            
            // Fetch products via AJAX
            axios.get('/api/products', {
                params: {
                    sort: this.value,
                    category: category,
                    search: search,
                    branch_id: branchId,
                    page: 1 // Reset to first page when sorting changes
                }
            })
            .then(response => {
                if (response.data.success) {
                    // Update URL without reloading the page
                    currentUrl.searchParams.set('sort', this.value);
                    window.history.pushState({}, '', currentUrl.toString());
                    
                    // Render products
                    renderProducts(response.data.products);
                    
                    // Update pagination
                    renderPagination(response.data.pagination);
                }
            })
            .catch(error => {
                console.error('Error fetching products:', error);
                productsGrid.innerHTML = `
                    <div class="col-span-4 text-center py-8">
                        <i class="fas fa-exclamation-circle text-red-500 text-4xl mb-4"></i>
                        <h3 class="text-xl font-bold text-gray-700 mb-2">Đã xảy ra lỗi</h3>
                        <p class="text-gray-500">Không thể tải sản phẩm. Vui lòng thử lại sau.</p>
                    </div>
                `;
            });
        });
    }
    
    // Function to render products
    function renderProducts(products) {
        if (products.length === 0) {
            productsGrid.innerHTML = `
                <div class="col-span-4 text-center py-8">
                    <i class="fas fa-search text-gray-400 text-4xl mb-4"></i>
                    <h3 class="text-xl font-bold text-gray-700 mb-2">Không tìm thấy sản phẩm</h3>
                    <p class="text-gray-500">Không có sản phẩm nào phù hợp với tiêu chí tìm kiếm của bạn.</p>
                </div>
            `;
            return;
        }
        
        let html = '';
        
        products.forEach(product => {
            const imageUrl = product.primary_image ? product.primary_image.s3_url : '';
            const isFavorite = product.is_favorite ? 'fas fa-heart text-red-500' : 'far fa-heart';
            const hasDiscount = product.discount_price && product.base_price > product.discount_price;
            const discountPercent = hasDiscount ? Math.round(((product.base_price - product.discount_price) / product.base_price) * 100) : 0;
            const isNew = new Date(product.created_at).getTime() > new Date().getTime() - (7 * 24 * 60 * 60 * 1000);
            const isAuthenticated = document.body.classList.contains('user-authenticated');
            
            // Convert variants to JSON string
            const variantsJson = JSON.stringify(product.variants.map(variant => ({
                id: variant.id,
                stock: variant.stock_quantity,
                branch_id: variant.branch_id
            })));
            
            html += `
                <div class="product-card bg-white rounded-lg overflow-hidden" 
                    data-product-id="${product.id}"
                    data-variants='${variantsJson}'
                    data-has-stock="${product.has_stock ? 'true' : 'false'}">
                    <div class="relative">
                        <a href="/shop/products/${product.id}" class="block">
                            ${imageUrl ? 
                                `<img src="${imageUrl}" alt="${product.name}" class="product-image">` : 
                                `<div class="no-image-placeholder">
                                    <i class="far fa-image"></i>
                                </div>`
                            }
                        </a>
                        
                        ${isAuthenticated ? 
                            `<button class="favorite-btn absolute top-2 right-2 p-2 rounded-full bg-white shadow-md hover:bg-gray-100 transition-colors" data-product-id="${product.id}">
                                <i class="${isFavorite}"></i>
                            </button>` : 
                            `<button class="favorite-btn login-prompt-btn absolute top-2 right-2 p-2 rounded-full bg-white shadow-md hover:bg-gray-100 transition-colors">
                                <i class="far fa-heart"></i>
                            </button>`
                        }

                        ${hasDiscount ? 
                            `<span class="custom-badge badge-sale">-${discountPercent}%</span>` : 
                            (isNew ? `<span class="custom-badge badge-new">Mới</span>` : '')
                        }
                    </div>

                    <div class="p-4">
                        <div class="flex items-center mb-2">
                            <div class="rating-stars flex">
                                ${renderStars(product.average_rating)}
                            </div>
                            <span class="rating-count ml-2">(${product.reviews_count})</span>
                        </div>

                        <a href="/shop/products/${product.id}" class="block">
                            <h3 class="product-title">${product.name}</h3>
                        </a>

                        <p class="text-gray-600 text-sm mb-4 line-clamp-2">
                            ${product.short_description || ''}
                        </p>

                        <div class="flex justify-between items-center">
                            <div class="flex flex-col">
                                ${hasDiscount ? 
                                    `<span class="product-price">${new Intl.NumberFormat('vi-VN').format(product.discount_price)}đ</span>
                                    <span class="product-original-price">${new Intl.NumberFormat('vi-VN').format(product.base_price)}đ</span>` :
                                    `<span class="product-price">${new Intl.NumberFormat('vi-VN').format(product.base_price)}đ</span>`
                                }
                            </div>
                            ${product.has_stock ? 
                                `<a href="/shop/products/${product.id}" class="add-to-cart-btn">
                                    <i class="fas fa-shopping-cart"></i>
                                    Mua hàng
                                </a>` : 
                                `<button class="add-to-cart-btn disabled" disabled>
                                    <i class="fas fa-ban"></i>
                                    Hết hàng
                                </button>`
                            }
                        </div>
                    </div>
                </div>
            `;
        });
        
        productsGrid.innerHTML = html;
        
        // Reattach event listeners
        attachEventListeners();
    }
    
    // Function to render stars
    function renderStars(rating) {
        let starsHtml = '';
        for(let i = 1; i <= 5; i++) {
            if(i <= Math.floor(rating)) {
                starsHtml += '<i class="fas fa-star"></i>';
            } else if(i - 0.5 <= rating) {
                starsHtml += '<i class="fas fa-star-half-alt"></i>';
            } else {
                starsHtml += '<i class="far fa-star"></i>';
            }
        }
        return starsHtml;
    }
    
    // Function to render pagination
    function renderPagination(pagination) {
        if (!paginationContainer) return;
        
        if (pagination.last_page <= 1) {
            paginationContainer.innerHTML = '';
            return;
        }
        
        const currentUrl = new URL(window.location.href);
        let html = '';
        
        // Previous page link
        if (pagination.current_page === 1) {
            html += `
                <span class="pagination-item disabled">
                    <i class="fas fa-chevron-left"></i>
                </span>
            `;
        } else {
            const prevUrl = new URL(currentUrl);
            prevUrl.searchParams.set('page', pagination.current_page - 1);
            html += `
                <a href="${prevUrl.toString()}" class="pagination-item pagination-link" data-page="${pagination.current_page - 1}">
                    <i class="fas fa-chevron-left"></i>
                </a>
            `;
        }
        
        // Page numbers
        for (let i = 1; i <= pagination.last_page; i++) {
            const pageUrl = new URL(currentUrl);
            pageUrl.searchParams.set('page', i);
            
            if (i === pagination.current_page) {
                html += `<span class="pagination-item active">${i}</span>`;
            } else {
                html += `<a href="${pageUrl.toString()}" class="pagination-item pagination-link" data-page="${i}">${i}</a>`;
            }
        }
        
        // Next page link
        if (pagination.current_page === pagination.last_page) {
            html += `
                <span class="pagination-item disabled">
                    <i class="fas fa-chevron-right"></i>
                </span>
            `;
        } else {
            const nextUrl = new URL(currentUrl);
            nextUrl.searchParams.set('page', pagination.current_page + 1);
            html += `
                <a href="${nextUrl.toString()}" class="pagination-item pagination-link" data-page="${pagination.current_page + 1}">
                    <i class="fas fa-chevron-right"></i>
                </a>
            `;
        }
        
        paginationContainer.innerHTML = html;
        
        // Add event listeners to pagination links
        document.querySelectorAll('.pagination-link').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Get current filters
                const currentUrl = new URL(window.location.href);
                const category = currentUrl.searchParams.get('category') || '';
                const search = currentUrl.searchParams.get('search') || '';
                const sort = currentUrl.searchParams.get('sort') || 'popular';
                const branchId = currentUrl.searchParams.get('branch_id') || '';
                const page = this.dataset.page;
                
                // Show loading state
                productsGrid.innerHTML = '<div class="col-span-4 flex justify-center py-12"><i class="fas fa-spinner fa-spin fa-3x text-orange-500"></i></div>';
                
                // Fetch products via AJAX
                axios.get('/api/products', {
                    params: {
                        sort: sort,
                        category: category,
                        search: search,
                        branch_id: branchId,
                        page: page
                    }
                })
                .then(response => {
                    if (response.data.success) {
                        // Update URL without reloading the page
                        currentUrl.searchParams.set('page', page);
                        window.history.pushState({}, '', currentUrl.toString());
                        
                        // Render products
                        renderProducts(response.data.products);
                        
                        // Update pagination
                        renderPagination(response.data.pagination);
                        
                        // Scroll to top of products
                        window.scrollTo({
                            top: productsGrid.offsetTop - 100,
                            behavior: 'smooth'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error fetching products:', error);
                });
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
    
    // Category filter handling with AJAX
    const categoryButtons = document.querySelectorAll('.category-btn');
    
    categoryButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Update active state
            categoryButtons.forEach(btn => {
                btn.classList.remove('bg-orange-500', 'text-white');
                btn.classList.add('bg-gray-100', 'text-gray-700');
            });
            this.classList.remove('bg-gray-100', 'text-gray-700');
            this.classList.add('bg-orange-500', 'text-white');
            
            // Show loading state
            productsGrid.innerHTML = '<div class="col-span-4 flex justify-center py-12"><i class="fas fa-spinner fa-spin fa-3x text-orange-500"></i></div>';
            
            // Get current filters
            const currentUrl = new URL(window.location.href);
            const search = currentUrl.searchParams.get('search') || '';
            const sort = currentUrl.searchParams.get('sort') || 'popular';
            const category = this.dataset.category;
            const branchId = currentUrl.searchParams.get('branch_id') || '';
            
            // Fetch products via AJAX
            axios.get('/api/products', {
                params: {
                    sort: sort,
                    category: category,
                    search: search,
                    branch_id: branchId,
                    page: 1 // Reset to first page when category changes
                }
            })
            .then(response => {
                if (response.data.success) {
                    // Update URL without reloading the page
                    if (category) {
                        currentUrl.searchParams.set('category', category);
                    } else {
                        currentUrl.searchParams.delete('category');
                    }
                    currentUrl.searchParams.set('page', 1);
                    window.history.pushState({}, '', currentUrl.toString());
                    
                    // Render products
                    renderProducts(response.data.products);
                    
                    // Update pagination
                    renderPagination(response.data.pagination);
                    
                    // Scroll to top of products if needed
                    if (window.scrollY > productsGrid.offsetTop) {
                        window.scrollTo({
                            top: productsGrid.offsetTop - 100,
                            behavior: 'smooth'
                        });
                    }
                }
            })
            .catch(error => {
                console.error('Error fetching products:', error);
                productsGrid.innerHTML = `
                    <div class="col-span-4 text-center py-8">
                        <i class="fas fa-exclamation-circle text-red-500 text-4xl mb-4"></i>
                        <h3 class="text-xl font-bold text-gray-700 mb-2">Đã xảy ra lỗi</h3>
                        <p class="text-gray-500">Không thể tải sản phẩm. Vui lòng thử lại sau.</p>
                    </div>
                `;
            });
        });
    });
    
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

    // Search form handling with AJAX
    const searchForm = document.getElementById('search-form');
    const searchInput = document.getElementById('search-input');
    
    if (searchForm && searchInput) {
        searchForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const searchValue = searchInput.value.trim();
            
            // Show loading state
            productsGrid.innerHTML = '<div class="col-span-4 flex justify-center py-12"><i class="fas fa-spinner fa-spin fa-3x text-orange-500"></i></div>';
            
            // Get current filters
            const currentUrl = new URL(window.location.href);
            const category = currentUrl.searchParams.get('category') || '';
            const sort = currentUrl.searchParams.get('sort') || 'popular';
            const branchId = currentUrl.searchParams.get('branch_id') || '';
            
            // Fetch products via AJAX
            axios.get('/api/products', {
                params: {
                    sort: sort,
                    category: category,
                    search: searchValue,
                    branch_id: branchId,
                    page: 1 // Reset to first page when search changes
                }
            })
            .then(response => {
                if (response.data.success) {
                    // Update URL without reloading the page
                    if (searchValue) {
                        currentUrl.searchParams.set('search', searchValue);
                    } else {
                        currentUrl.searchParams.delete('search');
                    }
                    currentUrl.searchParams.set('page', 1);
                    window.history.pushState({}, '', currentUrl.toString());
                    
                    // Render products
                    renderProducts(response.data.products);
                    
                    // Update pagination
                    renderPagination(response.data.pagination);
                }
            })
            .catch(error => {
                console.error('Error fetching products:', error);
                productsGrid.innerHTML = `
                    <div class="col-span-4 text-center py-8">
                        <i class="fas fa-exclamation-circle text-red-500 text-4xl mb-4"></i>
                        <h3 class="text-xl font-bold text-gray-700 mb-2">Đã xảy ra lỗi</h3>
                        <p class="text-gray-500">Không thể tải sản phẩm. Vui lòng thử lại sau.</p>
                    </div>
                `;
            });
        });
        
        // Also handle "Enter" key press
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                searchForm.dispatchEvent(new Event('submit'));
            }
        });
    }
});
