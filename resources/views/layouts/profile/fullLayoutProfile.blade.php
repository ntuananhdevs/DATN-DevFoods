@extends('layouts.customer.fullLayoutMaster')

@section('content')
<style>
    html {
    scroll-padding-top: 80px;
}
</style>
    @include('partials.profile.header')
    <div class="container mx-auto px-4 py-8 flex flex-col lg:flex-row gap-8">
        @include('partials.profile.sidebar')
        <div class="lg:w-3/4">
            @yield('profile_content')
        </div>
    </div>
@endsection