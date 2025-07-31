@php
    use Illuminate\Support\Facades\Storage;
    // dd(Storage::disk('s3')->files('banner'));
@endphp

@if ($banners->isNotEmpty())
    <div class="relative h-[300px] sm:h-[400px] md:h-[500px] overflow-hidden" id="banner-slider">
        @foreach ($banners as $index => $banner)
            <div class="banner-slide absolute inset-0 transition-opacity duration-1000 {{ $index === 0 ? 'opacity-100' : 'opacity-0' }}" 
                 data-banner-id="{{ $banner->id }}" 
                 data-banner-link="{{ $banner->link }}"
                 style="z-index: {{ $index === 0 ? 10 : 1 }};">
                <div class="relative h-full w-full">
                    @php
                        $hasLink = is_string($banner->link) && strlen($banner->link) > 0;
                        $link = $hasLink
                            ? (Str::startsWith($banner->link, ['http://', 'https://']) ? $banner->link : url($banner->link))
                            : null;
                    @endphp
                    @if ($hasLink)
                        <a href="{{ $link }}" 
                           class="block w-full h-full" 
                           title="Banner {{ $index + 1 }}: {{ $banner->title }} -> {{ $link }}"
                           onclick="console.log('Clicked banner {{ $index + 1 }}: {{ $banner->title }}, Link: {{ $link }}'); return true;">
                            <img src="{{ Str::startsWith($banner->image_path, ['http://', 'https://']) ? $banner->image_path : Storage::disk('s3')->url($banner->image_path) }}" 
                                 alt="{{ $banner->title }}" class="object-cover w-full h-full">
                        </a>
                    @else
                        <img src="{{ Str::startsWith($banner->image_path, ['http://', 'https://']) ? $banner->image_path : Storage::disk('s3')->url($banner->image_path) }}" 
                             alt="{{ $banner->title }}" class="object-cover w-full h-full">
                    @endif
                    <div class="absolute inset-0 bg-black/30 pointer-events-none"></div>
                    <div class="absolute inset-0 flex flex-col items-center justify-center text-center text-white p-4 pointer-events-none">
                        <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold mb-2 sm:mb-4">{{ $banner->title }}</h2>
                        <p class="text-lg max-w-2xl mx-auto">{{ $banner->description }}</p>
                        @if ($hasLink)
                            <a href="{{ $link }}" 
                               class="bg-orange-500 hover:bg-orange-600 text-white px-6 py-3 rounded-md font-medium transition-colors pointer-events-auto" 
                               title="Link to: {{ $link }}"
                               onclick="console.log('Clicked Xem Thêm button for banner {{ $index + 1 }}: {{ $banner->title }}, Link: {{ $link }}'); return true;">
                                Xem Thêm
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif