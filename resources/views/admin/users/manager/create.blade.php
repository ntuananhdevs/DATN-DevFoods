@extends('layouts.admin.contentLayoutMaster')

@section('content')
<style>
  :root {
    --primary: #6366f1;
    --primary-light: #eef2ff;
    --text-primary: #1e293b;
    --text-secondary: #64748b;
    --text-tertiary: #94a3b8;
    --bg-main: #f8fafc;
    --bg-card: #ffffff;
    --border-color: #e2e8f0;
    --border-light: #f1f5f9;
    --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    --radius-sm: 6px;
    --radius-md: 8px;
    --radius-lg: 12px;
    --transition-fast: all 0.2s ease;
  }


  .container {
    display: flex;
    gap: 1.5rem;
    max-width: 1700px;
    max-height: 1000px;
    margin: 0 auto;
  }

  .column {
    background-color: var(--bg-card);
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-md);
    padding: 1.5rem;
    flex: 1;
  }

  .column-left {
    flex: 3;
  }

  .column-right {
    flex: 2;
  }

  .form-group {
    margin-bottom: 1.25rem;
  }

  .form-row {
    display: flex;
    gap: 1rem;
    margin-bottom: 1.25rem;
  }

  .form-control {
    flex: 1;
  }

  label {
    display: block;
    font-size: 0.875rem;
    font-weight: 500;
    margin-bottom: 0.5rem;
    color: var(--text-primary);
  }

  input,
  select,
  textarea {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid var(--border-color);
    border-radius: var(--radius-sm);
    font-family: inherit;
    font-size: 0.875rem;
    color: var(--text-primary);
    background-color: var(--bg-main);
    transition: var(--transition-fast);
  }

  input:focus,
  select:focus,
  textarea:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
  }

  select {
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2364748b'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 0.75rem center;
    background-size: 1rem;
    padding-right: 2.5rem;
  }

  .toggle-container {
    display: flex;
    justify-content: space-between;
    align-items: center;
  }



  .date-time-container {
    display: flex;
    gap: 1rem;
  }

  .date-input,
  .time-input {
    position: relative;
    flex: 1;
  }

  .date-input i,
  .time-input i {
    position: absolute;
    left: 0.75rem;
    top: 50%;
    transform: translateY(-50%);
    color: var(--text-secondary);
    pointer-events: none;
  }

  .date-input input,
  .time-input input {
    padding-left: 2.5rem;
  }

  .upload-container {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 2rem;
    border: 2px dashed var(--border-color);
    border-radius: var(--radius-md);
    background-color: var(--bg-main);
    transition: var(--transition-fast);
    cursor: pointer;
    text-align: center;
  }

  .upload-container:hover {
    border-color: var(--primary);
    background-color: var(--primary-light);
  }

  .upload-icon {
    width: 48px;
    height: 48px;
    background-color: #dbeafe;
    border-radius: var(--radius-md);
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 1rem;
    color: var(--primary);
  }

  .upload-text {
    font-weight: 500;
    color: var(--primary);
    margin-bottom: 0.5rem;
  }

  .upload-description {
    font-size: 0.75rem;
    color: var(--text-secondary);
    margin-bottom: 0.5rem;
  }

  .upload-format {
    font-size: 0.75rem;
    color: var(--text-tertiary);
  }

  .section-title {
    font-size: 1rem;
    font-weight: 600;
    margin-bottom: 1rem;
    color: var(--text-primary);
  }

  .hint-text {
    font-size: 0.75rem;
    color: var(--text-tertiary);
    margin-top: 0.25rem;
  }

  .btn-container {
    display: flex;
    justify-content: flex-end;
    gap: 0.75rem;
    margin-top: 1.5rem;
  }

  .btn {
    padding: 0.625rem 1.25rem;
    border-radius: var(--radius-sm);
    font-weight: 500;
    font-size: 0.875rem;
    border: none;
    cursor: pointer;
    transition: var(--transition-fast);
  }

  .btn-secondary {
    background-color: var(--border-light);
    color: var(--text-secondary);
  }

  .btn-secondary:hover {
    background-color: var(--border-color);
    color: var(--text-primary);
  }

  .btn-primary {
    background-color: var(--primary);
    color: white;
  }

  .btn-primary:hover {
    background-color: #5558e6;
  }



  .is-invalid {
    border-color: #ef4444;
  }

  .invalid-feedback {
    color: #ef4444;
    font-size: 0.75rem;
    margin-top: 0.25rem;
    display: block;
    /* Thêm dòng này */
  }



  @media (max-width: 768px) {
    .container {
      flex-direction: column;
    }

    .form-row {
      flex-direction: column;
      gap: 1.25rem;
    }

    .date-time-container {
      flex-direction: column;
      gap: 1.25rem;
    }
  }
</style>

<div class="data-table-wrapper">
  <!-- Main Header -->
  <div class="data-table-main-header">
    <div class="data-table-brand">
      <div class="data-table-logo">
        <i class="fas fa-user-plus"></i>
      </div>
      <h1 class="data-table-title">Thêm Quản Lý</h1>
    </div>
  </div>

  <!-- Main Content -->
  <div class="container">
    <!-- Left Column -->
    <div class="column column-left">
      <div class="data-table-header" style="border-bottom: none; margin-left: -1.5rem; margin-right: -1.5rem;">
        <h2 class="data-table-card-title">Thông tin người dùng</h2>
      </div>
      <form action="{{ route('admin.users.managers.store') }}" method="POST" enctype="multipart/form-data" id="userForm">
        @csrf

        <div class="form-group">
          <label for="user_name">Tên đăng nhập</label>
          <input type="text" class="@error('user_name') is-invalid @enderror" id="user_name" name="user_name"
            value="{{ old('user_name') }}" placeholder="Tên đăng nhập">  
          @error('user_name')
          <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <div class="form-group">
          <label for="full_name">Họ và tên</label>
          <input type="text" class="@error('full_name') is-invalid @enderror" id="full_name" name="full_name"
            value="{{ old('full_name') }}" placeholder="Họ và tên">
          @error('full_name')
          <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <div class="form-group">
          <label for="email">Email</label>
          <input type="email" class="@error('email') is-invalid @enderror" id="email" name="email"
            value="{{ old('email') }}" placeholder="Email">
          @error('email')
          <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <div class="form-group">
          <label for="phone">Số điện thoại</label>
          <input type="tel" class="@error('phone') is-invalid @enderror" id="phone" name="phone"
            value="{{ old('phone') }}" placeholder="Số điện thoại">
          @error('phone')
          <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <div class="form-group">
          <label for="password">Mật khẩu</label>
          <input type="password" class="@error('password') is-invalid @enderror" id="password" name="password" placeholder="Mật khẩu">
          @error('password')
          <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <div class="form-group">
          <label for="password_confirmation">Xác nhận mật khẩu</label>
          <input type="password" class="@error('password_confirmation') is-invalid @enderror" id="password_confirmation"
            name="password_confirmation" placeholder="Xác nhận mật khẩu">
          @error('password_confirmation')
          <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

<div class="form-group" style="display: none;">
    <label for="role_ids">Vai trò</label>
    <select class="@error('role_ids') is-invalid @enderror" id="role_ids" name="role_ids[]">
        @foreach($roles as $role)
            <option value="{{ $role->id }}" 
                {{ $role->name === 'manager' ? 'selected' : '' }}>
                {{ $role->name }}
            </option>
        @endforeach
    </select>
    @error('role_ids')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
        <!-- File input ẩn để chứa dữ liệu avatar từ right column -->
        <input type="file" id="avatar-input" name="avatar" style="display: none;">

        <div class="btn-container">
          <button type="button" class="btn btn-secondary" onclick="window.history.back()">Hủy</button>
          <button type="submit" class="btn btn-primary">Tạo Quản Lý Mới </button>
        </div>
      </form>
    </div>

    <!-- Right Column -->
    <div class="column column-right">
      <div class="upload-container" onclick="document.getElementById('avatar').click()">
        <div id="avatar-preview">
          <div class="upload-icon">
            <i class="ri-image-line"></i>
          </div>
          <div class="upload-text">Upload Image</div>
          <div class="upload-description">Upload a profile image for this user.</div>
          <div class="upload-format">File format: jpeg, png (Recommended size: 600x600 (1:1))</div>
        </div>
        <input type="file" id="avatar" accept="image/*" style="display: none;" onchange="previewAndTransferAvatar(this)">
      </div>
      @error('avatar')
      <div class="invalid-feedback" style="display: block; margin-top: 0.5rem;">{{ $message }}</div>
      @enderror
    </div>
  </div>

  <script>
    function previewAndTransferAvatar(input) {
      if (input.files && input.files[0]) {
        // Đồng thời truyền dữ liệu file sang input ẩn trong form
        const hiddenInput = document.getElementById('avatar-input');
        const dataTransfer = new DataTransfer();
        dataTransfer.items.add(input.files[0]);
        hiddenInput.files = dataTransfer.files;

        // Hiển thị xem trước ảnh
        const reader = new FileReader();
        const previewContainer = document.getElementById('avatar-preview');

        reader.onload = function(e) {
          previewContainer.innerHTML = `
            <img src="${e.target.result}" alt="Avatar preview" style="max-width: 100%; max-height: 200px; border-radius: var(--radius-md);">
            <div class="upload-text" style="margin-top: 1rem;">Ảnh đã chọn</div>
            <div class="upload-description">Nhấp để chọn ảnh khác</div>
          `;
        }

        reader.readAsDataURL(input.files[0]);
      }
    }
  </script>
  @endsection

  