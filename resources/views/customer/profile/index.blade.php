@extends('layouts.profile.fullLayoutProfile')

@section('title', 'FastFood - ' . $user->full_name)

@section('profile_content')
    <style>
        .container {
            max-width: 1280px;
            margin: 0 auto;
        }
    </style>
    <div class="container">
        @include('partials.profile.overview')
        @include('partials.profile.orders')
        @include('partials.profile.addresses')
        @include('partials.profile.favorites')
        @include('partials.profile.rewards')
    </div>
@endsection
