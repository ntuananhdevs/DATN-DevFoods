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
                            <button type="button" data-status="{{ $statusKey }}" 
                               class="status-filter-btn px-4 py-2 rounded-full text-sm font-medium transition-colors 
                                    {{ request('status', 'all') == $statusKey ? 'bg-orange-500 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                                {{ $statusLabel }} {{ request('status', 'all') == $statusKey ? '(' . $orders->total() . ')' : '' }}
                            </button>
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
            // Xử lý lọc đơn hàng theo trạng thái bằng AJAX
            const statusFilterButtons = document.querySelectorAll('.status-filter-btn');
            const orderListContainer = document.getElementById('order-list-container');
            const paginationContainer = document.querySelector('.pagination-container');
            
            // Hàm hiển thị loading
            function showLoading() {
                orderListContainer.innerHTML = '<div class="flex justify-center items-center py-12"><div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-orange-500"></div></div>';
            }
            
            // Hàm cập nhật URL mà không reload trang
            function updateUrlParam(key, value) {
                const url = new URL(window.location.href);
                if (value === 'all') {
                    url.searchParams.delete(key);
                } else {
                    url.searchParams.set(key, value);
                }
                window.history.pushState({}, '', url);
                return url;
            }
            
            // Hàm cập nhật trạng thái active của các nút lọc
            function updateFilterButtonsState(activeStatus) {
                statusFilterButtons.forEach(button => {
                    const status = button.dataset.status;
                    if (status === activeStatus) {
                        button.classList.remove('bg-gray-100', 'text-gray-700', 'hover:bg-gray-200');
                        button.classList.add('bg-orange-500', 'text-white');
                    } else {
                        button.classList.remove('bg-orange-500', 'text-white');
                        button.classList.add('bg-gray-100', 'text-gray-700', 'hover:bg-gray-200');
                    }
                });
            }
            
            // Hàm để gắn lại các event listener cho các nút hành động
            function reattachActionButtonListeners() {
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
            }
            
            // Hàm tải danh sách đơn hàng theo trạng thái
            function loadOrdersByStatus(status) {
                showLoading();
                const url = updateUrlParam('status', status);
                
                // Sử dụng URL tuyệt đối thay vì route helper để đảm bảo đúng đường dẫn
                const listPartialUrl = "{{ url('/customer/orders/list') }}";
                const indexUrl = "{{ url('/customer/orders') }}";
                
                fetch(`${listPartialUrl}?${url.searchParams.toString()}`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP error! Status: ${response.status}`);
                        }
                        return response.text();
                    })
                    .then(html => {
                        orderListContainer.innerHTML = html;
                        
                        // Cập nhật lại các event listener cho các nút trong danh sách đơn hàng
                        reattachActionButtonListeners();
                        
                        // Cập nhật lại danh sách orderIds cho Pusher
                        orderIds = Array.from(document.querySelectorAll('[data-order-id]'))
                            .map(el => parseInt(el.dataset.orderId))
                            .filter(id => !isNaN(id));
                            
                        // Cập nhật phân trang
                        return fetch(`${indexUrl}?${url.searchParams.toString()}`);
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP error! Status: ${response.status}`);
                        }
                        return response.text();
                    })
                    .then(html => {
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');
                        const newPagination = doc.querySelector('.pagination-container');
                        
                        if (newPagination && paginationContainer) {
                            paginationContainer.innerHTML = newPagination.innerHTML;
                            
                            // Cập nhật event listener cho các nút phân trang
                            document.querySelectorAll('.pagination-item').forEach(item => {
                                if (!item.classList.contains('disabled') && !item.classList.contains('active')) {
                                    item.addEventListener('click', function(e) {
                                        e.preventDefault();
                                        const pageUrl = new URL(this.href);
                                        const pageParams = pageUrl.searchParams.toString();
                                        
                                        loadOrdersByPage(pageParams);
                                    });
                                }
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error loading orders:', error);
                        orderListContainer.innerHTML = '<div class="text-center text-red-500 py-12">Có lỗi xảy ra khi tải dữ liệu. Vui lòng thử lại sau.</div>';
                    });
            }
            
            // Hàm tải danh sách đơn hàng theo trang
            function loadOrdersByPage(pageParams) {
                showLoading();
                
                // Sử dụng URL tuyệt đối thay vì route helper để đảm bảo đúng đường dẫn
                const listPartialUrl = "{{ url('/customer/orders/list') }}";
                const indexUrl = "{{ url('/customer/orders') }}";
                
                fetch(`${listPartialUrl}?${pageParams}`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP error! Status: ${response.status}`);
                        }
                        return response.text();
                    })
                    .then(html => {
                        orderListContainer.innerHTML = html;
                        
                        // Cập nhật URL
                        window.history.pushState({}, '', `${indexUrl}?${pageParams}`);
                        
                        // Cập nhật lại các event listener
                        reattachActionButtonListeners();
                        
                        // Cập nhật lại danh sách orderIds cho Pusher
                        orderIds = Array.from(document.querySelectorAll('[data-order-id]'))
                            .map(el => parseInt(el.dataset.orderId))
                            .filter(id => !isNaN(id));
                    })
                    .catch(error => {
                        console.error('Error loading orders by page:', error);
                        orderListContainer.innerHTML = '<div class="text-center text-red-500 py-12">Có lỗi xảy ra khi tải dữ liệu. Vui lòng thử lại sau.</div>';
                    });
            }
            
            // Thêm event listener cho các nút lọc trạng thái
            statusFilterButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const status = this.dataset.status;
                    loadOrdersByStatus(status);
                    updateFilterButtonsState(status);
                });
            });
            
            // Thêm event listener cho các nút phân trang
            document.querySelectorAll('.pagination-item').forEach(item => {
                if (!item.classList.contains('disabled') && !item.classList.contains('active')) {
                    item.addEventListener('click', function(e) {
                        e.preventDefault();
                        const pageUrl = new URL(this.href);
                        const pageParams = pageUrl.searchParams.toString();
                        
                        loadOrdersByPage(pageParams);
                    });
                }
            });
            
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
                
                channel.bind('OrderStatusUpdated', function(data) {
                    console.log('Pusher event OrderStatusUpdated received for order', orderId, data);
                    showToast('🔄 Đơn hàng #' + orderId + ' vừa được cập nhật trạng thái!');
                    
                    // Lấy trạng thái hiện tại từ URL
                    const urlParams = new URLSearchParams(window.location.search);
                    const currentStatus = urlParams.get('status') || 'all';
                    
                    // Cập nhật danh sách đơn hàng với trạng thái hiện tại
                    fetch("{{ route('customer.orders.listPartial') }}?status=" + currentStatus)
                        .then(response => response.text())
                        .then(html => {
                            document.getElementById('order-list-container').innerHTML = html;
                            // Gắn lại các event listener sau khi cập nhật nội dung
                            reattachActionButtonListeners();
                        });
                });
            });
        }
    </script>
@endpush
