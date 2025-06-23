@extends('layouts.admin.contentLayoutMaster')

@section('title', 'Quản lý tồn kho - ' . $topping->name)

@section('content')
    <style>
        .status-tag {
        display: inline-flex;
        align-items: center;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 500;
        line-height: 1.25rem;
        transition: all 0.2s ease;
    }

    .status-tag.success {
        background-color: #dcfce7;
        color: #15803d;
    }

    .status-tag.failed {
        background-color: #fee2e2;
        color: #b91c1c;
    }

    </style>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl font-bold text-gray-900">Quản lý tồn kho</h1>
                <nav class="flex mt-2" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 md:space-x-3">
                        <li class="inline-flex items-center">
                            <a href="{{ route('admin.dashboard') }}"
                                class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600">
                                Dashboard
                            </a>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <svg class="w-3 h-3 text-gray-400 mx-1" aria-hidden="true"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2" d="m1 9 4-4-4-4" />
                                </svg>
                                <a href="{{ route('admin.toppings.index') }}"
                                    class="ml-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ml-2">Toppings</a>
                            </div>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <svg class="w-3 h-3 text-gray-400 mx-1" aria-hidden="true"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2" d="m1 9 4-4-4-4" />
                                </svg>
                                <a href="{{ route('admin.toppings.show', $topping->id) }}"
                                    class="ml-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ml-2">{{ $topping->name }}</a>
                            </div>
                        </li>
                        <li aria-current="page">
                            <div class="flex items-center">
                                <svg class="w-3 h-3 text-gray-400 mx-1" aria-hidden="true"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2" d="m1 9 4-4-4-4" />
                                </svg>
                                <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Tồn kho</span>
                            </div>
                        </li>
                    </ol>
                </nav>
            </div>
            <div>
                <a href="{{ route('admin.toppings.show', $topping->id) }}"
                    class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Quay lại
                </a>
            </div>
        </div>

        <!-- Topping Info Card -->
        <div class="bg-white shadow-lg rounded-lg mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-blue-600">Thông tin Topping</h3>
            </div>
            <div class="p-6">
                <div class="flex flex-col md:flex-row gap-6">
                    <div class="flex-shrink-0">
                        @if ($topping->image)
                            <img src="{{ Storage::disk('s3')->url($topping->image) }}" alt="{{ $topping->name }}"
                                class="w-24 h-24 object-cover rounded-lg">
                        @else
                            <div class="w-24 h-24 bg-gray-100 rounded-lg flex items-center justify-content-center">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                    </path>
                                </svg>
                            </div>
                        @endif
                    </div>
                    <div class="flex-1">
                        <h4 class="text-xl font-bold text-gray-900 mb-2">{{ $topping->name }}</h4>
                        <p class="text-gray-600 mb-4">{{ $topping->description }}</p>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <span class="text-sm font-medium text-gray-700">Giá:</span>
                                <span
                                    class="ml-2 text-lg font-semibold text-green-600">{{ number_format($topping->price) }}đ</span>
                            </div>
                            <div>
                                <span class="text-sm font-medium text-gray-700">Trạng thái:</span>
                                <span
                                    @if ($topping->active == 1)
                                        <span class="status-tag success">Hoạt động</span>
                                    @else
                                        <span class="status-tag failed">Không hoạt động</span>
                                    @endif
                                </span>
                            </div>
                            <div>
                                <span class="text-sm font-medium text-gray-700">Tổng tồn kho:</span>
                                <span
                                    class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $topping->toppingStocks->sum('stock_quantity') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stock Management Form -->
        <div class="bg-white shadow-lg rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-blue-600">Cập nhật tồn kho theo chi nhánh</h3>
            </div>
            <div class="p-6">
                <form action="{{ route('admin.toppings.update-stock', $topping->id) }}" method="POST">
                    @csrf
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Chi nhánh</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Địa chỉ</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Tồn kho hiện tại</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Tồn kho mới</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Thay đổi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($branches as $branch)
                                    @php
                                        $currentStock = $topping->toppingStocks
                                            ->where('branch_id', $branch->id)
                                            ->first();
                                        $currentQuantity = $currentStock ? $currentStock->stock_quantity : 0;
                                    @endphp
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $branch->name }}</div>
                                            <div class="text-sm text-gray-500">{{ $branch->phone }}</div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm text-gray-900">{{ $branch->address }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $currentQuantity > 0 ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                                {{ $currentQuantity }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <input type="hidden" name="stocks[{{ $loop->index }}][branch_id]"
                                                value="{{ $branch->id }}">
                                            <input type="number" name="stocks[{{ $loop->index }}][quantity]"
                                                value="{{ $currentQuantity }}" min="0"
                                                class="w-24 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 stock-input"
                                                data-current="{{ $currentQuantity }}">
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="change-indicator text-sm font-medium"
                                                data-index="{{ $loop->index }}">-</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mt-6 gap-4">
                        <div class="flex-1">
                            <div class="bg-blue-50 border border-blue-200 rounded-md p-4">
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-blue-400 mt-0.5 mr-3 flex-shrink-0" fill="currentColor"
                                        viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                    <div>
                                        <h4 class="text-sm font-medium text-blue-800">Lưu ý:</h4>
                                        <p class="text-sm text-blue-700 mt-1">Việc cập nhật tồn kho sẽ ảnh hưởng đến khả
                                            năng bán hàng tại các chi nhánh.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="flex gap-3">
                            <button type="button"
                                class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                                onclick="resetForm()">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                                    </path>
                                </svg>
                                Đặt lại
                            </button>
                            <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4">
                                    </path>
                                </svg>
                                Cập nhật tồn kho
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Calculate change when stock input changes
                const stockInputs = document.querySelectorAll('.stock-input');
                stockInputs.forEach(function(input) {
                    input.addEventListener('input', function() {
                        const currentValue = parseInt(this.dataset.current);
                        const newValue = parseInt(this.value) || 0;
                        const change = newValue - currentValue;

                        const indicator = this.closest('tr').querySelector('.change-indicator');

                        if (change > 0) {
                            indicator.innerHTML =
                                `<span class="text-green-600 font-semibold">+${change}</span>`;
                        } else if (change < 0) {
                            indicator.innerHTML =
                                `<span class="text-red-600 font-semibold">${change}</span>`;
                        } else {
                            indicator.innerHTML = '-';
                        }
                    });
                });

                // Reset form function
                window.resetForm = function() {
                    stockInputs.forEach(function(input) {
                        const currentValue = input.dataset.current;
                        input.value = currentValue;
                    });
                    document.querySelectorAll('.change-indicator').forEach(function(indicator) {
                        indicator.innerHTML = '-';
                    });
                };

                // Form validation
                const form = document.querySelector('form');
                form.addEventListener('submit', function(e) {
                    let hasChanges = false;
                    stockInputs.forEach(function(input) {
                        const currentValue = parseInt(input.dataset.current);
                        const newValue = parseInt(input.value) || 0;
                        if (currentValue !== newValue) {
                            hasChanges = true;
                        }
                    });

                    if (!hasChanges) {
                        e.preventDefault();
                        Swal.fire({
                            icon: 'info',
                            title: 'Không có thay đổi',
                            text: 'Bạn chưa thay đổi số lượng tồn kho nào.',
                            confirmButtonText: 'OK'
                        });
                        return false;
                    }

                    // Show confirmation
                    e.preventDefault();
                    Swal.fire({
                        title: 'Xác nhận cập nhật',
                        text: 'Bạn có chắc chắn muốn cập nhật tồn kho cho topping này?',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Cập nhật',
                        cancelButtonText: 'Hủy'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });
        </script>
    @endpush
@endsection
