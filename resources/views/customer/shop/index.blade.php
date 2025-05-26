@extends('layouts.customer.fullLayoutMaster')

@section('title', 'FastFood - Thực Đơn')

@section('content')
<style>
    .container {
      max-width: 1280px;
      margin: 0 auto;
    }
</style>

@php
    $menuBanner = app('App\Http\Controllers\Customer\BannerController')->getBannersByPosition('menu');
@endphp
@include('components.banner', ['banners' => $menuBanner])

<div class="container mx-auto px-4 py-12">
    <h1 class="text-3xl font-bold text-center mb-8">Thực Đơn</h1>
    
    <!-- Danh sách sản phẩm -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        @forelse($products as $product)
            <div class="product-card bg-white rounded-lg shadow-sm overflow-hidden hover:shadow-md transition-shadow">
                <a href="{{ route('products.show', $product->id) }}" class="block relative">
                    @php
                        $primaryImage = $product->images->where('is_primary', true)->first();
                        $imageUrl = $primaryImage ? asset('storage/' . $primaryImage->img) : '/placeholder.svg?height=300&width=400';
                    @endphp
                    <img src="{{ $imageUrl }}" alt="{{ $product->name }}" class="w-full h-48 object-cover">
                    
                    @if($product->is_featured)
                        <span class="absolute top-2 right-10 bg-red-500 text-white px-2 py-1 rounded-full text-xs">Nổi bật</span>
                    @endif
                    
                    @if($product->created_at->diffInDays() <= 7)
                        <span class="absolute top-2 right-2 bg-green-500 text-white px-2 py-1 rounded-full text-xs">Mới</span>
                    @endif
                </a>
                <div class="p-4">
                    @if($product->reviews->count() > 0)
                        @php
                            $averageRating = $product->reviews->avg('rating');
                            $reviewCount = $product->reviews->count();
                        @endphp
                        <div class="flex items-center mb-2">
                            <div class="flex text-orange-400">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= floor($averageRating))
                                        <i class="fas fa-star"></i>
                                    @elseif($i <= ceil($averageRating))
                                        <i class="fas fa-star-half-alt"></i>
                                    @else
                                        <i class="far fa-star"></i>
                                    @endif
                                @endfor
                            </div>
                            <span class="text-sm text-gray-500 ml-1">({{ $reviewCount }})</span>
                        </div>
                    @endif
                    
                    <h3 class="font-bold text-lg mb-1">{{ $product->name }}</h3>
                    <p class="text-gray-600 text-sm mb-3 line-clamp-2">
                        {{ $product->short_description ?? Str::limit($product->description, 80) }}
                    </p>
                    <div class="flex justify-between items-center">
                        <span class="font-bold text-orange-500">{{ number_format($product->base_price, 0, ',', '.') }}₫</span>
                        <button onclick="addToCart({{ $product->id }})" 
                                class="bg-orange-500 hover:bg-orange-600 text-white rounded-full w-8 h-8 flex items-center justify-center transition-colors">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-12">
                <div class="text-gray-500 text-lg mb-4">
                    <i class="fas fa-utensils text-4xl mb-4"></i>
                    <p>Chưa có sản phẩm nào</p>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="flex items-center justify-between px-4 py-4 border-t">
        <div class="text-sm text-muted-foreground">
            Hiển thị <span id="paginationStart">{{ $products->firstItem() }}</span> đến <span id="paginationEnd">{{ $products->lastItem() }}</span> của <span id="paginationTotal">{{ $products->total() }}</span> mục
        </div>
        <div class="flex items-center space-x-2" id="paginationControls">
            @unless($products->onFirstPage())
            <button class="h-8 w-8 rounded-md p-0 text-muted-foreground hover:bg-muted" onclick="changePage({{ $products->currentPage() - 1 }})">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4 mx-auto">
                    <path d="m15 18-6-6 6-6"></path>
                </svg>
            </button>
            @endunless

            @foreach ($products->getUrlRange(1, $products->lastPage()) as $page => $url)
            <button class="h-8 min-w-8 rounded-md px-2 text-xs font-medium {{ $products->currentPage() == $page ? 'bg-primary text-primary-foreground' : 'hover:bg-muted' }}" onclick="changePage({{ $page }})">
                {{ $page }}
            </button>
            @endforeach

            @unless($products->currentPage() === $products->lastPage())
            <button class="h-8 w-8 rounded-md p-0 text-muted-foreground hover:bg-muted" onclick="changePage({{ $products->currentPage() + 1 }})">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4 mx-auto">
                    <path d="m9 18 6-6-6-6"></path>
                </svg>
            </button>
            @endunless
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <script>
        function addToCart(productId) {
            console.log('Adding product to cart:', productId);
            // Implement add to cart functionality here
        }

        function changePage(page) {
            const urlParams = new URLSearchParams(window.location.search);
            urlParams.set('page', page);
            window.location.href = `${window.location.pathname}?${urlParams.toString()}`;
        }
    </script>
@endsection