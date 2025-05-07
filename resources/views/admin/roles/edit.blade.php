@extends('layouts/admin/contentLayoutMaster')

@section('content')
    <div class="container">
        <h1 class="my-4 text-center" style="font-size: 1.5rem;">Chỉnh sửa Role</h1>

        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        @if ($role)
            <!-- Kiểm tra nếu $role không null -->
            @include('admin.roles.form', [
                'action' => route('admin.roles.update', $role->id),
                'isEdit' => true,
                'role' => $role,
            ])
        @else
            <div class="alert alert-warning">Role không tồn tại.</div>
        @endif

    </div>
@endsection
