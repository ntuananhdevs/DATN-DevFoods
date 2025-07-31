@extends('layouts.branch.contentLayoutMaster')

@section('title', 'Quản lý đơn hàng')

@section('vendor-style')
<link rel="stylesheet" href="{{ asset('vendors/css/pickers/flatpickr/flatpickr.min.css') }}">
@endsection

@section('page-style')
<style>
.order-card {
    transition: all 0.3s ease;
}
.order-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
}

.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    padding: 0.25rem 0.75rem;
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 600;
}
.bulk-actions-bar {
    position: fixed;
    bottom: 1rem;
    left: 50%;
    transform: translateX(-50%);
    z-index: 50;
    background: #3b82f6;
    color: white;
    padding: 1rem;
    border-radius: 0.5rem;
    box-shadow: 0 10px 25px rgba(0,0,0,0.2);
    display: none;
}
.tooltip {
    position: relative;
    cursor: help;
}
.tooltip .tooltip-content {
    visibility: hidden;
    opacity: 0;
    position: absolute;
    bottom: 125%;
    left: 50%;
    transform: translateX(-50%);
    background: #1f2937;
    color: white;
    padding: 0.5rem;
    border-radius: 0.25rem;
    font-size: 0.75rem;
    white-space: nowrap;
    transition: all 0.3s;
    z-index: 1000;
    min-width: 200px;
}
.tooltip:hover .tooltip-content {
    visibility: visible;
    opacity: 1;
}
.status-tab.active {
    border-bottom-color: #3b82f6 !important;
    color: #3b82f6 !important;
}
.status-tab {
    border-bottom-color: transparent;
    color: #6b7280;
    cursor: pointer;
}
.status-tab:hover {
    color: #3b82f6;
}

/* Đảm bảo modal hiển thị đúng */
#cancel-order-modal {
    z-index: 9999;
}
</style>
@endsection

@section('content')
<div class="mx-auto p-4">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold mb-2 text-gray-900">Quản lý đơn hàng</h1>
        </div>
        <div class="flex items-center gap-2 mt-4 md:mt-0">
            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5-5-5h5v-12"></path>
            </svg>
            <span class="text-sm text-gray-500">Thông báo tự động</span>
        </div>
    </div>

    <!-- Search and Filter -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
        <div class="p-4">
            <form id="filterForm" method="GET" action="{{ route('branch.orders.index') }}">
                <div class="flex flex-col gap-4">
                    <div class="flex flex-col md:flex-row gap-4">
                        <div class="flex-1">
                            <div class="relative">
                                <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                <input
                                    type="text"
                                    name="search"
                                    value="{{ request('search') }}"
                                    placeholder="Tìm theo mã đơn, tên khách hàng hoặc số điện thoại..."
                                    class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                />
                            </div>
                        </div>
                        <div class="flex gap-2 flex-wrap">
                            <button type="button" id="dateRangeBtn" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                Thời gian
                            </button>

                            <select name="payment_method" class="px-3 py-2 border border-gray-300 rounded-md text-sm">
                                <option value="all">Tất cả thanh toán</option>
                                @foreach($paymentMethods as $method)
                                    <option value="{{ $method['key'] }}" {{ request('payment_method') == $method['key'] ? 'selected' : '' }}>
                                        {{ $method['label'] }}
                                    </option>
                                @endforeach
                            </select>

                            <select name="sort" class="px-3 py-2 border border-gray-300 rounded-md text-sm">
                                <option value="order_date-desc" {{ request('sort') == 'order_date-desc' ? 'selected' : '' }}>Mới nhất</option>
                                <option value="order_date-asc" {{ request('sort') == 'order_date-asc' ? 'selected' : '' }}>Cũ nhất</option>
                                <option value="total_amount-desc" {{ request('sort') == 'total_amount-desc' ? 'selected' : '' }}>Giá cao</option>
                                <option value="total_amount-asc" {{ request('sort') == 'total_amount-asc' ? 'selected' : '' }}>Giá thấp</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex justify-between items-center">
                        <div class="flex gap-2">
                            <button type="submit" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                                Lọc
                            </button>
                            <a href="{{ route('branch.orders.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                                Làm mới
                            </a>
                            <button type="button" id="exportBtn" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Xuất báo cáo
                            </button>
                        </div>

                        @if(request('search') || request('status') || request('date_from') || request('date_to') || request('payment_method'))
                            <a href="{{ route('branch.orders.index') }}" class="text-gray-500 hover:text-gray-700 text-sm">
                                Xóa bộ lọc
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Status Tabs (đặt ngoài form) -->
    <div class="mb-6">
        <div class="border-b border-gray-200">
            <nav id="orderStatusTabs" class="-mb-px flex space-x-8 overflow-x-auto">
                <a href="{{ route('branch.orders.index') }}" class="status-tab whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm {{ !request('status') || request('status') == 'all' ? 'active border-blue-500 text-blue-600' : 'border-transparent text-gray-500' }}" data-status="all">
                    Tất cả ({{ $statusCounts['all'] ?? 0 }})
                </a>
                <a href="{{ route('branch.orders.index', ['status' => 'awaiting_confirmation']) }}" class="status-tab whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm {{ request('status') == 'awaiting_confirmation' ? 'active border-blue-500 text-blue-600' : 'border-transparent text-gray-500' }}" data-status="awaiting_confirmation">
                    Chờ xác nhận ({{ $statusCounts['awaiting_confirmation'] ?? 0 }})
                </a>
                <a href="{{ route('branch.orders.index', ['status' => 'awaiting_driver']) }}" class="status-tab whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm {{ request('status') == 'awaiting_driver' ? 'active border-blue-500 text-blue-600' : 'border-transparent text-gray-500' }} tooltip" data-status="awaiting_driver">
                    Chờ tài xế ({{ $statusCounts['awaiting_driver'] ?? 0 }})
                </a>
                <a href="{{ route('branch.orders.index', ['status' => 'in_transit']) }}" class="status-tab whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm {{ request('status') == 'in_transit' ? 'active border-blue-500 text-blue-600' : 'border-transparent text-gray-500' }}" data-status="in_transit">
                    Đang giao ({{ $statusCounts['in_transit'] ?? 0 }})
                </a>
                <a href="{{ route('branch.orders.index', ['status' => 'delivered']) }}" class="status-tab whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm {{ request('status') == 'delivered' ? 'active border-blue-500 text-blue-600' : 'border-transparent text-gray-500' }}" data-status="delivered">
                    Đã giao ({{ $statusCounts['delivered'] ?? 0 }})
                </a>
                <a href="{{ route('branch.orders.index', ['status' => 'cancelled']) }}" class="status-tab whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm {{ request('status') == 'cancelled' ? 'active border-blue-500 text-blue-600' : 'border-transparent text-gray-500' }}" data-status="cancelled">
                    Đã hủy ({{ $statusCounts['cancelled'] ?? 0 }})
                </a>
                <a href="{{ route('branch.orders.index', ['status' => 'refunded']) }}" class="status-tab whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm {{ request('status') == 'refunded' ? 'active border-blue-500 text-blue-600' : 'border-transparent text-gray-500' }}" data-status="refunded">
                    Đã hoàn tiền ({{ $statusCounts['refunded'] ?? 0 }})
                </a>
            </nav>
        </div>
    </div>

    <!-- Orders Grid + Pagination -->
    @include('branch.orders.partials.orders_grid')

    <!-- Bulk Actions Bar -->

</div>

<!-- Modal xác nhận hủy đơn hàng -->
<div id="cancel-order-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-9999">
    <div class="relative mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                <i class="fas fa-times text-red-600 text-xl"></i>
            </div>
            <h3 class="text-lg leading-6 font-medium text-gray-900 mt-4">Xác nhận hủy đơn hàng</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500">
                    Bạn có chắc chắn muốn hủy đơn hàng này không? Hành động này không thể hoàn tác.
                </p>
                
                <!-- Phần chọn lý do hủy đơn -->
                <div id="cancel-reason-section" class="mt-4 text-left">
                    <p class="text-sm font-medium text-gray-700 mb-2">Vui lòng cho biết lý do hủy đơn hàng này.</p>
                    <div class="space-y-2">
                        <div>
                            <input type="radio" id="reason-out-of-stock" name="cancel_reason" value="Hết hàng" class="mr-2">
                            <label for="reason-out-of-stock" class="text-sm text-gray-600">Hết hàng</label>
                        </div>
                        <div>
                            <input type="radio" id="reason-too-busy" name="cancel_reason" value="Quá tải đơn hàng" class="mr-2">
                            <label for="reason-too-busy" class="text-sm text-gray-600">Quá tải đơn hàng</label>
                        </div>
                        <div>
                            <input type="radio" id="reason-closing" name="cancel_reason" value="Cửa hàng sắp đóng cửa" class="mr-2">
                            <label for="reason-closing" class="text-sm text-gray-600">Cửa hàng sắp đóng cửa</label>
                        </div>
                        <div>
                            <input type="radio" id="reason-customer-request" name="cancel_reason" value="Theo yêu cầu của khách hàng" class="mr-2">
                            <label for="reason-customer-request" class="text-sm text-gray-600">Theo yêu cầu của khách hàng</label>
                        </div>
                        <div>
                            <input type="radio" id="reason-other" name="cancel_reason" value="Khác" class="mr-2">
                            <label for="reason-other" class="text-sm text-gray-600">Khác</label>
                        </div>
                        <div id="other-reason-container" class="hidden mt-2">
                            <textarea id="other-reason-text" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500" placeholder="Vui lòng nhập lý do khác..."></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="items-center px-4 py-3 flex gap-3">
                <button id="cancel-abort-btn" class="w-full px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300">
                    Không
                </button>
                <button id="cancel-confirm-btn" class="w-full px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                    Đồng ý hủy
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('vendor-script')
<script src="{{ asset('vendors/js/pickers/flatpickr/flatpickr.min.js') }}"></script>
@endsection

@section('page-script')
<script>
window.branchId = @json($branch->id);
window.pusherKey = @json(config('broadcasting.connections.pusher.key'));
window.pusherCluster = @json(config('broadcasting.connections.pusher.options.cluster'));
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const tabs = document.querySelectorAll('#orderStatusTabs .status-tab');
    const ordersGrid = document.getElementById('ordersGrid');
    const ordersPagination = document.getElementById('ordersPagination');

    // AJAX load orders by tab
    tabs.forEach(tab => {
        tab.addEventListener('click', function(e) {
            e.preventDefault();
            // Xóa active ở tất cả tab và reset về mặc định
            tabs.forEach(t => {
                t.classList.remove('active', 'border-blue-500', 'text-blue-600');
                t.classList.add('border-transparent', 'text-gray-500');
            });
            // Thêm active cho tab vừa click
            tab.classList.add('active', 'border-blue-500', 'text-blue-600');
            tab.classList.remove('border-transparent', 'text-gray-500');
            const url = tab.getAttribute('href');
            fetchOrdersByUrl(url, true);
        });
    });

    function fetchOrdersByUrl(url, updateHistory = false) {
        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(res => res.text())
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const newGrid = doc.getElementById('ordersGrid');
            const newPagination = doc.getElementById('ordersPagination');
            const newTabs = doc.querySelectorAll('#orderStatusTabs .status-tab');
            // Cập nhật grid và pagination
            if (newGrid) {
                ordersGrid.innerHTML = newGrid.innerHTML;
            }
            if (newPagination) {
                ordersPagination.innerHTML = newPagination.innerHTML;
            }
            // Cập nhật số đếm trên tất cả tab
            const currentTabs = document.querySelectorAll('#orderStatusTabs .status-tab');
            currentTabs.forEach((tab, index) => {
                if (newTabs[index]) {
                    tab.innerHTML = newTabs[index].innerHTML;
                }
            });
            if (updateHistory) {
                history.replaceState(null, '', url);
            }
        })
        .catch(err => {
            if (typeof dtmodalShowToast === 'function') {
                dtmodalShowToast('error', {
                    title: 'Lỗi',
                    message: 'Không thể tải đơn hàng!'
                });
            }
            console.error('AJAX error:', err);
        });
    }
});
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toast thông báo thành công hoặc lỗi
    function showToast(message, color = "bg-green-600") {
        const toast = document.createElement('div');
        toast.className = `fixed top-20 right-4 ${color} text-white px-4 py-2 rounded-lg shadow-lg z-50 transition-opacity duration-300 opacity-0`;
        toast.textContent = message;
        document.body.appendChild(toast);
        setTimeout(() => {
            toast.classList.remove('opacity-0');
            toast.classList.add('opacity-100');
        }, 10);
        setTimeout(() => {
            toast.classList.remove('opacity-100');
            toast.classList.add('opacity-0');
            setTimeout(() => {
                document.body.removeChild(toast);
            }, 300);
        }, 3000);
    }

    // Xử lý modal hủy đơn hàng
    const cancelModal = document.getElementById('cancel-order-modal');
    const cancelConfirmBtn = document.getElementById('cancel-confirm-btn');
    const cancelAbortBtn = document.getElementById('cancel-abort-btn');
    const otherReasonContainer = document.getElementById('other-reason-container');
    const otherReasonText = document.getElementById('other-reason-text');
    let currentOrderId = null;

    // Xử lý hiển thị textarea khi chọn lý do "Khác"
    document.querySelectorAll('input[name="cancel_reason"]').forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.value === 'Khác') {
                otherReasonContainer.classList.remove('hidden');
            } else {
                otherReasonContainer.classList.add('hidden');
            }
        });
    });

    // Mở modal khi bấm nút hủy đơn
    document.addEventListener('click', function(e) {
        if (e.target && e.target.dataset.quickAction === 'cancel') {
            e.preventDefault();
            currentOrderId = e.target.dataset.orderId;
            cancelModal.classList.remove('hidden');
        }
    });

    // Đóng modal khi bấm nút không
    cancelAbortBtn.addEventListener('click', function() {
        closeModal();
    });

    // Xử lý khi bấm nút xác nhận hủy
    cancelConfirmBtn.addEventListener('click', function() {
        if (!currentOrderId) {
            closeModal();
            return;
        }

        const selectedReason = document.querySelector('input[name="cancel_reason"]:checked');
        if (!selectedReason) {
            showToast('Vui lòng chọn lý do hủy đơn hàng', "bg-red-600");
            return;
        }

        let reason = selectedReason.value;
        if (reason === 'Khác') {
            const otherReasonValue = otherReasonText.value.trim();
            if (!otherReasonValue) {
                showToast('Vui lòng nhập lý do hủy đơn hàng', "bg-red-600");
                return;
            }
            reason = otherReasonValue;
        }

        // Gửi request hủy đơn hàng
        const formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('reason', reason);

        fetch(`{{ route('branch.orders.cancel', ['id' => '__id__']) }}`.replace('__id__', currentOrderId), {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            closeModal();
            if (data.success) {
                showToast('Hủy đơn hàng thành công!');
                setTimeout(() => {
                    window.location.reload();
                }, 1300);
            } else {
                showToast(data.message || 'Có lỗi xảy ra!', "bg-red-600");
            }
        })
        .catch(() => {
            closeModal();
            showToast('Có lỗi khi kết nối!', "bg-red-600");
        });
    });

    function closeModal() {
        cancelModal.classList.add('hidden');
        currentOrderId = null;
        // Reset radio buttons
        document.querySelectorAll('input[name="cancel_reason"]').forEach(radio => {
            radio.checked = false;
        });
        otherReasonContainer.classList.add('hidden');
        otherReasonText.value = '';
    }
});
</script>

<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
<script src="{{ asset('js/branch/orders-realtime-simple.js') }}" defer></script>
<script src="{{ asset('js/branch/order-card-realtime.js') }}" defer></script>
<script src="{{ asset('js/modal.js') }}" defer></script>
@endsection