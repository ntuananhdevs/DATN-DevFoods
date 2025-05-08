@extends('layouts.admin.auth')

@section('content')
<div class="container mt-5">
    <h2>Đăng nhập quản trị</h2>

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    @if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif

    <form method="POST" action="{{ route('admin.login.submit') }}">
        @csrf
        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control" required value="{{ old('email') }}">
        </div>
        <div class="mb-3">
            <label>Mật khẩu</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <div class="mb-3 form-check">
            <input type="checkbox" name="remember" class="form-check-input" id="remember">
            <label class="form-check-label" for="remember">Ghi nhớ đăng nhập</label>
        </div>
        <button type="submit" class="btn btn-primary">Đăng nhập</button>
    </form>
</div>
@endsection
