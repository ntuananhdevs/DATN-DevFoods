@extends('layouts.admin.contentLayoutMaster')

@section('styles')
    <link href='https://api.mapbox.com/mapbox-gl-js/v3.1.0/mapbox-gl.css' rel='stylesheet' />
    <link href="{{ asset('css/admin/branchs/branch-edit.css') }}" rel="stylesheet">
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
    <script src="{{ asset('js/admin/branchs/branch-edit.js') }}"></script>
    <script>
        // Set Mapbox API key for the external JavaScript file
        setMapboxApiKey("{{ config('services.mapbox.access_token') }}");
    </script>
@endsection