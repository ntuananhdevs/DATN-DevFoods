@php
    $comboCategory = $categories->first(function($cat) {
        return stripos($cat->name, 'combo') !== false;
    });
    $otherCategories = $categories->filter(function($cat) use ($comboCategory) {
        return !$comboCategory || $cat->id !== $comboCategory->id;
    });
    $sectionIndex = 0;
@endphp

@if($comboCategory && $comboCategory->combos->count() > 0)
    <section class="category-section" id="category-section-{{ $comboCategory->id }}" data-section-index="{{ $sectionIndex }}" style="display: block; margin-bottom: 48px;">
        <h2 class="text-2xl font-bold mb-4 text-orange-600 flex items-center gap-2">
            <span>{{ $comboCategory->name }}</span>
        </h2>
        <div class="skeletons-container" style="display:none;">
            @for($i = 0; $i < $comboCategory->combos->count(); $i++)
                <div class="skeleton-card"></div>
            @endfor
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-6 product-cards-container">
            @foreach($comboCategory->combos as $combo)
                @include('customer.shop._combo_card', ['combo' => $combo])
            @endforeach
        </div>
    </section>
    @php $sectionIndex++; @endphp
@endif

@foreach($otherCategories as $category)
    @if($category->products->count() > 0)
    <section class="category-section" id="category-section-{{ $category->id }}" data-section-index="{{ $sectionIndex }}" style="display: block; margin-bottom: 48px;">
        <h2 class="text-2xl font-bold mb-4 text-orange-600 flex items-center gap-2">
            <span>{{ $category->name }}</span>
        </h2>
        <div class="skeletons-container" style="display:none;">
            @for($i = 0; $i < $category->products->count(); $i++)
                <div class="skeleton-card"></div>
            @endfor
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-6 product-cards-container">
            @foreach($category->products as $product)
                @include('customer.shop._product_card', ['product' => $product])
            @endforeach
        </div>
    </section>
    @php $sectionIndex++; @endphp
    @endif
@endforeach

@php
    $hasAnyProducts = false;
    foreach($categories as $category) {
        if (stripos($category->name, 'combo') !== false) {
            if ($category->combos->count() > 0) {
                $hasAnyProducts = true;
                break;
            }
        } else {
            if ($category->products->count() > 0) {
                $hasAnyProducts = true;
                break;
            }
        }
    }
@endphp

@if(!$hasAnyProducts)
    <div class="text-center py-12">
        <div class="text-gray-500 text-lg mb-4">
            <i class="fas fa-box-open text-4xl mb-4"></i>
            <p>Không có sản phẩm nào được tìm thấy.</p>
            <p class="text-sm text-gray-400 mt-2">Vui lòng thử từ khóa khác hoặc thay đổi bộ lọc.</p>
        </div>
    </div>
@endif