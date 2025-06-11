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
        .btn-danger { background: #f43f5e; color: #fff; }
        .btn-danger:hover { background: #e11d48; }
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
        .image-preview-btn { width: 2rem; height: 2rem; border-radius: 50%; background: #fff; color: #1f2937; border: none; cursor: pointer; margin: 0 0.25rem; }
        .image-preview-btn:hover { transform: scale(1.1); }
        .image-preview-btn.remove-btn:hover { background: #f43f5e; color: #fff; }
        .image-preview-badge { position: absolute; top: 0.5rem; left: 0.5rem; padding: 0.25rem 0.5rem; background: #f59e0b; color: #fff; border-radius: 9999px; font-size: 0.625rem; font-weight: 600; }

        .existing-images { display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 1rem; margin-bottom: 1rem; }
        .existing-image-item { position: relative; aspect-ratio: 4/3; border-radius: 12px; overflow: hidden; box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05); }
        .existing-image-img { width: 100%; height: 100%; object-fit: cover; }
        .existing-image-overlay { position: absolute; inset: 0; background: rgba(0, 0, 0, 0.5); display: flex; align-items: center; justify-content: center; opacity: 0; transition: all 0.3s ease; }
        .existing-image-item:hover .existing-image-overlay { opacity: 1; }
        .existing-image-badge { position: absolute; top: 0.5rem; left: 0.5rem; padding: 0.25rem 0.5rem; background: #10b981; color: #fff; border-radius: 9999px; font-size: 0.625rem; font-weight: 600; }
        .primary-badge { background: #f59e0b !important; }

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
        .badge-warning { background: rgba(245, 158, 11, 0.1); color: #f59e0b; padding: 0.25rem 0.5rem; border-radius: 9999px; }
    </style>
@endsection

@section('content')
    <div class="branch-form-container">
        <div class="page-header">
            <div class="header-content">
                <div class="flex items-center gap-4">
                    <div class="header-icon"><i class="fas fa-edit" style="color: #4361ee;"></i></div>
                    <div>
                        <h1>Chỉnh sửa chi nhánh</h1>
                        <p>Cập nhật thông tin chi nhánh: {{ $branch->name }}</p>
                    </div>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('admin.branches.show', $branch->id) }}" class="btn btn-outline"><i class="fas fa-eye"></i> Xem chi tiết</a>
                    <a href="{{ route('admin.branches.index') }}" class="btn btn-outline"><i class="fas fa-arrow-left"></i> Quay lại</a>
                </div>
            </div>
        </div>

        <form id="branchForm" action="{{ route('admin.branches.update', $branch->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
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
                                <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" placeholder="Nhập tên chi nhánh" value="{{ old('name', $branch->name) }}" maxlength="255">
                                @error('name') <div class="form-error">{{ $message }}</div> @enderror
                            </div>
                            <div class="form-group">
                                <label for="address" class="form-label"><i class="fas fa-map-marker-alt" style="color: #4361ee;"></i> Địa chỉ</label>
                                <textarea id="address" name="address" class="form-control @error('address') is-invalid @enderror" placeholder="Nhập địa chỉ chi nhánh" maxlength="255">{{ old('address', $branch->address) }}</textarea>
                                @error('address') <div class="form-error">{{ $message }}</div> @enderror
                            </div>
                            <div class="grid-2">
                                <div class="form-group">
                                    <label for="phone" class="form-label"><i class="fas fa-phone" style="color: #4361ee;"></i> Số điện thoại</label>
                                    <input type="tel" id="phone" name="phone" class="form-control @error('phone') is-invalid @enderror" placeholder="Nhập số điện thoại" value="{{ old('phone', $branch->phone) }}" pattern="[0-9\s\-\+\(\)]{10,}">
                                    @error('phone') <div class="form-error">{{ $message }}</div> @enderror
                                </div>
                                <div class="form-group">
                                    <label for="email" class="form-label"><i class="fas fa-envelope" style="color: #4361ee;"></i> Email</label>
                                    <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror" placeholder="Nhập email (không bắt buộc)" value="{{ old('email', $branch->email) }}">
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
                                    <label for="opening_hour" class="form-label"><i class="fas fa-sun" style="color:rgb(78, 228, 93);"></i> Giờ mở cửa</label>
                                    <input type="time" id="opening_hour" name="opening_hour" class="form-control @error('opening_hour') is-invalid @enderror" value="{{ old('opening_hour', $branch->opening_hour) }}">
                                    @error('opening_hour') <div class="form-error">{{ $message }}</div> @enderror
                                </div>
                                <div class="form-group">
                                    <label for="closing_hour" class="form-label"><i class="fas fa-moon" style="color:rgb(222, 93, 93);"></i> Giờ đóng cửa</label>
                                    <input type="time" id="closing_hour" name="closing_hour" class="form-control @error('closing_hour') is-invalid @enderror" value="{{ old('closing_hour', $branch->closing_hour) }}">
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
                                    <input type="text" id="latitude" name="latitude" class="form-control @error('latitude') is-invalid @enderror" value="{{ old('latitude', $branch->latitude) }}" readonly>
                                    @error('latitude') <div class="form-error">{{ $message }}</div> @enderror
                                </div>
                                <div class="form-group">
                                    <label for="longitude" class="form-label"><i class="fas fa-map-pin" style="color: #4361ee;"></i> Kinh độ</label>
                                    <input type="text" id="longitude" name="longitude" class="form-control @error('longitude') is-invalid @enderror" value="{{ old('longitude', $branch->longitude) }}" readonly>
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
                            @if($branch->images && $branch->images->count() > 0)
                                <div class="form-group">
                                    <label class="form-label"><i class="fas fa-image" style="color: #4361ee;"></i> Hình ảnh hiện tại</label>
                                    <div class="existing-images">
                                        @foreach($branch->images as $image)
                                            <div class="existing-image-item" data-image-id="{{ $image->id }}">
                                                <img src="{{ Storage::disk('s3')->url($image->image_path) }}" alt="{{ $image->caption }}" class="existing-image-img">
                                                @if($image->is_primary)
                                                    <div class="existing-image-badge primary-badge">Ảnh chính</div>
                                                @else
                                                    <div class="existing-image-badge">Ảnh phụ</div>
                                                @endif
                                                <div class="existing-image-overlay">
                                                    <button type="button" class="image-preview-btn" onclick="setPrimaryImage({{ $image->id }})" title="Đặt làm ảnh chính">
                                                        <i class="fas fa-star"></i>
                                                    </button>
                                                    <button type="button" class="image-preview-btn remove-btn" onclick="markImageForDeletion({{ $image->id }})" title="Xóa ảnh">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="form-hint">Nhấp vào nút sao để đặt ảnh chính, nhấp vào nút thùng rác để xóa ảnh</div>
                                </div>
                            @endif

                            <div class="form-group">
                                <label class="form-label" style="border: 2px solid #4361ee; padding: 12px; border-radius: 8px; display: inline-block; cursor: pointer;" onclick="document.getElementById('images').click()">
                                    <i class="fas fa-upload" style="color: #4361ee;"></i> Thêm hình ảnh mới
                                    <span id="imageCount" class="badge" style="display: none; background: #4361ee; color: white; margin-left: 8px; border-radius: 12px; padding: 4px 8px;">0 ảnh</span>
                                </label>
                                <input type="file" id="images" name="images[]" class="upload-input @error('images') is-invalid @enderror" accept="image/jpeg,image/png,image/jpg,image/gif" multiple style="display: none;">
                                <div class="form-hint">Chấp nhận: JPEG, PNG, JPG, GIF. Tối đa 2MB mỗi ảnh. Nhấp vào nút để chọn nhiều ảnh cùng lúc.</div>
                                @error('images') <div class="form-error">{{ $message }}</div> @enderror
                                @error('images.*') <div class="form-error">{{ $message }}</div> @enderror
                            </div>
                            
                            <div id="imagePreview" class="image-preview-grid hidden">
                                <h4>Xem trước hình ảnh mới:</h4>
                                <div id="previewContainer" class="flex gap-3"></div>
                                <div class="form-group">
                                    <label for="primary_image" class="form-label">Chọn ảnh chính từ ảnh mới</label>
                                    <select id="primary_image" name="primary_image" class="form-control">
                                        <option value="0">Ảnh đầu tiên</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div id="captionsContainer" class="hidden">
                                <h4>Mô tả ảnh mới:</h4>
                                <div id="captionInputs"></div>
                            </div>

                            <!-- Hidden inputs for image deletion -->
                            <div id="deleteImageInputs"></div>
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
                            <div class="badge-warning"><i class="fas fa-code"></i> {{ $branch->branch_code }}</div>
                            <p class="text-sm text-gray">Mã chi nhánh không thể thay đổi</p>
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
                                        <option value="{{ $manager->id }}" {{ old('manager_user_id', $branch->manager_user_id) == $manager->id ? 'selected' : '' }}>{{ $manager->full_name }}</option>
                                    @endforeach
                                </select>
                                <div class="form-hint">Chỉ hiển thị quản lý chưa được phân công hoặc quản lý hiện tại</div>
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
                                    <div class="form-hint" id="statusHint">{{ $branch->active ? 'Chi nhánh đang hoạt động' : 'Chi nhánh đã bị vô hiệu hóa' }}</div>
                                </div>
                                <label class="switch">
                                    <input type="checkbox" id="active" name="active" {{ old('active', $branch->active) ? 'checked' : '' }}>
                                    <span class="slider"></span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <button type="submit" id="submitButton" class="btn btn-primary btn-block"><i class="fas fa-save"></i> Cập nhật chi nhánh</button>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header"><h3>Xem trước</h3></div>
                        <div class="card-body">
                            <div class="preview-card">
                                <div class="preview-header">
                                    <i class="fas fa-building" style="color: #4361ee;"></i>
                                    <h4 id="previewName">{{ $branch->name }}</h4>
                                </div>
                                <div class="preview-item">
                                    <i class="fas fa-code" style="color: #4361ee;"></i>
                                    <span>Mã: <strong>{{ $branch->branch_code }}</strong></span>
                                </div>
                                <div class="preview-item">
                                    <i class="fas fa-map-marker-alt" style="color: #4361ee;"></i>
                                    <span id="previewAddress">{{ $branch->address }}</span>
                                </div>
                                <div class="preview-item">
                                    <i class="fas fa-phone" style="color: #4361ee;"></i>
                                    <span id="previewPhone">{{ $branch->phone }}</span>
                                </div>
                                @if($branch->email)
                                <div class="preview-item">
                                    <i class="fas fa-envelope" style="color: #4361ee;"></i>
                                    <span id="previewEmail">{{ $branch->email }}</span>
                                </div>
                                @endif
                                <div class="preview-hours">
                                    <div class="preview-hour opening">
                                        <div>Mở cửa</div>
                                        <div class="preview-hour-value opening" id="previewOpeningHour">{{ date('H:i', strtotime($branch->opening_hour)) }}</div>
                                    </div>
                                    <div class="preview-hour closing">
                                        <div>Đóng cửa</div>
                                        <div class="preview-hour-value closing" id="previewClosingHour">{{ date('H:i', strtotime($branch->closing_hour)) }}</div>
                                    </div>
                                </div>
                                <div class="preview-status">
                                    <span>Trạng thái:</span>
                                    <span class="preview-status-value {{ $branch->active ? 'active' : 'inactive' }}" id="previewStatus">{{ $branch->active ? 'Hoạt động' : 'Vô hiệu hóa' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@section('scripts')
    <script src='https://api.mapbox.com/mapbox-gl-js/v3.1.0/mapbox-gl.js'></script>
    <script>

            let map, marker;
            let imagesToDelete = [];
            let newImages = {}; // Object để lưu trữ ảnh mới
            let imageIndex = 0; // Index cho ảnh mới
            const mapboxApiKey = "{{ env('MAPBOX_API_KEY') }}";
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
        </script>
    </script>
@endsection
