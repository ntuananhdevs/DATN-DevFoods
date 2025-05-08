@extends('layouts.customer.fullLayoutMaster')

@section('title', 'Poly Crispy Wings')

@section('content')
<style>
    /* Base Styles */
    :root {
        --jollibee-red: #e31837;
        --jollibee-yellow: #ffc522;
        --jollibee-dark-red: #c41230;
        --jollibee-dark-yellow: #e6b01e;
        --text-dark: #333333;
        --text-light: #ffffff;
        --text-gray: #666666;
        --bg-light: #ffffff;
        --bg-gray: #f5f5f5;
        --border-color: #e0e0e0;
        --shadow-color: rgba(0, 0, 0, 0.1);
        --transition-fast: 0.2s;
        --transition-medium: 0.3s;
        --transition-slow: 0.5s;
        --border-radius-sm: 0.25rem;
        --border-radius-md: 0.5rem;
        --border-radius-lg: 1rem;
        --border-radius-xl: 2rem;
    }

    /* Hero Banner */
    .hero-banner {
        position: relative;
        background-color: var(--jollibee-red);
        overflow: hidden;
        padding: 4rem 0;
    }

    .hero-background {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-image: url('../images/hero-bg.jpg');
        background-size: cover;
        background-position: center;
        opacity: 0.2;
        z-index: 0;
    }

    .hero-content {
        position: relative;
        z-index: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
    }

    .hero-text {
        margin-bottom: 2.5rem;
    }

    .hero-title {
        font-size: 2.5rem;
        font-weight: 800;
        color: var(--text-light);
        margin-bottom: 1rem;
        line-height: 1.2;
    }

    .hero-title span {
        color: var(--jollibee-yellow);
    }

    .hero-description {
        color: var(--text-light);
        font-size: 1.125rem;
        max-width: 32rem;
        margin: 0 auto 2rem;
    }

    .hero-buttons {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 1rem;
    }

    .hero-image {
        position: relative;
        width: 100%;
        max-width: 400px;
    }

    .hero-image img {
        display: block;
        margin: 0 auto;
    }

    .floating-image {
        position: absolute;
        animation: float 5s ease-in-out infinite;
    }

    .floating-image-1 {
        top: -2.5rem;
        left: -2.5rem;
        opacity: 0.7;
        animation-delay: 0.3s;
    }

    .floating-image-2 {
        bottom: -1.25rem;
        right: -1.25rem;
        opacity: 0.7;
        animation-delay: 0.4s;
    }

    .hero-gradient {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 4rem;
        background: linear-gradient(to top, var(--bg-light), transparent);
    }

    /* Stats Section */
    .stats-section {
        padding: 3rem 0;
        background-color: var(--bg-light);
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1.5rem;
    }

    .stat-card {
        background-color: var(--bg-gray);
        border-radius: var(--border-radius-md);
        padding: 1.5rem;
        text-align: center;
        box-shadow: 0 4px 6px var(--shadow-color);
    }

    .stat-number {
        font-size: 2rem;
        font-weight: 700;
        color: var(--jollibee-red);
        margin-bottom: 0.5rem;
    }

    .stat-label {
        color: var(--text-gray);
    }

    /* Promo Carousel */
    .promo-carousel {
        position: relative;
    }

    .carousel-container {
        position: relative;
        overflow: hidden;
    }

    .carousel-slides {
        position: relative;
        height: 300px;
    }

    .carousel-slide {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        opacity: 0;
        transition: opacity var(--transition-slow) ease;
    }

    .carousel-slide.active {
        opacity: 1;
    }

    .carousel-slide img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .carousel-caption {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        padding: 1.5rem;
        background: linear-gradient(to top, rgba(0, 0, 0, 0.6), transparent);
        color: var(--text-light);
    }

    .carousel-caption h2 {
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }

    .carousel-control {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        background-color: rgba(0, 0, 0, 0.3);
        color: var(--text-light);
        width: 2.5rem;
        height: 2.5rem;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        border: none;
        cursor: pointer;
        transition: all var(--transition-fast) ease;
        z-index: 10;
    }

    .carousel-control:hover {
        background-color: rgba(0, 0, 0, 0.5);
    }

    .carousel-control.prev {
        left: 1rem;
    }

    .carousel-control.next {
        right: 1rem;
    }

    .carousel-indicators {
        position: absolute;
        bottom: 1rem;
        left: 50%;
        transform: translateX(-50%);
        display: flex;
        gap: 0.5rem;
        z-index: 10;
    }

    .indicator {
        width: 0.75rem;
        height: 0.75rem;
        border-radius: 50%;
        background-color: rgba(255, 255, 255, 0.5);
        border: none;
        cursor: pointer;
        transition: all var(--transition-fast) ease;
    }

    .indicator.active {
        background-color: var(--jollibee-yellow);
    }

    /* Category Showcase */
    .category-showcase {
        padding: 4rem 0;
        background-color: var(--bg-light);
    }

    .category-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1.5rem;
    }

    .category-card {
        position: relative;
        overflow: hidden;
        border-radius: var(--border-radius-lg);
        box-shadow: 0 4px 10px var(--shadow-color);
        transition: all var(--transition-medium) ease;
    }

    .category-card:hover {
        transform: translateY(-0.625rem);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }

    .category-image {
        position: relative;
        aspect-ratio: 1 / 1;
    }

    .category-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform var(--transition-slow) ease;
    }

    .category-card:hover .category-image img {
        transform: scale(1.1);
    }

    .category-overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(to top, rgba(0, 0, 0, 0.7), transparent);
    }

    .category-info {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        padding: 1rem;
        color: var(--text-light);
    }

    .category-info h3 {
        font-size: 1.25rem;
        font-weight: 700;
        margin-bottom: 0.25rem;
    }

    .category-info p {
        font-size: 0.875rem;
        color: var(--jollibee-yellow);
    }

    /* Featured Products */
    .featured-products {
        padding: 4rem 0;
        background-color: var(--bg-gray);
    }

    .products-grid {
        display: grid;
        grid-template-columns: repeat(1, 1fr);
        gap: 1.5rem;
        margin-bottom: 3rem;
    }

    .product-card {
        background-color: var(--bg-light);
        border-radius: var(--border-radius-lg);
        overflow: hidden;
        box-shadow: 0 4px 6px var(--shadow-color);
        transition: all var(--transition-medium) ease;
    }

    .product-card:hover {
        transform: translateY(-0.625rem);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }

    .product-image {
        position: relative;
        aspect-ratio: 1 / 1;
    }

    .product-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .product-overlay {
        position: absolute;
        inset: 0;
        background-color: rgba(0, 0, 0, 0.4);
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity var(--transition-medium) ease;
    }

    .product-card:hover .product-overlay {
        opacity: 1;
    }

    .product-actions {
        display: flex;
        gap: 0.75rem;
    }

    .action-btn {
        width: 2.5rem;
        height: 2.5rem;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: var(--bg-light);
        color: var(--text-dark);
        border: none;
        cursor: pointer;
        transition: all var(--transition-fast) ease;
        box-shadow: 0 2px 5px var(--shadow-color);
    }

    .action-btn:hover {
        transform: scale(1.1);
    }

    .action-btn.cart-btn {
        background-color: var(--jollibee-red);
        color: var(--text-light);
    }

    .discount-badge {
        position: absolute;
        top: 0.75rem;
        right: 0.75rem;
        background-color: var(--jollibee-yellow);
        color: var(--jollibee-red);
        font-weight: 700;
        font-size: 0.75rem;
        padding: 0.25rem 0.75rem;
        border-radius: var(--border-radius-xl);
        z-index: 1;
    }

    .product-info {
        padding: 1rem;
    }

    .product-title {
        font-size: 1.125rem;
        font-weight: 700;
        color: var(--jollibee-red);
        margin-bottom: 0.25rem;
    }

    .product-description {
        font-size: 0.875rem;
        color: var(--text-gray);
        margin-bottom: 0.75rem;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .product-price-actions {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .product-price {
        display: flex;
        align-items: flex-end;
        gap: 0.5rem;
    }

    .current-price {
        font-size: 1.125rem;
        font-weight: 700;
    }

    .original-price {
        font-size: 0.875rem;
        color: var(--text-gray);
        text-decoration: line-through;
    }

    .add-to-cart-btn {
        width: 2.5rem;
        height: 2.5rem;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: var(--jollibee-red);
        color: var(--text-light);
        border: none;
        cursor: pointer;
        transition: all var(--transition-fast) ease;
    }

    .add-to-cart-btn:hover {
        background-color: var(--jollibee-dark-red);
    }

    .view-all-container {
        text-align: center;
    }

    /* Delivery Banner */
    .delivery-banner {
        padding: 3rem 0;
        background: linear-gradient(to right, var(--jollibee-red), var(--jollibee-dark-red));
        position: relative;
        overflow: hidden;
    }

    .banner-background {
        position: absolute;
        inset: 0;
        background-image: url('../images/pattern.jpg');
        background-size: cover;
        opacity: 0.1;
    }

    .banner-content {
        position: relative;
        z-index: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
    }

    .banner-text {
        margin-bottom: 2rem;
    }

    .banner-text h2 {
        font-size: 2rem;
        font-weight: 700;
        color: var(--text-light);
        margin-bottom: 1rem;
    }

    .banner-text p {
        color: var(--text-light);
        font-size: 1.125rem;
        max-width: 32rem;
        margin: 0 auto 1.5rem;
    }

    .banner-image {
        position: relative;
        width: 100%;
        max-width: 320px;
    }

    .banner-image img {
        display: block;
        margin: 0 auto;
    }

    /* Parallax Section */
    .parallax-section {
        position: relative;
        height: 400px;
        display: flex;
        align-items: center;
        overflow: hidden;
    }

    .parallax-background {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-image: url('../images/parallax-bg.jpg');
        background-size: cover;
        background-position: center;
        background-attachment: fixed;
        filter: brightness(0.5);
    }

    .parallax-content {
        position: relative;
        z-index: 1;
        text-align: center;
        color: var(--text-light);
    }

    .parallax-content h2 {
        font-size: 3rem;
        font-weight: 700;
        margin-bottom: 1.5rem;
    }

    .parallax-content p {
        font-size: 1.25rem;
        max-width: 36rem;
        margin: 0 auto 2rem;
    }

    /* Kids Club Banner */
    .kids-club-banner {
        padding: 4rem 0;
        background-color: var(--jollibee-yellow);
        position: relative;
        overflow: hidden;
    }

    .kids-club-banner .banner-content {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
    }

    .kids-club-banner .banner-text {
        margin-bottom: 2rem;
    }

    .kids-club-banner .banner-text h2 {
        font-size: 2rem;
        font-weight: 700;
        color: var(--jollibee-red);
        margin-bottom: 1rem;
    }

    .kids-club-banner .banner-text p {
        color: var(--text-dark);
        font-size: 1.125rem;
        max-width: 32rem;
        margin: 0 auto 1.5rem;
    }

    .kids-club-banner .banner-buttons {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 1rem;
    }

    .kids-club-banner .btn-outline {
        border-color: var(--jollibee-red);
        color: var(--jollibee-red);
    }

    .kids-club-banner .btn-outline:hover {
        background-color: var(--jollibee-red);
        color: var(--text-light);
    }

    .kids-club-banner .banner-image {
        position: relative;
        width: 100%;
        max-width: 400px;
    }

    .rotating-image {
        position: absolute;
        top: -2.5rem;
        right: -2.5rem;
        width: 6rem;
        height: 6rem;
        animation: rotate 20s linear infinite;
    }

    .floating-decorations {
        position: absolute;
        inset: 0;
        pointer-events: none;
    }

    /* Services Section */
    .services-section {
        padding: 4rem 0;
        background-color: var(--bg-light);
        background-image: url('../images/services-bg.jpg');
        background-size: cover;
        background-blend-mode: lighten;
        background-position: center;
    }

    .services-grid {
        display: grid;
        grid-template-columns: repeat(1, 1fr);
        gap: 2rem;
    }

    .service-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        transition: transform var(--transition-medium) ease;
    }

    .service-item:hover {
        transform: translateY(-0.625rem);
    }

    .service-image {
        width: 12.5rem;
        height: 12.5rem;
        border-radius: 50%;
        overflow: hidden;
        margin-bottom: 1rem;
        transition: transform var(--transition-medium) ease;
    }

    .service-item:hover .service-image {
        transform: scale(1.05);
    }

    .service-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .service-item h3 {
        font-size: 1.25rem;
        font-weight: 700;
    }

    /* Product Modal */
    .product-modal {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1000;
        padding: 1rem;
        opacity: 0;
        visibility: hidden;
        transition: all var(--transition-medium) ease;
    }

    .product-modal.active {
        opacity: 1;
        visibility: visible;
    }

    .modal-content {
        background-color: var(--bg-light);
        border-radius: var(--border-radius-lg);
        width: 100%;
        max-width: 64rem;
        max-height: 90vh;
        overflow-y: auto;
        position: relative;
    }

    .modal-close {
        position: absolute;
        top: 1rem;
        right: 1rem;
        background: none;
        border: none;
        font-size: 1.5rem;
        color: var(--text-gray);
        cursor: pointer;
        z-index: 10;
    }

    .modal-body {
        padding: 1rem;
    }

    .product-detail {
        display: flex;
        flex-direction: column;
        gap: 2rem;
        margin-bottom: 2rem;
    }

    .product-detail .product-image {
        border-radius: var(--border-radius-md);
        overflow: hidden;
    }

    .quantity-selector {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
        padding: 1.5rem 0;
        border-top: 1px solid var(--border-color);
        border-bottom: 1px solid var(--border-color);
    }

    .quantity-controls {
        display: flex;
        align-items: center;
    }

    .quantity-btn {
        width: 2rem;
        height: 2rem;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: var(--bg-gray);
        border: none;
        cursor: pointer;
    }

    .quantity-btn.decrease {
        border-radius: var(--border-radius-md) 0 0 var(--border-radius-md);
    }

    .quantity-btn.increase {
        border-radius: 0 var(--border-radius-md) var(--border-radius-md) 0;
    }

    .quantity-value {
        width: 3rem;
        height: 2rem;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: var(--bg-light);
        border-top: 1px solid var(--border-color);
        border-bottom: 1px solid var(--border-color);
    }

    .product-options,
    .product-addons {
        margin-bottom: 1.5rem;
    }

    .product-options h3,
    .product-addons h3 {
        font-size: 1.125rem;
        font-weight: 700;
        margin-bottom: 1rem;
    }

    .options-list,
    .addons-list {
        display: grid;
        grid-template-columns: 1fr;
        gap: 0.75rem;
    }

    .option-item,
    .addon-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.75rem;
        border: 1px solid var(--border-color);
        border-radius: var(--border-radius-md);
        cursor: pointer;
        transition: all var(--transition-fast) ease;
    }

    .option-item.active,
    .addon-item.active {
        border-color: var(--jollibee-red);
        background-color: rgba(227, 24, 55, 0.05);
    }

    .option-item:hover,
    .addon-item:hover {
        border-color: var(--jollibee-red);
    }

    .option-price,
    .addon-price {
        color: var(--jollibee-red);
        font-weight: 600;
    }

    .product-total {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
        font-weight: 700;
        font-size: 1.25rem;
    }

    .total-price {
        color: var(--jollibee-red);
    }

    .product-actions {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
    }

    .product-tabs {
        margin-top: 2rem;
    }

    .tabs-header {
        display: flex;
        border-bottom: 1px solid var(--border-color);
        margin-bottom: 1.5rem;
    }

    .tab-btn {
        padding: 0.75rem 1rem;
        font-weight: 700;
        background: none;
        border: none;
        border-bottom: 2px solid transparent;
        cursor: pointer;
        color: var(--text-gray);
        transition: all var(--transition-fast) ease;
    }

    .tab-btn.active {
        color: var(--jollibee-red);
        border-bottom-color: var(--jollibee-red);
    }

    .tab-panel {
        display: none;
    }

    .tab-panel.active {
        display: block;
    }

    .ingredients-list {
        list-style-type: disc;
        padding-left: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .ingredients-list li {
        margin-bottom: 0.5rem;
        color: var(--text-gray);
    }

    .allergens {
        margin-bottom: 1.5rem;
    }

    .allergens-list {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
    }

    .allergen-tag {
        background-color: #fff3cd;
        color: #856404;
        font-size: 0.875rem;
        font-weight: 500;
        padding: 0.25rem 0.625rem;
        border-radius: var(--border-radius-md);
    }

    .nutrition-table {
        width: 100%;
        border-collapse: collapse;
        border-radius: var(--border-radius-md);
        overflow: hidden;
    }

    .nutrition-table th,
    .nutrition-table td {
        padding: 0.75rem 1rem;
        text-align: left;
    }

    .nutrition-table th {
        background-color: var(--bg-gray);
        font-weight: 700;
    }

    .nutrition-table td {
        border-top: 1px solid var(--border-color);
    }

    .nutrition-table tr:nth-child(even) {
        background-color: var(--bg-gray);
    }

    /* Animations */
    @keyframes float {

        0%,
        100% {
            transform: translateY(0);
        }

        50% {
            transform: translateY(-15px);
        }
    }

    @keyframes rotate {
        from {
            transform: rotate(0deg);
        }

        to {
            transform: rotate(360deg);
        }
    }

    /* Media Queries */
    @media (min-width: 576px) {
        .products-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .services-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (min-width: 768px) {
        .top-nav-links {
            display: flex;
        }

        .location-btn,
        .account-btn {
            display: flex;
        }

        .search-btn,
        .cart-btn {
            display: flex;
        }

        .hero-content {
            flex-direction: row;
            text-align: left;
            justify-content: space-between;
        }

        .hero-text {
            width: 50%;
            margin-bottom: 0;
        }

        .hero-buttons {
            justify-content: flex-start;
        }

        .hero-image {
            width: 50%;
        }

        .hero-title {
            font-size: 3.5rem;
        }

        .stats-grid {
            grid-template-columns: repeat(4, 1fr);
        }

        .carousel-slides {
            height: 400px;
        }

        .banner-content {
            flex-direction: row;
            text-align: left;
            justify-content: space-between;
        }

        .banner-text {
            width: 50%;
            margin-bottom: 0;
        }

        .banner-image {
            width: 50%;
        }

        .product-detail {
            flex-direction: row;
        }

        .product-detail .product-image {
            width: 50%;
        }

        .product-detail .product-info {
            width: 50%;
        }

        .options-list,
        .addons-list {
            grid-template-columns: repeat(2, 1fr);
        }

        .footer-content {
            grid-template-columns: repeat(2, 1fr);
        }

        .footer-bottom {
            flex-direction: row;
            justify-content: space-between;
        }

        .copyright {
            margin-bottom: 0;
        }
    }

    @media (min-width: 992px) {
        .desktop-menu {
            display: block;
        }

        .mobile-menu-btn {
            display: none;
        }

        .hotline {
            display: flex;
        }

        .category-grid {
            grid-template-columns: repeat(4, 1fr);
        }

        .products-grid {
            grid-template-columns: repeat(4, 1fr);
        }

        .services-grid {
            grid-template-columns: repeat(4, 1fr);
        }

        .footer-content {
            grid-template-columns: repeat(4, 1fr);
        }
    }
</style>

<body>

    <main>
        <!-- Hero Banner -->
        <section class="hero-banner">
            <div class="hero-background"></div>
            <div class="container">
                <div class="hero-content">
                    <div class="hero-text">
                        <h1 class="hero-title">Thưởng Thức <br><span>Hương Vị Đặc Trưng</span></h1>
                        <p class="hero-description">Khám phá thực đơn đa dạng và phong phú của Jollibee, với nhiều lựa
                            chọn cho bạn, gia đình và bạn bè.</p>
                        <div class="hero-buttons">
                            <button class="btn btn-primary">ĐẶT HÀNG NGAY</button>
                            <button class="btn btn-outline">XEM THỰC ĐƠN</button>
                        </div>
                    </div>
                    <div class="hero-image">
                        <img src="images/hero-product.png" alt="Jollibee Featured Product">
                        <div class="floating-image floating-image-1">
                            <img src="images/decoration-1.png" alt="Decoration">
                        </div>
                        <div class="floating-image floating-image-2">
                            <img src="images/decoration-2.png" alt="Decoration">
                        </div>
                    </div>
                </div>
            </div>
            <div class="hero-gradient"></div>
        </section>

        <!-- Stats Section -->
        <section class="stats-section">
            <div class="container">
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-number" data-count="150">0</div>
                        <p class="stat-label">Cửa hàng</p>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number" data-count="1000">0</div>
                        <p class="stat-label">Khách hàng (K+)</p>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number" data-count="100">0</div>
                        <p class="stat-label">Món ăn</p>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number" data-count="25">0</div>
                        <p class="stat-label">Năm kinh nghiệm</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Promotional Carousel -->
        <section class="promo-carousel">
            <div class="carousel-container">
                <div class="carousel-slides">
                    <div class="carousel-slide active">
                        <img src="images/promo-1.jpg" alt="Combo Gia Đình Vui Vẻ">
                        <div class="carousel-caption">
                            <h2>Combo Gia Đình Vui Vẻ</h2>
                            <p>Tiết kiệm đến 15% với combo dành cho gia đình</p>
                        </div>
                    </div>
                    <div class="carousel-slide">
                        <img src="images/promo-2.jpg" alt="Mua 1 Tặng 1">
                        <div class="carousel-caption">
                            <h2>Mua 1 Tặng 1</h2>
                            <p>Thứ 2 hàng tuần - Mua 1 gà giòn tặng 1 mỳ Ý</p>
                        </div>
                    </div>
                    <div class="carousel-slide">
                        <img src="images/promo-3.jpg" alt="Sinh Nhật Vui Vẻ">
                        <div class="carousel-caption">
                            <h2>Sinh Nhật Vui Vẻ</h2>
                            <p>Đặt tiệc sinh nhật tại Jollibee - Nhận quà hấp dẫn</p>
                        </div>
                    </div>
                </div>
                <button class="carousel-control prev">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <button class="carousel-control next">
                    <i class="fas fa-chevron-right"></i>
                </button>
                <div class="carousel-indicators">
                    <button class="indicator active" data-slide="0"></button>
                    <button class="indicator" data-slide="1"></button>
                    <button class="indicator" data-slide="2"></button>
                </div>
            </div>
        </section>

        <!-- Category Showcase -->
        <section class="category-showcase">
            <div class="container">
                <h2 class="section-title">DANH MỤC MÓN ĂN</h2>
                <p class="section-subtitle">KHÁM PHÁ CÁC DANH MỤC MÓN ĂN PHONG PHÚ CỦA JOLLIBEE</p>

                <div class="category-grid">
                    <div class="category-card">
                        <a href="#">
                            <div class="category-image">
                                <img src="images/category-1.jpg" alt="Gà Giòn Vui Vẻ">
                                <div class="category-overlay"></div>
                                <div class="category-info">
                                    <h3>Gà Giòn Vui Vẻ</h3>
                                    <p>8 món</p>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="category-card">
                        <a href="#">
                            <div class="category-image">
                                <img src="images/category-2.jpg" alt="Gà Sốt Cay">
                                <div class="category-overlay"></div>
                                <div class="category-info">
                                    <h3>Gà Sốt Cay</h3>
                                    <p>6 món</p>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="category-card">
                        <a href="#">
                            <div class="category-image">
                                <img src="images/category-3.jpg" alt="Burger & Sandwich">
                                <div class="category-overlay"></div>
                                <div class="category-info">
                                    <h3>Burger & Sandwich</h3>
                                    <p>10 món</p>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="category-card">
                        <a href="#">
                            <div class="category-image">
                                <img src="images/category-4.jpg" alt="Mỳ Ý & Cơm">
                                <div class="category-overlay"></div>
                                <div class="category-info">
                                    <h3>Mỳ Ý & Cơm</h3>
                                    <p>12 món</p>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Featured Products -->
        <section class="featured-products">
            <div class="container">
                <h2 class="section-title">SẢN PHẨM NỔI BẬT</h2>
                <p class="section-subtitle">KHÁM PHÁ CÁC MÓN ĂN ĐƯỢC YÊU THÍCH NHẤT TẠI JOLLIBEE</p>

                <div class="products-grid">
                    <div class="product-card" data-product-id="1">
                        <div class="product-image">
                            <img src="images/product-1.jpg" alt="Gà Giòn Vui Vẻ (1 miếng)">
                            <div class="product-overlay">
                                <div class="product-actions">
                                    <button class="action-btn favorite-btn">
                                        <i class="fas fa-heart"></i>
                                    </button>
                                    <button class="action-btn cart-btn">
                                        <i class="fas fa-shopping-bag"></i>
                                    </button>
                                    <button class="action-btn info-btn">
                                        <i class="fas fa-info"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="product-info">
                            <h3 class="product-title">Gà Giòn Vui Vẻ (1 miếng)</h3>
                            <p class="product-description">Gà rán giòn thơm ngon, hương vị đặc trưng của Jollibee với
                                lớp bột chiên giòn rụm và thịt gà mềm, thơm ngon.</p>
                            <div class="product-price-actions">
                                <div class="product-price">
                                    <span class="current-price">40.000đ</span>
                                </div>
                                <button class="add-to-cart-btn">
                                    <i class="fas fa-shopping-bag"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="product-card" data-product-id="2">
                        <div class="product-image">
                            <img src="images/product-2.jpg" alt="Gà Sốt Cay (1 miếng)">
                            <div class="product-overlay">
                                <div class="product-actions">
                                    <button class="action-btn favorite-btn">
                                        <i class="fas fa-heart"></i>
                                    </button>
                                    <button class="action-btn cart-btn">
                                        <i class="fas fa-shopping-bag"></i>
                                    </button>
                                    <button class="action-btn info-btn">
                                        <i class="fas fa-info"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="product-info">
                            <h3 class="product-title">Gà Sốt Cay (1 miếng)</h3>
                            <p class="product-description">Gà rán phủ sốt cay đặc biệt, cay nồng hấp dẫn, thịt gà mềm,
                                thơm ngon.</p>
                            <div class="product-price-actions">
                                <div class="product-price">
                                    <span class="current-price">45.000đ</span>
                                </div>
                                <button class="add-to-cart-btn">
                                    <i class="fas fa-shopping-bag"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="product-card" data-product-id="3">
                        <div class="product-image">
                            <img src="images/product-3.jpg" alt="Burger Gà Giòn">
                            <div class="product-overlay">
                                <div class="product-actions">
                                    <button class="action-btn favorite-btn">
                                        <i class="fas fa-heart"></i>
                                    </button>
                                    <button class="action-btn cart-btn">
                                        <i class="fas fa-shopping-bag"></i>
                                    </button>
                                    <button class="action-btn info-btn">
                                        <i class="fas fa-info"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="product-info">
                            <h3 class="product-title">Burger Gà Giòn</h3>
                            <p class="product-description">Burger với lớp thịt gà giòn, rau tươi và sốt mayonnaise đặc
                                biệt, đậm đà hương vị.</p>
                            <div class="product-price-actions">
                                <div class="product-price">
                                    <span class="current-price">50.000đ</span>
                                </div>
                                <button class="add-to-cart-btn">
                                    <i class="fas fa-shopping-bag"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="product-card" data-product-id="4">
                        <div class="product-image">
                            <img src="images/product-4.jpg" alt="Mỳ Ý Sốt Bò Bằm">
                            <div class="product-overlay">
                                <div class="product-actions">
                                    <button class="action-btn favorite-btn">
                                        <i class="fas fa-heart"></i>
                                    </button>
                                    <button class="action-btn cart-btn">
                                        <i class="fas fa-shopping-bag"></i>
                                    </button>
                                    <button class="action-btn info-btn">
                                        <i class="fas fa-info"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="discount-badge">Giảm giá</div>
                        </div>
                        <div class="product-info">
                            <h3 class="product-title">Mỳ Ý Sốt Bò Bằm</h3>
                            <p class="product-description">Mỳ Ý với sốt bò bằm đậm đà, thơm ngon, kết hợp với phô mai và
                                gia vị đặc biệt.</p>
                            <div class="product-price-actions">
                                <div class="product-price">
                                    <span class="current-price">45.000đ</span>
                                    <span class="original-price">55.000đ</span>
                                </div>
                                <button class="add-to-cart-btn">
                                    <i class="fas fa-shopping-bag"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="view-all-container">
                    <a href="menu.html" class="btn btn-primary">XEM TẤT CẢ SẢN PHẨM</a>
                </div>
            </div>
        </section>

        <!-- Delivery Banner -->
        <section class="delivery-banner">
            <div class="banner-background"></div>
            <div class="container">
                <div class="banner-content">
                    <div class="banner-text">
                        <h2>GIAO HÀNG TẬN NƠI</h2>
                        <p>Đặt hàng Jollibee trực tuyến và nhận giao hàng tận nơi nhanh chóng. Thưởng thức món ăn yêu
                            thích của bạn mà không cần rời khỏi nhà!</p>
                        <button class="btn btn-primary">ĐẶT HÀNG NGAY</button>
                    </div>
                    <div class="banner-image">
                        <img src="images/delivery.png" alt="Jollibee Delivery">
                        <div class="floating-image">
                            <img src="images/decoration-3.png" alt="Decoration">
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Menu Section -->
        <section class="menu-section">
            <!-- Menu content will be here -->
        </section>

        <!-- Combo Section -->
        <section class="combo-section">
            <!-- Combo content will be here -->
        </section>

        <!-- Parallax Section -->
        <section class="parallax-section">
            <div class="parallax-background"></div>
            <div class="container">
                <div class="parallax-content">
                    <h2>JOLLIBEE VIETNAM</h2>
                    <p>Thưởng thức hương vị đặc trưng của Jollibee - Nơi mang đến niềm vui và những khoảnh khắc ấm áp
                        cho mọi gia đình Việt Nam.</p>
                    <button class="btn btn-primary">ĐẶT HÀNG NGAY</button>
                </div>
            </div>
        </section>

        <!-- Delivery Section -->
        <section class="delivery-section">
            <!-- Delivery content will be here -->
        </section>

        <!-- Kids Club Banner -->
        <section class="kids-club-banner">
            <div class="container">
                <div class="banner-content">
                    <div class="banner-text">
                        <h2>JOLLIBEE KIDS CLUB</h2>
                        <p>Tham gia Jollibee Kids Club ngay hôm nay để nhận được nhiều ưu đãi đặc biệt, quà tặng sinh
                            nhật và các hoạt động thú vị dành riêng cho các bé!</p>
                        <div class="banner-buttons">
                            <button class="btn btn-primary">ĐĂNG KÝ NGAY</button>
                            <button class="btn btn-outline">TÌM HIỂU THÊM</button>
                        </div>
                    </div>
                    <div class="banner-image">
                        <img src="images/kids-club.png" alt="Jollibee Kids Club">
                        <div class="rotating-image">
                            <img src="images/star.png" alt="Jollibee Star">
                        </div>
                    </div>
                </div>
                <div class="floating-decorations">
                    <div class="floating-image floating-image-1">
                        <img src="images/decoration-4.png" alt="Decoration">
                    </div>
                    <div class="floating-image floating-image-2">
                        <img src="images/decoration-5.png" alt="Decoration">
                    </div>
                    <div class="floating-image floating-image-3">
                        <img src="images/decoration-6.png" alt="Decoration">
                    </div>
                </div>
            </div>
        </section>

        <!-- Testimonials Section -->
        <section class="testimonials-section">
            <!-- Testimonials content will be here -->
        </section>

        <!-- News Section -->
        <section class="news-section">
            <!-- News content will be here -->
        </section>

        <!-- Services Section -->
        <section class="services-section">
            <div class="container">
                <h2 class="section-title">DỊCH VỤ</h2>
                <p class="section-subtitle">TẬN HƯỞNG NHỮNG KHOẢNH KHẮC TRỌN VẸN CÙNG JOLLIBEE</p>

                <div class="services-grid">
                    <div class="service-item">
                        <div class="service-image">
                            <img src="images/service-1.jpg" alt="Đặt Hàng Online">
                        </div>
                        <h3>Đặt Hàng Online</h3>
                    </div>
                    <div class="service-item">
                        <div class="service-image">
                            <img src="images/service-2.jpg" alt="Tiệc Sinh Nhật">
                        </div>
                        <h3>Tiệc Sinh Nhật</h3>
                    </div>
                    <div class="service-item">
                        <div class="service-image">
                            <img src="images/service-3.jpg" alt="Jollibee Kids Club">
                        </div>
                        <h3>Jollibee Kids Club</h3>
                    </div>
                    <div class="service-item">
                        <div class="service-image">
                            <img src="images/service-4.jpg" alt="Đơn Hàng Lớn">
                        </div>
                        <h3>Đơn Hàng Lớn</h3>
                    </div>
                </div>
            </div>
        </section>

        <!-- Store Locator -->
        <section class="store-locator">
            <!-- Store locator content will be here -->
        </section>

        <!-- App Download Section -->
        <section class="app-download">
            <!-- App download content will be here -->
        </section>
    </main>

    <!-- Product Detail Modal -->
    <div class="product-modal" id="productModal">
        <div class="modal-content">
            <button class="modal-close">
                <i class="fas fa-times"></i>
            </button>
            <div class="modal-body">
                <div class="product-detail">
                    <div class="product-image">
                        <img src="images/product-1.jpg" alt="Product Image" id="modalProductImage">
                    </div>
                    <div class="product-info">
                        <h2 id="modalProductTitle">Gà Giòn Vui Vẻ (1 miếng)</h2>
                        <div class="product-price">
                            <span class="current-price" id="modalProductPrice">40.000đ</span>
                            <span class="original-price" id="modalProductOriginalPrice"></span>
                        </div>
                        <p class="product-description" id="modalProductDescription">Gà rán giòn thơm ngon, hương vị đặc
                            trưng của Jollibee với lớp bột chiên giòn rụm và thịt gà mềm, thơm ngon.</p>

                        <div class="quantity-selector">
                            <span>Số lượng</span>
                            <div class="quantity-controls">
                                <button class="quantity-btn decrease">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <span class="quantity-value">1</span>
                                <button class="quantity-btn increase">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>

                        <div class="product-options">
                            <h3>Tùy chọn</h3>
                            <div class="options-list" id="modalProductOptions">
                                <!-- Options will be dynamically added here -->
                            </div>
                        </div>

                        <div class="product-addons">
                            <h3>Thêm món</h3>
                            <div class="addons-list" id="modalProductAddons">
                                <!-- Addons will be dynamically added here -->
                            </div>
                        </div>

                        <div class="product-total">
                            <span>Tổng cộng:</span>
                            <span class="total-price" id="modalTotalPrice">40.000đ</span>
                        </div>

                        <div class="product-actions">
                            <button class="btn btn-outline">
                                <i class="fas fa-heart"></i>
                                <span>Yêu thích</span>
                            </button>
                            <button class="btn btn-primary">
                                <i class="fas fa-shopping-bag"></i>
                                <span>Thêm vào giỏ</span>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="product-tabs">
                    <div class="tabs-header">
                        <button class="tab-btn active" data-tab="details">Chi tiết sản phẩm</button>
                        <button class="tab-btn" data-tab="nutrition">Thông tin dinh dưỡng</button>
                    </div>
                    <div class="tabs-content">
                        <div class="tab-panel active" id="details">
                            <h3>Thành phần:</h3>
                            <ul class="ingredients-list" id="modalIngredients">
                                <!-- Ingredients will be dynamically added here -->
                            </ul>

                            <div class="allergens" id="modalAllergensContainer">
                                <h3>Chứa dị ứng:</h3>
                                <div class="allergens-list" id="modalAllergens">
                                    <!-- Allergens will be dynamically added here -->
                                </div>
                            </div>
                        </div>
                        <div class="tab-panel" id="nutrition">
                            <table class="nutrition-table">
                                <thead>
                                    <tr>
                                        <th>Thông tin dinh dưỡng</th>
                                        <th>Giá trị</th>
                                    </tr>
                                </thead>
                                <tbody id="modalNutrition">
                                    <!-- Nutrition info will be dynamically added here -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            // Product Modal
            const productCards = document.querySelectorAll(".product-card")
            const productModal = document.getElementById("productModal")
            const modalClose = document.querySelector(".modal-close")

            // Sample product data
            const products = [
                {
                    id: 1,
                    name: "Gà Giòn Vui Vẻ (1 miếng)",
                    description:
                        "Gà rán giòn thơm ngon, hương vị đặc trưng của Jollibee với lớp bột chiên giòn rụm và thịt gà mềm, thơm ngon.",
                    price: "40.000đ",
                    image: "images/product-1.jpg",
                    ingredients: ["Thịt gà tươi", "Bột chiên xù đặc biệt", "Gia vị Jollibee độc quyền", "Dầu thực vật"],
                    allergens: ["Gluten", "Đậu nành"],
                    nutritionalInfo: {
                        calories: "290 kcal",
                        protein: "17g",
                        fat: "18g",
                        carbs: "15g",
                    },
                    options: [
                        { id: 1, name: "Đùi gà", price: "+0đ" },
                        { id: 2, name: "Cánh gà", price: "+0đ" },
                        { id: 3, name: "Ức gà", price: "+0đ" },
                    ],
                    addons: [
                        { id: 1, name: "Khoai tây chiên (vừa)", price: "25.000đ" },
                        { id: 2, name: "Nước ngọt (vừa)", price: "15.000đ" },
                        { id: 3, name: "Sốt mayonnaise", price: "5.000đ" },
                    ],
                },
                {
                    id: 2,
                    name: "Gà Sốt Cay (1 miếng)",
                    description: "Gà rán phủ sốt cay đặc biệt, cay nồng hấp dẫn, thịt gà mềm, thơm ngon.",
                    price: "45.000đ",
                    image: "images/product-2.jpg",
                    ingredients: [
                        "Thịt gà tươi",
                        "Bột chiên xù đặc biệt",
                        "Sốt cay Jollibee",
                        "Gia vị Jollibee độc quyền",
                        "Dầu thực vật",
                    ],
                    allergens: ["Gluten", "Đậu nành", "Ớt"],
                    nutritionalInfo: {
                        calories: "320 kcal",
                        protein: "18g",
                        fat: "20g",
                        carbs: "16g",
                    },
                    options: [
                        { id: 1, name: "Đùi gà", price: "+0đ" },
                        { id: 2, name: "Cánh gà", price: "+0đ" },
                        { id: 3, name: "Ức gà", price: "+0đ" },
                    ],
                    addons: [
                        { id: 1, name: "Khoai tây chiên (vừa)", price: "25.000đ" },
                        { id: 2, name: "Nước ngọt (vừa)", price: "15.000đ" },
                        { id: 3, name: "Sốt mayonnaise", price: "5.000đ" },
                    ],
                },
                {
                    id: 3,
                    name: "Burger Gà Giòn",
                    description: "Burger với lớp thịt gà giòn, rau tươi và sốt mayonnaise đặc biệt, đậm đà hương vị.",
                    price: "50.000đ",
                    image: "images/product-3.jpg",
                    ingredients: ["Bánh mì burger", "Thịt gà chiên giòn", "Rau xà lách", "Cà chua", "Sốt mayonnaise đặc biệt"],
                    allergens: ["Gluten", "Đậu nành", "Trứng"],
                    nutritionalInfo: {
                        calories: "450 kcal",
                        protein: "22g",
                        fat: "25g",
                        carbs: "35g",
                    },
                    addons: [
                        { id: 1, name: "Thêm phô mai", price: "10.000đ" },
                        { id: 2, name: "Khoai tây chiên (vừa)", price: "25.000đ" },
                        { id: 3, name: "Nước ngọt (vừa)", price: "15.000đ" },
                    ],
                },
                {
                    id: 4,
                    name: "Mỳ Ý Sốt Bò Bằm",
                    description: "Mỳ Ý với sốt bò bằm đậm đà, thơm ngon, kết hợp với phô mai và gia vị đặc biệt.",
                    price: "45.000đ",
                    originalPrice: "55.000đ",
                    image: "images/product-4.jpg",
                    ingredients: ["Mỳ Ý", "Thịt bò xay", "Sốt cà chua", "Phô mai", "Gia vị Ý đặc biệt"],
                    allergens: ["Gluten", "Sữa"],
                    nutritionalInfo: {
                        calories: "520 kcal",
                        protein: "25g",
                        fat: "18g",
                        carbs: "65g",
                    },
                    options: [
                        { id: 1, name: "Cỡ vừa", price: "+0đ" },
                        { id: 2, name: "Cỡ lớn", price: "+20.000đ" },
                    ],
                    addons: [
                        { id: 1, name: "Thêm phô mai", price: "10.000đ" },
                        { id: 2, name: "Bánh mì nướng tỏi", price: "15.000đ" },
                        { id: 3, name: "Nước ngọt (vừa)", price: "15.000đ" },
                    ],
                },
            ]

            if (productCards.length > 0 && productModal) {
                // Modal elements
                const modalProductImage = document.getElementById("modalProductImage")
                const modalProductTitle = document.getElementById("modalProductTitle")
                const modalProductPrice = document.getElementById("modalProductPrice")
                const modalProductOriginalPrice = document.getElementById("modalProductOriginalPrice")
                const modalProductDescription = document.getElementById("modalProductDescription")
                const modalProductOptions = document.getElementById("modalProductOptions")
                const modalProductAddons = document.getElementById("modalProductAddons")
                const modalIngredients = document.getElementById("modalIngredients")
                const modalAllergens = document.getElementById("modalAllergens")
                const modalAllergensContainer = document.getElementById("modalAllergensContainer")
                const modalNutrition = document.getElementById("modalNutrition")
                const modalTotalPrice = document.getElementById("modalTotalPrice")

                // Quantity controls
                const decreaseBtn = document.querySelector(".quantity-btn.decrease")
                const increaseBtn = document.querySelector(".quantity-btn.increase")
                const quantityValue = document.querySelector(".quantity-value")

                // Tab controls
                const tabBtns = document.querySelectorAll(".tab-btn")
                const tabPanels = document.querySelectorAll(".tab-panel")

                // Open modal with product details
                const openProductModal = (productId) => {
                    const product = products.find((p) => p.id === productId)

                    if (product) {
                        // Set basic product info
                        modalProductImage.src = product.image
                        modalProductTitle.textContent = product.name
                        modalProductPrice.textContent = product.price
                        modalProductDescription.textContent = product.description
                        modalTotalPrice.textContent = product.price

                        // Set original price if exists
                        if (product.originalPrice) {
                            modalProductOriginalPrice.textContent = product.originalPrice
                            modalProductOriginalPrice.style.display = "inline"
                        } else {
                            modalProductOriginalPrice.style.display = "none"
                        }

                        // Reset quantity
                        quantityValue.textContent = "1"

                        // Set options if exist
                        if (product.options && product.options.length > 0) {
                            modalProductOptions.innerHTML = ""
                            product.options.forEach((option) => {
                                const optionItem = document.createElement("div")
                                optionItem.className = "option-item"
                                optionItem.dataset.optionId = option.id
                                optionItem.innerHTML = `
                              <div class="option-info">
                                  <input type="radio" name="option" id="option-${option.id}" ${option.id === 1 ? "checked" : ""}>
                                  <label for="option-${option.id}">${option.name}</label>
                              </div>
                              <span class="option-price">${option.price}</span>
                          `
                                if (option.id === 1) {
                                    optionItem.classList.add("active")
                                }
                                optionItem.addEventListener("click", function () {
                                    document.querySelectorAll(".option-item").forEach((item) => {
                                        item.classList.remove("active")
                                    })
                                    this.classList.add("active")
                                    document.getElementById(`option-${option.id}`).checked = true
                                    updateTotalPrice()
                                })
                                modalProductOptions.appendChild(optionItem)
                            })
                            document.querySelector(".product-options").style.display = "block"
                        } else {
                            document.querySelector(".product-options").style.display = "none"
                        }

                        // Set addons if exist
                        if (product.addons && product.addons.length > 0) {
                            modalProductAddons.innerHTML = ""
                            product.addons.forEach((addon) => {
                                const addonItem = document.createElement("div")
                                addonItem.className = "addon-item"
                                addonItem.dataset.addonId = addon.id
                                addonItem.dataset.addonPrice = addon.price
                                addonItem.innerHTML = `
                              <div class="addon-info">
                                  <input type="checkbox" id="addon-${addon.id}">
                                  <label for="addon-${addon.id}">${addon.name}</label>
                              </div>
                              <span class="addon-price">${addon.price}</span>
                          `
                                addonItem.addEventListener("click", function () {
                                    const checkbox = this.querySelector('input[type="checkbox"]')
                                    checkbox.checked = !checkbox.checked
                                    this.classList.toggle("active", checkbox.checked)
                                    updateTotalPrice()
                                })
                                modalProductAddons.appendChild(addonItem)
                            })
                            document.querySelector(".product-addons").style.display = "block"
                        } else {
                            document.querySelector(".product-addons").style.display = "none"
                        }

                        // Set ingredients if exist
                        if (product.ingredients && product.ingredients.length > 0) {
                            modalIngredients.innerHTML = ""
                            product.ingredients.forEach((ingredient) => {
                                const li = document.createElement("li")
                                li.textContent = ingredient
                                modalIngredients.appendChild(li)
                            })
                        } else {
                            modalIngredients.innerHTML = "<p>Thông tin đang được cập nhật.</p>"
                        }

                        // Set allergens if exist
                        if (product.allergens && product.allergens.length > 0) {
                            modalAllergens.innerHTML = ""
                            product.allergens.forEach((allergen) => {
                                const span = document.createElement("span")
                                span.className = "allergen-tag"
                                span.textContent = allergen
                                modalAllergens.appendChild(span)
                            })
                            modalAllergensContainer.style.display = "block"
                        } else {
                            modalAllergensContainer.style.display = "none"
                        }

                        // Set nutrition info if exists
                        if (product.nutritionalInfo) {
                            modalNutrition.innerHTML = ""
                            Object.entries(product.nutritionalInfo).forEach(([key, value]) => {
                                const tr = document.createElement("tr")
                                tr.innerHTML = `
                              <td>${key.charAt(0).toUpperCase() + key.slice(1)}</td>
                              <td>${value}</td>
                          `
                                modalNutrition.appendChild(tr)
                            })
                        } else {
                            modalNutrition.innerHTML = '<tr><td colspan="2">Thông tin dinh dưỡng đang được cập nhật.</td></tr>'
                        }

                        // Show modal
                        productModal.classList.add("active")
                        document.body.style.overflow = "hidden"
                    }
                }

                // Update total price based on quantity, options and addons
                const updateTotalPrice = () => {
                    const productId = Number.parseInt(document.querySelector(".product-card[data-product-id]").dataset.productId)
                    const product = products.find((p) => p.id === productId)

                    if (product) {
                        let basePrice = Number.parseInt(product.price.replace(/\D/g, ""))
                        const quantity = Number.parseInt(quantityValue.textContent)

                        // Add option price if selected
                        const selectedOption = document.querySelector(".option-item.active")
                        if (selectedOption) {
                            const optionPrice = selectedOption.querySelector(".option-price").textContent
                            if (optionPrice !== "+0đ") {
                                basePrice += Number.parseInt(optionPrice.replace(/[^\d]/g, ""))
                            }
                        }

                        // Calculate total
                        let total = basePrice * quantity

                        // Add addon prices
                        const selectedAddons = document.querySelectorAll('.addon-item input[type="checkbox"]:checked')
                        selectedAddons.forEach((addon) => {
                            const addonItem = addon.closest(".addon-item")
                            const addonPrice = Number.parseInt(addonItem.dataset.addonPrice.replace(/\D/g, ""))
                            total += addonPrice * quantity
                        })

                        // Update total price display
                        modalTotalPrice.textContent = total.toLocaleString("vi-VN") + "đ"
                    }
                }

                // Quantity controls
                if (decreaseBtn && increaseBtn && quantityValue) {
                    decreaseBtn.addEventListener("click", () => {
                        const quantity = Number.parseInt(quantityValue.textContent)
                        if (quantity > 1) {
                            quantityValue.textContent = quantity - 1
                            updateTotalPrice()
                        }
                    })

                    increaseBtn.addEventListener("click", () => {
                        const quantity = Number.parseInt(quantityValue.textContent)
                        quantityValue.textContent = quantity + 1
                        updateTotalPrice()
                    })
                }

                // Tab controls
                if (tabBtns.length > 0 && tabPanels.length > 0) {
                    tabBtns.forEach((btn) => {
                        btn.addEventListener("click", function () {
                            const tabId = this.dataset.tab

                            // Remove active class from all tabs and panels
                            tabBtns.forEach((btn) => btn.classList.remove("active"))
                            tabPanels.forEach((panel) => panel.classList.remove("active"))

                            // Add active class to clicked tab and corresponding panel
                            this.classList.add("active")
                            document.getElementById(tabId).classList.add("active")
                        })
                    })
                }

                // Add click event to product cards
                productCards.forEach((card) => {
                    card.addEventListener("click", function () {
                        const productId = Number.parseInt(this.dataset.productId)
                        openProductModal(productId)
                    })

                    // Prevent modal from opening when clicking add to cart button
                    const addToCartBtn = card.querySelector(".add-to-cart-btn")
                    if (addToCartBtn) {
                        addToCartBtn.addEventListener("click", (e) => {
                            e.stopPropagation()
                            console.log("Add to cart:", card.dataset.productId)
                        })
                    }
                })

                // Close modal
                if (modalClose) {
                    modalClose.addEventListener("click", () => {
                        productModal.classList.remove("active")
                        document.body.style.overflow = ""
                    })

                    // Close modal when clicking outside
                    productModal.addEventListener("click", (e) => {
                        if (e.target === productModal) {
                            productModal.classList.remove("active")
                            document.body.style.overflow = ""
                        }
                    })
                }
            }
        })

    </script>
@endsection