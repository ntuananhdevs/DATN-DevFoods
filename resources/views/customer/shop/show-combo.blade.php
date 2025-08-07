@extends('layouts.customer.fullLayoutMaster')

@section('title', $combo->name)

@section('content')
<x-customer-container>
<style>
    #report-review-modal .bg-white {
        max-width: 40rem;
        width: 100%;
        padding: 1rem 1.25rem;
        margin: 0;
        max-height: 80vh;
        overflow-y: auto;
    }
    #report-review-modal .flex.items-center.mb-4 {
        padding-bottom: 0.25rem;
        margin-bottom: 0.5rem;
    }
    #report-review-modal .bg-gray-50 {
        padding: 0.5rem 0.75rem;
        margin-bottom: 0.5rem;
    }
    #report-review-modal .reason-option {
        padding: 0.5rem 0.75rem;
        margin-bottom: 0;
    }
    #report-review-modal .reason-option .font-semibold {
        font-size: 0.95rem;
    }
    #report-review-modal .reason-option .text-xs {
        font-size: 0.78rem;
        line-height: 1.2;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    #report-review-modal textarea {
        min-height: 36px;
        font-size: 0.95rem;
        padding: 0.4rem 0.6rem;
        resize: vertical;
    }
    #report-review-modal .bg-blue-50 {
        padding: 0.5rem 0.75rem;
        font-size: 0.9rem;
        margin-bottom: 0.3rem;
    }
    #report-review-modal .flex.justify-end.gap-2.pt-2 {
        padding-top: 0.3rem;
    }
    #report-review-modal label.block.font-medium.mb-2 {
        margin-bottom: 0.3rem;
    }
    #report-review-modal .grid {
        gap: 0.5rem;
    }
    #report-review-modal .preview-binhluan {
        padding: 0.5rem 0.75rem;
        margin-bottom: 0.5rem;
        background: #f9fafb;
        border-left: 3px solid #ef4444;
        display: flex;
        gap: 0.75rem;
        align-items: flex-start;
    }
    #report-review-modal .preview-binhluan .avatar {
        width: 2.2rem;
        height: 2.2rem;
        font-size: 1.1rem;
    }
    #report-review-modal .preview-binhluan .info {
        flex: 1;
        min-width: 0;
    }
    #report-review-modal .preview-binhluan .info .name {
        font-weight: 600;
        font-size: 1rem;
        color: #222;
        margin-right: 0.5rem;
    }
    #report-review-modal .preview-binhluan .info .time {
        font-size: 0.85rem;
        color: #888;
    }
    #report-review-modal .preview-binhluan .info .content {
        font-size: 0.95rem;
        color: #444;
        margin-top: 0.1rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 100%;
    }
    @media (max-width: 700px) {
        #report-review-modal .bg-white {
            max-width: 98vw;
            padding: 0.5rem 0.2rem;
        }
    }
    .reply-item {
        display: flex;
        align-items: flex-start;
        gap: 0;
        margin-left: 56px;
        margin-top: 12px;
        position: relative;
    }
    .reply-item::before {
        content: '';
        position: absolute;
        left: -28px;
        top: -12px;
        width: 2px;
        height: 24px;
        background-color: #e5e7eb;
    }
    .reply-item::after {
        content: '';
        position: absolute;
        left: -28px;
        top: 12px;
        width: 20px;
        height: 2px;
        background-color: #e5e7eb;
    }
    .reply-item .reply-bubble {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 12px 16px;
        min-width: 0;
        flex: 1;
        margin-left: 8px;
    }
    .reply-item .reply-header {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 4px;
    }
    .reply-item .reply-author {
        font-weight: 600;
        color: #1e40af;
        font-size: 0.9rem;
    }
    .reply-item .reply-time {
        font-size: 0.8rem;
        color: #64748b;
    }
    .reply-item .reply-actions {
        margin-left: auto;
        display: flex;
        gap: 6px;
    }
    .reply-item .reply-actions button {
        background: none;
        border: none;
        color: #ef4444;
        font-size: 0.75rem;
        cursor: pointer;
        padding: 2px 6px;
        border-radius: 4px;
    }
    .reply-item .reply-content {
        color: #374151;
        font-size: 0.9rem;
        line-height: 1.4;
        word-break: break-word;
    }
</style>
    <div class="container mx-auto px-4 py-8">
        <!-- Product Info Section -->
        <div class="grid lg:grid-cols-2 gap-8 mb-12">
            <!-- Left column: Images -->
            <div class="space-y-4">
                <div id="combo-image-container" class="relative h-[300px] sm:h-[400px] rounded-lg overflow-hidden border">
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
                <div id="combo-out-of-stock-message" class="text-red-600 font-semibold text-base mt-2" style="display: {{ isset($combo->has_stock) && !$combo->has_stock ? 'block' : 'none' }};">
                    Sản phẩm này hiện tại đang hết hàng
                </div>
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
                    <button id="add-to-cart-combo" data-combo-id="{{ $combo->id }}" data-has-stock="{{ isset($combo->has_stock) && !$combo->has_stock ? 'false' : 'true' }}"
                        class="w-full sm:flex-1 px-6 py-3 rounded-md font-medium transition-colors flex items-center justify-center {{ isset($combo->has_stock) && $combo->has_stock ? 'bg-orange-500 hover:bg-orange-600' : 'bg-gray-400' }} text-white disabled:opacity-50 disabled:cursor-not-allowed"
                        @if(isset($combo->has_stock) && !$combo->has_stock) disabled @endif>
                        <i class="fas {{ isset($combo->has_stock) && $combo->has_stock ? 'fa-shopping-cart' : 'fa-ban' }} h-5 w-5 mr-2"></i>
                        <span>{{ isset($combo->has_stock) && !$combo->has_stock ? 'Hết hàng' : 'Thêm vào giỏ hàng' }}</span>
                    </button>
                    <button id="buy-now-combo-btn" data-combo-id="{{ $combo->id }}"
                        class="w-full sm:flex-1 border border-gray-300 hover:bg-gray-50 px-6 py-3 rounded-md font-medium transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                        @if(isset($combo->has_stock) && !$combo->has_stock) disabled @endif>
                        Mua ngay
                    </button>
                    <div class="flex gap-3 justify-center sm:justify-start">
                        @auth
                            <button
                                class="border border-gray-300 hover:bg-gray-50 h-12 w-12 rounded-md flex items-center justify-center favorite-btn"
                                data-combo-id="{{ $combo->id }}">
                                @if (isset($combo->is_favorite) && $combo->is_favorite)
                                    <i class="fas fa-heart text-red-500 h-5 w-5"></i>
                                @else
                                    <i class="far fa-heart h-5 w-5"></i>
                                @endif
                                <span class="sr-only">Yêu thích</span>
                            </button>
                        @else
                            <button
                                class="border border-gray-300 hover:bg-gray-50 h-12 w-12 rounded-md flex items-center justify-center"
                                id="login-prompt-btn">
                                <i class="far fa-heart h-5 w-5"></i>
                                <span class="sr-only">Yêu thích</span>
                            </button>
                        @endauth
                        <button
                            class="border border-gray-300 hover:bg-gray-50 h-12 w-12 rounded-md flex items-center justify-center">
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
                                    <span class="text-gray-500 text-sm">({{ optional($combo->reviews)->count() ?? 0 }}
                                        đánh giá)</span>
                                </h3>
                                <div class="flex items-center gap-2">
                                    <div class="flex items-center">
                                        @for ($i = 1; $i <= 5; $i++)
                                            @if ($i <= floor($combo->average_rating ?? 0))
                                                <i class="fas fa-star text-yellow-400"></i>
                                            @elseif($i - 0.5 <= ($combo->average_rating ?? 0))
                                                <i class="fas fa-star-half-alt text-yellow-400"></i>
                                            @else
                                                <i class="far fa-star text-yellow-400"></i>
                                            @endif
                                        @endfor
                                    </div>
                                    <span
                                        class="text-sm font-medium">{{ number_format($combo->average_rating ?? 0, 1) }}/5</span>
                                </div>
                            </div>
                        </div>
                        <div
                            class="divide-y max-h-[600px] overflow-y-auto scrollbar-thin scrollbar-thumb-orange-200 scrollbar-track-gray-100 hover:scrollbar-thumb-orange-300">
                            @forelse(($combo->reviews ?? []) as $review)
                                <div class="p-6 hover:bg-gray-50/50 transition-colors review-item" data-review-id="{{ $review->id }}">
                                    <div class="flex items-start justify-between gap-4">
                                        <div class="flex items-start gap-4">
                                            <div
                                                class="w-12 h-12 rounded-full bg-gradient-to-br from-orange-400 to-orange-600 flex items-center justify-center flex-shrink-0">
                                                <span class="text-white font-semibold text-lg">
                                                    {{ strtoupper(substr($review->user->name, 0, 1)) }}
                                                </span>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center gap-2 flex-wrap">
                                                    <span
                                                        class="font-medium text-gray-900">{{ $review->user->name }}</span>
                                                    @if ($review->is_verified_purchase)
                                                        <span
                                                            class="inline-flex items-center gap-1 text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full">
                                                            <i class="fas fa-check-circle"></i>
                                                            Đã mua hàng
                                                        </span>
                                                    @endif
                                                    @if ($review->is_featured)
                                                        <span
                                                            class="inline-flex items-center gap-1 text-xs bg-orange-100 text-orange-700 px-2 py-0.5 rounded-full">
                                                            <i class="fas fa-award"></i>
                                                            Đánh giá nổi bật
                                                        </span>
                                                    @endif
                                                </div>
                                                <div class="text-sm text-gray-500 mt-1 space-x-2">
                                                    <span>{{ $review->created_at->format('d/m/Y H:i') }}</span>
                                                    @if ($review->branch)
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
                                                    @for ($i = 1; $i <= 5; $i++)
                                                        @if ($i <= $review->rating)
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
                                        @if ($review->review_image)
                                            <div class="mt-3">
                                                <img src="{{ $review->review_image }}" alt="Review image"
                                                    class="rounded-lg max-h-48 object-cover hover:opacity-95 transition-opacity cursor-pointer">
                                            </div>
                                        @endif
                                        <div class="flex items-center gap-6 pt-2">
                                            @php
                                                $userHelpful = auth()->check()
                                                    ? \App\Models\ReviewHelpful::where('user_id', auth()->id())
                                                        ->where('review_id', $review->id)
                                                        ->exists()
                                                    : false;
                                            @endphp
                                            <button
                                                class="inline-flex items-center gap-2 text-sm helpful-btn {{ $userHelpful ? 'helpful-active text-sky-600' : '' }}"
                                                data-review-id="{{ $review->id }}"
                                                data-helpful="{{ $userHelpful ? '1' : '0' }}">
                                                <i
                                                    class="{{ $userHelpful ? 'fas' : 'far' }} fa-thumbs-up {{ $userHelpful ? 'text-sky-600' : '' }}"></i>
                                                <span>Hữu ích (<span
                                                        class="helpful-count">{{ $review->helpful_count }}</span>)</span>
                                            </button>
                                            @auth
                                                <button
                                                    class="inline-flex items-center gap-2 text-sm text-red-400 hover:text-red-600 transition-colors report-review-btn"
                                                    data-review-id="{{ $review->id }}">
                                                    <i class="fas fa-flag"></i>
                                                    <span>Báo cáo</span>
                                                    <span class="ml-1 text-xs report-count" @if($review->report_count == 0) style="display:none" @endif>
                                                        @if($review->report_count > 0)
                                                            ({{ $review->report_count }})
                                                        @endif
                                                    </span>
                                                </button>
                                                <button
                                                    class="inline-flex items-center gap-2 text-sm text-blue-500 hover:text-blue-700 transition-colors reply-review-btn"
                                                    data-review-id="{{ $review->id }}"
                                                    data-user-name="{{ $review->is_anonymous ? 'Ẩn danh' : $review->user->name }}"
                                                    data-route-reply="{{ route('reviews.reply', ['review' => $review->id]) }}">
                                                    <i class="fas fa-reply"></i>
                                                    <span>Phản hồi</span>
                                                </button>
                                                @if ($review->user_id === auth()->id() || (auth()->user()->is_admin ?? false))
                                                    <button
                                                        class="inline-flex items-center gap-2 text-sm text-red-500 hover:text-red-700 transition-colors delete-review-btn"
                                                        data-review-id="{{ $review->id }}">
                                                        <i class="fas fa-trash-alt"></i>
                                                        <span>Xóa</span>
                                                    </button>
                                                @endif
                                            @endauth
                                        </div>
                                    </div>
                                    <!-- Bọc các reply trong replies-list -->
                                    <div class="replies-list">
                                        @foreach ($review->replies as $reply)
                                            <div class="reply-item" data-reply-id="{{ $reply->id }}">
                                                <div class="reply-bubble">
                                                    <div class="reply-header">
                                                        <span class="reply-author">{{ $reply->user->name }}</span>
                                                        <span class="reply-time">{{ $reply->reply_date ? \Carbon\Carbon::parse($reply->reply_date)->format('d/m/Y H:i') : '' }}</span>
                                                        @auth
                                                            @if ($reply->user_id === auth()->id() || (auth()->user()->is_admin ?? false))
                                                                <span class="reply-actions">
                                                                    <button class="delete-reply-btn" data-reply-id="{{ $reply->id }}">
                                                                        <i class="fas fa-trash-alt"></i> Xóa
                                                                    </button>
                                                                </span>
                                                            @endif
                                                        @endauth
                                                    </div>
                                                    <div class="reply-content">{{ $reply->reply }}</div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @empty
                                <div class="p-8 text-center">
                                    <div
                                        class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <i class="far fa-comment-alt text-3xl text-gray-400"></i>
                                    </div>
                                    <p class="text-gray-500 font-medium">Chưa có đánh giá nào cho combo này.</p>
                                    <p class="text-gray-400 text-sm mt-1">Hãy là người đầu tiên đánh giá combo!</p>
                                </div>
                            @endforelse
                        </div>
                        <!-- Form gửi đánh giá hoặc phản hồi -->
                        @php
                            $hasPurchased = false;
                            if (auth()->check()) {
                                $hasPurchased = \App\Models\Order::where('customer_id', auth()->id())
                                    ->where('status', 'delivered')
                                    ->whereHas('orderItems', function($q) use ($combo) {
                                        $q->where('combo_id', $combo->id);
                                    })
                                    ->exists();
                            }
                        @endphp
                        @auth
                            @if($hasPurchased)
                                <div id="review-reply-form-container"
                                    class="mt-8 p-6 bg-gray-50 rounded-lg border border-gray-200">
                                    <form id="review-reply-form" action="{{ route('products.review', $combo->id) }}"
                                        method="POST" enctype="multipart/form-data" class="space-y-4"
                                        data-default-action="{{ route('products.review', $combo->id) }}">
                                        @csrf
                                        <input type="hidden" name="type" value="combo">
                                        <input type="hidden" name="branch_id" value="{{ $currentBranch->id }}">
                                        <input type="hidden" name="reply_review_id" id="reply_review_id" value="">
                                        <div id="replying-to" class="mb-2 hidden">
                                            <span class="text-sm text-blue-600">Phản hồi cho <b id="replying-to-user"></b></span>
                                            <button type="button" id="cancel-reply"
                                                class="ml-2 text-xs text-gray-500 hover:text-red-500">Hủy</button>
                                        </div>
                                        <div class="flex items-center justify-between mb-4 gap-2 flex-wrap" id="rating-row">
                                            <h4 class="font-semibold text-lg" id="form-title"
                                                data-default-title="Gửi đánh giá của bạn">Gửi đánh giá của bạn</h4>
                                            <div class="flex items-center" id="rating-stars">
                                                @for ($i = 1; $i <= 5; $i++)
                                                    <input type="radio" id="star{{ $i }}" name="rating"
                                                        value="{{ $i }}" class="sr-only">
                                                    <label for="star{{ $i }}"
                                                        class="cursor-pointer text-2xl text-yellow-400"
                                                        style="position: relative;">
                                                        <i class="fas fa-star"></i>
                                                    </label>
                                                @endfor
                                            </div>
                                        </div>
                                        <div id="review-message" class="mb-4 text-center"></div>
                                        <div>
                                            <textarea name="review" id="review-textarea" rows="3" class="w-full border rounded p-2"
                                                placeholder="Chia sẻ cảm nhận của bạn..." data-default-placeholder="Chia sẻ cảm nhận của bạn..."></textarea>
                                        </div>
                                        <div>
                                            <label class="block font-medium mb-1">Ảnh minh họa (tùy chọn):</label>
                                            <div class="flex items-center justify-between gap-4 flex-wrap">
                                                <div>
                                                    <input type="file" name="review_image" id="review_image" accept="image/*"
                                                        class="hidden">
                                                    <label for="review_image"
                                                        class="w-20 h-20 border-2 border-dashed border-gray-300 rounded-lg flex items-center justify-center cursor-pointer hover:border-orange-400 transition-colors relative">
                                                        <i class="fas fa-camera text-3xl text-orange-500"></i>
                                                        <img id="preview_image" src="#" alt="Preview"
                                                            class="absolute inset-0 w-full h-full object-cover rounded-lg hidden" />
                                                        <button type="button" id="remove_preview_image" class="absolute top-0 right-0 m-1 bg-white bg-opacity-80 rounded-full p-1 shadow text-gray-700 hover:bg-red-500 hover:text-white hidden" style="z-index:2;" title="Xoá ảnh">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <button type="submit" id="review-submit-btn"
                                            class="bg-orange-500 hover:bg-orange-600 text-white px-6 py-2 rounded font-medium"
                                            data-default-text="Gửi đánh giá">Gửi đánh giá</button>
                                    </form>
                                </div>
                            @else
                                <div class="mt-8 p-6 bg-gray-50 rounded-lg border border-gray-200 text-center">
                                    <p class="text-gray-600 mb-4">Vui lòng mua hàng để gửi đánh giá cho combo này.</p>
                                </div>
                            @endif
                        @else
                            <div class="mt-8 p-6 bg-gray-50 rounded-lg border border-gray-200 text-center">
                                <p class="text-gray-600 mb-4">Vui lòng <a href="{{ route('customer.login') }}" class="text-orange-500 font-semibold hover:underline">đăng nhập</a> để gửi đánh giá cho combo này.</p>
                            </div>
                        @endauth
                        @if (optional($combo->reviews)->count() > 0)
                            <div class="mt-6 flex items-center justify-between">
                                <div class="text-sm text-gray-500">
                                    Hiển thị {{ optional($combo->reviews)->count() ?? 0 }} đánh giá
                                </div>
                                <button
                                    class="inline-flex items-center gap-1 text-orange-500 hover:text-orange-600 font-medium text-sm transition-colors">
                                    <span>Xem tất cả</span>
                                    <i class="fas fa-chevron-right text-xs"></i>
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal báo cáo review -->
        <div id="report-review-modal" class="fixed inset-0 bg-black bg-opacity-40 z-50 flex items-center justify-center hidden">
            <div class="bg-white rounded-lg shadow-xl p-6 max-w-lg w-full mx-4">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                        <span class="text-xl">🚩</span> Báo cáo đánh giá
                    </h3>
                    <button id="close-report-modal" class="text-gray-400 hover:text-gray-500">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <form id="report-review-form" class="space-y-4">
                    <input type="hidden" name="review_id" id="report_review_id" value="">
                    <!-- Preview bình luận bị báo cáo (ngắn gọn) -->
                    <div class="preview-binhluan">
                        <div class="avatar w-10 h-10 rounded-full bg-gradient-to-br from-orange-400 to-orange-600 flex items-center justify-center text-white font-semibold text-lg">
                            <span id="report-modal-avatar">?</span>
                        </div>
                        <div class="info flex-1 min-w-0">
                            <span class="name" id="report-modal-username">Ẩn danh</span>
                            <span class="time" id="report-modal-time"></span>
                            <div class="content" id="report-modal-content">...</div>
                        </div>
                    </div>
                    <!-- Lý do báo cáo -->
                    <div>
                        <label class="block font-medium mb-2">Lý do báo cáo *</label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2" id="report-reason-options">
                            <label class="flex items-start gap-2 p-3 border-2 border-gray-200 rounded cursor-pointer hover:border-orange-400 reason-option">
                                <input type="radio" name="reason_type" value="spam" class="mt-1 reason-radio">
                                <div>
                                    <div class="font-semibold flex items-center gap-1"><span class="text-orange-500">🗑️</span> Spam/quảng cáo</div>
                                    <div class="text-xs text-gray-500">Quảng cáo, spam</div>
                                </div>
                            </label>
                            <label class="flex items-start gap-2 p-3 border-2 border-gray-200 rounded cursor-pointer hover:border-orange-400 reason-option">
                                <input type="radio" name="reason_type" value="harassment" class="mt-1 reason-radio">
                                <div>
                                    <div class="font-semibold flex items-center gap-1"><span class="text-red-500">🛡️</span> Quấy rối/bắt nạt</div>
                                    <div class="text-xs text-gray-500">Quấy rối, đe dọa</div>
                                </div>
                            </label>
                            <label class="flex items-start gap-2 p-3 border-2 border-gray-200 rounded cursor-pointer hover:border-orange-400 reason-option">
                                <input type="radio" name="reason_type" value="hate_speech" class="mt-1 reason-radio">
                                <div>
                                    <div class="font-semibold flex items-center gap-1"><span class="text-red-700">⚠️</span> Ngôn từ thù địch</div>
                                    <div class="text-xs text-gray-500">Phân biệt, xúc phạm</div>
                                </div>
                            </label>
                            <label class="flex items-start gap-2 p-3 border-2 border-gray-200 rounded cursor-pointer hover:border-orange-400 reason-option">
                                <input type="radio" name="reason_type" value="inappropriate" class="mt-1 reason-radio">
                                <div>
                                    <div class="font-semibold flex items-center gap-1"><span class="text-purple-500">👁️</span> Nội dung không phù hợp</div>
                                    <div class="text-xs text-gray-500">Không phù hợp cộng đồng</div>
                                </div>
                            </label>
                            <label class="flex items-start gap-2 p-3 border-2 border-gray-200 rounded cursor-pointer hover:border-orange-400 reason-option">
                                <input type="radio" name="reason_type" value="misinformation" class="mt-1 reason-radio">
                                <div>
                                    <div class="font-semibold flex items-center gap-1"><span class="text-yellow-500">⚠️</span> Thông tin sai lệch</div>
                                    <div class="text-xs text-gray-500">Không chính xác, gây hiểu lầm</div>
                                </div>
                            </label>
                            <label class="flex items-start gap-2 p-3 border-2 border-gray-200 rounded cursor-pointer hover:border-orange-400 reason-option">
                                <input type="radio" name="reason_type" value="other" class="mt-1 reason-radio">
                                <div>
                                    <div class="font-semibold flex items-center gap-1"><span class="text-gray-500">🚩</span> Lý do khác</div>
                                    <div class="text-xs text-gray-500">Khác</div>
                                </div>
                            </label>
                        </div>
                    </div>
                    <!-- Thông tin bổ sung -->
                    <div>
                        <label for="report_reason_detail" class="block font-medium mb-1">Thông tin bổ sung (tùy chọn)</label>
                        <textarea name="reason_detail" id="report_reason_detail" rows="2" class="w-full border rounded p-2" placeholder="Cung cấp thêm chi tiết..."></textarea>
                    </div>
                    <!-- Cam kết xử lý -->
                    <div class="bg-blue-50 border-l-4 border-blue-400 p-3 text-blue-800 text-sm rounded">
                        <div class="font-semibold mb-1">Cam kết của chúng tôi:</div>
                        Báo cáo của bạn sẽ được xem xét trong vòng 24 giờ. Chúng tôi cam kết thực hiện hành động phù hợp để duy trì môi trường an toàn cho cộng đồng.
                    </div>
                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" id="cancel-report-btn" class="px-4 py-2 rounded bg-gray-200 hover:bg-gray-300">Hủy</button>
                        <button type="submit" id="submit-report-btn" class="px-4 py-2 rounded bg-red-500 text-white hover:bg-red-600 font-semibold" disabled>Gửi báo cáo</button>
                    </div>
                </form>
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
                        
                        // Nếu tab thành phần được click, tự động hiển thị thành phần của sản phẩm đầu tiên
                        if (this.dataset.tab === 'ingredients') {
                            showFirstProductIngredients();
                        }
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
                        displayProductIngredients(this);
                    });
                });
            }

            // Function để hiển thị thành phần của một sản phẩm
            function displayProductIngredients(btn) {
                const panel = document.getElementById('ingredient-detail-content');
                if (!panel) return;
                
                const name = btn.dataset.name;
                let ingredients = btn.dataset.ingredients;
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
            }

            // Function để hiển thị thành phần của sản phẩm đầu tiên
            function showFirstProductIngredients() {
                const firstBtn = document.querySelector('.product-ingredient-btn');
                if (firstBtn) {
                    displayProductIngredients(firstBtn);
                }
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
                                <a href="{{ route('combos.show', $relatedCombo->slug) }}" class="block">
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
                                <a href="{{ route('combos.show', $relatedCombo->slug) }}" class="block">
                                    <h3 class="font-semibold text-lg text-gray-900 group-hover:text-orange-600 truncate">
                                        {{ $relatedCombo->name }}
                                    </h3>
                                </a>
                                @if ($relatedCombo->comboItems && $relatedCombo->comboItems->count() > 0)
                                    <div class="flex flex-wrap gap-1 items-center mt-1 mb-2">
                                        @foreach ($relatedCombo->comboItems->take(3) as $item)
                                            <span
                                                class="inline-flex items-center bg-orange-50 text-orange-700 rounded px-2 py-0.5 text-xs font-medium max-w-[110px] truncate"
                                                title="{{ $item->productVariant->product->name ?? '' }}">
                                                <i
                                                    class="fas fa-hamburger mr-1 text-orange-400"></i>{{ Str::limit($item->productVariant->product->name ?? '', 18) }}
                                            </span>
                                        @endforeach
                                        @if ($relatedCombo->comboItems->count() > 3)
                                            <span
                                                class="inline-flex items-center bg-gray-100 text-gray-600 rounded px-2 py-0.5 text-xs font-medium ml-1">+{{ $relatedCombo->comboItems->count() - 3 }}
                                                món</span>
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
</x-customer-container>
@endsection
@section('scripts')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script>
        window.csrfToken = document.querySelector('meta[name=csrf-token]').getAttribute('content');
        window.comboId = {{ $combo->id }};
    </script>
    @include('partials.customer.branch-check')
    <script src="{{ asset('js/Customer/Shop/combo.js') }}"></script>
    <script src="https://js.pusher.com/7.2/pusher.min.js"></script>
    <script>
        // Pusher configuration
        window.pusherKey = '{{ config('broadcasting.connections.pusher.key') }}';
        window.pusherCluster = '{{ config('broadcasting.connections.pusher.options.cluster') }}';
    </script>

    <!-- Pusher listeners for review and reply events -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Prevent multiple initializations
        if (window.comboPusherInitialized) {
            console.log('Combo Pusher already initialized, skipping...');
            return;
        }
        
        console.log('Combo Pusher setup starting...', {
            pusherKey: window.pusherKey,
            comboId: window.comboId
        });
        
        // Setup Pusher for realtime updates
        if (typeof Pusher !== 'undefined' && window.pusherKey && window.comboId) {
            try {
                const pusher = new Pusher(window.pusherKey, {
                    cluster: window.pusherCluster,
                    encrypted: true
                });

                console.log('Pusher initialized for combo:', window.comboId);
                window.comboPusherInitialized = true;

                // Subscribe to combo reviews channel for review deletion
                const comboChannel = pusher.subscribe('combo-reviews.' + window.comboId);
                console.log('Subscribed to combo channel:', 'combo-reviews.' + window.comboId);
                
                // Listen for deleted reviews
                comboChannel.bind('review-deleted', function(data) {
                    console.log('Review deleted event received for combo:', data);
                    
                    // Remove review from DOM
                    const reviewElement = document.querySelector(`[data-review-id="${data.review_id}"]`);
                    console.log('Found review element:', reviewElement);
                    
                    if (reviewElement) {
                        // Add fade out animation
                        reviewElement.style.transition = 'opacity 0.3s ease';
                        reviewElement.style.opacity = '0';
                        
                        setTimeout(() => {
                            reviewElement.remove();
                            console.log('Review element removed from DOM');
                            
                            // Update review count if exists
                            const reviewCountElement = document.querySelector('.review-count');
                            if (reviewCountElement) {
                                const currentCount = Math.max(0, (parseInt(reviewCountElement.textContent) || 0) - 1);
                                reviewCountElement.textContent = currentCount;
                                console.log('Updated review count to:', currentCount);
                            }
                        }, 300);
                        
                        // Show notification
                        if (typeof dtmodalShowToast === 'function') {
                            dtmodalShowToast('info', {
                                title: 'Bình luận đã xóa',
                                message: 'Một bình luận đã bị xóa bởi chi nhánh'
                            });
                        }
                    } else {
                        console.log('Review element not found for ID:', data.review_id);
                    }
                });

                // Subscribe to reply channels for each review
                const reviewElements = document.querySelectorAll('[data-review-id]');
                console.log('Found review elements:', reviewElements.length);
                
                // Track subscribed channels to avoid duplicates
                if (!window.subscribedChannels) {
                    window.subscribedChannels = new Set();
                }
                
                reviewElements.forEach(reviewElement => {
                    const reviewId = reviewElement.getAttribute('data-review-id');
                    if (reviewId) {
                        const channelName = 'review-replies.' + reviewId;
                        
                        // Skip if already subscribed
                        if (window.subscribedChannels.has(channelName)) {
                            console.log('Already subscribed to channel:', channelName);
                            return;
                        }
                        
                        const replyChannel = pusher.subscribe(channelName);
                        window.subscribedChannels.add(channelName);
                        console.log('Subscribed to reply channel:', channelName);

                        // Listen for new replies
                        replyChannel.bind('new-reply', function(data) {
                            console.log('New reply received for combo review:', reviewId, data);
                            
                            if (data.reply && data.reply.is_official) {
                                // Check if reply already exists to avoid duplicates
                                const existingReply = document.querySelector(`[data-reply-id="${data.reply.id}"]`);
                                if (existingReply) {
                                    console.log('Reply already exists, skipping duplicate');
                                    return;
                                }
                                
                                // Find replies container within this review
                                const repliesContainer = reviewElement.querySelector('.replies-list');
                                if (repliesContainer) {
                                    // Add new reply
                                    const replyHtml = `
                                        <div class="reply-item" data-reply-id="${data.reply.id}">
                                            <div class="reply-bubble">
                                                <div class="reply-header">
                                                    <span class="reply-author">Chi nhánh</span>
                                                    <span class="reply-time">${new Date(data.reply.reply_date).toLocaleString('vi-VN')}</span>
                                                </div>
                                                <div class="reply-content">${data.reply.reply}</div>
                                            </div>
                                        </div>
                                    `;
                                    repliesContainer.insertAdjacentHTML('beforeend', replyHtml);
                                    console.log('Reply added to DOM via Pusher');
                                }
                                
                                // Show notification only once per reply
                                if (typeof dtmodalShowToast === 'function') {
                                    // Track notified replies to avoid duplicates
                                    if (!window.notifiedReplies) {
                                        window.notifiedReplies = new Set();
                                    }
                                    
                                    if (!window.notifiedReplies.has(data.reply.id)) {
                                        window.notifiedReplies.add(data.reply.id);
                                        dtmodalShowToast('notification', {
                                            title: 'Phản hồi mới',
                                            message: 'Chi nhánh đã phản hồi bình luận của bạn!'
                                        });
                                        console.log('Notification shown for reply:', data.reply.id);
                                    } else {
                                        console.log('Notification already shown for reply:', data.reply.id);
                                    }
                                }
                            }
                        });

                        // Listen for deleted replies
                        replyChannel.bind('reply-deleted', function(data) {
                            console.log('Reply deleted for combo review:', reviewId, data);
                            
                            // Remove reply from DOM
                            const replyElement = document.querySelector(`[data-reply-id="${data.reply_id}"]`);
                            if (replyElement) {
                                // Add fade out animation
                                replyElement.style.transition = 'opacity 0.3s ease';
                                replyElement.style.opacity = '0';
                                
                                setTimeout(() => {
                                    replyElement.remove();
                                }, 300);
                                
                                // Show notification
                                if (typeof dtmodalShowToast === 'function') {
                                    dtmodalShowToast('info', {
                                        title: 'Phản hồi đã xóa',
                                        message: 'Một phản hồi đã bị xóa bởi chi nhánh'
                                    });
                                }
                            }
                        });
                    }
                });

            } catch (error) {
                console.error('Pusher setup error for combo events:', error);
            }
        } else {
            console.log('Pusher setup skipped:', {
                pusherAvailable: typeof Pusher !== 'undefined',
                pusherKey: !!window.pusherKey,
                comboId: !!window.comboId
            });
        }
    });
    </script>
@endsection
