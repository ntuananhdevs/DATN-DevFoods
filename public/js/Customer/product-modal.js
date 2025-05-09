// Product Modal JavaScript for Jollibee Website

document.addEventListener('DOMContentLoaded', function() {
    // Initialize product modal functionality
    initProductModal();

    // Sample product data (in a real application, this would come from a database)
    window.sampleProducts = [
        {
            id: 1,
            name: "Gà Giòn Vui Vẻ (1 miếng)",
            description: "Gà rán giòn thơm ngon, hương vị đặc trưng của Jollibee với lớp bột chiên giòn rụm và thịt gà mềm, thơm ngon.",
            price: 40000,
            image: "images/products/ga-gion-1-mieng.jpg",
            category: "Gà Giòn Vui Vẻ",
            options: [
                { name: "Thịt Đùi", price: 0 },
                { name: "Thịt Ức", price: 0 },
                { name: "Thịt Cánh", price: 5000 }
            ],
            addons: [
                { name: "Khoai Tây Chiên (Vừa)", price: 25000 },
                { name: "Nước Ngọt (Vừa)", price: 15000 },
                { name: "Sốt Mayonnaise", price: 5000 }
            ],
            ingredients: [
                "Thịt gà tươi",
                "Bột chiên xù đặc biệt",
                "Gia vị Jollibee",
                "Dầu thực vật"
            ],
            allergens: ["Gluten", "Đậu nành"],
            nutrition: {
                calories: 250,
                protein: 15,
                fat: 15,
                carbs: 12
            }
        },
        {
            id: 2,
            name: "Gà Sốt Cay (1 miếng)",
            description: "Gà rán phủ sốt cay đặc biệt, cay nồng hấp dẫn, thịt gà mềm, thơm ngon.",
            price: 45000,
            image: "images/products/ga-sot-cay-1-mieng.jpg",
            category: "Gà Sốt Cay",
            options: [
                { name: "Thịt Đùi", price: 0 },
                { name: "Thịt Ức", price: 0 },
                { name: "Thịt Cánh", price: 5000 }
            ],
            addons: [
                { name: "Khoai Tây Chiên (Vừa)", price: 25000 },
                { name: "Nước Ngọt (Vừa)", price: 15000 },
                { name: "Sốt Mayonnaise", price: 5000 }
            ],
            ingredients: [
                "Thịt gà tươi",
                "Bột chiên xù đặc biệt",
                "Sốt cay Jollibee",
                "Gia vị Jollibee",
                "Dầu thực vật"
            ],
            allergens: ["Gluten", "Đậu nành", "Ớt"],
            nutrition: {
                calories: 280,
                protein: 16,
                fat: 17,
                carbs: 13
            }
        },
        {
            id: 3,
            name: "Burger Gà Giòn",
            description: "Burger với lớp thịt gà giòn, rau tươi và sốt mayonnaise đặc biệt, đậm đà hương vị.",
            price: 50000,
            image: "images/products/burger-ga-gion.jpg",
            category: "Burger & Sandwich",
            options: [
                { name: "Cỡ Thường", price: 0 },
                { name: "Cỡ Lớn (Thêm thịt)", price: 15000 }
            ],
            addons: [
                { name: "Khoai Tây Chiên (Vừa)", price: 25000 },
                { name: "Nước Ngọt (Vừa)", price: 15000 },
                { name: "Thêm Phô Mai", price: 10000 }
            ],
            ingredients: [
                "Bánh mì burger",
                "Thịt gà chiên giòn",
                "Rau xà lách",
                "Cà chua",
                "Sốt mayonnaise đặc biệt"
            ],
            allergens: ["Gluten", "Đậu nành", "Trứng"],
            nutrition: {
                calories: 450,
                protein: 22,
                fat: 25,
                carbs: 35
            }
        },
        {
            id: 4,
            name: "Mỳ Ý Sốt Bò Bằm",
            description: "Mỳ Ý với sốt bò bằm đậm đà, thơm ngon, kết hợp với phô mai và gia vị đặc biệt.",
            price: 45000,
            originalPrice: 55000,
            image: "images/products/my-y-sot-bo-bam.jpg",
            category: "Mỳ Ý & Cơm",
            options: [
                { name: "Cỡ Vừa", price: 0 },
                { name: "Cỡ Lớn", price: 15000 }
            ],
            addons: [
                { name: "Thêm Phô Mai", price: 10000 },
                { name: "Nước Ngọt (Vừa)", price: 15000 },
                { name: "Bánh Mì Nướng Bơ Tỏi", price: 20000 }
            ],
            ingredients: [
                "Mỳ Ý",
                "Thịt bò xay",
                "Sốt cà chua",
                "Phô mai",
                "Gia vị Ý đặc biệt"
            ],
            allergens: ["Gluten", "Sữa", "Đậu nành"],
            nutrition: {
                calories: 520,
                protein: 25,
                fat: 18,
                carbs: 65
            }
        }
    ];
});

// Initialize Product Modal
function initProductModal() {
    const modal = document.getElementById('product-modal');

    if (!modal) return;

    // Close modal when clicking outside
    modal.addEventListener('click', function(event) {
        if (event.target === modal) {
            closeProductModal();
        }
    });

    // Close modal with Escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape' && modal.style.display === 'flex') {
            closeProductModal();
        }
    });

    // Make global function available
    window.openProductModal = openProductModal;
    window.closeProductModal = closeProductModal;
}

// Open Product Modal
function openProductModal(product) {
    const modal = document.getElementById('product-modal');

    if (!modal || !product) return;

    // Create modal content
    const modalContent = createModalContent(product);

    // Clear previous content and add new content
    modal.innerHTML = '';
    modal.appendChild(modalContent);

    // Show modal with animation
    modal.style.display = 'flex';
    setTimeout(() => {
        modal.querySelector('.modal-container').classList.add('fade-in');
    }, 10);

    // Initialize modal functionality
    initModalFunctionality(product);
}

// Close Product Modal
function closeProductModal() {
    const modal = document.getElementById('product-modal');

    if (!modal) return;

    // Add closing animation
    const container = modal.querySelector('.modal-container');
    if (container) {
        container.classList.add('fade-out');

        // Wait for animation to complete
        setTimeout(() => {
            modal.style.display = 'none';
            modal.innerHTML = '';
        }, 300);
    } else {
        modal.style.display = 'none';
        modal.innerHTML = '';
    }
}

// Create Modal Content
function createModalContent(product) {
    const container = document.createElement('div');
    container.className = 'modal-container';

    // Format price
    const formattedPrice = formatPrice(product.price);
    const formattedOriginalPrice = product.originalPrice ? formatPrice(product.originalPrice) : '';

    // Create HTML content
    container.innerHTML = `
        <div class="modal-header">
            <h2 class="modal-title">${product.name}</h2>
            <button class="close-modal" onclick="closeProductModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-content">
            <div class="modal-body">
                <div class="product-details-grid">
                    <div class="product-image">
                        <img src="${product.image}" alt="${product.name}">
                        ${product.originalPrice ? `<div class="product-tag">Giảm giá</div>` : ''}
                    </div>
                    <div class="product-info">
                        <h3>${product.name}</h3>
                        <p>${product.description}</p>
                        <div class="product-price">
                            <span class="current-price">${formattedPrice}</span>
                            ${formattedOriginalPrice ? `<span class="original-price">${formattedOriginalPrice}</span>` : ''}
                        </div>

                        <div class="quantity-selector">
                            <span>Số lượng:</span>
                            <div class="quantity-controls">
                                <button class="decrease-quantity">-</button>
                                <span id="quantity-value">1</span>
                                <button class="increase-quantity">+</button>
                            </div>
                        </div>

                        ${createOptionsHTML(product)}
                        ${createAddonsHTML(product)}

                        <div class="total-price">
                            <span class="label">Tổng cộng:</span>
                            <span class="price" id="total-price">${formattedPrice}</span>
                        </div>

                        <div class="action-buttons">
                            <button class="favorite-btn">
                                <i class="far fa-heart"></i>
                                <span>Yêu thích</span>
                            </button>
                            <button class="add-to-cart-btn">
                                <i class="fas fa-shopping-bag"></i>
                                <span>Thêm vào giỏ hàng</span>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="product-tabs">
                    <div class="tabs-header">
                        <div class="modal-tab active" data-tab="details">Chi tiết sản phẩm</div>
                        <div class="modal-tab" data-tab="ingredients">Thành phần</div>
                        <div class="modal-tab" data-tab="nutrition">Dinh dưỡng</div>
                    </div>
                    <div class="tabs-content">
                        <div class="tab-pane active" id="details-tab">
                            <p>${product.description}</p>
                            <p>Danh mục: ${product.category}</p>
                        </div>
                        <div class="tab-pane" id="ingredients-tab">
                            <h4>Thành phần:</h4>
                            <ul id="ingredients-list">
                                ${product.ingredients.map(ingredient => `<li>${ingredient}</li>`).join('')}
                            </ul>

                            <h4>Chất gây dị ứng:</h4>
                            <div class="allergens-list">
                                ${product.allergens.map(allergen => `<span class="allergen-tag">${allergen}</span>`).join('')}
                            </div>
                        </div>
                        <div class="tab-pane" id="nutrition-tab">
                            <h4>Thông tin dinh dưỡng:</h4>
                            <table class="nutrition-table">
                                <tr>
                                    <th>Thành phần</th>
                                    <th>Giá trị</th>
                                </tr>
                                <tr>
                                    <td>Calories</td>
                                    <td>${product.nutrition.calories} kcal</td>
                                </tr>
                                <tr>
                                    <td>Protein</td>
                                    <td>${product.nutrition.protein}g</td>
                                </tr>
                                <tr>
                                    <td>Chất béo</td>
                                    <td>${product.nutrition.fat}g</td>
                                </tr>
                                <tr>
                                    <td>Carbohydrates</td>
                                    <td>${product.nutrition.carbs}g</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;

    return container;
}

// Create Options HTML
function createOptionsHTML(product) {
    if (!product.options || product.options.length === 0) {
        return '';
    }

    return `
        <div class="options-container">
            <h4>Tùy chọn:</h4>
            <div class="options-list">
                ${product.options.map((option, index) => `
                    <div class="option-item ${index === 0 ? 'selected' : ''}" data-price="${option.price}">
                        <div class="option-input">
                            <input type="radio" name="product-option" id="option-${index}" ${index === 0 ? 'checked' : ''}>
                            <label for="option-${index}">${option.name}</label>
                        </div>
                        <span class="option-price">${option.price > 0 ? '+' + formatPrice(option.price) : ''}</span>
                    </div>
                `).join('')}
            </div>
        </div>
    `;
}

// Create Addons HTML
function createAddonsHTML(product) {
    if (!product.addons || product.addons.length === 0) {
        return '';
    }

    return `
        <div class="addons-container">
            <h4>Thêm món:</h4>
            <div class="addons-list">
                ${product.addons.map((addon, index) => `
                    <div class="addon-item" data-price="${addon.price}">
                        <div class="addon-input">
                            <input type="checkbox" id="addon-${index}" name="product-addon">
                            <label for="addon-${index}">${addon.name}</label>
                        </div>
                        <span class="addon-price">+${formatPrice(addon.price)}</span>
                    </div>
                `).join('')}
            </div>
        </div>
    `;
}

// Initialize Modal Functionality
function initModalFunctionality(product) {
    // Quantity controls
    const decreaseBtn = document.querySelector('.decrease-quantity');
    const increaseBtn = document.querySelector('.increase-quantity');
    const quantityValue = document.getElementById('quantity-value');

    if (decreaseBtn && increaseBtn && quantityValue) {
        let quantity = 1;

        decreaseBtn.addEventListener('click', () => {
            if (quantity > 1) {
                quantity--;
                quantityValue.textContent = quantity;
                updateTotalPrice();
            }
        });

        increaseBtn.addEventListener('click', () => {
            quantity++;
            quantityValue.textContent = quantity;
            updateTotalPrice();
        });
    }

    // Options selection
    const optionItems = document.querySelectorAll('.option-item');
    optionItems.forEach(item => {
        item.addEventListener('click', () => {
            // Update radio button
            const radio = item.querySelector('input[type="radio"]');
            radio.checked = true;

            // Update selected class
            optionItems.forEach(opt => opt.classList.remove('selected'));
            item.classList.add('selected');

            // Update total price
            updateTotalPrice();
        });
    });

    // Addon selection
    const addonItems = document.querySelectorAll('.addon-item');
    addonItems.forEach(item => {
        item.addEventListener('click', () => {
            // Toggle checkbox
            const checkbox = item.querySelector('input[type="checkbox"]');
            checkbox.checked = !checkbox.checked;

            // Toggle selected class
            item.classList.toggle('selected', checkbox.checked);

            // Update total price
            updateTotalPrice();
        });
    });

    // Tab switching
    const tabButtons = document.querySelectorAll('.modal-tab');
    const tabPanes = document.querySelectorAll('.tab-pane');

    tabButtons.forEach(button => {
        button.addEventListener('click', () => {
            const tabId = button.getAttribute('data-tab');

            // Update active tab button
            tabButtons.forEach(btn => btn.classList.remove('active'));
            button.classList.add('active');

            // Update active tab pane
            tabPanes.forEach(pane => pane.classList.remove('active'));
            document.getElementById(`${tabId}-tab`).classList.add('active');
        });
    });

    // Favorite button
    const favoriteBtn = document.querySelector('.favorite-btn');
    if (favoriteBtn) {
        favoriteBtn.addEventListener('click', () => {
            const icon = favoriteBtn.querySelector('i');
            if (icon.classList.contains('far')) {
                icon.classList.remove('far');
                icon.classList.add('fas');
                favoriteBtn.classList.add('active');
            } else {
                icon.classList.remove('fas');
                icon.classList.add('far');
                favoriteBtn.classList.remove('active');
            }
        });
    }

    // Add to cart button
    const addToCartBtn = document.querySelector('.add-to-cart-btn');
    if (addToCartBtn) {
        addToCartBtn.addEventListener('click', () => {
            // Get selected options and addons
            const selectedOption = document.querySelector('.option-item.selected');
            const selectedAddons = document.querySelectorAll('.addon-item.selected');
            const quantity = parseInt(document.getElementById('quantity-value').textContent);

            // Create cart item object
            const cartItem = {
                product: product,
                quantity: quantity,
                option: selectedOption ? {
                    name: selectedOption.querySelector('label').textContent,
                    price: parseInt(selectedOption.getAttribute('data-price'))
                } : null,
                addons: Array.from(selectedAddons).map(addon => ({
                    name: addon.querySelector('label').textContent,
                    price: parseInt(addon.getAttribute('data-price'))
                }))
            };

            // In a real application, you would add this to a cart state
            console.log('Added to cart:', cartItem);

            // Show mini cart notification
            showMiniCartNotification();

            // Close modal
            closeProductModal();
        });
    }

    // Update total price initially
    updateTotalPrice();

    // Function to update total price
    function updateTotalPrice() {
        const basePrice = product.price;
        const quantity = parseInt(document.getElementById('quantity-value').textContent);

        // Get selected option price
        const selectedOption = document.querySelector('.option-item.selected');
        const optionPrice = selectedOption ? parseInt(selectedOption.getAttribute('data-price')) : 0;

        // Get selected addons price
        const selectedAddons = document.querySelectorAll('.addon-item.selected');
        const addonsPrice = Array.from(selectedAddons).reduce((total, addon) => {
            return total + parseInt(addon.getAttribute('data-price'));
        }, 0);

        // Calculate total
        const totalPrice = (basePrice + optionPrice + addonsPrice) * quantity;

        // Update display
        document.getElementById('total-price').textContent = formatPrice(totalPrice);
    }
}

// Format price to Vietnamese currency
function formatPrice(price) {
    return price.toLocaleString('vi-VN') + 'đ';
}

// Show mini cart notification
function showMiniCartNotification() {
    // This function is defined in main.js
    if (window.showMiniCartNotification) {
        window.showMiniCartNotification();
    }
}

// Add CSS for modal animations
const style = document.createElement('style');
style.textContent = `
    .modal-container {
        background-color: white;
        border-radius: 12px;
        width: 100%;
        max-width: 900px;
        max-height: 90vh;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        opacity: 0;
        transform: translateY(20px);
        transition: opacity 0.3s ease, transform 0.3s ease;
    }

    .modal-container.fade-in {
        opacity: 1;
        transform: translateY(0);
    }

    .modal-container.fade-out {
        opacity: 0;
        transform: translateY(20px);
    }

    .favorite-btn.active {
        color: #e31837;
        border-color: #e31837;
        background-color: rgba(227, 24, 55, 0.05);
    }
`;

document.head.appendChild(style);
