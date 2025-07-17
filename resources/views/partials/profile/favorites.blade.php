<section id="favorites" class="mb-10">
    <h2 class="text-2xl font-bold mb-6">Món Ăn Yêu Thích</h2>
    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
        {{-- DYNAMIC FAVORITES --}}
        @forelse($favoriteProducts as $favorite)
            @if ($favorite->product)
                <div class="bg-white rounded-xl shadow-sm overflow-hidden group">
                    <a href="{{ route('products.show', $favorite->product->slug) }}"
                        class="block relative h-48"><img
                            src="{{ $favorite->product->primaryImage ? Storage::disk('s3')->url($favorite->product->primaryImage->img) : asset('images/default-product.png') }}"
                            alt="{{ $favorite->product->name }}" class="w-full h-full object-cover"></a>
                    <div class="p-4">
                        <h3 class="font-bold mb-1"><a
                                href="{{ route('products.show', $favorite->product->slug) }}"
                                class="hover:text-orange-500">{{ $favorite->product->name }}</a></h3>
                        <p class="text-gray-500 text-sm mb-2 h-10">
                            {{ Str::limit($favorite->product->short_description, 60) }}</p>
                        <div class="flex justify-between items-center">
                            <span
                                class="font-bold text-orange-500">{{ number_format($favorite->product->base_price, 0, ',', '.') }}đ</span>
                            <button
                                class="bg-orange-500 hover:bg-orange-600 text-white px-3 py-1 rounded-lg text-sm transition-colors">Thêm
                                vào giỏ</button>
                        </div>
                    </div>
                </div>
            @endif
        @empty
            <p class="md:col-span-2 lg:col-span-3 text-gray-500 text-center py-4">Bạn chưa yêu thích món ăn nào.</p>
        @endforelse
    </div>
</section>
