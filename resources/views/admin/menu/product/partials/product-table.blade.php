@forelse($products as $product)
<tr class="border-b">
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
        @if($product->trashed())
            <span class="product-status deleted">
                Đã xóa
            </span>
        @else
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
        @endif
    </td>
    <td class="py-3 px-4">
        <div class="flex justify-center space-x-1">
            @if($product->trashed())
                <!-- Nút khôi phục -->
                <form action="{{ route('admin.products.restore', $product->id) }}" method="POST" class="restore-form">
                    @csrf
                    @method('PATCH')
                    <button type="button" class="h-8 w-8 p-0 flex items-center justify-center rounded-md hover:bg-accent text-green-600"
                        onclick="dtmodalConfirmRestore({
                                title: 'Xác nhận khôi phục sản phẩm',
                                subtitle: 'Bạn có chắc chắn muốn khôi phục sản phẩm này?',
                                itemName: '{{ $product->name }}',
                                onConfirm: () => this.closest('form').submit()
                            })"
                        title="Khôi phục">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 12a9 9 0 0 1 9-9 9.75 9.75 0 0 1 6.74 2.74L21 8"></path>
                            <path d="M21 3v5h-5"></path>
                            <path d="M21 12a9 9 0 0 1-9 9 9.75 9.75 0 0 1-6.74-2.74L3 16"></path>
                            <path d="M8 16l-5 5v-5h5"></path>
                        </svg>
                    </button>
                </form>
                <!-- Nút xóa hoàn toàn -->
                <form action="{{ route('admin.products.forceDelete', $product->id) }}" method="POST" class="force-delete-form">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="h-8 w-8 p-0 flex items-center justify-center rounded-md hover:bg-accent text-red-600"
                        onclick="dtmodalConfirmForceDelete({
                                title: 'Xác nhận xóa hoàn toàn',
                                subtitle: 'Bạn có chắc chắn muốn xóa hoàn toàn sản phẩm này?',
                                message: 'Hành động này không thể hoàn tác và sẽ xóa vĩnh viễn sản phẩm khỏi hệ thống.',
                                itemName: '{{ $product->name }}',
                                onConfirm: () => this.closest('form').submit()
                            })"
                        title="Xóa hoàn toàn">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 6h18"></path>
                            <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path>
                            <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path>
                            <line x1="10" y1="11" x2="10" y2="17"></line>
                            <line x1="14" y1="11" x2="14" y2="17"></line>
                        </svg>
                    </button>
                </form>
            @else
                <!-- Nút chỉnh sửa -->
                <a href="{{ route('admin.products.edit', $product->id) }}"
                    class="flex items-center justify-center rounded-md hover:bg-accent p-2"
                    title="Chỉnh sửa">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                    </svg>
                </a>
                <!-- Nút xóa mềm -->
                <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" class="delete-form">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="h-8 w-8 p-0 flex items-center justify-center rounded-md hover:bg-accent"
                        onclick="dtmodalConfirmDelete({
                                title: 'Xác nhận ẩn sản phẩm',
                                subtitle: 'Bạn có chắc chắn muốn ẩn sản phẩm này?',
                                message: 'Sản phẩm sẽ được ẩn khỏi danh sách nhưng vẫn có thể khôi phục.',
                                itemName: '{{ $product->name }}',
                                onConfirm: () => this.closest('form').submit()
                            })"
                        title="Ẩn sản phẩm">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 6h18"></path>
                            <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path>
                            <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path>
                        </svg>
                    </button>
                </form>
            @endif
        </div>
    </td>
</tr>
@empty
<tr>
    <td colspan="8" class="text-center py-4">
        <div class="flex flex-col items-center justify-center text-muted-foreground">
            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mb-2">
                <path d="M6 2L3 6v13a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"></path>
                <path d="M3 6h18"></path>
                <path d="M16 10a4 4 0 0 1-8 0"></path>
            </svg>
            <h3 class="text-lg font-medium">Không có sản phẩm nào</h3>
            <p class="text-sm">Hãy thêm sản phẩm mới để bắt đầu</p>
        </div>
    </td>
</tr>
@endforelse