@extends('layouts.admin.contentLayoutMaster')

@section('content')
<div class="container mx-auto py-4">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-4 gap-2">
        <div>
            <h1 class="text-2xl font-bold mb-1">Quản lý đơn hàng</h1>
            <p class="text-gray-500">Theo dõi và xử lý các đơn hàng từ khách hàng</p>
        </div>
        <form method="GET" class="hidden md:flex items-center gap-2 w-1/3 ml-auto">
            <input type="text" name="order_code" value="{{ request('order_code') }}" placeholder="Tìm kiếm đơn hàng..." class="border border-gray-200 rounded-xl px-4 py-2 w-full text-[15px] bg-white text-gray-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition outline-none" style="min-width:220px;" />
            <button type="submit" class="hidden">Tìm</button>
        </form>
    </div>

    <div class="bg-white rounded-lg shadow-sm p-4 mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4 border">
        <div class="flex items-center gap-2 flex-wrap">
            <span class="inline-flex items-center gap-1 text-gray-500 font-medium"><i class="fa fa-filter"></i> Bộ lọc:</span>
            <form method="GET" class="flex items-center gap-2 flex-wrap">
                <select name="branch_id" class="rounded-xl border border-gray-200 px-4 py-2 min-w-[180px] text-[15px] bg-white text-gray-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition outline-none">
                    <option value="">Tất cả chi nhánh</option>
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                            {{ $branch->name }}
                        </option>
                    @endforeach
                </select>
                <input type="text" name="date" value="{{ request('date') }}" placeholder="dd/mm/yyyy" class="border border-gray-200 rounded-xl px-4 py-2 min-w-[140px] text-[15px] bg-white text-gray-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition outline-none" />
                <button type="submit" class="bg-primary text-white px-4 py-2 rounded">Xuất báo cáo</button>
            </form>
        </div>
        <form method="GET" class="flex md:hidden mt-2 w-full">
            <input type="text" name="order_code" value="{{ request('order_code') }}" placeholder="Tìm kiếm đơn hàng..." class="border border-gray-200 rounded-xl px-4 py-2 w-full text-[15px] bg-white text-gray-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition outline-none" />
        </form>
    </div>

    {{-- Tabs trạng thái và bảng sẽ render ở đây nếu có dữ liệu --}}
    @if($orders->count())
        <div class="flex gap-2 border-b mb-4">
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
                   class="px-4 py-2 -mb-px border-b-2 {{ (request('status') == $tab['key'] || (!request('status') && $tab['key'] === '')) ? 'border-primary font-bold' : 'border-transparent text-gray-500' }}">
                    {{ $tab['label'] }} <span class="ml-1 text-xs bg-gray-200 rounded px-2 py-0.5">{{ $tab['count'] }}</span>
                </a>
            @endforeach
        </div>

        <div class="bg-white rounded-xl shadow-sm p-2 overflow-x-auto border">
            <table class="min-w-full border-separate border-spacing-0">
                <thead>
                    <tr>
                        <th class="px-4 py-3 border-b font-semibold text-gray-700 bg-gray-50 text-left">Mã đơn hàng</th>
                        <th class="px-4 py-3 border-b font-semibold text-gray-700 bg-gray-50 text-left">Khách hàng</th>
                        <th class="px-4 py-3 border-b font-semibold text-gray-700 bg-gray-50 text-left">Chi nhánh</th>
                        <th class="px-4 py-3 border-b font-semibold text-gray-700 bg-gray-50 text-left">Tổng tiền</th>
                        <th class="px-4 py-3 border-b font-semibold text-gray-700 bg-gray-50 text-left">Trạng thái</th>
                        <th class="px-4 py-3 border-b font-semibold text-gray-700 bg-gray-50 text-left">Thời gian</th>
                        <th class="px-4 py-3 border-b font-semibold text-gray-700 bg-gray-50 text-left">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 py-3 border-b align-middle font-medium text-gray-900 text-left">#{{ $order->order_code }}</td>
                        <td class="px-4 py-3 border-b align-middle text-left">
                            <span class="font-medium">{{ $order->customer->name ?? 'Khách lẻ' }}</span><br>
                            <span class="text-xs text-gray-400">{{ $order->customer->phone ?? '' }}</span>
                        </td>
                        <td class="px-4 py-3 border-b align-middle text-left">{{ $order->branch->name ?? '' }}</td>
                        <td class="px-4 py-3 border-b align-middle font-bold text-left">{{ number_format($order->total_amount) }}đ</td>
                        <td class="px-4 py-3 border-b align-middle text-left">
                            @include('admin.order._status_badge', ['status' => $order->status])
                        </td>
                        <td class="px-4 py-3 border-b align-middle text-left">{{ $order->created_at->format('h:i A') }}</td>
                        <td class="px-4 py-3 border-b align-middle text-left">
                            <div class="flex gap-2 items-center">
                                <a href="{{ route('admin.orders.show', $order->id) }}" class="flex items-center gap-1 px-3 py-1 border border-gray-300 rounded-lg text-primary bg-white hover:bg-gray-100 transition text-sm font-medium">
                                    <i class="fa fa-eye"></i> Chi tiết
                                </a>
                                @if($order->status === 'awaiting_confirmation')
                                <div class="relative">
                                    <button class="px-3 py-1 border border-gray-300 rounded-lg bg-white text-gray-700 hover:bg-gray-100 transition text-sm font-medium flex items-center gap-1">Cập nhật <i class="fa fa-chevron-down text-xs"></i></button>
                                    {{-- Dropdown cập nhật trạng thái (nếu cần) --}}
                                </div>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted-foreground py-4">Không có đơn hàng nào.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $orders->withQueryString()->links() }}
        </div>
    @else
        <div class="text-center text-muted-foreground py-8">Không có đơn hàng nào.</div>
    @endif
</div>
@endsection

@push('page-style')
<style>
.admin-order-select {
    border-radius: 12px;
    border: 1px solid #e5e7eb;
    padding: 10px 16px;
    font-size: 15px;
    background: #fff;
    color: #222;
    transition: border-color 0.2s, box-shadow 0.2s;
    outline: none;
    min-width: 180px;
}
.admin-order-select:focus, .admin-order-select:hover {
    border-color: #2563eb;
    box-shadow: 0 0 0 2px #dbeafe;
}
</style>
@endpush
