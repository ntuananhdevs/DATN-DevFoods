// Thêm vào giỏ hàng cho combo
const addToCartBtn = document.getElementById('add-to-cart-combo');
const buyNowBtn = document.getElementById('buy-now-combo');
if (addToCartBtn) {
    addToCartBtn.addEventListener('click', function() {
        const comboId = this.getAttribute('data-combo-id');
        const quantity = parseInt(document.getElementById('quantity').textContent) || 1;
        addToCartBtn.disabled = true;
        fetch('/cart/add-combo', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.csrfToken
            },
            body: JSON.stringify({ combo_id: comboId, quantity: quantity })
        })
        .then(res => res.json())
        .then(data => {
            addToCartBtn.disabled = false;
            if (data.success) {
                if (window.dtmodalShowToast) {
                    dtmodalShowToast('success', { title: 'Thành công', message: data.message });
                } else {
                    alert('Thêm combo thành công!');
                }
                // Cập nhật số lượng giỏ hàng nếu có
                const cartCounter = document.getElementById('cart-counter');
                if (cartCounter && data.cart_count !== undefined) cartCounter.textContent = data.cart_count;
            } else {
                if (window.dtmodalShowToast) {
                    dtmodalShowToast('error', { title: 'Lỗi', message: data.message || 'Có lỗi xảy ra' });
                } else {
                    alert(data.message || 'Có lỗi xảy ra');
                }
            }
        })
        .catch(() => {
            addToCartBtn.disabled = false;
            if (window.dtmodalShowToast) {
                dtmodalShowToast('error', { title: 'Lỗi', message: 'Có lỗi khi thêm combo vào giỏ hàng' });
            } else {
                alert('Có lỗi khi thêm combo vào giỏ hàng');
            }
        });
    });
}
if (buyNowBtn) {
    buyNowBtn.addEventListener('click', function() {
        const comboId = this.getAttribute('data-combo-id');
        const quantity = parseInt(document.getElementById('quantity').textContent) || 1;
        buyNowBtn.disabled = true;
        fetch('/cart/add-combo', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.csrfToken
            },
            body: JSON.stringify({ combo_id: comboId, quantity: quantity, buy_now: true })
        })
        .then(res => res.json())
        .then(data => {
            buyNowBtn.disabled = false;
            if (data.success) {
                window.location.href = '/checkout';
            } else {
                if (window.dtmodalShowToast) {
                    dtmodalShowToast('error', { title: 'Lỗi', message: data.message || 'Có lỗi xảy ra' });
                } else {
                    alert(data.message || 'Có lỗi xảy ra');
                }
            }
        })
        .catch(() => {
            buyNowBtn.disabled = false;
            if (window.dtmodalShowToast) {
                dtmodalShowToast('error', { title: 'Lỗi', message: 'Có lỗi khi mua combo' });
            } else {
                alert('Có lỗi khi mua combo');
            }
        });
    });
}

// ==== ĐÁNH GIÁ, BÌNH LUẬN, PHẢN HỒI, HỮU ÍCH, BÁO CÁO, REALTIME (copy từ shop.js, chỉnh cho combo) ====

document.addEventListener('DOMContentLoaded', function() {
    // XỬ LÝ CHỌN SỐ SAO NGUYÊN (1-5) VÀ MODAL THÔNG BÁO
    const reviewForm = document.querySelector('form[action*="/review"]');
    if (reviewForm) {
        let selectedRating = 0;
        const ratingInputs = reviewForm.querySelectorAll('input[name="rating"]');
        const ratingLabels = reviewForm.querySelectorAll('label[for^="star"]');
        function updateStarDisplay(rating) {
            ratingLabels.forEach((label, idx) => {
                if (idx < rating) {
                    label.classList.add('text-yellow-400');
                } else {
                    label.classList.remove('text-yellow-400');
                }
            });
        }
        ratingInputs.forEach(input => { input.checked = false; });
        updateStarDisplay(0);
        ratingLabels.forEach((label, idx) => {
            label.addEventListener('click', function(e) {
                if (selectedRating === idx + 1) {
                    selectedRating = 0;
                    ratingInputs.forEach((input, i) => { input.checked = false; });
                    updateStarDisplay(0);
                } else {
                    selectedRating = idx + 1;
                    ratingInputs.forEach((input, i) => { input.checked = (i === idx); });
                    updateStarDisplay(selectedRating);
                }
            });
        });
        reviewForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(reviewForm);
            
            // Thêm type cho combo review
            if (!formData.get('type')) {
                formData.append('type', 'combo');
            }
            
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const submitBtn = reviewForm.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            submitBtn.textContent = 'Đang gửi...';
            fetch(reviewForm.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: formData
            })
            .then(async response => {
                submitBtn.disabled = false;
                submitBtn.textContent = submitBtn.getAttribute('data-default-text') || 'Gửi đánh giá';
                if (response.ok) {
                    dtmodalShowToast('success', { title: 'Thành công', message: 'Đánh giá của bạn đã được gửi!' });
                    setTimeout(() => {
                        const reviewForm = document.getElementById('review-reply-form');
                        const replyReviewIdInput = document.getElementById('reply_review_id');
                        const replyingToDiv = document.getElementById('replying-to');
                        const reviewTextarea = document.getElementById('review-textarea');
                        const reviewSubmitBtn = document.getElementById('review-submit-btn');
                        const formTitle = document.getElementById('form-title');
                        const ratingRow = document.getElementById('rating-row');
                        if (reviewForm) {
                            reviewForm.setAttribute('action', reviewForm.getAttribute('data-default-action') || '/products/review');
                            // Đảm bảo type combo được giữ lại
                            const typeInput = reviewForm.querySelector('input[name="type"]');
                            if (!typeInput) {
                                const newTypeInput = document.createElement('input');
                                newTypeInput.type = 'hidden';
                                newTypeInput.name = 'type';
                                newTypeInput.value = 'combo';
                                reviewForm.appendChild(newTypeInput);
                            }
                        }
                        if (replyReviewIdInput) replyReviewIdInput.value = '';
                        if (replyingToDiv) replyingToDiv.classList.add('hidden');
                        if (reviewTextarea) {
                            reviewTextarea.value = '';
                            reviewTextarea.placeholder = reviewTextarea.getAttribute('data-default-placeholder') || 'Chia sẻ cảm nhận của bạn...';
                            reviewTextarea.setAttribute('name', 'review');
                        }
                        if (reviewSubmitBtn) reviewSubmitBtn.textContent = reviewSubmitBtn.getAttribute('data-default-text') || 'Gửi đánh giá';
                        if (formTitle) formTitle.textContent = formTitle.getAttribute('data-default-title') || 'Gửi đánh giá của bạn';
                        if (ratingRow) ratingRow.style.display = '';
                        if (typeof selectedRating !== 'undefined') selectedRating = 0;
                        if (typeof updateStarDisplay === 'function') updateStarDisplay(0);
                        const preview = document.getElementById('preview_image');
                        if (preview) { preview.src = '#'; preview.classList.add('hidden'); }
                        const isAnonymous = document.getElementById('is_anonymous');
                        if (isAnonymous) isAnonymous.checked = false;
                        const ratingInputs = reviewForm.querySelectorAll('input[name="rating"]');
                        ratingInputs.forEach(input => { input.checked = false; });
                    }, 200);
                } else {
                    let errorMsg = 'Có lỗi xảy ra khi gửi đánh giá!';
                    try {
                        const data = await response.json();
                        if (data && data.errors) {
                            errorMsg = Object.values(data.errors).join('\n');
                        } else if (data && data.message) {
                            errorMsg = data.message;
                        }
                    } catch {}
                    dtmodalShowToast('error', { title: 'Lỗi', message: errorMsg });
                }
                return;
            })
            .catch(() => {
                submitBtn.disabled = false;
                submitBtn.textContent = submitBtn.getAttribute('data-default-text') || 'Gửi đánh giá';
                dtmodalShowToast('error', { title: 'Lỗi', message: 'Có lỗi xảy ra khi gửi đánh giá!' });
            });
        });
    }

    // XỬ LÝ XÓA BÌNH LUẬN (REVIEW)
    document.querySelectorAll('.delete-review-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const reviewId = this.getAttribute('data-review-id');
            dtmodalCreateModal({
                type: 'warning',
                title: 'Xác nhận xóa',
                message: 'Bạn có chắc chắn muốn xóa bình luận này?',
                confirmText: 'Xóa',
                cancelText: 'Hủy',
                onConfirm: function() {
                    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    fetch(`/reviews/${reviewId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                        },
                    })
                    .then(async res => {
                        if (res.ok) {
                            dtmodalShowToast('success', { title: 'Thành công', message: 'Đã xóa bình luận!' });
                            const reviewDiv = btn.closest('.p-6');
                            if (reviewDiv) reviewDiv.remove();
                        } else {
                            let msg = 'Không thể xóa bình luận!';
                            try {
                                const data = await res.json();
                                if (data && data.message) msg = data.message;
                            } catch {}
                            dtmodalShowToast('error', { title: 'Lỗi', message: msg });
                        }
                    })
                    .catch(() => {
                        dtmodalShowToast('error', { title: 'Lỗi', message: 'Lỗi mạng hoặc server!' });
                    });
                }
            });
        });
    });

    // Preview image functionality
    const input = document.getElementById('review_image');
    const preview = document.getElementById('preview_image');
    if (input && preview) {
        input.addEventListener('change', function(e) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(ev) {
                    preview.src = ev.target.result;
                    preview.classList.remove('hidden');
                }
                reader.readAsDataURL(input.files[0]);
            } else {
                preview.src = '#';
                preview.classList.add('hidden');
            }
        });
    }

    // HỮU ÍCH (AJAX + realtime)
    document.querySelectorAll('.helpful-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const reviewId = this.getAttribute('data-review-id');
            const countSpan = this.querySelector('.helpful-count');
            const button = this;
            const icon = button.querySelector('i');
            const isHelpful = button.getAttribute('data-helpful') === '1';
            if (!isHelpful) {
                fetch(`/reviews/${reviewId}/helpful`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': window.csrfToken,
                        'Accept': 'application/json',
                    },
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        countSpan.textContent = data.helpful_count;
                        button.classList.add('helpful-active', 'text-sky-600');
                        icon.classList.add('text-sky-600');
                        icon.classList.remove('far');
                        icon.classList.add('fas');
                        button.setAttribute('data-helpful', '1');
                    }
                });
            } else {
                fetch(`/reviews/${reviewId}/helpful`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': window.csrfToken,
                        'Accept': 'application/json',
                    },
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        countSpan.textContent = data.helpful_count;
                        button.classList.remove('helpful-active', 'text-sky-600');
                        icon.classList.remove('text-sky-600');
                        icon.classList.remove('fas');
                        icon.classList.add('far');
                        button.setAttribute('data-helpful', '0');
                    }
                });
            }
        });
    });

    // === FAVORITE (WISHLIST) LOGIC FOR COMBO ===
    document.querySelectorAll('.favorite-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            // Nếu là nút login-prompt-btn thì show popup đăng nhập
            if (btn.classList.contains('login-prompt-btn')) {
                if (document.getElementById('login-popup')) {
                    document.getElementById('login-popup').classList.remove('hidden');
                }
                return;
            }
            // Đã đăng nhập
            const comboId = btn.getAttribute('data-combo-id');
            const icon = btn.querySelector('i');
            const isFavorite = icon.classList.contains('fas') && icon.classList.contains('text-red-500');
            // Optimistic UI
            if (isFavorite) {
                icon.classList.remove('fas', 'text-red-500');
                icon.classList.add('far');
            } else {
                icon.classList.remove('far');
                icon.classList.add('fas', 'text-red-500');
            }
            // Gửi AJAX
            fetch('/wishlist', {
                method: isFavorite ? 'DELETE' : 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': window.csrfToken
                },
                body: JSON.stringify({ combo_id: comboId })
            })
            .then(res => res.json())
            .then(data => {
                if (data && data.message) {
                    if (window.dtmodalShowToast) {
                        dtmodalShowToast(isFavorite ? 'info' : 'success', {
                            title: isFavorite ? 'Thông báo' : 'Thành công',
                            message: data.message
                        });
                    }
                } else {
                    // Nếu lỗi, revert lại UI
                    if (isFavorite) {
                        icon.classList.remove('far');
                        icon.classList.add('fas', 'text-red-500');
                    } else {
                        icon.classList.remove('fas', 'text-red-500');
                        icon.classList.add('far');
                    }
                    if (window.dtmodalShowToast) {
                        dtmodalShowToast('error', {
                            title: 'Lỗi',
                            message: 'Có lỗi khi cập nhật yêu thích'
                        });
                    }
                }
            })
            .catch(() => {
                // Nếu lỗi, revert lại UI
                if (isFavorite) {
                    icon.classList.remove('far');
                    icon.classList.add('fas', 'text-red-500');
                } else {
                    icon.classList.remove('fas', 'text-red-500');
                    icon.classList.add('far');
                }
                if (window.dtmodalShowToast) {
                    dtmodalShowToast('error', {
                        title: 'Lỗi',
                        message: 'Có lỗi khi cập nhật yêu thích'
                    });
                }
            });
        });
    });
    // === REPLY REVIEW UX ===
    const replyForm = document.getElementById('review-reply-form');
    const replyReviewIdInput = document.getElementById('reply_review_id');
    const replyingToDiv = document.getElementById('replying-to');
    const replyingToUser = document.getElementById('replying-to-user');
    const cancelReplyBtn = document.getElementById('cancel-reply');
    const reviewTextarea = document.getElementById('review-textarea');
    const reviewSubmitBtn = document.getElementById('review-submit-btn');
    const formTitle = document.getElementById('form-title');
    const ratingRow = document.getElementById('rating-row');
    
    // Lưu mặc định ngay khi DOM load
    const defaultAction = replyForm ? replyForm.getAttribute('action') : '';
    const defaultPlaceholder = reviewTextarea ? reviewTextarea.getAttribute('placeholder') : '';
    const defaultBtnText = reviewSubmitBtn ? reviewSubmitBtn.textContent : '';
    const defaultTitle = formTitle ? formTitle.textContent : '';
    
    // Gắn sự kiện cho nút reply ở mỗi review
    document.querySelectorAll('.reply-review-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const reviewId = this.getAttribute('data-review-id');
            const userName = this.getAttribute('data-user-name');
            const routeReply = this.getAttribute('data-route-reply');
            console.log('[Reply] Clicked reply for reviewId:', reviewId, 'userName:', userName, 'routeReply:', routeReply);
            if (replyForm) {
                replyForm.setAttribute('action', routeReply);
                replyReviewIdInput.value = reviewId;
                replyingToUser.textContent = userName;
                replyingToDiv.classList.remove('hidden');
                reviewTextarea.placeholder = `Phản hồi cho ${userName}...`;
                reviewSubmitBtn.textContent = 'Gửi phản hồi';
                formTitle.textContent = 'Gửi phản hồi';
                reviewTextarea.focus();
                if (ratingRow) ratingRow.style.display = 'none';
                console.log('[Reply] Form action set to:', replyForm.getAttribute('action'));
            }
            reviewTextarea.setAttribute('name', 'reply');
        });
    });
    
    // Hủy reply, trở lại form đánh giá
    if (cancelReplyBtn) {
        cancelReplyBtn.addEventListener('click', function() {
            if (replyForm) {
                replyForm.setAttribute('action', defaultAction);
                replyReviewIdInput.value = '';
                replyingToDiv.classList.add('hidden');
                reviewTextarea.placeholder = defaultPlaceholder;
                reviewSubmitBtn.textContent = defaultBtnText;
                formTitle.textContent = defaultTitle;
                if (ratingRow) ratingRow.style.display = '';
                console.log('[Reply] Cancel reply, form action reset to:', replyForm.getAttribute('action'));
            }
            reviewTextarea.setAttribute('name', 'review');
        });
    }

    // === XÓA REPLY (fetch API) ===
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('delete-reply-btn')) {
            // Thay confirm bằng modal xác nhận đẹp
            dtmodalCreateModal({
                type: 'warning',
                title: 'Xác nhận xóa',
                message: 'Bạn có chắc chắn muốn xóa phản hồi này không?',
                confirmText: 'Xóa',
                cancelText: 'Hủy',
                onConfirm: function() {
                    const btn = e.target;
                    const replyId = btn.getAttribute('data-reply-id');
                    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    fetch('/review-replies/' + replyId, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                        },
                        credentials: 'same-origin'
                    })
                    .then(async res => {
                        if (res.ok) {
                            let data = await res.json();
                            if (window.dtmodalShowToast) {
                                dtmodalShowToast('success', { title: 'Thành công', message: data.message });
                            } else {
                                alert(data.message);
                            }
                            btn.closest('.reply-item').remove();
                        } else {
                            let msg = 'Đã xảy ra lỗi!';
                            try {
                                const data = await res.json();
                                if (data && data.message) msg = data.message;
                            } catch {}
                            if (window.dtmodalShowToast) {
                                dtmodalShowToast('error', { title: 'Lỗi', message: msg });
                            } else {
                                alert(msg);
                            }
                        }
                    })
                    .catch(() => {
                        if (window.dtmodalShowToast) {
                            dtmodalShowToast('error', { title: 'Lỗi', message: 'Lỗi mạng hoặc server!' });
                        } else {
                            alert('Lỗi mạng hoặc server!');
                        }
                    });
                }
            });
            return;
        }
    });

    // === Realtime reply (Pusher) ===
    if (window.pusherKey && window.pusherCluster) {
        const reviewRepliesPusher = new Pusher(window.pusherKey, {
            cluster: window.pusherCluster,
            encrypted: true,
            enabledTransports: ['ws', 'wss']
        });
        const reviewRepliesChannel = reviewRepliesPusher.subscribe('review-replies');
        
        reviewRepliesChannel.bind('review-reply-created', function(data) {
            console.log('[Realtime] Nhận reply mới:', data);
            const reviewBlock = document.querySelector(`[data-review-id="${data.review_id}"]`);
            if (!reviewBlock) return;
            
            // Kiểm tra quyền xóa
            const canDelete = (window.currentUserId && (window.currentUserId == data.user_id || window.isAdmin === true));
            const deleteBtnHtml = canDelete
                ? `<button class="inline-flex items-center gap-1 text-xs text-red-500 hover:text-red-700 transition-colors delete-reply-btn" data-reply-id="${data.reply_id}">
                        <i class="fas fa-trash-alt"></i> Xóa
                   </button>`
                : '';
            
            // Tạo HTML cho reply mới
            const replyHtml = `
                <div class="reply-item flex items-start gap-2 ml-8 mt-2 relative" data-reply-id="${data.reply_id}">
                    <div class="reply-arrow">
                        <svg width="24" height="24" viewBox="0 0 24 24" class="text-blue-400"><path d="M2 12h16M18 12l-4-4m4 4l-4 4" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </div>
                    <div class="bg-blue-50 border border-blue-200 rounded-lg px-4 py-2 flex-1">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="font-semibold text-blue-700">${data.user_name || 'Ẩn danh'}</span>
                            <span class="text-xs text-gray-400">${formatDate(data.reply_date)}</span>
                            ${deleteBtnHtml}
                        </div>
                        <div class="text-gray-700">${data.reply_content}</div>
                    </div>
                </div>
            `;
            
            // Chèn vào cuối danh sách reply của review này
            let lastReply = null;
            let sibling = reviewBlock.nextElementSibling;
            while (sibling && sibling.classList.contains('reply-item')) {
                lastReply = sibling;
                sibling = sibling.nextElementSibling;
            }
            
            let newReplyElem;
            if (lastReply) {
                lastReply.insertAdjacentHTML('afterend', replyHtml);
                newReplyElem = lastReply.nextElementSibling;
            } else {
                // Nếu chưa có reply nào, chèn ngay sau reviewBlock
                reviewBlock.insertAdjacentHTML('afterend', replyHtml);
                newReplyElem = reviewBlock.nextElementSibling;
            }
            
            // Scroll tới reply mới
            if (newReplyElem) {
                newReplyElem.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        });

        // Listen for review-reply-deleted event
        reviewRepliesChannel.bind('review-reply-deleted', function(data) {
            console.log('[Realtime] Xóa reply:', data);
            // Tìm reply-item theo data-reply-id
            const replyElem = document.querySelector(`.reply-item[data-reply-id="${data.reply_id}"]`);
            if (replyElem) {
                replyElem.remove();
            }
        });
    }

    // Format date helper
    function formatDate(dateStr) {
        const d = new Date(dateStr);
        if (isNaN(d)) return dateStr;
        const pad = n => n < 10 ? '0' + n : n;
        return `${pad(d.getDate())}/${pad(d.getMonth()+1)}/${d.getFullYear()} ${pad(d.getHours())}:${pad(d.getMinutes())}`;
    }
});
