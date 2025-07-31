document.addEventListener("DOMContentLoaded", () => {
    // Hàm ghi log vào file
    function logToFile(context, message) {
        const timestamp = new Date().toISOString();
        const logMessage = `[${timestamp}] [${context}] ${message}`;
        
        // Gửi log đến server để ghi vào file
        fetch('/log/debug', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.csrfToken,
            },
            body: JSON.stringify({
                context: context,
                message: message,
                timestamp: timestamp
            })
        }).catch(err => {
            // Fallback: ghi vào console nếu không gửi được
            console.log(logMessage);
        });
    }

    const productCards = document.querySelectorAll(".product-card");

    productCards.forEach((card) => {
        const productId = Number.parseInt(card.dataset.productId);

        // Click toàn bộ card => mở modal
        card.addEventListener("click", function () {
            openProductModal(productId);
        });

        // Tìm nút Add to Cart trong mỗi card
        const addToCartBtn = card.querySelector(".add-to-cart-btn");

        if (addToCartBtn) {
            addToCartBtn.addEventListener("click", function (e) {
                e.stopPropagation(); // Không cho click lan lên card

                // Kiểm tra xem có phải là combo không
                const comboId = addToCartBtn.dataset.comboId;
                if (comboId) {
                    // Xử lý thêm combo
                    addComboToCart(comboId, addToCartBtn);
                } else {
                    // Xử lý thêm sản phẩm
                    const productData = getSelectedProductData(card);
                    if (!productData) return;
                    addProductToCart(productData, addToCartBtn);
                }
            });
        }
    });

    // Hàm thêm sản phẩm vào giỏ hàng
    function addProductToCart(productData, button) {
        // Ghi log vào file
        logToFile("add-to-cart-direct", "Sending product data: " + JSON.stringify(productData));
        
        // Disable button while processing
        button.disabled = true;
        
        // Gửi request thêm vào giỏ hàng
        fetch("/cart/add", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": window.csrfToken,
            },
            body: JSON.stringify(productData),
        })
        .then((response) => response.json())
        .then((data) => {
            button.disabled = false;
            logToFile("add-to-cart-direct", "Server response: " + JSON.stringify(data));
            
            if (data.success) {
                // Cập nhật số lượng giỏ hàng trên header
                if (window.updateCartCount && data.cart_count !== undefined) {
                    window.updateCartCount(data.cart_count);
                }
                if (window.dtmodalShowToast) {
                    dtmodalShowToast("success", {
                        title: "Thành công",
                        message: data.message || "Đã thêm vào giỏ hàng!"
                    });
                }
            } else {
                if (window.dtmodalShowToast) {
                    dtmodalShowToast("error", {
                        title: "Lỗi",
                        message: data.message || "Có lỗi xảy ra khi thêm sản phẩm vào giỏ hàng"
                    });
                }
            }
        })
        .catch((error) => {
            button.disabled = false;
            logToFile("add-to-cart-direct", "Error: " + error.message);
            if (window.dtmodalShowToast) {
                dtmodalShowToast("error", {
                    title: "Lỗi",
                    message: "Có lỗi xảy ra khi thêm sản phẩm vào giỏ hàng"
                });
            }
        });
    }

    // Hàm thêm combo vào giỏ hàng
    function addComboToCart(comboId, button) {
        // Disable button while processing
        button.disabled = true;

        fetch("/cart/add-combo", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": window.csrfToken,
            },
            body: JSON.stringify({
                combo_id: comboId,
                quantity: 1
            }),
        })
            .then((response) => response.json())
            .then((data) => {
                button.disabled = false;
                if (data.success) {
                    if (window.updateCartCount && data.cart_count !== undefined) {
                        window.updateCartCount(data.cart_count);
                    }
                    if (window.dtmodalShowToast) {
                        dtmodalShowToast("success", {
                            title: "Thành công",
                            message: data.message || "Đã thêm combo vào giỏ hàng!",
                        });
                    }
                } else {
                    if (window.dtmodalShowToast) {
                        dtmodalShowToast("error", {
                            title: "Lỗi",
                            message: data.message || "Không thể thêm combo vào giỏ hàng",
                        });
                    }
                }
            })
            .catch((error) => {
                button.disabled = false;
                console.error("Add Combo to Cart Error:", error);
                if (window.dtmodalShowToast) {
                    dtmodalShowToast("error", {
                        title: "Lỗi",
                        message: "Có lỗi xảy ra khi thêm combo vào giỏ hàng",
                    });
                }
            });
    }

    // Hàm lấy dữ liệu sản phẩm giống như trong shop.js
    function getSelectedProductData(card) {
        // Lấy product_id từ card
        const productId = Number.parseInt(card.dataset.productId);
        
        // Debug log
        logToFile("add-to-cart-direct", "Card data: " + JSON.stringify({
            productId: productId,
            variantIdRaw: card.dataset.variantId,
            hasStock: card.dataset.hasStock
        }));
        
        // Lấy branch_id từ nhiều nguồn khác nhau
        let branchId = null;
        
        // Thử lấy từ window.selectedBranchId (nếu có)
        if (window.selectedBranchId) {
            branchId = window.selectedBranchId;
        }
        // Thử lấy từ meta tag
        else if (document.querySelector('meta[name="selected-branch"]')) {
            branchId = document.querySelector('meta[name="selected-branch"]').content;
        }
        // Thử lấy từ URL parameter
        else {
            const urlParams = new URLSearchParams(window.location.search);
            branchId = urlParams.get('branch_id');
        }

        if (!branchId) {
            logToFile("add-to-cart-direct", "No branch_id found");
            if (window.dtmodalShowToast) {
                dtmodalShowToast("warning", {
                    title: "Chọn chi nhánh",
                    message: "Vui lòng chọn chi nhánh trước khi thêm vào giỏ hàng"
                });
            }
            return null;
        }

        // Tạo object data cơ bản
        const productData = {
            product_id: productId,
            branch_id: branchId,
            quantity: 1,
            toppings: []
        };

        // Trên trang home không có radio button variant, để server tự chọn variant mặc định
        // Không gửi variant_values để server tự xử lý

        return productData;
    }
});
