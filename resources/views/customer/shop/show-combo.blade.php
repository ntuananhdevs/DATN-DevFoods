@extends('layouts.customer.fullLayoutMaster')

@section('content')

    <style>
        .combo-detail-page { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: linear-gradient(135deg, #ffff 0%, #ffff 100%); line-height: 1.6; color: #1f2937; min-height: 100vh; }
        .combo-detail-page * { margin: 0; padding: 0; box-sizing: border-box; }
        .combo-detail-page .combo-content { max-width: 1280px; margin:0 auto; padding-top:30px; }
        .combo-detail-page .main-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 2rem; }
        .combo-detail-page .left-column { display: flex; flex-direction: column; gap: 1.5rem; }
        .combo-detail-page .product-card { background: white; border-radius: 1rem; padding: 1.5rem; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1); border: 1px solid #e2e8f0; }
        .combo-detail-page .product-image { position: relative; border-radius: 0.75rem; overflow: hidden; margin-bottom: 1.5rem; group: hover; }
        .combo-detail-page .product-image img { width: 100%; height: 320px; object-fit: cover; transition: transform 0.3s; }
        .combo-detail-page .product-image:hover img { transform: scale(1.05); }
        .combo-detail-page .image-badges { position: absolute; top: 1rem; left: 1rem; display: flex; flex-direction: column; gap: 0.5rem; }
        .combo-detail-page .badge { padding: 0.5rem 0.75rem; border-radius: 9999px; font-size: 0.875rem; font-weight: 600; color: white; }
        .combo-detail-page .badge-popular { background: linear-gradient(135deg, #f59e0b, #f97316); }
        .combo-detail-page .badge-sale { background: linear-gradient(135deg, #ef4444, #dc2626); }
        .combo-detail-page .product-info { border-top: 1px solid #f1f5f9; padding-top: 1.5rem; }
        .combo-detail-page .product-header { display: flex; align-items: start; justify-content: space-between; margin-bottom: 1rem; }
        .combo-detail-page .product-title { font-size: 2rem; font-weight: bold; background: linear-gradient(135deg, #f97316, #ea580c); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; margin-bottom: 0.5rem; }
        .combo-detail-page .product-subtitle { color: #64748b; font-size: 1.125rem; }
        .combo-detail-page .price-section { text-align: right; }
        .combo-detail-page .current-price { font-size: 2rem; font-weight: bold; color: #ef4444; margin-bottom: 0.25rem; }
        .combo-detail-page .original-price { color: #94a3b8; text-decoration: line-through; font-size: 1.125rem; }
        .combo-detail-page .savings { background: #10b981; color: white; padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.875rem; font-weight: 600; margin-top: 0.5rem; display: inline-block; }
        .combo-detail-page .rating-section { display: flex; align-items: center; gap: 1rem; margin-bottom: 1.5rem; }
        .combo-detail-page .stars { display: flex; gap: 0.25rem; }
        .combo-detail-page .star { color: #fbbf24; font-size: 1.125rem; }
        .combo-detail-page .rating-text { color: #64748b; }
        .combo-detail-page .rating-badge { background: linear-gradient(135deg, #fbbf24, #f59e0b); color: white; padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.875rem; font-weight: 600; }
        .combo-detail-page .combo-items { background: #f8fafc; border-radius: 0.75rem; padding: 1rem; margin-bottom: 1.5rem; }
        .combo-detail-page .section-title { display: flex; align-items: center; gap: 0.5rem; font-size: 1.125rem; font-weight: 600; color: #1e293b; margin-bottom: 1rem; }
        .combo-detail-page .combo-item { display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem; background: white; border-radius: 0.5rem; margin-bottom: 0.75rem; }
        .combo-detail-page .combo-item:last-child { margin-bottom: 0; }
        .combo-detail-page .item-image { width: 48px; height: 48px; border-radius: 0.5rem; object-fit: cover; }
        .combo-detail-page .item-details { flex: 1; }
        .combo-detail-page .item-name { font-weight: 600; color: #1e293b; margin-bottom: 0.25rem; }
        .combo-detail-page .item-description { font-size: 0.875rem; color: #64748b; }
        .combo-detail-page .item-price { color: #f97316; font-weight: 600; }
        .combo-detail-page .customer-photos { border-top: 1px solid #f1f5f9; padding-top: 1.5rem; }
        .combo-detail-page .photos-grid { display: grid; grid-template-columns: repeat(6, 1fr); gap: 0.5rem; }
        .combo-detail-page .customer-photo { aspect-ratio: 1; border-radius: 0.5rem; overflow: hidden; cursor: pointer; transition: transform 0.2s; }
        .combo-detail-page .customer-photo:hover { transform: scale(1.05); }
        .combo-detail-page .customer-photo img { width: 100%; height: 100%; object-fit: cover; }
        .combo-detail-page .nutrition-grid { display: grid; grid-template-columns: repeat(6, 1fr); gap: 1rem; }
        .combo-detail-page .nutrition-item { text-align: center; padding: 1rem; background: #f8fafc; border-radius: 0.75rem; border: 1px solid #e2e8f0; }
        .combo-detail-page .nutrition-value { font-size: 1.5rem; font-weight: bold; color: #f97316; margin-bottom: 0.5rem; }
        .combo-detail-page .nutrition-label { font-size: 0.875rem; color: #64748b; }
        .combo-detail-page .right-column { display: flex; flex-direction: column; gap: 1.5rem; }
        .combo-detail-page .order-card { background: white; border-radius: 1rem; padding: 1.5rem; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1); border: 1px solid #e2e8f0; position: sticky; top: 6rem; }
        .combo-detail-page .custom-option { margin-bottom: 1.5rem; }
        .combo-detail-page .option-label { display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.75rem; }
        .combo-detail-page .option-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.5rem; }
        .combo-detail-page .option-grid-3 { display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.5rem; }
        .combo-detail-page .option-grid-1 { display: flex; flex-direction: column; gap: 0.5rem; }
        .combo-detail-page .choice-btn { padding: 0.75rem; font-size: 0.875rem; border: 2px solid #e2e8f0; border-radius: 0.5rem; background: white; color: #374151; cursor: pointer; transition: all 0.2s; text-align: left; }
        .combo-detail-page .choice-btn:hover { border-color: #fed7aa; }
        .combo-detail-page .choice-btn.active { border-color: #f97316; background: #fff7ed; color: #ea580c; }
        .combo-detail-page .extra-cost { font-size: 0.75rem; color: #64748b; display: block; }
        .combo-detail-page .extra-cost-inline { font-size: 0.75rem; color: #64748b; float: right; }
        .combo-detail-page .quantity-section { margin-bottom: 1.5rem; }
        .combo-detail-page .quantity-controls { display: flex; align-items: center; justify-content: center; }
        .combo-detail-page .qty-btn { width: 40px; height: 40px; border: 2px solid #e2e8f0; background: #f8fafc; color: #64748b; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; justify-content: center; }
        .combo-detail-page .qty-btn:hover { background: #f97316; color: white; border-color: #f97316; }
        .combo-detail-page .qty-btn:first-child { border-radius: 0.5rem 0 0 0.5rem; }
        .combo-detail-page .qty-btn:last-child { border-radius: 0 0.5rem 0.5rem 0; }
        .combo-detail-page .qty-input { width: 64px; height: 40px; border: 2px solid #e2e8f0; border-left: none; border-right: none; background: white; text-align: center; font-weight: 600; }
        .combo-detail-page .add-to-cart { width: 100%; background: linear-gradient(135deg, #f97316, #ea580c); color: white; padding: 1rem 2rem; border: none; border-radius: 0.75rem; font-weight: 600; font-size: 1.125rem; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; justify-content: center; gap: 0.5rem; }
        .combo-detail-page .add-to-cart:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(249, 115, 22, 0.4); }
        .combo-detail-page .add-to-cart:disabled { opacity: 0.5; cursor: not-allowed; }
        .combo-detail-page .loading-spinner { width: 20px; height: 20px; border: 2px solid white; border-top: 2px solid transparent; border-radius: 50%; animation: spin 1s linear infinite; }
        @keyframes spin { to { transform: rotate(360deg); } }
        .combo-detail-page .related-section { margin-top: 3rem; }
        .combo-detail-page .related-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.5rem; }
        .combo-detail-page .related-card { cursor: pointer; transition: transform 0.2s; }
        .combo-detail-page .related-card:hover { transform: translateY(-4px); }
        .combo-detail-page .related-image { aspect-ratio: 16/9; border-radius: 0.75rem; overflow: hidden; margin-bottom: 1rem; }
        .combo-detail-page .related-image img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.3s; }
        .combo-detail-page .related-card:hover .related-image img { transform: scale(1.1); }
        .combo-detail-page .related-title { font-weight: 600; color: #1e293b; margin-bottom: 0.5rem; }
        .combo-detail-page .related-price { color: #f97316; font-weight: bold; font-size: 1.125rem; }
        @media (max-width: 1024px) { .combo-detail-page .main-grid { grid-template-columns: 1fr; gap: 2rem; } .combo-detail-page .nutrition-grid { grid-template-columns: repeat(3, 1fr); } .combo-detail-page .related-grid { grid-template-columns: 1fr; } }
        @media (max-width: 768px) { .combo-detail-page .product-title { font-size: 1.75rem; } .combo-detail-page .product-header { flex-direction: column; align-items: start; } .combo-detail-page .price-section { text-align: left; margin-top: 1rem; } .combo-detail-page .nutrition-grid { grid-template-columns: repeat(2, 1fr); } .combo-detail-page .photos-grid { grid-template-columns: repeat(4, 1fr); } .combo-detail-page .option-grid { grid-template-columns: 1fr; } }
    </style>


    <div class="combo-detail-page">
        <div class="combo-content">
            <div class="main-grid">
                <!-- Left Column -->
                <div class="left-column">
                    <!-- Main Product Section -->
                    <div class="product-card">
                        <!-- Product Image -->
                        <div class="product-image">
                            <img src="{{ $combo->image_url ?? asset('images/default-combo.png') }}" alt="{{ $combo->name }}">
                            <div class="image-badges">
                                @if($combo->status === 'selling')
                                    <span class="badge badge-popular">Phổ biến nhất</span>
                                @endif
                                @if($combo->original_price > $combo->price)
                                    <span class="badge badge-sale">Giảm {{ round((($combo->original_price - $combo->price)/$combo->original_price)*100) }}%</span>
                                @endif
                            </div>
                        </div>

                        <!-- Product Info -->
                        <div class="product-info">
                            <div class="product-header">
                                <div>
                                    <h1 class="product-title">{{ $combo->name }}</h1>
                                    <p class="product-subtitle">{{ $combo->description ?? 'Bữa ăn hoàn hảo cho những ai yêu thích hương vị đậm đà và thỏa mãn cơn đói' }}</p>
                                </div>
                                <div class="price-section">
                                    <div class="current-price" id="displayPrice">{{ number_format($combo->price, 0, ',', '.') }}đ</div>
                                    @if($combo->original_price > $combo->price)
                                        <div class="original-price">{{ number_format($combo->original_price, 0, ',', '.') }}đ</div>
                                        <div class="savings">Tiết kiệm {{ number_format($combo->original_price - $combo->price, 0, ',', '.') }}đ</div>
                                    @endif
                                </div>
                            </div>

                            <div class="rating-section">
                                <div class="stars">
                                    <i class="fas fa-star star"></i>
                                    <i class="fas fa-star star"></i>
                                    <i class="fas fa-star star"></i>
                                    <i class="fas fa-star star"></i>
                                    <i class="fas fa-star star"></i>
                                </div>
                                <span class="rating-text">4.8 (156 đánh giá)</span>
                                <span class="rating-badge">Xuất sắc</span>
                            </div>

                            <!-- Combo Items -->
                            <div class="combo-items">
                                <h3 class="section-title">
                                    <i class="fas fa-utensils"></i>
                                    Chi tiết combo
                                </h3>
                                @foreach($items as $item)
                                    <div class="combo-item">
                                        <img src="{{ $item['image'] ?? asset('images/default-placeholder.png') }}" alt="{{ $item['product_name'] }}" class="item-image">
                                        <div class="item-details">
                                            <h4 class="item-name">{{ $item['product_name'] }}</h4>
                                            <p class="item-description">
                                                @foreach($item['variant_values'] as $v)
                                                    {{ $v['attribute'] }}: {{ $v['value'] }}@if(!$loop->last), @endif
                                                @endforeach
                                            </p>
                                        </div>
                                        <div class="item-price">{{ number_format($item['variant_price'], 0, ',', '.') }}đ x {{ $item['quantity'] }}</div>
                                    </div>
                                @endforeach
                            </div>

                            <!-- Customer Photos (fix cứng) -->
                            <div class="customer-photos">
                                <h3 class="section-title">
                                    <i class="fas fa-camera"></i>
                                    Ảnh từ khách hàng
                                </h3>
                                <div class="photos-grid">
                                    <div class="customer-photo"><img src="/placeholder.svg?height=80&width=80&text=Photo1" alt="Customer photo 1"></div>
                                    <div class="customer-photo"><img src="/placeholder.svg?height=80&width=80&text=Photo2" alt="Customer photo 2"></div>
                                    <div class="customer-photo"><img src="/placeholder.svg?height=80&width=80&text=Photo3" alt="Customer photo 3"></div>
                                    <div class="customer-photo"><img src="/placeholder.svg?height=80&width=80&text=Photo4" alt="Customer photo 4"></div>
                                    <div class="customer-photo"><img src="/placeholder.svg?height=80&width=80&text=Photo5" alt="Customer photo 5"></div>
                                    <div class="customer-photo"><img src="/placeholder.svg?height=80&width=80&text=+12" alt="More photos"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Nutrition Info (fix cứng) -->
                    <div class="product-card">
                        <h3 class="section-title">
                            <i class="fas fa-chart-pie"></i>
                            Thông tin dinh dưỡng (1 combo)
                        </h3>
                        <div class="nutrition-grid">
                            <div class="nutrition-item"><div class="nutrition-value">850</div><div class="nutrition-label">Calories</div></div>
                            <div class="nutrition-item"><div class="nutrition-value">45g</div><div class="nutrition-label">Protein</div></div>
                            <div class="nutrition-item"><div class="nutrition-value">65g</div><div class="nutrition-label">Carbs</div></div>
                            <div class="nutrition-item"><div class="nutrition-value">35g</div><div class="nutrition-label">Fat</div></div>
                            <div class="nutrition-item"><div class="nutrition-value">1200mg</div><div class="nutrition-label">Sodium</div></div>
                            <div class="nutrition-item"><div class="nutrition-value">8g</div><div class="nutrition-label">Fiber</div></div>
                        </div>
                    </div>
                </div>

                <!-- Right Column - Order Section (fix cứng) -->
                <div class="right-column">
                    <div class="order-card">
                        <h3 class="section-title">Tùy chỉnh combo</h3>
                        <!-- Drink Selection -->
                        <div class="custom-option">
                            <label class="option-label">Chọn loại nước uống:</label>
                            <div class="option-grid">
                                <button class="choice-btn active" data-option="drink" data-value="Coca Cola">Coca Cola</button>
                                <button class="choice-btn" data-option="drink" data-value="Pepsi">Pepsi</button>
                                <button class="choice-btn" data-option="drink" data-value="Sprite">Sprite</button>
                                <button class="choice-btn" data-option="drink" data-value="Fanta">Fanta</button>
                                <button class="choice-btn" data-option="drink" data-value="Nước cam tươi" data-extra="5000">Nước cam tươi<span class="extra-cost">+5.000đ</span></button>
                            </div>
                        </div>
                        <!-- Fries Size -->
                        <div class="custom-option">
                            <label class="option-label">Kích thước khoai tây:</label>
                            <div class="option-grid-3">
                                <button class="choice-btn" data-option="fries" data-value="Medium">Medium</button>
                                <button class="choice-btn active" data-option="fries" data-value="Large">Large</button>
                                <button class="choice-btn" data-option="fries" data-value="Extra Large" data-extra="5000">Extra Large<span class="extra-cost">+5.000đ</span></button>
                            </div>
                        </div>
                        <!-- Toppings -->
                        <div class="custom-option">
                            <label class="option-label">Thêm topping burger:</label>
                            <div class="option-grid-1">
                                <button class="choice-btn active" data-option="topping" data-value="Không thêm">Không thêm</button>
                                <button class="choice-btn" data-option="topping" data-value="Thêm phô mai" data-extra="5000">Thêm phô mai<span class="extra-cost-inline">+5.000đ</span></button>
                                <button class="choice-btn" data-option="topping" data-value="Thêm thịt xông khói" data-extra="8000">Thêm thịt xông khói<span class="extra-cost-inline">+8.000đ</span></button>
                                <button class="choice-btn" data-option="topping" data-value="Thêm trứng" data-extra="3000">Thêm trứng<span class="extra-cost-inline">+3.000đ</span></button>
                            </div>
                        </div>
                        <!-- Quantity -->
                        <div class="quantity-section">
                            <label class="option-label">Số lượng:</label>
                            <div class="quantity-controls">
                                <button class="qty-btn" id="decreaseQty"><i class="fas fa-minus"></i></button>
                                <input type="number" class="qty-input" id="quantity" value="1" min="1" max="10" readonly>
                                <button class="qty-btn" id="increaseQty"><i class="fas fa-plus"></i></button>
                            </div>
                        </div>
                        <!-- Add to Cart -->
                        <button class="add-to-cart" id="addToCartBtn">
                            <i class="fas fa-shopping-cart"></i>
                            <span id="cartButtonText">Thêm vào giỏ - <span id="totalPrice">{{ number_format($combo->price, 0, ',', '.') }}đ</span></span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Related Products (combo cùng danh mục) -->
            @php
                $relatedCombos = ($combo->category && $combo->category->combos) ? $combo->category->combos->where('id', '!=', $combo->id)->take(3) : collect();
            @endphp
            @if($relatedCombos->count())
            <div class="related-section">
                <div class="product-card">
                    <h3 class="section-title">
                        <i class="fas fa-fire"></i>
                        Combo khác bạn có thể thích
                    </h3>
                    <div class="related-grid">
                        @foreach($relatedCombos as $relatedCombo)
                            <div class="related-card">
                                <div class="related-image">
                                    <img src="{{ $relatedCombo->image_url ?? asset('images/default-combo.png') }}" alt="{{ $relatedCombo->name }}">
                                </div>
                                <h4 class="related-title">{{ $relatedCombo->name }}</h4>
                                <div class="related-price">{{ number_format($relatedCombo->price, 0, ',', '.') }}đ</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

    <script>
        // Live Search AJAX
        document.addEventListener('DOMContentLoaded', function() {
            const input = document.getElementById('liveSearchInput');
            const resultsBox = document.getElementById('liveSearchResults');
            let timer = null;
            input.addEventListener('input', function() {
                const query = this.value.trim();
                if (timer) clearTimeout(timer);
                if (query.length < 2) {
                    resultsBox.style.display = 'none';
                    resultsBox.innerHTML = '';
                    return;
                }
                timer = setTimeout(() => {
                    fetchLiveSearch(query);
                }, 250);
            });

            function fetchLiveSearch(query) {
                fetch("{{ route('customer.search.ajax') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ search: query })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.results && data.results.length > 0) {
                        resultsBox.innerHTML = data.results.map(item => `
                            <a href="/customer/shop/product/${item.slug}" style="display: flex; align-items: center; gap: 12px; padding: 10px 14px; text-decoration: none; color: #1f2937; border-bottom: 1px solid #f1f5f9;">
                                <img src="${item.image_url}" alt="${item.name}" style="width: 40px; height: 40px; object-fit: cover; border-radius: 0.4rem;">
                                <span style="flex:1;">${item.name}</span>
                                <span style="color: #f97316; font-weight: 600;">${item.price.toLocaleString('vi-VN')}đ</span>
                            </a>
                        `).join('');
                        resultsBox.style.display = 'block';
                    } else {
                        resultsBox.innerHTML = '<div style="padding: 12px; color: #64748b;">Không tìm thấy sản phẩm phù hợp.</div>';
                        resultsBox.style.display = 'block';
                    }
                })
                .catch(() => {
                    resultsBox.innerHTML = '<div style="padding: 12px; color: #ef4444;">Lỗi tìm kiếm.</div>';
                    resultsBox.style.display = 'block';
                });
            }

            // Hide results when clicking outside
            document.addEventListener('click', function(e) {
                if (!input.contains(e.target) && !resultsBox.contains(e.target)) {
                    resultsBox.style.display = 'none';
                }
            });
        });
    </script>

@endsection
