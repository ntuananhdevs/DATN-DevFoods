
@extends('layouts.admin.contentLayoutMaster')

@section('title', 'Users')

@section('vendor-style')
        {{-- vendor css files --}}
        <link rel="stylesheet" href="{{ asset(mix('vendors/css/charts/apexcharts.css')) }}">
@endsection
@section('page-style')
        {{-- Page css files --}}
        <link rel="stylesheet" href="{{ asset(mix('css/pages/card-analytics.css')) }}">
@endsection
@section('content')

<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <h3 class="content-header-title">Edit User</h3>
        </div>
    </div>

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <div class="alert-body">
            <h4 class="alert-heading">
                <i class="feather icon-alert-triangle mr-1"></i>
                Lỗi khi cập nhật người dùng
            </h4>
            <p>{{ session('error') }}</p>
            @if($errors->any())
                <hr>
                <div class="alert-details">
                    <strong>Chi tiết lỗi:</strong>
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif

    <div class="content-body">
        <div class="card">
            <div class="card-content">
                <div class="card-body">
                    <form action="{{ route('admin.users.update', $user->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label for="user_name">Username</label>
                            <input type="text" class="form-control @error('user_name') is-invalid @enderror" 
                                   id="user_name" name="user_name" value="{{ old('user_name', $user->user_name) }}">
                            @error('user_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="full_name">Full Name</label>
                            <input type="text" class="form-control @error('full_name') is-invalid @enderror" 
                                   id="full_name" name="full_name" value="{{ old('full_name', $user->full_name) }}">
                            @error('full_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                   id="email" name="email" value="{{ old('email', $user->email) }}">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="phone">Phone</label>
                            <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                   id="phone" name="phone" value="{{ old('phone', $user->phone) }}">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="avatar">Ảnh đại diện</label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input @error('avatar') is-invalid @enderror" 
                                       id="avatar" name="avatar" accept="image/*" onchange="previewImage(this);">
                                <label class="custom-file-label" for="avatar">
                                    {{ $user->avatar ? basename($user->avatar) : 'Chọn ảnh' }}
                                </label>
                            </div>
                            @error('avatar')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <div class="mt-2">
                                @if($user->avatar)
                                    <img id="preview" src="{{ asset('storage/'.$user->avatar) }}" 
                                         alt="Current avatar" style="max-width: 200px;">
                                @else
                                    <img id="preview" src="#" alt="Preview" 
                                         style="max-width: 200px; display: none;">
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="password">New Password (leave blank to keep current)</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                   id="password" name="password">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="password_confirmation">Xác nhận mật khẩu mới</label>
                            <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror" 
                                   id="password_confirmation" name="password_confirmation">
                            @error('password_confirmation')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="active">Status</label>
                            <select class="form-control" id="active" name="active">
                                <option value="1" {{ old('active', $user->active) ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ !old('active', $user->active) ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>

                     
                        <button type="submit" class="btn btn-primary">Update User</button>
                        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('vendor-script')
{{-- vendor files --}}
        <script src="{{ asset(mix('vendors/js/charts/apexcharts.min.js')) }}"></script>
@endsection
@section('page-script')
        {{-- Page js files --}}
        <script src="{{ asset(mix('js/scripts/pages/dashboard-ecommerce.js')) }}"></script>
@endsection

