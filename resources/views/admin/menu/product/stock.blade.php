@extends('layouts/admin/contentLayoutMaster')

@section('title', 'Quản lý tồn kho')

@include('components.modal')

@section('content')

@section('style-prd-stock')
    <link rel="stylesheet" href="{{ asset('css/admin/stock.css') }}">
@endsection

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
            <a href="{{ route('admin.products.edit', $product) }}"
                class="btn btn-secondary inline-flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path
                        d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                </svg>
                Chỉnh sửa sản phẩm
            </a>
        </div>
    </div>

    <!-- Header Section -->
    <div class="mb-6">
        <h2 class="text-xl font-semibold text-gray-900">Tồn kho biến thể sản phẩm</h2>
        <p class="text-gray-600 mt-1">Quản lý số lượng tồn kho cho các biến thể sản phẩm tại từng chi nhánh</p>
    </div>

    <!-- Variants Content -->
        <!-- Bulk Stock Section -->
        <div class="bulk-stock-section">
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Nhập số lượng tổng</h3>
                <div class="flex gap-4">
                    <div class="flex-1">
                        <input type="number" id="bulk-stock-input" min="0" class="stock-input"
                            placeholder="Nhập số lượng tổng" />
                    </div>
                    <button type="button" id="apply-bulk-stock"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        Áp dụng
                    </button>
                </div>
            </div>

            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Chọn biến thể áp dụng</h3>
                <div class="variant-list">
                    @foreach ($product->variants as $variant)
                        <div class="variant-item" data-variant-id="{{ $variant->id }}">
                            <input type="checkbox" name="selected_variants[]" value="{{ $variant->id }}"
                                class="form-checkbox text-blue-600 rounded" />
                            <span class="text-sm">
                                @foreach ($variant->productVariantDetails as $detail)
                                    @if (isset($detail->variantValue) && isset($detail->variantValue->attribute))
                                        {{ $detail->variantValue->attribute->name }}:
                                        {{ $detail->variantValue->value }}
                                        @if (!$loop->last)
                                            ,
                                        @endif
                                    @endif
                                @endforeach
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <form id="variant-stock-form" action="{{ route('admin.products.update-branch-stocks', $product->id) }}" method="POST">
            @csrf
            @method('POST')
            <input type="hidden" name="product_id" value="{{ $product->id }}">

            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-xl font-semibold text-gray-900">Chi nhánh áp dụng</h3>
                        <button type="button" id="select-all-branches-variants"
                            class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                            Chọn tất cả
                        </button>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach ($branches as $branch)
                            <div class="branch-card p-6">
                                <div class="flex justify-between items-start mb-4">
                                    <div>
                                        <h4 class="text-lg font-semibold text-gray-900">{{ $branch->name }}</h4>
                                        <p class="text-sm text-gray-500 mt-1">{{ $branch->address }}</p>
                                    </div>
                                    <span
                                        class="status-badge {{ $branch->active ? 'status-active' : 'status-inactive' }}">
                                        {{ $branch->active ? 'Đang hoạt động' : 'Ngừng hoạt động' }}
                                    </span>
                                </div>

                                <div class="flex items-center gap-2 mb-4">
                                    <label class="inline-flex items-center gap-2 cursor-pointer">
                                        <input type="checkbox" name="branch_selection_variants[]"
                                            value="{{ $branch->id }}" class="form-checkbox text-blue-600 rounded" />
                                        <span class="text-sm text-gray-700">Chọn chi nhánh này</span>
                                    </label>
                                </div>

                                <div class="variant-stocks space-y-3">
                                    @foreach ($product->variants as $variant)
                                        <div class="variant-stock" data-variant-id="{{ $variant->id }}">
                                            <div class="text-sm font-medium text-gray-700 mb-1">
                                                @foreach ($variant->productVariantDetails as $detail)
                                                    @if (isset($detail->variantValue) && isset($detail->variantValue->attribute))
                                                        {{ $detail->variantValue->attribute->name }}:
                                                        {{ $detail->variantValue->value }}
                                                        @if (!$loop->last)
                                                            ,
                                                        @endif
                                                    @endif
                                                @endforeach
                                            </div>
                                            <input type="number"
                                                name="stocks[{{ $branch->id }}][{{ $variant->id }}]"
                                                value="{{ isset($branchStocks[$branch->id][$variant->id]) ? $branchStocks[$branch->id][$variant->id] : 0 }}"
                                                min="0" class="stock-input" placeholder="Nhập số lượng" />
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="flex justify-end mt-8">
                        <button type="submit" id="save-variant-stocks"
                            class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            Lưu thay đổi
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</main>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Check for zero quantity items
        function checkZeroQuantityItems() {
            const stockInputs = document.querySelectorAll('.stock-input');
            let hasZeroQuantity = false;

            stockInputs.forEach(input => {
                if (parseInt(input.value) === 0 || input.value === '') {
                    hasZeroQuantity = true;
                }
            });

            if (hasZeroQuantity) {
                console.log('Warning: Some items have zero quantity');
            }
        }

        // Tab functionality
        const tabButtons = document.querySelectorAll('.tab');
        const tabContents = document.querySelectorAll('.tab-content');

        // Check if there's an active tab from session
        @if(session('active_tab'))
            const activeTab = '{{ session('active_tab') }}';
            if (activeTab === 'toppings') {
                // Remove active class from all tabs and contents
                tabButtons.forEach(btn => btn.classList.remove('active'));
                tabContents.forEach(content => content.classList.remove('active'));
                
                // Activate toppings tab
                const toppingsTabButton = document.querySelector('.tab[data-tab="toppings"]');
                const toppingsTabContent = document.getElementById('toppings-tab');
                
                if (toppingsTabButton && toppingsTabContent) {
                    toppingsTabButton.classList.add('active');
                    toppingsTabContent.classList.add('active');
                }
            }
        @endif

        tabButtons.forEach(button => {
            button.addEventListener('click', function() {
                const targetTab = this.getAttribute('data-tab') + '-tab';

                // Remove active class from all tabs and contents
                tabButtons.forEach(btn => btn.classList.remove('active'));
                tabContents.forEach(content => content.classList.remove('active'));

                // Add active class to clicked tab and corresponding content
                this.classList.add('active');
                document.getElementById(targetTab).classList.add('active');
            });
        });

        // Form submission confirmation for variant stocks
        const variantForm = document.getElementById('variant-stock-form');
        if (variantForm) {
            variantForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const form = this;
                dtmodalShowModal('warning', {
                    title: 'Xác nhận lưu',
                    message: 'Bạn có chắc chắn muốn lưu thay đổi kho hàng cho biến thể?',
                    confirmText: 'Lưu',
                    cancelText: 'Hủy',
                    createIfNotExists: true,
                    onConfirm: function() {
                        form.submit();
                    }
                });
            });
        }

        // Form submission confirmation for topping stocks
        const toppingForm = document.getElementById('topping-stock-form');
        if (toppingForm) {
            toppingForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const form = this;
                dtmodalShowModal('warning', {
                    title: 'Xác nhận lưu',
                    message: 'Bạn có chắc chắn muốn lưu thay đổi kho hàng cho topping?',
                    confirmText: 'Lưu',
                    cancelText: 'Hủy',
                    createIfNotExists: true,
                    onConfirm: function() {
                        form.submit();
                    }
                });
            });
        }

        // Select all branches functionality for variants
        const selectAllVariantsBtn = document.getElementById('select-all-branches-variants');
        if (selectAllVariantsBtn) {
            selectAllVariantsBtn.addEventListener('click', function() {
                const checkboxes = document.querySelectorAll(
                    'input[name="branch_selection_variants[]"]');
                const allChecked = Array.from(checkboxes).every(cb => cb.checked);

                checkboxes.forEach(checkbox => {
                    checkbox.checked = !allChecked;
                });

                this.textContent = allChecked ? 'Chọn tất cả' : 'Bỏ chọn tất cả';
            });
        }

        // Select all branches functionality for toppings
        const selectAllToppingsBtn = document.getElementById('select-all-branches-toppings');
        if (selectAllToppingsBtn) {
            selectAllToppingsBtn.addEventListener('click', function() {
                const checkboxes = document.querySelectorAll(
                    'input[name="branch_selection_toppings[]"]');
                const allChecked = Array.from(checkboxes).every(cb => cb.checked);

                checkboxes.forEach(checkbox => {
                    checkbox.checked = !allChecked;
                });

                this.textContent = allChecked ? 'Chọn tất cả' : 'Bỏ chọn tất cả';
            });
        }

        // Item selection for variants
        const variantCheckboxes = document.querySelectorAll('input[name="selected_variants[]"]');
        variantCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const variantId = this.value;
                const isChecked = this.checked;

                // Update visual feedback or perform actions based on selection
                console.log(`Variant ${variantId} ${isChecked ? 'selected' : 'deselected'}`);
            });
        });



        // Apply bulk stock for selected variants and branches
        const applyBulkStockBtn = document.getElementById('apply-bulk-stock');
        if (applyBulkStockBtn) {
            applyBulkStockBtn.addEventListener('click', function() {
                const bulkValue = document.getElementById('bulk-stock-input').value;
                const selectedVariants = document.querySelectorAll(
                    'input[name="selected_variants[]"]:checked');
                const selectedBranches = document.querySelectorAll(
                    'input[name="branch_selection_variants[]"]:checked');

                if (!bulkValue || selectedVariants.length === 0 || selectedBranches.length === 0) {
                    dtmodalShowToast('warning', {
                        title: 'Thiếu thông tin',
                        message: 'Vui lòng nhập số lượng và chọn ít nhất một biến thể và một chi nhánh'
                    });
                    return;
                }

                selectedBranches.forEach(branchCheckbox => {
                    const branchId = branchCheckbox.value;
                    selectedVariants.forEach(variantCheckbox => {
                        const variantId = variantCheckbox.value;
                        const stockInput = document.querySelector(
                            `input[name="stocks[${branchId}][${variantId}]"]`);
                        if (stockInput) {
                            stockInput.value = bulkValue;
                        }
                    });
                });

                dtmodalShowToast('success', {
                    title: 'Thành công',
                    message: 'Đã áp dụng số lượng tổng cho các biến thể và chi nhánh đã chọn'
                });
            });
        }



        // Add form interactions
        const stockInputs = document.querySelectorAll('.stock-input');
        stockInputs.forEach(input => {
            input.addEventListener('change', checkZeroQuantityItems);
        });

        // Add click handlers for variant items
        const variantItems = document.querySelectorAll('.variant-item');
        variantItems.forEach(item => {
            item.addEventListener('click', function(e) {
                // Don't trigger if the click is directly on the checkbox
                if (e.target.type === 'checkbox') {
                    return;
                }
                
                const checkbox = item.querySelector('input[type="checkbox"]');
                if (checkbox) {
                    checkbox.checked = !checkbox.checked;
                }
            });
            
            // Add cursor pointer style
            item.style.cursor = 'pointer';
        });

        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            // Ctrl/Cmd + S to save variant form
            if ((e.ctrlKey || e.metaKey) && e.key === 's') {
                e.preventDefault();

                const variantForm = document.getElementById('variant-stock-form');
                if (variantForm) {
                    dtmodalShowModal('warning', {
                        title: 'Xác nhận lưu',
                        message: 'Bạn có chắc chắn muốn lưu thay đổi kho hàng cho biến thể?',
                        confirmText: 'Lưu',
                        cancelText: 'Hủy',
                        createIfNotExists: true,
                        onConfirm: function() {
                            variantForm.submit();
                        }
                    });
                }
            }
        });
    });
</script>
@endsection
