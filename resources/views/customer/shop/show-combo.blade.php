@extends('layouts.customer.fullLayoutMaster')

@section('title', $combo->name)

@section('content')
    <style>
        .container {
            max-width: 1280px;
            margin: 0 auto;
        }

        /* ... giữ lại các style cần thiết từ show.blade.php ... */
    </style>
    <div class="container mx-auto px-4 py-8">
        <!-- Product Info Section -->
        <div class="grid lg:grid-cols-2 gap-8 mb-12">
            <!-- Left column: Images -->
            <div class="space-y-4">
                <div class="relative h-[300px] sm:h-[400px] rounded-lg overflow-hidden border">
                    <img src="{{ $combo->image_url ?? asset('images/default-combo.png') }}" alt="{{ $combo->name }}"
                        class="object-cover w-full h-full" id="main-product-image">
                    @if ($combo->status === 'selling')
                        <span class="absolute top-2 right-2 bg-yellow-500 text-white text-xs px-2 py-1 rounded-full">Phổ biến
                            nhất</span>
                    @endif
                    @if ($combo->original_price > $combo->price)
                        <span class="absolute top-2 left-2 bg-red-500 text-white text-xs px-2 py-1 rounded-full">Giảm
                            {{ round((($combo->original_price - $combo->price) / $combo->original_price) * 100) }}%</span>
                    @endif
                </div>
            </div>
            <!-- Right column: Product Info -->
            <div class="space-y-6">
                <h1 class="text-2xl sm:text-3xl font-bold">{{ $combo->name }}</h1>
                <div class="space-y-2">
                    <div class="flex items-center gap-3">
                        <span class="text-3xl font-bold text-orange-500 transition-all duration-300" id="current-price">
                            {{ number_format($combo->price, 0, '', '.') }} đ
                        </span>
                        @if ($combo->original_price > $combo->price)
                            <span class="text-lg text-gray-400 line-through" id="base-price">
                                {{ number_format($combo->original_price, 0, '', '.') }} đ
                            </span>
                        @endif
                    </div>
                </div>

                <div class="bg-orange-50 rounded-lg p-4">
                    <h3 class="font-medium mb-2 flex items-center gap-2"><i class="fas fa-utensils"></i> Chi tiết combo</h3>
                    @foreach ($items as $item)
                        <div class="flex items-center gap-3 mb-2">
                            <img src="{{ $item['image'] ?? asset('images/default-placeholder.png') }}"
                                alt="{{ $item['product_name'] }}" class="w-12 h-12 rounded object-cover">
                            <div class="flex-1">
                                <div class="font-semibold">{{ $item['product_name'] }}</div>
                                <div class="text-xs text-gray-500">
                                    @foreach ($item['variant_values'] as $v)
                                        {{ $v['attribute'] }}: {{ $v['value'] }}@if (!$loop->last)
                                            ,
                                        @endif
                                    @endforeach
                                </div>

                            </div>
                            <div class="text-orange-500 font-semibold">
                                {{ number_format($item['variant_price'], 0, ',', '.') }}đ x {{ $item['quantity'] }}</div>
                        </div>
                    @endforeach
                </div>

                <!-- Quantity & Action -->
                <div class="flex items-center gap-4 mt-4">
                    <span class="font-medium">Số lượng:</span>
                    <div class="flex items-center">
                        <button
                            class="h-8 w-8 rounded-l-md border border-gray-300 flex items-center justify-center hover:bg-gray-100"
                            id="decrease-quantity">
                            <i class="fas fa-minus h-3 w-3"></i>
                        </button>
                        <div class="h-8 px-3 flex items-center justify-center border-y border-gray-300" id="quantity">1
                        </div>
                        <button
                            class="h-8 w-8 rounded-r-md border border-gray-300 flex items-center justify-center hover:bg-gray-100"
                            id="increase-quantity">
                            <i class="fas fa-plus h-3 w-3"></i>
                        </button>
                    </div>
                </div>
                <div class="flex flex-col sm:flex-row gap-3 pt-4">
                    <button
                        class="w-full sm:flex-1 bg-orange-500 hover:bg-orange-600 text-white px-6 py-3 rounded-md font-medium transition-colors flex items-center justify-center"><i
                            class="fas fa-shopping-cart h-5 w-5 mr-2"></i>Thêm vào giỏ hàng</button>
                    <button
                        class="w-full sm:flex-1 border border-gray-300 hover:bg-gray-50 px-6 py-3 rounded-md font-medium transition-colors">Mua
                        ngay</button>
                    <div class="flex gap-3 justify-center sm:justify-start">
                        @auth
                        <button class="border border-gray-300 hover:bg-gray-50 h-12 w-12 rounded-md flex items-center justify-center favorite-btn" data-combo-id="{{ $combo->id }}">
                            @if(isset($combo->is_favorite) && $combo->is_favorite)
                                <i class="fas fa-heart text-red-500 h-5 w-5"></i>
                            @else
                                <i class="far fa-heart h-5 w-5"></i>
                            @endif
                            <span class="sr-only">Yêu thích</span>
                        </button>
                        @else
                        <button class="border border-gray-300 hover:bg-gray-50 h-12 w-12 rounded-md flex items-center justify-center" id="login-prompt-btn">
                            <i class="far fa-heart h-5 w-5"></i>
                            <span class="sr-only">Yêu thích</span>
                        </button>
                        @endauth
                        <button class="border border-gray-300 hover:bg-gray-50 h-12 w-12 rounded-md flex items-center justify-center">
                            <i class="fas fa-share-alt h-5 w-5"></i>
                            <span class="sr-only">Chia sẻ</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Tabs: Mô tả, Thành phần, Đánh giá -->
        <div class="border rounded-lg overflow-hidden bg-white mt-8">
            <div class="grid grid-cols-3 border-b">
                <button type="button"
                    class="py-4 text-center font-medium border-b-2 border-orange-500 text-orange-500 w-full"
                    id="tab-description" data-tab="description">
                    Mô tả
                </button>
                <button type="button"
                    class="py-4 text-center font-medium border-b-2 border-transparent hover:text-orange-500 w-full"
                    id="tab-ingredients" data-tab="ingredients">
                    Thành phần
                </button>
                <button type="button"
                    class="py-4 text-center font-medium border-b-2 border-transparent hover:text-orange-500 w-full"
                    id="tab-reviews" data-tab="reviews">
                    Đánh giá
                </button>
            </div>
            <div class="p-6">
                <!-- Description Tab -->
                <div class="tab-content" id="content-description">
                    <p class="text-gray-600 leading-relaxed">{{ $combo->description }}</p>
                </div>
                <!-- Ingredients Tab -->
                <div class="tab-content hidden" id="content-ingredients">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h4 class="font-medium mb-3 text-gray-900">Thành phần combo:</h4>
                            <ul class="space-y-2">
                                @foreach ($items as $item)
                                    <li class="flex items-center space-x-2 text-gray-700">
                                        <span class="w-1.5 h-1.5 bg-orange-500 rounded-full"></span>
                                        <button type="button"
                                            class="flex-1 text-left hover:text-orange-600 font-semibold product-ingredient-btn"
                                            data-ingredients='@json($item['product_ingredients'])'
                                            data-name="{{ $item['product_name'] }}">
                                            {{ $item['product_name'] }}
                                        </button>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        <div id="ingredient-detail-panel" class="bg-orange-50 rounded-lg p-4 min-h-[120px]">
                            <div id="ingredient-detail-content" class="text-gray-700 text-sm">
                                <span class="text-gray-400">Chọn tên sản phẩm để xem thành phần...</span>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Reviews Tab -->
                <div class="tab-content hidden" id="content-reviews">
                    <div class="bg-white rounded-lg">
                        <div class="mb-6">
                            <div class="flex items-center justify-between">
                                <h3 class="text-lg font-semibold flex items-center gap-2">
                                    <i class="fas fa-star text-yellow-400"></i>
                                    Đánh giá combo
                                    <span class="text-gray-500 text-sm">({{ optional($combo->reviews)->count() ?? 0 }} đánh giá)</span>
                                </h3>
                                <div class="flex items-center gap-2">
                                    <div class="flex items-center">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= floor($combo->average_rating ?? 0))
                                                <i class="fas fa-star text-yellow-400"></i>
                                            @elseif($i - 0.5 <= ($combo->average_rating ?? 0))
                                                <i class="fas fa-star-half-alt text-yellow-400"></i>
                                            @else
                                                <i class="far fa-star text-yellow-400"></i>
                                            @endif
                                        @endfor
                                    </div>
                                    <span class="text-sm font-medium">{{ number_format($combo->average_rating ?? 0, 1) }}/5</span>
                                </div>
                            </div>
                        </div>
                        <div class="divide-y max-h-[600px] overflow-y-auto scrollbar-thin scrollbar-thumb-orange-200 scrollbar-track-gray-100 hover:scrollbar-thumb-orange-300">
                            @forelse(($combo->reviews ?? []) as $review)
                            <div class="p-6 hover:bg-gray-50/50 transition-colors" data-review-id="{{ $review->id }}">
                                <div class="flex items-start justify-between gap-4">
                                    <div class="flex items-start gap-4">
                                        <div class="w-12 h-12 rounded-full bg-gradient-to-br from-orange-400 to-orange-600 flex items-center justify-center flex-shrink-0">
                                            <span class="text-white font-semibold text-lg">
                                                {{ strtoupper(substr($review->user->name, 0, 1)) }}
                                            </span>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-2 flex-wrap">
                                                <span class="font-medium text-gray-900">{{ $review->is_anonymous ? 'Ẩn danh' : $review->user->name }}</span>
                                                @if($review->is_verified_purchase)
                                                    <span class="inline-flex items-center gap-1 text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full">
                                                        <i class="fas fa-check-circle"></i>
                                                        Đã mua hàng
                                                    </span>
                                                @endif
                                                @if($review->is_featured)
                                                    <span class="inline-flex items-center gap-1 text-xs bg-orange-100 text-orange-700 px-2 py-0.5 rounded-full">
                                                        <i class="fas fa-award"></i>
                                                        Đánh giá nổi bật
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="text-sm text-gray-500 mt-1 space-x-2">
                                                <span>{{ $review->created_at->format('d/m/Y H:i') }}</span>
                                                @if($review->branch)
                                                    <span>•</span>
                                                    <span>{{ $review->branch->name }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex flex-col items-end gap-1">
                                        <div class="flex items-center gap-1 bg-yellow-50 px-2 py-1 rounded">
                                            <span class="font-medium text-yellow-700">{{ $review->rating }}.0</span>
                                            <div class="flex items-center">
                                                @for($i = 1; $i <= 5; $i++)
                                                    @if($i <= $review->rating)
                                                        <i class="fas fa-star text-yellow-400"></i>
                                                    @else
                                                        <i class="far fa-star text-yellow-400"></i>
                                                    @endif
                                                @endfor
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-4 space-y-3">
                                    <p class="text-gray-700 leading-relaxed">{{ $review->review }}</p>
                                    @if($review->review_image)
                                        <div class="mt-3">
                                            <img src="{{ $review->review_image }}" alt="Review image" class="rounded-lg max-h-48 object-cover hover:opacity-95 transition-opacity cursor-pointer">
                                        </div>
                                    @endif
                                    <div class="flex items-center gap-6 pt-2">
                                        @php
                                            $userHelpful = auth()->check() ? \App\Models\ReviewHelpful::where('user_id', auth()->id())->where('review_id', $review->id)->exists() : false;
                                        @endphp
                                        <button class="inline-flex items-center gap-2 text-sm helpful-btn {{ $userHelpful ? 'helpful-active text-sky-600' : '' }}"
                                                data-review-id="{{ $review->id }}"
                                                data-helpful="{{ $userHelpful ? '1' : '0' }}">
                                            <i class="{{ $userHelpful ? 'fas' : 'far' }} fa-thumbs-up {{ $userHelpful ? 'text-sky-600' : '' }}"></i>
                                            <span>Hữu ích (<span class="helpful-count">{{ $review->helpful_count }}</span>)</span>
                                        </button>
                                        @if($review->report_count > 0)
                                            <span class="inline-flex items-center gap-1 text-xs text-red-500">
                                                <i class="fas fa-flag"></i>
                                                {{ $review->report_count }} báo cáo
                                            </span>
                                        @endif
                                        @auth
                                            <button class="inline-flex items-center gap-2 text-sm text-blue-500 hover:text-blue-700 transition-colors reply-review-btn"
                                                data-review-id="{{ $review->id }}"
                                                data-user-name="{{ $review->is_anonymous ? 'Ẩn danh' : $review->user->name }}"
                                                data-route-reply="{{ route('combos.review.reply', ['review' => $review->id]) }}">
                                                <i class="fas fa-reply"></i>
                                                <span>Phản hồi</span>
                                            </button>
                                            @if($review->user_id === auth()->id() || (auth()->user()->is_admin ?? false))
                                                <button class="inline-flex items-center gap-2 text-sm text-red-500 hover:text-red-700 transition-colors delete-review-btn" data-review-id="{{ $review->id }}">
                                                    <i class="fas fa-trash-alt"></i>
                                                    <span>Xóa</span>
                                                </button>
                                            @endif
                                        @endauth
                                    </div>
                                </div>
                                <!-- Hiển thị các reply -->
                                @foreach($review->replies as $reply)
                                    <div class="reply-item flex items-start gap-2 ml-8 mt-2 relative" data-reply-id="{{ $reply->id }}">
                                        <div class="reply-arrow">
                                            <svg width="24" height="24" viewBox="0 0 24 24" class="text-blue-400"><path d="M2 12h16M18 12l-4-4m4 4l-4 4" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                        </div>
                                        <div class="bg-blue-50 border border-blue-200 rounded-lg px-4 py-2 flex-1">
                                            <div class="flex items-center gap-2 mb-1">
                                                <span class="font-semibold text-blue-700">{{ $reply->user->name ?? 'Ẩn danh' }}</span>
                                                <span class="text-xs text-gray-400">{{ $reply->reply_date ? \Carbon\Carbon::parse($reply->reply_date)->format('d/m/Y H:i') : '' }}</span>
                                                @auth
                                                    @if($reply->user_id === auth()->id() || (auth()->user()->is_admin ?? false))
                                                        <button class="inline-flex items-center gap-1 text-xs text-red-500 hover:text-red-700 transition-colors delete-reply-btn" data-reply-id="{{ $reply->id }}">
                                                            <i class="fas fa-trash-alt"></i> Xóa
                                                        </button>
                                                    @endif
                                                @endauth
                                            </div>
                                            <div class="text-gray-700">{{ $reply->reply }}</div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @empty
                            <div class="p-8 text-center">
                                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i class="far fa-comment-alt text-3xl text-gray-400"></i>
                                </div>
                                <p class="text-gray-500 font-medium">Chưa có đánh giá nào cho combo này.</p>
                                <p class="text-gray-400 text-sm mt-1">Hãy là người đầu tiên đánh giá combo!</p>
                            </div>
                            @endforelse
                        </div>
                        <!-- Form gửi đánh giá hoặc phản hồi -->
                        <div id="review-reply-form-container" class="mt-8 p-6 bg-gray-50 rounded-lg border border-gray-200">
                            <form id="review-reply-form" action="{{ route('products.review', $combo->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4" data-default-action="{{ route('products.review', $combo->id) }}">
                                @csrf
                                <input type="hidden" name="reply_review_id" id="reply_review_id" value="">
                                <div id="replying-to" class="mb-2 hidden">
                                    <span class="text-sm text-blue-600">Phản hồi cho <b id="replying-to-user"></b></span>
                                    <button type="button" id="cancel-reply" class="ml-2 text-xs text-gray-500 hover:text-red-500">Hủy</button>
                                </div>
                                <div class="flex items-center justify-between mb-4 gap-2 flex-wrap" id="rating-row">
                                    <h4 class="font-semibold text-lg" id="form-title" data-default-title="Gửi đánh giá của bạn">Gửi đánh giá của bạn</h4>
                                    <div class="flex items-center" id="rating-stars">
                                        @for($i = 1; $i <= 5; $i++)
                                            <input type="radio" id="star{{ $i }}" name="rating" value="{{ $i }}" class="sr-only">
                                            <label for="star{{ $i }}" class="cursor-pointer text-2xl text-yellow-400" style="position: relative;">
                                                <i class="fas fa-star"></i>
                                            </label>
                                        @endfor
                                    </div>
                                </div>
                                <div id="review-message" class="mb-4 text-center"></div>
                                <div>
                                    <textarea name="review" id="review-textarea" rows="3" class="w-full border rounded p-2" placeholder="Chia sẻ cảm nhận của bạn..." data-default-placeholder="Chia sẻ cảm nhận của bạn..."></textarea>
                                </div>
                                <div>
                                    <label class="block font-medium mb-1">Ảnh minh họa (tùy chọn):</label>
                                    <div class="flex items-center justify-between gap-4 flex-wrap">
                                        <div>
                                            <input type="file" name="review_image" id="review_image" accept="image/*" class="hidden">
                                            <label for="review_image" class="w-20 h-20 border-2 border-dashed border-gray-300 rounded-lg flex items-center justify-center cursor-pointer hover:border-orange-400 transition-colors relative">
                                                <i class="fas fa-camera text-3xl text-orange-500"></i>
                                                <img id="preview_image" src="#" alt="Preview" class="absolute inset-0 w-full h-full object-cover rounded-lg hidden" />
                                            </label>
                                        </div>
                                        <div class="flex items-center ml-auto">
                                            <input type="checkbox" name="is_anonymous" id="is_anonymous" value="1" class="custom-checkbox" {{ old('is_anonymous') ? 'checked' : '' }}>
                                            <label for="is_anonymous" class="anonymous-label">Ẩn danh</label>
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" id="review-submit-btn" class="bg-orange-500 hover:bg-orange-600 text-white px-6 py-2 rounded font-medium" data-default-text="Gửi đánh giá">Gửi đánh giá</button>
                            </form>
                        </div>
                        @if(optional($combo->reviews)->count() > 0)
                        <div class="mt-6 flex items-center justify-between">
                            <div class="text-sm text-gray-500">
                                Hiển thị {{ optional($combo->reviews)->count() ?? 0 }} đánh giá
                            </div>
                            <button class="inline-flex items-center gap-1 text-orange-500 hover:text-orange-600 font-medium text-sm transition-colors">
                                <span>Xem tất cả</span>
                                <i class="fas fa-chevron-right text-xs"></i>
                            </button>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const tabs = document.querySelectorAll('.grid.grid-cols-3 button');
                const contents = document.querySelectorAll('.tab-content');
                // Hiển thị tab mô tả mặc định
                contents.forEach(c => c.classList.add('hidden'));
                document.getElementById('content-description').classList.remove('hidden');
                tabs.forEach(tab => {
                    tab.classList.remove('border-orange-500', 'text-orange-500');
                    if (tab.dataset.tab === 'description') {
                        tab.classList.add('border-orange-500', 'text-orange-500');
                    }
                    tab.addEventListener('click', function() {
                        tabs.forEach(t => {
                            t.classList.remove('border-orange-500', 'text-orange-500');
                            t.classList.add('border-transparent');
                        });
                        this.classList.remove('border-transparent');
                        this.classList.add('border-orange-500', 'text-orange-500');
                        contents.forEach(c => c.classList.add('hidden'));
                        document.getElementById('content-' + this.dataset.tab).classList.remove(
                            'hidden');
                    });
                });
                // Script tăng giảm số lượng combo
                const decreaseBtn = document.getElementById('decrease-quantity');
                const increaseBtn = document.getElementById('increase-quantity');
                const quantityDiv = document.getElementById('quantity');
                let quantity = 1;
                const minQty = 1;
                const maxQty = 20; // hoặc số tối đa bạn muốn

                function updateQuantityDisplay() {
                    quantityDiv.textContent = quantity;
                }

                if (decreaseBtn && increaseBtn && quantityDiv) {
                    decreaseBtn.addEventListener('click', function() {
                        if (quantity > minQty) {
                            quantity--;
                            updateQuantityDisplay();
                        }
                    });
                    increaseBtn.addEventListener('click', function() {
                        if (quantity < maxQty) {
                            quantity++;
                            updateQuantityDisplay();
                        }
                    });
                }
            });
        </script>
        <script>
            // Script xử lý click vào tên sản phẩm để hiện thành phần bên phải
            function setupIngredientPanel() {
                const btns = document.querySelectorAll('.product-ingredient-btn');
                const panel = document.getElementById('ingredient-detail-content');
                if (!btns.length || !panel) return;
                btns.forEach(btn => {
                    btn.addEventListener('click', function() {
                        const name = this.dataset.name;
                        let ingredients = this.dataset.ingredients;
                        let html = '';
                        try {
                            ingredients = JSON.parse(ingredients);
                        } catch (e) {}
                        if (Array.isArray(ingredients)) {
                            html =
                                `<div class='font-semibold mb-1 text-orange-700'>${name}</div><ul class='list-disc pl-5'>` +
                                ingredients.map(i => `<li>${i}</li>`).join('') + '</ul>';
                        } else if (typeof ingredients === 'string' && ingredients.trim() !== '') {
                            html =
                                `<div class='font-semibold mb-1 text-orange-700'>${name}</div><div>${ingredients}</div>`;
                        } else {
                            html =
                                `<div class='font-semibold mb-1 text-orange-700'>${name}</div><div class='text-gray-400'>Không có thông tin thành phần.</div>`;
                        }
                        panel.innerHTML = html;
                    });
                });
            }
            // Gọi lại hàm này sau khi DOM đã render
            setupIngredientPanel();
        </script>
        <!-- Related Combos -->
        @php
            $relatedCombos =
                $combo->category && $combo->category->combos
                    ? $combo->category->combos->where('id', '!=', $combo->id)->take(3)
                    : collect();
        @endphp
        @if ($relatedCombos->count())
            <div class="mt-12">
                <h2 class="text-2xl font-bold mb-6">Combo liên quan</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                    @foreach ($relatedCombos as $relatedCombo)
                        <div
                            class="bg-white rounded-xl shadow-md hover:shadow-lg transition-shadow duration-300 overflow-hidden group border border-gray-100">
                            <div class="relative">
                                <a href="{{ route('combos.show', $relatedCombo->id) }}" class="block">
                                    <img src="{{ $relatedCombo->image_url ?? asset('images/default-combo.png') }}"
                                        alt="{{ $relatedCombo->name }}"
                                        class="object-cover w-full h-40 group-hover:scale-105 transition-transform duration-300">
                                    @if ($relatedCombo->original_price > $relatedCombo->price)
                                        <span
                                            class="absolute top-2 left-2 bg-red-500 text-white text-xs px-2 py-1 rounded-full shadow">-{{ round((($relatedCombo->original_price - $relatedCombo->price) / $relatedCombo->original_price) * 100) }}%</span>
                                    @endif
                                </a>
                            </div>
                            <div class="px-4 py-3 flex flex-col gap-2">
                                <a href="{{ route('combos.show', $relatedCombo->id) }}" class="block">
                                    <h3 class="font-semibold text-lg text-gray-900 group-hover:text-orange-600 truncate">
                                        {{ $relatedCombo->name }}
                                    </h3>
                                </a>
                                @if($relatedCombo->comboItems && $relatedCombo->comboItems->count() > 0)
                                    <div class="flex flex-wrap gap-1 items-center mt-1 mb-2">
                                        @foreach($relatedCombo->comboItems->take(3) as $item)
                                            <span class="inline-flex items-center bg-orange-50 text-orange-700 rounded px-2 py-0.5 text-xs font-medium max-w-[110px] truncate" title="{{ $item->productVariant->product->name ?? '' }}">
                                                <i class="fas fa-hamburger mr-1 text-orange-400"></i>{{ Str::limit($item->productVariant->product->name ?? '', 18) }}
                                            </span>
                                        @endforeach
                                        @if($relatedCombo->comboItems->count() > 3)
                                            <span class="inline-flex items-center bg-gray-100 text-gray-600 rounded px-2 py-0.5 text-xs font-medium ml-1">+{{ $relatedCombo->comboItems->count() - 3 }} món</span>
                                        @endif
                                    </div>
                                @endif
                                <div class="flex items-center gap-2">
                                    <span
                                        class="text-orange-500 font-bold text-base">{{ number_format($relatedCombo->price, 0, '', '.') }}đ</span>
                                    @if ($relatedCombo->original_price > $relatedCombo->price)
                                        <span
                                            class="text-gray-400 line-through text-sm">{{ number_format($relatedCombo->original_price, 0, '', '.') }}đ</span>
                                    @endif
                                </div>

                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
@endsection
