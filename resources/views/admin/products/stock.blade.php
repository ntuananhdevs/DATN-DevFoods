@extends('layouts/admin/contentLayoutMaster')

@section('title', 'Áp dụng sản phẩm cho chi nhánh')

@section('content')
<style>
    /* Style cho branch card */
    .branch-card {
        background: white;
        border-radius: 0.75rem;
        border: 1px solid #e5e7eb;
        transition: all 0.2s;
    }

    .branch-card:hover {
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    /* Style cho nút */
    .btn {
        padding: 0.75rem 1.5rem;
        font-weight: 500;
        border-radius: 0.5rem;
        transition: all 0.2s;
    }

    .btn-primary {
        background-color: #3b82f6;
        color: white;
    }

    .btn-primary:hover {
        background-color: #2563eb;
    }

    .btn-secondary {
        background-color: #f1f5f9;
        color: #475569;
    }

    .btn-secondary:hover {
        background-color: #e2e8f0;
    }

    /* Style cho status badge */
    .status-badge {
        padding: 0.25rem 0.5rem;
        border-radius: 0.375rem;
        font-size: 0.75rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        white-space: nowrap;
    }

    .status-active {
        background-color: #064e3b;
        color: #d1fae5;
    }

    .status-inactive {
        background-color: #7f1d1d;
        color: #fee2e2;
    }

    /* Style cho stock input */
    .stock-input {
        width: 100%;
        padding: 0.5rem;
        border: 1px solid #e5e7eb;
        border-radius: 0.375rem;
        font-size: 0.875rem;
    }

    .stock-input:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.1);
    }

    .variant-stock {
        margin-top: 0.5rem;
        padding-top: 0.5rem;
        border-top: 1px solid #e5e7eb;
    }

    .variant-name {
        font-size: 0.875rem;
        color: #4b5563;
        margin-bottom: 0.25rem;
    }

    /* Style cho bulk stock section */
    .bulk-stock-section {
        background: white;
        border-radius: 0.75rem;
        border: 1px solid #e5e7eb;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .variant-checkbox {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem;
        border-radius: 0.375rem;
        cursor: pointer;
        transition: all 0.2s;
    }

    .variant-checkbox:hover {
        background-color: #f3f4f6;
    }

    .variant-checkbox input[type="checkbox"] {
        width: 1rem;
        height: 1rem;
    }

    .variant-list {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 1rem;
        margin-top: 1rem;
    }

    .variant-item {
        padding: 0.5rem;
        border: 1px solid #e5e7eb;
        border-radius: 0.375rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        cursor: pointer;
        transition: all 0.2s;
    }

    .variant-item:hover {
        background-color: #f3f4f6;
    }

    .variant-item.selected {
        background-color: #dbeafe;
        border-color: #3b82f6;
    }
</style>

<main class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-extrabold mb-2">Quản lý tồn kho</h1>
            <div class="flex items-center gap-4">
                <p class="text-gray-600 text-lg">{{ $product->name }}</p>
                <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-medium">
                    SKU: {{ $product->sku }}
                </span>
            </div>
        </div>
        <div class="flex gap-4">
            <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-secondary inline-flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                </svg>
                Chỉnh sửa sản phẩm
            </a>
        </div>
    </div>

    <!-- Bulk Stock Section -->
    <div class="bulk-stock-section">
        <div class="mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Nhập số lượng tổng</h3>
            <div class="flex gap-4">
                <div class="flex-1">
                    <input type="number" 
                           id="bulk-stock-input" 
                           min="0" 
                           class="stock-input" 
                           placeholder="Nhập số lượng tổng" />
                </div>
                <button type="button" 
                        id="apply-bulk-stock" 
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    Áp dụng
                </button>
            </div>
        </div>

        <div>
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Chọn biến thể áp dụng</h3>
            <div class="variant-list">
                @foreach($product->variants as $variant)
                    <div class="variant-item" data-variant-id="{{ $variant->id }}">
                        <input type="checkbox" 
                               name="selected_variants[]" 
                               value="{{ $variant->id }}" 
                               class="form-checkbox text-blue-600 rounded" />
                        <span class="text-sm">
                            @foreach($variant->variantValues as $value)
                                {{ $value->attribute->name }}: 
                                {{ $value->value }}
                                @if(!$loop->last), @endif
                            @endforeach
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="p-6">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-semibold text-gray-900">Chi nhánh áp dụng</h3>
                <button type="button" 
                        id="select-all-branches" 
                        class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                    Chọn tất cả
                </button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($branches as $branch)
                <div class="branch-card p-6">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h4 class="text-lg font-semibold text-gray-900">{{ $branch->name }}</h4>
                            <p class="text-sm text-gray-500 mt-1">{{ $branch->address }}</p>
                        </div>
                        <span class="status-badge {{ $branch->active ? 'status-active' : 'status-inactive' }}">
                            {{ $branch->active ? 'Đang hoạt động' : 'Ngừng hoạt động' }}
                        </span>
                    </div>
                    
                    <div class="flex items-center gap-2 mb-4">
                        <label class="inline-flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" 
                                   name="branch_selection[]" 
                                   value="{{ $branch->id }}" 
                                   class="form-checkbox text-blue-600 rounded" />
                            <span class="text-sm text-gray-700">Chọn chi nhánh này</span>
                        </label>
                    </div>

                    <div class="variant-stocks space-y-3">
                        @foreach($product->variants as $variant)
                        <div class="variant-stock" data-variant-id="{{ $variant->id }}">
                            <div class="text-sm font-medium text-gray-700 mb-1">
                                @foreach($variant->variantValues as $value)
                                    {{ $value->attribute->name }}: 
                                    {{ $value->value }}
                                    @if(!$loop->last), @endif
                                @endforeach
                            </div>
                            <input type="number" 
                                   name="stocks[{{ $branch->id }}][{{ $variant->id }}]" 
                                   value="{{ $variant->branchStocks->where('branch_id', $branch->id)->first()?->stock_quantity ?? 0 }}"
                                   min="0"
                                   class="stock-input"
                                   placeholder="Nhập số lượng" />
                        </div>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>

            <div class="flex justify-end mt-8">
                <button type="button" 
                        id="save-stocks" 
                        class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    Lưu thay đổi
                </button>
            </div>
        </div>
    </div>
</main>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const bulkStockInput = document.getElementById('bulk-stock-input');
        const applyBulkStockBtn = document.getElementById('apply-bulk-stock');
        const selectAllBranchesBtn = document.getElementById('select-all-branches');
        const saveStocksBtn = document.getElementById('save-stocks');
        let isAllSelected = false;

        // Xử lý chọn tất cả chi nhánh
        selectAllBranchesBtn.addEventListener('click', () => {
            isAllSelected = !isAllSelected;
            document.querySelectorAll('input[name="branch_selection[]"]').forEach(input => {
                input.checked = isAllSelected;
            });
            selectAllBranchesBtn.textContent = isAllSelected ? 'Bỏ chọn tất cả' : 'Chọn tất cả';
        });

        // Xử lý chọn biến thể
        document.querySelectorAll('.variant-item').forEach(item => {
            item.addEventListener('click', (e) => {
                if (e.target.type !== 'checkbox') {
                    const checkbox = item.querySelector('input[type="checkbox"]');
                    checkbox.checked = !checkbox.checked;
                    item.classList.toggle('selected', checkbox.checked);
                }
            });
        });

        // Xử lý áp dụng số lượng tổng
        applyBulkStockBtn.addEventListener('click', () => {
            const stockValue = parseInt(bulkStockInput.value);
            if (isNaN(stockValue) || stockValue < 0) {
                alert('Vui lòng nhập số lượng hợp lệ');
                return;
            }

            const selectedVariants = Array.from(document.querySelectorAll('input[name="selected_variants[]"]:checked'))
                .map(input => input.value);

            if (selectedVariants.length === 0) {
                alert('Vui lòng chọn ít nhất một biến thể');
                return;
            }

            const selectedBranches = Array.from(document.querySelectorAll('input[name="branch_selection[]"]:checked'))
                .map(input => input.value);

            if (selectedBranches.length === 0) {
                alert('Vui lòng chọn ít nhất một chi nhánh');
                return;
            }

            // Áp dụng số lượng cho các biến thể và chi nhánh đã chọn
            selectedBranches.forEach(branchId => {
                selectedVariants.forEach(variantId => {
                    const input = document.querySelector(`input[name="stocks[${branchId}][${variantId}]"]`);
                    if (input) {
                        input.value = stockValue;
                    }
                });
            });
        });

        // Xử lý lưu thay đổi
        saveStocksBtn.addEventListener('click', () => {
            const data = {
                stocks: {}
            };

            document.querySelectorAll('.branch-card').forEach(card => {
                const branchId = card.querySelector('input[name="branch_selection[]"]').value;
                data.stocks[branchId] = {};

                card.querySelectorAll('.variant-stock').forEach(variantStock => {
                    const variantId = variantStock.dataset.variantId;
                const branchId = card.querySelector('input[type="checkbox"]').name.match(/\[(\d+)\]/)[1];
                const isActive = card.querySelector('input[type="checkbox"]').checked;
                data.active[branchId] = isActive;

                // Collect stocks for each variant
                data.stocks[branchId] = {};
                card.querySelectorAll('.variant-stock input').forEach(input => {
                    const variantId = input.name.match(/\[(\d+)\]$/)[1];
                    data.stocks[branchId][variantId] = parseInt(input.value) || 0;
                });
            });

            // Send to server
            fetch('{{ route("admin.products.update-stocks", $product) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Cập nhật chi nhánh và số lượng thành công');
                } else {
                    alert('Có lỗi xảy ra khi cập nhật');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Có lỗi xảy ra khi cập nhật');
            });
        });

        // Add keyboard shortcuts
        document.addEventListener('keydown', (e) => {
            // Ctrl/Cmd + S to save
            if ((e.ctrlKey || e.metaKey) && e.key === 's') {
                e.preventDefault();
                saveBranchesBtn.click();
            }
        });
    });
</script>
@endsection 