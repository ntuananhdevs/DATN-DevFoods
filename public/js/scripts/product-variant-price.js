/**
 * Xử lý giá tiền khi chọn biến thể sản phẩm
 */
document.addEventListener('DOMContentLoaded', function() {
    // Lấy tất cả các nút chọn biến thể
    const variantSelectors = document.querySelectorAll('.variant-selector');
    
    // Lưu trữ dữ liệu biến thể
    let variantData = {};
    
    // Khởi tạo dữ liệu biến thể từ thuộc tính data
    if (document.getElementById('product-variants-data')) {
        try {
            variantData = JSON.parse(document.getElementById('product-variants-data').textContent);
        } catch (e) {
            console.error('Lỗi khi phân tích dữ liệu biến thể:', e);
        }
    }
    
    // Lắng nghe sự kiện thay đổi biến thể
    variantSelectors.forEach(selector => {
        selector.addEventListener('change', updateProductPrice);
    });
    
    // Cập nhật giá sản phẩm dựa trên biến thể được chọn
    function updateProductPrice() {
        // Lấy tất cả các giá trị biến thể đã chọn
        const selectedVariants = {};
        variantSelectors.forEach(selector => {
            if (selector.tagName === 'SELECT') {
                selectedVariants[selector.getAttribute('data-attribute')] = selector.value;
            } else {
                // Xử lý cho radio buttons hoặc checkboxes
                const checkedInput = document.querySelector(`input[name="${selector.getAttribute('name')}"]:checked`);
                if (checkedInput) {
                    selectedVariants[selector.getAttribute('data-attribute')] = checkedInput.value;
                }
            }
        });
        
        // Tìm biến thể phù hợp với lựa chọn
        let selectedVariantId = null;
        let selectedVariantPrice = null;
        
        // Kiểm tra từng biến thể trong dữ liệu
        for (const variantId in variantData) {
            const variant = variantData[variantId];
            let isMatch = true;
            
            // Kiểm tra xem tất cả các thuộc tính của biến thể có khớp với lựa chọn không
            for (const attribute in selectedVariants) {
                if (variant.attributes[attribute] !== selectedVariants[attribute]) {
                    isMatch = false;
                    break;
                }
            }
            
            if (isMatch) {
                selectedVariantId = variantId;
                selectedVariantPrice = variant.price;
                break;
            }
        }
        
        // Cập nhật giá hiển thị
        if (selectedVariantPrice !== null) {
            // Cập nhật giá hiển thị
            const priceElement = document.getElementById('product-price');
            if (priceElement) {
                // Định dạng giá tiền theo VND
                priceElement.textContent = new Intl.NumberFormat('vi-VN', {
                    style: 'currency',
                    currency: 'VND'
                }).format(selectedVariantPrice);
            }
            
            // Cập nhật ID biến thể trong form
            const variantIdInput = document.getElementById('variant-id-input');
            if (variantIdInput) {
                variantIdInput.value = selectedVariantId;
            }
        }
    }
    
    // Khởi tạo giá ban đầu
    updateProductPrice();
});