// Branch Create Form JavaScript
document.addEventListener('DOMContentLoaded', () => {
    const mapboxApiKey = window.mapboxApiKey || '';
    const form = document.getElementById('branchForm');
    const inputs = {
        name: document.getElementById('name'),
        address: document.getElementById('address'),
        phone: document.getElementById('phone'),
        email: document.getElementById('email'),
        openingHour: document.getElementById('opening_hour'),
        closingHour: document.getElementById('closing_hour'),
        active: document.getElementById('active'),
        manager: document.getElementById('manager_user_id'),
        latitude: document.getElementById('latitude'),
        longitude: document.getElementById('longitude'),
        images: document.getElementById('images'),
        primaryImage: document.getElementById('primary_image')
    };
    const preview = {
        container: document.getElementById('previewContainer'),
        image: document.getElementById('imagePreview'),
        captions: document.getElementById('captionsContainer'),
        captionInputs: document.getElementById('captionInputs'),
        name: document.getElementById('previewName'),
        address: document.getElementById('previewAddress'),
        phone: document.getElementById('previewPhone'),
        email: document.getElementById('previewEmail'),
        emailContainer: document.getElementById('previewEmailContainer'),
        manager: document.getElementById('previewManager'),
        managerContainer: document.getElementById('previewManagerContainer'),
        openingHour: document.getElementById('previewOpeningHour'),
        closingHour: document.getElementById('previewClosingHour'),
        status: document.getElementById('previewStatus'),
        statusHint: document.getElementById('statusHint')
    };
    const mapHint = document.querySelector('.map-hint');
    let uploadedImages = [], map, marker;

    if (!mapboxApiKey) {
        mapHint.classList.add('form-error');
        mapHint.textContent = 'API key không được cấu hình.';
        return;
    }

    mapboxgl.accessToken = mapboxApiKey;
    function initMap(lat = 21.0285, lng = 105.8542) {
        map = new mapboxgl.Map({
            container: 'map',
            style: 'mapbox://styles/mapbox/streets-v12',
            center: [lng, lat],
            zoom: 13
        });
        map.addControl(new mapboxgl.NavigationControl());
        map.addControl(new mapboxgl.FullscreenControl());
        map.on('load', () => map.resize());
        setMarker(lat, lng);
        map.on('click', e => {
            setMarker(e.lngLat.lat, e.lngLat.lng);
            mapHint.classList.remove('form-error');
            mapHint.textContent = 'Nhấp vào bản đồ để chọn vị trí chi nhánh';
        });
    }

    function setMarker(lat, lng) {
        if (marker) marker.remove();
        marker = new mapboxgl.Marker({ draggable: true, color: '#4361ee' })
            .setLngLat([lng, lat])
            .addTo(map);
        map.setCenter([lng, lat]);
        inputs.latitude.value = lat.toFixed(6);
        inputs.longitude.value = lng.toFixed(6);
        marker.on('dragend', () => {
            const { lng, lat } = marker.getLngLat();
            inputs.latitude.value = lat.toFixed(6);
            inputs.longitude.value = lng.toFixed(6);
            mapHint.classList.remove('form-error');
            mapHint.textContent = 'Nhấp vào bản đồ để chọn vị trí chi nhánh';
        });
    }

    function geocodeAddress(address) {
        if (!address) {
            mapHint.classList.add('form-error');
            mapHint.textContent = 'Vui lòng nhập địa chỉ.';
            return;
        }
        fetch(`https://api.mapbox.com/geocoding/v5/mapbox.places/${encodeURIComponent(address)}.json?access_token=${mapboxgl.accessToken}&limit=1&country=VN`)
            .then(res => res.json())
            .then(data => {
                if (data.features?.length) {
                    const [lng, lat] = data.features[0].center;
                    setMarker(lat, lng);
                    mapHint.classList.remove('form-error');
                    mapHint.textContent = 'Nhấp vào bản đồ để chọn vị trí chi nhánh';
                } else {
                    mapHint.classList.add('form-error');
                    mapHint.textContent = 'Không tìm thấy địa chỉ.';
                }
            })
            .catch(() => {
                mapHint.classList.add('form-error');
                mapHint.textContent = 'Lỗi khi tìm vị trí.';
            });
    }

    function updatePreview() {
        preview.name.textContent = inputs.name.value || 'Tên chi nhánh';
        preview.address.textContent = inputs.address.value || 'Địa chỉ chi nhánh';
        preview.phone.textContent = inputs.phone.value || 'Số điện thoại';
        preview.emailContainer.classList.toggle('hidden', !inputs.email.value);
        preview.email.textContent = inputs.email.value || 'Email';
        preview.manager.textContent = inputs.manager.value ? inputs.manager.options[inputs.manager.selectedIndex].text : 'Chưa chọn quản lý';
        preview.openingHour.textContent = inputs.openingHour.value || '08:00';
        preview.closingHour.textContent = inputs.closingHour.value || '22:00';
        preview.status.textContent = inputs.active.checked ? 'Đang hoạt động' : 'Ngưng hoạt động';
        preview.status.className = `preview-status-value ${inputs.active.checked ? 'active' : 'inactive'}`;
        preview.statusHint.textContent = inputs.active.checked ? 'Chi nhánh đang hoạt động' : 'Chi nhánh ngưng hoạt động';
    }

    function handleImageUpload(e) {
        const files = Array.from(e.target.files).filter(file => file.type.match('image/(jpeg|png|jpg|gif)') && file.size <= 2048 * 1024);
        if (uploadedImages.length + files.length > 10) {
            alert('Tối đa 10 hình ảnh.');
            return;
        }
        uploadedImages = [...uploadedImages, ...files];
        if (files.length) {
            displayImagePreviews();
            preview.image.classList.remove('hidden');
            preview.captions.classList.remove('hidden');
            document.querySelector('.upload-label-text').textContent = `${uploadedImages.length} ảnh đã chọn`;
            updateFileInput();
        }
    }

    function updateFileInput() {
        const dt = new DataTransfer();
        uploadedImages.forEach(file => dt.items.add(file));
        inputs.images.files = dt.files;
    }

    function displayImagePreviews() {
        preview.container.innerHTML = '';
        inputs.primaryImage.innerHTML = '<option value="0">Ảnh đầu tiên</option>';
        preview.captionInputs.innerHTML = '';
        uploadedImages.forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = e => {
                const item = document.createElement('div');
                item.className = 'image-preview-item';
                item.dataset.index = index;
                item.innerHTML = `
                    <img src="${e.target.result}" class="image-preview-img" alt="Ảnh ${index + 1}">
                    <div class="image-preview-overlay">
                        <div class="image-preview-actions">
                            <button class="image-preview-btn remove-btn" aria-label="Xóa ảnh ${index + 1}"><i class="fas fa-trash-alt"></i></button>
                        </div>
                    </div>
                    ${index === 0 ? '<div class="image-preview-badge">Ảnh chính</div>' : ''}
                `;
                item.querySelector('.remove-btn').addEventListener('click', () => removeImage(index));
                preview.container.appendChild(item);

                if (index > 0) {
                    const option = document.createElement('option');
                    option.value = index;
                    option.textContent = `Ảnh ${index + 1}`;
                    inputs.primaryImage.appendChild(option);
                }

                const captionGroup = document.createElement('div');
                captionGroup.className = 'form-group';
                captionGroup.innerHTML = `
                    <label class="form-label">Mô tả ảnh ${index + 1}:</label>
                    <input type="text" class="form-control" name="captions[${index}]" maxlength="255" placeholder="Nhập mô tả cho ảnh...">
                `;
                preview.captionInputs.appendChild(captionGroup);
            };
            reader.readAsDataURL(file);
        });
    }

    function updatePrimaryImage() {
        const index = parseInt(inputs.primaryImage.value);
        preview.container.querySelectorAll('.image-preview-badge').forEach(badge => badge.remove());
        const selected = preview.container.querySelector(`[data-index="${index}"]`);
        if (selected) selected.innerHTML += '<div class="image-preview-badge">Ảnh chính</div>';
    }

    function removeImage(index) {
        uploadedImages.splice(index, 1);
        updateFileInput();
        if (uploadedImages.length) {
            displayImagePreviews();
            document.querySelector('.upload-label-text').textContent = `${uploadedImages.length} ảnh đã chọn`;
        } else {
            preview.image.classList.add('hidden');
            preview.captions.classList.add('hidden');
            inputs.images.value = '';
            document.querySelector('.upload-label-text').textContent = 'Chọn nhiều hình ảnh...';
        }
        if (parseInt(inputs.primaryImage.value) > uploadedImages.length - 1) {
            inputs.primaryImage.value = 0;
            updatePrimaryImage();
        }
    }

    function initForm() {
        updatePreview();
        inputs.address.addEventListener('input', () => {
            updatePreview();
            clearTimeout(geocodeTimeout);
            geocodeTimeout = setTimeout(() => inputs.address.value && geocodeAddress(inputs.address.value), 500);
        });
        ['name', 'phone', 'email', 'openingHour', 'closingHour', 'active', 'manager'].forEach(key => inputs[key].addEventListener('input', updatePreview));
        inputs.manager.addEventListener('change', updatePreview);
        inputs.images.addEventListener('change', handleImageUpload);
        inputs.primaryImage.addEventListener('change', updatePrimaryImage);
        form.addEventListener('submit', e => {
            if (inputs.openingHour.value >= inputs.closingHour.value) {
                e.preventDefault();
                alert('Giờ đóng cửa phải sau giờ mở cửa!');
            }
            if (!inputs.latitude.value || !inputs.longitude.value) {
                e.preventDefault();
                alert('Vui lòng chọn vị trí chi nhánh!');
            }
        });

        let lat = parseFloat(inputs.latitude.value) || 21.0285;
        let lng = parseFloat(inputs.longitude.value) || 105.8542;
        initMap(lat, lng);
        if (inputs.address.value) geocodeAddress(inputs.address.value);
    }

    let geocodeTimeout;
    initForm();
});