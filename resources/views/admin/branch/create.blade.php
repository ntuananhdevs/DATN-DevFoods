@extends('layouts.admin.contentLayoutMaster')

@section('styles')
    <link href='https://api.mapbox.com/mapbox-gl-js/v3.1.0/mapbox-gl.css' rel='stylesheet' />
    <link href="{{ asset('css/admin/branchs/branch-create.css') }}" rel="stylesheet">
@endsection

@section('content')
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
        // Expose mapbox API key to JavaScript
        window.mapboxApiKey = "{{ config('services.mapbox.access_token') }}";
    </script>
@endsection

@section('scripts')
    <script src='https://api.mapbox.com/mapbox-gl-js/v3.1.0/mapbox-gl.js'></script>
    <script src="{{ asset('js/admin/branchs/branch-create.js') }}"></script>
@endsection