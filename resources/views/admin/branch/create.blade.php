@extends('layouts.admin.contentLayoutMaster')
@section('styles')
    <link href='https://api.mapbox.com/mapbox-gl-js/v3.1.0/mapbox-gl.css' rel='stylesheet' />
    <style>
        .branch-form-container { max-width: 100%; margin: 0 auto; }
        h1 { font-size: 1.5rem; font-weight: 600; }
        h2, h3, h4 { font-size: 1.25rem; font-weight: 600; margin: 0; }
        p { margin: 0; line-height: 1.5; color: #6b7280; }

        .btn { display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1rem; border-radius: 12px; font-weight: 500; cursor: pointer; transition: all 0.3s ease; border: none; }
        .btn-primary { background: #4361ee; color: #fff; }
        .btn-primary:hover { background: #3f37c9; }
        .btn-outline { background: transparent; color: #4b5563; border: 1px solid #e5e7eb; }
        .btn-outline:hover { background: #e5e7eb; }
        .btn-block { width: 100%; }
        .btn:disabled { opacity: 0.7; cursor: not-allowed; }

        .page-header { margin-bottom: 1.5rem; }
        .header-content { display: flex; justify-content: space-between; align-items: center; gap: 1rem; flex-wrap: wrap; }
        .header-icon { width: 3rem; height: 3rem; background: rgba(67, 97, 238, 0.1); color: #4361ee; border-radius: 9999px; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; }

        .card { background: #fff; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); margin-bottom: 1.5rem; transition: all 0.3s ease; }
        .card:hover { transform: translateY(-2px); }
        .card-header { display: flex; align-items: center; padding: 1rem; border-bottom: 1px solid #e5e7eb; gap: 0.75rem; }
        .card-icon { width: 2.5rem; height: 2.5rem; background: rgba(67, 97, 238, 0.1); color: #4361ee; border-radius: 9999px; display: flex; align-items: center; justify-content: center; font-size: 1rem; }
        .card-body { padding: 1.5rem; }

        .form-grid { display: grid; gap: 1.5rem; }
        @media (min-width: 992px) { .form-grid { grid-template-columns: 2fr 1fr; } }
        .form-group { margin-bottom: 1rem; }
        .form-label { font-weight: 500; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.5rem; }
        .form-control { width: 100%; padding: 0.625rem; font-size: 0.875rem; border: 1px solid #e5e7eb; border-radius: 12px; transition: all 0.3s ease; }
        .form-control:focus { border-color: #4361ee; outline: none; box-shadow: 0 0 0 2px rgba(67, 97, 238, 0.25); }
        select.form-control { background: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e") no-repeat right 0.5rem center/1.5em; padding-right: 2.5rem; }
        textarea.form-control { min-height: 100px; resize: vertical; }
        .form-hint, .form-error { font-size: 0.75rem; margin-top: 0.25rem; }
        .form-error { color: #f43f5e; }
        .form-check { display: flex; align-items: center; gap: 0.5rem; padding: 1rem; border: 1px solid #e5e7eb; border-radius: 12px; }

        .switch { position: relative; width: 44px; height: 24px; }
        .switch input { opacity: 0; width: 0; height: 0; }
        .slider { position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: #e5e7eb; border-radius: 34px; transition: all 0.3s ease; }
        .slider:before { content: ""; position: absolute; width: 18px; height: 18px; left: 3px; bottom: 3px; background: #fff; border-radius: 50%; transition: all 0.3s ease; }
        input:checked + .slider { background: #4361ee; }
        input:checked + .slider:before { transform: translateX(20px); }

        .grid-2 { display: grid; gap: 1rem; }
        @media (min-width: 768px) { .grid-2 { grid-template-columns: 1fr 1fr; } }

        .upload-label { display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem; background: #e5e7eb; border-radius: 12px; cursor: pointer; }
        .upload-label:hover { background: #6b7280; color: #fff; }
        .upload-input { display: none; }
        .image-preview-grid { display: flex; gap: 1rem; overflow-x: auto; padding-bottom: 0.5rem; }
        .image-preview-item { position: relative; width: 150px; aspect-ratio: 4/3; border-radius: 12px; overflow: hidden; box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05); }
        .image-preview-img { width: 100%; height: 100%; object-fit: cover; }
        .image-preview-overlay { position: absolute; inset: 0; background: rgba(0, 0, 0, 0.5); display: flex; align-items: center; justify-content: center; opacity: 0; transition: all 0.3s ease; }
        .image-preview-item:hover .image-preview-overlay { opacity: 1; }
        .image-preview-btn { width: 2rem; height: 2rem; border-radius: 50%; background: #fff; color: #1f2937; border: none; cursor: pointer; }
        .image-preview-btn:hover { transform: scale(1.1); }
        .image-preview-btn.remove-btn:hover { background: #f43f5e; color: #fff; }
        .image-preview-badge { position: absolute; top: 0.5rem; left: 0.5rem; padding: 0.25rem 0.5rem; background: #f59e0b; color: #fff; border-radius: 9999px; font-size: 0.625rem; font-weight: 600; }

        .preview-card { padding: 1rem; background: rgba(67, 97, 238, 0.05); border: 1px solid rgba(67, 97, 238, 0.1); border-radius: 12px; }
        .preview-item { display: flex; gap: 0.5rem; margin-bottom: 0.5rem; font-size: 0.875rem; }
        .preview-hours { display: flex; gap: 1rem; margin-top: 1rem; }
        .preview-hour { flex: 1; padding: 0.75rem; border-radius: 12px; text-align: center; }
        .preview-hour.opening { background: rgba(74, 222, 128, 0.1); border: 1px solid rgba(74, 222, 128, 0.2); }
        .preview-hour.closing { background: rgba(244, 63, 94, 0.1); border: 1px solid rgba(244, 63, 94, 0.2); }
        .preview-hour-value.opening { color: #4ade80; }
        .preview-hour-value.closing { color: #f43f5e; }
        .preview-status { display: flex; justify-content: space-between; padding: 0.75rem; border: 1px solid #e5e7eb; border-radius: 12px; margin-top: 1rem; }
        .preview-status-value.active { background: rgba(74, 222, 128, 0.1); color: #4ade80; }
        .preview-status-value.inactive { background: rgba(244, 63, 94, 0.1); color: #f43f5e; }

        #map { height: 300px; width: 100%; border-radius: 12px; margin-bottom: 1rem; background: #f0f0f0; }
        .map-coordinates { display: flex; gap: 1rem; }
        .map-hint { font-size: 0.75rem; color: #6b7280; }
        .hidden { display: none; }
        .text-sm { font-size: 0.875rem; }
        .badge-info { background: rgba(59, 130, 246, 0.1); color: #3b82f6; padding: 0.25rem 0.5rem; border-radius: 9999px; }
    </style>
@endsection

@section('content')
    <!-- Giữ nguyên HTML và JavaScript như trong mã gốc -->
    <div class="branch-form-container">
        <div class="page-header">
            <div class="header-content">
                <div class="flex items-center gap-4">
                    <div class="header-icon"><i class="fas fa-building" style="color: #4361ee;"></i></div>
                    <div>
                        <h1>Thêm chi nhánh mới</h1>
                        <p>Nhập thông tin chi nhánh mới</p>
                    </div>
                </div>
                <a href="{{ route('admin.branches.index') }}" class="btn btn-outline"><i class="fas fa-arrow-left"></i> Quay lại</a>
            </div>
        </div>

        <form id="branchForm" action="{{ route('admin.branches.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-grid">
                <div class="space-y-6">
                    <div class="card">
                        <div class="card-header">
                            <div class="card-icon"><i class="fas fa-info-circle" style="color: #4361ee;"></i></div>
                            <h3>Thông tin cơ bản</h3>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label for="name" class="form-label"><i class="fas fa-building" style="color: #4361ee;"></i> Tên chi nhánh</label>
                                <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" placeholder="Nhập tên chi nhánh" value="{{ old('name') }}" maxlength="255">
                                @error('name') <div class="form-error">{{ $message }}</div> @enderror
                            </div>
                            <div class="form-group">
                                <label for="address" class="form-label"><i class="fas fa-map-marker-alt text-danger" style="color: #4361ee;"></i> Địa chỉ</label>
                                <textarea id="address" name="address" class="form-control @error('address') is-invalid @enderror" placeholder="Nhập địa chỉ chi nhánh" maxlength="255">{{ old('address') }}</textarea>
                                @error('address') <div class="form-error">{{ $message }}</div> @enderror
                            </div>
                            <div class="grid-2">
                                <div class="form-group">
                                    <label for="phone" class="form-label"><i class="fas fa-phone" style="color: #4361ee;"></i> Số điện thoại</label>
                                    <input type="tel" id="phone" name="phone" class="form-control @error('phone') is-invalid @enderror" placeholder="Nhập số điện thoại" value="{{ old('phone') }}" pattern="[0-9\s\-\+\(\)]{10,}">
                                    @error('phone') <div class="form-error">{{ $message }}</div> @enderror
                                </div>
                                <div class="form-group">
                                    <label for="email" class="form-label"><i class="fas fa-envelope" style="color: #4361ee;"></i> Email</label>
                                    <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror" placeholder="Nhập email (không bắt buộc)" value="{{ old('email') }}">
                                    @error('email') <div class="form-error">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <div class="card-icon"><i class="fas fa-clock" style="color: #4361ee;"></i></div>
                            <h3>Giờ hoạt động</h3>
                        </div>
                        <div class="card-body">
                            <div class="grid-2">
                                <div class="form-group">
                                    <label for="opening_hour" class="form-label"><i class="fas fa-sun text-success" style="color:rgb(128, 231, 103);"></i> Giờ mở cửa</label>
                                    <input type="time" id="opening_hour" name="opening_hour" class="form-control @error('opening_hour') is-invalid @enderror" value="{{ old('opening_hour', '08:00') }}">
                                    @error('opening_hour') <div class="form-error">{{ $message }}</div> @enderror
                                </div>
                                <div class="form-group">
                                    <label for="closing_hour" class="form-label"><i class="fas fa-moon text-danger" style="color:rgb(237, 72, 72);"></i> Giờ đóng cửa</label>
                                    <input type="time" id="closing_hour" name="closing_hour" class="form-control @error('closing_hour') is-invalid @enderror" value="{{ old('closing_hour', '22:00') }}">
                                    @error('closing_hour') <div class="form-error">{{ $message }}</div> @enderror
                                    <div class="form-hint">Giờ đóng cửa phải sau giờ mở cửa</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <div class="card-icon"><i class="fas fa-map-marked-alt" style="color: #4361ee;"></i></div>
                            <h3>Vị trí chi nhánh</h3>
                        </div>
                        <div class="card-body">
                            <div id="map"></div>
                            <div class="map-hint">Nhấp vào bản đồ để chọn vị trí chi nhánh</div>
                            <div class="map-coordinates grid-2">
                                <div class="form-group">
                                    <label for="latitude" class="form-label"><i class="fas fa-map-pin" style="color: #4361ee;"></i> Vĩ độ</label>
                                    <input type="text" id="latitude" name="latitude" class="form-control @error('latitude') is-invalid @enderror" value="{{ old('latitude') }}" readonly>
                                    @error('latitude') <div class="form-error">{{ $message }}</div> @enderror
                                </div>
                                <div class="form-group">
                                    <label for="longitude" class="form-label"><i class="fas fa-map-pin" style="color: #4361ee;"></i> Kinh độ</label>
                                    <input type="text" id="longitude" name="longitude" class="form-control @error('longitude') is-invalid @enderror" value="{{ old('longitude') }}" readonly>
                                    @error('longitude') <div class="form-error">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <div class="card-icon"><i class="fas fa-images" style="color: #4361ee;"></i></div>
                            <h3>Hình ảnh chi nhánh</h3>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label for="images" class="form-label" style="border: 1px solid #4361ee; padding: 8px; border-radius: 8px; display: inline-block;"><i class="fas fa-upload" style="color: #4361ee;"></i> Tải lên hình ảnh</label>
                                <div class="">
                                    <input type="file" id="images" name="images[]" class="upload-input @error('images') is-invalid @enderror" accept="image/jpeg,image/png,image/jpg,image/gif" multiple>
                                    <span class="upload-label-text">Chọn nhiều hình ảnh...</span>
                                </div>
                                <div class="form-hint">Chấp nhận: JPEG, PNG, JPG, GIF. Tối đa 2MB mỗi ảnh.</div>
                                @error('images') <div class="form-error">{{ $message }}</div> @enderror
                                @error('images.*') <div class="form-error">{{ $message }}</div> @enderror
                            </div>
                            <div id="imagePreview" class="image-preview-grid hidden">
                                <h4>Xem trước hình ảnh:</h4>
                                <div id="previewContainer" class="flex gap-3"></div>
                                <div class="form-group">
                                    <label for="primary_image" class="form-label">Chọn ảnh chính</label>
                                    <select id="primary_image" name="primary_image" class="form-control">
                                        <option value="0">Ảnh đầu tiên</option>
                                    </select>
                                </div>
                            </div>
                            <div id="captionsContainer" class="hidden">
                                <h4>Mô tả ảnh:</h4>
                                <div id="captionInputs"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="card">
                        <div class="card-header">
                            <div class="card-icon"><i class="fas fa-hashtag" style="color: #4361ee;"></i></div>
                            <h3>Mã chi nhánh</h3>
                        </div>
                        <div class="card-body text-center">
                            <div class="badge-info"><i class="fas fa-info-circle"></i> Mã chi nhánh sẽ được tạo tự động</div>
                            <p class="text-sm text-gray">Ví dụ: BR0001, BR0002, ...</p>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <div class="card-icon"><i class="fas fa-user-tie" style="color: #4361ee;"></i></div>
                            <h3>Quản lý chi nhánh</h3>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label for="manager_user_id" class="form-label">Chọn người quản lý</label>
                                <select id="manager_user_id" name="manager_user_id" class="form-control @error('manager_user_id') is-invalid @enderror">
                                    <option value="">-- Chọn quản lý --</option>
                                    @foreach($availableManagers as $manager)
                                        <option value="{{ $manager->id }}" {{ old('manager_user_id') == $manager->id ? 'selected' : '' }}>{{ $manager->full_name }}</option>
                                    @endforeach
                                </select>
                                <div class="form-hint">Chỉ hiển thị quản lý chưa được phân công</div>
                                @error('manager_user_id') <div class="form-error">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <div class="card-icon"><i class="fas fa-star" style="color: #4361ee;"></i></div>
                            <h3>Trạng thái</h3>
                        </div>
                        <div class="card-body">
                            <div class="form-check">
                                <div>
                                    <div class="form-check-label">Trạng thái hoạt động</div>
                                    <div class="form-hint" id="statusHint">Chi nhánh đang hoạt động</div>
                                </div>
                                <label class="switch">
                                    <input type="checkbox" id="active" name="active" {{ old('active', 1) ? 'checked' : '' }}>
                                    <span class="slider"></span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <button type="submit" id="submitButton" class="btn btn-primary btn-block"><i class="fas fa-save"></i> Lưu chi nhánh</button>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header"><h3>Xem trước</h3></div>
                        <div class="card-body">
                            <div class="preview-card">
                                <div class="preview-header" style="display: flex; align-items: center; gap: 0.5rem;">
                                    <i class="fas fa-building" style="color: #4361ee;"></i>
                                    <h4 id="previewName" style="margin: 0;">Tên chi nhánh</h4>
                                </div>
                                <div class="preview-item"><i class="fas fa-map-marker-alt text-danger"style="color: #4361ee;" ></i> <span id="previewAddress">Địa chỉ chi nhánh</span></div>
                                <div class="preview-item"><i class="fas fa-phone" style="color: #4361ee;"></i> <span id="previewPhone">Số điện thoại</span></div>
                                <div class="preview-item hidden" id="previewEmailContainer"><i class="fas fa-envelope" style="color: #4361ee;"></i> <span id="previewEmail">Email</span></div>
                                <div class="preview-item" id="previewManagerContainer"><i class="fas fa-user-tie" style="color: #4361ee;"></i> <span id="previewManager">Chưa chọn quản lý</span></div>
                            </div>
                            <div class="preview-hours">
                                <div class="preview-hour opening">
                                    <i class="fas fa-sun text-success" style="color:rgb(93, 242, 130);"></i>
                                    <div class="preview-hour-label">Mở cửa</div>
                                    <div class="preview-hour-value opening" id="previewOpeningHour">08:00</div>
                                </div>
                                <div class="preview-hour closing">
                                    <i class="fas fa-moon text-danger" style="color:rgb(235, 86, 86);"></i>
                                    <div class="preview-hour-label">Đóng cửa</div>
                                    <div class="preview-hour-value closing" id="previewClosingHour">22:00</div>
                                </div>
                            </div>
                            <div class="preview-status">
                                <div class="preview-status-label">Trạng thái</div>
                                <div class="preview-status-value active" id="previewStatus">Đang hoạt động</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        const mapboxApiKey = "{{ env('MAPBOX_API_KEY') }}";
        document.addEventListener('DOMContentLoaded', () => {
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
    </script>
@endsection

@section('scripts')
    <script src='https://api.mapbox.com/mapbox-gl-js/v3.1.0/mapbox-gl.js'></script>
@endsection