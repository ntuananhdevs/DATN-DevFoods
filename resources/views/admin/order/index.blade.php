@extends('layouts.admin.contentLayoutMaster')

@section('title', 'Danh sách đơn hàng')
@section('description', 'Quản lý danh sách đơn hàng của bạn')

@section('content')
<style>
    /* Badge style giống product index */
    .order-status-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 500;
        line-height: 1.25rem;
        transition: all 0.2s ease;
    }
    .order-status-badge.awaiting_confirmation {
        background-color: #fef3c7;
        color: #d97706;
    }
    .order-status-badge.order_confirmed {
        background-color: #dbeafe;
        color: #2563eb;
    }
    .order-status-badge.in_transit {
        background-color: #ede9fe;
        color: #7c3aed;
    }
    .order-status-badge.delivered {
        background-color: #dcfce7;
        color: #15803d;
    }
    .order-status-badge.cancelled {
        background-color: #fee2e2;
        color: #dc2626;
    }
    .order-status-badge.refunded {
        background-color: #e0e7ef;
        color: #334155;
    }
    
    /* Status tab style từ branch */
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
</style>
<div class="fade-in flex flex-col gap-4 pb-4">
    <!-- Main Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="flex aspect-square w-10 h-10 items-center justify-center rounded-lg bg-primary text-primary-foreground">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-file-text">
                    <path d="M4 22a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h7l7 7v13a2 2 0 0 1-2 2Z"></path>
                    <polyline points="14 3 14 8 19 8"></polyline>
                </svg>
            </div>
            <div>
                <h2 class="text-3xl font-bold tracking-tight">Quản lý đơn hàng</h2>
                <p class="text-muted-foreground">Quản lý danh sách đơn hàng của bạn</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <button class="btn btn-outline flex items-center" onclick="window.location.href='{{ route('admin.orders.export') }}'">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                    <polyline points="7 10 12 15 17 10"></polyline>
                    <line x1="12" y1="15" x2="12" y2="3"></line>
                </svg>
                Xuất báo cáo
            </button>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card border rounded-lg overflow-hidden">
        <div class="p-6 border-b flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <form method="GET" class="flex items-center gap-2 w-full md:w-auto">
                <input type="text" name="order_code" value="{{ request('order_code') }}" placeholder="Tìm kiếm mã đơn hàng..." class="border rounded-md px-3 py-2 bg-background text-sm w-full md:w-64" />
                <select name="branch_id" class="rounded-xl border border-gray-200 px-4 py-2 min-w-[180px] text-[15px] bg-white text-gray-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition outline-none">
                    <option value="">Tất cả chi nhánh</option>
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                            {{ $branch->name }}
                        </option>
                    @endforeach
                </select>
                <input type="text" name="date" value="{{ request('date') }}" placeholder="dd/mm/yyyy" class="border border-gray-200 rounded-xl px-4 py-2 min-w-[140px] text-[15px] bg-white text-gray-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition outline-none" />
                <button type="submit" class="btn btn-outline">Lọc</button>
            </form>
        </div>
    </div>

    <!-- Status Tabs (căn giữa) -->
    <div class="mb-6">
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex  space-x-8 overflow-x-auto">
                @php
                    $tabs = [
                        ['label' => 'Tất cả', 'key' => '', 'count' => $counts['all'] ?? 0],
                        ['label' => 'Chờ xác nhận', 'key' => 'awaiting_confirmation', 'count' => $counts['awaiting_confirmation'] ?? 0],
                        ['label' => 'Chờ tài xế', 'key' => 'awaiting_driver', 'count' => $counts['awaiting_driver'] ?? 0],
                        ['label' => 'Đang giao', 'key' => 'in_transit', 'count' => $counts['in_transit'] ?? 0],
                        ['label' => 'Đã giao', 'key' => 'delivered', 'count' => $counts['delivered'] ?? 0],
                        ['label' => 'Đã hủy', 'key' => 'cancelled', 'count' => $counts['cancelled'] ?? 0],
                        ['label' => 'Đã hoàn tiền', 'key' => 'refunded', 'count' => $counts['refunded'] ?? 0],
                    ];
                @endphp
                @foreach($tabs as $tab)
                    <a href="{{ route('admin.orders.index', array_merge(request()->except('page'), ['status' => $tab['key']])) }}"
                       class="status-tab whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm {{ (request('status') == $tab['key'] || (!request('status') && $tab['key'] === '')) ? 'active border-blue-500 text-blue-600' : 'border-transparent text-gray-500' }}">
                        {{ $tab['label'] }} ({{ $tab['count'] }})
                    </a>
                @endforeach
            </nav>
        </div>
    </div>

    <!-- Table Card -->
    <div class="card border rounded-lg overflow-hidden">
        <!-- Table header -->
        <div class="p-6 border-b">
            <h3 class="text-lg font-medium">Danh sách đơn hàng</h3>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b bg-muted/50">
                        <th class="py-3 px-4 text-left font-medium">Mã đơn hàng</th>
                        <th class="py-3 px-4 text-left font-medium">Khách hàng</th>
                        <th class="py-3 px-4 text-left font-medium">Chi nhánh</th>
                        <th class="py-3 px-4 text-right font-medium">Tổng tiền</th>
                        <th class="py-3 px-4 text-left font-medium">Trạng thái</th>
                        <th class="py-3 px-4 text-left font-medium">Thời gian</th>
                        <th class="py-3 px-4 text-center font-medium">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                        @include('admin.order._order_row', ['order' => $order])
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-8">
                            <div class="flex flex-col items-center justify-center text-muted-foreground">
                                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mb-2">
                                    <path d="M6 2L3 6v13a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"></path>
                                    <path d="M3 6h18"></path>
                                    <path d="M16 10a4 4 0 0 1-8 0"></path>
                                </svg>
                                <h3 class="text-lg font-medium">Không có đơn hàng nào</h3>
                                <p class="text-sm">Hãy thêm đơn hàng mới để bắt đầu</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="pagination-container flex items-center justify-between px-4 py-4 border-t">
            <div class="text-sm text-muted-foreground">
                Hiển thị <span id="paginationStart">{{ $orders->firstItem() }}</span> đến <span id="paginationEnd">{{ $orders->lastItem() }}</span> của <span id="paginationTotal">{{ $orders->total() }}</span> mục
            </div>
            <div class="flex items-center justify-end space-x-2 ml-auto" id="paginationControls">
                {{ $orders->withQueryString()->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Pusher Configuration -->
<script>
    window.pusherKey = '{{ config("broadcasting.connections.pusher.key") }}';
    window.pusherCluster = '{{ config("broadcasting.connections.pusher.options.cluster") }}';
</script>
<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
@endsection

@section('scripts')
<script src="{{ asset('js/admin/orders-realtime.js') }}"></script>
@endsection
