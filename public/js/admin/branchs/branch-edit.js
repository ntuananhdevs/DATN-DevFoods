let map, marker;
let imagesToDelete = [];
let newImages = {}; // Object để lưu trữ ảnh mới
let imageIndex = 0; // Index cho ảnh mới
let mapboxApiKey;

document.addEventListener('DOMContentLoaded', function() {
    // Initialize map
    initializeMap();
    
    // Initialize form listeners
    initializeFormListeners();
    
    // Initialize drag and drop
    initializeDragAndDrop();
    
    // Update preview on page load
    updatePreview();
});

function initializeMap() {
    mapboxgl.accessToken = mapboxApiKey;
    
    const lat = parseFloat(document.getElementById('latitude').value) || 21.0285;
    const lng = parseFloat(document.getElementById('longitude').value) || 105.8542;
    
    map = new mapboxgl.Map({
        container: 'map',
        style: 'mapbox://styles/mapbox/streets-v11',
        center: [lng, lat],
        zoom: 15
    });
    
    // Add existing marker if coordinates exist
    if (document.getElementById('latitude').value && document.getElementById('longitude').value) {
        marker = new mapboxgl.Marker()
            .setLngLat([lng, lat])
            .addTo(map);
    }
    
    // Add click event to map
    map.on('click', function(e) {
        const lat = e.lngLat.lat;
        const lng = e.lngLat.lng;
        
        // Update input fields
        document.getElementById('latitude').value = lat.toFixed(8);
        document.getElementById('longitude').value = lng.toFixed(8);
        
        // Remove existing marker
        if (marker) {
            marker.remove();
        }
        
        // Add new marker
        marker = new mapboxgl.Marker()
            .setLngLat([lng, lat])
            .addTo(map);
    });
}

function initializeFormListeners() {
    // Form input listeners for preview update
    ['name', 'address', 'phone', 'opening_hour', 'closing_hour'].forEach(field => {
        const element = document.getElementById(field);
        if (element) {
            element.addEventListener('input', updatePreview);
        }
    });
    
    // Image input listener
    document.getElementById('images').addEventListener('change', handleImageSelection);
    
    // Form submit listener
    document.getElementById('branchForm').addEventListener('submit', function() {
        document.getElementById('submitBtn').disabled = true;
        document.getElementById('submitBtn').innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang cập nhật...';
    });
}

function initializeDragAndDrop() {
    const uploadArea = document.querySelector('.upload-area');
    
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        uploadArea.addEventListener(eventName, preventDefaults, false);
    });
    
    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }
    
    ['dragenter', 'dragover'].forEach(eventName => {
        uploadArea.addEventListener(eventName, highlight, false);
    });
    
    ['dragleave', 'drop'].forEach(eventName => {
        uploadArea.addEventListener(eventName, unhighlight, false);
    });
    
    function highlight(e) {
        uploadArea.style.background = '#e3f2fd';
        uploadArea.style.borderColor = '#2196f3';
    }
    
    function unhighlight(e) {
        uploadArea.style.background = '#f8f9ff';
        uploadArea.style.borderColor = '#4361ee';
    }
    
    uploadArea.addEventListener('drop', handleDrop, false);
    
    function handleDrop(e) {
        const dt = e.dataTransfer;
        const files = dt.files;
        
        Array.from(files).forEach(file => {
            if (file.type.startsWith('image/')) {
                addSingleImage(file);
            }
        });
    }
}

function handleImageSelection(e) {
    const files = e.target.files;
    if (files.length > 0) {
        Array.from(files).forEach(file => {
            addSingleImage(file);
        });
        // Reset input để có thể chọn lại cùng file
        e.target.value = '';
    }
}

function addSingleImage(file) {
    const currentIndex = imageIndex++;
    newImages[currentIndex] = file;
    
    const reader = new FileReader();
    reader.onload = function(e) {
        updateNewImagePreviews();
        updateNewImageCount();
        updatePrimaryImageSelect();
        updatePreview();
    };
    reader.readAsDataURL(file);
}

function updateNewImagePreviews() {
    const previewContainer = document.getElementById('previewContainer');
    const imagePreview = document.getElementById('imagePreview');
    
    previewContainer.innerHTML = '';
    
    const hasImages = Object.keys(newImages).length > 0;
    
    if (hasImages) {
        imagePreview.classList.remove('hidden');
        
        Object.keys(newImages).forEach(index => {
            const file = newImages[index];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const previewItem = document.createElement('div');
                    previewItem.className = 'image-preview-item';
                    previewItem.style.cssText = 'position: relative; display: inline-block; margin: 5px; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1);';
                    previewItem.innerHTML = `
                        <img src="${e.target.result}" alt="Preview ${index}" class="image-preview-img" style="width: 120px; height: 120px; object-fit: cover; display: block;">
                        <div class="image-preview-badge" style="position: absolute; top: 5px; left: 5px; background: rgba(67, 97, 238, 0.9); color: white; padding: 2px 6px; border-radius: 4px; font-size: 0.8em;">Ảnh ${parseInt(index) + 1}</div>
                        <div class="image-preview-overlay" style="position: absolute; top: 0; right: 0; bottom: 0; left: 0; background: rgba(0,0,0,0.5); opacity: 0; transition: opacity 0.3s; display: flex; align-items: center; justify-content: center;" onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0'">
                            <button type="button" class="image-preview-btn remove-btn" onclick="removeNewImage(${index})" title="Xóa ảnh" style="background: #dc3545; color: white; border: none; border-radius: 50%; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.1)'" onmouseout="this.style.transform='scale(1)'">
                                <i class="fas fa-trash" style="font-size: 0.9em;"></i>
                            </button>
                        </div>
                    `;
                    previewContainer.appendChild(previewItem);
                };
                reader.readAsDataURL(file);
            }
        });
    } else {
        imagePreview.classList.add('hidden');
    }
    
    updateCaptionInputs();
    syncFileInput();
}

function updateCaptionInputs() {
    const captionsContainer = document.getElementById('captionsContainer');
    const captionInputs = document.getElementById('captionInputs');
    
    captionInputs.innerHTML = '';
    
    const hasImages = Object.keys(newImages).length > 0;
    
    if (hasImages) {
        captionsContainer.classList.remove('hidden');
        
        Object.keys(newImages).forEach(index => {
            if (newImages[index]) {
                const captionDiv = document.createElement('div');
                captionDiv.className = 'form-group';
                captionDiv.innerHTML = `
                    <label class="form-label">Mô tả ảnh ${parseInt(index) + 1}</label>
                    <input type="text" name="captions[${index}]" class="form-control" placeholder="Nhập mô tả cho ảnh ${parseInt(index) + 1}" maxlength="255">
                `;
                captionInputs.appendChild(captionDiv);
            }
        });
    } else {
        captionsContainer.classList.add('hidden');
    }
}

function updatePrimaryImageSelect() {
    const primarySelect = document.getElementById('primary_image');
    primarySelect.innerHTML = '<option value="0">Không chọn</option>';
    
    Object.keys(newImages).forEach(index => {
        if (newImages[index]) {
            const option = document.createElement('option');
            option.value = index;
            option.textContent = `Ảnh mới ${parseInt(index) + 1}`;
            primarySelect.appendChild(option);
        }
    });
}

function syncFileInput() {
    const fileInput = document.getElementById('images');
    const dataTransfer = new DataTransfer();
    
    Object.keys(newImages).forEach(index => {
        if (newImages[index]) {
            dataTransfer.items.add(newImages[index]);
        }
    });
    
    fileInput.files = dataTransfer.files;
}

function updateNewImageCount() {
    const imageCount = document.getElementById('imageCount');
    const totalImages = Object.keys(newImages).filter(key => newImages[key]).length;
    
    if (imageCount) {
        if (totalImages > 0) {
            imageCount.textContent = `${totalImages} ảnh`;
            imageCount.style.display = 'inline-block';
        } else {
            imageCount.style.display = 'none';
        }
    }
}

// Xóa ảnh mới ngay lập tức không cần xác nhận
function removeNewImage(index) {
    delete newImages[index];
    updateNewImagePreviews();
    updateNewImageCount();
    updatePrimaryImageSelect();
    updatePreview();
}

// Xóa ảnh hiện có ngay lập tức không cần xác nhận
function markImageForDeletion(imageId) {
    imagesToDelete.push(imageId);
    
    // Hide the image immediately
    const imageItem = document.querySelector(`[data-image-id="${imageId}"]`);
    if (imageItem) {
        imageItem.style.opacity = '0.5';
        imageItem.style.pointerEvents = 'none';
        
        // Add a "deleted" indicator
        const badge = imageItem.querySelector('.existing-image-badge');
        if (badge) {
            badge.textContent = 'Đã xóa';
            badge.style.background = '#dc3545';
        }
    }
    
    // Add hidden input
    const deleteInputs = document.getElementById('deleteImageInputs');
    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = 'delete_images[]';
    input.value = imageId;
    deleteInputs.appendChild(input);
}

// Set primary image (existing images)
function setPrimaryImage(imageId) {
    // Remove primary badge from all images
    document.querySelectorAll('.existing-image-badge').forEach(badge => {
        badge.textContent = 'Ảnh phụ';
        badge.classList.remove('primary-badge');
    });
    
    // Add primary badge to selected image
    const selectedImage = document.querySelector(`[data-image-id="${imageId}"] .existing-image-badge`);
    if (selectedImage) {
        selectedImage.textContent = 'Ảnh chính';
        selectedImage.classList.add('primary-badge');
    }
    
    // Add hidden input to mark as primary
    const existingPrimaryInput = document.querySelector('input[name="set_primary_image"]');
    if (existingPrimaryInput) {
        existingPrimaryInput.remove();
    }
    
    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = 'set_primary_image';
    input.value = imageId;
    document.getElementById('deleteImageInputs').appendChild(input);
}

function updatePreview() {
    const name = document.getElementById('name').value || 'Tên chi nhánh';
    const address = document.getElementById('address').value || 'Địa chỉ chi nhánh';
    const phone = document.getElementById('phone').value || 'Số điện thoại';
    const opening = document.getElementById('opening_hour').value || '00:00';
    const closing = document.getElementById('closing_hour').value || '00:00';
    
    document.getElementById('previewName').textContent = name;
    document.getElementById('previewAddress').textContent = address;
    document.getElementById('previewPhone').textContent = phone;
    document.getElementById('previewHours').textContent = `${opening} - ${closing}`;
}

// Function to set Mapbox API key from external source
function setMapboxApiKey(apiKey) {
    mapboxApiKey = apiKey;
}