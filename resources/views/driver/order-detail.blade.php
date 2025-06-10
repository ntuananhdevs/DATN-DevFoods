@extends('layouts.driver.masterLayout')

@section('title', 'Chi tiết đơn hàng ' . $order['id'])

@section('content')
    <div class="p-4 md:p-6 space-y-6">
        <div class="flex items-center justify-between">
            <a href="{{ route('driver.orders.index') }}" class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-9 w-9">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="m15 18-6-6 6-6"/></svg>
                <span class="sr-only">Quay lại</span>
            </a>
            <h1 class="text-xl font-semibold">Chi tiết đơn hàng: {{ $order['id'] }}</h1>
            <div class="w-10"></div> {{-- Spacer --}}
        </div>

        <div id="mapbox-container" class="relative w-full aspect-[16/10] rounded-lg overflow-hidden border">
            <div class="absolute bottom-2 right-2 bg-black/50 text-white text-xs px-2 py-1 rounded z-10">
                Bản đồ &copy; Mapbox &copy; OpenStreetMap
            </div>
        </div>

        <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
            <div class="flex flex-col space-y-1.5 p-4">
                <h3 class="text-2xl font-semibold leading-none tracking-tight">Thông tin đơn hàng</h3>
                <div class="flex justify-between items-center">
                    <p class="text-sm text-muted-foreground">
                        Trạng thái: <span id="order-status-span" class="font-semibold"></span>
                    </p>
                    <p id="estimated-delivery-time" class="text-xs text-muted-foreground flex items-center hidden"></p>
                </div>
            </div>
            <div class="p-4 pt-0 space-y-3 text-sm">
                <div class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4 mr-2 text-muted-foreground"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                    <strong>Lấy hàng tại:</strong>&nbsp;{{ $order['pickupBranch'] }}
                </div>
                <div class="flex items-start">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4 mr-2 mt-0.5 text-muted-foreground"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                    <div>
                        <strong>Giao đến:</strong>&nbsp;{{ $order['deliveryAddress'] }}
                    </div>
                </div>
                <div class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4 mr-2 text-muted-foreground"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    <strong>Người nhận:</strong>&nbsp;{{ $order['customerName'] }}
                </div>
                <div class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4 mr-2 text-muted-foreground"><path d="M22 16.92v3a2 2 0 0 1-2.18 2.02 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.63A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-1.18 2.19l-.7.69a12 12 0 0 0 6.06 6.06l.69-.7a2 2 0 0 1 2.19-1.18 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                    <strong>SĐT:</strong>&nbsp;
                    <a href="tel:{{ $order['customerPhone'] }}" class="text-blue-600 hover:underline">
                        {{ $order['customerPhone'] }}
                    </a>
                </div>
                <div class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4 mr-2 text-muted-foreground"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    <strong>Đặt lúc:</strong>&nbsp;{{ \Carbon\Carbon::parse($order['orderTime'])->locale('vi')->isoFormat('HH:mm DD/MM/YYYY') }}
                </div>

                <div class="my-2 h-[1px] w-full shrink-0 bg-border"></div>
                <h4 class="font-semibold text-md">Chi tiết món:</h4>
                <ul class="space-y-1 list-disc list-inside pl-1">
                    @foreach($order['items'] as $item)
                        <li>
                            {{ $item['name'] }} (x{{ $item['quantity'] }}) - {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}đ
                        </li>
                    @endforeach
                </ul>
                <div class="my-2 h-[1px] w-full shrink-0 bg-border"></div>
                @if(isset($order['notes']) && !empty($order['notes']))
                    <div class="relative w-full rounded-lg border p-4 [&>svg~*]:pl-7 [&>svg+div]:translate-y-[-3px] [&>svg]:absolute [&>svg]:left-4 [&>svg]:top-4 [&>svg]:text-foreground alert-info">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/></svg>
                        <h5 class="mb-1 font-medium leading-none tracking-tight">Ghi chú của khách</h5>
                        <div class="text-sm [&_p]:leading-relaxed">{{ $order['notes'] }}</div>
                    </div>
                @endif
                <div class="grid grid-cols-2 gap-x-4 gap-y-2 pt-2">
                    <p>Tổng tiền hàng: <span class="font-semibold float-right">{{ number_format($order['totalAmount'], 0, ',', '.') }}đ</span></p>
                    <p>Phí vận chuyển: <span class="font-semibold float-right">{{ number_format($order['shippingFee'], 0, ',', '.') }}đ</span></p>
                    <p class="text-lg font-bold">Khách trả: <span class="font-bold float-right">{{ number_format($order['finalTotal'], 0, ',', '.') }}đ</span></p>
                    <p class="text-lg font-bold text-green-600">Thu nhập tài xế: <span class="font-bold float-right">{{ number_format($order['driverEarnings'], 0, ',', '.') }}đ</span></p>
                </div>
            </div>
            <div id="order-action-buttons" class="flex flex-col sm:flex-row gap-2 pt-4 border-t p-4">
                {{-- Buttons will be rendered by JavaScript --}}
            </div>
        </div>
    </div>
@endsection

@section('page_scripts')
    <script>
        // Hàm JavaScript để gửi yêu cầu cập nhật trạng thái
        function updateOrderStatus(orderId, newStatus) {
            fetch('/driver/orders/' + orderId + '/update-status', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}' // Đảm bảo có CSRF token
                },
                body: JSON.stringify({ status: newStatus })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Cập nhật trạng thái thành công: ' + data.message);
                    // Cập nhật UI mà không cần tải lại trang
                    updateUI(orderId, newStatus);
                } else {
                    alert('Lỗi: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Đã xảy ra lỗi khi cập nhật trạng thái.');
            });
        }

        // Hàm để cập nhật UI sau khi trạng thái thay đổi
        function updateUI(orderId, newStatus) {
            const statusSpan = document.getElementById('order-status-span');
            const actionButtonsDiv = document.getElementById('order-action-buttons');
            const estimatedTimeDiv = document.getElementById('estimated-delivery-time');

            if (statusSpan) {
                statusSpan.textContent = newStatus;
                statusSpan.className = 'font-semibold'; // Reset class
                switch (newStatus) {
                    case 'Chờ nhận': statusSpan.classList.add('text-blue-600'); break;
                    case 'Đang giao': statusSpan.classList.add('text-yellow-600'); break;
                    case 'Đã hoàn thành': statusSpan.classList.add('text-green-600'); break;
                    case 'Đã hủy': statusSpan.classList.add('text-red-600'); break;
                }
            }

            // Cập nhật nút hành động
            if (actionButtonsDiv) {
                actionButtonsDiv.innerHTML = ''; // Xóa các nút cũ
                if (newStatus === 'Chờ nhận') {
                    actionButtonsDiv.innerHTML = `
                        <button type="button" onclick="updateOrderStatus('${orderId}', 'Đang giao')" class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-primary text-primary-foreground hover:bg-primary/90 h-10 px-4 py-2 w-full sm:w-auto flex-1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2 h-4 w-4"><path d="M10.5 14.5c2.4 2.4 5.6 2.4 8 0 2.4-2.4 2.4-5.6 0-8-2.4-2.4-5.6-2.4-8 0-2.4 2.4-2.4 5.6 0 8Z"/><path d="M2 2l2.5 2.5"/><path d="M21.5 21.5L19 19"/><path d="M17.5 17.5 22 22"/><path d="M2 12h2"/><path d="M12 2v2"/><path d="M20 12h2"/><path d="M12 20v2"/></svg>
                            Đã nhận hàng & Bắt đầu giao
                        </button>
                        <button type="button" onclick="updateOrderStatus('${orderId}', 'Đã hủy')" class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-10 px-4 py-2 w-full sm:w-auto">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2 h-4 w-4"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/></svg>
                            Báo cáo sự cố / Hủy đơn
                        </button>
                    `;
                    estimatedTimeDiv.classList.add('hidden');
                } else if (newStatus === 'Đang giao') {
                    const now = new Date();
                    const estimatedTime = new Date(now.getTime() + 30 * 60 * 1000).toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' });
                    actionButtonsDiv.innerHTML = `
                        <button type="button" onclick="updateOrderStatus('${orderId}', 'Đã hoàn thành')" class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-primary text-primary-foreground hover:bg-primary/90 h-10 px-4 py-2 w-full sm:w-auto flex-1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2 h-4 w-4"><path d="M12 22c-5.523 0-10-4.477-10-10S6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z"/><path d="m9 12 2 2 4-4"/></svg>
                            Đã giao thành công
                        </button>
                        <button type="button" onclick="updateOrderStatus('${orderId}', 'Đã hủy')" class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-10 px-4 py-2 w-full sm:w-auto">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2 h-4 w-4"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/></svg>
                            Báo cáo sự cố / Hủy đơn
                        </button>
                    `;
                    estimatedTimeDiv.classList.remove('hidden');
                    estimatedTimeDiv.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-3 h-3 mr-1"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg> Dự kiến giao: ${estimatedTime}`;
                } else if (newStatus === 'Đã hoàn thành') {
                    actionButtonsDiv.innerHTML = `
                        <div class="relative w-full rounded-lg border p-4 [&>svg~*]:pl-7 [&>svg+div]:translate-y-[-3px] [&>svg]:absolute [&>svg]:left-4 [&>svg]:top-4 [&>svg]:text-foreground border-success/50 text-success dark:border-success [&>svg]:text-success">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="M12 22c-5.523 0-10-4.477-10-10S6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z"/><path d="m9 12 2 2 4-4"/></svg>
                            <h5 class="mb-1 font-medium leading-none tracking-tight">Đơn hàng đã hoàn thành!</h5>
                            <div class="text-sm [&_p]:leading-relaxed">Thu nhập {{ number_format($order['driverEarnings'], 0, ',', '.') }}đ đã được ghi nhận.</div>
                        </div>
                    `;
                    estimatedTimeDiv.classList.add('hidden');
                } else if (newStatus === 'Đã hủy') {
                    actionButtonsDiv.innerHTML = `
                        <div class="relative w-full rounded-lg border p-4 [&>svg~*]:pl-7 [&>svg+div]:translate-y-[-3px] [&>svg]:absolute [&>svg]:left-4 [&>svg]:top-4 [&>svg]:text-foreground border-destructive/50 text-destructive dark:border-destructive [&>svg]:text-destructive">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/></svg>
                            <h5 class="mb-1 font-medium leading-none tracking-tight">Đơn hàng đã bị hủy.</h5>
                            <div class="text-sm [&_p]:leading-relaxed">Vui lòng liên hệ hỗ trợ nếu có vấn đề.</div>
                        </div>
                    `;
                    estimatedTimeDiv.classList.add('hidden');
                }
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const orderData = @json($order);
            updateUI(orderData.id, orderData.status); // Khởi tạo UI với trạng thái hiện tại
        });
    </script>
@endsection