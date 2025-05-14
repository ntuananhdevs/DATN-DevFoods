@extends('layouts.customer.fullLayoutMaster')

@section('title', 'Cart')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/cart-item.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
<link rel="stylesheet" href="{{ asset('fonts/feather/style.min.css') }}">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@6.8.4/swiper-bundle.min.css">
@endsection

@section('scripts')
<script src="{{ asset('js/scripts/cart-item.js') }}"></script>
@endsection

@section('content')
<div class="container">
    <!-- Breadcrumb -->
    <div class="breadcrumb">
        <a href="/">Trang chủ</a>
        <span class="separator">/</span>
        <a href="/shop">Cửa hàng</a>
        <span class="separator">/</span>
        <span class="current">Giỏ hàng</span>
    </div>

    <h1 class="page-title">Giỏ hàng của bạn</h1>

    <div class="cart-container">
        <!-- Cart Items Section -->
        <div class="cart-items-section">
            <div class="cart-header">
                <div class="cart-header-item product-col">Sản phẩm</div>
                <div class="cart-header-item price-col">Đơn giá</div>
                <div class="cart-header-item quantity-col">Số lượng</div>
                <div class="cart-header-item total-col">Thành tiền</div>
                <div class="cart-header-item action-col"></div>
            </div>

            <div class="cart-items">
                @if(count($cartData ?? []) > 0)
                    @foreach($cartData as $key => $item)
                        <div class="cart-item" data-cart-key="{{ $key }}">
                            <div class="product-col">
                                <div class="product-image">
                                    <img src="{{ $item['image'] ?? 'https://via.placeholder.com/100' }}" alt="{{ $item['name'] }}">
                                </div>
                                <div class="product-info">
                                    <h3 class="product-title">{{ $item['name'] }}</h3>
                                    @if($item['variant_name'])
                                        <div class="product-variant">Biến thể: <span>{{ $item['variant_name'] }}</span></div>
                                    @endif
                                    @if(!empty($item['attributes']))
                                        @foreach($item['attributes'] as $attrName => $attrValue)
                                            <div class="product-variant">{{ $attrName }}: <span>{{ $attrValue }}</span></div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                            <div class="price-col">{{ number_format($item['price'], 0, ',', '.') }}đ</div>
                            <div class="quantity-col">
                                <div class="quantity-selector">
                                    <button class="quantity-btn minus-btn" data-cart-key="{{ $key }}">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                    <input type="number" class="quantity-input" value="{{ $item['quantity'] }}" min="1" max="99" data-cart-key="{{ $key }}">
                                    <button class="quantity-btn plus-btn" data-cart-key="{{ $key }}">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="total-col">{{ number_format($item['total'], 0, ',', '.') }}đ</div>
                            <div class="action-col">
                                <button class="remove-btn" data-cart-key="{{ $key }}">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="empty-cart">
                        <div class="empty-cart-icon">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <h3>Giỏ hàng của bạn đang trống</h3>
                        <p>Hãy thêm sản phẩm vào giỏ hàng để tiếp tục mua sắm</p>
                    </div>
                @endif
            </div>

            <div class="cart-actions">
                <a href="{{ asset('shop/product') }}" class="continue-shopping-btn">
                    <i class="fas fa-arrow-left"></i>
                    Tiếp tục mua sắm
                </a>
                <button class="update-cart-btn">
                    <i class="fas fa-sync-alt"></i>
                    Cập nhật giỏ hàng
                </button>
            </div>
        </div>

        <!-- Cart Summary Section -->
        <div class="cart-summary-section">
            <div class="cart-summary-card">
                <h2 class="summary-title">Tóm tắt đơn hàng</h2>

                <div class="summary-row">
                    <div class="summary-label">Tạm tính</div>
                    <div class="summary-value">{{ number_format($totalPrice ?? 0, 0, ',', '.') }}đ</div>
                </div>

                <div class="summary-row">
                    <div class="summary-label">Phí vận chuyển</div>
                    <div class="summary-value">30.000đ</div>
                </div>

                <div class="summary-row discount">
                    <div class="summary-label">Giảm giá</div>
                    <div class="summary-value">-30.000đ</div>
                </div>

                <div class="summary-divider"></div>

                <div class="summary-row total">
                    <div class="summary-label">Tổng cộng</div>
                    <div class="summary-value">364.000đ</div>
                </div>

                <div class="coupon-section">
                    <h3 class="coupon-title">Mã giảm giá</h3>
                    <div class="coupon-form">
                        <input type="text" class="coupon-input" placeholder="Nhập mã giảm giá">
                        <button class="coupon-btn">Áp dụng</button>
                    </div>
                </div>

                <button class="checkout-btn">
                    <i class="fas fa-lock"></i>
                    Tiến hành thanh toán
                </button>

                <div class="payment-methods">
                    <div class="payment-title">Chấp nhận thanh toán</div>
                    <div class="payment-icons">
                        <i class="fab fa-cc-visa"></i>
                        <i class="fab fa-cc-mastercard"></i>
                        <i class="fab fa-cc-paypal"></i>
                        <i class="fab fa-cc-apple-pay"></i>
                    </div>
                </div>
            </div>

            <div class="delivery-info-card">
                <div class="delivery-icon">
                    <i class="fas fa-truck"></i>
                </div>
                <div class="delivery-content">
                    <h3 class="delivery-title">Miễn phí vận chuyển</h3>
                    <p class="delivery-text">Cho đơn hàng từ 200.000đ trong phạm vi 5km</p>
                </div>
            </div>

            <div class="support-card">
                <div class="support-icon">
                    <i class="fas fa-headset"></i>
                </div>
                <div class="support-content">
                    <h3 class="support-title">Hỗ trợ 24/7</h3>
                    <p class="support-text">Gọi ngay: <strong>1900 1234</strong></p>
                </div>
            </div>
        </div>
    </div>

    <!-- You May Also Like Section -->
    <div class="you-may-like-section">
        <h2 class="section-title">Có thể bạn cũng thích</h2>

        <div class="product-grid">
            <!-- Product 1 -->
            <div class="card">
                <span class="like"><i class='bx bx-heart'></i></span>
                <span class="cart"><i class='bx bx-cart-alt'></i></span>
                <div class="card__img">
                    <img src="https://via.placeholder.com/300" alt="Gà Sốt Teriyaki" />
                </div>
                <h2 class="card__title">Gà Sốt Teriyaki</h2>
                <p class="card__price">89.000đ</p>
                <div class="card__action">
                    <button class="action-btn favorite-btn">
                        <i class="fas fa-heart"></i>
                    </button>
                    <button class="action-btn cart-btn">
                        <i class="fas fa-shopping-bag"></i>
                    </button>
                    <a class="action-btn" href="#"><i class="fas fa-info"></i></a>
                </div>
            </div>

            <!-- Product 2 -->
            <div class="card">
                <span class="like"><i class='bx bx-heart'></i></span>
                <span class="cart"><i class='bx bx-cart-alt'></i></span>
                <div class="card__img">
                    <img src="https://via.placeholder.com/300" alt="Gà Sốt Mật Ong" />
                </div>
                <h2 class="card__title">Gà Sốt Mật Ong</h2>
                <p class="card__price">82.000đ</p>
                <div class="card__action">
                    <button class="action-btn favorite-btn">
                        <i class="fas fa-heart"></i>
                    </button>
                    <button class="action-btn cart-btn">
                        <i class="fas fa-shopping-bag"></i>
                    </button>
                    <a class="action-btn" href="#"><i class="fas fa-info"></i></a>
                </div>
            </div>

            <!-- Product 3 -->
            <div class="card">
                <span class="like"><i class='bx bx-heart'></i></span>
                <span class="cart"><i class='bx bx-cart-alt'></i></span>
                <div class="card__img">
                    <img src="https://via.placeholder.com/300" alt="Gà Rán Không Xương" />
                </div>
                <h2 class="card__title">Gà Rán Không Xương</h2>
                <p class="card__price">79.000đ</p>
                <div class="card__action">
                    <button class="action-btn favorite-btn">
                        <i class="fas fa-heart"></i>
                    </button>
                    <button class="action-btn cart-btn">
                        <i class="fas fa-shopping-bag"></i>
                    </button>
                    <a class="action-btn" href="#"><i class="fas fa-info"></i></a>
                </div>
            </div>

            <!-- Product 4 -->
            <div class="card">
                <span class="like"><i class='bx bx-heart'></i></span>
                <span class="cart"><i class='bx bx-cart-alt'></i></span>
                <div class="card__img">
                    <img src="https://via.placeholder.com/300" alt="Gà Sốt Phô Mai" />
                </div>
                <h2 class="card__title">Gà Sốt Phô Mai</h2>
                <p class="card__price">95.000đ</p>
                <div class="card__action">
                    <button class="action-btn favorite-btn">
                        <i class="fas fa-heart"></i>
                    </button>
                    <button class="action-btn cart-btn">
                        <i class="fas fa-shopping-bag"></i>
                    </button>
                    <a class="action-btn" href="#"><i class="fas fa-info"></i></a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection