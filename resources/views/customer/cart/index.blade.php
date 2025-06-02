@extends('layouts.customer.fullLayoutMaster')

@section('title', 'FastFood - Giỏ Hàng')

@section('content')
<style>
    .container {
      max-width: 1280px;
      margin: 0 auto;
   }
</style>
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-2">Giỏ Hàng</h1>
    <p class="text-gray-500 mb-8">Kiểm tra và chỉnh sửa các sản phẩm trong giỏ hàng của bạn</p>

    <div class="grid lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <div class="hidden md:grid grid-cols-12 gap-4 p-4 bg-gray-50 font-medium">
                    <div class="col-span-6">Sản phẩm</div>
                    <div class="col-span-2 text-center">Giá</div>
                    <div class="col-span-2 text-center">Số lượng</div>
                    <div class="col-span-2 text-right">Tổng</div>
                </div>

                <hr class="border-t border-gray-200">

                <!-- Cart Item 1 -->
                <div class="p-4 md:p-6 cart-item">
                    <div class="grid md:grid-cols-12 gap-4 items-center">
                        <div class="md:col-span-6 flex items-center gap-4">
                            <div class="relative h-20 w-20 flex-shrink-0 rounded overflow-hidden">
                                <img src="/placeholder.svg?height=400&width=400" alt="Burger Bò Cổ Điển" class="object-cover w-full h-full">
                            </div>
                            <div>
                                <h3 class="font-medium">Burger Bò Cổ Điển</h3>
                                <p class="text-sm text-gray-500">Size vừa, Không hành</p>
                                <button class="text-red-500 text-sm flex items-center mt-1 hover:underline remove-item">
                                    <i class="fas fa-trash-alt h-3 w-3 mr-1"></i>
                                    Xóa
                                </button>
                            </div>
                        </div>

                        <div class="md:col-span-2 text-center">
                            <span class="md:hidden font-medium mr-2">Giá:</span>
                            <span class="item-price">59.000đ</span>
                        </div>

                        <div class="md:col-span-2 flex items-center justify-center">
                            <div class="flex items-center border rounded">
                                <button class="px-2 py-1 hover:bg-gray-100 decrease-quantity">
                                    <i class="fas fa-minus h-3 w-3"></i>
                                </button>
                                <span class="px-3 py-1 item-quantity">2</span>
                                <button class="px-2 py-1 hover:bg-gray-100 increase-quantity">
                                    <i class="fas fa-plus h-3 w-3"></i>
                                </button>
                            </div>
                        </div>

                        <div class="md:col-span-2 text-right font-medium">
                            <span class="md:hidden font-medium mr-2">Tổng:</span>
                            <span class="item-total">118.000đ</span>
                        </div>
                    </div>
                </div>

                <hr class="border-t border-gray-200">

                <!-- Cart Item 2 -->
                <div class="p-4 md:p-6 cart-item">
                    <div class="grid md:grid-cols-12 gap-4 items-center">
                        <div class="md:col-span-6 flex items-center gap-4">
                            <div class="relative h-20 w-20 flex-shrink-0 rounded overflow-hidden">
                                <img src="/placeholder.svg?height=400&width=400" alt="Pizza Hải Sản Đặc Biệt" class="object-cover w-full h-full">
                            </div>
                            <div>
                                <h3 class="font-medium">Pizza Hải Sản Đặc Biệt</h3>
                                <p class="text-sm text-gray-500">Size lớn, Đế dày</p>
                                <button class="text-red-500 text-sm flex items-center mt-1 hover:underline remove-item">
                                    <i class="fas fa-trash-alt h-3 w-3 mr-1"></i>
                                    Xóa
                                </button>
                            </div>
                        </div>

                        <div class="md:col-span-2 text-center">
                            <span class="md:hidden font-medium mr-2">Giá:</span>
                            <span class="item-price">159.000đ</span>
                        </div>

                        <div class="md:col-span-2 flex items-center justify-center">
                            <div class="flex items-center border rounded">
                                <button class="px-2 py-1 hover:bg-gray-100 decrease-quantity">
                                    <i class="fas fa-minus h-3 w-3"></i>
                                </button>
                                <span class="px-3 py-1 item-quantity">1</span>
                                <button class="px-2 py-1 hover:bg-gray-100 increase-quantity">
                                    <i class="fas fa-plus h-3 w-3"></i>
                                </button>
                            </div>
                        </div>

                        <div class="md:col-span-2 text-right font-medium">
                            <span class="md:hidden font-medium mr-2">Tổng:</span>
                            <span class="item-total">159.000đ</span>
                        </div>
                    </div>
                </div>

                <hr class="border-t border-gray-200">

                <!-- Cart Item 3 -->
                <div class="p-4 md:p-6 cart-item">
                    <div class="grid md:grid-cols-12 gap-4 items-center">
                        <div class="md:col-span-6 flex items-center gap-4">
                            <div class="relative h-20 w-20 flex-shrink-0 rounded overflow-hidden">
                                <img src="/placeholder.svg?height=400&width=400" alt="Coca Cola" class="object-cover w-full h-full">
                            </div>
                            <div>
                                <h3 class="font-medium">Coca Cola</h3>
                                <p class="text-sm text-gray-500">Size lớn, Có đá</p>
                                <button class="text-red-500 text-sm flex items-center mt-1 hover:underline remove-item">
                                    <i class="fas fa-trash-alt h-3 w-3 mr-1"></i>
                                    Xóa
                                </button>
                            </div>
                        </div>

                        <div class="md:col-span-2 text-center">
                            <span class="md:hidden font-medium mr-2">Giá:</span>
                            <span class="item-price">15.000đ</span>
                        </div>

                        <div class="md:col-span-2 flex items-center justify-center">
                            <div class="flex items-center border rounded">
                                <button class="px-2 py-1 hover:bg-gray-100 decrease-quantity">
                                    <i class="fas fa-minus h-3 w-3"></i>
                                </button>
                                <span class="px-3 py-1 item-quantity">3</span>
                                <button class="px-2 py-1 hover:bg-gray-100 increase-quantity">
                                    <i class="fas fa-plus h-3 w-3"></i>
                                </button>
                            </div>
                        </div>

                        <div class="md:col-span-2 text-right font-medium">
                            <span class="md:hidden font-medium mr-2">Tổng:</span>
                            <span class="item-total">45.000đ</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-6 flex flex-wrap gap-4">
                <a href="/products" class="border border-gray-300 hover:bg-gray-50 px-4 py-2 rounded-md flex items-center transition-colors">
                    <i class="fas fa-arrow-left h-4 w-4 mr-2"></i>
                    Tiếp Tục Mua Sắm
                </a>
            </div>

            <!-- Suggested Products -->
            <div class="mt-12">
                <h2 class="text-xl font-bold mb-6">Có Thể Bạn Cũng Thích</h2>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <!-- Suggested Product 1 -->
                    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                        <div class="relative h-40">
                            <img src="/placeholder.svg?height=400&width=400" alt="Gà Rán Giòn Cay" class="object-cover w-full h-full">
                        </div>
                        <div class="p-3">
                            <h3 class="font-medium text-sm mb-1 line-clamp-1">Gà Rán Giòn Cay</h3>
                            <p class="text-orange-500 font-bold text-sm mb-2">55.000đ</p>
                            <button class="w-full bg-orange-500 hover:bg-orange-600 text-white px-3 py-1 rounded-md text-xs flex items-center justify-center transition-colors add-suggested">
                                Thêm vào giỏ
                            </button>
                        </div>
                    </div>

                    <!-- Suggested Product 2 -->
                    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                        <div class="relative h-40">
                            <img src="/placeholder.svg?height=400&width=400" alt="Khoai Tây Chiên" class="object-cover w-full h-full">
                        </div>
                        <div class="p-3">
                            <h3 class="font-medium text-sm mb-1 line-clamp-1">Khoai Tây Chiên</h3>
                            <p class="text-orange-500 font-bold text-sm mb-2">25.000đ</p>
                            <button class="w-full bg-orange-500 hover:bg-orange-600 text-white px-3 py-1 rounded-md text-xs flex items-center justify-center transition-colors add-suggested">
                                Thêm vào giỏ
                            </button>
                        </div>
                    </div>

                    <!-- Suggested Product 3 -->
                    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                        <div class="relative h-40">
                            <img src="/placeholder.svg?height=400&width=400" alt="Nước Cam Tươi" class="object-cover w-full h-full">
                        </div>
                        <div class="p-3">
                            <h3 class="font-medium text-sm mb-1 line-clamp-1">Nước Cam Tươi</h3>
                            <p class="text-orange-500 font-bold text-sm mb-2">35.000đ</p>
                            <button class="w-full bg-orange-500 hover:bg-orange-600 text-white px-3 py-1 rounded-md text-xs flex items-center justify-center transition-colors add-suggested">
                                Thêm vào giỏ
                            </button>
                        </div>
                    </div>

                    <!-- Suggested Product 4 -->
                    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                        <div class="relative h-40">
                            <img src="/placeholder.svg?height=400&width=400" alt="Bánh Chocolate Nóng" class="object-cover w-full h-full">
                        </div>
                        <div class="p-3">
                            <h3 class="font-medium text-sm mb-1 line-clamp-1">Bánh Chocolate Nóng</h3>
                            <p class="text-orange-500 font-bold text-sm mb-2">39.000đ</p>
                            <button class="w-full bg-orange-500 hover:bg-orange-600 text-white px-3 py-1 rounded-md text-xs flex items-center justify-center transition-colors add-suggested">
                                Thêm vào giỏ
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div>
            <div class="bg-white rounded-lg shadow-sm p-6 sticky top-4">
                <h2 class="text-xl font-bold mb-4">Tóm Tắt Đơn Hàng</h2>

                <div class="space-y-3 mb-6">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Tạm tính</span>
                        <span id="subtotal">322.000đ</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Phí giao hàng</span>
                        <span id="shipping">Miễn phí</span>
                    </div>
                    <div class="flex justify-between text-green-600 hidden" id="discount-container">
                        <span>Giảm giá</span>
                        <span id="discount">-0đ</span>
                    </div>
                    <hr class="border-t border-gray-200">
                    <div class="flex justify-between font-bold text-lg">
                        <span>Tổng cộng</span>
                        <span id="total">322.000đ</span>
                    </div>
                </div>

                <div class="mb-6">
                    <div class="flex gap-2 mb-2">
                        <input type="text" placeholder="Nhập mã giảm giá" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500" id="coupon-code">
                        <button class="border border-gray-300 hover:bg-gray-50 px-4 py-2 rounded-md transition-colors" id="apply-coupon">
                            Áp Dụng
                        </button>
                    </div>
                    <p class="text-xs text-gray-500">Nhập mã "FASTFOOD10" để được giảm 10%</p>
                </div>

                <a href="/checkout" class="block w-full bg-orange-500 hover:bg-orange-600 text-white text-center px-6 py-3 rounded-md font-medium transition-colors">
                    Tiến Hành Thanh Toán
                </a>

                <div class="mt-4 text-xs text-gray-500 text-center">
                    Đơn hàng trên 100.000đ được miễn phí giao hàng
                </div>
            </div>

            <div class="mt-6 bg-orange-50 rounded-lg p-4">
                <h3 class="font-medium mb-2">Chính sách mua hàng</h3>
                <ul class="text-sm text-gray-600 space-y-1">
                    <li>Miễn phí giao hàng cho đơn từ 100.000đ</li>
                    <li>Đổi trả trong vòng 30 phút nếu lỗi từ nhà hàng</li>
                    <li>Hỗ trợ 24/7: 1900 1234</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Mini Cart Aside -->
<div class="fixed top-0 right-0 h-full w-full md:w-96 bg-white shadow-xl z-50 transform translate-x-full transition-transform duration-300" id="mini-cart">
    <div class="flex flex-col h-full">
        <div class="flex items-center justify-between p-4 border-b">
            <h2 class="text-lg font-bold">Giỏ Hàng (3)</h2>
            <button class="p-2" id="close-mini-cart">
                <i class="fas fa-times h-5 w-5"></i>
            </button>
        </div>

        <div class="flex-1 overflow-y-auto p-4">
            <!-- Mini Cart Item 1 -->
            <div class="flex gap-3 pb-3 border-b">
                <div class="relative h-16 w-16 flex-shrink-0 rounded overflow-hidden">
                    <img src="/placeholder.svg?height=400&width=400" alt="Burger Bò Cổ Điển" class="object-cover w-full h-full">
                </div>
                <div class="flex-1">
                    <div class="flex justify-between">
                        <h3 class="font-medium text-sm">Burger Bò Cổ Điển</h3>
                        <button class="text-gray-400 hover:text-red-500">
                            <i class="fas fa-times h-4 w-4"></i>
                        </button>
                    </div>
                    <p class="text-xs text-gray-500">Size vừa, Không hành</p>
                    <div class="flex justify-between items-center mt-2">
                        <div class="flex items-center border rounded">
                            <button class="px-1 py-0.5 hover:bg-gray-100">
                                <i class="fas fa-minus h-3 w-3"></i>
                            </button>
                            <span class="px-2 py-0.5 text-sm">2</span>
                            <button class="px-1 py-0.5 hover:bg-gray-100">
                                <i class="fas fa-plus h-3 w-3"></i>
                            </button>
                        </div>
                        <p class="font-medium">118.000đ</p>
                    </div>
                </div>
            </div>

            <!-- Mini Cart Item 2 -->
            <div class="flex gap-3 py-3 border-b">
                <div class="relative h-16 w-16 flex-shrink-0 rounded overflow-hidden">
                    <img src="/placeholder.svg?height=400&width=400" alt="Pizza Hải Sản Đặc Biệt" class="object-cover w-full h-full">
                </div>
                <div class="flex-1">
                    <div class="flex justify-between">
                        <h3 class="font-medium text-sm">Pizza Hải Sản Đặc Biệt</h3>
                        <button class="text-gray-400 hover:text-red-500">
                            <i class="fas fa-times h-4 w-4"></i>
                        </button>
                    </div>
                    <p class="text-xs text-gray-500">Size lớn, Đế dày</p>
                    <div class="flex justify-between items-center mt-2">
                        <div class="flex items-center border rounded">
                            <button class="px-1 py-0.5 hover:bg-gray-100">
                                <i class="fas fa-minus h-3 w-3"></i>
                            </button>
                            <span class="px-2 py-0.5 text-sm">1</span>
                            <button class="px-1 py-0.5 hover:bg-gray-100">
                                <i class="fas fa-plus h-3 w-3"></i>
                            </button>
                        </div>
                        <p class="font-medium">159.000đ</p>
                    </div>
                </div>
            </div>

            <!-- Mini Cart Item 3 -->
            <div class="flex gap-3 pt-3">
                <div class="relative h-16 w-16 flex-shrink-0 rounded overflow-hidden">
                    <img src="/placeholder.svg?height=400&width=400" alt="Coca Cola" class="object-cover w-full h-full">
                </div>
                <div class="flex-1">
                    <div class="flex justify-between">
                        <h3 class="font-medium text-sm">Coca Cola</h3>
                        <button class="text-gray-400 hover:text-red-500">
                            <i class="fas fa-times h-4 w-4"></i>
                        </button>
                    </div>
                    <p class="text-xs text-gray-500">Size lớn, Có đá</p>
                    <div class="flex justify-between items-center mt-2">
                        <div class="flex items-center border rounded">
                            <button class="px-1 py-0.5 hover:bg-gray-100">
                                <i class="fas fa-minus h-3 w-3"></i>
                            </button>
                            <span class="px-2 py-0.5 text-sm">3</span>
                            <button class="px-1 py-0.5 hover:bg-gray-100">
                                <i class="fas fa-plus h-3 w-3"></i>
                            </button>
                        </div>
                        <p class="font-medium">45.000đ</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="p-4 border-t">
            <div class="flex justify-between mb-2">
                <span>Tạm tính:</span>
                <span>322.000đ</span>
            </div>
            <div class="flex justify-between mb-4">
                <span>Phí giao hàng:</span>
                <span>Miễn phí</span>
            </div>
            <a href="/cart" class="block w-full bg-orange-500 hover:bg-orange-600 text-white text-center px-4 py-2 rounded-md font-medium transition-colors mb-2">
                Xem Giỏ Hàng
            </a>
            <a href="/checkout" class="block w-full border border-gray-300 hover:bg-gray-50 text-center px-4 py-2 rounded-md font-medium transition-colors">
                Thanh Toán
            </a>
        </div>
    </div>
</div>

<!-- Overlay -->
<div class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden" id="mini-cart-overlay"></div>

<!-- Floating cart button -->
<button class="fixed bottom-4 right-4 bg-orange-500 text-white p-3 rounded-full shadow-lg md:hidden z-30" id="floating-cart-button">
    <i class="fas fa-shopping-cart h-6 w-6"></i>
</button>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Cart functionality
        const decreaseButtons = document.querySelectorAll('.decrease-quantity');
        const increaseButtons = document.querySelectorAll('.increase-quantity');
        const removeButtons = document.querySelectorAll('.remove-item');
        const addSuggestedButtons = document.querySelectorAll('.add-suggested');
        const applyButton = document.getElementById('apply-coupon');
        const couponInput = document.getElementById('coupon-code');
        
        // Mini cart functionality
        const miniCart = document.getElementById('mini-cart');
        const miniCartOverlay = document.getElementById('mini-cart-overlay');
        const closeMiniCartButton = document.getElementById('close-mini-cart');
        const floatingCartButton = document.getElementById('floating-cart-button');
        
        // Update cart totals
        function updateCartTotals() {
            let subtotal = 0;
            const cartItems = document.querySelectorAll('.cart-item');
            
            cartItems.forEach(item => {
                const priceText = item.querySelector('.item-price').textContent;
                const price = parseInt(priceText.replace(/\D/g, ''));
                const quantity = parseInt(item.querySelector('.item-quantity').textContent);
                const total = price * quantity;
                
                item.querySelector('.item-total').textContent = total.toLocaleString() + 'đ';
                subtotal += total;
            });
            
            document.getElementById('subtotal').textContent = subtotal.toLocaleString() + 'đ';
            
            // Check if discount is applied
            const discountContainer = document.getElementById('discount-container');
            const discountText = document.getElementById('discount').textContent;
            let discount = 0;
            
            if (!discountContainer.classList.contains('hidden')) {
                discount = parseInt(discountText.replace(/\D/g, ''));
            }
            
            // Free shipping for orders over 100,000đ
            const shipping = subtotal > 100000 ? 0 : 15000;
            document.getElementById('shipping').textContent = shipping === 0 ? 'Miễn phí' : shipping.toLocaleString() + 'đ';
            
            // Calculate total
            const total = subtotal + shipping - discount;
            document.getElementById('total').textContent = total.toLocaleString() + 'đ';
        }
        
        // Decrease quantity
        decreaseButtons.forEach(button => {
            button.addEventListener('click', function() {
                const quantityElement = this.parentElement.querySelector('.item-quantity');
                let quantity = parseInt(quantityElement.textContent);
                
                if (quantity > 1) {
                    quantity--;
                    quantityElement.textContent = quantity;
                    updateCartTotals();
                }
            });
        });
        
        // Increase quantity
        increaseButtons.forEach(button => {
            button.addEventListener('click', function() {
                const quantityElement = this.parentElement.querySelector('.item-quantity');
                let quantity = parseInt(quantityElement.textContent);
                
                quantity++;
                quantityElement.textContent = quantity;
                updateCartTotals();
            });
        });
        
        // Remove item
        removeButtons.forEach(button => {
            button.addEventListener('click', function() {
                const cartItem = this.closest('.cart-item');
                cartItem.remove();
                updateCartTotals();
                
                // Show toast notification
                showToast('Sản phẩm đã được xóa khỏi giỏ hàng');
                
                // Check if cart is empty
                const remainingItems = document.querySelectorAll('.cart-item');
                if (remainingItems.length === 0) {
                    // Redirect to empty cart page or show empty cart message
                    // window.location.href = '/cart/empty';
                }
            });
        });
        
        // Add suggested product
        addSuggestedButtons.forEach(button => {
            button.addEventListener('click', function() {
                const productName = this.closest('.bg-white').querySelector('h3').textContent.trim();
                
                // Show toast notification
                showToast(`Đã thêm ${productName} vào giỏ hàng`);
                
                // In a real application, you would add the product to the cart
                // and update the cart UI
            });
        });
        
        // Apply coupon
        applyButton.addEventListener('click', function() {
            const couponCode = couponInput.value.trim();
            
            if (couponCode === '') {
                showToast('Vui lòng nhập mã giảm giá');
                return;
            }
            
            // Simulate coupon validation
            if (couponCode.toUpperCase() === 'FASTFOOD10') {
                // Calculate 10% discount
                const subtotalText = document.getElementById('subtotal').textContent;
                const subtotal = parseInt(subtotalText.replace(/\D/g, ''));
                const discount = Math.round(subtotal * 0.1);
                
                // Show discount in summary
                document.getElementById('discount-container').classList.remove('hidden');
                document.getElementById('discount').textContent = '-' + discount.toLocaleString() + 'đ';
                
                // Update total
                updateCartTotals();
                
                // Show success message
                showToast('Áp dụng mã giảm giá thành công');
            } else {
                showToast('Mã giảm giá không hợp lệ');
            }
        });
        
        // Toggle mini cart
        function toggleMiniCart() {
            if (miniCart.classList.contains('translate-x-full')) {
                miniCart.classList.remove('translate-x-full');
                miniCartOverlay.classList.remove('hidden');
            } else {
                miniCart.classList.add('translate-x-full');
                miniCartOverlay.classList.add('hidden');
            }
        }
        
        floatingCartButton.addEventListener('click', toggleMiniCart);
        closeMiniCartButton.addEventListener('click', toggleMiniCart);
        miniCartOverlay.addEventListener('click', toggleMiniCart);
        
        // Simple toast notification function
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
</script>
@endsection