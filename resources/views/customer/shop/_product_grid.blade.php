@php
    $search = request('search');
@endphp
@if($products->count())
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        @foreach($products as $product)
            @include('customer.shop._product_card', ['product' => $product])
        @endforeach
    </div>
@else
    @if($search)
        <div class="text-gray-400">Chúng tôi không thể tìm thấy sản phẩm nào phù hợp với từ khóa "<span class='font-semibold'>{{ $search }}</span>".</div>
    @else
        <div class="text-gray-400">Không có sản phẩm.</div>
    @endif
@endif 