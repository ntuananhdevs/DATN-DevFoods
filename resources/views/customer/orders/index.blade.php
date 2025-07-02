@extends('layouts.customer.fullLayoutMaster')

@section('title', 'Lịch sử đơn hàng')

@section('content')
@section('styles')
    <style>
        .pagination-container {
            display: flex;
            justify-content: center;
            gap: 0.5rem;
            padding-bottom: 1rem;
        }

        .pagination-item {
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 2.5rem;
            height: 2.5rem;
            padding: 0 0.75rem;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            font-weight: 500;
            transition: all 0.2s;
        }

        .pagination-item:not(.active):hover {
            background-color: #F3F4F6;
        }

        .pagination-item.active {
            background-color: #F97316;
            color: white;
        }

        .pagination-item.disabled {
            color: #9CA3AF;
            cursor: not-allowed;
        }
    </style>
@endsection
<div class="container mx-auto px-4 py-8">
    <div class="flex flex-col lg:flex-row gap-8">
        <div class="lg:w-1/4">
            <div class="bg-white rounded-xl shadow-sm overflow-hidden sticky top-24">
                <div class="p-6 border-b border-gray-100">
                    <h2 class="font-bold text-lg">Tài khoản của tôi</h2>
                </div>
                <nav class="p-2">
                    <ul class="space-y-1">
                        <li><a href="{{ route('customer.profile') }}"
                                class="flex items-center px-4 py-3 rounded-lg hover:bg-gray-50 transition-colors"><i
                                    class="fas fa-home mr-3 w-5 text-center"></i>Tổng quan</a></li>
                        <li><a href="{{ route('customer.orders.index') }}"
                                class="flex items-center px-4 py-3 rounded-lg bg-orange-50 text-orange-500 font-medium"><i
                                    class="fas fa-shopping-bag mr-3 w-5 text-center"></i>Đơn hàng của tôi</a></li>
                        <li><a href="#addresses"
                                class="flex items-center px-4 py-3 rounded-lg hover:bg-gray-50 transition-colors"><i
                                    class="fas fa-map-marker-alt mr-3 w-5 text-center"></i>Địa chỉ đã lưu</a></li>
                        <li><a href="#favorites"
                                class="flex items-center px-4 py-3 rounded-lg hover:bg-gray-50 transition-colors"><i
                                    class="fas fa-heart mr-3 w-5 text-center"></i>Món ăn yêu thích</a></li>
                        <li><a href="#rewards"
                                class="flex items-center px-4 py-3 rounded-lg hover:bg-gray-50 transition-colors"><i
                                    class="fas fa-gift mr-3 w-5 text-center"></i>Điểm thưởng & Ưu đãi</a></li>
                        <li><a href="{{ route('customer.profile.setting') }}"
                                class="flex items-center px-4 py-3 rounded-lg hover:bg-gray-50 transition-colors"><i
                                    class="fas fa-cog mr-3 w-5 text-center"></i>Cài đặt tài khoản</a></li>
                        <li class="border-t border-gray-100 mt-2 pt-2">
                            <form method="POST" action="{{ route('customer.logout') }}" id="logout-form">
                                @csrf
                                <a href="{{ route('customer.logout') }}"
                                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                                    class="flex items-center px-4 py-3 rounded-lg text-red-500 hover:bg-red-50 transition-colors">
                                    <i class="fas fa-sign-out-alt mr-3 w-5 text-center"></i>Đăng xuất
                                </a>
                            </form>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>

        <div class="lg:w-3/4">
            <div class="bg-gray-50 min-h-screen">
                <div class="max-w-4xl mx-auto px-4 py-8">
                    {{-- <div class="mb-6">
                        <a href="{{ route('customer.profile') }}"
                            class="inline-flex items-center text-orange-600 hover:text-orange-700 font-medium">
                            <i class="fas fa-arrow-left mr-2 h-4 w-4"></i>
                            Quay lại trang cá nhân
                        </a>
                    </div> --}}

                    <div class="bg-white rounded-xl shadow-md">
                        <div class="p-6 border-b">
                            <h1 class="text-xl font-bold">Lịch sử đơn hàng</h1>
                        </div>

                        <div class="p-6">
                            @if ($orders->count() > 0)
                                <div class="space-y-4">
                                    @foreach ($orders as $order)
                                        <div class="border rounded-lg p-4 transition-shadow hover:shadow-md">
                                            <div class="flex justify-between items-start mb-3">
                                                <div>
                                                    <h4 class="font-semibold text-gray-800">
                                                        #{{ $order->order_code ?? $order->id }}</h4>
                                                    <p class="text-sm text-gray-500">
                                                        {{ $order->order_date->format('H:i') }} -
                                                        {{ $order->order_date->isToday() ? 'Hôm nay' : $order->order_date->format('d/m/Y') }}
                                                    </p>
                                                </div>
                                                <span class="text-xs font-medium px-2.5 py-1 rounded-full capitalize"
                                                    style="background-color: {{ $order->status_color['bg'] ?? '#f3f4f6' }}; color: {{ $order->status_color['text'] ?? '#374151' }};">
                                                    {{ $order->status_text }}
                                                </span>
                                            </div>

                                            <div class="mb-4">
                                                <p class="text-sm text-gray-500 mb-1 font-medium">
                                                    {{ $order->branch->name ?? 'N/A' }}</p>
                                                <p class="text-sm text-gray-700">
                                                    {{ $order->orderItems->map(fn($item) => (optional(optional($item->productVariant)->product)->name ?? (optional($item->combo)->name ?? 'Sản phẩm')) . ' x' . $item->quantity)->implode(', ') }}
                                                </p>
                                            </div>

                                            <div class="flex justify-between items-center">
                                                <span
                                                    class="font-semibold text-lg text-orange-600">{{ number_format($order->total_amount, 0, ',', '.') }}đ</span>
                                                <div class="flex space-x-2">
                                                    <a href="{{ route('customer.orders.show', $order) }}"
                                                        class="inline-flex items-center justify-center rounded-md text-sm font-medium h-9 px-4 py-2 bg-gray-100 text-gray-800 hover:bg-gray-200">
                                                        Chi tiết
                                                    </a>
                                                    {{-- ====== NEW BUTTON LOGIC ====== --}}
                                                    @if ($order->status == 'awaiting_confirmation')
                                                        <form
                                                            action="{{ route('customer.orders.updateStatus', $order) }}"
                                                            method="POST" class="cancel-order-form">
                                                            @csrf
                                                            <input type="hidden" name="status" value="cancelled">
                                                            <button type="submit"
                                                                class="inline-flex items-center justify-center rounded-md text-sm font-medium h-9 px-4 py-2 border border-red-500 text-red-600 hover:bg-red-50">Hủy
                                                                đơn</button>
                                                        </form>

                                                        {{-- Case 2: Order has been delivered by the driver --}}
                                                    @elseif($order->status == 'delivered')
                                                        <a href="{{ route('customer.orders.updateStatus', $order) }}"
                                                            class="inline-flex items-center justify-center rounded-md text-sm font-medium h-10 px-4 py-2 bg-red-100 text-red-700 hover:bg-red-200">Chưa
                                                            nhận được hàng</a>
                                                        <form class="receive-order-form"
                                                            action="{{ route('customer.orders.updateStatus', $order) }}"
                                                            method="POST">
                                                            @csrf
                                                            <input type="hidden" name="status" value="item_received">
                                                            <button type="submit"
                                                                class="inline-flex items-center justify-center rounded-md text-sm font-medium text-white h-10 px-4 py-2 bg-orange-500 hover:bg-orange-600">Xác
                                                                nhận đã nhận hàng</button>
                                                        </form>

                                                        {{-- Case 3: Customer has confirmed they received the item --}}
                                                    @elseif($order->status == 'item_received')
                                                        <a href="#"
                                                            class="inline-flex items-center justify-center rounded-md text-sm font-medium text-white h-10 px-4 py-2 bg-yellow-500 hover:bg-yellow-600">
                                                            <i class="fas fa-star mr-2"></i>Đánh giá
                                                        </a>
                                                    @endif
                                                    {{-- ====== END NEW BUTTON LOGIC ====== --}}
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center text-gray-500 py-12">
                                    <p>Bạn chưa có đơn hàng nào.</p>
                                </div>
                            @endif
                        </div>

                        <div class="pagination-container">
                            @if ($orders->hasPages())
                                {{-- Previous Page Link --}}
                                @if ($orders->onFirstPage())
                                    <span class="pagination-item disabled">
                                        <i class="fas fa-chevron-left"></i>
                                    </span>
                                @else
                                    <a href="{{ $orders->previousPageUrl() }}" class="pagination-item">
                                        <i class="fas fa-chevron-left"></i>
                                    </a>
                                @endif

                                {{-- Pagination Elements --}}
                                @foreach ($orders->getUrlRange(1, $orders->lastPage()) as $page => $url)
                                    @if ($page == $orders->currentPage())
                                        <span class="pagination-item active">{{ $page }}</span>
                                    @else
                                        <a href="{{ $url }}" class="pagination-item">{{ $page }}</a>
                                    @endif
                                @endforeach

                                {{-- Next Page Link --}}
                                @if ($orders->hasMorePages())
                                    <a href="{{ $orders->nextPageUrl() }}" class="pagination-item">
                                        <i class="fas fa-chevron-right"></i>
                                    </a>
                                @else
                                    <span class="pagination-item disabled">
                                        <i class="fas fa-chevron-right"></i>
                                    </span>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
{{-- THÊM ĐOẠN HTML NÀY VÀO CUỐI FILE BLADE CỦA BẠN --}}
<div id="action-confirmation-modal"
    class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
    <div class="relative mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div id="modal-icon-container"
                class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                <i id="modal-icon" class="fas fa-times text-red-600 text-xl"></i>
            </div>
            <h3 id="action-modal-title" class="text-lg leading-6 font-medium text-gray-900 mt-4">Xác nhận hành
                động</h3>
            <div class="mt-2 px-7 py-3">
                <p id="action-modal-message" class="text-sm text-gray-500">
                    Bạn có chắc chắn thực hiện thao tác này không?
                </p>
            </div>
            <div class="items-center px-4 py-3 flex gap-3">
                <button id="action-abort-btn"
                    class="w-full px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300">
                    Không
                </button>
                <button id="action-confirm-btn"
                    class="w-full px-4 py-2 bg-orange-600 text-white rounded-md hover:bg-orange-700">
                    Đồng ý
                </button>
            </div>
        </div>
    </div>
</div>

<div id="toast-message" class="fixed top-6 right-6 bg-green-600 text-white px-4 py-2 rounded shadow-lg z-50 hidden">


@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Toast thông báo thành công hoặc lỗi
            function showToast(message, color = "bg-green-600") {
                const toast = document.getElementById('toast-message');
                toast.textContent = message;
                toast.className = `fixed top-6 right-6 ${color} text-white px-4 py-2 rounded shadow-lg z-50`;
                toast.style.display = 'block';
                setTimeout(() => {
                    toast.style.display = 'none';
                }, 2000);
            }

            let formToSubmit = null;
            let modalAction = 'cancel'; // hoặc 'receive'

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
                    modalIconContainer.className =
                        "mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100";
                    modalTitle.textContent = "Xác nhận hủy đơn hàng";
                    modalMessage.textContent =
                        "Bạn có chắc chắn muốn hủy đơn hàng này không? Hành động này không thể hoàn tác.";
                    confirmBtn.className = "w-full px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700";
                    confirmBtn.textContent = "Đồng ý hủy";
                } else if (actionType === 'receive') {
                    modalIcon.className = "fas fa-check text-green-600 text-xl";
                    modalIconContainer.className =
                        "mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100";
                    modalTitle.textContent = "Xác nhận đã nhận hàng";
                    modalMessage.textContent =
                        "Bạn xác nhận đã nhận được hàng? Vui lòng kiểm tra kỹ trước khi xác nhận.";
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

            // Nút hủy đơn hàng
            document.querySelectorAll('.cancel-order-form button[type="submit"]').forEach(button => {
                button.addEventListener('click', function(event) {
                    event.preventDefault();
                    const form = this.closest('form');
                    openActionModal(form, 'cancel');
                });
            });

            // Nút xác nhận đã nhận hàng
            document.querySelectorAll('.receive-order-form button[type="submit"]').forEach(button => {
                button.addEventListener('click', function(event) {
                    event.preventDefault();
                    const form = this.closest('form');
                    openActionModal(form, 'receive');
                });
            });
        });
    </script>
@endpush
