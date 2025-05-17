@extends('layouts.driver.master')

@section('content')
    @include('driver.map')
    @include('driver.income')
    @include('driver.services')
    @include('driver.inbox')
    @include('driver.profile')
    @include('driver.tripDetail')
    @include('driver.wallet')
    @include('driver.profileDetail')
    @include('driver.tripHistory')
@endsection

