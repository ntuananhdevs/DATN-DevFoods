// Slider JavaScript for Jollibee Website

document.addEventListener('DOMContentLoaded', function() {
    // Initialize promotional carousel
    initPromoCarousel();
    
    // Initialize testimonials carousel
    initTestimonialsCarousel();
    
    // Add animation classes to elements when they come into view
    initScrollAnimations();
});

// Promotional Carousel
function initPromoCarousel() {
    const slides = document.querySelectorAll('#promo-slides .carousel-slide');
    const prevBtn = document.getElementById('promo-prev');
    const nextBtn = document.getElementById('promo-next');
    const indicators = document.getElementById('promo-indicators');

    if (slides.length === 0) return;

    let currentIndex = 0;
    let slideInterval;
    const autoPlayDelay = 5000; // 5 seconds

    function goToSlide(index) {
        slides.forEach(slide => slide.classList.remove('active'));
        slides[index].classList.add('active');
        currentIndex = index;

        // Cập nhật indicators nếu có
        if (indicators) {
            const indicatorElements = indicators.querySelectorAll('.carousel-indicator');
            indicatorElements.forEach((indicator, i) => {
                indicator.classList.toggle('active', i === index);
            });
        }
    }

    function startInterval() {
        slideInterval = setInterval(() => {
            goToSlide((currentIndex + 1) % slides.length);
        }, autoPlayDelay);
    }

    function resetInterval() {
        clearInterval(slideInterval);
        startInterval();
    }

    if (prevBtn) {
        prevBtn.addEventListener('click', () => {
            goToSlide((currentIndex - 1 + slides.length) % slides.length);
            resetInterval();
        });
    }

    if (nextBtn) {
        nextBtn.addEventListener('click', () => {
            goToSlide((currentIndex + 1) % slides.length);
            resetInterval();
        });
    }

    startInterval();
}

// Testimonials Carousel
function initTestimonialsCarousel() {
    const container = document.getElementById('testimonials-container');
    const prevBtn = document.getElementById('testimonial-prev');
    const nextBtn = document.getElementById('testimonial-next');
    
    if (!container) return;
    
    const cards = container.querySelectorAll('.testimonial-card');
    if (cards.length === 0) return;
    
    let currentPosition = 0;
    const cardWidth = cards[0].offsetWidth + 20; // Card width + margin
    const visibleCards = Math.floor(container.offsetWidth / cardWidth);
    const maxPosition = Math.max(0, cards.length - visibleCards);
    
    // Add animation classes to testimonial cards
    cards.forEach((card, index) => {
        card.classList.add('fade-in');
        card.style.animationDelay = `${index * 0.1}s`;
    });
    
    // Set up event listeners for controls
    if (prevBtn) {
        prevBtn.addEventListener('click', () => {
            if (currentPosition > 0) {
                currentPosition--;
                updatePosition();
            }
        });
    }
    
    if (nextBtn) {
        nextBtn.addEventListener('click', () => {
            if (currentPosition < maxPosition) {
                currentPosition++;
                updatePosition();
            }
        });
    }
    
    // Update carousel position
    function updatePosition() {
        const translateX = -currentPosition * cardWidth;
        container.style.transform = `translateX(${translateX}px)`;
        
        // Update button states
        if (prevBtn) {
            prevBtn.disabled = currentPosition === 0;
            prevBtn.classList.toggle('disabled', currentPosition === 0);
        }
        
        if (nextBtn) {
            nextBtn.disabled = currentPosition >= maxPosition;
            nextBtn.classList.toggle('disabled', currentPosition >= maxPosition);
        }
    }
    
    // Initialize position
    updatePosition();
    
    // Handle window resize
    window.addEventListener('resize', () => {
        const newVisibleCards = Math.floor(container.offsetWidth / cardWidth);
        const newMaxPosition = Math.max(0, cards.length - newVisibleCards);
        
        // Adjust position if needed
        if (currentPosition > newMaxPosition) {
            currentPosition = newMaxPosition;
        }
        
        updatePosition();
    });
}

// Count Up Animation
function countUp(element, target) {
    let count = 0;
    const duration = 2000; // 2 seconds
    const startTime = performance.now();

    function update(currentTime) {
        const elapsedTime = currentTime - startTime;
        const progress = Math.min(elapsedTime / duration, 1);
        count = Math.floor(progress * target);
        element.textContent = count;

        if (progress < 1) {
            requestAnimationFrame(update);
        }
    }

    requestAnimationFrame(update);
}

// Scroll Animations
function initScrollAnimations() {
    const animatedElements = document.querySelectorAll(
        '.hero-text, .hero-image, .section-title, .section-subtitle, ' +
        '.category-card, .product-card, .combo-card, .menu-category, ' +
        '.delivery-text, .delivery-image, .parallax-content, ' +
        '.kids-club-text, .kids-club-image, .news-card, .app-text, .app-image'
    );

    if (animatedElements.length === 0) return;

    const observer = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const element = entry.target;

                // Apply animation class
                element.classList.add('fade-in');
                observer.unobserve(element); // Stop observing after animation
            }
        });
    }, {
        root: null,
        rootMargin: '0px',
        threshold: 0.1
    });

    animatedElements.forEach(element => observer.observe(element));
}
