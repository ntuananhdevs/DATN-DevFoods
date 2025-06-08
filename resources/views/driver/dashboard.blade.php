@extends('layouts.driver.masterLayout')

@section('title', 'Tổng quan')

@section('content')
    <div class="p-4 md:p-6 space-y-6">
        <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
            <div class="flex flex-row items-center justify-between space-y-0 p-4 pb-2">
                <div class="flex items-center gap-3">
                    <img src="{{ $driver->avatarUrl ?? '/placeholder.svg?width=128&height=128' }}" alt="{{ $driver->name }}" width="56" height="56" class="rounded-full border" />
                    <div>
                        <h3 class="text-xl font-bold tracking-tight">{{ $driver->name }}</h3>
                        <p class="text-sm text-muted-foreground">{{ $driver->vehicle }} - {{ $driver->licensePlate }}</p>
                    </div>
                </div>
                <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 {{ $driver->isActive ? 'bg-green-500 hover:bg-green-600 text-white' : 'border-destructive text-destructive' }}">
                    {{ $driver->isActive ? "Đang hoạt động" : "Nghỉ" }}
                </span>
            </div>
            <div class="p-4 pt-0">
                <div class="grid grid-cols-3 gap-4 text-center pt-4">
                    <div>
                        <p class="text-2xl font-bold">{{ count($ordersToday) }}</p>
                        <p class="text-xs text-muted-foreground">Đơn hôm nay</p>
                    </div>
                    <div>
                        <p class="text-2xl font-bold">{{ number_format($totalEarnedToday, 0, ',', '.') }}đ</p>
                        <p class="text-xs text-muted-foreground">Thu nhập</p>
                    </div>
                    <div>
                        <p class="text-2xl font-bold">{{ number_format($totalKmToday, 1, ',', '.') }}</p>
                        <p class="text-xs text-muted-foreground">Km đã chạy</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
            <div class="flex flex-col space-y-1.5 p-4">
                <h3 class="text-2xl font-semibold leading-none tracking-tight">Đơn hàng mới chờ nhận</h3>
                <p class="text-sm text-muted-foreground">Các đơn hàng gần đây cần bạn xác nhận.</p>
            </div>
            <div class="p-4 pt-0 space-y-4">
                @if(count($pendingOrders) > 0)
                    @foreach($pendingOrders as $order)
                        @include('partials.driver.order-card', ['order' => $order, 'showActions' => true])
                    @endforeach
                @else
                    <p class="text-muted-foreground text-center py-4">Không có đơn hàng mới.</p>
                @endif
                @if(count($allPendingOrders) > 3)
                    <a href="{{ route('driver.orders.index', ['status' => 'Chờ nhận']) }}" class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-10 px-4 py-2 w-full">
                        Xem tất cả đơn chờ nhận
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="ml-2 h-4 w-4"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                    </a>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4 md:grid-cols-4">
            <a href="{{ route('driver.orders.index', ['status' => 'Chờ nhận']) }}" class="rounded-lg border bg-card text-card-foreground shadow-sm hover:shadow-md transition-shadow">
                <div class="p-6 flex flex-col items-center justify-center aspect-square">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-10 w-10 text-primary mb-2"><path d="M2 12h6"/><path d="M16 12h6"/><path d="M12 2v20"/><path d="M12 7l-5 5 5 5"/><path d="M12 17l5-5-5-5"/></svg>
                    <p class="font-semibold text-center">Đơn chờ nhận</p>
                    <p class="text-xs text-muted-foreground text-center">({{ count($allPendingOrders) }} đơn)</p>
                </div>
            </a>
            <a href="{{ route('driver.orders.index', ['status' => 'Đang giao']) }}" class="rounded-lg border bg-card text-card-foreground shadow-sm hover:shadow-md transition-shadow">
                <div class="p-6 flex flex-col items-center justify-center aspect-square">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-10 w-10 text-yellow-500 mb-2 animate-pulse"><path d="M2 12h6"/><path d="M16 12h6"/><path d="M12 2v20"/><path d="M12 7l-5 5 5 5"/><path d="M12 17l5-5-5-5"/></svg>
                    <p class="font-semibold text-center">Đơn đang giao</p>
                    <p class="text-xs text-muted-foreground text-center">({{ count($allDeliveringOrders) }} đơn)</p>
                </div>
            </a>
            <a href="{{ route('driver.history') }}" class="rounded-lg border bg-card text-card-foreground shadow-sm hover:shadow-md transition-shadow">
                <div class="p-6 flex flex-col items-center justify-center aspect-square">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-10 w-10 text-green-600 mb-2"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/></svg>
                    <p class="font-semibold text-center">Lịch sử giao</p>
                </div>
            </a>
            <a href="{{ route('driver.profile') }}" class="rounded-lg border bg-card text-card-foreground shadow-sm hover:shadow-md transition-shadow">
                <div class="p-6 flex flex-col items-center justify-center aspect-square">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-10 w-10 text-blue-600 mb-2"><line x1="12" x2="12" y1="2" y2="22"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                    <p class="font-semibold text-center">Thu nhập</p>
                </div>
            </a>
        </div>
    </div>
@endsection

@section('page_scripts')
    <script>
        // Any specific JS for dashboard page can go here
        // For example, if you had charts or dynamic elements
    </script>
@endsection