@extends('layouts.admin')

@section('content')
    <div class="container">
        <h1 class="my-4">Chỉnh sửa Role</h1>

        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        @include('admin.roles.form', [
            'action' => route('admin.roles.update', $role->id),
            'isEdit' => true,
            'role' => $role,
        ])
    </div>
@endsection
