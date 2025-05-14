@extends('layouts/admin/contentLayoutMaster')

@section('content')
    <div class="container fs-5">
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
