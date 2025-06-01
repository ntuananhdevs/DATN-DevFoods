@extends('layouts/admin/contentLayoutMaster')

@section('title', 'Quản lý tồn kho')

@include('components.modal')

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

    /* Tabs styling */
    .tabs {
        display: flex;
        border-bottom: 1px solid #e5e7eb;
        margin-bottom: 1.5rem;
    }

    .tab {
        padding: 1rem 1.5rem;
        font-weight: 500;
        cursor: pointer;
        border-bottom: 2px solid transparent;
        transition: all 0.2s;
    }

    .tab:hover {
        color: #3b82f6;
    }

    .tab.active {
        color: #3b82f6;
        border-bottom-color: #3b82f6;
    }

    .tab-content {
        display: none;
    }

    .tab-content.active {
        display: block;
    }

    /* Success and error messages */
    .alert {
        padding: 0.75rem 1rem;
        border-radius: 0.375rem;
        margin-bottom: 1rem;
    }

    .alert-success {
        background-color: #ecfdf5;
        border: 1px solid #10b981;
        color: #065f46;
    }

    .alert-error {
        background-color: #fef2f2;
        border: 1px solid #ef4444;
        color: #991b1b;
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

    <!-- Tabs Navigation -->
    <div class="tabs">
        <div class="tab active" data-tab="variants">Tồn kho biến thể sản phẩm</div>
        <div class="tab" data-tab="toppings">Tồn kho topping</div>
    </div>

    <!-- Variants Tab Content -->
    <div id="variants-tab" class="tab-content active">
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
                                @foreach($variant->productVariantDetails as $detail)
                                    @if(isset($detail->variantValue) && isset($detail->variantValue->attribute))
                                        {{ $detail->variantValue->attribute->name }}: 
                                        {{ $detail->variantValue->value }}
                                        @if(!$loop->last), @endif
                                    @endif
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
                            id="select-all-branches-variants" 
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
                                      name="branch_selection_variants[]" 
                                      value="{{ $branch->id }}" 
                                      class="form-checkbox text-blue-600 rounded" />
                                <span class="text-sm text-gray-700">Chọn chi nhánh này</span>
                            </label>
                        </div>

                        <div class="variant-stocks space-y-3">
                            @foreach($product->variants as $variant)
                            <div class="variant-stock" data-variant-id="{{ $variant->id }}">
                                <div class="text-sm font-medium text-gray-700 mb-1">
                                    @foreach($variant->productVariantDetails as $detail)
                                        @if(isset($detail->variantValue) && isset($detail->variantValue->attribute))
                                            {{ $detail->variantValue->attribute->name }}: 
                                            {{ $detail->variantValue->value }}
                                            @if(!$loop->last), @endif
                                        @endif
                                    @endforeach
                                </div>
                                <input type="number" 
                                      name="stocks[{{ $branch->id }}][{{ $variant->id }}]" 
                                      value="{{ isset($branchStocks[$branch->id][$variant->id]) ? $branchStocks[$branch->id][$variant->id] : 0 }}"
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
                            id="save-variant-stocks" 
                            class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        Lưu thay đổi
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Toppings Tab Content -->
    <div id="toppings-tab" class="tab-content">
        <!-- Bulk Topping Stock Section -->
        <div class="bulk-stock-section">
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Nhập số lượng tổng cho Topping</h3>
                <div class="flex gap-4">
                    <div class="flex-1">
                        <input type="number" 
                              id="bulk-topping-stock-input" 
                              min="0" 
                              class="stock-input" 
                              placeholder="Nhập số lượng tổng" />
                    </div>
                    <button type="button" 
                            id="apply-bulk-topping-stock" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        Áp dụng
                    </button>
                </div>
            </div>

            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Chọn topping áp dụng</h3>
                <div class="variant-list">
                    @if(isset($product->toppings) && count($product->toppings) > 0)
                        @foreach($product->toppings as $topping)
                            <div class="variant-item" data-topping-id="{{ $topping->id }}">
                                <input type="checkbox" 
                                      name="selected_toppings[]" 
                                      value="{{ $topping->id }}" 
                                      class="form-checkbox text-blue-600 rounded" />
                                <span class="text-sm">{{ $topping->name }} ({{ number_format($topping->price) }} đ)</span>
                            </div>
                        @endforeach
                    @else
                        <p class="text-gray-500">Không có topping nào cho sản phẩm này</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-semibold text-gray-900">Chi nhánh áp dụng</h3>
                    <button type="button" 
                            id="select-all-branches-toppings" 
                            class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                        Chọn tất cả
                    </button>
                </div>

                @if(isset($product->toppings) && count($product->toppings) > 0)
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
                                      name="branch_selection_toppings[]" 
                                      value="{{ $branch->id }}" 
                                      class="form-checkbox text-blue-600 rounded" />
                                <span class="text-sm text-gray-700">Chọn chi nhánh này</span>
                            </label>
                        </div>

                        <div class="topping-stocks space-y-3">
                            @foreach($product->toppings as $topping)
                            <div class="variant-stock" data-topping-id="{{ $topping->id }}">
                                <div class="flex items-center gap-3 mb-2">
                                    @if($topping->image)
                                        <img src="{{ Storage::disk('s3')->url($topping->image) }}" alt="{{ $topping->name }}" 
                                            class="w-10 h-10 object-cover rounded-md">
                                    @else
                                        <div class="w-10 h-10 bg-gray-100 rounded-md flex items-center justify-center">
                                            <i class="fas fa-utensils text-gray-400"></i>
                                        </div>
                                    @endif
                                    <div class="text-sm font-medium text-gray-700">
                                        {{ $topping->name }} ({{ number_format($topping->price) }} đ)
                                    </div>
                                </div>
                                <input type="number" 
                                      name="topping_stock[{{ $branch->id }}][{{ $topping->id }}]" 
                                      value="{{ isset($toppingStocks[$branch->id][$topping->id]) ? $toppingStocks[$branch->id][$topping->id] : 0 }}"
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
                            id="save-topping-stocks" 
                            class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        Lưu thay đổi
                    </button>
                </div>
                @else
                <div class="text-center py-8">
                    <p class="text-gray-500">Không có topping nào cho sản phẩm này</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</main>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Function to check for zero quantities and warn the user
        function checkZeroQuantities() {
            const stockInputs = document.querySelectorAll('input[type="number"]');
            const zeroQuantityItems = [];
            
            stockInputs.forEach(input => {
                if (parseInt(input.value) === 0) {
                    // Get product/variant/topping name
                    const stockItem = input.closest('.variant-stock');
                    let itemName = '';
                    
                    if (stockItem) {
                        // Check if it's a variant or topping
                        if (stockItem.querySelector('.text-sm.font-medium.text-gray-700')) {
                            itemName = stockItem.querySelector('.text-sm.font-medium.text-gray-700').textContent.trim();
                        } else if (stockItem.parentElement.previousElementSibling) {
                            // Try to get branch name
                            const branchCard = stockItem.closest('.branch-card');
                            const branchName = branchCard ? branchCard.querySelector('h4').textContent.trim() : '';
                            itemName = `${stockItem.querySelector('.text-sm').textContent.trim()} ở ${branchName}`;
                        }
                        
                        zeroQuantityItems.push(itemName);
                    }
                }
            });
            
            if (zeroQuantityItems.length > 0) {
                // Show warning
                dtmodalShowToast('warning', {
                    title: 'Cảnh báo!',
                    message: `Có ${zeroQuantityItems.length} sản phẩm có số lượng 0. Vui lòng kiểm tra lại.`
                });
                
                // Return array of items with zero quantity
                return zeroQuantityItems;
            }
            
            return false;
        }

        // Tabs functionality
        const tabs = document.querySelectorAll('.tab');
        const tabContents = document.querySelectorAll('.tab-content');
        
        tabs.forEach(tab => {
            tab.addEventListener('click', () => {
                // Remove active class from all tabs and tab contents
                tabs.forEach(t => t.classList.remove('active'));
                tabContents.forEach(c => c.classList.remove('active'));
                
                // Add active class to current tab and corresponding content
                tab.classList.add('active');
                document.getElementById(`${tab.dataset.tab}-tab`).classList.add('active');
            });
        });

        // Product Variants Section
        const bulkStockInput = document.getElementById('bulk-stock-input');
        const applyBulkStockBtn = document.getElementById('apply-bulk-stock');
        const selectAllBranchesVariantsBtn = document.getElementById('select-all-branches-variants');
        const saveVariantStocksBtn = document.getElementById('save-variant-stocks');
        let isAllVariantBranchesSelected = false;

        // Xử lý chọn tất cả chi nhánh cho variants
        selectAllBranchesVariantsBtn.addEventListener('click', () => {
            isAllVariantBranchesSelected = !isAllVariantBranchesSelected;
            document.querySelectorAll('input[name="branch_selection_variants[]"]').forEach(input => {
                input.checked = isAllVariantBranchesSelected;
            });
            selectAllBranchesVariantsBtn.textContent = isAllVariantBranchesSelected ? 'Bỏ chọn tất cả' : 'Chọn tất cả';
        });

        // Xử lý chọn biến thể
        document.querySelectorAll('.variant-item').forEach(item => {
            item.addEventListener('click', (e) => {
                if (e.target.type !== 'checkbox') {
                    const checkbox = item.querySelector('input[type="checkbox"]');
                    checkbox.checked = !checkbox.checked;
                    item.classList.toggle('selected', checkbox.checked);
                } else {
                    // Update UI when checkbox is directly clicked
                    item.classList.toggle('selected', e.target.checked);
                }
            });
        });

        // Xử lý áp dụng số lượng tổng cho biến thể
        applyBulkStockBtn.addEventListener('click', () => {
            const stockValue = parseInt(bulkStockInput.value);
            if (isNaN(stockValue) || stockValue < 0) {
                dtmodalShowToast('warning', {
                    title: 'Cảnh báo!',
                    message: 'Vui lòng nhập số lượng hợp lệ'
                });
                return;
            }

            const selectedVariants = Array.from(document.querySelectorAll('input[name="selected_variants[]"]:checked'))
                .map(input => input.value);

            if (selectedVariants.length === 0) {
                dtmodalShowToast('warning', {
                    title: 'Cảnh báo!',
                    message: 'Vui lòng chọn ít nhất một biến thể'
                });
                return;
            }

            const selectedBranches = Array.from(document.querySelectorAll('input[name="branch_selection_variants[]"]:checked'))
                .map(input => input.value);

            if (selectedBranches.length === 0) {
                dtmodalShowToast('warning', {
                    title: 'Cảnh báo!',
                    message: 'Vui lòng chọn ít nhất một chi nhánh'
                });
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
            
            dtmodalShowToast('success', {
                title: 'Thành công!',
                message: 'Đã áp dụng số lượng cho các biến thể đã chọn'
            });
        });

        // Xử lý lưu thay đổi biến thể
        saveVariantStocksBtn.addEventListener('click', () => {
            // Check for zero quantities first
            const zeroItems = checkZeroQuantities();
            if (zeroItems && zeroItems.length > 0) {
                if (!confirm(`Cảnh báo: Có ${zeroItems.length} sản phẩm có số lượng 0. Bạn có muốn tiếp tục lưu không?`)) {
                    return;
                }
            }

            const data = {
                stocks: {}
            };

            // Collect branch selections and stock data
            document.querySelectorAll('#variants-tab .branch-card').forEach(card => {
                const branchCheckbox = card.querySelector('input[name="branch_selection_variants[]"]');
                const branchId = branchCheckbox.value;
                
                // Initialize branch stocks
                data.stocks[branchId] = {};
                
                // Collect stock values for each variant in this branch
                card.querySelectorAll('.variant-stock').forEach(variantStock => {
                    const variantId = variantStock.getAttribute('data-variant-id');
                    const stockInput = variantStock.querySelector('input[type="number"]');
                    const stockValue = parseInt(stockInput.value) || 0;
                    
                    data.stocks[branchId][variantId] = stockValue;
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
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    dtmodalShowToast('success', {
                        title: 'Thành công!',
                        message: 'Cập nhật số lượng biến thể thành công'
                    });
                } else {
                    dtmodalShowToast('error', {
                        title: 'Lỗi!',
                        message: data.message || 'Có lỗi xảy ra khi cập nhật'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                dtmodalShowToast('error', {
                    title: 'Lỗi!',
                    message: 'Có lỗi xảy ra khi cập nhật: ' + error.message
                });
            });
        });

        // Topping Section
        const bulkToppingStockInput = document.getElementById('bulk-topping-stock-input');
        const applyBulkToppingStockBtn = document.getElementById('apply-bulk-topping-stock');
        const selectAllBranchesToppingsBtn = document.getElementById('select-all-branches-toppings');
        const saveToppingStocksBtn = document.getElementById('save-topping-stocks');
        let isAllToppingBranchesSelected = false;

        if (selectAllBranchesToppingsBtn) {
            // Xử lý chọn tất cả chi nhánh cho toppings
            selectAllBranchesToppingsBtn.addEventListener('click', () => {
                isAllToppingBranchesSelected = !isAllToppingBranchesSelected;
                document.querySelectorAll('input[name="branch_selection_toppings[]"]').forEach(input => {
                    input.checked = isAllToppingBranchesSelected;
                });
                selectAllBranchesToppingsBtn.textContent = isAllToppingBranchesSelected ? 'Bỏ chọn tất cả' : 'Chọn tất cả';
            });
        }

        // Xử lý chọn topping
        document.querySelectorAll('#toppings-tab .variant-item').forEach(item => {
            item.addEventListener('click', (e) => {
                if (e.target.type !== 'checkbox') {
                    const checkbox = item.querySelector('input[type="checkbox"]');
                    if (checkbox) {
                        checkbox.checked = !checkbox.checked;
                        item.classList.toggle('selected', checkbox.checked);
                    }
                } else {
                    // Update UI when checkbox is directly clicked
                    item.classList.toggle('selected', e.target.checked);
                }
            });
        });

        // Xử lý áp dụng số lượng tổng cho topping
        if (applyBulkToppingStockBtn) {
            applyBulkToppingStockBtn.addEventListener('click', () => {
                const stockValue = parseInt(bulkToppingStockInput.value);
                if (isNaN(stockValue) || stockValue < 0) {
                    dtmodalShowToast('warning', {
                        title: 'Cảnh báo!',
                        message: 'Vui lòng nhập số lượng hợp lệ'
                    });
                    return;
                }

                const selectedToppings = Array.from(document.querySelectorAll('input[name="selected_toppings[]"]:checked'))
                    .map(input => input.value);

                if (selectedToppings.length === 0) {
                    dtmodalShowToast('warning', {
                        title: 'Cảnh báo!',
                        message: 'Vui lòng chọn ít nhất một topping'
                    });
                    return;
                }

                const selectedBranches = Array.from(document.querySelectorAll('input[name="branch_selection_toppings[]"]:checked'))
                    .map(input => input.value);

                if (selectedBranches.length === 0) {
                    dtmodalShowToast('warning', {
                        title: 'Cảnh báo!',
                        message: 'Vui lòng chọn ít nhất một chi nhánh'
                    });
                    return;
                }

                // Áp dụng số lượng cho các topping và chi nhánh đã chọn
                selectedBranches.forEach(branchId => {
                    selectedToppings.forEach(toppingId => {
                        const input = document.querySelector(`input[name="topping_stock[${branchId}][${toppingId}]"]`);
                        if (input) {
                            input.value = stockValue;
                        }
                    });
                });
                
                dtmodalShowToast('success', {
                    title: 'Thành công!',
                    message: 'Đã áp dụng số lượng cho các topping đã chọn'
                });
            });
        }

        // Xử lý lưu thay đổi topping
        if (saveToppingStocksBtn) {
            saveToppingStocksBtn.addEventListener('click', () => {
                // Check for zero quantities first
                const zeroItems = checkZeroQuantities();
                if (zeroItems && zeroItems.length > 0) {
                    if (!confirm(`Cảnh báo: Có ${zeroItems.length} topping có số lượng 0. Bạn có muốn tiếp tục lưu không?`)) {
                        return;
                    }
                }

                const data = {
                    topping_stock: {}
                };

                // Collect branch selections and topping stock data
                document.querySelectorAll('#toppings-tab .branch-card').forEach(card => {
                    const branchCheckbox = card.querySelector('input[name="branch_selection_toppings[]"]');
                    if (!branchCheckbox) return;
                    
                    const branchId = branchCheckbox.value;
                    
                    // Initialize branch topping stocks
                    data.topping_stock[branchId] = {};
                    
                    // Collect stock values for each topping in this branch
                    card.querySelectorAll('.topping-stocks .variant-stock').forEach(toppingStock => {
                        const toppingId = toppingStock.getAttribute('data-topping-id');
                        const stockInput = toppingStock.querySelector('input[type="number"]');
                        const stockValue = parseInt(stockInput.value) || 0;
                        
                        // Ensure toppingId exists and is a valid number
                        if (toppingId && !isNaN(parseInt(toppingId))) {
                            data.topping_stock[branchId][toppingId] = stockValue;
                        } else {
                            console.error('Invalid topping ID:', toppingId, 'for branch:', branchId);
                        }
                    });
                    
                    // If no toppings were found for this branch, log an error
                    if (Object.keys(data.topping_stock[branchId]).length === 0) {
                        console.warn('No toppings found for branch ID:', branchId);
                    }
                });
                
                // Debug log the data being sent
                console.log('Topping stock data being sent:', data);

                // Send to server
                fetch('{{ route("admin.products.update-topping-stocks") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(data)
                })
                .then(response => {
                    console.log('Topping stock response status:', response.status);
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Topping stock response data:', data);
                    if (data.success) {
                        dtmodalShowToast('success', {
                            title: 'Thành công!',
                            message: 'Cập nhật số lượng topping thành công'
                        });
                        
                        // Reload the page to refresh the stock data
                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);
                    } else {
                        dtmodalShowToast('error', {
                            title: 'Lỗi!',
                            message: data.message || 'Có lỗi xảy ra khi cập nhật'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error updating topping stocks:', error);
                    dtmodalShowToast('error', {
                        title: 'Lỗi!',
                        message: 'Có lỗi xảy ra khi cập nhật: ' + error.message
                    });
                });
            });
        }

        // Add keyboard shortcuts
        document.addEventListener('keydown', (e) => {
            // Ctrl/Cmd + S to save
            if ((e.ctrlKey || e.metaKey) && e.key === 's') {
                e.preventDefault();
                const activeTab = document.querySelector('.tab.active').dataset.tab;
                if (activeTab === 'variants') {
                    saveVariantStocksBtn.click();
                } else if (activeTab === 'toppings' && saveToppingStocksBtn) {
                    saveToppingStocksBtn.click();
                }
            }
        });
    });
</script>
@endsection 