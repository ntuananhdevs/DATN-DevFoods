// Branch Show Page JavaScript
document.addEventListener('DOMContentLoaded', () => {
    // Modal handling
    const toggleModal = (modal, show) => {
        modal.classList.toggle('show', show);
        modal.style.display = show ? 'flex' : 'none';
        document.body.style.overflow = show ? 'hidden' : 'auto';
    };

    const uploadModal = document.getElementById('uploadImagesModal');

    // Open upload modal
    ['uploadImagesBtn', 'emptyStateUploadBtn'].forEach(id => {
        const btn = document.getElementById(id);
        if (btn) {
            btn.addEventListener('click', () => toggleModal(uploadModal, true));
        }
    });

    // Close upload modal
    ['closeUploadModal', 'cancelUploadBtn'].forEach(id => {
        const btn = document.getElementById(id);
        if (btn) {
            btn.addEventListener('click', () => {
                toggleModal(uploadModal, false);
                document.getElementById('branchImages').value = '';
                document.getElementById('imagePreviewGrid').innerHTML = '';
                document.querySelector('.image-preview-container').classList.add('hidden');
            });
        }
    });

    // Close modals on backdrop click
    window.addEventListener('click', e => {
        if (e.target.classList.contains('modal-backdrop')) {
            toggleModal(uploadModal, false);
            document.getElementById('branchImages').value = '';
            document.getElementById('imagePreviewGrid').innerHTML = '';
            document.querySelector('.image-preview-container').classList.add('hidden');
        }
    });

    // Image upload preview
    const imageInput = document.getElementById('branchImages');
    const previewContainer = document.querySelector('.image-preview-container');
    const previewGrid = document.getElementById('imagePreviewGrid');
    if (imageInput) {
        imageInput.addEventListener('change', () => {
            previewGrid.innerHTML = '';
            const files = imageInput.files;
            if (files.length) {
                previewContainer.classList.remove('hidden');
                Array.from(files).forEach((file, idx) => {
                    if (!file.type.match('image.*')) return;
                    const reader = new FileReader();
                    reader.onload = e => {
                        const item = document.createElement('div');
                        item.className = 'preview-item';
                        item.innerHTML = `<img src="${e.target.result}" alt="Preview" class="preview-img"><button class="preview-remove"><i class="fas fa-times"></i></button>`;
                        const removeBtn = item.querySelector('.preview-remove');
                        removeBtn.addEventListener('click', () => {
                            item.remove();
                            const dt = new DataTransfer();
                            Array.from(files).forEach((f, i) => {
                                if (i !== idx) dt.items.add(f);
                            });
                            imageInput.files = dt.files;
                            if (!previewGrid.children.length) {
                                previewContainer.classList.add('hidden');
                            }
                        });
                        previewGrid.appendChild(item);
                    };
                    reader.readAsDataURL(file);
                });
            } else {
                previewContainer.classList.add('hidden');
            }
        });
    }

    // Clear preview button
    const clearPreviewBtn = document.querySelector('.clear-preview');
    if (clearPreviewBtn) {
        clearPreviewBtn.addEventListener('click', () => {
            previewGrid.innerHTML = '';
            previewContainer.classList.add('hidden');
            imageInput.value = '';
        });
    }

    // Delete image with AJAX
    document.querySelectorAll('.delete-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const imageId = btn.dataset.imageId;
            const branchId = btn.dataset.branchId;
            dtmodalConfirmDelete({
                itemName: 'hình ảnh',
                onConfirm: () => {
                    btn.classList.add('btn-loading');
                    fetch(`/admin/branches/${branchId}/images/${imageId}`, {
                            method: 'DELETE',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            }
                        })
                        .then(res => {
                            if (res.headers.get('content-type')?.includes('application/json')) {
                                return res.json();
                            } else {
                                throw new Error('Server did not return JSON.');
                            }
                        })
                        .then(data => {
                            btn.classList.remove('btn-loading');
                            if (data.success) {
                                const galleryItem = document.querySelector(`.gallery-item[data-image-id="${imageId}"]`);
                                if (galleryItem) {
                                    galleryItem.style.animation = 'fadeOut 0.3s ease forwards';
                                    setTimeout(() => galleryItem.remove(), 300);
                                }
                                const galleryGrid = document.querySelector('.gallery-grid');
                                if (galleryGrid && !galleryGrid.children.length) {
                                    const cardBody = galleryGrid.closest('.card-body');
                                    cardBody.innerHTML = `
                                <div class="empty-state" id="emptyState">
                                    <i class="fas fa-images empty-icon"></i>
                                    <h4>Chưa có hình ảnh</h4>
                                    <p>Chi nhánh này chưa có hình ảnh nào</p>
                                    <button class="btn btn-primary" id="emptyStateUploadBtn"><i class="fas fa-upload"></i> Tải lên hình ảnh</button>
                                </div>`;
                                    const newUploadBtn = document.getElementById('emptyStateUploadBtn');
                                    if (newUploadBtn) {
                                        newUploadBtn.addEventListener('click', () => toggleModal(uploadModal, true));
                                    }
                                }
                                dtmodalShowToast('success', {
                                    message: 'Xóa hình ảnh thành công'
                                });
                            } else {
                                dtmodalShowToast('error', {
                                    message: 'Có lỗi khi xóa hình ảnh: ' + (data.message || 'Unknown error')
                                });
                            }
                        })
                        .catch(err => {
                            btn.classList.remove('btn-loading');
                            dtmodalShowToast('error', {
                                message: 'Có lỗi khi xóa hình ảnh: ' + err.message
                            });
                        });
                }
            });
        });
    });

    // Set featured image
    document.querySelectorAll('.set-featured-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            btn.classList.add('btn-loading');
            // Sửa URL để khớp với route đã định nghĩa
            fetch(`/admin/branches/${window.branchId}/set-featured`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    // Gửi imageId trong body thay vì URL
                    body: JSON.stringify({
                        imageId: btn.dataset.imageId
                    })
                })
                .then(res => {
                    // Kiểm tra response status trước khi parse JSON
                    if (!res.ok) {
                        throw new Error(`HTTP error! status: ${res.status}`);
                    }
                    return res.json();
                })
                .then(data => {
                    btn.classList.remove('btn-loading');
                    if (data.success) {
                        // Thay vì reload trang, cập nhật UI trực tiếp
                        // Bỏ featured từ tất cả các nút khác
                        document.querySelectorAll('.featured-btn').forEach(featuredBtn => {
                            featuredBtn.classList.remove('featured-btn');
                            featuredBtn.classList.add('set-featured-btn');
                            featuredBtn.innerHTML = '<i class="far fa-star"></i>';
                        });

                        // Xóa tất cả badge "ảnh đại diện" hiện có
                        document.querySelectorAll('.primary-badge').forEach(badge => {
                            badge.remove();
                        });

                        // Đặt nút hiện tại thành featured
                        btn.classList.remove('set-featured-btn');
                        btn.classList.add('featured-btn');
                        btn.innerHTML = '<i class="fas fa-star"></i>';

                        // Thêm badge "ảnh đại diện" vào ảnh vừa được set
                        const currentGalleryItem = btn.closest('.gallery-item');
                        if (currentGalleryItem) {
                            const primaryBadge = document.createElement('div');
                            primaryBadge.className = 'primary-badge';
                            primaryBadge.innerHTML = '<i class="fas fa-star"></i><span>Ảnh đại diện</span>';
                            currentGalleryItem.appendChild(primaryBadge);
                            
                            // Thêm hiệu ứng animation cho badge mới
                            primaryBadge.style.opacity = '0';
                            primaryBadge.style.transform = 'scale(0.8)';
                            setTimeout(() => {
                                primaryBadge.style.transition = 'all 0.3s ease';
                                primaryBadge.style.opacity = '1';
                                primaryBadge.style.transform = 'scale(1)';
                            }, 100);
                        }

                        // Hiển thị thông báo thành công
                        dtmodalShowToast('success', {
                            message: 'Đã đặt ảnh làm ảnh chính thành công'
                        });
                    } else {
                        dtmodalShowToast('error', {
                            message: 'Có lỗi khi đặt ảnh đại diện: ' + (data.message || 'Unknown error')
                        });
                    }
                })
                .catch(err => {
                    btn.classList.remove('btn-loading');
                    console.error('Error setting featured image:', err);
                    dtmodalShowToast('error', {
                        message: 'Có lỗi khi đặt ảnh đại diện: ' + err.message
                    });
                });
        });
    });

    // Quick click actions with feedback
    document.querySelectorAll('.action-item').forEach(item => {
        item.addEventListener('click', () => {
            item.style.animation = 'pulse 0.2s ease';
            setTimeout(() => item.style.animation = '', 200);
            console.log(`Action clicked: ${item.dataset.action}`);
        });
    });

    // Card animations
    const cards = document.querySelectorAll('.card');
    cards.forEach(card => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
    });
    const animateCards = () => {
        cards.forEach(card => {
            if (card.getBoundingClientRect().top < window.innerHeight * 0.85) {
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }
        });
    };
    window.addEventListener('scroll', animateCards);
    setTimeout(animateCards, 100);

    // Fancybox bind
    if (typeof Fancybox !== 'undefined') {
        Fancybox.bind("[data-fancybox]", {
            Thumbs: {
                autoStart: false
            }
        });
    }
});

// Animation keyframes
const style = document.createElement('style');
style.textContent = `
@keyframes fadeOut {
    from { opacity: 1; }
    to { opacity: 0; transform: scale(0.95); }
}
@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}
`;
document.head.appendChild(style);