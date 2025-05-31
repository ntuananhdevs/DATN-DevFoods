@extends('layouts.customer.fullLayoutMaster')

@section('content')
<head>
    <link rel="stylesheet" href="{{ asset('css/customer/hiring.css') }}">
    <style>
    /* Basic styles until we create a separate CSS file */
    .hiring-application-container {
        font-family: 'Roboto', sans-serif;
        background-color: #f8f9fa;
        min-height: 100vh;
    }
    
    .application-title {
        font-size: 2.2rem;
        font-weight: 700;
        color: #2c3e50;
        margin-bottom: 10px;
    }
    
    .application-subtitle {
        font-size: 1.1rem;
        color: #7f8c8d;
        margin-bottom: 0;
    }
    
    .application-form-wrapper {
        background-color: #fff;
        border-radius: 10px;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.05);
        padding: 40px;
    }
    
    .form-section {
        margin-bottom: 40px;
        padding-bottom: 30px;
        border-bottom: 1px solid #eee;
    }
    
    .form-section:last-child {
        border-bottom: none;
        margin-bottom: 20px;
    }
    
    .section-title {
        font-size: 1.3rem;
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 25px;
        padding-bottom: 10px;
        border-bottom: 2px solid #3498db;
        display: inline-block;
    }
    
    .form-control {
        height: calc(2.5em + 0.75rem + 2px);
    }
    
    .custom-file-label {
        height: calc(2.5em + 0.75rem + 2px);
        padding-top: 0.7rem;
    }
    
    .form-action {
        margin-top: 40px;
    }
    
    .btn-primary {
        background-color: #27ae60;
        border-color: #27ae60;
        padding: 12px 30px;
        font-weight: 600;
    }
    
    .btn-primary:hover {
        background-color: #229954;
        border-color: #229954;
    }
    
    @media (max-width: 768px) {
        .application-form-wrapper {
            padding: 25px;
        }
    }
    </style>
</head>

@if (session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif

@if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

<main>
<div class="hiring-application-container">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-10"> 
                <div class="application-header text-center mb-5">
                    <h1 class="application-title">Đăng ký trở thành đối tác tài xế</h1>
                    <p class="application-subtitle">Vui lòng điền đầy đủ thông tin dưới đây để hoàn tất đơn đăng ký</p>
                </div>

                <div class="application-form-wrapper">
                    <form action="{{ route('driver.application.submit') }}" method="POST" enctype="multipart/form-data" class="application-form">
                        @csrf
                        
                        <!-- Thông tin cá nhân -->
                        <div class="form-section">
                            <h3 class="section-title"><i class="fas fa-user mr-2"></i> Thông tin cá nhân</h3>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="full_name">Họ và tên <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('full_name') is-invalid @enderror" id="full_name" name="full_name" value="{{ old('full_name') }}" required>
                                        @error('full_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="date_of_birth">Ngày sinh <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control @error('date_of_birth') is-invalid @enderror" id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth') }}" required>
                                        @error('date_of_birth')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="gender">Giới tính <span class="text-danger">*</span></label>
                                        <select class="form-control @error('gender') is-invalid @enderror" id="gender" name="gender" required>
                                            <option value="">-- Chọn giới tính --</option>
                                            <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Nam</option>
                                            <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Nữ</option>
                                            <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Khác</option>
                                        </select>
                                        @error('gender')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="profile_image">Ảnh chân dung <span class="text-danger">*</span></label>
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input @error('profile_image') is-invalid @enderror" id="profile_image" name="profile_image" accept="image/*" required>
                                            <label class="custom-file-label" for="profile_image">Chọn ảnh</label>
                                            @error('profile_image')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <small class="form-text text-muted">Tải lên ảnh chân dung rõ mặt, không đeo kính và nón.</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Thông tin liên hệ -->
                        <div class="form-section">
                            <h3 class="section-title"><i class="fas fa-phone-alt mr-2"></i> Thông tin liên hệ</h3>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="phone_number">Số điện thoại <span class="text-danger">*</span></label>
                                        <input type="tel" class="form-control @error('phone_number') is-invalid @enderror" id="phone_number" name="phone_number" value="{{ old('phone_number') }}" required>
                                        @error('phone_number')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="email">Email <span class="text-danger">*</span></label>
                                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required>
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="address">Địa chỉ <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('address') is-invalid @enderror" id="address" name="address" value="{{ old('address') }}" required>
                                        @error('address')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="city">Thành phố <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('city') is-invalid @enderror" id="city" name="city" value="{{ old('city') }}" required>
                                        @error('city')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="district">Quận/Huyện <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('district') is-invalid @enderror" id="district" name="district" value="{{ old('district') }}" required>
                                        @error('district')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Thông tin CMND/CCCD -->
                        <div class="form-section">
                            <h3 class="section-title"><i class="fas fa-id-card mr-2"></i> Thông tin CMND/CCCD</h3>
                            
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="id_card_number">Số CMND/CCCD <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('id_card_number') is-invalid @enderror" id="id_card_number" name="id_card_number" value="{{ old('id_card_number') }}" required>
                                        @error('id_card_number')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="id_card_issue_date">Ngày cấp <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control @error('id_card_issue_date') is-invalid @enderror" id="id_card_issue_date" name="id_card_issue_date" value="{{ old('id_card_issue_date') }}" required>
                                        @error('id_card_issue_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="id_card_issue_place">Nơi cấp <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('id_card_issue_place') is-invalid @enderror" id="id_card_issue_place" name="id_card_issue_place" value="{{ old('id_card_issue_place') }}" required>
                                        @error('id_card_issue_place')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="id_card_front_image">Ảnh mặt trước CMND/CCCD <span class="text-danger">*</span></label>
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input @error('id_card_front_image') is-invalid @enderror" id="id_card_front_image" name="id_card_front_image" accept="image/*" required>
                                            <label class="custom-file-label" for="id_card_front_image">Chọn ảnh</label>
                                            @error('id_card_front_image')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="id_card_back_image">Ảnh mặt sau CMND/CCCD <span class="text-danger">*</span></label>
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input @error('id_card_back_image') is-invalid @enderror" id="id_card_back_image" name="id_card_back_image" accept="image/*" required>
                                            <label class="custom-file-label" for="id_card_back_image">Chọn ảnh</label>
                                            @error('id_card_back_image')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Thông tin phương tiện -->
                        <div class="form-section">
                            <h3 class="section-title"><i class="fas fa-motorcycle mr-2"></i> Thông tin phương tiện</h3>
                            
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="vehicle_type">Loại phương tiện <span class="text-danger">*</span></label>
                                        <select class="form-control @error('vehicle_type') is-invalid @enderror" id="vehicle_type" name="vehicle_type" required>
                                            <option value="">-- Chọn loại phương tiện --</option>
                                            <option value="motorcycle" {{ old('vehicle_type') == 'motorcycle' ? 'selected' : '' }}>Xe máy</option>
                                            <option value="car" {{ old('vehicle_type') == 'car' ? 'selected' : '' }}>Ô tô</option>
                                            <option value="bicycle" {{ old('vehicle_type') == 'bicycle' ? 'selected' : '' }}>Xe đạp</option>
                                        </select>
                                        @error('vehicle_type')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="vehicle_model">Dòng xe <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('vehicle_model') is-invalid @enderror" id="vehicle_model" name="vehicle_model" value="{{ old('vehicle_model') }}" required>
                                        @error('vehicle_model')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="vehicle_color">Màu xe <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('vehicle_color') is-invalid @enderror" id="vehicle_color" name="vehicle_color" value="{{ old('vehicle_color') }}" required>
                                        @error('vehicle_color')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="license_plate">Biển số xe <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('license_plate') is-invalid @enderror" id="license_plate" name="license_plate" value="{{ old('license_plate') }}" required>
                                        @error('license_plate')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="driver_license_number">Số GPLX <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('driver_license_number') is-invalid @enderror" id="driver_license_number" name="driver_license_number" value="{{ old('driver_license_number') }}" required>
                                        @error('driver_license_number')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="driver_license_image">Ảnh GPLX <span class="text-danger">*</span></label>
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input @error('driver_license_image') is-invalid @enderror" id="driver_license_image" name="driver_license_image" accept="image/*" required>
                                            <label class="custom-file-label" for="driver_license_image">Chọn ảnh</label>
                                            @error('driver_license_image')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="vehicle_registration_image">Ảnh đăng ký xe <span class="text-danger">*</span></label>
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input @error('vehicle_registration_image') is-invalid @enderror" id="vehicle_registration_image" name="vehicle_registration_image" accept="image/*" required>
                                            <label class="custom-file-label" for="vehicle_registration_image">Chọn ảnh</label>
                                            @error('vehicle_registration_image')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Thông tin ngân hàng -->
                        <div class="form-section">
                            <h3 class="section-title"><i class="fas fa-university mr-2"></i> Thông tin tài khoản ngân hàng</h3>
                            
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="bank_name">Tên ngân hàng <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('bank_name') is-invalid @enderror" id="bank_name" name="bank_name" value="{{ old('bank_name') }}" required>
                                        @error('bank_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="bank_account_number">Số tài khoản <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('bank_account_number') is-invalid @enderror" id="bank_account_number" name="bank_account_number" value="{{ old('bank_account_number') }}" required>
                                        @error('bank_account_number')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="bank_account_name">Tên chủ tài khoản <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('bank_account_name') is-invalid @enderror" id="bank_account_name" name="bank_account_name" value="{{ old('bank_account_name') }}" required>
                                        @error('bank_account_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Người liên hệ khẩn cấp -->
                        <div class="form-section">
                            <h3 class="section-title"><i class="fas fa-first-aid mr-2"></i> Thông tin liên hệ khẩn cấp</h3>
                            
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="emergency_contact_name">Tên người liên hệ <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('emergency_contact_name') is-invalid @enderror" id="emergency_contact_name" name="emergency_contact_name" value="{{ old('emergency_contact_name') }}" required>
                                        @error('emergency_contact_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="emergency_contact_phone">Số điện thoại <span class="text-danger">*</span></label>
                                        <input type="tel" class="form-control @error('emergency_contact_phone') is-invalid @enderror" id="emergency_contact_phone" name="emergency_contact_phone" value="{{ old('emergency_contact_phone') }}" required>
                                        @error('emergency_contact_phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="emergency_contact_relationship">Mối quan hệ <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('emergency_contact_relationship') is-invalid @enderror" id="emergency_contact_relationship" name="emergency_contact_relationship" value="{{ old('emergency_contact_relationship') }}" required>
                                        @error('emergency_contact_relationship')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Điều khoản và điều kiện -->
                        <div class="form-section">
                            <div class="form-group">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input @error('terms_accepted') is-invalid @enderror" id="terms_accepted" name="terms_accepted" required>
                                    <label class="custom-control-label" for="terms_accepted">
                                        Tôi đồng ý với <a href="#" data-toggle="modal" data-target="#termsModal">Điều khoản và Điều kiện</a> của DevFoods
                                    </label>
                                    @error('terms_accepted')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-action text-center">
                            <button type="submit" class="btn btn-primary btn-lg">Gửi đơn đăng ký</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Điều khoản và điều kiện -->
<div class="modal fade" id="termsModal" tabindex="-1" aria-labelledby="termsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="termsModalLabel">Điều khoản và Điều kiện</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <h4>1. Đối tác tài xế</h4>
                <p>Bằng việc đăng ký và sử dụng dịch vụ của chúng tôi, bạn đồng ý rằng bạn là đối tác độc lập và không phải là nhân viên của DevFoods.</p>
                
                <h4>2. Trách nhiệm</h4>
                <p>Bạn chịu trách nhiệm đảm bảo xe của bạn đáp ứng các tiêu chuẩn an toàn và luật pháp địa phương. Bạn cũng chịu trách nhiệm về hành vi của mình trong quá trình giao hàng.</p>
                
                <h4>3. Thanh toán</h4>
                <p>DevFoods sẽ thanh toán cho bạn theo các điều khoản thanh toán đã thỏa thuận. Bạn chịu trách nhiệm về thuế và các khoản phí khác liên quan đến thu nhập của bạn.</p>
                
                <h4>4. Bảo mật thông tin</h4>
                <p>DevFoods cam kết bảo vệ thông tin cá nhân của bạn theo chính sách bảo mật của chúng tôi. Thông tin của bạn sẽ chỉ được sử dụng cho mục đích vận hành dịch vụ.</p>
                
                <h4>5. Chấm dứt hợp tác</h4>
                <p>Cả hai bên đều có quyền chấm dứt mối quan hệ hợp tác này bất cứ lúc nào, với hoặc không có lý do.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">Đã hiểu</button>
            </div>
        </div>
    </div>
</div>
</main>

<script>
    // Hiển thị tên file khi chọn file upload
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.custom-file-input').forEach(input => {
            input.addEventListener('change', function() {
                const fileName = this.files[0].name;
                const label = this.nextElementSibling;
                label.textContent = fileName;
            });
        });
    });
</script>
@endsection 