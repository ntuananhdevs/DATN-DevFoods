@forelse($products as $product)
<tr class="border-b">
    <td class="py-3 px-4">
        <input type="checkbox" name="selected_products[]" value="{{ $product->id }}" class="product-checkbox rounded border-gray-300">
    </td>
    <td class="py-3 px-4 font-medium">{{ $product->sku }}</td>
    <td class="py-3 px-4">
        <div class="h-12 w-12 rounded-md bg-muted flex items-center justify-center overflow-hidden" style="width:100px; height:60px; border-radius:4px; background:#f3f4f6;">
            @php
                $primaryImg = $product->images->where('is_primary', true)->first() ?? $product->images->first();
            @endphp
            @if($primaryImg)
                <img src="{{ Storage::disk('s3')->url($primaryImg->img) }}" alt="{{ $product->name }}" style="width:100%; height:100%; object-fit:cover; border-radius:5px;" />
            @else
                <span class="text-xs text-gray-400">No image</span>
            @endif
        </div>
    </td>
    <td class="py-3 px-4">
        <div class="font-medium">{{ $product->name }}</div>
    </td>
    <td class="py-3 px-4">{{ $product->category->name ?? 'N/A' }}</td>
    <td class="py-3 px-4 text-right">
        {{ number_format($product->base_price, 0, ',', '.') }} đ
    </td>
    <td class="py-3 px-4 text-center">
        @php
            $totalStock = 0;
            foreach ($product->variants as $variant) {
                $totalStock += $variant->branchStocks->sum('stock_quantity');
            }
            if ($totalStock == 0) {
                $stockClass = 'out-of-stock';
                $stockText = 'Hết hàng';
            } elseif ($totalStock > 0 && $totalStock < 10) {
                $stockClass = 'low-stock';
                $stockText = 'Sắp hết ('.$totalStock.')';
            } else {
                $stockClass = 'in-stock';
                $stockText = $totalStock;
            }
        @endphp
        <span class="stock-badge {{ $stockClass }}">
            {{ $stockText }}
        </span>
    </td>
    <td class="py-3 px-4">
        @php
            switch ($product->status) {
                case 'selling':
                    $statusText = 'Đang bán';
                    $statusClass = 'selling';
                    break;
                case 'coming_soon':
                    $statusText = 'Sắp ra mắt';
                    $statusClass = 'coming-soon';
                    break;
                case 'discontinued':
                default:
                    $statusText = 'Ngừng bán';
                    $statusClass = 'discontinued';
                    break;
            }
        @endphp
        <span class="product-status {{ $statusClass }}">
            {{ $statusText }}
        </span>
    </td>
    <td class="py-3 px-4">
        <div class="flex justify-center space-x-1">
            <a href="{{ route('admin.products.show', $product->id) }}"
                class="flex items-center justify-center rounded-md hover:bg-accent p-2"
                title="Xem chi tiết">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                    <circle cx="12" cy="12" r="3"></circle>
                </svg>
            </a>
            <a href="{{ route('admin.products.edit', $product->id) }}"
                class="flex items-center justify-center rounded-md hover:bg-accent p-2"
                title="Chỉnh sửa">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                </svg>
            </a>
            <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" class="inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này?')">
                @csrf
                @method('DELETE')
                <button type="submit"
                    class="flex items-center justify-center rounded-md hover:bg-destructive hover:text-destructive-foreground p-2"
                    title="Xóa">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="3,6 5,6 21,6"></polyline>
                        <path d="M19,6v14a2,2 0 0,1 -2,2H7a2,2 0 0,1 -2,-2V6m3,0V4a2,2 0 0,1 2,-2h4a2,2 0 0,1 2,2v2"></path>
                        <line x1="10" y1="11" x2="10" y2="17"></line>
                        <line x1="14" y1="11" x2="14" y2="17"></line>
                    </svg>
                </button>
            </form>
        </div>
    </td>
</tr>
@empty
<tr>
    <td colspan="9" class="py-8 text-center text-gray-500">
        <div class="flex flex-col items-center">
            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" class="mb-4 text-gray-400">
                <circle cx="11" cy="11" r="8"></circle>
                <path d="M21 21l-4.35-4.35"></path>
            </svg>
            <p class="text-lg font-medium mb-2">Không tìm thấy sản phẩm</p>
            <p class="text-sm">Thử thay đổi từ khóa tìm kiếm hoặc bộ lọc</p>
        </div>
    </td>
</tr>
@endforelse