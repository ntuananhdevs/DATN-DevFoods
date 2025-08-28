@extends('layouts.customer.fullLayoutMaster')

@section('title', 'Lịch sử đơn hàng')

@section('content')
<style>
    .container-ft {
        max-width: 1240px;
    }
    #action-confirmation-modal {
        z-index: 9999;
    }
</style>
    <div class="bg-gradient-to-r from-orange-500 to-red-500 py-8">
        <div class="container-ft mx-auto px-4">
            <div class="flex items-center">
                <a href="{{ route('customer.profile') }}" class="text-white hover:text-white/80 mr-2">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <h1 class="text-2xl md:text-3xl font-bold text-white">Lịch sử đơn hàng</h1>
            </div>
        </div>
    </div>
    <div class="container-ft mx-auto ">
        <div class="flex flex-col gap-8">
            <section class="mb-10">
                <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                    <div class="p-6 border-b border-gray-100">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-xl font-bold">Đơn hàng của bạn</h2>
                    </div>
                    
                    <!-- Bộ lọc trạng thái đơn hàng -->
                    <div class="flex flex-wrap gap-2 mt-4 overflow-x-auto pb-2" id="status-filter">
                        @foreach($statuses as $statusKey => $statusLabel)
                            <a href="{{ route('customer.orders.index', $statusKey != 'all' ? ['status' => $statusKey] : []) }}" 
                               class="status-filter-btn px-4 py-2 rounded-full text-sm font-medium transition-colors 
                                    {{ request('status', 'all') == $statusKey ? 'bg-orange-500 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                                {{ $statusLabel }} {{ request('status', 'all') == $statusKey ? '(' . $orders->total() . ')' : '' }}
                            </a>
                        @endforeach
                    </div>
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
                                        <span
                                            class="pagination-item disabled px-3 py-2 bg-gray-100 text-gray-400 rounded-l-md">
                                            <i class="fas fa-chevron-left"></i>
                                        </span>
                                    @else
                                        <a href="{{ $orders->previousPageUrl() }}"
                                            class="pagination-item px-3 py-2 bg-white text-gray-700 hover:bg-orange-50 rounded-l-md border border-gray-200">
                                            <i class="fas fa-chevron-left"></i>
                                        </a>
                                    @endif
                                    {{-- Pagination Elements --}}
                                    @foreach ($orders->getUrlRange(1, $orders->lastPage()) as $page => $url)
                                        @if ($page == $orders->currentPage())
                                            <span
                                                class="pagination-item active px-3 py-2 bg-orange-500 text-white font-bold">{{ $page }}</span>
                                        @else
                                            <a href="{{ $url }}"
                                                class="pagination-item px-3 py-2 bg-white text-gray-700 hover:bg-orange-50">{{ $page }}</a>
                                        @endif
                                    @endforeach
                                    {{-- Next Page Link --}}
                                    @if ($orders->hasMorePages())
                                        <a href="{{ $orders->nextPageUrl() }}"
                                            class="pagination-item px-3 py-2 bg-white text-gray-700 hover:bg-orange-50 rounded-r-md border border-gray-200">
                                            <i class="fas fa-chevron-right"></i>
                                        </a>
                                    @else
                                        <span
                                            class="pagination-item disabled px-3 py-2 bg-gray-100 text-gray-400 rounded-r-md">
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
    <div id="action-confirmation-modal"
        class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4">
        <div class="relative mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <div id="modal-icon-container"
                    class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                    <i id="modal-icon" class="fas fa-times text-red-600 text-xl"></i>
                </div>
                <h3 id="action-modal-title" class="text-lg leading-6 font-medium text-gray-900 mt-4">Xác nhận hành động</h3>
                <div class="mt-2 px-7 py-3">
                    <p id="action-modal-message" class="text-sm text-gray-500">
                        Bạn có chắc chắn thực hiện thao tác này không?
                    </p>
                    
                    <!-- Phần chọn lý do hủy đơn -->
                    <div id="cancel-reason-section" class="mt-4 text-left hidden">
                        <p class="text-sm font-medium text-gray-700 mb-2">Vui lòng cho chúng tôi biết lý do bạn muốn hủy đơn hàng này.</p>
                        <div class="space-y-2">
                            <div>
                                <input type="radio" id="reason-changed-mind" name="cancel_reason" value="Tôi đã thay đổi ý định" class="mr-2">
                                <label for="reason-changed-mind" class="text-sm text-gray-600">Tôi đã thay đổi ý định</label>
                            </div>
                            <div>
                                <input type="radio" id="reason-better-price" name="cancel_reason" value="Tìm thấy giá tốt hơn ở nơi khác" class="mr-2">
                                <label for="reason-better-price" class="text-sm text-gray-600">Tìm thấy giá tốt hơn ở nơi khác</label>
                            </div>
                            <div>
                                <input type="radio" id="reason-delivery-time" name="cancel_reason" value="Thời gian giao hàng quá lâu" class="mr-2">
                                <label for="reason-delivery-time" class="text-sm text-gray-600">Thời gian giao hàng quá lâu</label>
                            </div>
                            <div>
                                <input type="radio" id="reason-wrong-product" name="cancel_reason" value="Đặt nhầm sản phẩm" class="mr-2">
                                <label for="reason-wrong-product" class="text-sm text-gray-600">Đặt nhầm sản phẩm</label>
                            </div>
                            <div>
                                <input type="radio" id="reason-financial" name="cancel_reason" value="Vấn đề tài chính" class="mr-2">
                                <label for="reason-financial" class="text-sm text-gray-600">Vấn đề tài chính</label>
                            </div>
                            <div>
                                <input type="radio" id="reason-duplicate" name="cancel_reason" value="Đặt trùng đơn hàng" class="mr-2">
                                <label for="reason-duplicate" class="text-sm text-gray-600">Đặt trùng đơn hàng</label>
                            </div>
                            <div>
                                <input type="radio" id="reason-other" name="cancel_reason" value="Khác" class="mr-2">
                                <label for="reason-other" class="text-sm text-gray-600">Khác</label>
                            </div>
                            <div id="other-reason-container" class="hidden mt-2">
                                <textarea id="other-reason-text" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-orange-500" placeholder="Vui lòng nhập lý do khác..."></textarea>
                            </div>
                        </div>
                    </div>
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
@endsection

@push('scripts')
    <script>
        // Lấy danh sách order id từ blade
        var orderIds = @json($orders->pluck('id'));
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Xử lý các nút hành động
            const statusFilterButtons = document.querySelectorAll('.status-filter-btn');
            const orderListContainer = document.getElementById('order-list-container');
            const paginationContainer = document.querySelector('.pagination-container');
            
            // Xử lý các nút hành động cho hủy đơn và xác nhận nhận hàng
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
            
            // Không cần JavaScript cho các nút lọc trạng thái vì đã chuyển thành liên kết thông thường
            
            // Toast thông báo thành công hoặc lỗi
            function showToast(message, color = "bg-green-600") {
                const toast = document.createElement('div');
                toast.className =
                    `fixed top-20 right-4 ${color} text-white px-4 py-2 rounded-lg shadow-lg z-50 transition-opacity duration-300 opacity-0`;
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
            const cancelReasonSection = document.getElementById('cancel-reason-section');
            const otherReasonContainer = document.getElementById('other-reason-container');
            const otherReasonText = document.getElementById('other-reason-text');

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
                    cancelReasonSection.classList.remove('hidden');
                } else if (actionType === 'receive') {
                    modalIcon.className = "fas fa-check text-green-600 text-xl";
                    modalIconContainer.className =
                        "mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100";
                    modalTitle.textContent = "Xác nhận đã nhận hàng";
                    modalMessage.textContent =
                        "Bạn xác nhận đã nhận được hàng? Vui lòng kiểm tra kỹ trước khi xác nhận.";
                    confirmBtn.className = "w-full px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700";
                    confirmBtn.textContent = "Đã nhận";
                    cancelReasonSection.classList.add('hidden');
                }
                modal.classList.remove('hidden');
            }

            function closeActionModal() {
                formToSubmit = null;
                modal.classList.add('hidden');
                // Reset radio buttons
                document.querySelectorAll('input[name="cancel_reason"]').forEach(radio => {
                    radio.checked = false;
                });
                otherReasonContainer.classList.add('hidden');
                otherReasonText.value = '';
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
                        
                        // Thêm lý do hủy đơn nếu đang hủy đơn hàng
                        if (modalAction === 'cancel') {
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
                            
                            formData.append('reason', reason);
                        }
                        
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
                                    
                                    // Cập nhật DOM trực tiếp thay vì reload trang
                                    if (data.order && formToSubmit) {
                                        const orderElement = formToSubmit.closest('[data-order-id]');
                                        if (orderElement) {
                                            // Cập nhật status badge
                                            const statusBadge = orderElement.querySelector('.status-badge');
                                            if (statusBadge && data.order.status_text) {
                                                statusBadge.textContent = data.order.status_text;
                                                if (data.order.status_color) {
                                                    statusBadge.style.backgroundColor = data.order.status_color;
                                                }
                                                if (data.order.status_text_color) {
                                                    statusBadge.style.color = data.order.status_text_color;
                                                }
                                            }
                                            
                                            // Ẩn form đã submit
                                            formToSubmit.style.display = 'none';
                                            
                                            // Nếu là hủy đơn, ẩn tất cả action buttons
                                            if (modalAction === 'cancel') {
                                                const actionContainer = orderElement.querySelector('.order-actions');
                                                if (actionContainer) {
                                                    const actionButtons = actionContainer.querySelectorAll('form, button');
                                                    actionButtons.forEach(btn => btn.style.display = 'none');
                                                }
                                            }
                                            
                                            // Nếu là xác nhận đã nhận hàng, thêm nút đánh giá
                                            if (modalAction === 'receive') {
                                                const actionContainer = orderElement.querySelector('.order-actions');
                                                if (actionContainer) {
                                                    // Tạo nút đánh giá
                                                    const reviewButton = document.createElement('a');
                                                    reviewButton.href = '#';
                                                    reviewButton.className = 'inline-flex items-center justify-center rounded-md text-sm font-medium text-white px-4 py-2 bg-yellow-500 hover:bg-yellow-600';
                                                    reviewButton.innerHTML = '<svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path></svg> Đánh giá';
                                                    actionContainer.appendChild(reviewButton);
                                                }
                                            }
                                        }
                                    }
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
        var pusher = new Pusher('{{ env('PUSHER_APP_KEY') }}', {
            cluster: '{{ env('PUSHER_APP_CLUSTER') }}',
            encrypted: true,
            authEndpoint: '/broadcasting/auth',
            auth: {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                }
            }
        });
        
        // Add Pusher debugging
        pusher.connection.bind('connected', function() {
            console.log('✅ Pusher connected successfully');
        });

        pusher.connection.bind('error', function(err) {
            console.error('❌ Pusher connection error:', err);
        });

        pusher.connection.bind('disconnected', function() {
            console.log('⚠️ Pusher disconnected');
        });
        
        if (orderIds && Array.isArray(orderIds)) {
            orderIds.forEach(function(orderId) {
                var channel = pusher.subscribe('private-order.' + orderId);
                
                channel.bind('pusher:subscription_succeeded', function() {
                    console.log('✅ Subscribed to order channel:', 'private-order.' + orderId);
                });
                
                channel.bind('pusher:subscription_error', function(error) {
                    console.error('❌ Failed to subscribe to order channel:', 'private-order.' + orderId, error);
                });
                
                channel.bind('order-status-updated', function(data) {
                    console.log('Pusher event order-status-updated received for order', orderId, data);
                    showToast('🔄 Đơn hàng #' + orderId + ' vừa được cập nhật trạng thái!');
                    
                    // Cập nhật DOM trực tiếp thay vì reload trang
                    if (data.order) {
                        const orderElement = document.querySelector(`[data-order-id="${orderId}"]`);
                        if (orderElement) {
                            // Cập nhật status badge
                            const statusBadge = orderElement.querySelector('.status-badge');
                            if (statusBadge && data.order.status_text) {
                                statusBadge.textContent = data.order.status_text;
                                if (data.order.status_color) {
                                    statusBadge.style.backgroundColor = data.order.status_color;
                                }
                                if (data.order.status_text_color) {
                                    statusBadge.style.color = data.order.status_text_color;
                                }
                            }
                            
                            // Ẩn/hiện các action buttons dựa trên status mới
                            const actionContainer = orderElement.querySelector('.order-actions');
                            if (actionContainer) {
                                const cancelForm = actionContainer.querySelector('form[action*="updateStatus"][method="POST"] input[value="cancelled"]');
                                const receiveForm = actionContainer.querySelector('form[action*="updateStatus"][method="POST"] input[value="item_received"]');
                                
                                // Ẩn tất cả action buttons nếu đơn đã hủy hoặc hoàn thành
                                if (data.order.status === 'cancelled' || data.order.status === 'item_received') {
                                    const actionButtons = actionContainer.querySelectorAll('form, button');
                                    actionButtons.forEach(btn => btn.style.display = 'none');
                                }
                                
                                // Hiện nút nhận hàng nếu đơn đã giao
                                if (data.order.status === 'delivered' && receiveForm) {
                                    receiveForm.closest('form').style.display = 'block';
                                }
                                
                                // Ẩn nút hủy nếu đơn không còn ở trạng thái chờ xác nhận
                                if (data.order.status !== 'awaiting_confirmation' && cancelForm) {
                                    cancelForm.closest('form').style.display = 'none';
                                }
                            }
                        }
                    }
                });
            });
        }
    </script>
@endpush
