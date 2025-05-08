@extends('layouts.admin')

@section('content')
    <div class="container">
        <h1 class="my-4">Thêm Role</h1>

        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        @include('admin.roles.form', [
            'action' => route('admin.roles.store'),
            'isEdit' => false,
            'role' => null,
        ])
    </div>
@endsection
