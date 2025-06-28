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
<div class="bg-gray-50 min-h-screen">
    <div class="max-w-4xl mx-auto px-4 py-8">
        
        <div class="mb-6">
            <a href="{{ route('customer.profile') }}" class="inline-flex items-center text-orange-600 hover:text-orange-700 font-medium">
                <i class="fas fa-arrow-left mr-2 h-4 w-4"></i>
                Quay lại trang cá nhân
            </a>
        </div>

        <div class="bg-white rounded-xl shadow-md">
            <div class="p-6 border-b">
                <h1 class="text-xl font-bold">Lịch sử đơn hàng</h1>
            </div>
            
            <div class="p-6">
                @if($orders->count() > 0)
                    <div class="space-y-4">
                        @foreach($orders as $order)
                            <div class="border rounded-lg p-4 transition-shadow hover:shadow-md">
                                <div class="flex justify-between items-start mb-3">
                                    <div>
                                        <h4 class="font-semibold text-gray-800">#{{ $order->order_code ?? $order->id }}</h4>
                                        <p class="text-sm text-gray-500">{{ $order->order_date->format('H:i') }} - {{ $order->order_date->isToday() ? 'Hôm nay' : $order->order_date->format('d/m/Y') }}</p>
                                    </div>
                                    <span class="text-xs font-medium px-2.5 py-1 rounded-full capitalize" style="background-color: {{ $order->status_color['bg'] ?? '#f3f4f6' }}; color: {{ $order->status_color['text'] ?? '#374151' }};">
                                        {{ $order->status_text }}
                                    </span>
                                </div>

                                <div class="mb-4">
                                    <p class="text-sm text-gray-500 mb-1 font-medium">{{ $order->branch->name ?? 'N/A' }}</p>
                                    <p class="text-sm text-gray-700">
                                        {{ $order->orderItems->map(fn($item) => (optional(optional($item->productVariant)->product)->name ?? optional($item->combo)->name ?? 'Sản phẩm') . ' x' . $item->quantity)->implode(', ') }}
                                    </p>
                                </div>

                                <div class="flex justify-between items-center">
                                    <span class="font-semibold text-lg text-orange-600">{{ number_format($order->total_amount, 0, ',', '.') }}đ</span>
                                    <div class="flex space-x-2">
                                        <a href="{{ route('customer.orders.show', $order) }}" class="inline-flex items-center justify-center rounded-md text-sm font-medium h-9 px-4 py-2 bg-gray-100 text-gray-800 hover:bg-gray-200">
                                            Chi tiết
                                        </a>
                                        {{-- ====== NEW BUTTON LOGIC ====== --}}
                                        @if($order->status == 'awaiting_confirmation')
                                            <form action="{{ route('customer.orders.updateStatus', $order) }}" method="POST" class="cancel-order-form">
                                                @csrf
                                                <input type="hidden" name="status" value="cancelled">
                                                <button type="submit" class="inline-flex items-center justify-center rounded-md text-sm font-medium h-9 px-4 py-2 border border-red-500 text-red-600 hover:bg-red-50">Hủy đơn</button>
                                            </form>

                                        {{-- Case 2: Order has been delivered by the driver --}}
                                        @elseif($order->status == 'delivered')
                                            <a href="#" class="inline-flex items-center justify-center rounded-md text-sm font-medium h-10 px-4 py-2 bg-red-100 text-red-700 hover:bg-red-200">Chưa nhận được hàng</a>
                                            <form action="{{ route('customer.orders.updateStatus', $order) }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="status" value="item_received">
                                                <button type="submit" class="inline-flex items-center justify-center rounded-md text-sm font-medium text-white h-10 px-4 py-2 bg-orange-500 hover:bg-orange-600">Xác nhận đã nhận hàng</button>
                                            </form>

                                        {{-- Case 3: Customer has confirmed they received the item --}}
                                        @elseif($order->status == 'item_received')
                                            <a href="#" class="inline-flex items-center justify-center rounded-md text-sm font-medium text-white h-10 px-4 py-2 bg-yellow-500 hover:bg-yellow-600">
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
{{-- THÊM ĐOẠN HTML NÀY VÀO CUỐI FILE BLADE CỦA BẠN --}}
<div id="cancel-confirmation-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
    <div class="relative mx-auto p-5 border w-full max-w-sm shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
            </div>
            <h3 class="text-lg leading-6 font-medium text-gray-900 mt-4">Xác nhận hủy đơn hàng</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500">
                    Bạn có chắc chắn muốn hủy đơn hàng này không? Hành động này không thể hoàn tác.
                </p>
            </div>
            <div class="items-center px-4 py-3 flex gap-3">
                <button id="cancel-abort-btn" class="w-full px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 focus:outline-none">
                    Không
                </button>
                <button id="cancel-confirm-btn" class="w-full px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none">
                    Đồng ý hủy
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Lắng nghe sự kiện submit trên TẤT CẢ các form hủy đơn
    function showToast(message, type = 'success') {
        const toast = document.createElement('div');
        const bgColor = type === 'success' ? 'bg-green-600' : 'bg-red-600';
        toast.className = `fixed bottom-4 right-4 text-white px-4 py-2 rounded-lg shadow-lg z-[101] transition-opacity duration-300 opacity-0 ${bgColor}`;
        toast.textContent = message;
        
        document.body.appendChild(toast);
        
        setTimeout(() => toast.classList.remove('opacity-0'), 10);
        setTimeout(() => {
            toast.classList.add('opacity-0');
            setTimeout(() => document.body.removeChild(toast), 300);
        }, 3000);
    }

    /**
     * Logic để xử lý việc hủy đơn hàng trên toàn trang
     */
    const OrderCancellationLogic = {
        elements: {
            modal: document.getElementById('cancel-confirmation-modal'),
            confirmBtn: document.getElementById('cancel-confirm-btn'),
            abortBtn: document.getElementById('cancel-abort-btn'),
            cancelForms: document.querySelectorAll('.cancel-order-form')
        },
        state: {
            formToSubmit: null
        },

        init() {
            if (!this.elements.modal) return;
            this.setupEventListeners();
        },

        setupEventListeners() {
            // Gán sự kiện cho tất cả các form hủy đơn
            this.elements.cancelForms.forEach(form => {
                form.addEventListener('submit', (e) => {
                    e.preventDefault();
                    this.openModal(form);
                });
            });

            // Gán sự kiện cho các nút trong modal
            this.elements.confirmBtn?.addEventListener('click', () => this.confirmAction());
            this.elements.abortBtn?.addEventListener('click', () => this.closeModal());
            this.elements.modal?.addEventListener('click', (e) => {
                if (e.target === this.elements.modal) this.closeModal();
            });
            document.addEventListener('keydown', (e) => {
                if (e.key === "Escape" && !this.elements.modal.classList.contains('hidden')) {
                    this.closeModal();
                }
            });
        },

        openModal(form) {
            this.state.formToSubmit = form;
            this.elements.modal.classList.remove('hidden');
        },

        closeModal() {
            this.state.formToSubmit = null;
            this.elements.modal.classList.add('hidden');
        },

        confirmAction() {
            if (this.state.formToSubmit) {
                this.sendCancelRequest(this.state.formToSubmit);
            }
            this.closeModal();
        },

        sendCancelRequest(form) {
            const button = form.querySelector('button[type="submit"]');
            const originalButtonContent = button.innerHTML;
            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            button.disabled = true;

            const url = form.action;
            const formData = new FormData(form);

            fetch(url, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': formData.get('_token'),
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json().then(data => ({ ok: response.ok, data })))
            .then(({ ok, data }) => {
                if (ok && data.success) {
                    showToast(data.message || 'Hủy đơn hàng thành công!', 'success');
                    this.updateUIAfterCancel(form);
                } else {
                    throw new Error(data.message || 'Hủy đơn thất bại.');
                }
            })
            .catch(error => {
                showToast(error.message, 'error');
                button.innerHTML = originalButtonContent;
                button.disabled = false;
            });
        },

        updateUIAfterCancel(form) {
            const orderCard = form.closest('.border.rounded-lg');
            if (!orderCard) return;

            // Cập nhật thẻ trạng thái
            const statusBadge = orderCard.querySelector('.text-xs.font-medium');
            if (statusBadge) {
                statusBadge.textContent = 'Đã hủy';
                statusBadge.style.backgroundColor = '#FEE2E2'; // Màu nền đỏ nhạt
                statusBadge.style.color = '#DC2626'; // Màu chữ đỏ
            }

            // Tìm và thay thế khu vực chứa các nút hành động
            const actionButtonsContainer = form.parentElement;
            if (actionButtonsContainer) {
                actionButtonsContainer.innerHTML = '<span class="text-sm text-gray-500">Đã hủy bởi bạn</span>';
            }
        }
    };

    // Khởi chạy logic
    OrderCancellationLogic.init();
});
</script>
@endpush