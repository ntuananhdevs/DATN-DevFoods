@extends('layouts.customer.fullLayoutMaster')

@section('title', 'Lịch sử đơn hàng')

@section('content')
<style>
    .container {
        max-width: 1280px;
        margin: 0 auto;
    }
</style>
<div class="bg-gradient-to-r from-orange-500 to-red-500 py-8">
    <div class="container mx-auto px-4">
        <div class="flex items-center">
            <a href="{{ route('customer.profile') }}" class="text-white hover:text-white/80 mr-2">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h1 class="text-2xl md:text-3xl font-bold text-white">Lịch sử đơn hàng</h1>
        </div>
    </div>
</div>
<div class="container mx-auto px-4 py-8">
    <div class="flex flex-col gap-8">
        <section class="mb-10">
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="p-6 border-b border-gray-100 flex items-center justify-between">
                    <h2 class="text-xl font-bold">Đơn hàng của bạn</h2>
                </div>
                <div class="p-6">
                    <div id="order-list-container">
                        @include('customer.orders.partials.list', ['orders' => $orders])
                    </div>
                    <div class="pagination-container mt-6 flex justify-center">
                        @if ($orders->hasPages())
                            <nav class="inline-flex rounded-md shadow-sm" aria-label="Pagination">
                                {{-- Previous Page Link --}}
                                @if ($orders->onFirstPage())
                                    <span class="pagination-item disabled px-3 py-2 bg-gray-100 text-gray-400 rounded-l-md">
                                        <i class="fas fa-chevron-left"></i>
                                    </span>
                                @else
                                    <a href="{{ $orders->previousPageUrl() }}" class="pagination-item px-3 py-2 bg-white text-gray-700 hover:bg-orange-50 rounded-l-md border border-gray-200">
                                        <i class="fas fa-chevron-left"></i>
                                    </a>
                                @endif
                                {{-- Pagination Elements --}}
                                @foreach ($orders->getUrlRange(1, $orders->lastPage()) as $page => $url)
                                    @if ($page == $orders->currentPage())
                                        <span class="pagination-item active px-3 py-2 bg-orange-500 text-white font-bold">{{ $page }}</span>
                                    @else
                                        <a href="{{ $url }}" class="pagination-item px-3 py-2 bg-white text-gray-700 hover:bg-orange-50">{{ $page }}</a>
                                    @endif
                                @endforeach
                                {{-- Next Page Link --}}
                                @if ($orders->hasMorePages())
                                    <a href="{{ $orders->nextPageUrl() }}" class="pagination-item px-3 py-2 bg-white text-gray-700 hover:bg-orange-50 rounded-r-md border border-gray-200">
                                        <i class="fas fa-chevron-right"></i>
                                    </a>
                                @else
                                    <span class="pagination-item disabled px-3 py-2 bg-gray-100 text-gray-400 rounded-r-md">
                                        <i class="fas fa-chevron-right"></i>
                                    </span>
                                @endif
                            </nav>
                        @endif
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
<!-- Modal xác nhận hành động -->
<div id="action-confirmation-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
    <div class="relative mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div id="modal-icon-container" class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                <i id="modal-icon" class="fas fa-times text-red-600 text-xl"></i>
            </div>
            <h3 id="action-modal-title" class="text-lg leading-6 font-medium text-gray-900 mt-4">Xác nhận hành động</h3>
            <div class="mt-2 px-7 py-3">
                <p id="action-modal-message" class="text-sm text-gray-500">
                    Bạn có chắc chắn thực hiện thao tác này không?
                </p>
            </div>
            <div class="items-center px-4 py-3 flex gap-3">
                <button id="action-abort-btn" class="w-full px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300">
                    Không
                </button>
                <button id="action-confirm-btn" class="w-full px-4 py-2 bg-orange-600 text-white rounded-md hover:bg-orange-700">
                    Đồng ý
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Lấy danh sách order id từ blade
    var orderIds = @json($orders->pluck('id'));
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Toast thông báo thành công hoặc lỗi
        function showToast(message, color = "bg-green-600") {
            const toast = document.createElement('div');
            toast.className = `fixed bottom-4 right-4 ${color} text-white px-4 py-2 rounded-lg shadow-lg z-50 transition-opacity duration-300 opacity-0`;
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
        let formToSubmit = null;
        let modalAction = 'cancel';
        const modal = document.getElementById('action-confirmation-modal');
        const modalIcon = document.getElementById('modal-icon');
        const modalIconContainer = document.getElementById('modal-icon-container');
        const modalTitle = document.getElementById('action-modal-title');
        const modalMessage = document.getElementById('action-modal-message');
        const confirmBtn = document.getElementById('action-confirm-btn');
        const abortBtn = document.getElementById('action-abort-btn');
        function openActionModal(form, actionType) {
            formToSubmit = form;
            modalAction = actionType;
            if (actionType === 'cancel') {
                modalIcon.className = "fas fa-times text-red-600 text-xl";
                modalIconContainer.className = "mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100";
                modalTitle.textContent = "Xác nhận hủy đơn hàng";
                modalMessage.textContent = "Bạn có chắc chắn muốn hủy đơn hàng này không? Hành động này không thể hoàn tác.";
                confirmBtn.className = "w-full px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700";
                confirmBtn.textContent = "Đồng ý hủy";
            } else if (actionType === 'receive') {
                modalIcon.className = "fas fa-check text-green-600 text-xl";
                modalIconContainer.className = "mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100";
                modalTitle.textContent = "Xác nhận đã nhận hàng";
                modalMessage.textContent = "Bạn xác nhận đã nhận được hàng? Vui lòng kiểm tra kỹ trước khi xác nhận.";
                confirmBtn.className = "w-full px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700";
                confirmBtn.textContent = "Đã nhận";
            }
            modal.classList.remove('hidden');
        }
        function closeActionModal() {
            formToSubmit = null;
            modal.classList.add('hidden');
        }
        if (confirmBtn) {
            confirmBtn.addEventListener('click', function() {
                if (formToSubmit) {
                    const form = formToSubmit;
                    const action = form.getAttribute('action');
                    const methodInput = form.querySelector('input[name="_method"]');
                    const csrf = form.querySelector('input[name="_token"]').value;
                    const status = form.querySelector('input[name="status"]').value;
                    const method = methodInput ? methodInput.value : form.method;
                    const formData = new FormData();
                    formData.append('_token', csrf);
                    formData.append('status', status);
                    if (methodInput) formData.append('_method', method);
                    fetch(action, {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: formData
                    })
                    .then(res => res.json())
                    .then(data => {
                        closeActionModal();
                        if (data.success) {
                            showToast(
                                modalAction === 'cancel' ? 'Hủy đơn hàng thành công!' :
                                'Đã nhận hàng thành công!'
                            );
                            setTimeout(() => {
                                window.location.reload();
                            }, 1300);
                        } else {
                            showToast(data.message || 'Có lỗi xảy ra!', "bg-red-600");
                        }
                    })
                    .catch(() => {
                        closeActionModal();
                        showToast('Có lỗi khi kết nối!', "bg-red-600");
                    });
                } else {
                    closeActionModal();
                }
            });
        }
        if (abortBtn) {
            abortBtn.addEventListener('click', function() {
                closeActionModal();
            });
        }
        document.querySelectorAll('.cancel-order-form button[type="submit"]').forEach(button => {
            button.addEventListener('click', function(event) {
                event.preventDefault();
                const form = this.closest('form');
                openActionModal(form, 'cancel');
            });
        });
        document.querySelectorAll('.receive-order-form button[type="submit"]').forEach(button => {
            button.addEventListener('click', function(event) {
                event.preventDefault();
                const form = this.closest('form');
                openActionModal(form, 'receive');
            });
        });
    });
</script>
<script src="https://js.pusher.com/7.2/pusher.min.js"></script>
<script>
    Pusher.logToConsole = true;
    var pusher = new Pusher('2a1310e928036cd9f6d5', {
        cluster: 'ap1',
        encrypted: true
    });
    if (orderIds && Array.isArray(orderIds)) {
        orderIds.forEach(function(orderId) {
            var channel = pusher.subscribe('private-order.' + orderId);
            channel.bind('OrderStatusUpdated', function(data) {
                console.log('Pusher event OrderStatusUpdated received for order', orderId, data);
                showToast('🔄 Đơn hàng #' + orderId + ' vừa được cập nhật trạng thái!');
                fetch("{{ route('customer.orders.listPartial') }}")
                    .then(response => response.text())
                    .then(html => {
                        document.getElementById('order-list-container').innerHTML = html;
                    });
            });
        });
    }
</script>
@endpush
