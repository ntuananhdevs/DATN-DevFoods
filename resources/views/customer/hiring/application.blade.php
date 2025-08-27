@extends('layouts.customer.fullLayoutMaster')

@section('title', 'Đăng ký tài xế - DevFoods')

@section('content')
<!-- Custom CSS for application form -->
<link rel="stylesheet" href="{{ asset('css/sample-control.css') }}">
<style>
    .hiring-application-container {
        font-family: 'Inter', sans-serif;
        background-color: #f8fafc;
        min-height: 100vh;
    }
    
    .form-section {
        background-color: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        margin-bottom: 24px;
    }
    
    .section-title {
        color: #1a202c;
        font-weight: 600;
        border-bottom: 2px solid #3182ce;
        display: inline-block;
        padding-bottom: 8px;
    }
    
    .form-input {
        width: 100%;
        padding: 12px 16px;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        font-size: 16px;
        transition: border-color 0.2s ease;
    }
    
    .form-input:focus {
        outline: none;
        border-color: #3182ce;
        box-shadow: 0 0 0 3px rgba(49, 130, 206, 0.1);
    }
    
    .form-input.error {
        border-color: #e53e3e;
    }
    
    .form-select {
        width: 100%;
        padding: 12px 16px;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        background-color: white;
        font-size: 16px;
        transition: border-color 0.2s ease;
    }
    
    .form-select:focus {
        outline: none;
        border-color: #3182ce;
        box-shadow: 0 0 0 3px rgba(49, 130, 206, 0.1);
    }
    
    .file-input-wrapper {
        position: relative;
        display: inline-block;
        width: 100%;
    }
    
    .file-input {
        position: absolute;
        opacity: 0;
        width: 100%;
        height: 100%;
        cursor: pointer;
    }
    
    .file-input-label {
        display: block;
        padding: 12px 16px;
        border: 2px dashed #d1d5db;
        border-radius: 6px;
        text-align: center;
        cursor: pointer;
        transition: all 0.2s ease;
        background-color: #f7fafc;
    }
    
    .file-input-label:hover {
        border-color: #3182ce;
        background-color: #ebf8ff;
    }
    
    .btn-primary {
        background-color: #3182ce;
        color: white;
        padding: 12px 32px;
        border-radius: 6px;
        font-weight: 600;
        border: none;
        cursor: pointer;
        transition: background-color 0.2s ease;
    }
    
    .btn-primary:hover {
        background-color: #2c5282;
    }
    
    .checkbox-wrapper {
        display: flex;
        align-items: flex-start;
        gap: 12px;
    }
    
    .checkbox-input {
        width: 18px;
        height: 18px;
        margin-top: 2px;
    }
    
    .error-message {
        color: #e53e3e;
        font-size: 14px;
        margin-top: 4px;
    }
    
    .required {
        color: #e53e3e;
    }
    
    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1000;
    }
    
    .modal-content {
        background-color: white;
        border-radius: 8px;
        max-width: 600px;
        max-height: 80vh;
        overflow-y: auto;
        margin: 20px;
    }
    
    .modal-header {
        padding: 20px;
        border-bottom: 1px solid #e2e8f0;
    }
    
    .modal-body {
        padding: 20px;
    }
    
    .modal-footer {
        padding: 20px;
        border-top: 1px solid #e2e8f0;
        text-align: right;
    }

    /* === Primary Color Overrides === */
    :root {
        --primary-color: #f97316;
        --primary-color-dark: #c2410c;
        --primary-color-light: #ffedd5;
    }
    .section-title { border-bottom: 2px solid var(--primary-color); }
    .form-input:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.1);
    }
    .form-select:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.1);
    }
    .file-input-label:hover {
        border-color: var(--primary-color);
        background-color: var(--primary-color-light);
    }
    .btn-primary {
        background-color: var(--primary-color);
    }
    .btn-primary:hover {
        background-color: var(--primary-color-dark);
    }
    .text-blue-600 { color: var(--primary-color) !important; }
    .hover\\:text-blue-800:hover { color: var(--primary-color-dark) !important; }
    .bg-blue-600 { background-color: var(--primary-color) !important; }
    .border-blue-600 { border-color: var(--primary-color) !important; }
</style>

@if (session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 max-w-7xl mx-auto">
        {{ session('error') }}
    </div>
@endif

@if (session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 max-w-7xl mx-auto">
        {{ session('success') }}
    </div>
@endif

<main class="hiring-application-container">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- Header -->
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">Đăng ký trở thành đối tác tài xế</h1>
            <p class="text-xl text-gray-600">Vui lòng điền đầy đủ thông tin dưới đây để hoàn tất đơn đăng ký</p>
        </div>

        <form action="{{ route('driver.application.submit') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
            @csrf
            
            <!-- Thông tin cá nhân -->
            <div class="form-section p-6">
                <h3 class="section-title text-xl mb-6">
                    <i class="fas fa-user mr-3 text-blue-600"></i>Thông tin cá nhân
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="full_name" class="block text-sm font-medium text-gray-700 mb-2">
                            Họ và tên <span class="required">*</span>
                        </label>
                        <input type="text" id="full_name" name="full_name" 
                               class="form-input @error('full_name') error @enderror" 
                               value="{{ old('full_name') }}" required>
                        @error('full_name')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="date_of_birth" class="block text-sm font-medium text-gray-700 mb-2">
                            Ngày sinh <span class="required">*</span>
                        </label>
                        <input type="date" id="date_of_birth" name="date_of_birth" 
                               class="form-input @error('date_of_birth') error @enderror" 
                               value="{{ old('date_of_birth') }}" required>
                        @error('date_of_birth')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="gender" class="block text-sm font-medium text-gray-700 mb-2">
                            Giới tính <span class="required">*</span>
                        </label>
                        <select id="gender" name="gender" class="form-select @error('gender') error @enderror" required>
                            <option value="">-- Chọn giới tính --</option>
                            <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Nam</option>
                            <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Nữ</option>
                            <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Khác</option>
                        </select>
                        @error('gender')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="profile_image" class="block text-sm font-medium text-gray-700 mb-2">
                            Ảnh chân dung <span class="required">*</span>
                        </label>
                        <div class="file-input-wrapper">
                            <input type="file" id="profile_image" name="profile_image" 
                                   class="file-input @error('profile_image') error @enderror" 
                                   accept="image/*" required>
                            <label for="profile_image" class="file-input-label" id="profile_image_label">
                                <i class="fas fa-cloud-upload-alt mr-2"></i>Chọn ảnh chân dung
                            </label>
                        </div>
                        @error('profile_image')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                        <p class="text-sm text-gray-500 mt-2">Tải lên ảnh chân dung rõ mặt, không đeo kính và nón.</p>
                    </div>
                </div>
            </div>
            
            <!-- Thông tin liên hệ -->
            <div class="form-section p-6">
                <h3 class="section-title text-xl mb-6">
                    <i class="fas fa-phone-alt mr-3 text-blue-600"></i>Thông tin liên hệ
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="phone_number" class="block text-sm font-medium text-gray-700 mb-2">
                            Số điện thoại <span class="required">*</span>
                        </label>
                        <input type="tel" id="phone_number" name="phone_number" 
                               class="form-input @error('phone_number') error @enderror" 
                               value="{{ old('phone_number') }}" required>
                        @error('phone_number')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            Email <span class="required">*</span>
                        </label>
                        <input type="email" id="email" name="email" 
                               class="form-input @error('email') error @enderror" 
                               value="{{ old('email') }}" required>
                        @error('email')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="md:col-span-2">
                        <label for="address" class="block text-sm font-medium text-gray-700 mb-2">
                            Địa chỉ <span class="required">*</span>
                        </label>
                        <input type="text" id="address" name="address" 
                               class="form-input @error('address') error @enderror" 
                               value="{{ old('address') }}" required>
                        @error('address')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="city" class="block text-sm font-medium text-gray-700 mb-2">
                            Tỉnh/Thành phố <span class="required">*</span>
                        </label>
                        <select id="city" name="city" 
                               class="form-select @error('city') error @enderror" required>
                            <option value="">-- Chọn Tỉnh/Thành phố --</option>
                        </select>
                        @error('city')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="district" class="block text-sm font-medium text-gray-700 mb-2">
                            Quận/Huyện <span class="required">*</span>
                        </label>
                        <select id="district" name="district" 
                               class="form-select @error('district') error @enderror" required>
                            <option value="">-- Chọn Quận/Huyện --</option>
                        </select>
                        @error('district')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <!-- Thông tin CMND/CCCD -->
            <div class="form-section p-6">
                <h3 class="section-title text-xl mb-6">
                    <i class="fas fa-id-card mr-3 text-blue-600"></i>Thông tin CMND/CCCD
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <div>
                        <label for="id_card_number" class="block text-sm font-medium text-gray-700 mb-2">
                            Số CMND/CCCD <span class="required">*</span>
                        </label>
                        <input type="text" id="id_card_number" name="id_card_number" 
                               class="form-input @error('id_card_number') error @enderror" 
                               value="{{ old('id_card_number') }}" required>
                        @error('id_card_number')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="id_card_issue_date" class="block text-sm font-medium text-gray-700 mb-2">
                            Ngày cấp <span class="required">*</span>
                        </label>
                        <input type="date" id="id_card_issue_date" name="id_card_issue_date" 
                               class="form-input @error('id_card_issue_date') error @enderror" 
                               value="{{ old('id_card_issue_date') }}" required>
                        @error('id_card_issue_date')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="id_card_issue_place" class="block text-sm font-medium text-gray-700 mb-2">
                            Nơi cấp <span class="required">*</span>
                        </label>
                        <input type="text" id="id_card_issue_place" name="id_card_issue_place" 
                               class="form-input @error('id_card_issue_place') error @enderror" 
                               value="{{ old('id_card_issue_place') }}" required>
                        @error('id_card_issue_place')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="id_card_front_image" class="block text-sm font-medium text-gray-700 mb-2">
                            Ảnh mặt trước CMND/CCCD <span class="required">*</span>
                        </label>
                        <div class="file-input-wrapper">
                            <input type="file" id="id_card_front_image" name="id_card_front_image" 
                                   class="file-input @error('id_card_front_image') error @enderror" 
                                   accept="image/*" required>
                            <label for="id_card_front_image" class="file-input-label" id="id_card_front_image_label">
                                <i class="fas fa-cloud-upload-alt mr-2"></i>Chọn ảnh mặt trước
                            </label>
                        </div>
                        @error('id_card_front_image')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="id_card_back_image" class="block text-sm font-medium text-gray-700 mb-2">
                            Ảnh mặt sau CMND/CCCD <span class="required">*</span>
                        </label>
                        <div class="file-input-wrapper">
                            <input type="file" id="id_card_back_image" name="id_card_back_image" 
                                   class="file-input @error('id_card_back_image') error @enderror" 
                                   accept="image/*" required>
                            <label for="id_card_back_image" class="file-input-label" id="id_card_back_image_label">
                                <i class="fas fa-cloud-upload-alt mr-2"></i>Chọn ảnh mặt sau
                            </label>
                        </div>
                        @error('id_card_back_image')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <!-- Thông tin phương tiện -->
            <div class="form-section p-6">
                <h3 class="section-title text-xl mb-6">
                    <i class="fas fa-motorcycle mr-3 text-blue-600"></i>Thông tin phương tiện
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <div>
                        <label for="vehicle_type" class="block text-sm font-medium text-gray-700 mb-2">
                            Loại phương tiện <span class="required">*</span>
                        </label>
                        <select id="vehicle_type" name="vehicle_type" class="form-select @error('vehicle_type') error @enderror" required>
                            <option value="">-- Chọn loại phương tiện --</option>
                            <option value="motorcycle" {{ old('vehicle_type') == 'motorcycle' ? 'selected' : '' }}>Xe máy</option>
                            <option value="car" {{ old('vehicle_type') == 'car' ? 'selected' : '' }}>Ô tô</option>
                            <option value="bicycle" {{ old('vehicle_type') == 'bicycle' ? 'selected' : '' }}>Xe đạp</option>
                        </select>
                        @error('vehicle_type')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="vehicle_model" class="block text-sm font-medium text-gray-700 mb-2">
                            Dòng xe <span class="required">*</span>
                        </label>
                        <input type="text" id="vehicle_model" name="vehicle_model" 
                               class="form-input @error('vehicle_model') error @enderror" 
                               value="{{ old('vehicle_model') }}" required>
                        @error('vehicle_model')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="vehicle_color" class="block text-sm font-medium text-gray-700 mb-2">
                            Màu xe <span class="required">*</span>
                        </label>
                        <input type="text" id="vehicle_color" name="vehicle_color" 
                               class="form-input @error('vehicle_color') error @enderror" 
                               value="{{ old('vehicle_color') }}" required>
                        @error('vehicle_color')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label for="license_plate" class="block text-sm font-medium text-gray-700 mb-2">
                            Biển số xe <span class="required">*</span>
                        </label>
                        <input type="text" id="license_plate" name="license_plate" 
                               class="form-input @error('license_plate') error @enderror" 
                               value="{{ old('license_plate') }}" required>
                        @error('license_plate')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="driver_license_number" class="block text-sm font-medium text-gray-700 mb-2">
                            Số GPLX <span class="required">*</span>
                        </label>
                        <input type="text" id="driver_license_number" name="driver_license_number" 
                               class="form-input @error('driver_license_number') error @enderror" 
                               value="{{ old('driver_license_number') }}" required>
                        @error('driver_license_number')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="driver_license_image" class="block text-sm font-medium text-gray-700 mb-2">
                            Ảnh GPLX <span class="required">*</span>
                        </label>
                        <div class="file-input-wrapper">
                            <input type="file" id="driver_license_image" name="driver_license_image" 
                                   class="file-input @error('driver_license_image') error @enderror" 
                                   accept="image/*" required>
                            <label for="driver_license_image" class="file-input-label" id="driver_license_image_label">
                                <i class="fas fa-cloud-upload-alt mr-2"></i>Chọn ảnh GPLX
                            </label>
                        </div>
                        @error('driver_license_image')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="vehicle_registration_image" class="block text-sm font-medium text-gray-700 mb-2">
                            Ảnh đăng ký xe <span class="required">*</span>
                        </label>
                        <div class="file-input-wrapper">
                            <input type="file" id="vehicle_registration_image" name="vehicle_registration_image" 
                                   class="file-input @error('vehicle_registration_image') error @enderror" 
                                   accept="image/*" required>
                            <label for="vehicle_registration_image" class="file-input-label" id="vehicle_registration_image_label">
                                <i class="fas fa-cloud-upload-alt mr-2"></i>Chọn ảnh đăng ký xe
                            </label>
                        </div>
                        @error('vehicle_registration_image')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <!-- Thông tin ngân hàng -->
            <div class="form-section p-6">
                <h3 class="section-title text-xl mb-6">
                    <i class="fas fa-university mr-3 text-blue-600"></i>Thông tin tài khoản ngân hàng
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="bank_name" class="block text-sm font-medium text-gray-700 mb-2">
                            Tên ngân hàng <span class="required">*</span>
                        </label>
                        <input type="text" id="bank_name" name="bank_name" 
                               class="form-input @error('bank_name') error @enderror" 
                               value="{{ old('bank_name') }}" required>
                        @error('bank_name')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="bank_account_number" class="block text-sm font-medium text-gray-700 mb-2">
                            Số tài khoản <span class="required">*</span>
                        </label>
                        <input type="text" id="bank_account_number" name="bank_account_number" 
                               class="form-input @error('bank_account_number') error @enderror" 
                               value="{{ old('bank_account_number') }}" required>
                        @error('bank_account_number')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="bank_account_name" class="block text-sm font-medium text-gray-700 mb-2">
                            Tên chủ tài khoản <span class="required">*</span>
                        </label>
                        <input type="text" id="bank_account_name" name="bank_account_name" 
                               class="form-input @error('bank_account_name') error @enderror" 
                               value="{{ old('bank_account_name') }}" required>
                        @error('bank_account_name')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <!-- Người liên hệ khẩn cấp -->
            <div class="form-section p-6">
                <h3 class="section-title text-xl mb-6">
                    <i class="fas fa-first-aid mr-3 text-blue-600"></i>Thông tin liên hệ khẩn cấp
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="emergency_contact_name" class="block text-sm font-medium text-gray-700 mb-2">
                            Tên người liên hệ <span class="required">*</span>
                        </label>
                        <input type="text" id="emergency_contact_name" name="emergency_contact_name" 
                               class="form-input @error('emergency_contact_name') error @enderror" 
                               value="{{ old('emergency_contact_name') }}" required>
                        @error('emergency_contact_name')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="emergency_contact_phone" class="block text-sm font-medium text-gray-700 mb-2">
                            Số điện thoại <span class="required">*</span>
                        </label>
                        <input type="tel" id="emergency_contact_phone" name="emergency_contact_phone" 
                               class="form-input @error('emergency_contact_phone') error @enderror" 
                               value="{{ old('emergency_contact_phone') }}" required>
                        @error('emergency_contact_phone')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="emergency_contact_relationship" class="block text-sm font-medium text-gray-700 mb-2">
                            Mối quan hệ <span class="required">*</span>
                        </label>
                        <input type="text" id="emergency_contact_relationship" name="emergency_contact_relationship" 
                               class="form-input @error('emergency_contact_relationship') error @enderror" 
                               value="{{ old('emergency_contact_relationship') }}" required>
                        @error('emergency_contact_relationship')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <!-- Điều khoản và điều kiện -->
            <div class="form-section p-6">
                <div class="checkbox-wrapper">
                    <input type="checkbox" id="terms_accepted" name="terms_accepted" 
                           class="checkbox-input @error('terms_accepted') error @enderror" required>
                    <label for="terms_accepted" class="text-sm text-gray-700">
                        Tôi đồng ý với <button type="button" onclick="showTermsModal()" class="text-blue-600 underline hover:text-blue-800">Điều khoản và Điều kiện</button> của DevFoods
                    </label>
                </div>
                @error('terms_accepted')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>
            
            <!-- Cloudflare Turnstile CAPTCHA -->
            <div class="form-group mt-4">
                <div class="cf-turnstile mt-2" 
                     data-sitekey="{{ config('turnstile.site_key') }}"
                     data-theme="light"
                     data-size="normal"
                     data-callback="onTurnstileCallback"
                     data-error-callback="onTurnstileError"
                     data-expired-callback="onTurnstileExpired">
                </div>
                @error('cf-turnstile-response')
                    <div class="text-danger mt-2"><small>{{ $message }}</small></div>
                @enderror
            </div>
            
            <div class="text-center">
                <button type="submit" class="btn-primary text-lg opacity-50 cursor-not-allowed" data-turnstile-required disabled>Gửi đơn đăng ký</button>
            </div>
        </form>
    </div>
</main>

<!-- Modal: Điều khoản và điều kiện -->
<div id="termsModal" class="modal-overlay hidden">
    <div class="modal-content">
        <div class="modal-header">
            <div class="flex justify-between items-center">
                <h5 class="text-xl font-semibold">Điều khoản và Điều kiện</h5>
                <button type="button" onclick="hideTermsModal()" class="text-gray-500 hover:text-gray-700 text-2xl font-bold">
                    &times;
                </button>
            </div>
        </div>
        <div class="modal-body">
            <h4 class="font-semibold text-lg mb-3">1. Đối tác tài xế</h4>
            <p class="mb-4">Bằng việc đăng ký và sử dụng dịch vụ của chúng tôi, bạn đồng ý rằng bạn là đối tác độc lập và không phải là nhân viên của DevFoods.</p>
            
            <h4 class="font-semibold text-lg mb-3">2. Trách nhiệm</h4>
            <p class="mb-4">Bạn chịu trách nhiệm đảm bảo xe của bạn đáp ứng các tiêu chuẩn an toàn và luật pháp địa phương. Bạn cũng chịu trách nhiệm về hành vi của mình trong quá trình giao hàng.</p>
            
            <h4 class="font-semibold text-lg mb-3">3. Thanh toán</h4>
            <p class="mb-4">DevFoods sẽ thanh toán cho bạn theo các điều khoản thanh toán đã thỏa thuận. Bạn chịu trách nhiệm về thuế và các khoản phí khác liên quan đến thu nhập của bạn.</p>
            
            <h4 class="font-semibold text-lg mb-3">4. Bảo mật thông tin</h4>
            <p class="mb-4">DevFoods cam kết bảo vệ thông tin cá nhân của bạn theo chính sách bảo mật của chúng tôi. Thông tin của bạn sẽ chỉ được sử dụng cho mục đích vận hành dịch vụ.</p>
            
            <h4 class="font-semibold text-lg mb-3">5. Chấm dứt hợp tác</h4>
            <p class="mb-0">Cả hai bên đều có quyền chấm dứt mối quan hệ hợp tác này bất cứ lúc nào, với hoặc không có lý do.</p>
        </div>
        <div class="modal-footer">
            <button type="button" onclick="hideTermsModal()" class="btn-primary">Đã hiểu</button>
        </div>
    </div>
</div>

<script>
// Hiển thị tên file khi chọn file upload
document.addEventListener('DOMContentLoaded', function() {
    // Handle file input changes
    const fileInputs = document.querySelectorAll('.file-input');
    fileInputs.forEach(input => {
        input.addEventListener('change', function() {
            const fileName = this.files[0]?.name || 'Chọn file';
            const label = document.getElementById(this.id + '_label');
            if (label) {
                label.innerHTML = `<i class="fas fa-check mr-2"></i>${fileName}`;
                label.style.borderColor = '#10b981';
                label.style.backgroundColor = '#ecfdf5';
                label.style.color = '#047857';
            }
        });
    });

    // Load provinces on page load
    loadProvinces();
    
    // Handle province selection change
    const citySelect = document.getElementById('city');
    const districtSelect = document.getElementById('district');
    
    if (citySelect && districtSelect) {
        citySelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const provinceCode = selectedOption.dataset.code;
            
            // Reset district dropdown
            districtSelect.innerHTML = '<option value="">-- Chọn Quận/Huyện --</option>';
            
            if (provinceCode) {
                loadDistricts(provinceCode);
            }
        });
    }
});

// Load provinces on page load - Chỉ hiển thị Hà Nội
function loadProvinces() {
    const citySelect = document.getElementById('city');
    if (!citySelect) return;
    
    // Chỉ hiển thị Hà Nội
    citySelect.innerHTML = '<option value="Hà Nội" data-code="1" selected>Hà Nội</option>';
    
    // Tự động load quận/huyện của Hà Nội
    loadDistricts('1');
}

// Load districts for selected province
function loadDistricts(provinceCode) {
    const districtSelect = document.getElementById('district');
    if (!districtSelect) return;
    
    // Show loading state
    districtSelect.innerHTML = '<option value="">Đang tải...</option>';
    
    // Sử dụng file JSON thay vì API
    fetch('/data/hanoi-districts.json')
        .then(response => response.json())
        .then(data => {
            // Clear loading state
            districtSelect.innerHTML = '<option value="">-- Chọn Quận/Huyện --</option>';
            
            // Add district options
            if (data && data.districts && Array.isArray(data.districts)) {
                data.districts.forEach(district => {
                    const option = document.createElement('option');
                    option.value = district.name;
                    option.textContent = district.name;
                    option.dataset.code = district.code;
                    districtSelect.appendChild(option);
                });
            } else {
                console.error('Invalid districts data format');
            }
        })
        .catch(error => {
            console.error('Error loading districts:', error);
            districtSelect.innerHTML = '<option value="">Không thể tải dữ liệu</option>';
        });}

// Modal functions
function showTermsModal() {
    const modal = document.getElementById('termsModal');
    if (modal) {
        modal.classList.remove('hidden');
        modal.style.display = 'flex';
        // Prevent body scroll
        document.body.style.overflow = 'hidden';
    }
}

function hideTermsModal() {
    const modal = document.getElementById('termsModal');
    if (modal) {
        modal.classList.add('hidden');
        modal.style.display = 'none';
        // Restore body scroll
        document.body.style.overflow = 'auto';
    }
}

// Close modal when clicking outside or pressing Escape
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('termsModal');
    
    if (modal) {
        // Close when clicking outside modal content
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                hideTermsModal();
            }
        });
        
        // Close when pressing Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
                hideTermsModal();
            }
        });
    }
});
</script>

<!-- Sample Data Script for Development -->
<script src="{{ asset('js/driver-application-sample.js') }}"></script>

<!-- Turnstile Script and Callbacks -->
<script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
<script>
    let turnstileToken = null;

    // Turnstile callbacks
    window.onTurnstileCallback = function(token) {
        turnstileToken = token;
        console.log('Turnstile completed successfully');
        // Enable submit button
        const submitBtn = document.querySelector('button[type="submit"][data-turnstile-required]');
        if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
        }
    };

    window.onTurnstileError = function() {
        turnstileToken = null;
        console.error('Turnstile error occurred');
        // Disable submit button
        const submitBtn = document.querySelector('button[type="submit"][data-turnstile-required]');
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
        }
        alert('Xác minh bảo mật thất bại. Vui lòng thử lại.');
    };

    window.onTurnstileExpired = function() {
        turnstileToken = null;
        console.log('Turnstile expired');
        // Disable submit button
        const submitBtn = document.querySelector('button[type="submit"][data-turnstile-required]');
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
        }
        alert('Xác minh bảo mật đã hết hạn. Vui lòng thực hiện lại.');
    };

    // Form validation
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form');
        if (form) {
            form.addEventListener('submit', function(e) {
                const submitBtn = this.querySelector('button[type="submit"][data-turnstile-required]');
                if (submitBtn && !turnstileToken) {
                    e.preventDefault();
                    alert('Vui lòng hoàn thành xác minh bảo mật');
                    return false;
                }
            });
        }
    });
</script>

@endsection