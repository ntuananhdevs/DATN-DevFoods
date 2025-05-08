// Main JavaScript for Jollibee Website

document.addEventListener('DOMContentLoaded', function() {
    // Initialize mobile menu
    initMobileMenu();

    // Initialize dropdown menus
    initDropdowns();

    // Initialize stats counter
    initStatsCounter();

    // Initialize sticky header
    initStickyHeader();

    // Initialize product cards
    initProductCards();

    // Initialize order tabs
    initOrderTabs();

    // Initialize testimonials carousel
    initTestimonialsCarousel();
});

// Mobile Menu
function initMobileMenu() {
    const mobileMenuBtn = document.getElementById('mobile-menu-btn');
    const mobileMenu = document.getElementById('mobile-menu');
    const mobileMenuClose = document.getElementById('mobile-menu-close');

    if (mobileMenuBtn && mobileMenu) {
        mobileMenuBtn.addEventListener('click', function() {
            mobileMenu.classList.add('active');
            document.body.style.overflow = 'hidden';
        });

        if (mobileMenuClose) {
            mobileMenuClose.addEventListener('click', function() {
                mobileMenu.classList.remove('active');
                document.body.style.overflow = '';
            });
        }

        // Close mobile menu when clicking outside
        document.addEventListener('click', function(event) {
            if (mobileMenu.classList.contains('active') &&
                !mobileMenu.contains(event.target) &&
                event.target !== mobileMenuBtn) {
                mobileMenu.classList.remove('active');
                document.body.style.overflow = '';
            }
        });
    }
}

// Dropdown Menus
function initDropdowns() {
    const dropdowns = document.querySelectorAll('.dropdown');

    dropdowns.forEach(dropdown => {
        const toggle = dropdown.querySelector('.dropdown-toggle');
        const menu = dropdown.querySelector('.dropdown-menu');

        if (toggle && menu) {
            // For mobile/touch devices
            toggle.addEventListener('click', function(e) {
                e.preventDefault();

                // Close all other dropdowns
                dropdowns.forEach(otherDropdown => {
                    if (otherDropdown !== dropdown) {
                        otherDropdown.querySelector('.dropdown-menu')?.classList.remove('active');
                    }
                });

                // Toggle current dropdown
                menu.classList.toggle('active');
            });
        }
    });

    // Close dropdowns when clicking outside
    document.addEventListener('click', function(event) {
        dropdowns.forEach(dropdown => {
            const toggle = dropdown.querySelector('.dropdown-toggle');
            const menu = dropdown.querySelector('.dropdown-menu');

            if (menu && menu.classList.contains('active') &&
                !dropdown.contains(event.target) &&
                event.target !== toggle) {
                menu.classList.remove('active');
            }
        });
    });
}

// Stats Counter
function initStatsCounter() {
    const statNumbers = document.querySelectorAll('.stat-number');

    if (statNumbers.length > 0) {
        const observer = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const target = entry.target;
                    const countTo = parseInt(target.getAttribute('data-count'), 10);

                    if (!target.classList.contains('counted')) {
                        countUp(target, countTo);
                        target.classList.add('counted');
                    }

                    observer.unobserve(target); // Stop observing after counting
                }
            });
        }, {
            root: null,
            rootMargin: '0px',
            threshold: 0.5
        });

        statNumbers.forEach(stat => observer.observe(stat));
    }
}

function countUp(element, target) {
    let count = 0;
    const speed = 50; // Lower is faster
    const increment = target / (2000 / speed); // Aim to complete in 2 seconds

    const timer = setInterval(() => {
        count += increment;

        if (count >= target) {
            element.textContent = target;
            clearInterval(timer);
        } else {
            element.textContent = Math.floor(count);
        }
    }, speed);
}

// Sticky Header
function initStickyHeader() {
    const mainNav = document.getElementById('main-nav');

    if (mainNav) {
        const mainNavTop = mainNav.offsetTop;

        window.addEventListener('scroll', () => {
            if (window.scrollY > mainNavTop) {
                mainNav.classList.add('sticky');
            } else {
                mainNav.classList.remove('sticky');
            }
        });
    }
}

// Product Cards
function initProductCards() {
    const productCards = document.querySelectorAll('.product-card');

    productCards.forEach(card => {
        // Get product ID
        const productId = card.getAttribute('data-product-id');

        if (productId) {
            // Add click event to open product modal
            card.addEventListener('click', () => {
                // Find the product in the sample data
                const product = window.sampleProducts?.find(p => p.id === parseInt(productId));

                if (product && window.openProductModal) {
                    window.openProductModal(product);
                }
            });

            // Prevent modal opening when clicking add to cart button
            const addToCartBtn = card.querySelector('.add-to-cart-btn');
            if (addToCartBtn) {
                addToCartBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    console.log('Add to cart:', productId);

                    // Show mini cart notification
                    showMiniCartNotification();
                });
            }
        }
    });
}

// Show mini cart notification
function showMiniCartNotification() {
    // Create notification element if it doesn't exist
    let notification = document.querySelector('.cart-notification');

    if (!notification) {
        notification = document.createElement('div');
        notification.className = 'cart-notification';
        notification.innerHTML = `
      <div class="cart-notification-content">
        <i class="fas fa-check-circle"></i>
        <span>Sản phẩm đã được thêm vào giỏ hàng!</span>
      </div>
    `;

        document.body.appendChild(notification);
    }

    // Show notification
    notification.classList.add('active');

    // Hide notification after 3 seconds
    setTimeout(() => {
        notification.classList.remove('active');
    }, 3000);

    // Update cart count
    updateCartCount();
}

// Update cart count
function updateCartCount() {
    const cartCountElements = document.querySelectorAll('.cart-count');

    cartCountElements.forEach(element => {
        // Get current count
        const currentCount = parseInt(element.textContent, 10) || 0;

        // Increment count
        element.textContent = currentCount + 1;

        // Animate count change
        element.classList.add('pulse');
        setTimeout(() => {
            element.classList.remove('pulse');
        }, 300);
    });
}

// Order Tabs
function initOrderTabs() {
    const orderTabs = document.querySelectorAll('.order-tab');
    const orderContents = document.querySelectorAll('.order-tab-content');

    orderTabs.forEach(tab => {
        tab.addEventListener('click', () => {
            const tabId = tab.getAttribute('data-tab');

            // Remove active class from all tabs and contents
            orderTabs.forEach(t => t.classList.remove('active'));
            orderContents.forEach(c => c.classList.remove('active'));

            // Add active class to clicked tab and corresponding content
            tab.classList.add('active');
            document.getElementById(`${tabId}-content`)?.classList.add('active');
        });
    });
}

// Testimonials Carousel
function initTestimonialsCarousel() {
    const container = document.getElementById('testimonials-container');
    const prevBtn = document.getElementById('testimonial-prev');
    const nextBtn = document.getElementById('testimonial-next');
    const cards = container.querySelectorAll('.testimonial-card');

    if (!container || cards.length === 0) return;

    let currentIndex = 0;
    const cardWidth = cards[0].offsetWidth + 20; // Card width + margin
    const visibleCards = Math.floor(container.offsetWidth / cardWidth);
    const maxIndex = Math.max(0, cards.length - visibleCards);

    function updateCarousel() {
        const translateX = -currentIndex * cardWidth;
        container.style.transform = `translateX(${translateX}px)`;
        container.style.transition = 'transform 0.5s ease-in-out';

        // Disable/enable buttons based on position
        if (prevBtn) prevBtn.disabled = currentIndex === 0;
        if (nextBtn) nextBtn.disabled = currentIndex === maxIndex;
    }

    if (prevBtn) {
        prevBtn.addEventListener('click', () => {
            if (currentIndex > 0) {
                currentIndex--;
                updateCarousel();
            }
        });
    }

    if (nextBtn) {
        nextBtn.addEventListener('click', () => {
            if (currentIndex < maxIndex) {
                currentIndex++;
                updateCarousel();
            }
        });
    }

    // Handle window resize
    window.addEventListener('resize', () => {
        const newVisibleCards = Math.floor(container.offsetWidth / cardWidth);
        const newMaxIndex = Math.max(0, cards.length - newVisibleCards);

        if (currentIndex > newMaxIndex) currentIndex = newMaxIndex;
        updateCarousel();
    });

    updateCarousel();
}

// Add CSS for cart notification
const style = document.createElement('style');
style.textContent = `
  .cart-notification {
    position: fixed;
    bottom: 20px;
    right: 20px;
    z-index: 1000;
    transform: translateY(100px);
    opacity: 0;
    transition: all 0.3s ease;
  }

  .cart-notification.active {
    transform: translateY(0);
    opacity: 1;
  }

  .cart-notification-content {
    background-color: #4caf50;
    color: white;
    padding: 15px 20px;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    display: flex;
    align-items: center;
  }

  .cart-notification-content i {
    margin-right: 10px;
    font-size: 20px;
  }

  .cart-count.pulse {
    animation: pulse 0.3s ease;
  }

  @keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.3); }
    100% { transform: scale(1); }
  }

  .sticky {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    z-index: 1000;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    animation: slideDown 0.3s ease;
  }

  @keyframes slideDown {
    from { transform: translateY(-100%); }
    to { transform: translateY(0); }
  }
`;

document.head.appendChild(style);
