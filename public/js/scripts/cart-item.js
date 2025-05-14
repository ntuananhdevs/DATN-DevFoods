document.addEventListener('DOMContentLoaded', function() {
    // Xử lý nút tăng/giảm số lượng
    const minusBtns = document.querySelectorAll('.minus-btn');
    const plusBtns = document.querySelectorAll('.plus-btn');
    const quantityInputs = document.querySelectorAll('.quantity-input');
    const removeBtns = document.querySelectorAll('.remove-btn');
    const updateCartBtn = document.querySelector('.update-cart-btn');
    
    // Xử lý giảm số lượng
    minusBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const cartKey = this.getAttribute('data-cart-key');
            const input = document.querySelector(`.quantity-input[data-cart-key="${cartKey}"]`);
            let value = parseInt(input.value);
            if (value > 1) {
                input.value = value - 1;
            }
        });
    });
    
    // Xử lý tăng số lượng
    plusBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const cartKey = this.getAttribute('data-cart-key');
            const input = document.querySelector(`.quantity-input[data-cart-key="${cartKey}"]`);
            let value = parseInt(input.value);
            if (value < 99) {
                input.value = value + 1;
            }
        });
    });
    
    // Xử lý cập nhật giỏ hàng
    if (updateCartBtn) {
        updateCartBtn.addEventListener('click', function() {
            const updates = [];
            
            quantityInputs.forEach(input => {
                const cartKey = input.getAttribute('data-cart-key');
                const quantity = parseInt(input.value);
                
                updates.push({
                    cart_key: cartKey,
                    quantity: quantity
                });
            });
            
            // Gửi request AJAX để cập nhật giỏ hàng
            fetch('/cart/update', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ updates: updates })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Reload trang để cập nhật thông tin
                    window.location.reload();
                } else {
                    alert(data.message || 'Có lỗi xảy ra khi cập nhật giỏ hàng');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Có lỗi xảy ra khi cập nhật giỏ hàng');
            });
        });
    }
    
    // Xử lý xóa sản phẩm
    removeBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const cartKey = this.getAttribute('data-cart-key');
            
            if (confirm('Bạn có chắc chắn muốn xóa sản phẩm này khỏi giỏ hàng?')) {
                // Gửi request AJAX để xóa sản phẩm
                fetch('/cart/remove', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ cart_key: cartKey })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Xóa phần tử khỏi DOM
                        const cartItem = document.querySelector(`.cart-item[data-cart-key="${cartKey}"]`);
                        if (cartItem) {
                            cartItem.remove();
                        }
                        
                        // Nếu không còn sản phẩm nào, hiển thị giỏ hàng trống
                        if (document.querySelectorAll('.cart-item').length === 0) {
                            const cartItems = document.querySelector('.cart-items');
                            cartItems.innerHTML = `
                                <div class="empty-cart">
                                    <div class="empty-cart-icon">
                                        <i class="fas fa-shopping-cart"></i>
                                    </div>
                                    <h3>Giỏ hàng của bạn đang trống</h3>
                                    <p>Hãy thêm sản phẩm vào giỏ hàng để tiếp tục mua sắm</p>
                                    <a href="/shop/product-list" class="btn btn-primary">Tiếp tục mua sắm</a>
                                </div>
                            `;
                        }
                        
                        // Cập nhật tổng tiền
                        window.location.reload();
                    } else {
                        alert(data.message || 'Có lỗi xảy ra khi xóa sản phẩm');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Có lỗi xảy ra khi xóa sản phẩm');
                });
            }
        });
    });
});