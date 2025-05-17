@extends('layouts.customer.fullLayoutMaster')

@section('title', 'Cart')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/cart-item.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
<link rel="stylesheet" href="{{ asset('fonts/feather/style.min.css') }}">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@6.8.4/swiper-bundle.min.css">
<style>
    /* CSS để đẩy toast xuống thấp hơn header */
    .toast-container {
        position: fixed;
        top: 80px; /* Điều chỉnh giá trị này tùy theo chiều cao của header */
        right: 20px;
        z-index: 1050;
    }
    
    /* Đảm bảo toast hiển thị đúng */
    .toast {
        min-width: 250px;
        margin-bottom: 10px;
        background-color: #fff;
        border-radius: 4px;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        opacity: 0;
        transition: opacity 0.3s ease-in-out;
    }
    
    .toast.show {
        opacity: 1;
    }
    
    /* Tùy chỉnh màu sắc cho các loại toast khác nhau */
    .toast.success .toast-header {
        background-color: #28a745;
        color: white;
    }
    
    .toast.error .toast-header {
        background-color: #dc3545;
        color: white;
    }
    
    .toast.warning .toast-header {
        background-color: #ffc107;
        color: #212529;
    }
    
    .toast.info .toast-header {
        background-color: #17a2b8;
        color: white;
    }
    
    /* CSS cho checkbox và các phần tử mới */
    .select-col {
        width: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .item-checkbox {
        width: 20px;
        height: 20px;
        cursor: pointer;
    }
    
    .select-all-container {
        display: flex;
        align-items: center;
        padding: 10px 0;
        margin-bottom: 10px;
        border-bottom: 1px solid #eee;
    }
    
    .select-all-label {
        display: flex;
        align-items: center;
        cursor: pointer;
        font-weight: 500;
    }
    
    .select-all-label input {
        margin-right: 10px;
        width: 20px;
        height: 20px;
    }
    
    .checkout-btn:disabled {
        background-color: #ccc;
        cursor: not-allowed;
    }
</style>
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    // Thiết lập CSRF token cho tất cả các request Ajax
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    
    $(document).ready(function() {
        // Hàm tính toán tổng tiền dựa trên các sản phẩm được chọn
        function calculateTotal() {
            let subtotal = 0;
            $('.item-checkbox:checked').each(function() {
                const cartKey = $(this).data('cart-key');
                const price = parseFloat($(this).data('price'));
                subtotal += price;
            });
            
            // Phí vận chuyển và giảm giá
            const shipping = 30000;
            const discount = 30000;
            
            // Tổng cộng
            const total = subtotal + shipping - discount;
            
            // Cập nhật hiển thị
            $('.summary-value:first').text(formatCurrency(subtotal));
            $('.summary-row.total .summary-value').text(formatCurrency(total));
            
            // Cập nhật nút thanh toán
            if (subtotal > 0) {
                $('.checkout-btn').prop('disabled', false);
            } else {
                $('.checkout-btn').prop('disabled', true);
            }
        }
        
        // Hàm định dạng tiền tệ
        function formatCurrency(amount) {
            return new Intl.NumberFormat('vi-VN', { style: 'decimal' }).format(amount) + 'đ';
        }
        
        // Xử lý sự kiện khi checkbox của sản phẩm thay đổi
        $(document).on('change', '.item-checkbox', function() {
            calculateTotal();
            
            // Kiểm tra nếu tất cả checkbox đã được chọn
            const allChecked = $('.item-checkbox:checked').length === $('.item-checkbox').length;
            $('#select-all-checkbox').prop('checked', allChecked);
        });
        
        // Xử lý sự kiện khi checkbox "Chọn tất cả" thay đổi
        $('#select-all-checkbox').on('change', function() {
            const isChecked = $(this).prop('checked');
            $('.item-checkbox').prop('checked', isChecked);
            calculateTotal();
        });
        
        // Xử lý sự kiện khi nhấn nút xóa sản phẩm
        $(document).on('click', '.remove-btn', function(e) {
            e.preventDefault();
            
            const cartKey = $(this).data('cart-key');
            console.log('Removing item with key:', cartKey); // Thêm log để debug
            
            // Kiểm tra xem cartKey có tồn tại không
            if (!cartKey) {
                console.error('Cart key is missing');
                return;
            }
            
            const cartItem = $(this).closest('.cart-item');
            
            // Hiển thị xác nhận xóa
            if (confirm('Bạn có chắc chắn muốn xóa sản phẩm này khỏi giỏ hàng?')) {
                // Gửi yêu cầu Ajax để xóa sản phẩm
                $.ajax({
                    url: '{{ route("customer.cart.ajax.remove") }}',
                    type: 'POST',
                    data: {
                        cart_key: cartKey
                    },
                    success: function(response) {
                        console.log('Response:', response); // Thêm log để debug
                        if (response.success) {
                            // Xóa phần tử khỏi DOM
                            cartItem.fadeOut(300, function() {
                                $(this).remove();
                                
                                // Cập nhật tổng giá
                                $('.summary-value:first').text(response.subtotal);
                                $('.summary-row.total .summary-value').text(response.total);
                                
                                // Cập nhật số lượng sản phẩm trong giỏ hàng ở header
                                $('.cart-count').text(response.cart_count);
                                
                                // Kiểm tra nếu giỏ hàng trống
                                if (response.cart_count === 0) {
                                    $('.cart-items').html(`
                                        <div class="empty-cart">
                                            <div class="empty-cart-icon">
                                                <i class="fas fa-shopping-cart"></i>
                                            </div>
                                            <h3>Giỏ hàng của bạn đang trống</h3>
                                            <p>Hãy thêm sản phẩm vào giỏ hàng để tiếp tục mua sắm</p>
                                        </div>
                                    `);
                                }
                            });
                        } else {
                            alert('Có lỗi xảy ra khi xóa sản phẩm. Vui lòng thử lại!');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', xhr.responseText); // Thêm log chi tiết lỗi
                        alert('Có lỗi xảy ra khi xóa sản phẩm. Vui lòng thử lại!');
                    }
                });
            }
        });
        
        // Xử lý sự kiện khi thay đổi số lượng
        $('.quantity-input').on('change', function() {
            const cartKey = $(this).data('cart-key');
            const quantity = parseInt($(this).val());
            
            // Kiểm tra giá trị hợp lệ
            if (quantity < 1) {
                $(this).val(1);
                updateCartItem(cartKey, 1);
            } else if (quantity > 99) {
                $(this).val(99);
                updateCartItem(cartKey, 99);
            } else {
                updateCartItem(cartKey, quantity);
            }
        });
        
        // Xử lý nút tăng số lượng
        $('.plus-btn').on('click', function() {
            const cartKey = $(this).data('cart-key');
            const input = $(this).siblings('.quantity-input');
            const currentValue = parseInt(input.val());
            
            if (currentValue < 99) {
                input.val(currentValue + 1);
                updateCartItem(cartKey, currentValue + 1);
            }
        });
        
        // Xử lý nút giảm số lượng
        $('.minus-btn').on('click', function() {
            const cartKey = $(this).data('cart-key');
            const input = $(this).siblings('.quantity-input');
            const currentValue = parseInt(input.val());
            
            if (currentValue > 1) {
                input.val(currentValue - 1);
                updateCartItem(cartKey, currentValue - 1);
            }
        });
        
        // Xử lý nút cập nhật giỏ hàng
        $('.update-cart-btn').on('click', function() {
            location.reload();
        });
        
        // Hàm cập nhật số lượng sản phẩm
        function updateCartItem(cartKey, quantity) {
            $.ajax({
                url: '{{ route("customer.cart.ajax.update") }}',
                type: 'POST',
                data: {
                    cart_key: cartKey,
                    quantity: quantity
                },
                success: function(response) {
                    if (response.success) {
                        // Cập nhật thành tiền cho sản phẩm
                        const cartItem = $(`.cart-item[data-cart-key="${cartKey}"]`);
                        cartItem.find('.total-col').text(response.item_total);
                        
                        // Cập nhật tổng giá
                        $('.summary-value:first').text(response.subtotal);
                        $('.summary-row.total .summary-value').text(response.total);
                    } else {
                        alert('Có lỗi xảy ra khi cập nhật giỏ hàng. Vui lòng thử lại!');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error:', xhr.responseText);
                    alert('Có lỗi xảy ra khi cập nhật giỏ hàng. Vui lòng thử lại!');
                }
            });
        }
    });
</script>
@endsection

@section('content')
<div class="container-cart">
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
            <div class="select-all-container">
                <label class="select-all-label">
                    <input type="checkbox" id="select-all-checkbox" checked>
                    <span>Chọn tất cả</span>
                </label>
            </div>
            
            <div class="cart-header">
                <div class="cart-header-item select-col">Chọn</div>
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
                            <div class="select-col">
                                <input type="checkbox" class="item-checkbox" data-cart-key="{{ $key }}" data-price="{{ $item['total'] }}" checked>
                            </div>
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
                    <div class="summary-value">{{ number_format(($totalPrice ?? 0) + 30000 - 30000, 0, ',', '.') }}đ</div>
                </div>

                <div class="coupon-section">
                    <h3 class="coupon-title">Mã giảm giá</h3>
                    <div class="coupon-form">
                        <input type="text" class="coupon-input" placeholder="Nhập mã giảm giá">
                        <button class="coupon-btn">Áp dụng</button>
                    </div>
                </div>

                <button class="checkout-btn" id="checkout-btn">
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
