@extends('layouts.admin')

@section('content')
<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <h3 class="content-header-title">User Details</h3>
        </div>
    </div>

    <div class="content-body">
        <div class="card">
            <div class="card-content">
                <div class="card-body">
               
                    
                    <div class="row">
                        <div class="col-12">
                            <table class="table">
                                <tr>
                                    <th width="200">Username</th>
                                    <td>{{ $user->user_name }}</td>
                                </tr>
                                <tr>
                                    <th>Full Name</th>
                                    <td>{{ $user->full_name }}</td>
                                </tr>
                                <tr>
                                    <th>Email</th>
                                    <td>{{ $user->email }}</td>
                                </tr>
                                <tr>
                                    <th>Phone</th>
                                    <td>{{ $user->phone }}</td>
                                </tr>
                                <tr>
                                    <th>Balance</th>
                                    <td>${{ number_format($user->balance, 2) }}</td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td>
                                        <span class="badge badge-{{ $user->active ? 'success' : 'danger' }}">
                                            {{ $user->active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Created At</th>
                                    <td>{{ $user->created_at ? $user->created_at->format('Y-m-d H:i:s') : 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="mt-2">
                        <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-warning">Edit</a>
                        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Back to List</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection