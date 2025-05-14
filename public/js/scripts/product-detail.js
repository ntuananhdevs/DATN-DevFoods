document.addEventListener('DOMContentLoaded', function() {
    // Khởi tạo Swiper cho gallery
    var thumbnailSwiper = new Swiper('.thumbnail-swiper', {
        slidesPerView: 4,
        spaceBetween: 10,
        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
        },
    });
    
    // Xử lý chọn thumbnail
    const thumbnails = document.querySelectorAll('.thumbnail');
    const mainImage = document.getElementById('main-product-image');
    
    thumbnails.forEach(thumbnail => {
        thumbnail.addEventListener('click', function() {
            // Xóa class active từ tất cả thumbnails
            thumbnails.forEach(t => t.classList.remove('active'));
            // Thêm class active vào thumbnail được chọn
            this.classList.add('active');
            // Cập nhật ảnh chính
            mainImage.src = this.getAttribute('data-img');
        });
    });
    
    // Xử lý nút tăng/giảm số lượng
    const minusBtn = document.querySelector('.minus-btn');
    const plusBtn = document.querySelector('.plus-btn');
    const quantityInput = document.querySelector('.quantity-input');
    
    minusBtn.addEventListener('click', function() {
        let value = parseInt(quantityInput.value);
        if (value > 1) {
            quantityInput.value = value - 1;
        }
    });
    
    plusBtn.addEventListener('click', function() {
        let value = parseInt(quantityInput.value);
        if (value < 99) {
            quantityInput.value = value + 1;
        }
    });
    
    // Xử lý chọn biến thể
    const optionItems = document.querySelectorAll('.option-item');
    let selectedAttributes = {};
    let selectedVariant = null;
    
    // Lấy dữ liệu biến thể từ PHP
    const variantsData = @json($variantsData);
    
    // Khởi tạo giá trị mặc định cho selectedAttributes
    optionItems.forEach(item => {
        if (item.classList.contains('active')) {
            const attributeId = item.getAttribute('data-attribute-id');
            const valueId = item.getAttribute('data-value-id');
            selectedAttributes[attributeId] = valueId;
        }
    });
    
    // Tìm biến thể phù hợp với các thuộc tính đã chọn
    function findMatchingVariant() {
        for (const variantId in variantsData) {
            const variant = variantsData[variantId];
            let isMatch = true;
            
            for (const attributeId in selectedAttributes) {
                if (variant.attributes[attributeId] != selectedAttributes[attributeId]) {
                    isMatch = false;
                    break;
                }
            }
            
            if (isMatch) {
                return variant;
            }
        }
        
        return null;
    }
    
    // Cập nhật thông tin biến thể đã chọn
    function updateSelectedVariantInfo() {
        selectedVariant = findMatchingVariant();
        const variantInfoElement = document.querySelector('.selected-variant-info');
        const priceElement = document.getElementById('product-price');
        const stockStatusElement = document.getElementById('stock-status');
        
        if (selectedVariant) {
            // Cập nhật thông tin biến thể
            variantInfoElement.textContent = selectedVariant.name;
            
            // Cập nhật giá
            priceElement.textContent = new Intl.NumberFormat('vi-VN').format(selectedVariant.price) + ' ₫';
            
            // Cập nhật trạng thái tồn kho
            if (selectedVariant.stock_quantity > 0) {
                stockStatusElement.textContent = 'Còn hàng';
                stockStatusElement.className = 'stock-status in-stock';
            } else {
                stockStatusElement.textContent = 'Hết hàng';
                stockStatusElement.className = 'stock-status out-of-stock';
            }
            
            // Cập nhật ảnh nếu có
            if (selectedVariant.image) {
                mainImage.src = selectedVariant.image;
            }
        } else {
            variantInfoElement.textContent = 'Không tìm thấy biến thể phù hợp';
        }
    }
    
    // Xử lý sự kiện khi chọn thuộc tính
    optionItems.forEach(item => {
        item.addEventListener('click', function() {
            const attributeId = this.getAttribute('data-attribute-id');
            const valueId = this.getAttribute('data-value-id');
            
            // Xóa class active từ tất cả các option cùng nhóm
            document.querySelectorAll(`.option-item[data-attribute-id="${attributeId}"]`).forEach(option => {
                option.classList.remove('active');
            });
            
            // Thêm class active vào option được chọn
            this.classList.add('active');
            
            // Cập nhật thuộc tính đã chọn
            selectedAttributes[attributeId] = valueId;
            
            // Cập nhật thông tin biến thể
            updateSelectedVariantInfo();
        });
    });
    
    // Khởi tạo thông tin biến thể ban đầu
    updateSelectedVariantInfo();
    
    // Xử lý thêm vào giỏ hàng
    const addToCartBtn = document.getElementById('add-to-cart-btn');
    
    addToCartBtn.addEventListener('click', function() {
        const productId = {{ $product->id }};
        const quantity = parseInt(document.getElementById('product-quantity').value);
        
        // Dữ liệu gửi đi
        const data = {
            product_id: productId,
            quantity: quantity,
            _token: '{{ csrf_token() }}'
        };
        
        // Nếu có biến thể được chọn
        if (selectedVariant) {
            data.variant_id = selectedVariant.id;
            data.attributes = selectedAttributes;
        }
        
        // Gửi request AJAX để thêm vào giỏ hàng
        fetch('{{ route("customer.cart.add") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Hiển thị thông báo thành công
                alert(data.message);
                
                // Cập nhật số lượng sản phẩm trong giỏ hàng (nếu có hiển thị)
                const cartCountElement = document.querySelector('.cart-count');
                if (cartCountElement && data.cart_count) {
                    cartCountElement.textContent = data.cart_count;
                }
            } else {
                alert(data.message || 'Có lỗi xảy ra khi thêm vào giỏ hàng');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Có lỗi xảy ra khi thêm vào giỏ hàng');
        });
    });
});