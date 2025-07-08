@extends('layouts.profile.fullLayoutProfile')

@section('title', 'FastFood - ' . $user->full_name)

@section('profile_content')
    @include('partials.profile.overview')
    @include('partials.profile.orders')
    @include('partials.profile.addresses')
    @include('partials.profile.favorites')
    @include('partials.profile.rewards')
@endsection
