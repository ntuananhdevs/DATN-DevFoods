<div class="rounded-lg border bg-card text-card-foreground shadow-sm overflow-hidden order-card-container">
    <div class="flex flex-col space-y-1.5 p-4 bg-muted/50">
        <div class="flex justify-between items-start">
            <div>
                <h3 class="text-base font-semibold leading-none tracking-tight">Mã đơn: <span class="order-id">{{ $order['id'] }}</span></h3>
                <p class="text-xs text-muted-foreground flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-3 h-3 mr-1"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    {{ \Carbon\Carbon::parse($order['orderTime'])->locale('vi')->isoFormat('HH:mm DD/MM/YYYY') }}
                </p>
            </div>
            @php
                $statusVariant = '';
                switch ($order['status']) {
                    case 'Chờ nhận': $statusVariant = 'info'; break;
                    case 'Đang giao': $statusVariant = 'warning'; break;
                    case 'Đã hoàn thành': $statusVariant = 'success'; break;
                    case 'Đã hủy': $statusVariant = 'destructive'; break;
                    default: $statusVariant = 'secondary'; break;
                }
            @endphp
            <div class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 badge-{{ $statusVariant }} whitespace-nowrap">
                {{ $order['status'] }}
            </div>
        </div>
    </div>
    <div class="p-4 pt-0 text-sm space-y-2">
        <div class="flex items-start">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4 mr-2 mt-0.5 text-primary flex-shrink-0"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18"/><path d="M16 10a2 2 0 0 1-4 0v-4h4Z"/></svg>
            <div>
                <span class="font-medium">Lấy hàng:</span> {{ $order['pickupBranch'] }}
            </div>
        </div>
        <div class="flex items-start">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4 mr-2 mt-0.5 text-destructive flex-shrink-0"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
            <div>
                <span class="font-medium">Giao đến:</span> <span class="delivery-address">{{ $order['deliveryAddress'] }}</span>
            </div>
        </div>
        <div class="flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4 mr-2 text-gray-500 flex-shrink-0"><path d="M22 16.92v3a2 2 0 0 1-2.18 2.02 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.63A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-1.18 2.19l-.7.69a12 12 0 0 0 6.06 6.06l.69-.7a2 2 0 0 1 2.19-1.18 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
            <span>
                <span class="customer-name">{{ $order['customerName'] }}</span> - {{ $order['customerPhone'] }}
            </span>
        </div>
        <div class="shrink-0 bg-border h-[1px] w-full my-2"></div>
        <div class="flex justify-between items-center">
            <p class="font-semibold text-primary">Phí ship: {{ number_format($order['shippingFee'], 0, ',', '.') }}đ</p>
            <p class="text-xs text-muted-foreground">{{ number_format($order['distanceKm'], 1) }} km</p>
        </div>
    </div>
    <div class="flex items-center p-4 bg-muted/50">
        <a href="{{ route('driver.orders.show', ['orderId' => $order['id']]) }}" class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-primary text-primary-foreground hover:bg-primary/90 h-10 px-4 py-2 w-full">
            Xem chi tiết & Nhận đơn <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="ml-2 h-4 w-4"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
        </a>
    </div>
</div>