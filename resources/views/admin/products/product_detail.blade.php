@extends('layouts/admin/contentLayoutMaster')
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h1>Product Detail</h1>
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Product Name: {{ $product->name }}</h5>
                    <p class="card-text">Description: {{ $product->description }}</p>
                    <p class="card-text">Price: {{ $product->price }}</p>
                    <p class="card-text">Stock: {{ $product->stock }}</p>
                    <p class="card-text">Category: {{ $product->category->name ?? 'N/A' }}</p>
                </div>
            </div>
@endsection